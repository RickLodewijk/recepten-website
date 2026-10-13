<?php

function rick_register_oliebollen_cpt() {
    $labels = array(
        'name'                  => 'Oliebollen',
        'singular_name'         => 'Oliebol',
        'menu_name'             => 'Oliebollen',
        'name_admin_bar'        => 'Oliebol',
        'add_new'               => 'Nieuwe oliebol',
        'add_new_item'          => 'Nieuwe oliebol review toevoegen',
        'new_item'              => 'Nieuwe oliebol',
        'edit_item'             => 'Oliebol bewerken',
        'view_item'             => 'Oliebol bekijken',
        'all_items'             => 'Alle oliebollen',
        'search_items'          => 'Oliebollen zoeken',
        'not_found'             => 'Geen oliebollen gevonden.',
        'not_found_in_trash'    => 'Geen oliebollen gevonden in de prullenbak.',
        'featured_image'        => 'Uitgelichte afbeelding',
        'set_featured_image'    => 'Uitgelichte afbeelding instellen',
        'remove_featured_image' => 'Uitgelichte afbeelding verwijderen',
        'use_featured_image'    => 'Gebruik als uitgelichte afbeelding',
        'archives'              => 'Oliebollenarchief',
        'insert_into_item'      => 'In oliebol invoegen',
        'uploaded_to_this_item' => 'Geüpload naar deze oliebol',
        'filter_items_list'     => 'Oliebollenlijst filteren',
        'items_list_navigation' => 'Navigatie oliebollenlijst',
        'items_list'            => 'Oliebollenlijst',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        'has_archive'        => true,
        'menu_position'      => 22,
        'menu_icon'          => 'dashicons-store',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'            => array('slug' => 'oliebollen', 'with_front' => false),
        'show_in_menu'       => true,
        'publicly_queryable' => true,
        'query_var'          => true,
    );

    register_post_type('oliebol', $args);
}
add_action('init', 'rick_register_oliebollen_cpt');
