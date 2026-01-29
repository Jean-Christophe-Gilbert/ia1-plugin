<?php
/**
 * Gestion des paramètres du plugin IA1
 *
 * @package IA1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class IA1_Settings {
    
    /**
     * Récupère toutes les options de personnalisation
     */
    public static function get_customization_settings() {
        return array(
            'assistant_name' => get_option( 'ia1_assistant_name', 'IA1' ),
            'assistant_subtitle' => get_option( 'ia1_assistant_subtitle', 'Votre assistante IA locale' ),
            'welcome_message' => get_option( 'ia1_welcome_message', 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?' ),
            'primary_color' => get_option( 'ia1_primary_color', '#2271b1' ),
            'avatar_initials' => get_option( 'ia1_avatar_initials', 'IA' ),
        );
    }
    
    /**
     * Récupère les paramètres de l'API Mistral
     */
    public static function get_api_settings() {
        return array(
            'api_key' => get_option( 'ia1_api_key', '' ),
            'model' => get_option( 'ia1_model', 'mistral-small-latest' ),
            'temperature' => (float) get_option( 'ia1_temperature', 0.7 ),
            'max_contexts' => (int) get_option( 'ia1_max_contexts', 5 ),
            'system_prompt' => get_option( 'ia1_system_prompt', 'Tu es un assistant conversationnel intégré à un site WordPress.' ),
            'use_wikipedia' => (bool) get_option( 'ia1_use_wikipedia', true ),
        );
    }
    
    /**
     * Sauvegarde les paramètres de personnalisation
     */
    public static function save_customization_settings( $data ) {
        $updated = array();
        
        // Nom de l'assistant (max 30 caractères)
        if ( isset( $data['assistant_name'] ) ) {
            $name = sanitize_text_field( substr( $data['assistant_name'], 0, 30 ) );
            update_option( 'ia1_assistant_name', $name );
            $updated['assistant_name'] = $name;
        }
        
        // Sous-titre (max 60 caractères)
        if ( isset( $data['assistant_subtitle'] ) ) {
            $subtitle = sanitize_text_field( substr( $data['assistant_subtitle'], 0, 60 ) );
            update_option( 'ia1_assistant_subtitle', $subtitle );
            $updated['assistant_subtitle'] = $subtitle;
        }
        
        // Message de bienvenue
        if ( isset( $data['welcome_message'] ) ) {
            $message = sanitize_textarea_field( $data['welcome_message'] );
            update_option( 'ia1_welcome_message', $message );
            $updated['welcome_message'] = $message;
        }
        
        // Couleur principale (validation hex)
        if ( isset( $data['primary_color'] ) ) {
            $color = sanitize_hex_color( $data['primary_color'] );
            if ( $color ) {
                update_option( 'ia1_primary_color', $color );
                $updated['primary_color'] = $color;
            }
        }
        
        // Initiales avatar (max 3 caractères)
        if ( isset( $data['avatar_initials'] ) ) {
            $initials = strtoupper( sanitize_text_field( substr( $data['avatar_initials'], 0, 3 ) ) );
            update_option( 'ia1_avatar_initials', $initials );
            $updated['avatar_initials'] = $initials;
        }
        
        return $updated;
    }
    
    /**
     * Sauvegarde les paramètres de l'API
     */
    public static function save_api_settings( $data ) {
        $updated = array();
        
        // Clé API
        if ( isset( $data['api_key'] ) ) {
            $api_key = sanitize_text_field( $data['api_key'] );
            update_option( 'ia1_api_key', $api_key );
            $updated['api_key'] = '***'; // Ne pas retourner la clé complète
        }
        
        // Modèle
        if ( isset( $data['model'] ) ) {
            $allowed_models = array( 'mistral-small-latest', 'mistral-medium-latest', 'mistral-large-latest' );
            $model = in_array( $data['model'], $allowed_models ) ? $data['model'] : 'mistral-small-latest';
            update_option( 'ia1_model', $model );
            $updated['model'] = $model;
        }
        
        // Température
        if ( isset( $data['temperature'] ) ) {
            $temp = max( 0.1, min( 1.0, (float) $data['temperature'] ) );
            update_option( 'ia1_temperature', $temp );
            $updated['temperature'] = $temp;
        }
        
        // Nombre de contextes
        if ( isset( $data['max_contexts'] ) ) {
            $contexts = max( 1, min( 10, (int) $data['max_contexts'] ) );
            update_option( 'ia1_max_contexts', $contexts );
            $updated['max_contexts'] = $contexts;
        }
        
        // Prompt système
        if ( isset( $data['system_prompt'] ) ) {
            $prompt = sanitize_textarea_field( $data['system_prompt'] );
            update_option( 'ia1_system_prompt', $prompt );
            $updated['system_prompt'] = $prompt;
        }
        
        // Wikipedia
        if ( isset( $data['use_wikipedia'] ) ) {
            $use_wiki = (bool) $data['use_wikipedia'];
            update_option( 'ia1_use_wikipedia', $use_wiki );
            $updated['use_wikipedia'] = $use_wiki;
        }
        
        return $updated;
    }
    
    /**
     * Vérifie si la configuration est valide
     */
    public static function is_configured() {
        $api_key = get_option( 'ia1_api_key', '' );
        return ! empty( $api_key );
    }
    
    /**
     * Réinitialise les paramètres de personnalisation aux valeurs par défaut
     */
    public static function reset_customization() {
        update_option( 'ia1_assistant_name', 'IA1' );
        update_option( 'ia1_assistant_subtitle', 'Votre assistante IA locale' );
        update_option( 'ia1_welcome_message', 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?' );
        update_option( 'ia1_primary_color', '#2271b1' );
        update_option( 'ia1_avatar_initials', 'IA' );
    }
}
