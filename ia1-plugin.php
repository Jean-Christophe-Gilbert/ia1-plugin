<?php
/**
 * Plugin Name: IA1 - Intelligence Artificielle Locale
 * Plugin URI: https://ia1.fr
 * Description: Assistant IA conversationnel local basé sur Mistral AI, avec indexation RAG de votre contenu WordPress
 * Version: 3.1.9
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: IA1
 * Author URI: https://ia1.fr
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: ia1
 * Domain Path: /languages
 */

// Si ce fichier est appelé directement, on arrête
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Constantes du plugin
define( 'IA1_VERSION', '3.1.9' );
define( 'IA1_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IA1_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'IA1_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Classe principale du plugin IA1
 */
class IA1_Plugin {
    
    /**
     * Instance unique du plugin (Singleton)
     */
    private static $instance = null;
    
    /**
     * Récupère l'instance unique du plugin
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructeur privé (Singleton)
     */
    private function __construct() {
        $this->load_dependencies();
        $this->define_hooks();
    }
    
    /**
     * Charge les dépendances du plugin
     */
    private function load_dependencies() {
        // Classes principales
        require_once IA1_PLUGIN_DIR . 'includes/class-ia1-settings.php';
        require_once IA1_PLUGIN_DIR . 'includes/class-ia1-core.php';
        require_once IA1_PLUGIN_DIR . 'includes/class-ia1-indexer.php';
        require_once IA1_PLUGIN_DIR . 'includes/class-ia1-mistral.php';
        
        // Admin
        if ( is_admin() ) {
            require_once IA1_PLUGIN_DIR . 'admin/class-ia1-admin.php';
        }
        
        // Public
        require_once IA1_PLUGIN_DIR . 'public/class-ia1-public.php';
    }
    
    /**
     * Définit les hooks WordPress
     */
    private function define_hooks() {
        // Activation et désactivation
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
        
        // Initialisation
        add_action( 'plugins_loaded', array( $this, 'init' ) );
        
        // Admin
        if ( is_admin() ) {
            $admin = new IA1_Admin();
        }
        
        // Public
        $public = new IA1_Public();
    }
    
    /**
     * Initialise le plugin
     */
    public function init() {
        // Chargement des traductions
        load_plugin_textdomain( 'ia1', false, dirname( IA1_PLUGIN_BASENAME ) . '/languages' );
        
        // Migration automatique depuis Lydia
        $this->maybe_migrate_from_lydia();
    }
    
    /**
     * Activation du plugin
     */
    public function activate() {
        // Créer les tables si nécessaire
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_name = $wpdb->prefix . 'ia1_index';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            post_type varchar(20) NOT NULL,
            title text NOT NULL,
            content longtext NOT NULL,
            url varchar(255) NOT NULL,
            embedding longtext,
            indexed_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY post_type (post_type)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        
        // Options par défaut
        $defaults = array(
            'ia1_api_key' => '',
            'ia1_model' => 'mistral-small-latest',
            'ia1_temperature' => 0.7,
            'ia1_max_contexts' => 5,
            
            // Personnalisation (NOUVEAU)
            'ia1_assistant_name' => 'IA1',
            'ia1_assistant_subtitle' => 'Votre assistante IA locale',
            'ia1_welcome_message' => 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?',
            'ia1_primary_color' => '#2271b1',
            'ia1_avatar_initials' => 'IA',
            'ia1_system_prompt' => 'Tu es un assistant conversationnel intégré à un site WordPress. Tu aides les visiteurs à trouver l\'information qu\'ils cherchent en te basant sur le contenu du site.',
            'ia1_use_wikipedia' => true,
        );
        
        foreach ( $defaults as $option => $value ) {
            if ( false === get_option( $option ) ) {
                add_option( $option, $value );
            }
        }
        
        // Version du plugin
        update_option( 'ia1_version', IA1_VERSION );
    }
    
    /**
     * Désactivation du plugin
     */
    public function deactivate() {
        // Nettoyage si nécessaire
        // Note : on ne supprime pas les données pour permettre la réactivation
    }
    
    /**
     * Migration automatique depuis Lydia (si ancienne version détectée)
     */
    private function maybe_migrate_from_lydia() {
        // Si on détecte des options Lydia, on les migre
        $lydia_api_key = get_option( 'lydia_api_key' );
        
        if ( $lydia_api_key && ! get_option( 'ia1_api_key' ) ) {
            // Migration des options
            update_option( 'ia1_api_key', $lydia_api_key );
            update_option( 'ia1_model', get_option( 'lydia_model', 'mistral-small-latest' ) );
            update_option( 'ia1_temperature', get_option( 'lydia_temperature', 0.7 ) );
            update_option( 'ia1_max_contexts', get_option( 'lydia_max_contexts', 5 ) );
            
            // Pour la rétrocompatibilité, on garde "Lydia" comme nom par défaut pour les migrations
            update_option( 'ia1_assistant_name', 'Lydia' );
            update_option( 'ia1_assistant_subtitle', 'Votre assistante IA' );
            
            // Migration de la table d'index si elle existe
            global $wpdb;
            $old_table = $wpdb->prefix . 'lydia_index';
            $new_table = $wpdb->prefix . 'ia1_index';
            
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$old_table'" ) === $old_table ) {
                $wpdb->query( "INSERT IGNORE INTO $new_table SELECT * FROM $old_table" );
            }
            
            // Marquer la migration comme complète
            update_option( 'ia1_migrated_from_lydia', true );
            
            // Notification admin
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible">';
                echo '<p><strong>IA1 :</strong> Migration depuis Lydia réussie ! Vos données ont été préservées.</p>';
                echo '</div>';
            });
        }
    }
}

/**
 * Démarre le plugin
 */
function ia1_init() {
    return IA1_Plugin::get_instance();
}

// Lancer le plugin
ia1_init();
