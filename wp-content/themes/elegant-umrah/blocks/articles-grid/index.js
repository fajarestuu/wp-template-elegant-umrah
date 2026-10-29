/**
 * Articles Grid Block - Editor Script
 */

(function (blocks, element, blockEditor, components, serverSideRender) {
    'use strict';

    const { registerBlockType } = blocks;
    const { createElement: el, Fragment } = element;
    const { InspectorControls } = blockEditor;
    const { PanelBody, RangeControl } = components;
    const ServerSideRender = serverSideRender || (window.wp && window.wp.serverSideRender);

    registerBlockType('elegant-umrah/articles-grid', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { postsPerPage } = attributes;

            return el(Fragment, null,
                el(InspectorControls, null,
                    el(PanelBody, { title: 'Articles Grid Settings', initialOpen: true },
                        el(RangeControl, {
                            label: 'Number of Articles to Show',
                            value: postsPerPage,
                            onChange: (v) => setAttributes({ postsPerPage: v }),
                            min: 1,
                            max: 9,
                        }),
                    )
                ),
                ServerSideRender
                    ? el(ServerSideRender, {
                        block: 'elegant-umrah/articles-grid',
                        attributes: attributes,
                    })
                    : el('div', { className: 'eu-articles-grid-editor-preview', style: { padding: '2rem', textAlign: 'center', background: '#F8F8F8', borderRadius: '8px' } },
                        el('p', { style: { fontWeight: '700', color: '#D4AF37', margin: '0 0 0.5rem' } }, '📰 Dynamic Articles Grid'),
                        el('p', { style: { margin: 0, fontSize: '0.9rem', color: '#666' } }, `Showing up to ${postsPerPage} articles (live data from Blog Posts)`)
                    )
            );
        },
        save: () => null,
    });

})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.serverSideRender);
