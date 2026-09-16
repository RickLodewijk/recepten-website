/**
 * Gutenberg Editor Script for ACF Spin Wheel Block
 *
 * @package ACF_Spin_Wheel
 */

( function( wp ) {
    'use strict';

    if ( ! wp || ! wp.blocks || ! wp.element ) {
        return;
    }

    var registerBlockType = wp.blocks.registerBlockType;
    var el = wp.element.createElement;
    var Fragment = wp.element.Fragment;
    var useBlockProps = ( wp.blockEditor && wp.blockEditor.useBlockProps ) || function() { return {}; };
    var InspectorControls = ( wp.blockEditor && wp.blockEditor.InspectorControls ) || ( wp.editor && wp.editor.InspectorControls );
    var components = wp.components || {};
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var __ = ( wp.i18n && wp.i18n.__ ) ? wp.i18n.__ : function( s ) { return s; };

    registerBlockType( 'acf-spin-wheel/wheel', {
        title: __( 'Spin Wheel', 'acf-spin-wheel' ),
        description: __( 'Interactief draairad voor het kiezen van namen, recepten of opties.', 'acf-spin-wheel' ),
        icon: 'controls-repeat',
        category: 'widgets',
        keywords: [
            __( 'rad', 'acf-spin-wheel' ),
            __( 'spin', 'acf-spin-wheel' ),
            __( 'wheel', 'acf-spin-wheel' ),
            __( 'fortuin', 'acf-spin-wheel' ),
            __( 'lootjes', 'acf-spin-wheel' )
        ],
        attributes: {
            title: {
                type: 'string',
                default: ''
            }
        },

        edit: function( props ) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var blockProps = useBlockProps( {
                className: 'acf-spin-wheel-block-preview'
            } );

            var customTitle = attributes.title || '';

            return el(
                Fragment,
                null,
                InspectorControls && el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        {
                            title: __( 'Rad Instellingen', 'acf-spin-wheel' ),
                            initialOpen: true
                        },
                        el( TextControl, {
                            label: __( 'Standaard Titel (optioneel)', 'acf-spin-wheel' ),
                            value: customTitle,
                            placeholder: __( 'Bijv. Wat eten we vanavond?', 'acf-spin-wheel' ),
                            help: __( 'Wordt als initiële titel getoond bij het laden van het rad.', 'acf-spin-wheel' ),
                            onChange: function( newTitle ) {
                                setAttributes( { title: newTitle } );
                            }
                        } )
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el(
                        'div',
                        { className: 'acf-spin-wheel-preview-card' },
                        el(
                            'div',
                            { className: 'acf-spin-wheel-preview-wheel-icon' },
                            el(
                                'svg',
                                {
                                    viewBox: '0 0 100 100',
                                    width: '64',
                                    height: '64',
                                    className: 'acf-spin-wheel-svg-icon'
                                },
                                el( 'circle', { cx: '50', cy: '50', r: '46', fill: '#f8fafc', stroke: '#cbd5e1', strokeWidth: '4' } ),
                                el( 'path', { d: 'M50 50 L50 4 A46 46 0 0 1 96 50 Z', fill: '#3b82f6' } ),
                                el( 'path', { d: 'M50 50 L96 50 A46 46 0 0 1 50 96 Z', fill: '#10b981' } ),
                                el( 'path', { d: 'M50 50 L50 96 A46 46 0 0 1 4 50 Z', fill: '#f59e0b' } ),
                                el( 'path', { d: 'M50 50 L4 50 A46 46 0 0 1 50 4 Z', fill: '#ef4444' } ),
                                el( 'circle', { cx: '50', cy: '50', r: '14', fill: '#1e293b' } ),
                                el( 'circle', { cx: '50', cy: '50', r: '7', fill: '#ffffff' } )
                            )
                        ),
                        el(
                            'div',
                            { className: 'acf-spin-wheel-preview-info' },
                            el(
                                'h3',
                                { className: 'acf-spin-wheel-preview-title' },
                                customTitle ? customTitle : __( '🎡 ACF Spin Wheel', 'acf-spin-wheel' )
                            ),
                            el(
                                'p',
                                { className: 'acf-spin-wheel-preview-desc' },
                                __( 'Interactief draairad met live canvas, audio-effecten, winnaarsmodal en opgeslagen radden.', 'acf-spin-wheel' )
                            ),
                            el(
                                'div',
                                { className: 'acf-spin-wheel-preview-badge' },
                                el( 'span', { className: 'dashicons dashicons-visibility' } ),
                                ' ' + __( 'Volledig interactief op de live website', 'acf-spin-wheel' )
                            )
                        )
                    )
                )
            );
        },

        save: function() {
            // Dynamic block rendered via PHP render_callback
            return null;
        }
    } );
} )( window.wp );
