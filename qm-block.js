window.onload = function() {
    console.log('Quantimodo iFrame block loaded');
    const { registerBlockType } = wp.blocks;
    const { ServerSideRender } = wp.editor;
    debugger

    registerBlockType('quantimodo/quantimodo-iframe', {
        title: 'QuantiModo iFrame',
        icon: 'format-gallery',  // Choose an icon: https://developer.wordpress.org/resource/dashicons/
        category: 'common',  // Choose a category: https://developer.wordpress.org/block-editor/developers/block-api/block-registration/#category-optional
        attributes: {
            // Here you can define optional block attributes.
        },
        edit: function(props) {
            return (
                <ServerSideRender
                    block="quantimodo/quantimodo-iframe"
                    attributes={props.attributes}
                />
            );
        },
        save: function() {
            // This block is dynamic, so we save nothing to the post content
            return null;
        },
    });
}

