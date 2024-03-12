<?php
if(!defined('WP_SITEURL')){
	define('WP_SITEURL', wp_guess_url());
}

/**
 * Runs only when the plugin is activated.
 * @since 0.1.0
 */
function fx_admin_notice_example_activation_hook() {
    /* Create transient data */
    set_transient( 'fx-admin-notice-example', true, 5 );
}
register_activation_hook( __FILE__, 'fx_admin_notice_example_activation_hook' );

/**
 * Admin Notice on Activation.
 * @since 0.1.0
 */
function fx_admin_notice_example_notice(){
    // Get options
    // Check to see if QuantiModo is enabled
    $has_client_id = !empty(qm_api_client_id());
    $settingsUrl = get_bloginfo('wpurl') . '/wp-admin/admin.php?page=menus.php';
    $builderUrl = get_app_builder_url();
    $html = '<div class="updated">';
    $html .= "<p>Get your <a href='".$builderUrl."' target=\"_blank\">QuantiModo client id</a> and add it to <a href='".$settingsUrl."'  target=\"_blank\">Settings -> QuantiModo</a></p>";
    $html .= '</div><!-- /.updated -->';
    if(!$has_client_id){
        echo $html;
    }
}
add_action( 'admin_notices', 'fx_admin_notice_example_notice' );  /* Add admin notice */


if(stripos(WP_SITEURL, '.quantimo.do') !== false){
    function send_push_notification( $message ) {
        $apiUrl = qm_api_origin() . "/api/v1/messages";
        $response = wp_remote_post($apiUrl, ['body' => json_encode(['message' => $message])]);
        return $response;
    }
    add_action('messages_message_after_save', 'send_push_notification', 1, 1);
}
