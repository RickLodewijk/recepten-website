(function (wp) {
    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    const { registerBlockType } = wp.blocks;
    const { InspectorControls, BlockControls, useBlockProps } = wp.blockEditor || wp.editor || {};
    const { PanelBody, TextControl, TextareaControl, SelectControl, ToolbarButton, ToolbarGroup } = wp.components || {};
    const { createElement: el, Fragment, useState } = wp.element;

    registerBlockType('rick/pageheader', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                title = 'Ontdek Heerlijke Recepten',
                subtitle = 'Fresh, simpel en lekker koken met de beste ingrediënten en baktips.',
                badge = 'Rick Recepten',
                buttons = [
                    { text: 'Bekijk Recepten', url: '/recepten/', style: 'primary' }
                ],
                textAlign = 'center',
                bgImageUrl = 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=1600&q=80',
                overlayType = 'warm-dark',
                minHeight = 'medium'
            } = attributes;

            const [mode, setMode] = useState('edit');

            const blockProps = (typeof useBlockProps === 'function')
                ? useBlockProps({ className: 'rick-block-wrapper' })
                : { className: 'rick-block-wrapper' };

            // Knoppen repeater helpers
            const updateButton = function (index, key, value) {
                const newButtons = [...buttons];
                newButtons[index] = { ...newButtons[index], [key]: value };
                setAttributes({ buttons: newButtons });
            };

            const addButton = function () {
                const newButtons = [
                    ...buttons,
                    {
                        text: 'Nieuwe Knop',
                        url: '#',
                        style: buttons.length === 0 ? 'primary' : 'secondary'
                    }
                ];
                setAttributes({ buttons: newButtons });
            };

            const removeButton = function (index) {
                const newButtons = buttons.filter(function (_, i) { return i !== index; });
                setAttributes({ buttons: newButtons });
            };

            const editToggleToolbar = BlockControls && el(
                BlockControls,
                null,
                ToolbarGroup && el(
                    ToolbarGroup,
                    null,
                    ToolbarButton && el(ToolbarButton, {
                        icon: mode === 'edit' ? 'visibility' : 'edit',
                        label: mode === 'edit' ? 'Toon Voorbeeld' : 'Bewerk ACF Velden',
                        isPressed: mode === 'edit',
                        onClick: function () {
                            setMode(mode === 'edit' ? 'preview' : 'edit');
                        },
                    })
                )
            );

            const sidebarControls = InspectorControls && PanelBody && el(
                InspectorControls,
                null,
                el(
                    PanelBody,
                    { title: 'Header Instellingen', initialOpen: true },
                    SelectControl && el(SelectControl, {
                        label: 'Weergave modus in editor',
                        value: mode,
                        options: [
                            { label: '📝 ACF Velden Invoeren', value: 'edit' },
                            { label: '👁️ Live Voorbeeld', value: 'preview' },
                        ],
                        onChange: function (val) { setMode(val); },
                    }),
                    SelectControl && el(SelectControl, {
                        label: 'Tekst uitlijning',
                        value: textAlign,
                        options: [
                            { label: 'Gecentreerd', value: 'center' },
                            { label: 'Links', value: 'left' },
                            { label: 'Rechts', value: 'right' },
                        ],
                        onChange: function (val) { setAttributes({ textAlign: val }); },
                    }),
                    SelectControl && el(SelectControl, {
                        label: 'Header hoogte',
                        value: minHeight,
                        options: [
                            { label: 'Normaal (360px)', value: 'medium' },
                            { label: 'Groot / Hero (460px)', value: 'large' },
                            { label: 'Compact (260px)', value: 'compact' },
                        ],
                        onChange: function (val) { setAttributes({ minHeight: val }); },
                    }),
                    SelectControl && el(SelectControl, {
                        label: 'Donkere overlay filter',
                        value: overlayType,
                        options: [
                            { label: 'Warme gradient (Aanbevolen)', value: 'warm-dark' },
                            { label: 'Klassiek donker', value: 'dark' },
                            { label: 'Warm amber goud', value: 'amber' },
                        ],
                        onChange: function (val) { setAttributes({ overlayType: val }); },
                    })
                )
            );

            // ACF Form View
            const acfFormView = el(
                'div',
                { className: 'acf-block-fields-container' },
                el(
                    'div',
                    { className: 'acf-block-header' },
                    el('div', { className: 'acf-block-header__title' },
                        el('span', { className: 'dashicons dashicons-heading acf-block-header__icon' }),
                        el('strong', null, 'Page Header'),
                        el('span', { className: 'acf-block-header__tag' }, 'ACF Velden')
                    ),
                    el(
                        'button',
                        {
                            type: 'button',
                            className: 'acf-btn-preview',
                            onClick: function () { setMode('preview'); }
                        },
                        el('span', { className: 'dashicons dashicons-visibility' }),
                        ' Voorbeeld bekijken'
                    )
                ),
                el(
                    'div',
                    { className: 'acf-fields-table' },

                    // Veld: Achtergrondafbeelding URL
                    el('div', { className: 'acf-field acf-field--text' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Achtergrondafbeelding URL (Sfeerfoto)'),
                            el('p', { className: 'description' }, 'Plak de URL van een mooie sfeerfoto (of laat leeg voor een effen warme achtergrond).')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: bgImageUrl,
                                placeholder: 'https://images.unsplash.com/... of /app/uploads/...',
                                onChange: function (val) { setAttributes({ bgImageUrl: val }); },
                            })
                        )
                    ),

                    // Veld: Overlay & Hoogte
                    el('div', { className: 'acf-fields-row' },
                        el('div', { className: 'acf-field acf-field--select' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Overlay Filter Type'),
                                el('p', { className: 'description' }, 'Filter over de achtergrondfoto voor perfecte leesbaarheid.')
                            ),
                            el('div', { className: 'acf-input' },
                                SelectControl && el(SelectControl, {
                                    value: overlayType,
                                    options: [
                                        { label: 'Warme gradient (Aanbevolen)', value: 'warm-dark' },
                                        { label: 'Klassiek donker', value: 'dark' },
                                        { label: 'Warm amber goud', value: 'amber' },
                                    ],
                                    onChange: function (val) { setAttributes({ overlayType: val }); },
                                })
                            )
                        ),
                        el('div', { className: 'acf-field acf-field--select' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Header Hoogte'),
                                el('p', { className: 'description' }, 'Kies de gewenste minimum hoogte.')
                            ),
                            el('div', { className: 'acf-input' },
                                SelectControl && el(SelectControl, {
                                    value: minHeight,
                                    options: [
                                        { label: 'Normaal (360px)', value: 'medium' },
                                        { label: 'Groot / Hero (460px)', value: 'large' },
                                        { label: 'Compact (260px)', value: 'compact' },
                                    ],
                                    onChange: function (val) { setAttributes({ minHeight: val }); },
                                })
                            )
                        )
                    ),

                    // Veld: Badge
                    el('div', { className: 'acf-field acf-field--text' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Badge / Label'),
                            el('p', { className: 'description' }, 'Optioneel badge label boven de titel.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: badge,
                                placeholder: 'bijv. Rick Recepten',
                                onChange: function (val) { setAttributes({ badge: val }); },
                            })
                        )
                    ),

                    // Veld: Paginatitel
                    el('div', { className: 'acf-field acf-field--text acf-field--required' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Paginatitel ', el('span', { className: 'acf-required' }, '*')),
                            el('p', { className: 'description' }, 'De grote hoofdtitel.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: title,
                                placeholder: 'Voer paginatitel in...',
                                onChange: function (val) { setAttributes({ title: val }); },
                            })
                        )
                    ),

                    // Veld: Subtitel
                    el('div', { className: 'acf-field acf-field--textarea' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Subtitel / Introductie'),
                            el('p', { className: 'description' }, 'Toelichtende tekst onder de titel.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextareaControl && el(TextareaControl, {
                                value: subtitle,
                                rows: 2,
                                placeholder: 'Voer een subtitel in...',
                                onChange: function (val) { setAttributes({ subtitle: val }); },
                            })
                        )
                    ),

                    // Veld: Tekst Uitlijning
                    el('div', { className: 'acf-field acf-field--select' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Inhoud Uitlijning'),
                            el('p', { className: 'description' }, 'Kies hoe de badge, titel en knoppen worden uitgelijnd.')
                        ),
                        el('div', { className: 'acf-input' },
                            SelectControl && el(SelectControl, {
                                value: textAlign,
                                options: [
                                    { label: 'Gecentreerd', value: 'center' },
                                    { label: 'Links', value: 'left' },
                                    { label: 'Rechts', value: 'right' },
                                ],
                                onChange: function (val) { setAttributes({ textAlign: val }); },
                            })
                        )
                    ),

                    // ACF Repeater: Actieknoppen
                    el('div', { className: 'acf-field acf-repeater-field' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Actieknoppen (Herhaler / Repeater)'),
                            el('p', { className: 'description' }, 'Voeg één of meerdere knoppen toe met een eigen tekst, link en stijl.')
                        ),
                        el(
                            'div',
                            { className: 'acf-repeater-list' },
                            buttons.map(function (btn, index) {
                                return el(
                                    'div',
                                    { key: index, className: 'acf-repeater-item' },
                                    el(
                                        'div',
                                        { className: 'acf-repeater-item__header' },
                                        el('span', { className: 'acf-repeater-item__num' }, '#' + (index + 1)),
                                        el('strong', null, btn.text || 'Knop ' + (index + 1)),
                                        el(
                                            'button',
                                            {
                                                type: 'button',
                                                className: 'acf-repeater-remove-btn',
                                                title: 'Verwijder knop',
                                                onClick: function () { removeButton(index); }
                                            },
                                            '✕ Verwijderen'
                                        )
                                    ),
                                    el(
                                        'div',
                                        { className: 'acf-repeater-item__body' },
                                        el('div', { className: 'acf-fields-row' },
                                            el('div', { className: 'acf-field' },
                                                el('div', { className: 'acf-label' }, el('label', null, 'Knop Tekst')),
                                                el('div', { className: 'acf-input' },
                                                    TextControl && el(TextControl, {
                                                        value: btn.text || '',
                                                        placeholder: 'bijv. Bekijk Recepten',
                                                        onChange: function (val) { updateButton(index, 'text', val); }
                                                    })
                                                )
                                            ),
                                            el('div', { className: 'acf-field' },
                                                el('div', { className: 'acf-label' }, el('label', null, 'Link URL')),
                                                el('div', { className: 'acf-input' },
                                                    TextControl && el(TextControl, {
                                                        value: btn.url || '',
                                                        placeholder: 'bijv. /recepten/',
                                                        onChange: function (val) { updateButton(index, 'url', val); }
                                                    })
                                                )
                                            ),
                                            el('div', { className: 'acf-field' },
                                                el('div', { className: 'acf-label' }, el('label', null, 'Knop Stijl')),
                                                el('div', { className: 'acf-input' },
                                                    SelectControl && el(SelectControl, {
                                                        value: btn.style || 'primary',
                                                        options: [
                                                            { label: 'Primair (Gevuld Amber)', value: 'primary' },
                                                            { label: 'Secundair (Glazen Rand)', value: 'secondary' },
                                                            { label: 'Wit (Gevuld)', value: 'white' },
                                                        ],
                                                        onChange: function (val) { updateButton(index, 'style', val); }
                                                    })
                                                )
                                            )
                                        )
                                    )
                                );
                            }),
                            el(
                                'button',
                                {
                                    type: 'button',
                                    className: 'acf-add-row-btn',
                                    onClick: addButton
                                },
                                '+ Knop toevoegen (Nieuwe Actieknop)'
                            )
                        )
                    )
                )
            );

            // Preview View
            const hasBg = !!bgImageUrl;
            const previewClasses = [
                'rick-pageheader',
                'is-align-' + textAlign,
                'is-height-' + minHeight,
                hasBg ? ('has-bg-image overlay-' + overlayType) : 'no-bg-image'
            ].join(' ');

            const previewView = el(
                'div',
                { className: 'rick-block-preview-wrapper' },
                el(
                    'div',
                    {
                        className: previewClasses,
                        style: hasBg ? { backgroundImage: 'url(' + bgImageUrl + ')' } : {}
                    },
                    el('div', { className: 'rick-pageheader__overlay' }),
                    el(
                        'div',
                        { className: 'container rick-pageheader__container' },
                        badge && el(
                            'div',
                            { className: 'rick-pageheader__badge-wrapper' },
                            el('span', { className: 'rick-pageheader__badge' }, badge)
                        ),
                        title && el('h1', { className: 'rick-pageheader__title' }, title),
                        subtitle && el('p', { className: 'rick-pageheader__subtitle' }, subtitle),
                        buttons && buttons.length > 0 && el(
                            'div',
                            { className: 'rick-pageheader__actions' },
                            buttons.map(function (btn, index) {
                                if (!btn.text) return null;
                                const btnClass = 'button rick-pageheader__button rick-pageheader__button--' + (btn.style || 'primary');
                                return el(
                                    'span',
                                    { key: index, className: btnClass },
                                    el('span', null, btn.text),
                                    el('span', { className: 'rick-pageheader__button-arrow' }, ' →')
                                );
                            })
                        )
                    )
                ),
                el(
                    'button',
                    {
                        type: 'button',
                        className: 'acf-preview-edit-overlay-btn',
                        onClick: function (e) {
                            e.stopPropagation();
                            setMode('edit');
                        }
                    },
                    el('span', { className: 'dashicons dashicons-edit' }),
                    ' Bewerk ACF Velden'
                )
            );

            return el(
                Fragment,
                null,
                editToggleToolbar,
                sidebarControls,
                el('div', blockProps, mode === 'edit' ? acfFormView : previewView)
            );
        },
        save: function () {
            return null;
        },
    });
})(window.wp);
