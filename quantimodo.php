<?php
/*
 * Plugin Name: QuantiModo
 * Version: 0.6.4
 * Description: Allow your users to record, aggregate, analyze and visualize their health and life-tracking data.
 * Author: QuantiModo
 * Author URI: https://quantimo.do
 * Plugin URI: https://quantimo.do
 */

// Prevent Direct Access
defined('ABSPATH') or die("Restricted access!");

define('QUANTIMODO_4f050d29b8BB9_VERSION', '1.5');
define('QUANTIMODO_4f050d29b8BB9_DIR', plugin_dir_path(__FILE__));
define('QUANTIMODO_4f050d29b8BB9_URL', plugin_dir_url(__FILE__));
defined('QUANTIMODO_4f050d29b8BB9_PATH') or define('QUANTIMODO_4f050d29b8BB9_PATH', untrailingslashit(plugins_url('', __FILE__)));
define('APP_BUILDER_URL', 'https://builder.quantimo.do');

require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/core.php');
require_once plugin_dir_path(__FILE__) . 'includes/access_token.php';
require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/menus.php');
require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/admin.php');
require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/embed.php');
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';

function enqueue_qm_block_assets() {
    wp_enqueue_script(
        'qm-block',
        plugins_url('build/index.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor'),
        true
    );
}
add_action('enqueue_block_editor_assets', 'enqueue_qm_block_assets');

function render_quantimodo_iframe_block($attributes, $content) {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        // If user is not logged in, redirect to login page
        auth_redirect();
    }
    // Get the user's access token
    $access_token = get_qm_access_token();

    if(!$access_token) {
        qm_error('No QM access token found');
    }
    // Your shortcode function here
    return quantimodo_iframe_func($attributes);
}

function register_quantimodo_iframe_block() {
    if (function_exists('register_block_type')) {
        register_block_type('quantimodo/quantimodo-iframe', array(
            'attributes' => array(
                // Define your block's attributes here, if any
            ),
            'render_callback' => 'render_quantimodo_iframe_block',
        ));
    }
}
add_action('init', 'register_quantimodo_iframe_block');


