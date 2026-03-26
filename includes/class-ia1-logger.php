<?php
/**
 * Logger RAG pour IA1
 *
 * Enregistre chaque échange (question → sources → réponse)
 * pour permettre l'analyse des lacunes d'indexation.
 *
 * @package IA1
 * @since   3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class IA1_Logger {

    const TABLE_SUFFIX = 'ia1_logs';

    /**
     * Retourne le nom complet de la table.
     */
    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . self::TABLE_SUFFIX;
    }

    /**
     * Crée la table de logs.
     * Appelé depuis IA1_Plugin::activate().
     */
    public static function create_table() {
        global $wpdb;

        $table_name      = self::get_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id          bigint(20)   NOT NULL AUTO_INCREMENT,
            asked_at    datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
            question    text         NOT NULL,
            answered    tinyint(1)   NOT NULL DEFAULT 0,
            sources     longtext,
            tokens_used int          DEFAULT NULL,
            model       varchar(60)  DEFAULT NULL,
            page_url    varchar(255) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY asked_at (asked_at),
            KEY answered (answered)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Enregistre un échange RAG.
     *
     * @param string $question     Question posée par le visiteur.
     * @param array  $contexts     Sources retournées par l'indexer.
     * @param array  $api_response Tableau retourné par IA1_Mistral::generate_response().
     */
    public static function log_query( $question, $contexts, $api_response ) {
        global $wpdb;

        // Détermine si une vraie réponse a été trouvée
        $response_text = $api_response['text'] ?? '';
        $no_answer_phrases = array(
            "Je n'ai pas trouvé",
            "Je n'ai pas d'information",
            "Aucune information",
            "pas dans le contenu",
        );

        $answered = 1;
        foreach ( $no_answer_phrases as $phrase ) {
            if ( stripos( $response_text, $phrase ) !== false ) {
                $answered = 0;
                break;
            }
        }

        // Prépare les sources à stocker (titre + URL + type)
        $sources_data = array();
        if ( ! empty( $contexts ) ) {
            foreach ( $contexts as $ctx ) {
                $sources_data[] = array(
                    'title'     => $ctx['title'] ?? '',
                    'url'       => $ctx['url'] ?? '',
                    'post_type' => $ctx['post_type'] ?? '',
                );
            }
        }

        // Récupère l'URL de la page courante si disponible
        $page_url = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_url( $_SERVER['HTTP_REFERER'] ) : null;

        $wpdb->insert(
            self::get_table_name(),
            array(
                'asked_at'    => current_time( 'mysql' ),
                'question'    => sanitize_text_field( $question ),
                'answered'    => $answered,
                'sources'     => wp_json_encode( $sources_data ),
                'tokens_used' => isset( $api_response['tokens_used']['total_tokens'] )
                                    ? intval( $api_response['tokens_used']['total_tokens'] )
                                    : null,
                'model'       => sanitize_text_field( $api_response['model'] ?? '' ),
                'page_url'    => $page_url,
            ),
            array( '%s', '%s', '%d', '%s', '%d', '%s', '%s' )
        );
    }

    /**
     * Retourne les logs récents.
     *
     * @param int $limit Nombre de logs à retourner.
     * @return array
     */
    public static function get_recent_logs( $limit = 50 ) {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM %i ORDER BY asked_at DESC LIMIT %d",
                self::get_table_name(),
                $limit
            ),
            ARRAY_A
        );
    }

    /**
     * Retourne les questions sans réponse.
     *
     * @param int $limit
     * @return array
     */
    public static function get_unanswered_questions( $limit = 20 ) {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT question, COUNT(*) as nb, MAX(asked_at) as last_asked
                 FROM %i
                 WHERE answered = 0
                 GROUP BY question
                 ORDER BY nb DESC, last_asked DESC
                 LIMIT %d",
                self::get_table_name(),
                $limit
            ),
            ARRAY_A
        );
    }

    /**
     * Retourne des statistiques globales.
     *
     * @return array
     */
    public static function get_stats() {
        global $wpdb;
        $table = self::get_table_name();

        $total     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `$table`" );
        $answered  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `$table` WHERE answered = 1" );
        $unanswered = $total - $answered;
        $rate      = $total > 0 ? round( ( $answered / $total ) * 100 ) : 0;

        return array(
            'total'      => $total,
            'answered'   => $answered,
            'unanswered' => $unanswered,
            'rate'       => $rate,
        );
    }

    /**
     * Vide tous les logs.
     */
    public static function clear_logs() {
        global $wpdb;
        $wpdb->query( "TRUNCATE TABLE `" . self::get_table_name() . "`" );
    }
}
