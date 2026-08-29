(function (wp) {
    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    const { registerBlockType } = wp.blocks;
    const { InspectorControls, BlockControls, useBlockProps } = wp.blockEditor || wp.editor || {};
    const { PanelBody, TextControl, ToolbarButton, ToolbarGroup } = wp.components || {};
    const { createElement: el, Fragment, useState } = wp.element;

    registerBlockType('rick/gallery-slider', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                title = 'Uitgelichte Collecties & Categorieën',
                items = []
            } = attributes;

            const [mode, setMode] = useState('edit');
            const [isCollapsed, setIsCollapsed] = useState(false);

            const blockProps = (typeof useBlockProps === 'function')
                ? useBlockProps({ className: 'rick-block-wrapper' })
                : { className: 'rick-block-wrapper' };

            const updateItem = function (index, key, value) {
                const newItems = [...items];
                newItems[index] = { ...newItems[index], [key]: value };
                setAttributes({ items: newItems });
            };

            const addItem = function () {
                const newItems = [
                    ...items,
                    {
                        imageUrl: 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600&q=80',
                        label: 'Nieuwe Collectie',
                        url: '#',
                        imageAlt: ''
                    }
                ];
                setAttributes({ items: newItems });
            };

            const removeItem = function (index) {
                const newItems = items.filter(function (_, i) { return i !== index; });
                setAttributes({ items: newItems });
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

            // Inklapbalk weergave
            const collapsedView = el(
                'div',
                {
                    className: 'acf-block-collapsed-bar',
                    onClick: function () { setIsCollapsed(false); }
                },
                el('div', { className: 'acf-block-header__title' },
                    el('span', { className: 'dashicons dashicons-images-alt2 acf-block-header__icon' }),
                    el('strong', null, 'Galerij Slider (Carrousel)'),
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
                        el('span', { className: 'dashicons dashicons-images-alt2 acf-block-header__icon' }),
                        el('strong', null, 'Galerij Slider (Carrousel)'),
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

                    // Veld: Titel
                    el('div', { className: 'acf-field acf-field--text acf-field--required' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Slider Sectietitel ', el('span', { className: 'acf-required' }, '*')),
                            el('p', { className: 'description' }, 'De koptekst boven de slider.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: title,
                                placeholder: 'bijv. Uitgelichte Categorieën',
                                onChange: function (val) { setAttributes({ title: val }); },
                            })
                        )
                    ),

                    // ACF Repeater: Items
                    el('div', { className: 'acf-field acf-repeater-field' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Slider Afbeeldingen & Links (Repeater)'),
                            el('p', { className: 'description' }, 'Voeg de klikbare slides toe aan de carrousel.')
                        ),
                        el(
                            'div',
                            { className: 'acf-repeater-list' },
                            items.map(function (item, index) {
                                return el(
                                    'div',
                                    { key: index, className: 'acf-repeater-item' },
                                    el(
                                        'div',
                                        { className: 'acf-repeater-item__header' },
                                        el('span', { className: 'acf-repeater-item__num' }, '#' + (index + 1)),
                                        el('strong', null, item.label || 'Slide ' + (index + 1)),
                                        el(
                                            'button',
                                            {
                                                type: 'button',
                                                className: 'acf-repeater-remove-btn',
                                                title: 'Verwijder slide',
                                                onClick: function () { removeItem(index); }
                                            },
                                            '✕ Verwijderen'
                                        )
                                    ),
                                    el(
                                        'div',
                                        { className: 'acf-repeater-item__body' },
                                        el('div', { className: 'acf-fields-row' },
                                            el('div', { className: 'acf-field' },
                                                el('div', { className: 'acf-label' }, el('label', null, 'Badge Label / Titel')),
                                                el('div', { className: 'acf-input' },
                                                    TextControl && el(TextControl, {
                                                        value: item.label || '',
                                                        placeholder: 'bijv. Ontbijt & Brunch',
                                                        onChange: function (val) { updateItem(index, 'label', val); }
                                                    })
                                                )
                                            ),
                                            el('div', { className: 'acf-field' },
                                                el('div', { className: 'acf-label' }, el('label', null, 'Link URL')),
                                                el('div', { className: 'acf-input' },
                                                    TextControl && el(TextControl, {
                                                        value: item.url || '',
                                                        placeholder: 'bijv. /recepten/ontbijt/',
                                                        onChange: function (val) { updateItem(index, 'url', val); }
                                                    })
                                                )
                                            )
                                        ),
                                        el('div', { className: 'acf-fields-row' },
                                            el('div', { className: 'acf-field' },
                                                el('div', { className: 'acf-label' }, el('label', null, 'Afbeelding URL')),
                                                el('div', { className: 'acf-input' },
                                                    TextControl && el(TextControl, {
                                                        value: item.imageUrl || '',
                                                        placeholder: 'https://...',
                                                        onChange: function (val) { updateItem(index, 'imageUrl', val); }
                                                    })
                                                )
                                            ),
                                            el('div', { className: 'acf-field' },
                                                el('div', { className: 'acf-label' }, el('label', null, 'Alt Tekst')),
                                                el('div', { className: 'acf-input' },
                                                    TextControl && el(TextControl, {
                                                        value: item.imageAlt || '',
                                                        placeholder: 'Beschrijving...',
                                                        onChange: function (val) { updateItem(index, 'imageAlt', val); }
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
                                    onClick: addItem
                                },
                                '+ Rij toevoegen (Nieuwe Slide)'
                            )
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
                    { className: 'rick-gallery-slider-section' },
                    title && el(
                        'h2',
                        { className: 'rick-slider-title' },
                        el('span', { className: 'rick-slider-icon' }, '🍳 '),
                        title
                    ),
                    el(
                        'div',
                        { className: 'rick-slider-container' },
                        items.map(function (item, index) {
                            return el(
                                'div',
                                { key: index, className: 'rick-slider-item' },
                                item.imageUrl && el('img', { src: item.imageUrl, alt: item.imageAlt || item.label }),
                                item.label && el(
                                    'div',
                                    { className: 'rick-slider-badge' },
                                    el('span', null, item.label),
                                    el('span', { className: 'rick-slider-badge-icon' }, '↗')
                                )
                            );
                        })
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
                el('div', blockProps, isCollapsed ? collapsedView : (mode === 'edit' ? acfFormView : previewView))
            );
        },
        save: function () {
            return null;
        },
    });
})(window.wp);
