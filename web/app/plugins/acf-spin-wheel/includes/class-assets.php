<?php
/**
 * Assets Enqueue and Localization
 *
 * @package ACF_Spin_Wheel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Spin_Wheel_Assets {

    /**
     * Singleton instance
     *
     * @var ACF_Spin_Wheel_Assets|null
     */
    private static ?ACF_Spin_Wheel_Assets $instance = null;

    /**
     * Whether assets should be enqueued
     *
     * @var bool
     */
    private bool $should_enqueue = false;

    /**
     * Get instance
     *
     * @return ACF_Spin_Wheel_Assets
     */
    public static function get_instance(): ACF_Spin_Wheel_Assets {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
    }

    /**
     * Register frontend styles and scripts
     */
    public function register_assets(): void {
        $ver = ACF_SPIN_WHEEL_VERSION;

        // Stylesheet
        wp_register_style(
            'acf-spin-wheel-css',
            ACF_SPIN_WHEEL_URL . 'assets/css/spin-wheel.css',
            [],
            $ver
        );

        // Audio synthesizer module
        wp_register_script(
            'acf-spin-wheel-audio',
            ACF_SPIN_WHEEL_URL . 'assets/js/spin-wheel-audio.js',
            [],
            $ver,
            true
        );

        // Confetti module
        wp_register_script(
            'acf-spin-wheel-confetti',
            ACF_SPIN_WHEEL_URL . 'assets/js/spin-wheel-confetti.js',
            [],
            $ver,
            true
        );

        // Canvas Wheel module
        wp_register_script(
            'acf-spin-wheel-canvas',
            ACF_SPIN_WHEEL_URL . 'assets/js/spin-wheel-canvas.js',
            [ 'acf-spin-wheel-audio' ],
            $ver,
            true
        );

        // Main App Controller
        wp_register_script(
            'acf-spin-wheel-app',
            ACF_SPIN_WHEEL_URL . 'assets/js/spin-wheel-app.js',
            [ 'acf-spin-wheel-audio', 'acf-spin-wheel-confetti', 'acf-spin-wheel-canvas' ],
            $ver,
            true
        );

        // Localize App Data
        $current_token = isset( $_GET['wheel'] ) ? sanitize_text_field( wp_unslash( $_GET['wheel'] ) ) : '';
        $current_url   = is_singular() ? get_permalink() : ( ! empty( $_SERVER['REQUEST_URI'] ) ? home_url( add_query_arg( [] ) ) : home_url( '/' ) );

        wp_localize_script( 'acf-spin-wheel-app', 'acfSpinWheelData', [
            'restUrl'           => esc_url_raw( rest_url( ACF_Spin_Wheel_REST_API::NAMESPACE . '/' ) ),
            'nonce'             => wp_create_nonce( 'wp_rest' ),
            'isLoggedIn'        => is_user_logged_in(),
            'requireLoginToSave'=> (bool) get_option( 'acf_spin_wheel_require_login', 0 ),
            'currentUserId'     => get_current_user_id(),
            'loginUrl'          => wp_login_url( $current_url ),
            'registerUrl'       => wp_registration_url(),
            'currentShareToken' => $current_token,
            'defaultPalette'    => ACF_Spin_Wheel_REST_API::PALETTE,
            'i18n'              => [
                'spin'                => __( 'SPIN', 'acf-spin-wheel' ),
                'spinning'            => __( 'Spinning...', 'acf-spin-wheel' ),
                'winner'              => __( 'We have a winner!', 'acf-spin-wheel' ),
                'savedSuccess'        => __( 'Wheel saved successfully!', 'acf-spin-wheel' ),
                'saveFailed'          => __( 'Failed to save wheel.', 'acf-spin-wheel' ),
                'deletedSuccess'      => __( 'Wheel deleted.', 'acf-spin-wheel' ),
                'deleteConfirm'       => __( 'Are you sure you want to permanently delete this wheel?', 'acf-spin-wheel' ),
                'linkCopied'          => __( 'Share link copied to clipboard!', 'acf-spin-wheel' ),
                'copyFailed'          => __( 'Failed to copy link.', 'acf-spin-wheel' ),
                'emptyNotice'         => __( 'Please enter at least two options.', 'acf-spin-wheel' ),
                'loadConfirm'         => __( 'Load this wheel? Unsaved changes in the editor will be replaced.', 'acf-spin-wheel' ),
                'sharedBannerNotice'  => __( 'Viewing shared wheel. Log in to copy and create your own.', 'acf-spin-wheel' ),
                'noSavedWheels'       => __( 'You haven\'t saved any wheels yet. Create one above!', 'acf-spin-wheel' ),
                'savePromptTitle'     => __( 'Log In to Save Your Wheel', 'acf-spin-wheel' ),
                'savePromptDesc'      => __( 'Create a free account or log in to save and manage your custom wheels anytime.', 'acf-spin-wheel' ),
                'removeWinner'        => __( 'Haal eruit', 'acf-spin-wheel' ),
                'resetWheel'          => __( 'Reset rad', 'acf-spin-wheel' ),
                'winnerRemoved'       => __( '"%s" is uit het rad gehaald.', 'acf-spin-wheel' ),
                'wheelReset'          => __( 'Rad hersteld! Alle opties doen weer mee.', 'acf-spin-wheel' ),
                'saveWheel'           => __( 'Save Wheel', 'acf-spin-wheel' ),
                'saving'              => __( 'Saving...', 'acf-spin-wheel' ),
                'loadFailed'          => __( 'Kon opgeslagen wielen niet laden.', 'acf-spin-wheel' ),
                'retry'               => __( 'Opnieuw proberen', 'acf-spin-wheel' ),
            ],
        ] );
    }

    /**
     * Enqueue all required assets
     */
    public function enqueue_assets(): void {
        wp_enqueue_style( 'acf-spin-wheel-css' );
        wp_enqueue_script( 'acf-spin-wheel-audio' );
        wp_enqueue_script( 'acf-spin-wheel-confetti' );
        wp_enqueue_script( 'acf-spin-wheel-canvas' );
        wp_enqueue_script( 'acf-spin-wheel-app' );
    }
}
