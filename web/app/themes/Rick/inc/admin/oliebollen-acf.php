<?php

if ( ! function_exists('acf_add_local_field_group') ) {
    return;
}

acf_add_local_field_group(array(
    'key' => 'group_rick_oliebollen',
    'title' => 'Oliebol velden',
    'fields' => array(
        array(
            'key' => 'field_rick_oliebol_cijfer',
            'label' => 'Cijfer (0-10)',
            'name' => 'oliebol_cijfer',
            'type' => 'number',
            'instructions' => 'Geef een cijfer tussen 0 en 10 (bijv. 8.5 of 9)',
            'required' => 0,
            'wrapper' => array(
                'width' => '50',
            ),
            'default_value' => '',
            'min' => '0',
            'max' => '10',
            'step' => '0.1',
        ),
        array(
            'key' => 'field_rick_oliebol_locatie',
            'label' => 'Locatie / Plaats',
            'name' => 'oliebol_locatie',
            'type' => 'text',
            'instructions' => 'Plaats of stad waar de oliebol gekocht is (bijv. Amsterdam, Spui)',
            'required' => 0,
            'wrapper' => array(
                'width' => '50',
            ),
            'default_value' => '',
            'placeholder' => 'bijv. Amsterdam, Leidseplein',
        ),
        array(
            'key' => 'field_rick_oliebol_kraam',
            'label' => 'Kraam / Bakker',
            'name' => 'oliebol_kraam',
            'type' => 'text',
            'instructions' => 'Naam van de gebakkraam of bakkerij',
            'required' => 0,
            'wrapper' => array(
                'width' => '33',
            ),
            'default_value' => '',
            'placeholder' => 'bijv. Hollandse Gebakkraam Muller',
        ),
        array(
            'key' => 'field_rick_oliebol_smaak',
            'label' => 'Smaak / Soort',
            'name' => 'oliebol_smaak',
            'type' => 'text',
            'instructions' => 'Smaak of soort oliebol (bijv. Naturel, Rozijnen, Nutella)',
            'required' => 0,
            'wrapper' => array(
                'width' => '25',
            ),
            'default_value' => '',
            'placeholder' => 'bijv. Rozijnen, Appel',
        ),
        array(
            'key' => 'field_rick_oliebol_auteur',
            'label' => 'Geschreven door',
            'name' => 'oliebol_auteur',
            'type' => 'text',
            'instructions' => 'Door wie is deze review geschreven?',
            'required' => 0,
            'wrapper' => array(
                'width' => '25',
            ),
            'default_value' => '',
            'placeholder' => 'bijv. Rick Lodewijk',
        ),
        array(
            'key' => 'field_rick_oliebol_prijs',
            'label' => 'Prijs (€)',
            'name' => 'oliebol_prijs',
            'type' => 'text',
            'instructions' => 'Prijs per stuk of zak',
            'required' => 0,
            'wrapper' => array(
                'width' => '25',
            ),
            'default_value' => '',
            'placeholder' => 'bijv. 1,50',
        ),
        array(
            'key' => 'field_rick_oliebol_afbeelding',
            'label' => 'Afbeelding URL',
            'name' => 'oliebol_afbeelding',
            'type' => 'url',
            'instructions' => 'Optionele link naar foto van de oliebol of kraam',
            'required' => 0,
            'wrapper' => array(
                'width' => '100',
            ),
            'default_value' => '',
            'placeholder' => 'https://...',
        ),
        array(
            'key' => 'field_rick_oliebol_omschrijving',
            'label' => 'Omschrijving / Review',
            'name' => 'oliebol_omschrijving',
            'type' => 'textarea',
            'instructions' => 'Oordeel over de smaak, luchtigheid, vulling en krenten/rozijnen',
            'required' => 0,
            'rows' => 4,
            'wrapper' => array(
                'width' => '100',
            ),
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'oliebol',
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
