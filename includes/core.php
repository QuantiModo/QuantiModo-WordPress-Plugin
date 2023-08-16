<?php
/**
 * @param string $message
 */
function qm_error(string $message): void {
    $pluginLog = plugin_dir_path(__FILE__).'debug.log';
    error_log($message.PHP_EOL, 3, $pluginLog);
}
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

/**
 * @return string
 */
function qm_api_hostname(): string
{
    //return "https://local.quantimo.do";
    $apiHostName = "https://app.quantimo.do";
    $env = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : getenv('APP_HOST_NAME');
    if (!$env) {
        $env = "https://" . $_SERVER["HTTP_HOST"];
    }
    if (stripos($env, "https://utopia.quantimo.do") === 0 || stripos($env, "https://app.quantimo.do") === 0) {
        $apiHostName = "https://utopia.quantimo.do";
    }
    return $apiHostName;
}

if(stripos(WP_SITEURL, '.quantimo.do') !== false){
    function send_push_notification( $message ) {
        $apiUrl = qm_api_hostname() . "/api/v1/messages";
        $response = wp_remote_post($apiUrl, ['body' => json_encode(['message' => $message])]);
        return $response;
    }
    add_action('messages_message_after_save', 'send_push_notification', 1, 1);
    function annointed_admin_bar_remove() {
        global $wp_admin_bar;
        if($wp_admin_bar && method_exists($wp_admin_bar, 'remove_menu')){$wp_admin_bar->remove_menu('wp-logo');}
    }
    add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0); // Only done if stripos(WP_SITEURL, '.quantimo.do') !== false
    function change_footer_admin (): string
    {return ' ';} //Hide admin footer from admin
    add_filter('admin_footer_text', 'change_footer_admin', 9999); // Only done if stripos(WP_SITEURL, '.quantimo.do') !== false
    function change_footer_version(): string
    {return ' ';}
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

function qm_api_client_id() {
    $options = qm_settings();
    $quantimodo_client_id = (isset($options['quantimodo_client_id'])) ? $options['quantimodo_client_id'] : null;
    return $quantimodo_client_id;
}

function qm_settings() {
    $settings = get_option('QuantiModo_settings');
    if(!$settings){$settings = [];}
    return $settings;
}

function get_qm_option(string $name, $default = null){
    $options = qm_settings();
    $value = (isset($options[$name])) ? $options[$name] : $default;
    return $value;
}

function get_app_builder_url(): string
{
    $appBuilderUrl = APP_BUILDER_URL;
    $qmClientId = qm_api_client_id();
    if($qmClientId){$appBuilderUrl .= "?client_id=" . $qmClientId;}
    return $appBuilderUrl;
}

function qm_floating_button_enabled(): bool
{
    $floating_button_enabled = get_qm_option('floating_button_enabled') == "on";
    return $floating_button_enabled;
}
