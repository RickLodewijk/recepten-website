<?php

function rick_get_recept_field( $field_name, $post_id = null ) {
    $post_id = $post_id ? $post_id : get_the_ID();

    if ( function_exists('get_field') ) {
        $value = get_field( $field_name, $post_id );

        if ( $value !== null && $value !== '' ) {
            return $value;
        }
    }

    return get_post_meta( $post_id, $field_name, true );
}

function rick_get_recept_primary_category( $post_id = null ) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $terms = get_the_terms( $post_id, 'recept_categorie' );

    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return null;
    }

    return array_shift( $terms );
}

function rick_get_recept_primary_category_color( $post_id = null ) {
    $term = rick_get_recept_primary_category( $post_id );

    if ( ! $term ) {
        return '#d97706';
    }

    $color = get_term_meta( $term->term_id, 'rick_category_color', true );

    return $color ? sanitize_hex_color( $color ) : '#d97706';
}