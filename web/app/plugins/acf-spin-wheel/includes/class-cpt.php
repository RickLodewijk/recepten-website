<?php
/**
 * Custom Post Type Registration for Custom Wheel
 *
 * @package ACF_Spin_Wheel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Spin_Wheel_CPT {

    /**
     * Post type slug
     */
    public const POST_TYPE = 'custom_wheel';

    /**
     * Singleton instance
     *
     * @var ACF_Spin_Wheel_CPT|null
     */
    private static ?ACF_Spin_Wheel_CPT $instance = null;

    /**
     * Get instance
     *
     * @return ACF_Spin_Wheel_CPT
     */
    public static function get_instance(): ACF_Spin_Wheel_CPT {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'init', [ __CLASS__, 'register_post_type' ] );
        add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', [ $this, 'set_custom_columns' ] );
        add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', [ $this, 'render_custom_columns' ], 10, 2 );
    }

    /**
     * Register Custom Post Type
     */
    public static function register_post_type(): void {
        $labels = [
            'name'                  => _x( 'Custom Wheels', 'Post Type General Name', 'acf-spin-wheel' ),
            'singular_name'         => _x( 'Custom Wheel', 'Post Type Singular Name', 'acf-spin-wheel' ),
            'menu_name'             => __( 'Spin Wheels', 'acf-spin-wheel' ),
            'name_admin_bar'        => __( 'Spin Wheel', 'acf-spin-wheel' ),
            'archives'              => __( 'Wheel Archives', 'acf-spin-wheel' ),
            'attributes'            => __( 'Wheel Attributes', 'acf-spin-wheel' ),
            'parent_item_colon'     => __( 'Parent Wheel:', 'acf-spin-wheel' ),
            'all_items'             => __( 'All Wheels', 'acf-spin-wheel' ),
            'add_new_item'          => __( 'Add New Wheel', 'acf-spin-wheel' ),
            'add_new'               => __( 'Add New', 'acf-spin-wheel' ),
            'new_item'              => __( 'New Wheel', 'acf-spin-wheel' ),
            'edit_item'             => __( 'Edit Wheel', 'acf-spin-wheel' ),
            'update_item'           => __( 'Update Wheel', 'acf-spin-wheel' ),
            'view_item'             => __( 'View Wheel', 'acf-spin-wheel' ),
            'view_items'            => __( 'View Wheels', 'acf-spin-wheel' ),
            'search_items'          => __( 'Search Wheel', 'acf-spin-wheel' ),
            'not_found'             => __( 'Not found', 'acf-spin-wheel' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'acf-spin-wheel' ),
            'featured_image'        => __( 'Featured Image', 'acf-spin-wheel' ),
            'set_featured_image'    => __( 'Set featured image', 'acf-spin-wheel' ),
            'remove_featured_image' => __( 'Remove featured image', 'acf-spin-wheel' ),
            'use_featured_image'    => __( 'Use as featured image', 'acf-spin-wheel' ),
            'insert_into_item'      => __( 'Insert into wheel', 'acf-spin-wheel' ),
            'uploaded_to_this_item' => __( 'Uploaded to this wheel', 'acf-spin-wheel' ),
            'items_list'            => __( 'Wheels list', 'acf-spin-wheel' ),
            'items_list_navigation' => __( 'Wheels list navigation', 'acf-spin-wheel' ),
            'filter_items_list'     => __( 'Filter wheels list', 'acf-spin-wheel' ),
        ];

        $args = [
            'label'                 => __( 'Custom Wheel', 'acf-spin-wheel' ),
            'description'           => __( 'Custom Spin Wheels saved by users', 'acf-spin-wheel' ),
            'labels'                => $labels,
            'supports'              => [ 'title', 'author' ],
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-chart-pie',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => false,
        ];

        register_post_type( self::POST_TYPE, $args );
    }

    /**
     * Custom admin columns
     *
     * @param array $columns
     * @return array
     */
    public function set_custom_columns( array $columns ): array {
        $new_columns = [];
        foreach ( $columns as $key => $title ) {
            $new_columns[ $key ] = $title;
            if ( 'title' === $key ) {
                $new_columns['share_token'] = __( 'Share Token', 'acf-spin-wheel' );
                $new_columns['slice_count'] = __( 'Slices', 'acf-spin-wheel' );
            }
        }
        return $new_columns;
    }

    /**
     * Render custom admin columns
     *
     * @param string $column
     * @param int    $post_id
     */
    public function render_custom_columns( string $column, int $post_id ): void {
        if ( 'share_token' === $column ) {
            $token = function_exists( 'get_field' ) ? get_field( 'share_token', $post_id ) : get_post_meta( $post_id, 'share_token', true );
            echo $token ? '<code>' . esc_html( $token ) . '</code>' : '—';
        } elseif ( 'slice_count' === $column ) {
            $slices = function_exists( 'get_field' ) ? get_field( 'wheel_slices', $post_id ) : get_post_meta( $post_id, 'wheel_slices', true );
            $count = is_array( $slices ) ? count( $slices ) : 0;
            echo esc_html( $count );
        }
    }
}
