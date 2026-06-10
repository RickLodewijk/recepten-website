<?php

function rick_register_recepten_cpt() {
    $labels = array(
        'name'                  => 'Recepten',
        'singular_name'         => 'Recept',
        'menu_name'             => 'Recepten',
        'name_admin_bar'        => 'Recept',
        'add_new'               => 'Nieuw recept',
        'add_new_item'          => 'Nieuw recept toevoegen',
        'new_item'              => 'Nieuw recept',
        'edit_item'             => 'Recept bewerken',
        'view_item'             => 'Recept bekijken',
        'all_items'             => 'Alle recepten',
        'search_items'          => 'Recepten zoeken',
        'parent_item_colon'     => 'Hoofdrecept:',
        'not_found'             => 'Geen recepten gevonden.',
        'not_found_in_trash'    => 'Geen recepten gevonden in de prullenbak.',
        'featured_image'        => 'Uitgelichte afbeelding',
        'set_featured_image'    => 'Uitgelichte afbeelding instellen',
        'remove_featured_image' => 'Uitgelichte afbeelding verwijderen',
        'use_featured_image'    => 'Gebruik als uitgelichte afbeelding',
        'archives'              => 'Receptarchief',
        'insert_into_item'      => 'In recept invoegen',
        'uploaded_to_this_item' => 'Geüpload naar dit recept',
        'filter_items_list'     => 'Receptenlijst filteren',
        'items_list_navigation' => 'Navigatie receptenlijst',
        'items_list'            => 'Receptenlijst',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        'has_archive'        => true,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-carrot',
        'supports'           => array('title'),
        'rewrite'            => array('slug' => 'recepten', 'with_front' => false),
        'show_in_menu'       => true,
        'publicly_queryable' => true,
        'query_var'          => true,
    );

    register_post_type('recept', $args);
}
add_action('init', 'rick_register_recepten_cpt');