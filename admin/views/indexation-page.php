<?php
/**
 * Page d'indexation IA1
 *
 * @package IA1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Statistiques d'indexation
global $wpdb;
$table_name = $wpdb->prefix . 'ia1_index';

$stats = array(
    'total' => $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" ),
    'posts' => $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE post_type = 'post'" ),
    'pages' => $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE post_type = 'page'" ),
    'products' => $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE post_type = 'product'" ),
);
?>

<div class="wrap">
    <h1>Indexation IA1</h1>
    
    <div class="ia1-indexation-page">
        
        <!-- Statistiques -->
        <div class="ia1-card">
            <h2><span class="dashicons dashicons-chart-bar"></span> Statistiques</h2>
            
            <div class="ia1-stats-grid">
                <div class="ia1-stat-box">
                    <div class="ia1-stat-label">Documents indexés</div>
                    <div class="ia1-stat-value"><?php echo esc_html( $stats['total'] ); ?></div>
                </div>
                
                <div class="ia1-stat-box">
                    <div class="ia1-stat-label">Articles</div>
                    <div class="ia1-stat-value"><?php echo esc_html( $stats['posts'] ); ?></div>
                </div>
                
                <div class="ia1-stat-box">
                    <div class="ia1-stat-label">Pages</div>
                    <div class="ia1-stat-value"><?php echo esc_html( $stats['pages'] ); ?></div>
                </div>
                
                <div class="ia1-stat-box">
                    <div class="ia1-stat-label">Produits</div>
                    <div class="ia1-stat-value"><?php echo esc_html( $stats['products'] ); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="ia1-card">
            <h2><span class="dashicons dashicons-admin-tools"></span> Actions</h2>
            
            <p class="description">
                L'indexation permet à IA1 de connaître le contenu de votre site pour pouvoir y répondre. 
                Elle s'exécute automatiquement lors de la publication de nouveaux contenus, mais vous pouvez aussi la relancer manuellement ici.
            </p>
            
            <p>
                <button type="button" class="button button-primary" id="ia1-reindex-btn">
                    <span class="dashicons dashicons-update"></span> Réindexer tout le contenu
                </button>
            </p>
            
            <p class="description">
                Cette opération peut prendre quelques minutes selon la taille de votre site.
            </p>
            
            <div id="ia1-indexation-result" style="display: none; margin-top: 15px;"></div>
        </div>
        
        <!-- Indexation automatique -->
        <div class="ia1-card">
            <h2><span class="dashicons dashicons-update"></span> Indexation automatique</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Indexation à la publication</th>
                    <td>
                        <label>
                            <input type="checkbox" checked disabled>
                            Indexer automatiquement les nouveaux contenus
                        </label>
                        <p class="description">Cette option est toujours activée pour garantir que votre IA reste à jour.</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Informations -->
        <div class="ia1-card ia1-info-box">
            <h2><span class="dashicons dashicons-info"></span> Informations</h2>
            
            <h3>Que fait l'indexation ?</h3>
            <p>
                L'indexation analyse vos articles, pages et produits pour permettre à IA1 de les comprendre et d'y répondre. 
                Le contenu est stocké localement dans votre base de données WordPress.
            </p>
            
            <p class="ia1-warning">
                <strong>Note :</strong> Seul le contenu publié est indexé. Les brouillons et les contenus privés ne sont pas accessibles à l'IA.
            </p>
        </div>
        
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#ia1-reindex-btn').on('click', function() {
        var $btn = $(this);
        var $result = $('#ia1-indexation-result');
        
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Indexation en cours...');
        $result.hide();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'ia1_reindex_content',
                nonce: ia1Admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    $result.html(
                        '<div class="notice notice-success inline"><p><strong>✓ ' + response.data.message + '</strong><br>' +
                        'Documents indexés : ' + response.data.indexed + '</p></div>'
                    ).show();
                    
                    // Recharger la page après 2 secondes pour mettre à jour les stats
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    $result.html(
                        '<div class="notice notice-error inline"><p><strong>✗ Erreur :</strong> ' + response.data.message + '</p></div>'
                    ).show();
                }
            },
            error: function() {
                $result.html(
                    '<div class="notice notice-error inline"><p><strong>✗ Erreur réseau</strong></p></div>'
                ).show();
            },
            complete: function() {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Réindexer tout le contenu');
            }
        });
    });
});
</script>
