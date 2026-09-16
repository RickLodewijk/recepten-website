<?php
/**
 * REST API Endpoints for ACF Spin Wheel
 *
 * @package ACF_Spin_Wheel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACF_Spin_Wheel_REST_API {

    /**
     * REST Namespace
     */
    public const NAMESPACE = 'acf-wheel/v1';

    /**
     * Default color palette
     */
    public const PALETTE = [
        '#EF4444', // Red
        '#F97316', // Orange
        '#F59E0B', // Amber
        '#10B981', // Emerald
        '#06B6D4', // Cyan
        '#3B82F6', // Blue
        '#6366F1', // Indigo
        '#8B5CF6', // Purple
        '#EC4899', // Pink
        '#14B8A6', // Teal
        '#84CC16', // Lime
        '#E11D48', // Rose
    ];

    /**
     * Singleton instance
     *
     * @var ACF_Spin_Wheel_REST_API|null
     */
    private static ?ACF_Spin_Wheel_REST_API $instance = null;

    /**
     * Get instance
     *
     * @return ACF_Spin_Wheel_REST_API
     */
    public static function get_instance(): ACF_Spin_Wheel_REST_API {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    /**
     * Register all REST API endpoints
     */
    public function register_routes(): void {
        // Save wheel (Create or Update)
        register_rest_route( self::NAMESPACE, '/save', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [ $this, 'handle_save_wheel' ],
            'permission_callback' => [ $this, 'check_save_permission' ],
        ] );

        // Get saved wheels for current user (or guest by tokens)
        register_rest_route( self::NAMESPACE, '/my-wheels', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'handle_get_my_wheels' ],
            'permission_callback' => '__return_true',
        ] );

        // Delete wheel
        register_rest_route( self::NAMESPACE, '/delete/(?P<id>\d+)', [
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => [ $this, 'handle_delete_wheel' ],
            'permission_callback' => [ $this, 'check_delete_permission' ],
            'args'                => [
                'id' => [
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param );
                    },
                ],
            ],
        ] );

        // Public read-only wheel by token
        register_rest_route( self::NAMESPACE, '/public/(?P<token>[a-zA-Z0-9_-]+)', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'handle_get_public_wheel' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'token' => [
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ] );
    }

    /**
     * Permission check: Save wheel (Respects require_login setting)
     */
    public function check_save_permission( WP_REST_Request $request ): bool|WP_Error {
        $require_login = (bool) get_option( 'acf_spin_wheel_require_login', 0 );

        if ( $require_login && ! is_user_logged_in() ) {
            return new WP_Error(
                'rest_not_logged_in',
                __( 'You must be logged in to perform this action.', 'acf-spin-wheel' ),
                [ 'status' => 401 ]
            );
        }

        // Verify nonce only for logged-in users
        if ( is_user_logged_in() ) {
            $nonce = $request->get_header( 'x_wp_nonce' );
            if ( ! $nonce ) {
                $nonce = $request->get_param( '_wpnonce' );
            }
            if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
                return new WP_Error(
                    'rest_forbidden_nonce',
                    __( 'Invalid or expired security token.', 'acf-spin-wheel' ),
                    [ 'status' => 403 ]
                );
            }
        }

        return true;
    }

    /**
     * Permission check: User can delete the specified wheel
     */
    public function check_delete_permission( WP_REST_Request $request ): bool|WP_Error {
        $post_id = (int) $request->get_param( 'id' );
        $post    = get_post( $post_id );

        if ( ! $post || ACF_Spin_Wheel_CPT::POST_TYPE !== $post->post_type ) {
            return new WP_Error(
                'rest_post_not_found',
                __( 'Wheel not found.', 'acf-spin-wheel' ),
                [ 'status' => 404 ]
            );
        }

        $require_login   = (bool) get_option( 'acf_spin_wheel_require_login', 0 );
        $current_user_id = get_current_user_id();

        if ( is_user_logged_in() ) {
            if ( (int) $post->post_author !== $current_user_id && ! current_user_can( 'delete_post', $post_id ) ) {
                return new WP_Error(
                    'rest_forbidden',
                    __( 'You do not have permission to delete this wheel.', 'acf-spin-wheel' ),
                    [ 'status' => 403 ]
                );
            }
        } else {
            if ( $require_login ) {
                return new WP_Error(
                    'rest_forbidden',
                    __( 'You must be logged in to delete this wheel.', 'acf-spin-wheel' ),
                    [ 'status' => 403 ]
                );
            }
        }

        return true;
    }

    /**
     * Generate unique random alphanumeric share token (11 characters)
     */
    public static function generate_share_token(): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $char_length = strlen( $characters );
        do {
            $token = '';
            for ( $i = 0; $i < 11; $i++ ) {
                $token .= $characters[ wp_rand( 0, $char_length - 1 ) ];
            }
            // Ensure token is unique across custom_wheel posts
            $existing = get_posts( [
                'post_type'      => ACF_Spin_Wheel_CPT::POST_TYPE,
                'meta_key'       => 'share_token',
                'meta_value'     => $token,
                'posts_per_page' => 1,
                'fields'         => 'ids',
            ] );
        } while ( ! empty( $existing ) );

        return $token;
    }

    /**
     * Build public share URL from token
     */
    public static function get_share_url( string $token, ?string $base_url = null ): string {
        if ( empty( $base_url ) ) {
            $base_url = home_url( '/' );
        }
        return add_query_arg( 'wheel', $token, $base_url );
    }

    /**
     * Format slices array from raw inputs or array of slice objects
     */
    private function format_slices( array $raw_entries ): array {
        $slices  = [];
        $palette = self::PALETTE;
        $palette_count = count( $palette );

        $index = 0;
        foreach ( $raw_entries as $entry ) {
            if ( is_array( $entry ) ) {
                $label  = isset( $entry['label'] ) ? sanitize_text_field( $entry['label'] ) : '';
                $color  = ! empty( $entry['color'] ) ? sanitize_hex_color( $entry['color'] ) : $palette[ $index % $palette_count ];
                $weight = isset( $entry['weight'] ) ? max( 1, (int) $entry['weight'] ) : 1;
            } else {
                $label  = sanitize_text_field( (string) $entry );
                $color  = $palette[ $index % $palette_count ];
                $weight = 1;
            }

            // Skip empty labels
            if ( '' === trim( $label ) ) {
                continue;
            }

            $slices[] = [
                'label'  => $label,
                'color'  => $color ?: $palette[ $index % $palette_count ],
                'weight' => $weight,
            ];
            $index++;
        }

        return $slices;
    }

    /**
     * POST /wp-json/acf-wheel/v1/save
     */
    public function handle_save_wheel( WP_REST_Request $request ): WP_REST_Response|WP_Error {
        $current_user_id = get_current_user_id();
        $params          = $request->get_json_params();

        $wheel_id = ! empty( $params['id'] ) ? absint( $params['id'] ) : 0;
        $title    = ! empty( $params['title'] ) ? sanitize_text_field( $params['title'] ) : __( 'Untitled Wheel', 'acf-spin-wheel' );
        $entries  = isset( $params['entries'] ) && is_array( $params['entries'] ) ? $params['entries'] : [];
        $base_url = ! empty( $params['base_url'] ) ? esc_url_raw( $params['base_url'] ) : null;

        // Process all entries and eliminated entries
        $all_entries        = isset( $params['all_entries'] ) && is_array( $params['all_entries'] ) ? $params['all_entries'] : $entries;
        $eliminated_entries = [];

        if ( ! empty( $params['eliminated_entries'] ) ) {
            if ( is_array( $params['eliminated_entries'] ) ) {
                $eliminated_entries = array_values( array_filter( array_map( 'sanitize_text_field', $params['eliminated_entries'] ) ) );
            } elseif ( is_string( $params['eliminated_entries'] ) ) {
                $eliminated_entries = array_values( array_filter( array_map( 'sanitize_text_field', explode( "\n", $params['eliminated_entries'] ) ) ) );
            }
        }

        // Ensure all eliminated entries are preserved in the master all_entries list
        foreach ( $eliminated_entries as $elim ) {
            if ( ! in_array( $elim, $all_entries, true ) ) {
                $all_entries[] = $elim;
            }
        }

        if ( empty( $all_entries ) ) {
            return new WP_Error(
                'empty_wheel',
                __( 'A wheel must contain at least one entry.', 'acf-spin-wheel' ),
                [ 'status' => 400 ]
            );
        }

        $formatted_slices = $this->format_slices( $all_entries );

        if ( empty( $formatted_slices ) ) {
            return new WP_Error(
                'invalid_entries',
                __( 'Please provide at least one valid slice label.', 'acf-spin-wheel' ),
                [ 'status' => 400 ]
            );
        }

        // Updating existing wheel
        if ( $wheel_id > 0 ) {
            $post = get_post( $wheel_id );
            if ( ! $post || ACF_Spin_Wheel_CPT::POST_TYPE !== $post->post_type ) {
                $wheel_id = 0; // Fork as new wheel
            } elseif ( $current_user_id > 0 ) {
                if ( (int) $post->post_author !== $current_user_id && ! current_user_can( 'edit_post', $wheel_id ) ) {
                    $wheel_id = 0; // Fork as new wheel for this user
                }
            } else {
                // Guest update: verify it's a guest wheel and token matches
                $existing_token = function_exists( 'get_field' ) ? get_field( 'share_token', $wheel_id ) : get_post_meta( $wheel_id, 'share_token', true );
                $sent_token     = ! empty( $params['token'] ) ? sanitize_text_field( $params['token'] ) : '';
                if ( (int) $post->post_author !== 0 || ( ! empty( $existing_token ) && $existing_token !== $sent_token ) ) {
                    $wheel_id = 0; // Fork as new wheel
                }
            }

            if ( $wheel_id > 0 ) {
                wp_update_post( [
                    'ID'         => $wheel_id,
                    'post_title' => $title,
                ] );

                $share_token = function_exists( 'get_field' ) ? get_field( 'share_token', $wheel_id ) : get_post_meta( $wheel_id, 'share_token', true );
                if ( empty( $share_token ) ) {
                    $share_token = self::generate_share_token();
                    if ( function_exists( 'update_field' ) ) {
                        update_field( 'share_token', $share_token, $wheel_id );
                    } else {
                        update_post_meta( $wheel_id, 'share_token', $share_token );
                    }
                }
            }
        }

        if ( 0 === $wheel_id ) {
            // Create new wheel
            $wheel_id = wp_insert_post( [
                'post_type'   => ACF_Spin_Wheel_CPT::POST_TYPE,
                'post_status' => 'publish',
                'post_author' => $current_user_id ?: 0,
                'post_title'  => $title,
            ] );

            if ( is_wp_error( $wheel_id ) ) {
                return $wheel_id;
            }

            $share_token = self::generate_share_token();
            if ( function_exists( 'update_field' ) ) {
                update_field( 'share_token', $share_token, $wheel_id );
            } else {
                update_post_meta( $wheel_id, 'share_token', $share_token );
            }
        }

        // Store slices in ACF repeater
        if ( function_exists( 'update_field' ) ) {
            update_field( 'wheel_slices', $formatted_slices, $wheel_id );
            update_field( 'eliminated_entries', implode( "\n", $eliminated_entries ), $wheel_id );
        } else {
            update_post_meta( $wheel_id, 'wheel_slices', $formatted_slices );
            update_post_meta( $wheel_id, 'eliminated_entries', $eliminated_entries );
        }
        update_post_meta( $wheel_id, 'eliminated_entries', $eliminated_entries );

        $share_url = self::get_share_url( $share_token, $base_url );

        return rest_ensure_response( [
            'success'            => true,
            'id'                 => $wheel_id,
            'title'              => $title,
            'token'              => $share_token,
            'share_url'          => $share_url,
            'slices'             => $formatted_slices,
            'eliminated_entries' => $eliminated_entries,
        ] );
    }

    /**
     * GET /wp-json/acf-wheel/v1/my-wheels
     * Returns all saved published wheels globally available to everyone
     */
    public function handle_get_my_wheels( WP_REST_Request $request ): WP_REST_Response {
        $base_url = $request->get_param( 'base_url' );
        if ( $base_url ) {
            $base_url = esc_url_raw( $base_url );
        }

        // Return all saved published wheels for all visitors on any device
        $query_args = [
            'post_type'      => ACF_Spin_Wheel_CPT::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'modified',
            'order'          => 'DESC',
        ];

        $query = new WP_Query( $query_args );

        $wheels = [];

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();

                $token = function_exists( 'get_field' ) ? get_field( 'share_token', $post_id ) : get_post_meta( $post_id, 'share_token', true );
                if ( empty( $token ) ) {
                    $token = self::generate_share_token();
                    if ( function_exists( 'update_field' ) ) {
                        update_field( 'share_token', $token, $post_id );
                    } else {
                        update_post_meta( $post_id, 'share_token', $token );
                    }
                }

                $slices = function_exists( 'get_field' ) ? get_field( 'wheel_slices', $post_id ) : get_post_meta( $post_id, 'wheel_slices', true );
                if ( ! is_array( $slices ) ) {
                    $slices = [];
                }

                $raw_labels = array_map( function( $slice ) {
                    return is_array( $slice ) && isset( $slice['label'] ) ? $slice['label'] : (string) $slice;
                }, $slices );

                $eliminated = self::get_eliminated_entries( $post_id );

                $wheels[] = [
                    'id'                 => $post_id,
                    'title'              => get_the_title(),
                    'token'              => $token,
                    'share_url'          => self::get_share_url( $token, $base_url ),
                    'slice_count'        => count( $slices ),
                    'slices'             => $slices,
                    'raw_entries'        => implode( "\n", $raw_labels ),
                    'eliminated_entries' => $eliminated,
                    'eliminated_count'   => count( $eliminated ),
                    'updated_at'         => get_the_modified_date( 'Y-m-d H:i' ),
                    'date_human'         => sprintf(
                        /* translators: %s: human-readable time difference */
                        __( '%s ago', 'acf-spin-wheel' ),
                        human_time_diff( get_the_modified_time( 'U' ), current_time( 'timestamp' ) )
                    ),
                ];
            }
            wp_reset_postdata();
        }

        return rest_ensure_response( [
            'success' => true,
            'wheels'  => $wheels,
        ] );
    }

    /**
     * DELETE /wp-json/acf-wheel/v1/delete/{id}
     */
    public function handle_delete_wheel( WP_REST_Request $request ): WP_REST_Response|WP_Error {
        $wheel_id = (int) $request->get_param( 'id' );

        $result = wp_delete_post( $wheel_id, true );

        if ( ! $result ) {
            return new WP_Error(
                'delete_failed',
                __( 'Failed to delete the wheel. Please try again.', 'acf-spin-wheel' ),
                [ 'status' => 500 ]
            );
        }

        return rest_ensure_response( [
            'success' => true,
            'id'      => $wheel_id,
            'message' => __( 'Wheel deleted successfully.', 'acf-spin-wheel' ),
        ] );
    }

    /**
     * GET /wp-json/acf-wheel/v1/public/{token}
     */
    public function handle_get_public_wheel( WP_REST_Request $request ): WP_REST_Response|WP_Error {
        $token = sanitize_text_field( $request->get_param( 'token' ) );

        $posts = get_posts( [
            'post_type'      => ACF_Spin_Wheel_CPT::POST_TYPE,
            'post_status'    => 'publish',
            'meta_key'       => 'share_token',
            'meta_value'     => $token,
            'posts_per_page' => 1,
        ] );

        if ( empty( $posts ) ) {
            return new WP_Error(
                'not_found',
                __( 'Shared wheel not found or has been removed.', 'acf-spin-wheel' ),
                [ 'status' => 404 ]
            );
        }

        $post = $posts[0];
        $slices = function_exists( 'get_field' ) ? get_field( 'wheel_slices', $post->ID ) : get_post_meta( $post->ID, 'wheel_slices', true );

        if ( ! is_array( $slices ) ) {
            $slices = [];
        }

        $raw_labels = array_map( function( $slice ) {
            return is_array( $slice ) && isset( $slice['label'] ) ? $slice['label'] : (string) $slice;
        }, $slices );

        $eliminated = self::get_eliminated_entries( $post->ID );

        return rest_ensure_response( [
            'success'            => true,
            'title'              => get_the_title( $post ),
            'token'              => $token,
            'slices'             => $slices,
            'raw_entries'        => implode( "\n", $raw_labels ),
            'eliminated_entries' => $eliminated,
            'author_name'        => get_the_author_meta( 'display_name', $post->post_author ),
        ] );
    }

    /**
     * Helper to retrieve eliminated entries for a post
     */
    private static function get_eliminated_entries( int $post_id ): array {
        $meta = get_post_meta( $post_id, 'eliminated_entries', true );
        if ( empty( $meta ) && function_exists( 'get_field' ) ) {
            $meta = get_field( 'eliminated_entries', $post_id );
        }

        if ( is_string( $meta ) ) {
            return array_values( array_filter( array_map( 'trim', explode( "\n", $meta ) ) ) );
        }
        if ( is_array( $meta ) ) {
            return array_values( array_filter( array_map( 'trim', $meta ) ) );
        }
        return [];
    }
}
