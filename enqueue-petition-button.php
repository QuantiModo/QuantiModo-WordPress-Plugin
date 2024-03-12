<?php
function enqueue_petition_button_assets() {
    wp_enqueue_script(
        'quantimodo-petition-button-editor-script',
        plugins_url( 'src/blocks/petition-button/index.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-editor' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'src/blocks/petition-button/index.js' )
    );

    wp_enqueue_style(
        'quantimodo-petition-button-editor-style',
        plugins_url( 'src/blocks/petition-button/index.css', __FILE__ ),
        array( 'wp-edit-blocks' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'src/blocks/petition-button/index.css' )
    );
}

add_action( 'enqueue_block_editor_assets', 'enqueue_petition_button_assets' );
