(function (wp) {
    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    const { registerBlockType } = wp.blocks;
    const { InspectorControls, BlockControls, useBlockProps } = wp.blockEditor || wp.editor || {};
    const { PanelBody, TextControl, TextareaControl, ToolbarButton, ToolbarGroup } = wp.components || {};
    const { createElement: el, Fragment, useState } = wp.element;

    registerBlockType('rick/pepernoot-card', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                title = 'Van Delft Stroopwafel Pepernoten',
                subtitle = 'Krokante kruidnoten omhuld met echte stroopwafelsmaak en karamel',
                score = '8.8',
                brand = 'Van Delft',
                shop = 'Albert Heijn',
                price = '€ 2,49',
                imageUrl = 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80',
                imageAlt = '',
                pro = 'Echte stroopwafelsmaak en heerlijke bite',
                con = 'Iets aan de zoete kant',
                pluspunten = 'Heerlijke zachte karameltoets\nKnapperige structuur van binnen\nHersluitbare zak',
                minpunten = 'Relatief snel uitverkocht\nPrijziger dan reguliere kruidnoten',
                intro = 'Deze stroopwafel pepernoten van Van Delft zijn een absolute aanrader voor het najaar. De combinatie van kaneel, speculaaskruiden en een zoete laag stroopwafelglazuur smaakt authentiek en niet te kunstmatig.',
                buttonText = 'Bekijk in de winkel',
                buttonUrl = '#'
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

            const plusItems = pluspunten ? pluspunten.split('\n').filter(Boolean) : [];
            const minItems = minpunten ? minpunten.split('\n').filter(Boolean) : [];

            // Inklapbalk weergave
            const collapsedView = el(
                'div',
                {
                    className: 'acf-block-collapsed-bar',
                    onClick: function () { setIsCollapsed(false); }
                },
                el('div', { className: 'acf-block-header__title' },
                    el('span', { className: 'dashicons dashicons-star-filled acf-block-header__icon' }),
                    el('strong', null, 'Pepernoot Review Kaart'),
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

            // ACF Form View in de Admin Editor
            const acfFormView = el(
                'div',
                { className: 'acf-block-fields-container' },
                el(
                    'div',
                    { className: 'acf-block-header' },
                    el('div', { className: 'acf-block-header__title' },
                        el('span', { className: 'dashicons dashicons-star-filled acf-block-header__icon' }),
                        el('strong', null, 'Pepernoot Review Kaart'),
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

                    // 1. Basis Info
                    el('div', { className: 'acf-field acf-field--text acf-field--required' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Pepernoot Naam / Smaak ', el('span', { className: 'acf-required' }, '*')),
                            el('p', { className: 'description' }, 'De volledige naam van de geteste pepernoot.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: title,
                                placeholder: 'bijv. Van Delft Stroopwafel Pepernoten',
                                onChange: function (val) { setAttributes({ title: val }); },
                            })
                        )
                    ),

                    el('div', { className: 'acf-field acf-field--text' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Korte Subtitel'),
                            el('p', { className: 'description' }, 'Korte beschrijving onder de titel.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: subtitle,
                                placeholder: 'bijv. Knapperige kruidnoten met echte stroopwafelsmaak',
                                onChange: function (val) { setAttributes({ subtitle: val }); },
                            })
                        )
                    ),

                    // 2. Score, Merk, Winkel, Prijs
                    el('div', { className: 'acf-fields-row' },
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Score (Cijfer 1-10) ⭐'),
                                el('p', { className: 'description' }, 'Bijv. 8.8')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: score,
                                    placeholder: '8.8',
                                    onChange: function (val) { setAttributes({ score: val }); },
                                })
                            )
                        ),
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Merk'),
                                el('p', { className: 'description' }, 'Bijv. Van Delft')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: brand,
                                    placeholder: 'Van Delft',
                                    onChange: function (val) { setAttributes({ brand: val }); },
                                })
                            )
                        ),
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Winkel'),
                                el('p', { className: 'description' }, 'Bijv. Albert Heijn')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: shop,
                                    placeholder: 'Albert Heijn',
                                    onChange: function (val) { setAttributes({ shop: val }); },
                                })
                            )
                        ),
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Prijs'),
                                el('p', { className: 'description' }, 'Bijv. € 2,49')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: price,
                                    placeholder: '€ 2,49',
                                    onChange: function (val) { setAttributes({ price: val }); },
                                })
                            )
                        )
                    ),

                    // 3. Afbeelding URL
                    el('div', { className: 'acf-field acf-field--text' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Afbeelding URL (Foto van de zak / pepernoot)'),
                            el('p', { className: 'description' }, 'Link naar de productfoto.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: imageUrl,
                                placeholder: 'https://...',
                                onChange: function (val) { setAttributes({ imageUrl: val }); },
                            })
                        )
                    ),

                    // 4. Korte Highlights (Pro & Con)
                    el('div', { className: 'acf-fields-row' },
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Belangrijkste Pluspunt (Kort) 👍'),
                                el('p', { className: 'description' }, 'Korte highlight regel.')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: pro,
                                    placeholder: 'Echte stroopwafelsmaak en lekkere bite',
                                    onChange: function (val) { setAttributes({ pro: val }); },
                                })
                            )
                        ),
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Belangrijkste Minpunt (Kort) 👎'),
                                el('p', { className: 'description' }, 'Korte minpunt regel.')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: con,
                                    placeholder: 'Iets aan de zoete kant',
                                    onChange: function (val) { setAttributes({ con: val }); },
                                })
                            )
                        )
                    ),

                    // 5. Uitgebreide Plus- en Minpunten
                    el('div', { className: 'acf-fields-row' },
                        el('div', { className: 'acf-field acf-field--textarea' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Pluspunten (1 per regel)'),
                                el('p', { className: 'description' }, 'Typ elke pluspunt op een nieuwe regel.')
                            ),
                            el('div', { className: 'acf-input' },
                                TextareaControl && el(TextareaControl, {
                                    value: pluspunten,
                                    rows: 4,
                                    placeholder: 'Heerlijke smaak\nGoede crunch',
                                    onChange: function (val) { setAttributes({ pluspunten: val }); },
                                })
                            )
                        ),
                        el('div', { className: 'acf-field acf-field--textarea' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Minpunten (1 per regel)'),
                                el('p', { className: 'description' }, 'Typ elke minpunt op een nieuwe regel.')
                            ),
                            el('div', { className: 'acf-input' },
                                TextareaControl && el(TextareaControl, {
                                    value: minpunten,
                                    rows: 4,
                                    placeholder: 'Prijzig\nSnel uitverkocht',
                                    onChange: function (val) { setAttributes({ minpunten: val }); },
                                })
                            )
                        )
                    ),

                    // 6. Review Tekst
                    el('div', { className: 'acf-field acf-field--textarea' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Introductie & Review Tekst'),
                            el('p', { className: 'description' }, 'Uitgebreide smaakervaring en conclusie.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextareaControl && el(TextareaControl, {
                                value: intro,
                                rows: 4,
                                placeholder: 'Schrijf hier de review...',
                                onChange: function (val) { setAttributes({ intro: val }); },
                            })
                        )
                    ),

                    // 7. Optionele Knop
                    el('div', { className: 'acf-fields-row' },
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Knop Tekst (Optioneel)'),
                                el('p', { className: 'description' }, 'Bijv. Bekijk in de winkel')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: buttonText,
                                    placeholder: 'Bekijk in de winkel',
                                    onChange: function (val) { setAttributes({ buttonText: val }); },
                                })
                            )
                        ),
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Knop Link URL'),
                                el('p', { className: 'description' }, 'Doel URL')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: buttonUrl,
                                    placeholder: 'https://...',
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
                    'article',
                    { className: 'rick-pepernoot-review-card' },
                    el(
                        'div',
                        { className: 'rick-pepernoot-review-header' },
                        el(
                            'div',
                            { className: 'rick-pepernoot-review-header__left' },
                            el(
                                'div',
                                { className: 'rick-pepernoot-review-badges' },
                                brand && el('span', { className: 'rick-review-badge rick-review-badge--brand' }, '🏷️ ' + brand),
                                shop && el('span', { className: 'rick-review-badge rick-review-badge--shop' }, '🛒 ' + shop),
                                price && el('span', { className: 'rick-review-badge rick-review-badge--price' }, '💶 ' + price)
                            ),
                            title && el('h2', { className: 'rick-pepernoot-review-title' }, title),
                            subtitle && el('p', { className: 'rick-pepernoot-review-subtitle' }, subtitle)
                        ),
                        score && el(
                            'div',
                            { className: 'rick-pepernoot-score-circle' },
                            el('span', { className: 'rick-pepernoot-score-star' }, '★'),
                            el('span', { className: 'rick-pepernoot-score-number' }, score),
                            el('span', { className: 'rick-pepernoot-score-max' }, '/10')
                        )
                    ),
                    el(
                        'div',
                        { className: 'rick-pepernoot-review-body' },
                        imageUrl && el(
                            'div',
                            { className: 'rick-pepernoot-review-media' },
                            el('img', { src: imageUrl, alt: imageAlt || title })
                        ),
                        el(
                            'div',
                            { className: 'rick-pepernoot-review-content' },
                            intro && el('div', { className: 'rick-pepernoot-review-intro' }, el('p', null, intro)),
                            (pro || con) && el(
                                'div',
                                { className: 'rick-pepernoot-highlights' },
                                pro && el('div', { className: 'rick-highlight-pill rick-highlight-pill--pro' }, '👍 Pluspunt: ' + pro),
                                con && el('div', { className: 'rick-highlight-pill rick-highlight-pill--con' }, '👎 Minpunt: ' + con)
                            )
                        )
                    ),
                    (plusItems.length > 0 || minItems.length > 0) && el(
                        'div',
                        { className: 'rick-pepernoot-pros-cons-grid' },
                        plusItems.length > 0 && el(
                            'details',
                            { className: 'rick-pros-box rick-collapsible', open: true },
                            el('summary', { className: 'rick-pros-box__title rick-collapsible-summary' },
                                el('div', { className: 'rick-collapsible-title-wrap' },
                                    el('span', { className: 'rick-pros-icon' }, '✓'),
                                    el('span', null, 'Pluspunten (' + plusItems.length + ')')
                                ),
                                el('span', { className: 'rick-collapsible-chevron' })
                            ),
                            el('div', { className: 'rick-collapsible-body' },
                                el('ul', { className: 'rick-pros-list' },
                                    plusItems.map(function (item, idx) { return el('li', { key: idx }, item); })
                                )
                            )
                        ),
                        minItems.length > 0 && el(
                            'details',
                            { className: 'rick-cons-box rick-collapsible', open: true },
                            el('summary', { className: 'rick-cons-box__title rick-collapsible-summary' },
                                el('div', { className: 'rick-collapsible-title-wrap' },
                                    el('span', { className: 'rick-cons-icon' }, '✕'),
                                    el('span', null, 'Minpunten (' + minItems.length + ')')
                                ),
                                el('span', { className: 'rick-collapsible-chevron' })
                            ),
                            el('div', { className: 'rick-collapsible-body' },
                                el('ul', { className: 'rick-cons-list' },
                                    minItems.map(function (item, idx) { return el('li', { key: idx }, item); })
                                )
                            )
                        )
                    ),
                    buttonText && el(
                        'div',
                        { className: 'rick-pepernoot-card-footer' },
                        el('span', { className: 'button rick-pepernoot-card-button' }, buttonText + ' →')
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
