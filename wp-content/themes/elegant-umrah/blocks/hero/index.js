/**
 * Hero Block Editor JavaScript
 * Registers the block in Gutenberg editor
 * Elegant Umrah Theme
 */

(function (blocks, element, blockEditor, components) {
    'use strict';

    const { registerBlockType } = blocks;
    const { createElement: el, Fragment } = element;
    const { InspectorControls, MediaUpload, MediaUploadCheck } = blockEditor;
    const {
        PanelBody, TextControl, TextareaControl, RangeControl,
        Button
    } = components;

    registerBlockType('elegant-umrah/hero', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                title, subtitle, badge, ctaPrimary, ctaPrimaryUrl,
                ctaSecondary, ctaSecondaryUrl, backgroundUrl, backgroundId,
                overlayOpacity, minHeight
            } = attributes;

            const bgStyle = backgroundUrl
                ? { backgroundImage: `url(${backgroundUrl})`, backgroundSize: 'cover', backgroundPosition: 'center' }
                : { background: 'linear-gradient(135deg, #0B0B0B, #1a1a1a)' };

            return el(Fragment, null,
                // Inspector controls panel
                el(InspectorControls, null,
                    el(PanelBody, { title: 'Hero Content', initialOpen: true },
                        el(TextControl, {
                            label: 'Badge Text',
                            value: badge,
                            onChange: (v) => setAttributes({ badge: v }),
                        }),
                        el(TextControl, {
                            label: 'Hero Title',
                            value: title,
                            onChange: (v) => setAttributes({ title: v }),
                        }),
                        el(TextareaControl, {
                            label: 'Subtitle',
                            value: subtitle,
                            onChange: (v) => setAttributes({ subtitle: v }),
                        }),
                    ),
                    el(PanelBody, { title: 'CTA Buttons', initialOpen: false },
                        el(TextControl, {
                            label: 'Primary CTA Text',
                            value: ctaPrimary,
                            onChange: (v) => setAttributes({ ctaPrimary: v }),
                        }),
                        el(TextControl, {
                            label: 'Primary CTA URL',
                            value: ctaPrimaryUrl,
                            onChange: (v) => setAttributes({ ctaPrimaryUrl: v }),
                        }),
                        el(TextControl, {
                            label: 'Secondary CTA Text',
                            value: ctaSecondary,
                            onChange: (v) => setAttributes({ ctaSecondary: v }),
                        }),
                        el(TextControl, {
                            label: 'Secondary CTA URL',
                            value: ctaSecondaryUrl,
                            onChange: (v) => setAttributes({ ctaSecondaryUrl: v }),
                        }),
                    ),
                    el(PanelBody, { title: 'Background & Overlay', initialOpen: false },
                        el(MediaUploadCheck, null,
                            el(MediaUpload, {
                                onSelect: (media) => setAttributes({
                                    backgroundId: media.id,
                                    backgroundUrl: media.url,
                                }),
                                allowedTypes: ['image'],
                                value: backgroundId,
                                render: ({ open }) => el('div', null,
                                    backgroundUrl && el('img', {
                                        src: backgroundUrl,
                                        style: { maxWidth: '100%', marginBottom: '8px', borderRadius: '4px' }
                                    }),
                                    el(Button, {
                                        onClick: open,
                                        variant: 'primary',
                                        style: { marginBottom: '8px' }
                                    }, backgroundUrl ? 'Change Background' : 'Select Background Image'),
                                    backgroundUrl && el(Button, {
                                        onClick: () => setAttributes({ backgroundId: 0, backgroundUrl: '' }),
                                        variant: 'secondary',
                                        isDestructive: true,
                                    }, 'Remove'),
                                ),
                            })
                        ),
                        el(RangeControl, {
                            label: 'Overlay Opacity',
                            value: overlayOpacity,
                            onChange: (v) => setAttributes({ overlayOpacity: v }),
                            min: 0,
                            max: 1,
                            step: 0.05,
                        }),
                        el(TextControl, {
                            label: 'Minimum Height (CSS value)',
                            value: minHeight,
                            onChange: (v) => setAttributes({ minHeight: v }),
                            help: 'e.g. 100svh, 100vh, 600px',
                        }),
                    ),
                ),

                // Editor preview
                el('section', {
                    className: 'eu-hero wp-block-elegant-umrah-hero',
                    style: {
                        ...bgStyle,
                        minHeight: minHeight || '100svh',
                        position: 'relative',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        overflow: 'hidden',
                    }
                },
                    el('div', {
                        style: {
                            position: 'absolute', inset: 0,
                            background: `rgba(11,11,11,${overlayOpacity || 0.75})`,
                            zIndex: 1,
                        }
                    }),
                    el('div', {
                        className: 'eu-hero-content',
                        style: {
                            position: 'relative', zIndex: 2,
                            textAlign: 'center', maxWidth: '800px',
                            padding: '2rem 1.5rem', width: '100%',
                        }
                    },
                        badge && el('div', { className: 'eu-hero-badge' }, badge),
                        el('h1', { style: { color: '#FFFFFF', fontSize: 'clamp(2rem,6vw,4rem)', fontWeight: '800', lineHeight: '1.15', marginBottom: '1.25rem' } }, title),
                        subtitle && el('p', { className: 'eu-hero-subtitle' }, subtitle),
                        el('div', { style: { display: 'flex', gap: '1rem', justifyContent: 'center', flexWrap: 'wrap' } },
                            el('a', { className: 'eu-btn eu-btn-cta', href: '#' }, ctaPrimary),
                            ctaSecondary && el('a', {
                                style: { background: 'transparent', color: '#D4AF37', border: '2px solid rgba(212,175,55,0.5)', padding: '0.85rem 2rem', borderRadius: '4px', fontWeight: '600', fontSize: '0.9rem', textTransform: 'uppercase', textDecoration: 'none' }
                            }, ctaSecondary),
                        ),
                    )
                )
            );
        },
        save: function () {
            // Uses server-side rendering (render.php)
            return null;
        },
    });

})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components);
