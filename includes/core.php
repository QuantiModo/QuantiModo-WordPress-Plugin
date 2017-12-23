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
    $builderUrl = 'https://app.quantimo.do/builder';
    $html = '<div class="updated">';
    $html .= "<p>Get your <a href='".$builderUrl."' target=\"_blank\">QuantiModo client id</a> and add it to <a href='".$settingsUrl."'  target=\"_blank\">Settings -> QuantiModo</a></p>";
    $html .= '</div><!-- /.updated -->';
    if(!$quantimodo_activated){
        echo $html;
    }
}
?>