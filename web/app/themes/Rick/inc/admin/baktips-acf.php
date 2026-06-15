<?php
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
    'key' => 'group_rick_baktips',
    'title' => 'Baktip details',
    'fields' => array(
        array(
            'key' => 'field_rick_baktip_tekst',
            'label' => 'Baktip Tekst',
            'name' => 'baktip_tekst',
            'type' => 'wysiwyg',
            'instructions' => 'Typ hier je handige baktip. Je kunt opmaak gebruiken zoals dikgedrukt, lijstjes, etc.',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'tabs' => 'all',
            'toolbar' => 'full',
            'media_upload' => 1,
            'delay' => 0,
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => 'baktip',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => array(
        0 => 'the_content', // Verberg the normale editor omdat we ACF gebruiken
    ),
    'active' => true,
    'description' => '',
));

endif;
