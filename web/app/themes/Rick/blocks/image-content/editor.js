(function (wp) {
    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    const { registerBlockType } = wp.blocks;
    const { InspectorControls, BlockControls, useBlockProps } = wp.blockEditor || wp.editor || {};
    const { PanelBody, TextControl, TextareaControl, SelectControl, ToolbarButton, ToolbarGroup } = wp.components || {};
    const { createElement: el, Fragment, useState } = wp.element;

    registerBlockType('rick/image-content', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                imageUrl = 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&q=80',
                imageAlt = '',
                imagePosition = 'left',
                title = 'Uitgelicht Recept',
                content = '',
                buttonText = 'Bekijk Recept',
                buttonUrl = '#'
            } = attributes;

            const [mode, setMode] = useState('edit'); // 'edit' or 'preview'

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
                        },
                    })
                )
            );

            const sidebarControls = InspectorControls && PanelBody && el(
                InspectorControls,
                null,
                el(
                    PanelBody,
                    { title: 'Blok Instellingen', initialOpen: true },
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
                        label: 'Positie afbeelding',
                        value: imagePosition,
                        options: [
                            { label: 'Links (standaard)', value: 'left' },
                            { label: 'Rechts', value: 'right' },
                        ],
                        onChange: function (val) { setAttributes({ imagePosition: val }); },
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
                        el('span', { className: 'dashicons dashicons-align-pull-left acf-block-header__icon' }),
                        el('strong', null, 'Afbeelding & Tekst (Split Layout)'),
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

                    // Veld: Afbeelding URL & Alt
                    el('div', { className: 'acf-fields-row' },
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Afbeelding URL ', el('span', { className: 'acf-required' }, '*')),
                                el('p', { className: 'description' }, 'Directe link naar de afbeelding of media URL.')
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
                                el('label', null, 'Afbeelding Alt Tekst'),
                                el('p', { className: 'description' }, 'Beschrijving voor toegankelijkheid en SEO.')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: imageAlt,
                                    placeholder: 'bijv. Verse groenteschotel',
                                    onChange: function (val) { setAttributes({ imageAlt: val }); },
                                })
                            )
                        )
                    ),

                    // Veld: Positie afbeelding
                    el('div', { className: 'acf-field acf-field--select' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Positie van de afbeelding'),
                            el('p', { className: 'description' }, 'Kies of de foto links of rechts naast de tekst staat.')
                        ),
                        el('div', { className: 'acf-input' },
                            SelectControl && el(SelectControl, {
                                value: imagePosition,
                                options: [
                                    { label: 'Links (Afbeelding links, tekst rechts)', value: 'left' },
                                    { label: 'Rechts (Tekst links, afbeelding rechts)', value: 'right' },
                                ],
                                onChange: function (val) { setAttributes({ imagePosition: val }); },
                            })
                        )
                    ),

                    // Veld: Titel
                    el('div', { className: 'acf-field acf-field--text acf-field--required' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Sectietitel ', el('span', { className: 'acf-required' }, '*')),
                            el('p', { className: 'description' }, 'De koptekst van deze sectie.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: title,
                                placeholder: 'bijv. Uitgelicht Recept',
                                onChange: function (val) { setAttributes({ title: val }); },
                            })
                        )
                    ),

                    // Veld: Tekst inhoud
                    el('div', { className: 'acf-field acf-field--textarea' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Tekst / Beschrijving'),
                            el('p', { className: 'description' }, 'De toelichtende tekst bij de afbeelding.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextareaControl && el(TextareaControl, {
                                value: content,
                                rows: 4,
                                placeholder: 'Schrijf hier je toelichting of introductie...',
                                onChange: function (val) { setAttributes({ content: val }); },
                            })
                        )
                    ),

                    // Veld: Knop Tekst & Link
                    el('div', { className: 'acf-fields-row' },
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Knop Tekst'),
                                el('p', { className: 'description' }, 'Laat leeg voor geen knop.')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: buttonText,
                                    placeholder: 'bijv. Bekijk Recept',
                                    onChange: function (val) { setAttributes({ buttonText: val }); },
                                })
                            )
                        ),
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Knop Link URL'),
                                el('p', { className: 'description' }, 'Doelpagina URL of anker.')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: buttonUrl,
                                    placeholder: 'bijv. /recepten/pasta/',
                                    onChange: function (val) { setAttributes({ buttonUrl: val }); },
                                })
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
                    { className: 'rick-image-content-block ' + (imagePosition === 'right' ? 'is-image-right' : 'is-image-left') },
                    imageUrl && el(
                        'div',
                        { className: 'rick-image-content-media' },
                        el('img', { src: imageUrl, alt: imageAlt || title })
                    ),
                    el(
                        'div',
                        { className: 'rick-image-content-text' },
                        title && el('h2', null, title),
                        content && el('div', { className: 'rick-image-content-body' }, el('p', null, content)),
                        buttonText && el(
                            'div',
                            { className: 'rick-image-content-actions' },
                            el('span', { className: 'button rick-btn-primary' }, buttonText, ' →')
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
