<?php
require_once get_theme_file_path('inc/post-types/recepten.php');
require_once get_theme_file_path('inc/post-types/bak-tips.php');
require_once get_theme_file_path('inc/post-types/pepernoten.php');
require_once get_theme_file_path('inc/taxonomies/recept-categorie.php');
require_once get_theme_file_path('inc/admin/recepten-meta-boxes.php');
require_once get_theme_file_path('inc/admin/baktips-acf.php');
require_once get_theme_file_path('inc/admin/pepernoten-acf.php');
require_once get_theme_file_path('inc/admin/pepernoten-meta-boxes.php');
require_once get_theme_file_path('inc/recepten/helpers.php');

function rick_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    register_nav_menus([
        'primary' => __('Primary Menu', 'rick'),
        'footer' => __('Footer Menu', 'rick'),
    ]);
}
add_action('after_setup_theme', 'rick_setup');

/**
 * Voeg een eigen blokcategorie toe voor het Rick thema.
 */
function rick_block_categories($categories) {
    return array_merge(
        [
            [
                'slug' => 'rick-blocks',
                'title' => __('Rick Blokken', 'rick'),
                'icon' => 'food',
            ],
        ],
        $categories
    );
}
add_filter('block_categories_all', 'rick_block_categories', 10, 1);

function rick_primary_menu_fallback() {
    $items = array(
        array(
            'label' => __('Home', 'rick'),
            'url' => home_url('/'),
            'active' => is_front_page(),
        ),
        array(
            'label' => __('Recepten', 'rick'),
            'url' => get_post_type_archive_link('recept'),
            'active' => is_post_type_archive('recept') || is_singular('recept'),
        ),
        array(
            'label' => __('Baktips', 'rick'),
            'url' => get_post_type_archive_link('baktip'),
            'active' => is_post_type_archive('baktip') || is_singular('baktip'),
        ),
        array(
            'label' => __('Pepernoten', 'rick'),
            'url' => get_post_type_archive_link('pepernoot'),
            'active' => is_post_type_archive('pepernoot') || is_singular('pepernoot'),
        ),
    );

    echo '<ul class="primary-menu">';

    foreach ($items as $item) {
        $class = $item['active'] ? ' class="current-menu-item"' : '';
        echo '<li' . $class . '><a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a></li>';
    }

    echo '</ul>';
}

function rick_enqueue_assets() {
    $theme_version = wp_get_theme()->get('Version');
    $app_js_rel = 'app.js';
    $app_js_abs = get_theme_file_path($app_js_rel);

    wp_enqueue_style('rick-style', get_stylesheet_uri(), array(), $theme_version);
    wp_enqueue_script(
        'rick-app',
        get_theme_file_uri($app_js_rel),
        array(),
        file_exists($app_js_abs) ? filemtime($app_js_abs) : $theme_version,
        true
    );

    if (is_front_page() || is_page_template('template-overview.php')) {
        $home_css_rel = 'assets/css/home.css';
        $home_css_abs = get_theme_file_path($home_css_rel);

        wp_enqueue_style(
            'rick-home',
            get_theme_file_uri($home_css_rel),
            array('rick-style'),
            file_exists($home_css_abs) ? filemtime($home_css_abs) : $theme_version
        );
    }

    if (is_page_template('template-acf.php')) {
        $template_css_rel = 'assets/css/templates/template-acf.css';
        $template_css_abs = get_theme_file_path($template_css_rel);

        wp_enqueue_style(
            'rick-template-acf',
            get_theme_file_uri($template_css_rel),
            array('rick-style'),
            file_exists($template_css_abs) ? filemtime($template_css_abs) : $theme_version
        );
    }

    if (is_singular('recept')) {
        $single_css_rel = 'assets/css/single-recept.css';
        $single_css_abs = get_theme_file_path($single_css_rel);

        wp_enqueue_style(
            'rick-single-recept',
            get_theme_file_uri($single_css_rel),
            array('rick-style'),
            file_exists($single_css_abs) ? filemtime($single_css_abs) : $theme_version
        );
    }

    if (is_singular('baktip')) {
        $baktip_css_rel = 'assets/css/single-baktip.css';
        $baktip_css_abs = get_theme_file_path($baktip_css_rel);

        wp_enqueue_style(
            'rick-single-baktip',
            get_theme_file_uri($baktip_css_rel),
            array('rick-style'),
            file_exists($baktip_css_abs) ? filemtime($baktip_css_abs) : $theme_version
        );
    }
}
add_action('wp_enqueue_scripts','rick_enqueue_assets');

// Sta applicatiewachtwoorden toe op een lokale niet-HTTPS (HTTP) site
add_filter('wp_is_application_passwords_supported', '__return_true');

/**
 * Registreer alle custom Gutenberg blokken van het Rick thema.
 */
function rick_register_blocks() {
    $blocks_dir = get_theme_file_path('blocks');
    if (is_dir($blocks_dir)) {
        foreach (glob($blocks_dir . '/*', GLOB_ONLYDIR) as $block_path) {
            if (file_exists($block_path . '/block.json')) {
                register_block_type($block_path);
            }
        }
    }
}
add_action('init', 'rick_register_blocks');

