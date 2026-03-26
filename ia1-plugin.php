<?php
/**
 * Plugin Name: IA1 - Intelligence Artificielle Locale
 * Plugin URI: https://ia1.fr
 * Description: Assistant IA conversationnel local basé sur Mistral AI, avec indexation RAG de votre contenu WordPress. Cultivé à Niort, France.
 * Version: 3.3.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: IA1
 * Author URI: https://ia1.fr
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: ia1
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'IA1_VERSION', '3.3.0' );
define( 'IA1_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IA1_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'IA1_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

class IA1_Plugin {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->define_hooks();
    }

    private function load_dependencies() {
        require_once IA1_PLUGIN_DIR . 'includes/class-ia1-settings.php';
        require_once IA1_PLUGIN_DIR . 'includes/class-ia1-core.php';
        require_once IA1_PLUGIN_DIR . 'includes/class-ia1-indexer.php';
        require_once IA1_PLUGIN_DIR . 'includes/class-ia1-mistral.php';
        require_once IA1_PLUGIN_DIR . 'includes/class-ia1-logger.php';

        if ( is_admin() ) {
            require_once IA1_PLUGIN_DIR . 'admin/class-ia1-admin.php';
            require_once IA1_PLUGIN_DIR . 'admin/class-ia1-logs-admin.php';
        }

        require_once IA1_PLUGIN_DIR . 'public/class-ia1-public.php';
    }

    private function define_hooks() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'plugins_loaded', array( $this, 'init' ) );
        add_action( 'admin_menu', array( 'IA1_Logs_Admin', 'register_menu' ), 20 );

        if ( is_admin() ) {
            $admin = new IA1_Admin();
        }

        $public = new IA1_Public();
    }

    public function init() {
        load_plugin_textdomain( 'ia1', false, dirname( IA1_PLUGIN_BASENAME ) . '/languages' );
        $this->maybe_migrate_from_lydia();
    }

    public function activate() {
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
            taxonomy_terms text,
            searchable_text longtext,
            hub_score int DEFAULT 0,
            indexed_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY post_type (post_type),
            KEY hub_score (hub_score)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        $defaults = array(
            'ia1_api_key'            => '',
            'ia1_model'              => 'mistral-small-latest',
            'ia1_temperature'        => 0.7,
            'ia1_max_contexts'       => 5,
            'ia1_assistant_name'     => 'IA1',
            'ia1_assistant_subtitle' => 'Votre assistante IA locale',
            'ia1_welcome_message'    => 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?',
            'ia1_primary_color'      => '#2271b1',
            'ia1_avatar_initials'    => 'IA',
            'ia1_system_prompt'      => 'Tu es un assistant conversationnel intégré à un site WordPress. Tu aides les visiteurs à trouver l\'information qu\'ils cherchent en te basant sur le contenu du site.',
            'ia1_use_wikipedia'      => true,
        );

        foreach ( $defaults as $option => $value ) {
            if ( false === get_option( $option ) ) {
                add_option( $option, $value );
            }
        }

        update_option( 'ia1_version', IA1_VERSION );

        $current_db_version = get_option( 'ia1_db_version', '3.0.0' );

        if ( version_compare( $current_db_version, '3.2.0', '<' ) ) {
            if ( file_exists( IA1_PLUGIN_DIR . 'includes/ia1-migration-v3-2.php' ) ) {
                require_once IA1_PLUGIN_DIR . 'includes/ia1-migration-v3-2.php';
                $migration_result = ia1_migrate_database_v3_2();
                if ( $migration_result['success'] ) {
                    update_option( 'ia1_db_version', '3.2.0' );
                    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                        error_log( 'IA1 Migration v3.2: ' . $migration_result['message'] );
                    }
                    set_transient( 'ia1_migration_notice', $migration_result['message'], 300 );
                }
            } else {
                update_option( 'ia1_db_version', '3.2.0' );
            }
        }

        // Créer la table de logs RAG (v3.3)
        IA1_Logger::create_table();
        update_option( 'ia1_db_version', '3.3.0' );
    }

    public function deactivate() {
        // Nettoyage si nécessaire
    }

    private function maybe_migrate_from_lydia() {
        $lydia_api_key = get_option( 'lydia_api_key' );

        if ( $lydia_api_key && ! get_option( 'ia1_api_key' ) ) {
            update_option( 'ia1_api_key', $lydia_api_key );
            update_option( 'ia1_model', get_option( 'lydia_model', 'mistral-small-latest' ) );
            update_option( 'ia1_temperature', get_option( 'lydia_temperature', 0.7 ) );
            update_option( 'ia1_max_contexts', get_option( 'lydia_max_contexts', 5 ) );
            update_option( 'ia1_assistant_name', 'Lydia' );
            update_option( 'ia1_assistant_subtitle', 'Votre assistante IA' );

            global $wpdb;
            $old_table = $wpdb->prefix . 'lydia_index';
            $new_table = $wpdb->prefix . 'ia1_index';

            if ( $wpdb->get_var( "SHOW TABLES LIKE '$old_table'" ) === $old_table ) {
                $wpdb->query( "INSERT IGNORE INTO $new_table SELECT * FROM $old_table" );
            }

            update_option( 'ia1_migrated_from_lydia', true );

            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible">';
                echo '<p><strong>IA1 :</strong> Migration depuis Lydia réussie ! Vos données ont été préservées.</p>';
                echo '</div>';
            });
        }
    }
}

add_action( 'admin_notices', function() {
    $migration_notice = get_transient( 'ia1_migration_notice' );
    if ( $migration_notice ) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>IA1 v3.2 :</strong> ' . esc_html( $migration_notice ) . '</p>';
        echo '<p>N\'oubliez pas de <strong>réindexer votre contenu</strong> dans IA1 → Indexation.</p>';
        echo '</div>';
        delete_transient( 'ia1_migration_notice' );
    }
});

function ia1_init() {
    return IA1_Plugin::get_instance();
}

ia1_init();
