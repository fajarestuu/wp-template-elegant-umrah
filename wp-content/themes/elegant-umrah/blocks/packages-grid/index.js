/**
 * Packages Grid Block - Editor Script
 */

(function (blocks, element, blockEditor, components, serverSideRender) {
    'use strict';

    const { registerBlockType } = blocks;
    const { createElement: el, Fragment } = element;
    const { InspectorControls } = blockEditor;
    const { PanelBody, RangeControl, ToggleControl } = components;
    const ServerSideRender = serverSideRender || (window.wp && window.wp.serverSideRender);

    registerBlockType('elegant-umrah/packages-grid', {
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { postsPerPage, showFilter } = attributes;

            return el(Fragment, null,
                el(InspectorControls, null,
                    el(PanelBody, { title: 'Package Grid Settings', initialOpen: true },
                        el(RangeControl, {
                            label: 'Number of Packages to Show',
                            value: postsPerPage,
                            onChange: (v) => setAttributes({ postsPerPage: v }),
                            min: 1,
                            max: 12,
                        }),
                        el(ToggleControl, {
                            label: 'Show Category Filter Bar',
                            checked: showFilter,
                            onChange: (v) => setAttributes({ showFilter: v }),
                        }),
                    )
                ),
                ServerSideRender
                    ? el(ServerSideRender, {
                        block: 'elegant-umrah/packages-grid',
                        attributes: attributes,
                    })
                    : el('div', { className: 'eu-packages-grid-editor-preview', style: { padding: '2rem', textAlign: 'center', background: '#F8F8F8', borderRadius: '8px' } },
                        el('p', { style: { fontWeight: '700', color: '#D4AF37', margin: '0 0 0.5rem' } }, '📦 Dynamic Packages Grid'),
                        el('p', { style: { margin: 0, fontSize: '0.9rem', color: '#666' } }, `Showing up to ${postsPerPage} packages (live data from Umrah Packages)`)
                    )
            );
        },
        save: () => null,
    });

})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.serverSideRender);
