(function (wp) {
    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    const { registerBlockType } = wp.blocks;
    const { InspectorControls, BlockControls, useBlockProps } = wp.blockEditor || wp.editor || {};
    const { PanelBody, TextControl, TextareaControl, SelectControl, ToolbarButton, ToolbarGroup, Button } = wp.components || {};
    const { createElement: el, Fragment, useState } = wp.element;

    registerBlockType('rick/info-grid', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                title = 'Informatie & Categorieën',
                subtitle = 'Een overzichtelijk tekstblok met directe verwijzingen naar belangrijke pagina\'s.',
                cards = []
            } = attributes;

            const [mode, setMode] = useState('edit');

            const blockProps = (typeof useBlockProps === 'function')
                ? useBlockProps({ className: 'rick-block-wrapper' })
                : { className: 'rick-block-wrapper' };

            const updateCard = function (index, key, value) {
                const newCards = [...cards];
                newCards[index] = { ...newCards[index], [key]: value };
                setAttributes({ cards: newCards });
            };

            const addCard = function () {
                const newCards = [
                    ...cards,
                    { title: 'Nieuwe Kaart', description: 'Omschrijving van deze kaart...', url: '#' }
                ];
                setAttributes({ cards: newCards });
            };

            const removeCard = function (index) {
                const newCards = cards.filter(function (_, i) { return i !== index; });
                setAttributes({ cards: newCards });
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

            // ACF Form View
            const acfFormView = el(
                'div',
                { className: 'acf-block-fields-container' },
                el(
                    'div',
                    { className: 'acf-block-header' },
                    el('div', { className: 'acf-block-header__title' },
                        el('span', { className: 'dashicons dashicons-grid-view acf-block-header__icon' }),
                        el('strong', null, 'Informatie Kaarten Grid'),
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

                    // Veld: Titel
                    el('div', { className: 'acf-field acf-field--text acf-field--required' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Sectietitel ', el('span', { className: 'acf-required' }, '*')),
                            el('p', { className: 'description' }, 'Hoofdtitel boven de kaarten.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: title,
                                placeholder: 'bijv. Informatie & Categorieën',
                                onChange: function (val) { setAttributes({ title: val }); },
                            })
                        )
                    ),

                    // Veld: Subtitel
                    el('div', { className: 'acf-field acf-field--textarea' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Subtitel / Introductie'),
                            el('p', { className: 'description' }, 'Korte toelichting onder de titel.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextareaControl && el(TextareaControl, {
                                value: subtitle,
                                rows: 2,
                                placeholder: 'Voer toelichtende tekst in...',
                                onChange: function (val) { setAttributes({ subtitle: val }); },
                            })
                        )
                    ),

                    // ACF Repeater: Kaarten
                    el('div', { className: 'acf-field acf-repeater-field' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Informatie Kaarten (Repeater)'),
                            el('p', { className: 'description' }, 'Voeg kaarten toe die in het grid worden getoond.')
                        ),
                        el(
                            'div',
                            { className: 'acf-repeater-list' },
                            cards.map(function (card, index) {
                                return el(
                                    'div',
                                    { key: index, className: 'acf-repeater-item' },
                                    el(
                                        'div',
                                        { className: 'acf-repeater-item__header' },
                                        el('span', { className: 'acf-repeater-item__num' }, '#' + (index + 1)),
                                        el('strong', null, card.title || 'Kaart ' + (index + 1)),
                                        el(
                                            'button',
                                            {
                                                type: 'button',
                                                className: 'acf-repeater-remove-btn',
                                                title: 'Verwijder kaart',
                                                onClick: function () { removeCard(index); }
                                            },
                                            '✕ Verwijderen'
                                        )
                                    ),
                                    el(
                                        'div',
                                        { className: 'acf-repeater-item__body' },
                                        el('div', { className: 'acf-fields-row' },
                                            el('div', { className: 'acf-field' },
                                                el('div', { className: 'acf-label' }, el('label', null, 'Kaart Titel')),
                                                el('div', { className: 'acf-input' },
                                                    TextControl && el(TextControl, {
                                                        value: card.title || '',
                                                        placeholder: 'bijv. Baktips',
                                                        onChange: function (val) { updateCard(index, 'title', val); }
                                                    })
                                                )
                                            ),
                                            el('div', { className: 'acf-field' },
                                                el('div', { className: 'acf-label' }, el('label', null, 'Link URL')),
                                                el('div', { className: 'acf-input' },
                                                    TextControl && el(TextControl, {
                                                        value: card.url || '',
                                                        placeholder: 'bijv. /baktips/',
                                                        onChange: function (val) { updateCard(index, 'url', val); }
                                                    })
                                                )
                                            )
                                        ),
                                        el('div', { className: 'acf-field', style: { padding: '10px 14px' } },
                                            el('div', { className: 'acf-label' }, el('label', null, 'Omschrijving')),
                                            el('div', { className: 'acf-input' },
                                                TextControl && el(TextControl, {
                                                    value: card.description || '',
                                                    placeholder: 'Korte beschrijving op de kaart...',
                                                    onChange: function (val) { updateCard(index, 'description', val); }
                                                })
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
                                    onClick: addCard
                                },
                                '+ Rij toevoegen (Nieuwe Kaart)'
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
                    { className: 'rick-info-grid-block' },
                    title && el('h2', { className: 'rick-info-grid-title' }, title),
                    subtitle && el('p', { className: 'rick-info-grid-subtitle' }, subtitle),
                    el(
                        'div',
                        { className: 'rick-info-cards-grid' },
                        cards.map(function (card, index) {
                            return el(
                                'div',
                                { key: index, className: 'rick-info-card' },
                                el(
                                    'div',
                                    { className: 'rick-info-card__content' },
                                    el('h3', { className: 'rick-info-card__title' }, card.title),
                                    card.description && el('p', { className: 'rick-info-card__desc' }, card.description)
                                ),
                                el('span', { className: 'rick-info-card__arrow' }, '→')
                            );
                        })
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
                el('div', blockProps, mode === 'edit' ? acfFormView : previewView)
            );
        },
        save: function () {
            return null;
        },
    });
})(window.wp);
