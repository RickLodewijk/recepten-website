<?php

if ( function_exists('acf_add_local_field_group') ) {
    return;
}

add_action('add_meta_boxes', 'rick_add_pepernoot_meta_box');
add_action('save_post_pepernoot', 'rick_save_pepernoot_meta_box');

function rick_add_pepernoot_meta_box() {
    add_meta_box(
        'rick_pepernoot_details',
        'Pepernoot details',
        'rick_render_pepernoot_meta_box',
        'pepernoot',
        'normal',
        'default'
    );
}

function rick_render_pepernoot_meta_box( $post ) {
    wp_nonce_field('rick_save_pepernoot_meta_box', 'rick_pepernoot_meta_box_nonce');

    $fields = array(
        'pepernoot_score' => array(
            'label' => 'Score',
            'type' => 'number',
            'description' => 'Bijv: 8.5 of 9',
        ),
        'pepernoot_subtitle' => array(
            'label' => 'Subtitel',
            'type' => 'text',
            'description' => 'Korte titelregel onder de naam',
        ),
        'pepernoot_afbeelding' => array(
            'label' => 'Afbeelding',
            'type' => 'url',
            'description' => 'Plak hier de afbeelding-URL',
        ),
        'pepernoot_intro' => array(
            'label' => 'Introductie',
            'type' => 'textarea',
            'description' => 'Korte beschrijving of eerste indruk',
        ),
        'pepernoot_pluspunten' => array(
            'label' => 'Pluspunten',
            'type' => 'textarea',
            'description' => 'Een punt per regel',
        ),
        'pepernoot_minpunten' => array(
            'label' => 'Minpunten',
            'type' => 'textarea',
            'description' => 'Een punt per regel',
        ),
    );

    echo '<div class="rick-pepernoot-meta-box">';

    foreach ( $fields as $name => $field ) {
        $value = get_post_meta($post->ID, $name, true);

        echo '<p style="margin: 0 0 1rem;">';
        echo '<label for="' . esc_attr( $name ) . '" style="display:block;font-weight:600;margin-bottom:0.35rem;">' . esc_html( $field['label'] ) . '</label>';

        if ( $field['type'] === 'textarea' ) {
            echo '<textarea id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" rows="5" style="width:100%;">' . esc_textarea( $value ) . '</textarea>';
        } else {
            echo '<input id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $value ) . '" style="width:100%;" />';
        }

        if ( ! empty( $field['description'] ) ) {
            echo '<small style="display:block;margin-top:0.35rem;color:#666;">' . esc_html( $field['description'] ) . '</small>';
        }

        echo '</p>';
    }

    echo '</div>';
}

function rick_save_pepernoot_meta_box( $post_id ) {
    if ( ! isset( $_POST['rick_pepernoot_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['rick_pepernoot_meta_box_nonce'], 'rick_save_pepernoot_meta_box' ) ) {
        return;
    }

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can('edit_post', $post_id) ) {
        return;
    }

    $fields = array(
        'pepernoot_score',
        'pepernoot_subtitle',
        'pepernoot_afbeelding',
        'pepernoot_intro',
        'pepernoot_pluspunten',
        'pepernoot_minpunten',
    );

    foreach ( $fields as $field_name ) {
        $value = isset( $_POST[ $field_name ] ) ? wp_unslash( $_POST[ $field_name ] ) : '';
        $sanitized_value = $field_name === 'pepernoot_score' ? sanitize_text_field( $value ) : (in_array( $field_name, array('pepernoot_intro', 'pepernoot_pluspunten', 'pepernoot_minpunten'), true ) ? sanitize_textarea_field( $value ) : sanitize_text_field( $value ));
        update_post_meta( $post_id, $field_name, $sanitized_value );
    }
}
