(function (wp) {
    if (!wp || !wp.blocks || !wp.element) {
        return;
    }

    const { registerBlockType } = wp.blocks;
    const { useBlockProps } = wp.blockEditor || wp.editor || {};
    const { TextControl } = wp.components || {};
    const { createElement: el, Fragment } = wp.element;

    registerBlockType('rick/oliebol-card', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { buttonText = 'Bekijk Volledige Review' } = attributes;

            const blockProps = (typeof useBlockProps === 'function')
                ? useBlockProps({ className: 'rick-block-wrapper' })
                : { className: 'rick-block-wrapper' };

            return el(
                Fragment,
                null,
                el(
                    'div',
                    blockProps,
                    el(
                        'div',
                        { className: 'rick-oliebol-review-card' },
                        el(
                            'div',
                            { className: 'rick-oliebol-review-header' },
                            el(
                                'div',
                                { className: 'rick-oliebol-review-header__left' },
                                el(
                                    'div',
                                    { className: 'rick-oliebol-review-badges' },
                                    el('span', { className: 'rick-review-badge rick-review-badge--locatie' }, '📍 Amsterdam'),
                                    el('span', { className: 'rick-review-badge rick-review-badge--kraam' }, '🎪 Bakkerij Muller'),
                                    el('span', { className: 'rick-review-badge rick-review-badge--price' }, '💶 €1,50')
                                ),
                                el('h2', { className: 'rick-oliebol-review-title' }, 'Oliebollenkraam Spui'),
                                el('div', { className: 'rick-oliebol-stars' }, '★★★★☆')
                            ),
                            el(
                                'div',
                                { className: 'rick-oliebol-score-circle' },
                                el('span', { className: 'rick-oliebol-score-star' }, '★'),
                                el('span', { className: 'rick-oliebol-score-number' }, '8.5'),
                                el('span', { className: 'rick-oliebol-score-max' }, '/10')
                            )
                        ),
                        el(
                            'div',
                            { className: 'rick-oliebol-review-body' },
                            el(
                                'div',
                                { className: 'rick-oliebol-review-content' },
                                el(
                                    'div',
                                    { className: 'rick-oliebol-review-intro' },
                                    el('p', null, 'Heerlijk knapperige oliebol met een royale vulling van rozijnen en krenten. Niet vet en perfect van smaak!')
                                )
                            )
                        ),
                        el(
                            'div',
                            { className: 'rick-oliebol-card-footer' },
                            el('span', { className: 'button rick-oliebol-card-button' }, buttonText)
                        )
                    )
                )
            );
        },
        save: function () {
            return null;
        },
    });
})(window.wp);
