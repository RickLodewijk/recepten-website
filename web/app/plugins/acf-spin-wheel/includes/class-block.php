<?php
/**
 * Gutenberg Block Handler for ACF Spin Wheel
 *
 * @package ACF_Spin_Wheel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Spin_Wheel_Block {

    /**
     * Block name
     */
    public const BLOCK_NAME = 'acf-spin-wheel/wheel';

    /**
     * Singleton instance
     *
     * @var ACF_Spin_Wheel_Block|null
     */
    private static ?ACF_Spin_Wheel_Block $instance = null;

    /**
     * Get singleton instance
     *
     * @return ACF_Spin_Wheel_Block
     */
    public static function get_instance(): ACF_Spin_Wheel_Block {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'init', [ $this, 'register_block' ] );
    }

    /**
     * Register Gutenberg block using block.json metadata
     */
    public function register_block(): void {
        $block_dir = ACF_SPIN_WHEEL_PATH . 'blocks/spin-wheel';

        if ( ! file_exists( $block_dir . '/block.json' ) ) {
            return;
        }

        register_block_type( $block_dir, [
            'render_callback' => [ $this, 'render_block' ],
        ] );
    }

    /**
     * Server-side render callback for the block
     *
     * @param array  $attributes Block attributes.
     * @param string $content    Block inner content.
     * @return string
     */
    public function render_block( array $attributes = [], string $content = '' ): string {
        // Enqueue all frontend assets for the spin wheel
        ACF_Spin_Wheel_Assets::get_instance()->enqueue_assets();

        // Delegate HTML rendering to shortcode renderer for seamless consistency
        return ACF_Spin_Wheel_Shortcode::get_instance()->render_shortcode( $attributes );
    }
}
