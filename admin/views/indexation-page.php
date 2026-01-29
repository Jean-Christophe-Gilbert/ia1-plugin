<?php
/**
 * Template de la page d'indexation
 *
 * @package IA1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Récupérer les stats
$indexer = new IA1_Indexer();
$indexed_count = $indexer->get_indexed_count();

// Compter les posts publiés
$total_posts = wp_count_posts( 'post' )->publish;
$total_pages = wp_count_posts( 'page' )->publish;
$total_products = post_type_exists( 'product' ) ? wp_count_posts( 'product' )->publish : 0;
$total_content = $total_posts + $total_pages + $total_products;
?>

<div class="wrap ia1-admin-wrap">
    <div class="ia1-admin-container">
        
        <!-- Header -->
        <h1 class="ia1-admin-title">
            <img src="https://ia1.fr/wp-content/uploads/2026/01/cropped-Gemini_Generated_Image_e2r4dee2r4dee2r4.png" alt="IA1 Logo" class="ia1-logo">
            Indexation IA1
        </h1>
        
        <!-- Notifications -->
        <div id="ia1-notifications"></div>
        
        <!-- Statistiques -->
        <div class="ia1-section">
            <h2 class="ia1-section-title">📊 Statistiques</h2>
            
            <div class="ia1-indexation-stats">
                <div class="ia1-stat-box">
                    <h3>Documents indexés</h3>
                    <div class="ia1-stat-number"><?php echo number_format_i18n( $indexed_count ); ?></div>
                </div>
                
                <div class="ia1-stat-box">
                    <h3>Articles</h3>
                    <div class="ia1-stat-number"><?php echo number_format_i18n( $total_posts ); ?></div>
                </div>
                
                <div class="ia1-stat-box">
                    <h3>Pages</h3>
                    <div class="ia1-stat-number"><?php echo number_format_i18n( $total_pages ); ?></div>
                </div>
                
                <?php if ( $total_products > 0 ) : ?>
                <div class="ia1-stat-box">
                    <h3>Produits</h3>
                    <div class="ia1-stat-number"><?php echo number_format_i18n( $total_products ); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="ia1-section">
            <h2 class="ia1-section-title">🔄 Actions</h2>
            
            <p class="description">
                L'indexation permet à IA1 de connaître le contenu de votre site pour pouvoir y répondre.
                Elle s'exécute automatiquement lors de la publication de nouveaux contenus, mais vous pouvez
                aussi la relancer manuellement ici.
            </p>
            
            <div style="margin-top: 20px;">
                <button type="button" id="ia1-start-indexation" class="button button-primary button-large">
                    🚀 Réindexer tout le contenu
                </button>
                <p class="description" style="margin-top: 10px;">
                    Cette opération peut prendre quelques minutes selon la taille de votre site.
                </p>
            </div>
            
            <!-- Progress bar -->
            <div id="ia1-indexation-progress" style="display: none; margin-top: 20px;">
                <div class="ia1-progress-bar">
                    <div class="ia1-progress-fill" style="width: 0%;">0%</div>
                </div>
                <p id="ia1-indexation-status" style="text-align: center; margin-top: 10px;"></p>
            </div>
        </div>
        
        <!-- Indexation automatique -->
        <div class="ia1-section">
            <h2 class="ia1-section-title">⚙️ Indexation automatique</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Indexation à la publication</th>
                    <td>
                        <label>
                            <input type="checkbox" id="ia1_auto_index" checked disabled>
                            Indexer automatiquement les nouveaux contenus
                        </label>
                        <p class="description">
                            Cette option est toujours activée pour garantir que votre IA reste à jour.
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Informations -->
        <div class="ia1-section">
            <h2 class="ia1-section-title">ℹ️ Informations</h2>
            
            <div class="notice notice-info inline">
                <p>
                    <strong>Que fait l'indexation ?</strong><br>
                    L'indexation analyse vos articles, pages et produits pour permettre à IA1 de les comprendre
                    et d'y répondre. Le contenu est stocké localement dans votre base de données WordPress.
                </p>
            </div>
            
            <div class="notice notice-warning inline" style="margin-top: 10px;">
                <p>
                    <strong>Note :</strong> Seul le contenu <strong>publié</strong> est indexé. Les brouillons
                    et les contenus privés ne sont pas accessibles à l'IA.
                </p>
            </div>
        </div>
        
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    
    $('#ia1-start-indexation').on('click', function() {
        const $btn = $(this);
        const $progress = $('#ia1-indexation-progress');
        const $progressFill = $('.ia1-progress-fill');
        const $status = $('#ia1-indexation-status');
        
        if (!confirm('Êtes-vous sûr de vouloir réindexer tout le contenu ?')) {
            return;
        }
        
        $btn.prop('disabled', true).text('Indexation en cours...');
        $progress.show();
        $progressFill.css('width', '10%').text('10%');
        $status.text('Préparation...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'ia1_reindex_content',
                nonce: '<?php echo wp_create_nonce( 'ia1_admin' ); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $progressFill.css('width', '100%').text('100%');
                    $status.html('<strong style="color: #00a32a;">✓ Indexation terminée !</strong> ' + 
                                response.data.indexed + ' documents indexés.');
                    
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    $status.html('<strong style="color: #d63638;">✗ Erreur :</strong> ' + response.data.message);
                }
            },
            error: function() {
                $status.html('<strong style="color: #d63638;">✗ Erreur de connexion</strong>');
            },
            complete: function() {
                $btn.prop('disabled', false).text('🚀 Réindexer tout le contenu');
            }
        });
        
        // Animation de la barre de progression
        let progress = 10;
        const interval = setInterval(function() {
            if (progress < 90) {
                progress += 5;
                $progressFill.css('width', progress + '%').text(progress + '%');
                $status.text('Indexation en cours... ' + progress + '%');
            } else {
                clearInterval(interval);
            }
        }, 500);
    });
    
});
</script>
