(function (wp) {
    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    const { registerBlockType } = wp.blocks;
    const { InspectorControls, BlockControls, useBlockProps } = wp.blockEditor || wp.editor || {};
    const { PanelBody, TextControl, TextareaControl, SelectControl, ToolbarButton, ToolbarGroup } = wp.components || {};
    const { createElement: el, Fragment, useState } = wp.element;

    registerBlockType('rick/oliebol-form', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                title = 'Nieuwe Oliebol Review Toevoegen',
                subtitle = 'Deel jouw ervaring! Waar ben je geweest en hoe smaakte de oliebol?',
                badge = '✨ Oliebollen Test & Beoordeling',
                buttonText = '✨ Oliebol Review Opslaan & Publiceren',
                postStatus = 'publish'
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
                        label: mode === 'edit' ? 'Toon Voorbeeld' : 'Bewerk Velden',
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
                    el('span', { className: 'dashicons dashicons-store acf-block-header__icon' }),
                    el('strong', null, 'Oliebol Inzendformulier'),
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

            // Settings view
            const acfFormView = el(
                'div',
                { className: 'acf-block-fields-container' },
                el(
                    'div',
                    { className: 'acf-block-header' },
                    el('div', { className: 'acf-block-header__title' },
                        el('span', { className: 'dashicons dashicons-store acf-block-header__icon' }),
                        el('strong', null, 'Oliebol Inzendformulier'),
                        el('span', { className: 'acf-block-header__tag' }, 'Instellingen')
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

                    // Veld: Badge
                    el('div', { className: 'acf-field' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Badge / Label'),
                            el('p', { className: 'description' }, 'Label boven de formuliertitel.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: badge,
                                placeholder: 'bijv. ✨ Oliebollen Test & Beoordeling',
                                onChange: function (val) { setAttributes({ badge: val }); },
                            })
                        )
                    ),

                    // Veld: Formuliertitel
                    el('div', { className: 'acf-field' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Formulier Titel'),
                            el('p', { className: 'description' }, 'De hoofdtitel boven het formulier.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: title,
                                placeholder: 'bijv. Nieuwe Oliebol Review Toevoegen',
                                onChange: function (val) { setAttributes({ title: val }); },
                            })
                        )
                    ),

                    // Veld: Subtitel
                    el('div', { className: 'acf-field' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Subtitel / Instructie'),
                            el('p', { className: 'description' }, 'Toelichtende tekst onder de formuliertitel.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextareaControl && el(TextareaControl, {
                                value: subtitle,
                                rows: 2,
                                placeholder: 'Voer instructies in...',
                                onChange: function (val) { setAttributes({ subtitle: val }); },
                            })
                        )
                    ),

                    // Knoptekst & Status
                    el('div', { className: 'acf-field' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Verzendknop Tekst'),
                            el('p', { className: 'description' }, 'Tekst op de actieknop.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: buttonText,
                                placeholder: 'bijv. ✨ Oliebol Review Opslaan & Publiceren',
                                onChange: function (val) { setAttributes({ buttonText: val }); },
                            })
                        )
                    ),
                    el('div', { className: 'acf-field' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Standaard Post Status na Inzenden')
                        ),
                        el('div', { className: 'acf-input' },
                            SelectControl && el(SelectControl, {
                                value: postStatus,
                                options: [
                                    { label: 'Direct Publiceren (Gepubliceerd)', value: 'publish' },
                                    { label: 'Ter Beoordeling (In Afwachting)', value: 'pending' },
                                    { label: 'Concept (Draft)', value: 'draft' },
                                ],
                                onChange: function (val) { setAttributes({ postStatus: val }); },
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
                    { className: 'rick-oliebol-card' },
                    el(
                        'div',
                        { className: 'rick-oliebol-header' },
                        badge && el('div', { className: 'rick-oliebol-badge' }, badge),
                        title && el('h2', { className: 'rick-oliebol-title' }, title),
                        subtitle && el('p', { className: 'rick-oliebol-subtitle' }, subtitle)
                    ),
                    el(
                        'div',
                        { style: { padding: '20px', background: '#fffdf9', borderRadius: '12px', border: '1px solid #f3e8d6' } },
                        el('div', { className: 'rick-form-heading' },
                            el('span', { className: 'rick-form-heading__icon' }, '📍 '),
                            el('span', null, '1. Locatie, Kraam & Reviewer')
                        ),
                        el('div', { className: 'rick-form-grid rick-form-grid--2' },
                            el('div', { className: 'rick-form-group' },
                                el('label', null, 'Locatie / Titel Kraam *'),
                                el('input', { type: 'text', disabled: true, placeholder: 'bijv. Oliebollenkraam Spui' })
                            ),
                            el('div', { className: 'rick-form-group' },
                                el('label', null, 'Plaats / Stad'),
                                el('input', { type: 'text', disabled: true, placeholder: 'bijv. Amsterdam' })
                            )
                        ),
                        el('div', { className: 'rick-form-grid rick-form-grid--2' },
                            el('div', { className: 'rick-form-group' },
                                el('label', null, 'Geschreven door'),
                                el('input', { type: 'text', disabled: true, placeholder: 'bijv. Rick Lodewijk' })
                            ),
                            el('div', { className: 'rick-form-group' },
                                el('label', null, 'Smaak / Soort'),
                                el('input', { type: 'text', disabled: true, placeholder: 'bijv. Naturel, Rozijnen, Nutella' })
                            )
                        ),
                        el('div', { className: 'rick-form-heading', style: { marginTop: '16px' } },
                            el('span', { className: 'rick-form-heading__icon' }, '⭐ '),
                            el('span', null, '2. Cijfer (0-10) & Prijs (€)')
                        ),
                        el('div', { className: 'rick-form-heading', style: { marginTop: '16px' } },
                            el('span', { className: 'rick-form-heading__icon' }, '✍️ '),
                            el('span', null, '3. Omschrijving & Smaakervaring')
                        ),
                        el('div', { className: 'rick-form-submit-wrapper', style: { marginTop: '20px' } },
                            el('button', { type: 'button', className: 'button rick-oliebol-submit-btn', disabled: true },
                                el('span', null, buttonText || 'Oliebol Review Opslaan'),
                                el('span', { className: 'rick-oliebol-submit-arrow' }, ' →')
                            )
                        )
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
