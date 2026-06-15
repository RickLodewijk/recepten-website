<?php

function rick_register_baktips_cpt() {
    $labels = array(
        'name'                  => 'Baktips',
        'singular_name'         => 'Baktip',
        'menu_name'             => 'Baktips',
        'name_admin_bar'        => 'Baktip',
        'add_new'               => 'Nieuwe Toevoegen',
        'add_new_item'          => 'Nieuwe Baktip Toevoegen',
        'new_item'              => 'Nieuwe Baktip',
        'edit_item'             => 'Bewerk Baktip',
        'view_item'             => 'Bekijk Baktip',
        'all_items'             => 'Alle Baktips',
        'search_items'          => 'Zoek Baktips',
        'not_found'             => 'Geen baktips gevonden.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'baktip'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-lightbulb',
        'supports'           => array('title', 'editor', 'thumbnail'),
        'show_in_rest'       => true, // Enable Gutenberg editor
    );

    register_post_type('baktip', $args);
}
add_action('init', 'rick_register_baktips_cpt');
