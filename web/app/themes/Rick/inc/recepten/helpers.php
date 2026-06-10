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