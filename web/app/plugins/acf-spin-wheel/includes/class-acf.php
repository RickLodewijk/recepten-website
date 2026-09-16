<?php
/**
 * ACF Field Group Registration for Spin Wheels
 *
 * @package ACF_Spin_Wheel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Spin_Wheel_ACF {

    /**
     * Singleton instance
     *
     * @var ACF_Spin_Wheel_ACF|null
     */
    private static ?ACF_Spin_Wheel_ACF $instance = null;

    /**
     * Get instance
     *
     * @return ACF_Spin_Wheel_ACF
     */
    public static function get_instance(): ACF_Spin_Wheel_ACF {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'acf/init', [ $this, 'register_field_group' ] );
    }

    /**
     * Register ACF Field Group programmatically
     */
    public function register_field_group(): void {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        acf_add_local_field_group( [
            'key'                   => 'group_acf_spin_wheel_fields',
            'title'                 => __( 'Wheel Configuration', 'acf-spin-wheel' ),
            'fields'                => [
                [
                    'key'               => 'field_wheel_share_token',
                    'label'             => __( 'Share Token', 'acf-spin-wheel' ),
                    'name'              => 'share_token',
                    'type'              => 'text',
                    'instructions'      => __( 'Unique alphanumeric token for public sharing URLs.', 'acf-spin-wheel' ),
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper'           => [
                        'width' => '50',
                        'class' => '',
                        'id'    => '',
                    ],
                    'default_value'     => '',
                    'placeholder'       => 'e.g. wX9j2pLmQ1',
                    'prepend'           => '',
                    'append'            => '',
                    'maxlength'         => 32,
                    'readonly'          => 1,
                ],
                [
                    'key'               => 'field_wheel_slices',
                    'label'             => __( 'Wheel Slices', 'acf-spin-wheel' ),
                    'name'              => 'wheel_slices',
                    'type'              => 'repeater',
                    'instructions'      => __( 'Each row represents a slice on the spin wheel.', 'acf-spin-wheel' ),
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper'           => [
                        'width' => '100',
                        'class' => '',
                        'id'    => '',
                    ],
                    'collapsed'         => 'field_wheel_slice_label',
                    'min'               => 0,
                    'max'               => 0,
                    'layout'            => 'table',
                    'button_label'      => __( 'Add Slice', 'acf-spin-wheel' ),
                    'sub_fields'        => [
                        [
                            'key'          => 'field_wheel_slice_label',
                            'label'        => __( 'Label', 'acf-spin-wheel' ),
                            'name'         => 'label',
                            'type'         => 'text',
                            'instructions' => '',
                            'required'     => 1,
                            'wrapper'      => [
                                'width' => '50',
                            ],
                            'default_value'=> '',
                            'placeholder'  => __( 'Slice title / option', 'acf-spin-wheel' ),
                        ],
                        [
                            'key'          => 'field_wheel_slice_color',
                            'label'        => __( 'Color', 'acf-spin-wheel' ),
                            'name'         => 'color',
                            'type'         => 'color_picker',
                            'instructions' => '',
                            'required'     => 0,
                            'wrapper'      => [
                                'width' => '30',
                            ],
                            'default_value'=> '#3b82f6',
                            'enable_opacity' => 0,
                            'return_format' => 'value',
                        ],
                        [
                            'key'          => 'field_wheel_slice_weight',
                            'label'        => __( 'Weight', 'acf-spin-wheel' ),
                            'name'         => 'weight',
                            'type'         => 'number',
                            'instructions' => '',
                            'required'     => 0,
                            'wrapper'      => [
                                'width' => '20',
                            ],
                            'default_value'=> 1,
                            'min'          => 1,
                            'max'          => 100,
                            'step'         => 1,
                        ],
                    ],
                ],
                [
                    'key'               => 'field_wheel_eliminated_entries',
                    'label'             => __( 'Eliminated / Spun Entries', 'acf-spin-wheel' ),
                    'name'              => 'eliminated_entries',
                    'type'              => 'textarea',
                    'instructions'      => __( 'List of entries that have already been spun and removed (one per line).', 'acf-spin-wheel' ),
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper'           => [
                        'width' => '100',
                        'class' => '',
                        'id'    => '',
                    ],
                    'default_value'     => '',
                    'placeholder'       => '',
                    'maxlength'         => '',
                    'rows'              => 4,
                    'new_lines'         => '',
                ],
            ],
            'location'              => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => ACF_Spin_Wheel_CPT::POST_TYPE,
                    ],
                ],
            ],
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen'        => '',
            'active'                => true,
            'description'           => __( 'Settings and slice configuration for ACF Spin Wheel', 'acf-spin-wheel' ),
            'show_in_rest'          => 0,
        ] );
    }
}
