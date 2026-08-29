(function (wp) {
    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    const { registerBlockType } = wp.blocks;
    const { InspectorControls, BlockControls, useBlockProps } = wp.blockEditor || wp.editor || {};
    const { PanelBody, SelectControl, TextControl, ToolbarButton, ToolbarGroup } = wp.components || {};
    const { createElement: el, Fragment, useState, useEffect } = wp.element;
    const apiFetch = wp.apiFetch;

    registerBlockType('rick/pepernoot-card', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                pepernootId = 0,
                buttonText = 'Bekijk Volledige Review'
            } = attributes;

            const [pepernotenList, setPepernotenList] = useState([]);
            const [selectedPepernoot, setSelectedPepernoot] = useState(null);
            const [isLoading, setIsLoading] = useState(true);
            const [isCollapsed, setIsCollapsed] = useState(false);

            const blockProps = (typeof useBlockProps === 'function')
                ? useBlockProps({ className: 'rick-block-wrapper' })
                : { className: 'rick-block-wrapper' };

            // Haal pepernoten op via de REST API
            useEffect(function () {
                setIsLoading(true);
                apiFetch({ path: '/wp-json/wp/v2/pepernoot?per_page=100&_embed=true' })
                    .then(function (posts) {
                        setPepernotenList(posts || []);
                        setIsLoading(false);
                    })
                    .catch(function (err) {
                        console.error('Fout bij ophalen pepernoten:', err);
                        setIsLoading(false);
                    });
            }, []);

            // Bepaal de getoonde pepernoot
            useEffect(function () {
                if (!pepernotenList || pepernotenList.length === 0) {
                    setSelectedPepernoot(null);
                    return;
                }

                if (pepernootId && pepernootId > 0) {
                    const found = pepernotenList.find(function (p) { return p.id === parseInt(pepernootId, 10); });
                    setSelectedPepernoot(found || pepernotenList[0]);
                } else {
                    // Automatisch meest recente
                    setSelectedPepernoot(pepernotenList[0]);
                }
            }, [pepernootId, pepernotenList]);

            const options = [
                { label: '🌟 Meest recente pepernoot tonen (Automatisch)', value: 0 }
            ];

            if (pepernotenList && pepernotenList.length > 0) {
                pepernotenList.forEach(function (item) {
                    const titleText = item.title && item.title.rendered ? item.title.rendered.replace(/&#038;/g, '&') : 'Pepernoot #' + item.id;
                    options.push({
                        label: '🍪 ' + titleText + ' (ID: ' + item.id + ')',
                        value: item.id
                    });
                });
            }

            const editToggleToolbar = BlockControls && el(
                BlockControls,
                null,
                ToolbarGroup && el(
                    ToolbarGroup,
                    null,
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
                    { title: 'Pepernoot Koppeling (CPT)', initialOpen: true },
                    SelectControl && el(SelectControl, {
                        label: 'Selecteer Pepernoot Bericht',
                        value: pepernootId,
                        options: options,
                        onChange: function (val) {
                            setAttributes({ pepernootId: parseInt(val, 10) });
                        },
                    }),
                    TextControl && el(TextControl, {
                        label: 'Knoptekst',
                        value: buttonText,
                        onChange: function (val) { setAttributes({ buttonText: val }); },
                    })
                )
            );

            const postTitle = selectedPepernoot
                ? (selectedPepernoot.title && selectedPepernoot.title.rendered ? selectedPepernoot.title.rendered.replace(/&#038;/g, '&') : 'Pepernoot #' + selectedPepernoot.id)
                : 'Van Delft Stroopwafel Pepernoten';

            const acfData = (selectedPepernoot && selectedPepernoot.acf) ? selectedPepernoot.acf : {};
            const score = acfData.pepernoot_score || '8.8';
            const subtitle = acfData.pepernoot_subtitle || 'Krokante kruidnoten omhuld met echte stroopwafelsmaak en karamel';
            const brand = acfData.pepernoot_brand || 'Van Delft';
            const shop = acfData.pepernoot_shop || 'Albert Heijn';
            const price = acfData.pepernoot_price || '€ 2,49';
            const pro = acfData.pepernoot_pro || 'Echte stroopwafelsmaak en heerlijke bite';
            const con = acfData.pepernoot_con || 'Iets aan de zoete kant';
            const pluspuntenRaw = acfData.pepernoot_pluspunten || 'Heerlijke zachte karameltoets\nKnapperige structuur van binnen\nHersluitbare zak';
            const minpuntenRaw = acfData.pepernoot_minpunten || 'Relatief snel uitverkocht\nPrijziger dan reguliere kruidnoten';
            const plusItems = pluspuntenRaw ? pluspuntenRaw.split('\n').filter(Boolean) : [];
            const minItems = minpuntenRaw ? minpuntenRaw.split('\n').filter(Boolean) : [];
            const intro = acfData.pepernoot_intro || (selectedPepernoot && selectedPepernoot.content && selectedPepernoot.content.rendered ? selectedPepernoot.content.rendered.replace(/<[^>]+>/g, '') : 'Heerlijke vers geteste pepernoten.');
            const imageUrl = acfData.pepernoot_afbeelding || 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80';

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
                    el('span', { className: 'acf-block-collapsed-title' }, '— ' + postTitle),
                    el('span', { className: 'acf-block-header__tag' }, 'CPT Gekoppeld')
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

            // Hoofdweergave (Selectie balk + Live Kaart)
            const mainView = el(
                'div',
                { className: 'acf-block-fields-container' },
                el(
                    'div',
                    { className: 'acf-block-header' },
                    el('div', { className: 'acf-block-header__title' },
                        el('span', { className: 'dashicons dashicons-star-filled acf-block-header__icon' }),
                        el('strong', null, 'Pepernoot Review Kaart'),
                        el('span', { className: 'acf-block-header__tag' }, 'CPT Gekoppeld')
                    ),
                    el(
                        'div',
                        { className: 'acf-block-header__actions' },
                        selectedPepernoot && el(
                            'a',
                            {
                                href: '/wp-admin/post.php?post=' + selectedPepernoot.id + '&action=edit',
                                target: '_blank',
                                className: 'acf-btn-preview',
                                style: { textDecoration: 'none' }
                            },
                            el('span', { className: 'dashicons dashicons-edit' }),
                            ' Bewerk dit bericht in Admin'
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

                // Koppelingspaneel (Selector)
                el(
                    'div',
                    { style: { padding: '16px 20px', background: '#fffdf8', borderBottom: '1px solid #fef3c7' } },
                    el('div', { style: { display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '12px' } },
                        el('div', { style: { flex: '1 1 300px' } },
                            el('label', { style: { display: 'block', fontSize: '13px', fontWeight: '700', color: '#92400e', marginBottom: '4px' } },
                                'Kies Pepernoot uit de database:'
                            ),
                            SelectControl && el(SelectControl, {
                                value: pepernootId,
                                options: options,
                                onChange: function (val) {
                                    setAttributes({ pepernootId: parseInt(val, 10) });
                                }
                            })
                        ),
                        el('div', { style: { flex: '1 1 200px' } },
                            el('label', { style: { display: 'block', fontSize: '13px', fontWeight: '700', color: '#92400e', marginBottom: '4px' } },
                                'Knoptekst:'
                            ),
                            TextControl && el(TextControl, {
                                value: buttonText,
                                onChange: function (val) { setAttributes({ buttonText: val }); }
                            })
                        )
                    ),
                    el('p', { style: { margin: '8px 0 0 0', fontSize: '12px', color: '#b45309', display: 'flex', alignItems: 'center', gap: '6px' } },
                        el('span', { className: 'dashicons dashicons-info' }),
                        'Dit blok haalt automatisch alle velden (score, merk, winkel, prijs, plus- en minpunten en foto) op uit het geselecteerde Pepernoot CPT-bericht.'
                    )
                ),

                // Live Kaart Preview
                el(
                    'div',
                    { style: { padding: '16px' } },
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
                                el('h2', { className: 'rick-pepernoot-review-title' }, postTitle),
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
                                el('img', { src: imageUrl, alt: postTitle })
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
                    )
                )
            );

            return el(
                Fragment,
                null,
                editToggleToolbar,
                sidebarControls,
                el('div', blockProps, isCollapsed ? collapsedView : mainView)
            );
        },
        save: function () {
            return null;
        },
    });
})(window.wp);
