/**
 * Testimonial Block - Editor Script
 */

(function (blocks, element, blockEditor, components) {
    'use strict';

    const { registerBlockType } = blocks;
    const { createElement: el } = element;
    const { InspectorControls } = blockEditor;
    const { PanelBody, TextControl, TextareaControl, RangeControl } = components;

    registerBlockType('elegant-umrah/testimonial', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { quote, authorName, authorOrigin, packageType, rating } = attributes;
            const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);

            return el('div', { className: 'eu-testimonial-card' },
                el(InspectorControls, null,
                    el(PanelBody, { title: 'Testimonial Details', initialOpen: true },
                        el(TextareaControl, { label: 'Quote', value: quote, onChange: (v) => setAttributes({ quote: v }), rows: 4 }),
                        el(TextControl, { label: 'Author Name', value: authorName, onChange: (v) => setAttributes({ authorName: v }) }),
                        el(TextControl, { label: 'City / Origin', value: authorOrigin, onChange: (v) => setAttributes({ authorOrigin: v }) }),
                        el(TextControl, { label: 'Package Type / Year', value: packageType, onChange: (v) => setAttributes({ packageType: v }) }),
                        el(RangeControl, { label: 'Star Rating', value: rating, onChange: (v) => setAttributes({ rating: v }), min: 1, max: 5 }),
                    ),
                ),
                el('div', { className: 'eu-testimonial-quote' }, '"'),
                el('div', { className: 'eu-testimonial-stars' }, stars),
                el('p', { className: 'eu-testimonial-text' }, quote),
                el('div', { className: 'eu-testimonial-author' },
                    el('div', { className: 'eu-testimonial-avatar' }, authorName.charAt(0).toUpperCase()),
                    el('div', { className: 'eu-testimonial-info' },
                        el('p', { className: 'name' }, authorName),
                        el('p', { className: 'origin' }, [authorOrigin, packageType].filter(Boolean).join(' · ')),
                    ),
                ),
            );
        },
        save: () => null,
    });

})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components);
