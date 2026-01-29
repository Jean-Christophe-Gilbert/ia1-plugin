<?php
/**
 * Interface d'administration du plugin IA1
 *
 * @package IA1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class IA1_Admin {
    
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'admin_init', array( $this, 'handle_form_submission' ) );
        add_action( 'wp_ajax_ia1_save_settings', array( $this, 'ajax_save_settings' ) );
        add_action( 'wp_ajax_ia1_reset_customization', array( $this, 'ajax_reset_customization' ) );
        add_action( 'wp_ajax_ia1_reindex_content', array( $this, 'ajax_reindex_content' ) );
    }
    
    /**
     * Ajoute le menu dans l'admin WordPress
     */
    public function add_admin_menu() {
        add_menu_page(
            'IA1 - Configuration',
            'IA1',
            'manage_options',
            'ia1',
            array( $this, 'display_admin_page' ),
            'dashicons-admin-generic',
            30
        );
        
        // Sous-menu : Configuration (page principale)
        add_submenu_page(
            'ia1',
            'Configuration IA1',
            'Configuration',
            'manage_options',
            'ia1',
            array( $this, 'display_admin_page' )
        );
        
        // Sous-menu : Indexation
        add_submenu_page(
            'ia1',
            'Indexation IA1',
            'Indexation',
            'manage_options',
            'ia1-indexation',
            array( $this, 'display_indexation_page' )
        );
    }
    
    /**
     * Charge les assets CSS/JS pour l'admin
     */
    public function enqueue_admin_assets( $hook ) {
        // Ne charger que sur les pages IA1
        if ( strpos( $hook, 'ia1' ) === false ) {
            return;
        }
        
        wp_enqueue_style(
            'ia1-admin',
            IA1_PLUGIN_URL . 'admin/css/ia1-admin.css',
            array(),
            IA1_VERSION
        );
        
        wp_enqueue_script(
            'ia1-admin',
            IA1_PLUGIN_URL . 'admin/js/ia1-admin.js',
            array( 'jquery' ),
            IA1_VERSION,
            true
        );
        
        // Variables JavaScript
        wp_localize_script( 'ia1-admin', 'ia1Admin', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'ia1_admin' ),
            'settings' => array_merge(
                IA1_Settings::get_customization_settings(),
                IA1_Settings::get_api_settings()
            )
        ));
    }
    
    /**
     * Affiche la page de configuration principale
     */
    public function display_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Vous n\'avez pas les permissions nécessaires.' );
        }
        
        $customization = IA1_Settings::get_customization_settings();
        $api = IA1_Settings::get_api_settings();
        
        include IA1_PLUGIN_DIR . 'admin/views/admin-page.php';
    }
    
    /**
     * Traite la soumission du formulaire (méthode standard, non-AJAX)
     */
    public function handle_form_submission() {
        if ( ! isset( $_POST['ia1_save_settings'] ) ) {
            return;
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        check_admin_referer( 'ia1_save_settings_action', 'ia1_settings_nonce' );
        
        // Récupérer toutes les données
        $data = array(
            'api_key' => sanitize_text_field( wp_unslash( $_POST['api_key'] ?? '' ) ),
            'model' => sanitize_text_field( wp_unslash( $_POST['model'] ?? 'mistral-small-latest' ) ),
            'assistant_name' => sanitize_text_field( wp_unslash( $_POST['assistant_name'] ?? 'IA1' ) ),
            'assistant_subtitle' => sanitize_text_field( wp_unslash( $_POST['assistant_subtitle'] ?? '' ) ),
            'welcome_message' => sanitize_textarea_field( wp_unslash( $_POST['welcome_message'] ?? '' ) ),
            'primary_color' => sanitize_text_field( wp_unslash( $_POST['primary_color'] ?? '#2271b1' ) ),
            'avatar_initials' => sanitize_text_field( wp_unslash( $_POST['avatar_initials'] ?? 'IA' ) ),
            'system_prompt' => sanitize_textarea_field( wp_unslash( $_POST['system_prompt'] ?? '' ) ),
            'use_wikipedia' => isset( $_POST['use_wikipedia'] ),
        );
        
        // Sauvegarder
        IA1_Settings::save_customization_settings( $data );
        IA1_Settings::save_api_settings( $data );
        
        // Rediriger avec message de succès
        wp_redirect( add_query_arg( array(
            'page' => 'ia1',
            'settings-updated' => 'true'
        ), admin_url( 'admin.php' ) ) );
        exit;
    }
    
    /**
     * Affiche la page d'indexation
     */
    public function display_indexation_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Vous n\'avez pas les permissions nécessaires.' );
        }
        
        include IA1_PLUGIN_DIR . 'admin/views/indexation-page.php';
    }
    
    /**
     * AJAX : Sauvegarde des paramètres
     */
    public function ajax_save_settings() {
        check_ajax_referer( 'ia1_admin', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }
        
        $data = $_POST['data'] ?? array();
        
        try {
            // Sauvegarder les paramètres de personnalisation
            $customization_updated = IA1_Settings::save_customization_settings( $data );
            
            // Sauvegarder les paramètres API
            $api_updated = IA1_Settings::save_api_settings( $data );
            
            wp_send_json_success( array(
                'message' => 'Paramètres sauvegardés avec succès !',
                'customization' => $customization_updated,
                'api' => $api_updated
            ));
            
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'message' => $e->getMessage() ) );
        }
    }
    
    /**
     * AJAX : Réinitialisation de la personnalisation
     */
    public function ajax_reset_customization() {
        check_ajax_referer( 'ia1_admin', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }
        
        IA1_Settings::reset_customization();
        
        wp_send_json_success( array(
            'message' => 'Personnalisation réinitialisée',
            'settings' => IA1_Settings::get_customization_settings()
        ));
    }
    
    /**
     * AJAX : Réindexation du contenu
     */
    public function ajax_reindex_content() {
        check_ajax_referer( 'ia1_admin', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission refusée' ) );
        }
        
        try {
            $indexer = new IA1_Indexer();
            $result = $indexer->reindex_all();
            
            wp_send_json_success( array(
                'message' => 'Indexation terminée !',
                'indexed' => $result['indexed'],
                'errors' => $result['errors']
            ));
            
        } catch ( Exception $e ) {
            wp_send_json_error( array( 'message' => $e->getMessage() ) );
        }
    }
}
