<?php
/**
 * Gestion du frontend du plugin IA1
 *
 * @package IA1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class IA1_Public {
    
    public function __construct() {
        // Enregistrer les shortcodes
        add_shortcode( 'ia1_chat', array( $this, 'render_chat_widget' ) );
        add_shortcode( 'lydia_chat', array( $this, 'render_chat_widget' ) ); // Rétrocompatibilité
        
        // Enregistrer les assets
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
        
        // AJAX pour les requêtes de chat
        add_action( 'wp_ajax_ia1_chat_query', array( $this, 'ajax_chat_query' ) );
        add_action( 'wp_ajax_nopriv_ia1_chat_query', array( $this, 'ajax_chat_query' ) );
    }
    
    /**
     * Charge les assets CSS/JS du frontend
     */
    public function enqueue_public_assets() {
        // CSS du chat
        wp_enqueue_style(
            'ia1-chat',
            IA1_PLUGIN_URL . 'public/css/ia1-chat.css',
            array(),
            IA1_VERSION
        );
        
        // JavaScript du chat
        wp_enqueue_script(
            'ia1-chat',
            IA1_PLUGIN_URL . 'public/js/ia1-chat.js',
            array( 'jquery' ),
            IA1_VERSION,
            true
        );
        
        // Récupérer les paramètres de personnalisation
        $customization = IA1_Settings::get_customization_settings();
        
        // Variables JavaScript
        wp_localize_script( 'ia1-chat', 'ia1Chat', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'ia1_chat' ),
            'settings' => $customization
        ));
        
        // Injecter les CSS personnalisés inline
        $custom_css = $this->generate_custom_css( $customization );
        wp_add_inline_style( 'ia1-chat', $custom_css );
    }
    
    /**
     * Génère le CSS personnalisé basé sur les paramètres
     */
    private function generate_custom_css( $customization ) {
        $color = esc_attr( $customization['primary_color'] );
        
        return "
            .ia1-chat-header {
                background-color: {$color} !important;
            }
            
            .ia1-chat-avatar {
                background-color: {$color} !important;
            }
            
            .ia1-chat-send-btn {
                background-color: #f0f0f0 !important;
                color: #666 !important;
                border: none !important;
            }
            
            .ia1-chat-send-btn:hover {
                background-color: #e0e0e0 !important;
                color: #333 !important;
            }
        ";
    }
    
    /**
     * Assombrit une couleur hex
     */
    private function darken_color( $hex, $percent ) {
        $hex = str_replace( '#', '', $hex );
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        
        $r = max( 0, min( 255, $r - ( $r * $percent / 100 ) ) );
        $g = max( 0, min( 255, $g - ( $g * $percent / 100 ) ) );
        $b = max( 0, min( 255, $b - ( $b * $percent / 100 ) ) );
        
        return sprintf( '#%02x%02x%02x', $r, $g, $b );
    }
    
    /**
     * Rend le widget de chat (shortcode)
     */
    public function render_chat_widget( $atts ) {
        // Attributs par défaut
        $atts = shortcode_atts( array(
            'placeholder' => 'Demander à ' . get_option( 'ia1_assistant_name', 'IA1' ),
            'height' => '500px'
        ), $atts );
        
        // Vérifier que le plugin est configuré
        if ( ! IA1_Settings::is_configured() ) {
            if ( current_user_can( 'manage_options' ) ) {
                return '<div class="ia1-error">⚠️ IA1 n\'est pas encore configuré. <a href="' . admin_url( 'admin.php?page=ia1' ) . '">Configurer maintenant</a></div>';
            }
            return '';
        }
        
        // Récupérer les paramètres
        $customization = IA1_Settings::get_customization_settings();
        
        // Buffer de sortie
        ob_start();
        include IA1_PLUGIN_DIR . 'public/views/chat-widget.php';
        return ob_get_clean();
    }
    
    /**
     * AJAX : Traite une requête de chat
     */
    public function ajax_chat_query() {
        check_ajax_referer( 'ia1_chat', 'nonce' );
        
        $question = sanitize_textarea_field( $_POST['question'] ?? '' );
        
        if ( empty( $question ) ) {
            wp_send_json_error( array( 'message' => 'Question vide' ) );
        }
        
        try {
            // Rechercher dans l'index
            $indexer = new IA1_Indexer();
            $contexts = $indexer->search( $question );
            
            // Appeler Mistral AI
            $mistral = new IA1_Mistral();
            $response = $mistral->generate_response( $question, $contexts );
            
            wp_send_json_success( array(
                'response' => $response['text'],
                'sources' => $response['sources'],
                'contexts_used' => count( $contexts )
            ));
            
        } catch ( Exception $e ) {
            wp_send_json_error( array( 
                'message' => 'Erreur : ' . $e->getMessage() 
            ));
        }
    }
}
