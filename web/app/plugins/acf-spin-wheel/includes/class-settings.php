<?php
/**
 * Admin Settings Page for ACF Spin Wheel
 *
 * @package ACF_Spin_Wheel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Spin_Wheel_Settings {

    /**
     * Option key for requiring login
     */
    public const OPTION_REQUIRE_LOGIN = 'acf_spin_wheel_require_login';

    /**
     * Singleton instance
     *
     * @var ACF_Spin_Wheel_Settings|null
     */
    private static ?ACF_Spin_Wheel_Settings $instance = null;

    /**
     * Get instance
     *
     * @return ACF_Spin_Wheel_Settings
     */
    public static function get_instance(): ACF_Spin_Wheel_Settings {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Add settings submenu under custom_wheel CPT
     */
    public function add_settings_menu(): void {
        add_submenu_page(
            'edit.php?post_type=' . ACF_Spin_Wheel_CPT::POST_TYPE,
            __( 'Spin Wheel Settings', 'acf-spin-wheel' ),
            __( 'Settings', 'acf-spin-wheel' ),
            'manage_options',
            'acf-spin-wheel-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    /**
     * Register settings and fields
     */
    public function register_settings(): void {
        register_setting( 'acf_spin_wheel_settings_group', self::OPTION_REQUIRE_LOGIN, [
            'type'              => 'boolean',
            'sanitize_callback' => [ $this, 'sanitize_checkbox' ],
            'default'           => 0,
        ] );

        add_settings_section(
            'acf_spin_wheel_general_section',
            __( 'General Access & Permissions', 'acf-spin-wheel' ),
            [ $this, 'render_section_info' ],
            'acf-spin-wheel-settings'
        );

        add_settings_field(
            self::OPTION_REQUIRE_LOGIN,
            __( 'Require Login to Save', 'acf-spin-wheel' ),
            [ $this, 'render_require_login_field' ],
            'acf-spin-wheel-settings',
            'acf_spin_wheel_general_section'
        );
    }

    /**
     * Sanitize boolean checkbox
     */
    public function sanitize_checkbox( $value ): int {
        return ! empty( $value ) ? 1 : 0;
    }

    /**
     * Section description
     */
    public function render_section_info(): void {
        echo '<p>' . esc_html__( 'Configure access controls and user permissions for the spin wheel frontend application.', 'acf-spin-wheel' ) . '</p>';
    }

    /**
     * Render require login checkbox field
     */
    public function render_require_login_field(): void {
        $require_login = get_option( self::OPTION_REQUIRE_LOGIN, 0 );
        ?>
        <label for="<?php echo esc_attr( self::OPTION_REQUIRE_LOGIN ); ?>">
            <input type="checkbox" id="<?php echo esc_attr( self::OPTION_REQUIRE_LOGIN ); ?>" name="<?php echo esc_attr( self::OPTION_REQUIRE_LOGIN ); ?>" value="1" <?php checked( 1, $require_login ); ?> />
            <strong><?php esc_html_e( 'Require users to be logged in to save wheels', 'acf-spin-wheel' ); ?></strong>
        </label>
        <p class="description">
            <?php esc_html_e( 'When unchecked, visitors and guests can create, save, and share custom wheels without having an account or logging in.', 'acf-spin-wheel' ); ?>
        </p>
        <?php
    }

    /**
     * Render the admin settings page
     */
    public function render_settings_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <?php settings_errors(); ?>

            <form method="post" action="options.php" style="max-width: 800px; margin-top: 1.5rem; background: #fff; border: 1px solid #ccd0d4; padding: 1.5rem 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <?php
                settings_fields( 'acf_spin_wheel_settings_group' );
                do_settings_sections( 'acf-spin-wheel-settings' );
                submit_button( __( 'Save Settings', 'acf-spin-wheel' ) );
                ?>
            </form>

            <div style="max-width: 800px; margin-top: 2rem; padding: 1.25rem 1.5rem; background: #f0f6fc; border-left: 4px solid #72aee6; border-radius: 4px;">
                <h3 style="margin-top: 0; color: #1d2327;">💡 <?php esc_html_e( 'Shortcode Usage', 'acf-spin-wheel' ); ?></h3>
                <p>
                    <?php esc_html_e( 'Embed the spin wheel on any page or post by pasting this shortcode:', 'acf-spin-wheel' ); ?>
                    <code>[acf_spin_wheel]</code>
                </p>
            </div>
        </div>
        <?php
    }
}
