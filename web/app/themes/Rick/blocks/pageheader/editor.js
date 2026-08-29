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
            const [isCollapsed, setIsCollapsed] = useState(false);

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
                            if (isCollapsed) setIsCollapsed(false);
                        },
                    }),
                    ToolbarButton && el(ToolbarButton, {
                        icon: isCollapsed ? 'arrow-down-alt2' : 'arrow-up-alt2',
                        label: isCollapsed ? 'Klap blok uit' : 'Klap blok in',
                        onClick: function () {
                            setIsCollapsed(!isCollapsed);
                        },
                    })
                )
            );

            const sidebarControls = InspectorControls && PanelBody && el(
                InspectorControls,
                null,
                el(
                    PanelBody,
                    { title: 'Pageheader Layout', initialOpen: true },
                    SelectControl && el(SelectControl, {
                        label: 'Weergave modus',
                        value: mode,
                        options: [
                            { label: '📝 ACF Velden Invoeren', value: 'edit' },
                            { label: '👁️ Live Voorbeeld', value: 'preview' },
                        ],
                        onChange: function (val) { setMode(val); },
                    }),
                    SelectControl && el(SelectControl, {
                        label: 'Minimale Hoogte',
                        value: minHeight,
                        options: [
                            { label: 'Groot (460px)', value: 'large' },
                            { label: 'Middelgroot (360px)', value: 'medium' },
                            { label: 'Compact (260px)', value: 'compact' },
                        ],
                        onChange: function (val) { setAttributes({ minHeight: val }); },
                    }),
                    SelectControl && el(SelectControl, {
                        label: 'Tekst Uitlijning',
                        value: textAlign,
                        options: [
                            { label: 'Gecentreerd', value: 'center' },
                            { label: 'Links', value: 'left' },
                            { label: 'Rechts', value: 'right' },
                        ],
                        onChange: function (val) { setAttributes({ textAlign: val }); },
                    }),
                    SelectControl && el(SelectControl, {
                        label: 'Kleur Overlay (bij foto)',
                        value: overlayType,
                        options: [
                            { label: 'Warm Donker (Aanbevolen)', value: 'warm-dark' },
                            { label: 'Neutraal Donker', value: 'dark' },
                            { label: 'Amber / Goud', value: 'amber' },
                        ],
                        onChange: function (val) { setAttributes({ overlayType: val }); },
                    })
                )
            );

            // Inklapbalk weergave
            const collapsedView = el(
                'div',
                {
                    className: 'acf-block-collapsed-bar',
                    onClick: function () { setIsCollapsed(false); }
                },
                el('div', { className: 'acf-block-header__title' },
                    el('span', { className: 'dashicons dashicons-cover-image acf-block-header__icon' }),
                    el('strong', null, 'Pageheader (Hero Banner)'),
                    title && el('span', { className: 'acf-block-collapsed-title' }, '— ' + title),
                    el('span', { className: 'acf-block-header__tag' }, 'Ingeklapt')
                ),
                el(
                    'button',
                    {
                        type: 'button',
                        className: 'acf-btn-collapse',
                        onClick: function (e) {
                            e.stopPropagation();
                            setIsCollapsed(false);
                        }
                    },
                    el('span', { className: 'dashicons dashicons-arrow-down-alt2' }),
                    ' Uitklappen'
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
                        el('span', { className: 'dashicons dashicons-cover-image acf-block-header__icon' }),
                        el('strong', null, 'Pageheader (Hero Banner)'),
                        el('span', { className: 'acf-block-header__tag' }, 'ACF Velden')
                    ),
                    el(
                        'div',
                        { className: 'acf-block-header__actions' },
                        el(
                            'button',
                            {
                                type: 'button',
                                className: 'acf-btn-preview',
                                onClick: function () { setMode('preview'); }
                            },
                            el('span', { className: 'dashicons dashicons-visibility' }),
                            ' Voorbeeld'
                        ),
                        el(
                            'button',
                            {
                                type: 'button',
                                className: 'acf-btn-collapse',
                                onClick: function () { setIsCollapsed(true); }
                            },
                            el('span', { className: 'dashicons dashicons-arrow-up-alt2' }),
                            ' Inklappen'
                        )
                    )
                ),
                el(
                    'div',
                    { className: 'acf-fields-table' },

                    // Veld: Badge / Label
                    el('div', { className: 'acf-field acf-field--text' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Badge / Label Boven Titel'),
                            el('p', { className: 'description' }, 'Klein label boven de hoofdtitel.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: badge,
                                placeholder: 'bijv. Rick Recepten',
                                onChange: function (val) { setAttributes({ badge: val }); },
                            })
                        )
                    ),

                    // Veld: Hoofdtitel
                    el('div', { className: 'acf-field acf-field--text acf-field--required' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Hoofdtitel ', el('span', { className: 'acf-required' }, '*')),
                            el('p', { className: 'description' }, 'De grote paginatitel.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: title,
                                placeholder: 'bijv. Ontdek Heerlijke Recepten',
                                onChange: function (val) { setAttributes({ title: val }); },
                            })
                        )
                    ),

                    // Veld: Subtitel
                    el('div', { className: 'acf-field acf-field--textarea' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Subtitel / Introductie'),
                            el('p', { className: 'description' }, 'Korte toelichtende tekst onder de hoofdtitel.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextareaControl && el(TextareaControl, {
                                value: subtitle,
                                rows: 3,
                                placeholder: 'Voer een introductietekst in...',
                                onChange: function (val) { setAttributes({ subtitle: val }); },
                            })
                        )
                    ),

                    // Veld: Knoppen Repeater
                    el('div', { className: 'acf-field acf-field--repeater' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Actieknoppen (Herhaler / Multi-buttons)'),
                            el('p', { className: 'description' }, 'Voeg meerdere knoppen toe met eigen tekst, link en opmaakstijl.')
                        ),
                        el('div', { className: 'acf-input' },
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
                                            el('span', null,
                                                el('span', { className: 'acf-repeater-item__num' }, index + 1),
                                                el('strong', null, btn.text || 'Knop ' + (index + 1))
                                            ),
                                            el(
                                                'button',
                                                {
                                                    type: 'button',
                                                    className: 'acf-repeater-remove-btn',
                                                    onClick: function () { removeButton(index); }
                                                },
                                                '✕ Verwijderen'
                                            )
                                        ),
                                        el(
                                            'div',
                                            { className: 'acf-fields-row' },
                                            el('div', { className: 'acf-field' },
                                                el('div', { className: 'acf-label' }, el('label', null, 'Knop Tekst')),
                                                el('div', { className: 'acf-input' },
                                                    TextControl && el(TextControl, {
                                                        value: btn.text,
                                                        placeholder: 'bijv. Bekijk Recepten',
                                                        onChange: function (val) { updateButton(index, 'text', val); }
                                                    })
                                                )
                                            ),
                                            el('div', { className: 'acf-field' },
                                                el('div', { className: 'acf-label' }, el('label', null, 'Link URL')),
                                                el('div', { className: 'acf-input' },
                                                    TextControl && el(TextControl, {
                                                        value: btn.url,
                                                        placeholder: 'bijv. /recepten/ of https://...',
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
                                                            { label: 'Primair (Goud / Amber)', value: 'primary' },
                                                            { label: 'Secundair (Glazen Rand)', value: 'secondary' },
                                                            { label: 'Wit (Helder)', value: 'white' },
                                                        ],
                                                        onChange: function (val) { updateButton(index, 'style', val); }
                                                    })
                                                )
                                            )
                                        )
                                    );
                                })
                            ),
                            el(
                                'button',
                                {
                                    type: 'button',
                                    className: 'acf-add-row-btn',
                                    onClick: addButton
                                },
                                '+ Voeg Knop Toe'
                            )
                        )
                    ),

                    // Veld: Achtergrondafbeelding URL
                    el('div', { className: 'acf-field acf-field--text' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Achtergrondafbeelding URL'),
                            el('p', { className: 'description' }, 'Directe link naar achtergrondfoto. Laat leeg voor rustige warme achtergrond.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: bgImageUrl,
                                placeholder: 'https://...',
                                onChange: function (val) { setAttributes({ bgImageUrl: val }); },
                            })
                        )
                    )
                )
            );

            // Preview View
            const previewView = el(
                'div',
                { className: 'rick-block-preview-wrapper' },
                el(
                    'div',
                    {
                        className: 'rick-pageheader is-align-' + textAlign + ' is-height-' + minHeight + ' ' + (bgImageUrl ? 'has-bg-image overlay-' + overlayType : 'no-bg-image'),
                        style: bgImageUrl ? { backgroundImage: 'url(' + bgImageUrl + ')' } : {}
                    },
                    bgImageUrl && el('div', { className: 'rick-pageheader__overlay' }),
                    el(
                        'div',
                        { className: 'rick-pageheader__container' },
                        badge && el('div', { className: 'rick-pageheader__badge-wrapper' },
                            el('span', { className: 'rick-pageheader__badge' }, badge)
                        ),
                        title && el('h1', { className: 'rick-pageheader__title' }, title),
                        subtitle && el('p', { className: 'rick-pageheader__subtitle' }, subtitle),
                        buttons && buttons.length > 0 && el(
                            'div',
                            { className: 'rick-pageheader__actions' },
                            buttons.map(function (btn, index) {
                                return el(
                                    'span',
                                    {
                                        key: index,
                                        className: 'button rick-pageheader__button rick-pageheader__button--' + (btn.style || 'primary')
                                    },
                                    btn.text || 'Knop',
                                    el('span', { className: 'rick-pageheader__button-arrow' }, ' →')
                                );
                            })
                        )
                    )
                ),
                el(
                    'div',
                    { className: 'acf-preview-actions-overlay' },
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
                    ),
                    el(
                        'button',
                        {
                            type: 'button',
                            className: 'acf-preview-collapse-overlay-btn',
                            onClick: function (e) {
                                e.stopPropagation();
                                setIsCollapsed(true);
                            }
                        },
                        el('span', { className: 'dashicons dashicons-arrow-up-alt2' }),
                        ' Inklappen'
                    )
                )
            );

            return el(
                Fragment,
                null,
                editToggleToolbar,
                sidebarControls,
                el('div', blockProps, isCollapsed ? collapsedView : (mode === 'edit' ? acfFormView : previewView))
            );
        },
        save: function () {
            return null;
        },
    });
})(window.wp);
