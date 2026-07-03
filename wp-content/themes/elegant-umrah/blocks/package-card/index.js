/**
 * Package Card Block - Editor Script
 * Elegant Umrah Theme
 */

(function (blocks, element, blockEditor, components) {
    'use strict';

    const { registerBlockType } = blocks;
    const { createElement: el } = element;
    const { InspectorControls, MediaUpload, MediaUploadCheck } = blockEditor;
    const { PanelBody, TextControl, ToggleControl, Button } = components;

    registerBlockType('elegant-umrah/package-card', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                title, packageType, price, duration, departureDate,
                hotel, airline, isFeatured, bookingUrl, imageUrl, imageId
            } = attributes;

            return el('div', { className: 'eu-package-card' + (isFeatured ? ' is-featured' : '') },
                el(InspectorControls, null,
                    el(PanelBody, { title: 'Package Details', initialOpen: true },
                        el(TextControl, { label: 'Package Title', value: title, onChange: (v) => setAttributes({ title: v }) }),
                        el(TextControl, { label: 'Package Type', value: packageType, onChange: (v) => setAttributes({ packageType: v }) }),
                        el(TextControl, { label: 'Price', value: price, onChange: (v) => setAttributes({ price: v }), help: 'e.g. USD 1,500 or Rp 22.500.000' }),
                        el(TextControl, { label: 'Duration', value: duration, onChange: (v) => setAttributes({ duration: v }), help: 'e.g. 12 Days / 11 Nights' }),
                        el(TextControl, { label: 'Departure Date', value: departureDate, onChange: (v) => setAttributes({ departureDate: v }) }),
                        el(TextControl, { label: 'Hotel', value: hotel, onChange: (v) => setAttributes({ hotel: v }) }),
                        el(TextControl, { label: 'Airline', value: airline, onChange: (v) => setAttributes({ airline: v }) }),
                        el(TextControl, { label: 'Booking URL', value: bookingUrl, onChange: (v) => setAttributes({ bookingUrl: v }) }),
                        el(ToggleControl, { label: 'Featured / Best Seller', checked: isFeatured, onChange: (v) => setAttributes({ isFeatured: v }) }),
                    ),
                    el(PanelBody, { title: 'Card Image', initialOpen: false },
                        el(MediaUploadCheck, null,
                            el(MediaUpload, {
                                onSelect: (media) => setAttributes({ imageId: media.id, imageUrl: media.url }),
                                allowedTypes: ['image'],
                                value: imageId,
                                render: ({ open }) => el('div', null,
                                    imageUrl && el('img', { src: imageUrl, style: { maxWidth: '100%', borderRadius: '6px', marginBottom: '8px' } }),
                                    el(Button, { onClick: open, variant: 'primary' }, imageUrl ? 'Change Image' : 'Select Image'),
                                    imageUrl && el(Button, {
                                        onClick: () => setAttributes({ imageId: 0, imageUrl: '' }),
                                        variant: 'secondary', isDestructive: true, style: { marginLeft: '8px' }
                                    }, 'Remove'),
                                ),
                            })
                        )
                    ),
                ),

                // Editor preview
                isFeatured && el('span', { className: 'eu-package-badge' }, 'Best Seller'),
                imageUrl
                    ? el('img', { src: imageUrl, style: { width: '100%', height: '200px', objectFit: 'cover' }, alt: title })
                    : el('div', { className: 'eu-package-img-placeholder' }, '🕌'),
                el('div', { className: 'eu-package-body' },
                    el('div', { className: 'eu-package-type' }, packageType),
                    el('h3', { className: 'eu-package-name' }, title),
                    el('div', { className: 'eu-package-meta' },
                        duration && el('span', { className: 'eu-package-meta-item' }, '🕐 ' + duration),
                        departureDate && el('span', { className: 'eu-package-meta-item' }, '📅 ' + departureDate),
                        hotel && el('span', { className: 'eu-package-meta-item' }, '🏨 ' + hotel),
                        airline && el('span', { className: 'eu-package-meta-item' }, '✈️ ' + airline),
                    ),
                    el('div', { className: 'eu-package-divider' }),
                    el('div', { className: 'eu-package-price-row' },
                        price && el('div', { className: 'eu-package-price' },
                            el('span', { className: 'from' }, 'Starting from'),
                            el('span', { className: 'amount', style: { color: '#D4AF37' } }, price),
                            el('span', { className: 'per' }, '/ person'),
                        ),
                        el('a', { className: 'eu-package-card-btn', href: '#' }, 'Book Now'),
                    ),
                ),
            );
        },
        save: () => null,
    });

})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components);
