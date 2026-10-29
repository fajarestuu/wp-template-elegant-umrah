/**
 * Hero Search Block - Editor Script
 * Renders directly inside Gutenberg canvas with full theme CSS
 */

(function (blocks, element, blockEditor, components) {
    'use strict';

    const { registerBlockType } = blocks;
    const { createElement: el, Fragment } = element;
    const { InspectorControls } = blockEditor;
    const { PanelBody, TextControl } = components;

    registerBlockType('elegant-umrah/hero-search', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { monthLabel, monthPlaceholder, typeLabel, typePlaceholder, buttonText } = attributes;

            return el(Fragment, null,
                el(InspectorControls, null,
                    el(PanelBody, { title: 'Search Form Settings', initialOpen: true },
                        el(TextControl, {
                            label: 'Month Label',
                            value: monthLabel,
                            onChange: (v) => setAttributes({ monthLabel: v }),
                        }),
                        el(TextControl, {
                            label: 'Month Placeholder',
                            value: monthPlaceholder,
                            onChange: (v) => setAttributes({ monthPlaceholder: v }),
                        }),
                        el(TextControl, {
                            label: 'Type Label',
                            value: typeLabel,
                            onChange: (v) => setAttributes({ typeLabel: v }),
                        }),
                        el(TextControl, {
                            label: 'Type Placeholder',
                            value: typePlaceholder,
                            onChange: (v) => setAttributes({ typePlaceholder: v }),
                        }),
                        el(TextControl, {
                            label: 'Button Text',
                            value: buttonText,
                            onChange: (v) => setAttributes({ buttonText: v }),
                        }),
                    )
                ),
                el('div', { className: 'eu-hero-search-wrapper' },
                    el('div', { className: 'eu-hero-search-card' },
                        el('div', { className: 'eu-hero-search-form' },
                            el('div', { className: 'eu-hero-search-field' },
                                el('label', { className: 'eu-hero-search-label' },
                                    el('span', null, '📅 '),
                                    el('span', null, monthLabel || 'Bulan Keberangkatan')
                                ),
                                el('div', { className: 'eu-hero-search-select-wrap' },
                                    el('select', { className: 'eu-hero-search-select', disabled: true },
                                        el('option', null, monthPlaceholder || 'Semua Bulan Keberangkatan')
                                    )
                                )
                            ),
                            el('div', { className: 'eu-hero-search-divider' }),
                            el('div', { className: 'eu-hero-search-field' },
                                el('label', { className: 'eu-hero-search-label' },
                                    el('span', null, '📦 '),
                                    el('span', null, typeLabel || 'Tipe Paket')
                                ),
                                el('div', { className: 'eu-hero-search-select-wrap' },
                                    el('select', { className: 'eu-hero-search-select', disabled: true },
                                        el('option', null, typePlaceholder || 'Semua Tipe Paket')
                                    )
                                )
                            ),
                            el('div', { className: 'eu-hero-search-action' },
                                el('button', { type: 'button', className: 'eu-hero-search-submit' },
                                    el('span', null, '🔍 ' + (buttonText || 'Cari Paket'))
                                )
                            )
                        )
                    )
                )
            );
        },
        save: () => null,
    });

})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components);
