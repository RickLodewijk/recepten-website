<?php
require_once get_theme_file_path('inc/post-types/recepten.php');
require_once get_theme_file_path('inc/taxonomies/recept-categorie.php');
require_once get_theme_file_path('inc/admin/recepten-meta-boxes.php');
require_once get_theme_file_path('inc/recepten/helpers.php');

function rick_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form','comment-form','comment-list','gallery','caption'));
}
add_action('after_setup_theme','rick_setup');

function rick_enqueue_assets() {
    $theme_version = wp_get_theme()->get('Version');

    wp_enqueue_style('rick-style', get_stylesheet_uri(), array(), $theme_version);

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

        $book_css_rel = 'assets/css/recept-book.css';
        $book_css_abs = get_theme_file_path($book_css_rel);

        wp_enqueue_style(
            'rick-recept-book',
            get_theme_file_uri($book_css_rel),
            array('rick-style', 'rick-single-recept'),
            file_exists($book_css_abs) ? filemtime($book_css_abs) : $theme_version
        );
    }
}
add_action('wp_enqueue_scripts','rick_enqueue_assets');

// Sta applicatiewachtwoorden toe op een lokale niet-HTTPS (HTTP) site
add_filter('wp_is_application_passwords_supported', '__return_true');
