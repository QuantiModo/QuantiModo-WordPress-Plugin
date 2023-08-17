function qm_register_blocks() {
    console.log('Quantimodo iFrame block loaded');
    const {registerBlockType} = wp.blocks;
    const {ServerSideRender} = wp.editor;

    registerBlockType('quantimodo/qm-iframe', {
        title: 'QuantiModo iFrame',
        icon: 'format-gallery',  
        category: 'common',  
        attributes: {
        },
        edit: function (props) {
            return (
                <div>
                    <p>This is my QuantiModo iFrame block.</p>
                    {window.wp.shortcode.string({ tag: 'qm_iframe' })}
                </div>
            );
        },
        save: function () {
            return null;
        },
    });
}

wp.domReady(qm_register_blocks)
