<?php
function QuantiModo_register_settings()
{
  register_setting( 'QuantiModo_settings_group', 'QuantiModo_settings' );
}
add_action( 'admin_init', 'QuantiModo_register_settings' );
function QuantiModo_uninstall()
{
  delete_option( 'QuantiModo_settings' );  // Delete options on uninstall
}
register_uninstall_hook( __FILE__, 'QuantiModo_uninstall' );
register_activation_hook( __FILE__, 'fx_admin_notice_example_activation_hook' );
/**
 * Runs only when the plugin is activated.
 * @since 0.1.0
 */
function fx_admin_notice_example_activation_hook() {
    /* Create transient data */
    set_transient( 'fx-admin-notice-example', true, 5 );
}
add_action( 'admin_notices', 'fx_admin_notice_example_notice' );  /* Add admin notice */
/**
 * Admin Notice on Activation.
 * @since 0.1.0
 */
function fx_admin_notice_example_notice(){
    // Get options
    $options = get_option('QuantiModo_settings');

    // Check to see if QuantiModo is enabled
    $quantimodo_activated = false;
    if ( esc_attr( $options['quantimodo_widget_code'] ) ) {
        $quantimodo_activated = true;
    }
    $settingsUrl = get_bloginfo('wpurl') . '/wp-admin/admin.php?page=menus.php';
    $builderUrl = 'https://builder.quantimo.do';
    $html = '<div class="updated">';
    $html .= "<p>Get your <a href='".$builderUrl."' target=\"_blank\">QuantiModo client id</a> and add it to <a href='".$settingsUrl."'  target=\"_blank\">Settings -> QuantiModo</a></p>";
    $html .= '</div><!-- /.updated -->';
    if(!$quantimodo_activated){
        echo $html;
    }
}
if(stripos(WP_SITEURL, '.quantimo.do') !== false){
    function send_push_notification( $message ) {
        $apiUrl = "https://app.quantimo.do/api/v1/messages";
        if(stripos(WP_SITEURL, 'local.quantimo.do') !== false){
            $apiUrl = "https://local.quantimo.do/api/v1/messages";
        }
        $response = wp_remote_post($apiUrl, ['body' => json_encode(['message' => $message])]);
        return $response;
    }
    add_action('messages_message_after_save', 'send_push_notification', 1, 1);

    function annointed_admin_bar_remove() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
    }
    add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0);

    //Hide admin footer from admin
    function change_footer_admin () {return ' ';}
    add_filter('admin_footer_text', 'change_footer_admin', 9999);
    function change_footer_version() {return ' ';}
    add_filter( 'update_footer', 'change_footer_version', 9999);
}
