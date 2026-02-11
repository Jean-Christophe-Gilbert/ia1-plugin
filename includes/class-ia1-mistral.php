<?php
/**
 * Gestion de l'API Mistral AI - VERSION AMÉLIORÉE
 *
 * @package IA1
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
        
        // Utiliser le nouveau prompt système amélioré
        $this->system_prompt = $this->get_improved_system_prompt();
    }
    
    /**
     * Prompt système amélioré pour plus de précision
     */
    private function get_improved_system_prompt() {
        $assistant_name = get_option( 'ia1_assistant_name', 'IA1' );
        $site_name = get_bloginfo( 'name' );
        
        return "Tu es {$assistant_name}, l'assistant conversationnel du site « {$site_name} ».

## Ta mission
Aider les visiteurs à trouver l'information qu'ils cherchent en te basant EXCLUSIVEMENT sur le contenu du site qui te sera fourni.

## Règles absolues

### 1. Sources et véracité
- Tu réponds UNIQUEMENT avec les informations présentes dans les sources fournies
- Si l'information n'est pas dans les sources, tu dis clairement « Je n'ai pas trouvé cette information dans le contenu du site »
- Tu NE dois JAMAIS inventer ou deviner une information
- Cite TOUJOURS les sources en utilisant le format [Source X] où X est le numéro de la source

### 2. Style de réponse
- Réponds de manière naturelle et conversationnelle, comme si tu parlais à quelqu'un
- Sois précis et factuel, mais évite le jargon inutile
- Va droit au but : commence par répondre à la question, puis développe si nécessaire
- Si la question est complexe ou a plusieurs parties, structure ta réponse clairement

### 3. Format et présentation
- N'utilise AUCUN formatage Markdown (pas de **, pas de #, pas de -, pas de *)
- Écris en texte simple avec des retours à la ligne pour aérer
- Sépare les paragraphes par une ligne vide
- Pour les listes, utilise des numéros suivis d'un point (1. 2. 3.) ou des phrases simples

### 4. Citations des sources
- Chaque fois que tu donnes une information, cite la source : [Source 1], [Source 2], etc.
- Si plusieurs sources disent la même chose, cite-les toutes : [Sources 1 et 3]
- Place la citation juste après l'information concernée

### 5. Gestion de l'incertitude
- Si tu n'es pas certain, dis-le : « D'après les informations disponibles... » ou « Le site mentionne que... »
- Si les sources sont contradictoires, signale-le clairement
- Si la question est ambiguë, demande une précision

### 6. Ce que tu ne dois PAS faire
- Ne réponds JAMAIS sur des sujets non présents dans les sources
- N'invente JAMAIS de liens, prix, dates ou détails
- Ne donne JAMAIS d'opinions personnelles
- N'utilise JAMAIS de formatage Markdown

## Exemples de bonnes réponses

Question : « Quel est le prix du produit X ? »
Réponse : « Le produit X est proposé à 49 euros [Source 1]. Il est disponible en trois coloris : rouge, bleu et vert [Source 1]. »

Question : « Comment puis-je vous contacter ? »
Réponse : « Vous pouvez nous contacter de plusieurs façons [Source 2] :

1. Par email à contact@exemple.fr
2. Par téléphone au 01 23 45 67 89 du lundi au vendredi de 9h à 18h
3. Via le formulaire de contact disponible sur la page Contact

Notre équipe vous répondra sous 24 heures en moyenne [Source 2]. »

Question : « Faites-vous des réductions ? »
(Si pas dans les sources) Réponse : « Je n'ai pas trouvé d'information sur les réductions dans le contenu actuel du site. Je vous invite à nous contacter directement pour connaître nos offres en cours. »";
    }
    
    /**
     * Génère une réponse à partir d'une question et de contextes
     */
    public function generate_response( $question, $contexts = array() ) {
        if ( empty( $this->api_key ) ) {
            throw new Exception( 'Clé API Mistral non configurée' );
        }
        
        // Construire le contexte avec le nouveau format amélioré
        $context_text = $this->build_improved_context_text( $contexts );
        
        // Construire le prompt utilisateur avec le nouveau format
        $user_prompt = $this->build_improved_user_prompt( $question, $context_text, $contexts );
        
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
     * Construit le texte de contexte de manière structurée et hiérarchisée
     */
    private function build_improved_context_text( $contexts ) {
        if ( empty( $contexts ) ) {
            return 'Aucune information pertinente n\'a été trouvée dans le contenu du site pour répondre à cette question.';
        }
        
        $context_parts = array();
        
        foreach ( $contexts as $i => $context ) {
            $source_number = $i + 1;
            
            // Déterminer le type de contenu pour aider l'IA
            $type_label = $this->get_content_type_label( $context['post_type'] );
            
            // Extraire un contenu pertinent (pas juste 300 caractères arbitraires)
            $content = $this->extract_relevant_content( $context );
            
            // Format structuré et clair
            $context_parts[] = sprintf(
                "=== SOURCE %d ===
Type : %s
Titre : %s
URL : %s

Contenu :
%s",
                $source_number,
                $type_label,
                $context['title'],
                $context['url'],
                $content
            );
        }
        
        return implode( "\n\n", $context_parts );
    }
    
    /**
     * Extrait le contenu pertinent de manière intelligente
     */
    private function extract_relevant_content( $context ) {
        $content = $context['content'];
        
        // Si le contenu est court (< 500 caractères), le prendre en entier
        if ( strlen( $content ) <= 500 ) {
            return trim( $content );
        }
        
        // Sinon, prendre un extrait intelligent
        // Option 1 : Si un excerpt existe, l'utiliser
        if ( ! empty( $context['excerpt'] ) && strlen( $context['excerpt'] ) > 50 ) {
            return trim( $context['excerpt'] );
        }
        
        // Option 2 : Prendre le début du contenu (jusqu'à 600 caractères)
        // en coupant à la fin d'une phrase
        $excerpt = substr( $content, 0, 600 );
        
        // Trouver le dernier point
        $last_period = strrpos( $excerpt, '.' );
        if ( $last_period !== false && $last_period > 200 ) {
            $excerpt = substr( $excerpt, 0, $last_period + 1 );
        }
        
        return trim( $excerpt ) . '...';
    }
    
    /**
     * Retourne un label compréhensible pour le type de contenu
     */
    private function get_content_type_label( $post_type ) {
        $labels = array(
            'post' => 'Article de blog',
            'page' => 'Page du site',
            'product' => 'Produit',
            'service' => 'Service',
        );
        
        return $labels[ $post_type ] ?? ucfirst( $post_type );
    }
    
    /**
     * Construit le prompt utilisateur amélioré
     */
    private function build_improved_user_prompt( $question, $context_text, $contexts ) {
        $num_sources = count( $contexts );
        
        if ( $num_sources === 0 ) {
            return sprintf(
                "Question de l'utilisateur : %s

Aucune source n'a été trouvée dans le contenu du site pour répondre à cette question.

Indique poliment à l'utilisateur que tu n'as pas trouvé d'information sur ce sujet dans le contenu actuel du site, et suggère-lui de reformuler sa question ou de contacter directement l'équipe du site.",
                $question
            );
        }
        
        return sprintf(
            "Voici le contenu pertinent trouvé sur le site (%d source%s) :

%s

---

Question de l'utilisateur : %s

Instructions de réponse :
1. Réponds à la question en te basant EXCLUSIVEMENT sur les sources ci-dessus
2. Cite chaque source utilisée avec le format [Source X]
3. Si l'information n'est pas présente, dis-le clairement
4. Sois précis et factuel
5. N'utilise AUCUN formatage Markdown
6. Commence directement par répondre à la question",
            $num_sources,
            $num_sources > 1 ? 's' : '',
            $context_text,
            $question
        );
    }
    
    /**
     * Prépare la liste des sources avec plus de détails
     */
    private function prepare_sources( $contexts ) {
        $sources = array();
        
        foreach ( $contexts as $i => $context ) {
            $sources[] = array(
                'title' => sprintf( 'Source %d : %s', $i + 1, $context['title'] ),
                'url' => $context['url'],
                'post_type' => $context['post_type'],
                'type_label' => $this->get_content_type_label( $context['post_type'] )
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
            'max_tokens' => 1500  // Augmenté pour des réponses plus complètes
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
