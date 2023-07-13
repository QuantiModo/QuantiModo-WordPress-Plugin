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
	if(!$options){$options = array();}
    // Check to see if QuantiModo is enabled
    $quantimodo_activated = false;
    if ( esc_attr( $options['quantimodo_widget_code'] ?? false ) ) {
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
        if(stripos(WP_SITEURL, 'local.quantimo.do') !== false){$apiUrl = "https://local.quantimo.do/api/v1/messages";}
        $response = wp_remote_post($apiUrl, ['body' => json_encode(['message' => $message])]);
        return $response;
    }
    add_action('messages_message_after_save', 'send_push_notification', 1, 1);
    function annointed_admin_bar_remove() {
        global $wp_admin_bar;
        if($wp_admin_bar && method_exists($wp_admin_bar, 'remove_menu')){$wp_admin_bar->remove_menu('wp-logo');}
    }
    add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0); // Only done if stripos(WP_SITEURL, '.quantimo.do') !== false
    function change_footer_admin () {return ' ';} //Hide admin footer from admin
    add_filter('admin_footer_text', 'change_footer_admin', 9999); // Only done if stripos(WP_SITEURL, '.quantimo.do') !== false
    function change_footer_version() {return ' ';}
    add_filter( 'update_footer', 'change_footer_version', 9999); // Only done if stripos(WP_SITEURL, '.quantimo.do') !== false
    function qm_development_testing_login(){
        $origin = 'https://' . $_SERVER['HTTP_HOST'];
        if(!in_array($origin, [
            "https://staging-wp.quantimo.do",
            "https://dev-wp.quantimo.do"
        ])){return;}
        if(!isset($_GET['log']) || !isset($_GET['pwd'])){return;}
        add_action('init', function() {
            $origin =  'https://' . $_SERVER['HTTP_HOST'];
            $currentUrl = $origin . $_SERVER["REQUEST_URI"];
            $loginUrl = wp_login_url();
            $user = get_user_by('login', $_GET['log']);
            $redirect_to = admin_url();
            if($currentUrl !== $loginUrl){$redirect_to = explode("?", $currentUrl)[0];}
            if($user && wp_check_password($_GET['pwd'], $user->data->user_pass, $user->ID)){
                wp_set_current_user($user->ID, $user->user_login);
                wp_set_auth_cookie($user->ID, true);
                do_action('wp_login', $user->user_login);
                wp_redirect($redirect_to);
                exit;
            }
            wp_redirect(home_url());
            exit;
        });
    }
    qm_development_testing_login();
}
