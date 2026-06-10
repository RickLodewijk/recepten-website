<?php

if ( function_exists('acf_add_local_field_group') ) {
    return;
}

add_action('add_meta_boxes', 'rick_add_recept_meta_box');
add_action('save_post_recept', 'rick_save_recept_meta_box');

function rick_add_recept_meta_box() {
    add_meta_box(
        'rick_recept_details',
        'Recept details',
        'rick_render_recept_meta_box',
        'recept',
        'normal',
        'default'
    );
}

function rick_render_recept_meta_box( $post ) {
    wp_nonce_field('rick_save_recept_meta_box', 'rick_recept_meta_box_nonce');

    $fields = array(
        'meta_info' => array(
            'label' => 'Ondertitel',
            'type' => 'text',
            'description' => 'Bijv: "Recept voor Zelfgemaakte"',
        ),
        'recept_afbeelding' => array(
            'label' => 'Afbeelding',
            'type' => 'url',
            'description' => 'Plak hier de afbeelding-URL',
        ),
        'bereidingstijd' => array(
            'label' => 'Bereidingstijd',
            'type' => 'text',
            'description' => 'Bijv: "2 uur"',
        ),
        'intro_tekst' => array(
            'label' => 'Introductie',
            'type' => 'textarea',
            'description' => 'Je introductiepraatje',
        ),
        'ingredienten' => array(
            'label' => 'Ingrediënten',
            'type' => 'textarea',
            'description' => 'Elke regel: Ingrediënt|Hoeveelheid',
        ),
        'bereidingswijze' => array(
            'label' => 'Bereidingswijze',
            'type' => 'textarea',
            'description' => 'Elke stap op een nieuwe regel. Koppen met ##',
        ),
        'bakker_tip' => array(
            'label' => 'Bakker Tip',
            'type' => 'textarea',
            'description' => 'De tip onderaan de pagina',
        ),
    );

    echo '<div class="rick-recept-meta-box">';

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

function rick_save_recept_meta_box( $post_id ) {
    if ( ! isset( $_POST['rick_recept_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['rick_recept_meta_box_nonce'], 'rick_save_recept_meta_box' ) ) {
        return;
    }

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can('edit_post', $post_id) ) {
        return;
    }

    $fields = array(
        'meta_info',
        'recept_afbeelding',
        'bereidingstijd',
        'intro_tekst',
        'ingredienten',
        'bereidingswijze',
        'bakker_tip',
    );

    foreach ( $fields as $field_name ) {
        $value = isset( $_POST[ $field_name ] ) ? wp_unslash( $_POST[ $field_name ] ) : '';
        $sanitized_value = in_array( $field_name, array('intro_tekst', 'ingredienten', 'bereidingswijze', 'bakker_tip'), true ) ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
        update_post_meta( $post_id, $field_name, $sanitized_value );
    }
}