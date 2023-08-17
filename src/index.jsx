function qm_register_blocks() {
    console.log('Quantimodo iFrame block loaded');
    const {registerBlockType} = wp.blocks;
    const {ServerSideRender} = wp.editor;
    debugger

    registerBlockType('quantimodo/qm-iframe', {
        title: 'QuantiModo iFrame',
        icon: 'format-gallery',  // Choose an icon: https://developer.wordpress.org/resource/dashicons/
        category: 'common',  // Choose a category: https://developer.wordpress.org/block-editor/developers/block-api/block-registration/#category-optional
        attributes: {
            // Here you can define optional block attributes.
        },
        edit: function (props) {
            return (
                // <ServerSideRender
                //     block="quantimodo/qm-iframe"
                //     attributes={props.attributes}
                // />
                <div>
                    {/* Your block content */}
                    <p>This is my QuantiModo iFrame block.</p>
                    {/* Insert the shortcode */}
                    {window.wp.shortcode.string({ tag: 'qm_iframe' })}
                </div>
            );
        },
        save: function () {
            // This block is dynamic, so we save nothing to the post content
            return null;
        },
    });

    registerBlockType('quantimodo/qm-redirect', {
        title: 'QuantiModo Redirect',
        icon: 'external',  // Choose an icon: https://developer.wordpress.org/resource/dashicons/
        category: 'common',  // Choose a category: https://developer.wordpress.org/block-editor/developers/block-api/block-registration/#category-optional
        attributes: {
            // Here you can define optional block attributes.
        },
        edit: function (props) {
            return (
                <div>
                    <ServerSideRender
                        block="quantimodo/qm-redirect"
                        attributes={props.attributes}
                    />
                    <h1>Block That Redirects to Your QuantiModo App</h1>
                </div>
            );
        },
        save: function () {
            // This block is dynamic so we save nothing to the post content
            return null;
        },
    });
}

wp.domReady(qm_register_blocks)
