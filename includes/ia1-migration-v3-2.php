<?php
/**
 * Script de migration pour ajouter les colonnes d'indexation améliorée
 * 
 * À exécuter UNE SEULE FOIS après avoir mis à jour le fichier class-ia1-indexer.php
 * 
 * IMPORTANT : Ce script modifie la structure de la base de données.
 * Faites une sauvegarde avant de l'exécuter.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Fonction de migration à appeler depuis le fichier principal du plugin
 */
function ia1_migrate_database_v3_2() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'ia1_index';
    $charset_collate = $wpdb->get_charset_collate();
    
    // Vérifier si les colonnes existent déjà
    $columns = $wpdb->get_results( "SHOW COLUMNS FROM {$table_name}" );
    $existing_columns = array();
    
    foreach ( $columns as $column ) {
        $existing_columns[] = $column->Field;
    }
    
    $changes_made = array();
    
    // Ajouter la colonne taxonomy_terms si elle n'existe pas
    if ( ! in_array( 'taxonomy_terms', $existing_columns ) ) {
        $wpdb->query( "ALTER TABLE {$table_name} ADD COLUMN taxonomy_terms TEXT AFTER url" );
        $changes_made[] = 'taxonomy_terms ajouté';
    }
    
    // Ajouter la colonne searchable_text si elle n'existe pas
    if ( ! in_array( 'searchable_text', $existing_columns ) ) {
        $wpdb->query( "ALTER TABLE {$table_name} ADD COLUMN searchable_text LONGTEXT AFTER taxonomy_terms" );
        $changes_made[] = 'searchable_text ajouté';
    }
    
    // Ajouter la colonne hub_score si elle n'existe pas
    if ( ! in_array( 'hub_score', $existing_columns ) ) {
        $wpdb->query( "ALTER TABLE {$table_name} ADD COLUMN hub_score INT DEFAULT 0 AFTER searchable_text" );
        $changes_made[] = 'hub_score ajouté';
    }
    
    // Marquer la migration comme effectuée
    update_option( 'ia1_db_version', '3.2.0' );
    
    return array(
        'success' => true,
        'changes' => $changes_made,
        'message' => count( $changes_made ) > 0 
            ? 'Migration réussie : ' . implode( ', ', $changes_made )
            : 'Base de données déjà à jour'
    );
}

/**
 * Fonction de migration complète (drop + recréer)
 * À utiliser UNIQUEMENT en dernier recours si la migration échoue
 */
function ia1_recreate_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'ia1_index';
    $charset_collate = $wpdb->get_charset_collate();
    
    // ATTENTION : Cette requête supprime toutes les données !
    $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
    
    // Recréer la table avec la nouvelle structure
    $sql = "CREATE TABLE {$table_name} (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        post_type varchar(20) NOT NULL,
        title text NOT NULL,
        content longtext NOT NULL,
        url varchar(255) NOT NULL,
        taxonomy_terms text,
        searchable_text longtext,
        hub_score int DEFAULT 0,
        indexed_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY post_id (post_id),
        KEY post_type (post_type),
        KEY hub_score (hub_score),
        FULLTEXT KEY searchable_text (searchable_text)
    ) $charset_collate;";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    return array(
        'success' => true,
        'message' => 'Table recréée avec succès. Vous devez maintenant réindexer tout le contenu.'
    );
}
