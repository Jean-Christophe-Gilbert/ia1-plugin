<?php
/**
 * Page d'administration des logs RAG pour IA1
 *
 * @package IA1
 * @since   3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class IA1_Logs_Admin {

    /**
     * Enregistre la page de menu.
     */
    public static function register_menu() {
        add_submenu_page(
            'ia1',
            'Logs des requêtes',
            'Logs',
            'manage_options',
            'ia1-logs',
            array( __CLASS__, 'render_page' )
        );
    }

    /**
     * Gère les actions POST (vider les logs).
     */
    public static function handle_actions() {
        if (
            isset( $_POST['ia1_clear_logs'] ) &&
            check_admin_referer( 'ia1_clear_logs_nonce' ) &&
            current_user_can( 'manage_options' )
        ) {
            IA1_Logger::clear_logs();
            add_settings_error( 'ia1_logs', 'cleared', 'Logs vidés avec succès.', 'success' );
        }
    }

    /**
     * Affiche la page de logs.
     */
    public static function render_page() {
        self::handle_actions();

        $stats       = IA1_Logger::get_stats();
        $logs        = IA1_Logger::get_recent_logs( 50 );
        $unanswered  = IA1_Logger::get_unanswered_questions( 20 );

        settings_errors( 'ia1_logs' );
        ?>
        <div class="wrap">
            <h1>Logs IA1 (<?php echo esc_html( $stats['total'] ); ?> requêtes)</h1>

            <!-- Barre de recherche -->
            <form method="get" style="margin-bottom: 16px;">
                <input type="hidden" name="page" value="ia1-logs">
                <input
                    type="search"
                    name="q"
                    value="<?php echo esc_attr( $_GET['q'] ?? '' ); ?>"
                    placeholder="Rechercher dans les questions..."
                    style="width: 300px; margin-right: 8px;"
                >
                <button type="submit" class="button">Filtrer</button>
                <?php if ( ! empty( $_GET['q'] ) ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=ia1-logs' ) ); ?>" class="button">Réinitialiser</a>
                <?php endif; ?>

                <!-- Bouton vider -->
                <form method="post" style="display:inline; margin-left: 16px;">
                    <?php wp_nonce_field( 'ia1_clear_logs_nonce' ); ?>
                    <button
                        type="submit"
                        name="ia1_clear_logs"
                        class="button button-secondary"
                        style="color: #b32d2e; border-color: #b32d2e;"
                        onclick="return confirm('Vider tous les logs ?');"
                    >&#9642; Vider les logs</button>
                </form>
            </form>

            <!-- Tableau des logs -->
            <table class="wp-list-table widefat fixed striped" style="margin-top: 0;">
                <thead>
                    <tr>
                        <th style="width: 150px;">Date</th>
                        <th>Question</th>
                        <th>Réponse (extrait)</th>
                        <th style="width: 80px;">Page</th>
                        <th style="width: 40px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $search = sanitize_text_field( $_GET['q'] ?? '' );

                foreach ( $logs as $log ) :
                    if ( $search && stripos( $log['question'], $search ) === false ) {
                        continue;
                    }

                    $sources = json_decode( $log['sources'], true ) ?? array();
                    $answered = (bool) $log['answered'];

                    // Extrait de la réponse via la première source
                    $response_preview = '';
                    if ( ! empty( $sources ) ) {
                        $titles = array_column( $sources, 'title' );
                        $response_preview = implode( ', ', array_slice( $titles, 0, 2 ) );
                        if ( count( $sources ) > 2 ) {
                            $response_preview .= '…';
                        }
                    }
                ?>
                    <tr>
                        <td><?php echo esc_html( date_i18n( 'Y-m-d H:i:s', strtotime( $log['asked_at'] ) ) ); ?></td>
                        <td><?php echo esc_html( $log['question'] ); ?></td>
                        <td>
                            <?php if ( $answered ) : ?>
                                <?php echo esc_html( $response_preview ?: '(sources trouvées)' ); ?>
                            <?php else : ?>
                                <em style="color: #999;">Je n'ai pas trouvé d'information…</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $log['page_url'] ) : ?>
                                <a href="<?php echo esc_url( $log['page_url'] ); ?>" target="_blank">Voir</a>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="color: #b32d2e; cursor: default;">&#10005;</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
