<?php
/**
 * Gestion de l'API Mistral AI
 *
 * @package IA1
 * @version 3.1.10 - Prompt intelligent amélioré
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class IA1_Mistral {
    
    private $api_url = 'https://api.mistral.ai/v1/chat/completions';
    private $api_key;
    private $model;
    private $temperature;
    private $system_prompt;
    
    public function __construct() {
        $settings = IA1_Settings::get_api_settings();
        
        $this->api_key = $settings['api_key'];
        $this->model = $settings['model'];
        $this->temperature = $settings['temperature'];
        $this->system_prompt = $settings['system_prompt'];
    }
    
    /**
     * Génère une réponse à partir d'une question et de contextes
     */
    public function generate_response( $question, $contexts = array() ) {
        if ( empty( $this->api_key ) ) {
            throw new Exception( 'Clé API Mistral non configurée' );
        }
        
        // Construire le contexte
        $context_text = $this->build_context_text( $contexts );
        
        // Construire le prompt AMÉLIORÉ
        $user_prompt = $this->build_smart_user_prompt( $question, $context_text, $contexts );
        
        // Préparer les messages
        $messages = array(
            array(
                'role' => 'system',
                'content' => $this->system_prompt
            ),
            array(
                'role' => 'user',
                'content' => $user_prompt
            )
        );
        
        // Appeler l'API
        $response = $this->call_api( $messages );
        
        // Extraire la réponse
        $text = $response['choices'][0]['message']['content'] ?? 'Pas de réponse';
        
        // Préparer les sources
        $sources = $this->prepare_sources( $contexts );
        
        return array(
            'text' => $text,
            'sources' => $sources,
            'model' => $this->model,
            'tokens_used' => $response['usage'] ?? null
        );
    }
    
    /**
     * Construit le texte de contexte à partir des résultats de recherche
     */
    private function build_context_text( $contexts ) {
        if ( empty( $contexts ) ) {
            return 'Aucun contexte disponible.';
        }
        
        $context_parts = array();
        
        foreach ( $contexts as $i => $context ) {
            $excerpt = $context['excerpt'] ?? substr( $context['content'], 0, 300 );
            
            // Ajouter des métadonnées pour aider l'IA
            $metadata = '';
            if ( isset( $context['is_hub_page'] ) && $context['is_hub_page'] ) {
                $metadata .= ' [PAGE PRINCIPALE]';
            }
            if ( isset( $context['category'] ) && $context['category'] !== $context['post_type'] ) {
                $metadata .= ' [Catégorie: ' . $context['category'] . ']';
            }
            
            $context_parts[] = sprintf(
                "Source %d%s: %s\nType: %s\nURL: %s\nContenu: %s",
                $i + 1,
                $metadata,
                $context['title'],
                $context['post_type'],
                $context['url'],
                $excerpt
            );
        }
        
        return implode( "\n\n---\n\n", $context_parts );
    }
    
    /**
     * Construit un prompt utilisateur INTELLIGENT (v3.1.10)
     * 
     * Améliorations :
     * - Guide l'IA vers les pages principales/hub
     * - Détecte les intentions (achat, info, contact...)
     * - Encourage l'IA à faire des liens intelligents
     */
    private function build_smart_user_prompt( $question, $context_text, $contexts ) {
        // Analyser l'intention de la question
        $intent = $this->detect_query_intent( $question );
        
        // Identifier les pages principales dans les résultats
        $hub_pages = array_filter( $contexts, function( $c ) {
            return isset( $c['is_hub_page'] ) && $c['is_hub_page'];
        });
        
        // Construire les instructions spécifiques
        $specific_instructions = $this->build_intent_instructions( $intent, $hub_pages );
        
        return sprintf(
            "Voici le contenu disponible sur le site :\n\n%s\n\n---\n\n%s\n\nQuestion de l'utilisateur : %s\n\n%s",
            $context_text,
            $specific_instructions,
            $question,
            $this->get_response_guidelines()
        );
    }
    
    /**
     * Détecte l'intention de la question
     */
    private function detect_query_intent( $question ) {
        $question_lower = strtolower( $question );
        
        $intents = array(
            'purchase' => array( 'acheter', 'commander', 'prix', 'coût', 'combien', 'boutique', 'shop', 'vendre', 'disponible' ),
            'contact' => array( 'contact', 'appeler', 'téléphone', 'email', 'écrire', 'joindre', 'parler' ),
            'information' => array( 'qu\'est-ce', 'c\'est quoi', 'comment', 'pourquoi', 'définition' ),
            'navigation' => array( 'où', 'trouver', 'page', 'section', 'aller' ),
            'general' => array()
        );
        
        foreach ( $intents as $intent => $keywords ) {
            foreach ( $keywords as $keyword ) {
                if ( strpos( $question_lower, $keyword ) !== false ) {
                    return $intent;
                }
            }
        }
        
        return 'general';
    }
    
    /**
     * Construit des instructions spécifiques selon l'intention
     */
    private function build_intent_instructions( $intent, $hub_pages ) {
        $instructions = "INSTRUCTIONS IMPORTANTES :\n";
        
        switch ( $intent ) {
            case 'purchase':
                $instructions .= "- L'utilisateur cherche à ACHETER ou consulter des PRODUITS\n";
                $instructions .= "- Privilégie les pages boutique/shop dans ta réponse\n";
                if ( ! empty( $hub_pages ) ) {
                    $instructions .= "- Les pages marquées [PAGE PRINCIPALE] sont probablement des pages de catégorie ou boutique principale\n";
                    $instructions .= "- ORIENTE L'UTILISATEUR vers ces pages principales plutôt que vers des produits individuels\n";
                }
                break;
                
            case 'contact':
                $instructions .= "- L'utilisateur cherche à CONTACTER le site\n";
                $instructions .= "- Cherche les informations de contact dans les sources\n";
                break;
                
            case 'navigation':
                $instructions .= "- L'utilisateur cherche à NAVIGUER vers une section spécifique\n";
                if ( ! empty( $hub_pages ) ) {
                    $instructions .= "- Les pages marquées [PAGE PRINCIPALE] sont des points d'entrée importants\n";
                    $instructions .= "- Dirige l'utilisateur vers la page principale la plus pertinente\n";
                }
                break;
                
            case 'information':
            default:
                $instructions .= "- L'utilisateur cherche des INFORMATIONS\n";
                $instructions .= "- Base-toi sur le contenu fourni pour répondre\n";
                break;
        }
        
        return $instructions;
    }
    
    /**
     * Règles générales de réponse
     */
    private function get_response_guidelines() {
        return "RÈGLES DE RÉPONSE :
1. Base-toi principalement sur le contenu fourni
2. Si plusieurs pages sont pertinentes, privilégie les [PAGE PRINCIPALE] dans tes recommandations
3. Si tu ne trouves pas l'information exacte, suggère la page la plus pertinente où l'utilisateur pourrait chercher
4. Reste naturel et conversationnel dans ton ton
5. Fournis toujours des liens clairs vers les pages mentionnées";
    }
    
    /**
     * Prépare la liste des sources
     */
    private function prepare_sources( $contexts ) {
        $sources = array();
        
        foreach ( $contexts as $context ) {
            $sources[] = array(
                'title' => $context['title'],
                'url' => $context['url'],
                'post_type' => $context['post_type']
            );
        }
        
        return $sources;
    }
    
    /**
     * Appelle l'API Mistral
     */
    private function call_api( $messages ) {
        // Nettoyer tous les textes UTF-8 AVANT l'encodage JSON
        $messages = $this->clean_utf8_recursive( $messages );
        
        $body = array(
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => floatval( $this->temperature ),
            'max_tokens' => 1000
        );
        
        $json_body = json_encode( $body, JSON_UNESCAPED_UNICODE );
        
        // Vérifier que l'encodage JSON a réussi
        if ( $json_body === false ) {
            throw new Exception( 'Erreur d\'encodage JSON : ' . json_last_error_msg() );
        }
        
        $response = wp_remote_post( $this->api_url, array(
            'headers' => array(
                'Content-Type' => 'application/json; charset=utf-8',
                'Authorization' => 'Bearer ' . $this->api_key
            ),
            'body' => $json_body,
            'timeout' => 30,
            'sslverify' => true
        ));
        
        // Vérifier les erreurs
        if ( is_wp_error( $response ) ) {
            throw new Exception( 'Erreur de connexion à Mistral AI : ' . $response->get_error_message() );
        }
        
        $status_code = wp_remote_retrieve_response_code( $response );
        if ( $status_code !== 200 ) {
            $error_body = wp_remote_retrieve_body( $response );
            throw new Exception( "Erreur API Mistral (code {$status_code}) : {$error_body}" );
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            throw new Exception( 'Erreur de décodage de la réponse Mistral' );
        }
        
        return $data;
    }
    
    /**
     * Nettoie récursivement les caractères UTF-8 mal formés
     */
    private function clean_utf8_recursive( $data ) {
        if ( is_string( $data ) ) {
            // Nettoyer la chaîne UTF-8
            return mb_convert_encoding( $data, 'UTF-8', 'UTF-8' );
        }
        
        if ( is_array( $data ) ) {
            return array_map( array( $this, 'clean_utf8_recursive' ), $data );
        }
        
        return $data;
    }
    
    /**
     * Test de connexion à l'API
     */
    public function test_connection() {
        try {
            $messages = array(
                array(
                    'role' => 'user',
                    'content' => 'Réponds simplement "OK" si tu me reçois.'
                )
            );
            
            $response = $this->call_api( $messages );
            
            return array(
                'success' => true,
                'message' => 'Connexion réussie à Mistral AI',
                'model' => $this->model
            );
            
        } catch ( Exception $e ) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }
}
