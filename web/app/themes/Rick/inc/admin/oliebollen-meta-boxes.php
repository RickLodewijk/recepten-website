<?php

if ( function_exists('acf_add_local_field_group') ) {
    return;
}

add_action('add_meta_boxes', 'rick_add_oliebol_meta_box');
add_action('save_post_oliebol', 'rick_save_oliebol_meta_box');

function rick_add_oliebol_meta_box() {
    add_meta_box(
        'rick_oliebol_details',
        'Oliebol details',
        'rick_render_oliebol_meta_box',
        'oliebol',
        'normal',
        'default'
    );
}

function rick_render_oliebol_meta_box( $post ) {
    wp_nonce_field('rick_save_oliebol_meta_box', 'rick_oliebol_meta_box_nonce');

    $fields = array(
        'oliebol_cijfer' => array(
            'label' => 'Cijfer (0-10)',
            'type' => 'number',
            'description' => 'Bijv: 8.5 of 9',
        ),
        'oliebol_locatie' => array(
            'label' => 'Locatie / Plaats',
            'type' => 'text',
            'description' => 'Plaats of stad waar de oliebol gekocht is',
        ),
        'oliebol_kraam' => array(
            'label' => 'Kraam / Bakker',
            'type' => 'text',
            'description' => 'Naam van de gebakkraam of bakkerij',
        ),
        'oliebol_smaak' => array(
            'label' => 'Smaak / Soort',
            'type' => 'text',
            'description' => 'Bijv: Naturel, Rozijnen, Nutella, Appel',
        ),
        'oliebol_auteur' => array(
            'label' => 'Geschreven door',
            'type' => 'text',
            'description' => 'Naam van degene die de review heeft ingevuld',
        ),
        'oliebol_prijs' => array(
            'label' => 'Prijs (€)',
            'type' => 'text',
            'description' => 'Bijv: 1,50',
        ),
        'oliebol_afbeelding' => array(
            'label' => 'Afbeelding URL',
            'type' => 'url',
            'description' => 'Optionele link naar foto van de oliebol of kraam',
        ),
        'oliebol_omschrijving' => array(
            'label' => 'Omschrijving / Review',
            'type' => 'textarea',
            'description' => 'Smaakervaring en beoordeling',
        ),
    );

    echo '<div class="rick-oliebol-meta-box">';

    foreach ( $fields as $name => $field ) {
        $value = get_post_meta($post->ID, $name, true);

        echo '<p style="margin: 0 0 1rem;">';
        echo '<label for="' . esc_attr( $name ) . '" style="display:block;font-weight:600;margin-bottom:0.35rem;">' . esc_html( $field['label'] ) . '</label>';

        if ( $field['type'] === 'textarea' ) {
            echo '<textarea id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" rows="4" style="width:100%;">' . esc_textarea( $value ) . '</textarea>';
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

function rick_save_oliebol_meta_box( $post_id ) {
    if ( ! isset( $_POST['rick_oliebol_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['rick_oliebol_meta_box_nonce'], 'rick_save_oliebol_meta_box' ) ) {
        return;
    }

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can('edit_post', $post_id) ) {
        return;
    }

    $fields = array(
        'oliebol_cijfer',
        'oliebol_locatie',
        'oliebol_kraam',
        'oliebol_smaak',
        'oliebol_auteur',
        'oliebol_prijs',
        'oliebol_afbeelding',
        'oliebol_omschrijving',
    );

    foreach ( $fields as $field_name ) {
        $value = isset( $_POST[ $field_name ] ) ? wp_unslash( $_POST[ $field_name ] ) : '';
        $sanitized_value = $field_name === 'oliebol_omschrijving' ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
        update_post_meta( $post_id, $field_name, $sanitized_value );
    }
}
