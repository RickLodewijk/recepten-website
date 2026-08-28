<?php

function rick_register_pepernoten_cpt() {
    $labels = array(
        'name'                  => 'Pepernoten',
        'singular_name'         => 'Pepernoot',
        'menu_name'             => 'Pepernoten',
        'name_admin_bar'        => 'Pepernoot',
        'add_new'               => 'Nieuwe pepernoot',
        'add_new_item'          => 'Nieuwe pepernoot toevoegen',
        'new_item'              => 'Nieuwe pepernoot',
        'edit_item'             => 'Pepernoot bewerken',
        'view_item'             => 'Pepernoot bekijken',
        'all_items'             => 'Alle pepernoten',
        'search_items'          => 'Pepernoten zoeken',
        'not_found'             => 'Geen pepernoten gevonden.',
        'not_found_in_trash'    => 'Geen pepernoten gevonden in de prullenbak.',
        'featured_image'        => 'Uitgelichte afbeelding',
        'set_featured_image'    => 'Uitgelichte afbeelding instellen',
        'remove_featured_image' => 'Uitgelichte afbeelding verwijderen',
        'use_featured_image'    => 'Gebruik als uitgelichte afbeelding',
        'archives'              => 'Pepernotenarchief',
        'insert_into_item'      => 'In pepernoot invoegen',
        'uploaded_to_this_item' => 'Geüpload naar deze pepernoot',
        'filter_items_list'     => 'Pepernotenlijst filteren',
        'items_list_navigation' => 'Navigatie pepernotenlijst',
        'items_list'            => 'Pepernotenlijst',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        'has_archive'        => true,
        'menu_position'      => 21,
        'menu_icon'          => 'dashicons-star-filled',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'            => array('slug' => 'pepernoten', 'with_front' => false),
        'show_in_menu'       => true,
        'publicly_queryable' => true,
        'query_var'          => true,
    );

    register_post_type('pepernoot', $args);
}
add_action('init', 'rick_register_pepernoten_cpt');
