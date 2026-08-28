<?php

if ( ! function_exists('acf_add_local_field_group') ) {
    return;
}

acf_add_local_field_group(array(
    'key' => 'group_rick_pepernoten',
    'title' => 'Pepernoot velden',
    'fields' => array(
        array(
            'key' => 'field_rick_pepernoot_score',
            'label' => 'Score',
            'name' => 'pepernoot_score',
            'type' => 'number',
            'instructions' => 'Bijv: 8.5 of 9',
            'required' => 0,
            'wrapper' => array(
                'width' => '50',
            ),
            'default_value' => '',
            'min' => '',
            'max' => '',
            'step' => '0.1',
        ),
        array(
            'key' => 'field_rick_pepernoot_subtitle',
            'label' => 'Subtitel',
            'name' => 'pepernoot_subtitle',
            'type' => 'text',
            'instructions' => 'Korte titelregel onder de naam',
            'required' => 0,
            'wrapper' => array(
                'width' => '50',
            ),
            'default_value' => '',
            'maxlength' => '',
        ),
        array(
            'key' => 'field_rick_pepernoot_brand',
            'label' => 'Merk',
            'name' => 'pepernoot_brand',
            'type' => 'text',
            'instructions' => 'Bijv. Jumbo, Albert Heijn, huismerk',
            'required' => 0,
            'wrapper' => array(
                'width' => '33',
            ),
        ),
        array(
            'key' => 'field_rick_pepernoot_shop',
            'label' => 'Winkel',
            'name' => 'pepernoot_shop',
            'type' => 'text',
            'instructions' => 'Bijv. Jumbo, Lidl, AH',
            'required' => 0,
            'wrapper' => array(
                'width' => '33',
            ),
        ),
        array(
            'key' => 'field_rick_pepernoot_price',
            'label' => 'Prijs',
            'name' => 'pepernoot_price',
            'type' => 'text',
            'instructions' => 'Bijv. 2,49',
            'required' => 0,
            'wrapper' => array(
                'width' => '33',
            ),
        ),
        array(
            'key' => 'field_rick_pepernoot_pro',
            'label' => 'Pluspunt kort',
            'name' => 'pepernoot_pro',
            'type' => 'text',
            'instructions' => 'Korte pluspuntregel voor het overzicht',
            'required' => 0,
              'wrapper' => array(
                'width' => '50',
            ),
        ),
        array(
            'key' => 'field_rick_pepernoot_con',
            'label' => 'Minpunt kort',
            'name' => 'pepernoot_con',
            'type' => 'text',
            'instructions' => 'Korte minpuntregel voor het overzicht',
            'required' => 0,
              'wrapper' => array(
                'width' => '50',
            ),
        ),
        array(
            'key' => 'field_rick_pepernoot_pluspunten',
            'label' => 'Pluspunten',
            'name' => 'pepernoot_pluspunten',
            'type' => 'textarea',
            'instructions' => 'Een punt per regel',
            'required' => 0,
            'rows' => 5,
            'new_lines' => 'br',
              'wrapper' => array(
                'width' => '50',
            ),
        ),
        array(
            'key' => 'field_rick_pepernoot_minpunten',
            'label' => 'Minpunten',
            'name' => 'pepernoot_minpunten',
            'type' => 'textarea',
            'instructions' => 'Een punt per regel',
            'required' => 0,
            'rows' => 5,
            'new_lines' => 'br',
              'wrapper' => array(
                'width' => '50',
            ),
        ),
            array(
            'key' => 'field_rick_pepernoot_intro',
            'label' => 'Introductie',
            'name' => 'pepernoot_intro',
            'type' => 'wysiwyg',
            'instructions' => 'Korte beschrijving of eerste indruk',
            'required' => 0,
            'tabs' => 'visual',
            'toolbar' => 'full',
            'media_upload' => 0,
              'wrapper' => array(
                'width' => '50',
            ),
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'pepernoot',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
));
