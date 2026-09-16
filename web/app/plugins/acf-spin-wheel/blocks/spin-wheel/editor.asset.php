<?php
/**
 * Gutenberg Editor Script Assets for ACF Spin Wheel
 *
 * @package ACF_Spin_Wheel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return [
    'dependencies' => [
        'wp-blocks',
        'wp-element',
        'wp-components',
        'wp-block-editor',
        'wp-i18n',
    ],
    'version'      => '1.0.0',
];
