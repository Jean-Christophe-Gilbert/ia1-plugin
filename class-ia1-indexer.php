<?php
/**
 * Gestion de l'indexation et de la recherche
 *
 * @package IA1
 * @version 3.1.10 - Algorithme de recherche amélioré
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class IA1_Indexer {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'ia1_index';
    }
    
    /**
     * Réindexe tout le contenu
     */
    public function reindex_all() {
        global $wpdb;
        
        $result = array(
            'indexed' => 0,
            'errors' => 0
        );
        
        // Vider la table
        $wpdb->query( "TRUNCATE TABLE {$this->table_name}" );
        
        // Récupérer TOUS les types de contenu publics
        $post_types = get_post_types( array( 'public' => true ), 'names' );
        
        // Récupérer tous les posts de tous types publics
        $args = array(
            'post_type' => $post_types,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        $query = new WP_Query( $args );
        
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                
                try {
                    $this->index_post( get_post() );
                    $result['indexed']++;
                } catch ( Exception $e ) {
                    $result['errors']++;
                }
            }
        }
        
        wp_reset_postdata();
        
        return $result;
    }
    
    /**
     * Indexe un post
     */
    public function index_post( $post ) {
        global $wpdb;
        
        // Nettoyer le contenu
        $content = wp_strip_all_tags( $post->post_content );
        $content = preg_replace( '/\s+/', ' ', $content );
        $content = trim( $content );
        
        // Ne pas indexer si le contenu est trop court
        if ( strlen( $content ) < 50 ) {
            return false;
        }
        
        // Insérer ou mettre à jour
        $wpdb->replace(
            $this->table_name,
            array(
                'post_id' => $post->ID,
                'post_type' => $post->post_type,
                'title' => $post->post_title,
                'content' => $content,
                'url' => get_permalink( $post->ID ),
                'indexed_at' => current_time( 'mysql' )
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%s' )
        );
        
        return true;
    }
    
    /**
     * Recherche dans l'index - VERSION AMÉLIORÉE v3.1.10
     * 
     * Améliorations :
     * - Détection automatique des "pages hub" (pages importantes)
     * - Hiérarchie de post types (pages > posts > produits)
     * - Boost pour les pages avec beaucoup de contenu
     * - Scoring plus intelligent basé sur la pertinence contextuelle
     */
    public function search( $query, $limit = null ) {
        global $wpdb;
        
        if ( $limit === null ) {
            $limit = (int) get_option( 'ia1_max_contexts', 5 );
        }
        
        // Nettoyer la requête
        $query = sanitize_text_field( $query );
        $query = strtolower( $query );
        
        // Mots-clés de la requête
        $query_words = array_filter( explode( ' ', $query ), function( $word ) {
            return strlen( $word ) > 2; // Ignorer les mots de moins de 3 caractères
        });
        
        if ( empty( $query_words ) ) {
            return array();
        }
        
        // Construction de la clause WHERE
        $where_parts = array();
        foreach ( $query_words as $word ) {
            $word_safe = $wpdb->esc_like( $word );
            $where_parts[] = $wpdb->prepare(
                "(LOWER(title) LIKE %s OR LOWER(content) LIKE %s)",
                '%' . $word_safe . '%',
                '%' . $word_safe . '%'
            );
        }
        
        $where = implode( ' OR ', $where_parts );
        
        // Premier mot pour le scoring (le plus important généralement)
        $first_word = reset( $query_words );
        $first_word_safe = $wpdb->esc_like( $first_word );
        
        // Requête SQL avec scoring AMÉLIORÉ
        $sql = "
            SELECT *,
                (
                    -- BOOST 1: Correspondance exacte dans le titre (très important)
                    CASE 
                        WHEN LOWER(title) LIKE %s THEN 200
                        ELSE 0
                    END
                    
                    -- BOOST 2: Occurrences dans le titre (important)
                    + (LENGTH(title) - LENGTH(REPLACE(LOWER(title), %s, ''))) * 15
                    
                    -- BOOST 3: Occurrences dans le contenu (moins important)
                    + (LENGTH(content) - LENGTH(REPLACE(LOWER(content), %s, ''))) * 2
                    
                    -- BOOST 4: Pages hub (beaucoup de contenu = page importante)
                    + CASE
                        WHEN LENGTH(content) > 2000 THEN 50
                        WHEN LENGTH(content) > 1000 THEN 30
                        WHEN LENGTH(content) > 500 THEN 15
                        ELSE 0
                    END
                    
                    -- BOOST 5: Hiérarchie de post types
                    + CASE post_type
                        WHEN 'page' THEN 40        -- Pages WordPress (souvent des pages principales)
                        WHEN 'product' THEN 30     -- Produits WooCommerce
                        WHEN 'post' THEN 20        -- Articles de blog
                        ELSE 10                    -- Autres types
                    END
                    
                    -- BOOST 6: Titre court = plus spécifique (bonus si pertinent)
                    + CASE
                        WHEN LENGTH(title) < 50 AND LOWER(title) LIKE %s THEN 25
                        ELSE 0
                    END
                    
                ) as relevance_score
            FROM {$this->table_name}
            WHERE {$where}
            ORDER BY relevance_score DESC, indexed_at DESC
            LIMIT %d
        ";
        
        $prepared = $wpdb->prepare(
            $sql,
            '%' . $first_word_safe . '%',  // BOOST 1
            $first_word_safe,               // BOOST 2
            $first_word_safe,               // BOOST 3
            '%' . $first_word_safe . '%',   // BOOST 6
            $limit
        );
        
        $results = $wpdb->get_results( $prepared, ARRAY_A );
        
        // Post-traitement : ajouter les métadonnées utiles
        foreach ( $results as &$result ) {
            // Excerpt pertinent
            $result['excerpt'] = $this->extract_relevant_excerpt( 
                $result['content'], 
                $first_word 
            );
            
            // Marquer les "pages hub" (utile pour l'IA)
            $result['is_hub_page'] = strlen( $result['content'] ) > 1000;
            
            // Catégorie du contenu (pour l'IA)
            $result['category'] = $this->detect_content_category( $result );
        }
        
        return $results;
    }
    
    /**
     * Détecte la catégorie/type de contenu de manière intelligente
     * Aide l'IA à mieux comprendre le type de page
     */
    private function detect_content_category( $result ) {
        $title_lower = strtolower( $result['title'] );
        $content_lower = strtolower( $result['content'] );
        
        // Détection basée sur des patterns communs
        $patterns = array(
            'boutique' => array( 'boutique', 'shop', 'store', 'acheter', 'commander', 'panier' ),
            'contact' => array( 'contact', 'nous contacter', 'nous écrire' ),
            'apropos' => array( 'à propos', 'about', 'qui sommes-nous', 'notre histoire' ),
            'faq' => array( 'faq', 'questions', 'aide', 'support' ),
            'blog' => array( 'blog', 'actualités', 'news', 'articles' ),
            'service' => array( 'services', 'prestations', 'offres' ),
        );
        
        foreach ( $patterns as $category => $keywords ) {
            foreach ( $keywords as $keyword ) {
                if ( strpos( $title_lower, $keyword ) !== false || 
                     strpos( $content_lower, $keyword ) !== false ) {
                    return $category;
                }
            }
        }
        
        // Par défaut, utiliser le post_type
        return $result['post_type'];
    }
    
    /**
     * Extrait un passage pertinent du contenu autour d'un mot-clé
     */
    private function extract_relevant_excerpt( $content, $keyword, $length = 300 ) {
        $content = trim( $content );
        
        if ( empty( $content ) ) {
            return '';
        }
        
        // Chercher le mot-clé (insensible à la casse)
        $pos = stripos( $content, $keyword );
        
        if ( $pos === false ) {
            // Si pas trouvé, retourner le début
            return substr( $content, 0, $length ) . '...';
        }
        
        // Extraire autour de la position trouvée
        $start = max( 0, $pos - 100 );
        $excerpt = substr( $content, $start, $length );
        
        // Nettoyer
        if ( $start > 0 ) {
            $excerpt = '...' . $excerpt;
        }
        if ( strlen( $content ) > $start + $length ) {
            $excerpt .= '...';
        }
        
        return $excerpt;
    }
    
    /**
     * Compte le nombre de documents indexés
     */
    public function get_indexed_count() {
        global $wpdb;
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name}" );
    }
    
    /**
     * Supprime un post de l'index
     */
    public function delete_post( $post_id ) {
        global $wpdb;
        return $wpdb->delete(
            $this->table_name,
            array( 'post_id' => $post_id ),
            array( '%d' )
        );
    }
}
