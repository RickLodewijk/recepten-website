<?php
function rick_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form','comment-form','comment-list','gallery','caption'));
}
add_action('after_setup_theme','rick_setup');

function rick_enqueue_assets() {
    wp_enqueue_style('rick-style', get_stylesheet_uri(), array(), '1.0');
}
add_action('wp_enqueue_scripts','rick_enqueue_assets');
