<?php
/**
 * Gestion de l'indexation et de la recherche - VERSION AMÉLIORÉE
 *
 * @package IA1
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
     * Indexe un post avec TOUTES ses métadonnées
     */
    public function index_post( $post ) {
        global $wpdb;
        
        // CORRECTION v3.2.3 : Rendre le contenu (Gutenberg, shortcodes) AVANT de nettoyer
        $rendered_content = apply_filters( 'the_content', $post->post_content );
        $content = wp_strip_all_tags( $rendered_content );
        $content = preg_replace( '/\s+/', ' ', $content );
        $content = trim( $content );
        
        // CORRECTION v3.2.3 : Enrichir l'indexation des produits WooCommerce
        if ( $post->post_type === 'product' && function_exists( 'wc_get_product' ) ) {
            $content = $this->enrich_woocommerce_product_content( $post, $content );
        }
        
        // Extraire TOUTES les taxonomies (catégories, tags, taxonomies custom)
        $taxonomy_terms = $this->get_all_taxonomy_terms( $post->ID );
        
        // Créer un champ de recherche enrichi qui combine tout
        $searchable_text = $this->build_searchable_text( $post, $content, $taxonomy_terms );
        
        // Ne pas indexer si le contenu est trop court ET pas de taxonomies
        if ( strlen( $content ) < 50 && empty( $taxonomy_terms ) ) {
            return false;
        }
        
        // Calculer un score de "hub page" (page importante qui présente du contenu)
        $hub_score = $this->calculate_hub_score( $post, $content );
        
        // Insérer ou mettre à jour
        $wpdb->replace(
            $this->table_name,
            array(
                'post_id' => $post->ID,
                'post_type' => $post->post_type,
                'title' => $post->post_title,
                'content' => $content,
                'url' => get_permalink( $post->ID ),
                'taxonomy_terms' => $taxonomy_terms,
                'searchable_text' => $searchable_text,
                'hub_score' => $hub_score,
                'indexed_at' => current_time( 'mysql' )
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s' )
        );
        
        return true;
    }
    
    /**
     * Enrichit le contenu d'un produit WooCommerce avec TOUTES ses métadonnées
     * CORRECTION v3.2.3 : Les produits WooCommerce ont souvent peu de contenu dans post_content
     * mais beaucoup d'infos dans les métadonnées (prix, SKU, description courte, etc.)
     */
    private function enrich_woocommerce_product_content( $post, $content ) {
        $product = wc_get_product( $post->ID );
        
        if ( ! $product ) {
            return $content;
        }
        
        $enriched_parts = array();
        
        // Contenu existant (description longue)
        if ( ! empty( $content ) ) {
            $enriched_parts[] = $content;
        }
        
        // Description courte (très importante pour les produits)
        $short_description = $product->get_short_description();
        if ( ! empty( $short_description ) ) {
            $enriched_parts[] = wp_strip_all_tags( $short_description );
        }
        
        // Prix (formaté pour la recherche)
        $price = $product->get_price();
        if ( ! empty( $price ) ) {
            $enriched_parts[] = "Prix : " . $price . " euros";
            $enriched_parts[] = $price . "€";
        }
        
        // SKU (référence produit)
        $sku = $product->get_sku();
        if ( ! empty( $sku ) ) {
            $enriched_parts[] = "Référence : " . $sku;
            $enriched_parts[] = "SKU : " . $sku;
        }
        
        // Type de produit (CD, vinyle, etc.)
        $product_type = $product->get_type();
        $enriched_parts[] = "Type : " . $product_type;
        
        // Stock status
        $stock_status = $product->get_stock_status();
        if ( $stock_status === 'instock' ) {
            $enriched_parts[] = "En stock";
            $enriched_parts[] = "Disponible";
        } else {
            $enriched_parts[] = "Rupture de stock";
        }
        
        // Catégories produits (ex: "CD", "Vinyle", "Merchandising")
        $categories = wp_get_post_terms( $post->ID, 'product_cat', array( 'fields' => 'names' ) );
        if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
            foreach ( $categories as $category ) {
                $enriched_parts[] = "Catégorie : " . $category;
                $enriched_parts[] = $category;
            }
        }
        
        // Tags produits
        $tags = wp_get_post_terms( $post->ID, 'product_tag', array( 'fields' => 'names' ) );
        if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
            foreach ( $tags as $tag ) {
                $enriched_parts[] = $tag;
            }
        }
        
        // Attributs (taille, couleur, format, etc.)
        $attributes = $product->get_attributes();
        if ( ! empty( $attributes ) ) {
            foreach ( $attributes as $attribute ) {
                if ( is_a( $attribute, 'WC_Product_Attribute' ) ) {
                    $values = $attribute->get_options();
                    if ( is_array( $values ) ) {
                        foreach ( $values as $value ) {
                            $term = get_term( $value );
                            if ( $term && ! is_wp_error( $term ) ) {
                                $enriched_parts[] = $term->name;
                            }
                        }
                    }
                }
            }
        }
        
        // Combiner tout le contenu enrichi
        $enriched_content = implode( ' ', array_filter( $enriched_parts ) );
        
        return $enriched_content;
    }
    
    /**
     * Récupère TOUS les termes de taxonomies d'un post
     */
    private function get_all_taxonomy_terms( $post_id ) {
        $all_terms = array();
        
        // Récupérer toutes les taxonomies associées au post
        $taxonomies = get_object_taxonomies( get_post_type( $post_id ) );
        
        foreach ( $taxonomies as $taxonomy ) {
            $terms = get_the_terms( $post_id, $taxonomy );
            
            if ( $terms && ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    $all_terms[] = $term->name;
                }
            }
        }
        
        return implode( ', ', $all_terms );
    }
    
    /**
     * Construit un texte de recherche enrichi
     */
    private function build_searchable_text( $post, $content, $taxonomy_terms ) {
        $parts = array();
        
        // Titre (poids x3 car très important)
        $parts[] = $post->post_title;
        $parts[] = $post->post_title;
        $parts[] = $post->post_title;
        
        // Taxonomies (poids x2)
        if ( ! empty( $taxonomy_terms ) ) {
            $parts[] = $taxonomy_terms;
            $parts[] = $taxonomy_terms;
        }
        
        // Contenu
        $parts[] = $content;
        
        // Excerpt si différent
        if ( ! empty( $post->post_excerpt ) ) {
            $parts[] = $post->post_excerpt;
        }
        
        return implode( ' ', $parts );
    }
    
    /**
     * Calcule un score de "page hub" (page importante de présentation)
     */
    private function calculate_hub_score( $post, $content ) {
        $score = 0;
        
        // Type de post (pages sont souvent des hubs)
        if ( $post->post_type === 'page' ) {
            $score += 20;
        }
        
        // Titre court et descriptif (souvent des pages de présentation)
        $title_words = str_word_count( $post->post_title );
        if ( $title_words <= 4 ) {
            $score += 15;
        }
        
        // Contenu ni trop court ni trop long (pages de présentation)
        $content_length = strlen( $content );
        if ( $content_length > 200 && $content_length < 2000 ) {
            $score += 10;
        }
        
        // Mots-clés de pages hub dans le contenu
        $hub_keywords = array(
            'découvrir', 'explorer', 'voici', 'présentation', 
            'collection', 'mes', 'portfolio', 'galerie',
            'catégorie', 'archives', 'tous les', 'toutes les'
        );
        
        $content_lower = strtolower( $content );
        foreach ( $hub_keywords as $keyword ) {
            if ( strpos( $content_lower, $keyword ) !== false ) {
                $score += 5;
            }
        }
        
        // Présence de listes ou de liens (signe de page de navigation)
        if ( preg_match_all( '/<li>|<ul>|<ol>/', $post->post_content ) > 5 ) {
            $score += 10;
        }
        
        return min( $score, 100 ); // Score max = 100
    }
    
    /**
     * Recherche dans l'index - VERSION AMÉLIORÉE AVEC DÉTECTION D'INTENTION
     */
    public function search( $query, $limit = null ) {
        global $wpdb;
        
        if ( $limit === null ) {
            $limit = (int) get_option( 'ia1_max_contexts', 5 );
        }
        
        // Nettoyer la requête
        $query = sanitize_text_field( $query );
        $query_lower = strtolower( $query );
        
        // Mots-clés de la requête
        $query_words = array_filter( explode( ' ', $query_lower ), function( $word ) {
            return strlen( $word ) > 2; // Ignorer les mots de moins de 3 caractères
        });
        
        if ( empty( $query_words ) ) {
            return array();
        }
        
        // Détecter l'intention de la question
        $intent = $this->detect_search_intent( $query_lower );
        
        // Construction de la clause WHERE avec recherche enrichie
        $where_parts = array();
        foreach ( $query_words as $word ) {
            $word_safe = $wpdb->esc_like( $word );
            
            // Chercher dans le texte enrichi (inclut taxonomies)
            $where_parts[] = $wpdb->prepare(
                "(LOWER(searchable_text) LIKE %s)",
                '%' . $word_safe . '%'
            );
        }
        
        $where = implode( ' OR ', $where_parts );
        
        // Construire les parties du score pour TOUS les mots
        $score_parts = array();
        $prepare_values = array();
        
        foreach ( $query_words as $word ) {
            $word_safe = $wpdb->esc_like( $word );
            
            // Ajouter au SQL : fréquence dans titre
            $score_parts[] = "(LENGTH(LOWER(title)) - LENGTH(REPLACE(LOWER(title), %s, ''))) * 15";
            $prepare_values[] = $word_safe;
            
            // Ajouter au SQL : fréquence dans taxonomies
            $score_parts[] = "(LENGTH(LOWER(taxonomy_terms)) - LENGTH(REPLACE(LOWER(taxonomy_terms), %s, ''))) * 20";
            $prepare_values[] = $word_safe;
            
            // Ajouter au SQL : fréquence dans contenu
            $score_parts[] = "(LENGTH(LOWER(content)) - LENGTH(REPLACE(LOWER(content), %s, ''))) * 2";
            $prepare_values[] = $word_safe;
        }
        
        $score_formula = implode( ' + ', $score_parts );
        
        // Requête SQL avec scoring multicritère sur TOUS les mots
        $sql = "
            SELECT *,
                (
                    {$score_formula}
                    +
                    -- Score bonus: Hub score (pages de présentation)
                    hub_score * 0.5
                    +
                    -- Score bonus: Type de post (pages = hubs souvent)
                    CASE 
                        WHEN post_type = 'page' THEN 10
                        WHEN post_type = 'post' THEN 5
                        ELSE 0
                    END
                ) as relevance_score
            FROM {$this->table_name}
            WHERE {$where}
            ORDER BY relevance_score DESC, indexed_at DESC
            LIMIT %d
        ";
        
        // Préparer avec tous les paramètres + la limite
        $prepare_values[] = $limit;
        
        $prepared = $wpdb->prepare( $sql, ...$prepare_values );
        
        $results = $wpdb->get_results( $prepared, ARRAY_A );
        
        // Ajouter les excerpts et enrichir les résultats
        foreach ( $results as &$result ) {
            // Utiliser tous les mots pour l'excerpt, pas juste le premier
            $result['excerpt'] = $this->extract_relevant_excerpt( 
                $result['content'], 
                implode( ' ', $query_words )
            );
            
            // Ajouter les taxonomies dans le résultat pour affichage
            $result['taxonomies'] = $result['taxonomy_terms'];
            
            // Score de confiance (pour debug si besoin)
            $result['confidence'] = min( 100, $result['relevance_score'] / 10 );
        }
        
        return $results;
    }
    
    /**
     * Détecte l'intention de la recherche
     */
    private function detect_search_intent( $query ) {
        $intent = array(
            'type' => 'general',
            'looking_for_category' => false,
            'looking_for_list' => false,
            'looking_for_specific' => false
        );
        
        // Recherche de catégorie/collection
        $category_patterns = array(
            '/tous les?/', '/toutes les?/', '/liste de/', '/catégorie/',
            '/voir les?/', '/afficher les?/', '/mes/', '/portfolio/'
        );
        
        foreach ( $category_patterns as $pattern ) {
            if ( preg_match( $pattern, $query ) ) {
                $intent['looking_for_category'] = true;
                $intent['type'] = 'category';
                break;
            }
        }
        
        // Recherche de liste
        if ( strpos( $query, 'combien' ) !== false || 
             strpos( $query, 'liste' ) !== false ||
             strpos( $query, 'quels sont' ) !== false ) {
            $intent['looking_for_list'] = true;
        }
        
        // Recherche spécifique (question avec "quel", "où", "comment")
        if ( preg_match( '/^(quel|quelle|où|comment|pourquoi|quand)/', $query ) ) {
            $intent['looking_for_specific'] = true;
            $intent['type'] = 'specific';
        }
        
        return $intent;
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
    
    /**
     * Récupère des statistiques sur l'index
     */
    public function get_index_stats() {
        global $wpdb;
        
        $stats = array(
            'total' => 0,
            'by_type' => array(),
            'with_taxonomies' => 0,
            'hub_pages' => 0
        );
        
        // Total
        $stats['total'] = $this->get_indexed_count();
        
        // Par type
        $by_type = $wpdb->get_results( 
            "SELECT post_type, COUNT(*) as count FROM {$this->table_name} GROUP BY post_type",
            ARRAY_A 
        );
        
        foreach ( $by_type as $row ) {
            $stats['by_type'][ $row['post_type'] ] = (int) $row['count'];
        }
        
        // Avec taxonomies
        $stats['with_taxonomies'] = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->table_name} WHERE taxonomy_terms IS NOT NULL AND taxonomy_terms != ''"
        );
        
        // Pages hub (score > 30)
        $stats['hub_pages'] = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->table_name} WHERE hub_score > 30"
        );
        
        return $stats;
    }
}
