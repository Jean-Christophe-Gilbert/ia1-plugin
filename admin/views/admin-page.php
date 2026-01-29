<?php
/**
 * Template de la page d'administration principale
 *
 * @package IA1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap ia1-admin-wrap">
    <div class="ia1-admin-container">
        
        <!-- Header -->
        <h1 class="ia1-admin-title">
            <img src="https://ia1.fr/wp-content/uploads/2026/01/cropped-Gemini_Generated_Image_e2r4dee2r4dee2r4.png" alt="IA1 Logo" class="ia1-logo">
            Configuration IA1
        </h1>
        
        <!-- Notifications -->
        <div id="ia1-notifications">
            <?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] === 'true' ) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><strong>Paramètres sauvegardés avec succès !</strong></p>
                </div>
            <?php endif; ?>
        </div>
        
        <form id="ia1-settings-form" method="post" action="">
            <?php wp_nonce_field( 'ia1_save_settings_action', 'ia1_settings_nonce' ); ?>
            <input type="hidden" name="ia1_save_settings" value="1">
            
            <!-- Section Connexion API -->
            <div class="ia1-section">
                <h2 class="ia1-section-title">🔑 Connexion à l'API</h2>
                
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="api_key">Clé API Mistral</label>
                        </th>
                        <td>
                            <div class="ia1-input-addon">
                                <input type="password" 
                                       id="api_key" 
                                       name="api_key" 
                                       value="<?php echo esc_attr( $api['api_key'] ); ?>" 
                                       class="regular-text"
                                       placeholder="cn3ibcxRRgXPiw7b1TlK4RYL43/MJUoFjj">
                                <a href="https://console.mistral.ai" target="_blank" class="button button-secondary">
                                    console.mistral.ai
                                </a>
                            </div>
                            <p class="description">
                                Obtenez votre clé sur <a href="https://console.mistral.ai" target="_blank">console.mistral.ai</a>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="model">Modèle Mistral</label>
                        </th>
                        <td>
                            <select id="model" name="model">
                                <option value="mistral-small-latest" <?php selected( $api['model'], 'mistral-small-latest' ); ?>>
                                    mistral-small-latest (Recommandé)
                                </option>
                                <option value="mistral-medium-latest" <?php selected( $api['model'], 'mistral-medium-latest' ); ?>>
                                    mistral-medium-latest
                                </option>
                                <option value="mistral-large-latest" <?php selected( $api['model'], 'mistral-large-latest' ); ?>>
                                    mistral-large-latest
                                </option>
                            </select>
                            <p class="description">
                                Le modèle small est optimisé pour les conversations, rapide et économique
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Section Personnalisation (NOUVEAU) -->
            <div class="ia1-section ia1-section-highlight">
                <h2 class="ia1-section-title">
                    🎨 Personnalisation de votre assistant
                    <span class="ia1-badge ia1-badge-new">NOUVEAU</span>
                </h2>
                
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="assistant_name">Nom de votre assistant IA</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="assistant_name" 
                                   name="assistant_name" 
                                   value="<?php echo esc_attr( $customization['assistant_name'] ); ?>" 
                                   class="regular-text"
                                   maxlength="30"
                                   placeholder="Ex: Emma, Alex, Assistant Virtuel...">
                            <p class="description">
                                Donnez un nom à votre assistant pour qu'il reflète l'identité de votre marque (max 30 caractères)
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="assistant_subtitle">Sous-titre / Description courte</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="assistant_subtitle" 
                                   name="assistant_subtitle" 
                                   value="<?php echo esc_attr( $customization['assistant_subtitle'] ); ?>" 
                                   class="regular-text"
                                   maxlength="60"
                                   placeholder="Ex: Assistant virtuel de [Votre Entreprise]">
                            <p class="description">
                                Apparaît sous le nom dans l'interface de chat (max 60 caractères)
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="welcome_message">Message d'accueil personnalisé</label>
                        </th>
                        <td>
                            <textarea id="welcome_message" 
                                      name="welcome_message" 
                                      rows="3" 
                                      class="large-text"
                                      placeholder="Ex: Bonjour ! Je suis [Nom] et je suis là pour vous aider..."><?php echo esc_textarea( $customization['welcome_message'] ); ?></textarea>
                            <p class="description">
                                Premier message que verra l'utilisateur en ouvrant le chat
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="primary_color">Couleur principale</label>
                        </th>
                        <td>
                            <div class="ia1-input-addon">
                                <input type="color" 
                                       id="primary_color_picker" 
                                       value="<?php echo esc_attr( $customization['primary_color'] ); ?>">
                                <input type="text" 
                                       id="primary_color" 
                                       name="primary_color" 
                                       value="<?php echo esc_attr( $customization['primary_color'] ); ?>" 
                                       pattern="^#[0-9A-Fa-f]{6}$"
                                       placeholder="#2271b1">
                            </div>
                            <p class="description">
                                Couleur de l'en-tête et des boutons
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="avatar_initials">Initiales avatar</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="avatar_initials" 
                                   name="avatar_initials" 
                                   value="<?php echo esc_attr( $customization['avatar_initials'] ); ?>" 
                                   maxlength="3"
                                   placeholder="Ex: L, IA, AV..."
                                   style="width: 100px; text-transform: uppercase;">
                            <p class="description">
                                1 à 3 caractères affichés dans l'avatar
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Section Comportement -->
            <div class="ia1-section">
                <h2 class="ia1-section-title">⚙️ Comportement de l'IA</h2>
                
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="system_prompt">Instructions système (optionnel)</label>
                        </th>
                        <td>
                            <textarea id="system_prompt" 
                                      name="system_prompt" 
                                      rows="4" 
                                      class="large-text"
                                      placeholder="Ex: Tu es un assistant spécialisé dans le domaine du e-commerce..."><?php echo esc_textarea( $api['system_prompt'] ); ?></textarea>
                            <p class="description">
                                Personnalisez le comportement de l'IA avec des instructions spécifiques à votre activité
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Wikipedia</th>
                        <td>
                            <label>
                                <input type="checkbox" 
                                       id="use_wikipedia" 
                                       name="use_wikipedia" 
                                       value="1" 
                                       <?php checked( $api['use_wikipedia'], true ); ?>>
                                Utiliser Wikipedia pour enrichir les réponses
                            </label>
                            <p class="description">
                                Permet à l'IA de consulter Wikipedia pour des informations générales
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Prévisualisation -->
            <div class="ia1-section">
                <h2 class="ia1-section-title">👀 Prévisualisation</h2>
                
                <div class="ia1-preview-box">
                    <p class="ia1-preview-title">Aperçu de votre widget de chat :</p>
                    
                    <div class="ia1-chat-preview">
                        <div class="ia1-chat-header" style="background-color: <?php echo esc_attr( $customization['primary_color'] ); ?>;">
                            <div class="ia1-chat-avatar" id="preview-avatar" style="background-color: <?php echo esc_attr( $customization['primary_color'] ); ?>;">
                                <?php echo esc_html( $customization['avatar_initials'] ); ?>
                            </div>
                            <div class="ia1-chat-info">
                                <h3 id="preview-name"><?php echo esc_html( $customization['assistant_name'] ); ?></h3>
                                <p id="preview-subtitle"><?php echo esc_html( $customization['assistant_subtitle'] ); ?></p>
                            </div>
                        </div>
                        <div class="ia1-chat-body">
                            <div class="ia1-chat-message" id="preview-message">
                                <?php echo esc_html( $customization['welcome_message'] ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section Intégration -->
            <div class="ia1-section">
                <h2 class="ia1-section-title">🔧 Comment utiliser IA1</h2>
                
                <div class="notice notice-info inline">
                    <p>
                        <strong>💡 Shortcode :</strong> 
                        Utilisez le shortcode <code>[ia1_chat]</code> sur n'importe quelle page de votre site.
                    </p>
                </div>
                
                <p class="description">
                    Exemple : créez une page "Assistance IA" et ajoutez simplement le shortcode. 
                    L'assistant apparaîtra automatiquement avec votre personnalisation.
                </p>
            </div>
            
            <!-- Boutons d'action -->
            <p class="submit">
                <input type="submit" name="submit" class="button button-primary button-large" value="Enregistrer les modifications">
                <button type="button" id="ia1-reset-customization" class="button button-secondary">
                    Réinitialiser la personnalisation
                </button>
            </p>
            
        </form>
        
    </div>
</div>
