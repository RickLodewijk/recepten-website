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

/**
 * Voorgedefinieerde kleurentabel voor alle receptcategorieën.
 */
function rick_get_default_category_colors() {
    return array(
        'avondeten'        => '#e11d48', // Warm Koraalrood / Avondeten
        'beslagrecepten'   => '#d97706', // Warm Goudgeel / Beslag
        'brood'            => '#92400e', // Authentiek Broodbruin
        'drinken'          => '#0284c7', // Oceaanblauw / Verfrissend
        'gebak'            => '#db2777', // Frambozenroze / Zoet gebak
        'hartig'           => '#15803d', // Saliegroen / Verse kruiden
        'koek'             => '#c2410c', // Kaneeloranje / Koekjes
        'ninja-creami'     => '#0891b2', // IJzig Cyaan / IJs & Creami
        'saus'             => '#b91c1c', // Diep Robijnrood / Sauzen
        'sinterklaas'      => '#7c3aed', // Feestelijk Paars / Sinterklaas
        'smoothie'         => '#65a30d', // Tropisch Limoengroen / Smoothies
        'vulling-topping'  => '#8b5cf6', // Romig Lavendel / Toppings
    );
}

/**
 * Haal de kleur op van een specifieke term of term slug.
 */
function rick_get_term_color( $term_or_slug ) {
    $term = null;

    if ( is_object( $term_or_slug ) ) {
        $term = $term_or_slug;
    } elseif ( is_numeric( $term_or_slug ) ) {
        $term = get_term( (int) $term_or_slug, 'recept_categorie' );
    } elseif ( is_string( $term_or_slug ) ) {
        $term = get_term_by( 'slug', $term_or_slug, 'recept_categorie' );
    }

    if ( $term && ! is_wp_error( $term ) ) {
        $color = get_term_meta( $term->term_id, 'rick_category_color', true );
        if ( ! empty( $color ) ) {
            return sanitize_hex_color( $color );
        }

        $defaults = rick_get_default_category_colors();
        if ( isset( $defaults[ $term->slug ] ) ) {
            return $defaults[ $term->slug ];
        }
    }

    return '#d97706';
}

function rick_get_recept_primary_category_color( $post_id = null ) {
    $term = rick_get_recept_primary_category( $post_id );

    if ( ! $term ) {
        return '#d97706';
    }

    return rick_get_term_color( $term );
}