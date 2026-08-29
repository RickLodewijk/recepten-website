(function (wp) {
    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    const { registerBlockType } = wp.blocks;
    const { InspectorControls, BlockControls, useBlockProps } = wp.blockEditor || wp.editor || {};
    const { PanelBody, TextControl, TextareaControl, SelectControl, ToolbarButton, ToolbarGroup } = wp.components || {};
    const { createElement: el, Fragment, useState } = wp.element;

    registerBlockType('rick/pepernoot-form', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                title = 'Nieuwe Pepernoot Toevoegen',
                subtitle = 'Vul onderstaand formulier in om direct een nieuwe pepernoot review en beoordeling te publiceren.',
                badge = '🍪 Pepernoten Test & Review',
                buttonText = '🍪 Pepernoot Opslaan & Publiceren',
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
                    el('span', { className: 'dashicons dashicons-feedback acf-block-header__icon' }),
                    el('strong', null, 'Pepernoot Inzendformulier'),
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
                        el('span', { className: 'dashicons dashicons-feedback acf-block-header__icon' }),
                        el('strong', null, 'Pepernoot Inzendformulier'),
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

                    // Veld: Badge
                    el('div', { className: 'acf-field acf-field--text' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Badge / Label'),
                            el('p', { className: 'description' }, 'Label boven de formuliertitel.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: badge,
                                placeholder: 'bijv. 🍪 Pepernoten Test & Review',
                                onChange: function (val) { setAttributes({ badge: val }); },
                            })
                        )
                    ),

                    // Veld: Formuliertitel
                    el('div', { className: 'acf-field acf-field--text acf-field--required' },
                        el('div', { className: 'acf-label' },
                            el('label', null, 'Formulier Koptekst / Titel ', el('span', { className: 'acf-required' }, '*')),
                            el('p', { className: 'description' }, 'De hoofdtitel boven het formulier.')
                        ),
                        el('div', { className: 'acf-input' },
                            TextControl && el(TextControl, {
                                value: title,
                                placeholder: 'bijv. Nieuwe Pepernoot Toevoegen',
                                onChange: function (val) { setAttributes({ title: val }); },
                            })
                        )
                    ),

                    // Veld: Subtitel
                    el('div', { className: 'acf-field acf-field--textarea' },
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

                    // Rij voor Knoptekst & Status
                    el('div', { className: 'acf-fields-row' },
                        el('div', { className: 'acf-field acf-field--text' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Verzendknop Tekst'),
                                el('p', { className: 'description' }, 'Tekst op de actieknop.')
                            ),
                            el('div', { className: 'acf-input' },
                                TextControl && el(TextControl, {
                                    value: buttonText,
                                    placeholder: 'bijv. 🍪 Pepernoot Opslaan & Publiceren',
                                    onChange: function (val) { setAttributes({ buttonText: val }); },
                                })
                            )
                        ),
                        el('div', { className: 'acf-field acf-field--select' },
                            el('div', { className: 'acf-label' },
                                el('label', null, 'Standaard Post Status na Inzenden'),
                                el('p', { className: 'description' }, 'Moet de pepernoot direct live gaan of eerst op concept/in afwachting staan?')
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
                )
            );

            // Preview View
            const previewView = el(
                'div',
                { className: 'rick-block-preview-wrapper' },
                el(
                    'div',
                    { className: 'rick-pepernoot-card' },
                    el(
                        'div',
                        { className: 'rick-pepernoot-header' },
                        badge && el('div', { className: 'rick-pepernoot-badge' }, badge),
                        title && el('h2', { className: 'rick-pepernoot-title' }, title),
                        subtitle && el('p', { className: 'rick-pepernoot-subtitle' }, subtitle)
                    ),
                    el(
                        'div',
                        { className: 'rick-pepernoot-form-preview-mock' },
                        el('div', { className: 'rick-form-heading' },
                            el('span', { className: 'rick-form-heading__icon' }, '🍪 '),
                            el('span', null, '1. Basis Informatie')
                        ),
                        el('div', { className: 'rick-form-grid rick-form-grid--2' },
                            el('div', { className: 'rick-form-group' },
                                el('label', null, 'Naam / Smaak Pepernoot *'),
                                el('input', { type: 'text', disabled: true, placeholder: 'bijv. Van Delft Stroopwafel Pepernoten' })
                            ),
                            el('div', { className: 'rick-form-group' },
                                el('label', null, 'Korte Subtitel'),
                                el('input', { type: 'text', disabled: true, placeholder: 'bijv. Knapperige kruidnoten...' })
                            )
                        ),
                        el('div', { className: 'rick-form-heading', style: { marginTop: '16px' } },
                            el('span', { className: 'rick-form-heading__icon' }, '⭐ '),
                            el('span', null, '2. Beoordeling & Specificaties (Score, Merk, Winkel, Prijs)')
                        ),
                        el('div', { className: 'rick-form-heading', style: { marginTop: '16px' } },
                            el('span', { className: 'rick-form-heading__icon' }, '📝 '),
                            el('span', null, '3. Pluspunten, Minpunten & Review')
                        ),
                        el('div', { className: 'rick-form-submit-wrapper', style: { marginTop: '20px' } },
                            el('button', { type: 'button', className: 'button rick-pepernoot-submit-btn', disabled: true },
                                el('span', null, buttonText || 'Pepernoot Opslaan & Publiceren'),
                                el('span', { className: 'rick-pepernoot-submit-arrow' }, ' →')
                            )
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
                el('div', blockProps, isCollapsed ? collapsedView : (mode === 'edit' ? acfFormView : previewView))
            );
        },
        save: function () {
            return null;
        },
    });
})(window.wp);
