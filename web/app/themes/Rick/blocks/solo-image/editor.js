(function (wp) {
    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    const { registerBlockType } = wp.blocks;
    const { InspectorControls, BlockControls, useBlockProps } = wp.blockEditor || wp.editor || {};
    const { PanelBody, TextControl, SelectControl, ToolbarButton, ToolbarGroup } = wp.components || {};
    const { createElement: el, Fragment, useState } = wp.element;

    registerBlockType('rick/solo-image', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                imageUrl = 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200&q=80',
                imageAlt = '',
                overlayTitle = '',
                height = '350px'
            } = attributes;

            const [mode, setMode] = useState('edit');
            const [isCollapsed, setIsCollapsed] = useState(false);

            const blockProps = (typeof useBlockProps === 'function')
                ? useBlockProps({ className: 'rick-block-wrapper' })
                : { className: 'rick-block-wrapper' };

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
                    el('span', { className: 'dashicons dashicons-format-image acf-block-header__icon' }),
                    el('strong', null, 'Solo Afbeelding / Banner'),
                    overlayTitle && el('span', { className: 'acf-block-collapsed-title' }, '— ' + overlayTitle),
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
                        el('span', { className: 'dashicons dashicons-format-image acf-block-header__icon' }),
                        el('strong', null, 'Solo Afbeelding / Banner'),
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

                    // Veld: Afbeelding URL & Alt
                    el('div', { className: 'acf-fields-row' },
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Afbeelding URL ', el('span', { className: 'acf-required' }, '*')),
                                el('p', { className: 'description' }, 'Directe link naar de grote sfeerafbeelding.')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: imageUrl,
                                    placeholder: 'https://...',
                                    onChange: function (val) { setAttributes({ imageUrl: val }); },
                                })
                            )
                        ),
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Alt Tekst'),
                                el('p', { className: 'description' }, 'Beschrijving van de afbeelding.')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: imageAlt,
                                    placeholder: 'bijv. Verse appeltaart op tafel',
                                    onChange: function (val) { setAttributes({ imageAlt: val }); },
                                })
                            )
                        )
                    ),

                    // Veld: Overlay Titel
                    el('div', { className: 'acf-field acf-field--text' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Overlay Titel (Optioneel)'),
                            el('p', { className: 'description' }, 'Tekst die over de afbeelding heen valt. Laat leeg voor puur foto.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: overlayTitle,
                                placeholder: 'bijv. Puur Kook- en Bakplezier',
                                onChange: function (val) { setAttributes({ overlayTitle: val }); },
                            })
                        )
                    ),

                    // Veld: Hoogte
                    el('div', { className: 'acf-field acf-field--select' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Banner Hoogte'),
                            el('p', { className: 'description' }, 'Kies hoe hoog de afbeeldingsbanner moet worden.')
                        ),
                        el('div', { className: 'acf-input' },
                            SelectControl && el(SelectControl, {
                                value: height,
                                options: [
                                    { label: 'Normaal (350px)', value: '350px' },
                                    { label: 'Groot (450px)', value: '450px' },
                                    { label: 'Compact (250px)', value: '250px' },
                                ],
                                onChange: function (val) { setAttributes({ height: val }); },
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
                    { className: 'rick-solo-image-block', style: { height: height } },
                    imageUrl && el('img', { src: imageUrl, alt: imageAlt || overlayTitle }),
                    overlayTitle && el(
                        'div',
                        { className: 'rick-solo-image-overlay' },
                        el('h2', null, overlayTitle)
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
