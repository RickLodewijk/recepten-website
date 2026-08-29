<?php

add_action('init', 'rick_register_recept_categorie_taxonomy');
add_action('init', 'rick_register_recept_categorie_meta');
add_action('recept_categorie_add_form_fields', 'rick_recept_categorie_add_color_field');
add_action('recept_categorie_edit_form_fields', 'rick_recept_categorie_edit_color_field');
add_action('created_recept_categorie', 'rick_save_recept_categorie_color_field');
add_action('edited_recept_categorie', 'rick_save_recept_categorie_color_field');
add_filter('manage_edit-recept_categorie_columns', 'rick_recept_categorie_columns');
add_filter('manage_recept_categorie_custom_column', 'rick_recept_categorie_custom_column', 10, 3);

function rick_register_recept_categorie_meta() {
    register_term_meta('recept_categorie', 'rick_category_color', array(
        'type'              => 'string',
        'description'       => 'Kleurcode voor de receptcategorie',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'sanitize_hex_color',
        'default'           => '#d97706',
        'auth_callback'     => function() {
            return current_user_can('edit_posts');
        }
    ));
}

function rick_register_recept_categorie_taxonomy() {
    $labels = array(
        'name'              => 'Receptcategorieën',
        'singular_name'     => 'Receptcategorie',
        'search_items'      => 'Receptcategorieën zoeken',
        'all_items'         => 'Alle receptcategorieën',
        'parent_item'       => 'Hoofdcategorie',
        'parent_item_colon' => 'Hoofdcategorie:',
        'edit_item'         => 'Receptcategorie bewerken',
        'update_item'       => 'Receptcategorie bijwerken',
        'add_new_item'      => 'Nieuwe receptcategorie toevoegen',
        'new_item_name'     => 'Nieuwe receptcategorie naam',
        'menu_name'         => 'Receptcategorieën',
    );

    register_taxonomy(
        'recept_categorie',
        array('recept'),
        array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_quick_edit' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'recept-categorie'),
            'show_in_rest'      => true,
        )
    );
}

function rick_recept_categorie_add_color_field() {
    ?>
    <div class="form-field term-color-wrap">
        <label for="rick_category_color">Kleurcode</label>
        <input type="color" name="rick_category_color" id="rick_category_color" value="#d97706" />
        <p>Deze kleur wordt gebruikt op de receptpagina.</p>
    </div>
    <?php
}

function rick_recept_categorie_edit_color_field( $term ) {
    $color = get_term_meta( $term->term_id, 'rick_category_color', true );
    $color = $color ? $color : '#d97706';
    ?>
    <tr class="form-field term-color-wrap">
        <th scope="row"><label for="rick_category_color">Kleurcode</label></th>
        <td>
            <input type="color" name="rick_category_color" id="rick_category_color" value="<?php echo esc_attr( $color ); ?>" />
            <p class="description">Deze kleur wordt gebruikt op de receptpagina.</p>
        </td>
    </tr>
    <?php
}

function rick_save_recept_categorie_color_field( $term_id ) {
    if ( ! isset( $_POST['rick_category_color'] ) ) {
        return;
    }

    $color = sanitize_hex_color( wp_unslash( $_POST['rick_category_color'] ) );

    if ( ! $color ) {
        $color = '#d97706';
    }

    update_term_meta( $term_id, 'rick_category_color', $color );
}

function rick_recept_categorie_columns( $columns ) {
    $columns['rick_category_color'] = 'Kleur';

    return $columns;
}

function rick_recept_categorie_custom_column( $content, $column_name, $term_id ) {
    if ( $column_name !== 'rick_category_color' ) {
        return $content;
    }

    $color = get_term_meta( $term_id, 'rick_category_color', true );

    if ( ! $color ) {
        return 'Geen';
    }

    return '<span style="display:inline-block;width:18px;height:18px;border-radius:4px;background:' . esc_attr( $color ) . ';vertical-align:middle;margin-right:8px;"></span>' . esc_html( $color );
}