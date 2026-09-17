<?php
/**
 * Plugin Name:       ACF Spin Wheel
 * Plugin URI:        https://example.com/acf-spin-wheel
 * Description:       A modular WordPress plugin providing an interactive spin wheel with live text reactivity, CPT & ACF storage, saved wheels management, and public sharing.
 * Version:           1.0.0
 * Author:            Rick
 * Author URI:        https://example.com
 * Text Domain:       acf-spin-wheel
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define Plugin Constants
define( 'ACF_SPIN_WHEEL_VERSION', '1.0.4' );
define( 'ACF_SPIN_WHEEL_FILE', __FILE__ );
define( 'ACF_SPIN_WHEEL_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACF_SPIN_WHEEL_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Plugin Class
 */
final class ACF_Spin_Wheel {

    /**
     * Singleton instance
     *
     * @var ACF_Spin_Wheel|null
     */
    private static ?ACF_Spin_Wheel $instance = null;

    /**
     * Get singleton instance
     *
     * @return ACF_Spin_Wheel
     */
    public static function get_instance(): ACF_Spin_Wheel {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load all modular class components
     */
    private function load_dependencies(): void {
        require_once ACF_SPIN_WHEEL_PATH . 'includes/class-cpt.php';
        require_once ACF_SPIN_WHEEL_PATH . 'includes/class-acf.php';
        require_once ACF_SPIN_WHEEL_PATH . 'includes/class-settings.php';
        require_once ACF_SPIN_WHEEL_PATH . 'includes/class-rest-api.php';
        require_once ACF_SPIN_WHEEL_PATH . 'includes/class-shortcode.php';
        require_once ACF_SPIN_WHEEL_PATH . 'includes/class-block.php';
        require_once ACF_SPIN_WHEEL_PATH . 'includes/class-assets.php';
    }

    /**
     * Initialize plugin hooks
     */
    private function init_hooks(): void {
        add_action( 'init', [ $this, 'init' ] );
        add_action( 'admin_notices', [ $this, 'check_acf_dependency' ] );

        // Instantiate modules
        ACF_Spin_Wheel_CPT::get_instance();
        ACF_Spin_Wheel_ACF::get_instance();
        ACF_Spin_Wheel_Settings::get_instance();
        ACF_Spin_Wheel_REST_API::get_instance();
        ACF_Spin_Wheel_Shortcode::get_instance();
        ACF_Spin_Wheel_Block::get_instance();
        ACF_Spin_Wheel_Assets::get_instance();
    }

    /**
     * Plugin initialization
     */
    public function init(): void {
        load_plugin_textdomain( 'acf-spin-wheel', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Display an admin notice if ACF is not active
     */
    public function check_acf_dependency(): void {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
                esc_html__( 'ACF Spin Wheel', 'acf-spin-wheel' ) .
                '</strong>: ' .
                esc_html__( 'Advanced Custom Fields (ACF) is required for custom wheel storage. Please ensure ACF is installed and active.', 'acf-spin-wheel' ) .
                '</p></div>';
        }
    }
}

/**
 * Activation Hook
 */
register_activation_hook( __FILE__, function() {
    require_once ACF_SPIN_WHEEL_PATH . 'includes/class-cpt.php';
    ACF_Spin_Wheel_CPT::register_post_type();
    flush_rewrite_rules();
} );

/**
 * Deactivation Hook
 */
register_deactivation_hook( __FILE__, function() {
    flush_rewrite_rules();
} );

// Bootstrap the plugin
add_action( 'plugins_loaded', [ 'ACF_Spin_Wheel', 'get_instance' ] );
