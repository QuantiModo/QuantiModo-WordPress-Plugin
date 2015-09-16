<?php

/**
 * Plugin Name: QuantiModo
 * Plugin URI: https://app.quantimod.do
 * Description: A WordPress plugin that allows users to login or register by authenticating with an existing Quantimodo account via OAuth 2.0. Easily drops into new or existing sites, integrates with existing users.
 * Version: 0.2.9
 * Author: QuantiModo
 * Author URI: https://app.quantimod.do
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/** Load composer */
$composer = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($composer)) {
    require_once $composer;
}

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

require_once('includes/QMWPAuth.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/*
 * Plugin Class
*/

Class QMWP
{
    // singleton class pattern:
    protected static $instance = NULL;

    /**
     * @return null|QMWP
     */
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }

    // set a version that we can use for performing plugin updates, this should always match the plugin version:
    const PLUGIN_VERSION = "0.2.5";

    // define the settings used by this plugin; this array will be used for registering settings, applying default values, and deleting them during uninstall:
    private $settings = array(
        'qmwp_show_login_messages' => 0,                                // 0, 1
        'qmwp_login_redirect' => 'home_page',                            // home_page, last_page, specific_page, admin_dashboard, profile_page, custom_url
        'qmwp_login_redirect_page' => 0,                                // any whole number (wordpress page id)
        'qmwp_login_redirect_url' => '',                                // any string (url)
        'qmwp_logout_redirect' => 'home_page',                            // home_page, last_page, specific_page, admin_dashboard, profile_page, custom_url, default_handling
        'qmwp_logout_redirect_page' => 0,                                // any whole number (wordpress page id)
        'qmwp_logout_redirect_url' => '',                                // any string (url)
        'qmwp_logout_inactive_users' => 0,                                // any whole number (minutes)
        'qmwp_hide_wordpress_login_form' => 0,                            // 0, 1
        'qmwp_logo_links_to_site' => 0,                                    // 0, 1
        'qmwp_logo_image' => '',                                        // any string (image url)
        'qmwp_bg_image' => '',                                            // any string (image url)
        'qmwp_login_form_show_login_screen' => 'Login Screen',            // any string (name of a custom login form shortcode design)
        'qmwp_login_form_show_profile_page' => 'Profile Page',            // any string (name of a custom login form shortcode design)
        'qmwp_login_form_show_comments_section' => 'None',                // any string (name of a custom login form shortcode design)
        'qmwp_login_form_designs' => array(                                // array of shortcode designs to be included by default; same array signature as the shortcode function uses
            'Login Screen' => array(
                'icon_set' => 'none',
                'layout' => 'buttons-column',
                'align' => 'center',
                'show_login' => 'conditional',
                'show_logout' => 'conditional',
                'button_prefix' => 'Login with',
                'logged_out_title' => 'Please login:',
                'logged_in_title' => 'You are already logged in.',
                'logging_in_title' => 'Logging in...',
                'logging_out_title' => 'Logging out...',
                'style' => '',
                'class' => '',
            ),
            'Profile Page' => array(
                'icon_set' => 'none',
                'layout' => 'buttons-row',
                'align' => 'left',
                'show_login' => 'always',
                'show_logout' => 'never',
                'button_prefix' => 'Link',
                'logged_out_title' => 'Select a provider:',
                'logged_in_title' => 'Select a provider:',
                'logging_in_title' => 'Authenticating...',
                'logging_out_title' => 'Logging out...',
                'style' => '',
                'class' => '',
            ),
        ),
        'qmwp_suppress_welcome_email' => 0,                                // 0, 1
        'qmwp_new_user_role' => 'contributor',                            // role
        'qmwp_google_api_enabled' => 0,                                    // 0, 1
        'qmwp_google_api_id' => '',                                        // any string
        'qmwp_google_api_secret' => '',                                    // any string
        'qmwp_quantimodo_api_enabled' => 0,                                    // 0, 1
        'qmwp_quantimodo_api_id' => '',                                        // any string
        'qmwp_quantimodo_api_secret' => '',                                    // any string
        'qmwp_x_mashape_key' => '',                                     //any string
        'qmwp_http_util' => 'curl',                                        // curl, stream-context
        'qmwp_http_util_verify_ssl' => 1,                                // 0, 1
        'qmwp_restore_default_settings' => 0,                            // 0, 1
        'qmwp_delete_settings_on_uninstall' => 0,                        // 0, 1
        'qmwp_plugin_pages' => array(
            'QMWP Search Correlations' => '[qmwp_search_correlations]',
            'QMWP Mood Tracker' => '[qmwp_mood_tracker]',
            'QMWP Connectors' => '[qmwp_connectors]',
            'QMWP Manage Accounts' => '[qmwp_manage_accounts]',
            'QMWP Bargraph Scatterplot Timeline' => '[qmwp_bargraph_scatterplot_timeline]',
            'QMWP Timeline' => '[qmwp_timeline]',
            'QMWP Add Measurement'  =>  '[qmwp_add_measurement]'
        )
    );

    /**
     * when the plugin class gets created, fire the initialization
     */
    function __construct()
    {
        // hook activation and deactivation for the plugin:
        register_activation_hook(__FILE__, array($this, 'qmwp_activate'));
        register_deactivation_hook(__FILE__, array($this, 'qmwp_deactivate'));
        // hook load event to handle any plugin updates:
        add_action('plugins_loaded', array($this, 'qmwp_update'));
        // hook init event to handle plugin initialization:
        add_action('init', array($this, 'init'));
    }

    /**
     *  initialize the plugin's functionality by hooking into wordpress
     */
    function init()
    {
        add_shortcode('qmwp_mood_tracker', array($this, 'qmwp_mood_tracker'));
        add_shortcode('qmwp_connectors', array($this, 'qmwp_connectors'));
        add_shortcode('qmwp_manage_accounts', array($this, 'qmwp_manage_accounts'));
        add_shortcode('qmwp_bargraph_scatterplot_timeline', array($this, 'qmwp_bargraph_scatterplot_timeline'));
        add_shortcode('qmwp_timeline', array($this, 'qmwp_timeline'));
        add_shortcode('qmwp_search_correlations', array($this, 'qmwp_search_correlations'));
        add_shortcode('qmwp_add_measurement', array($this, 'qmwp_add_measurement'));

        // restore default settings if necessary; this might get toggled by the admin or forced by a new version of the plugin:
        if (get_option("qmwp_restore_default_settings")) {
            $this->qmwp_restore_default_settings();
        }

        // hook the query_vars and template_redirect so we can stay within the wordpress context no matter what (avoids having to use wp-load.php)
        add_filter('query_vars', array($this, 'qmwp_qvar_triggers'));
        add_action('template_redirect', array($this, 'qmwp_qvar_handlers'));
        // hook scripts and styles for frontend pages:
        add_action('wp_enqueue_scripts', array($this, 'qmwp_init_frontend_scripts_styles'));
        // hook scripts and styles for backend pages:
        add_action('admin_enqueue_scripts', array($this, 'qmwp_init_backend_scripts_styles'));
        add_action('admin_menu', array($this, 'qmwp_settings_page'));
        add_action('admin_init', array($this, 'qmwp_register_settings'));
        $plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$plugin", array($this, 'qmwp_settings_link'));
        // hook scripts and styles for login page:
        add_action('login_enqueue_scripts', array($this, 'qmwp_init_login_scripts_styles'));
        if (get_option('qmwp_logo_links_to_site') == true) {
            add_filter('login_headerurl', array($this, 'qmwp_logo_link'));
        }
        add_filter('login_message', array($this, 'qmwp_customize_login_screen'));
        // hooks used globally:
        add_filter('comment_form_defaults', array($this, 'qmwp_customize_comment_form_fields'));
        //add_action('comment_form_top', array($this, 'qmwp_customize_comment_form'));
        add_action('show_user_profile', array($this, 'qmwp_linked_accounts'));
        add_action('wp_logout', array($this, 'qmwp_end_logout'));
        add_action('wp_ajax_qmwp_logout', array($this, 'qmwp_logout_user'));
        add_action('wp_ajax_qmwp_unlink_account', array($this, 'qmwp_unlink_account'));
        add_action('wp_ajax_nopriv_qmwp_unlink_account', array($this, 'qmwp_unlink_account'));
        add_shortcode('qmwp_login_form', array($this, 'qmwp_login_form'));

        // push login messages into the DOM if the setting is enabled:
        if (get_option('qmwp_show_login_messages') !== false) {
            add_action('wp_footer', array($this, 'qmwp_push_login_messages'));
            add_filter('admin_footer', array($this, 'qmwp_push_login_messages'));
            add_filter('login_footer', array($this, 'qmwp_push_login_messages'));
        }
    }

    /**
     * do something during plugin activation
     */
    function qmwp_activate()
    {
        $this->create_plugin_pages($this->settings['qmwp_plugin_pages']);
    }

    /**
     * do something during plugin deactivation
     */
    function qmwp_deactivate()
    {

        $this->delete_plugin_pages($this->settings['qmwp_plugin_pages']);

    }

    /**
     * do something during plugin update
     */
    function qmwp_update()
    {
        $plugin_version = QMWP::PLUGIN_VERSION;
        $installed_version = get_option("qmwp_plugin_version");
        if (!$installed_version || $installed_version <= 0 || $installed_version != $plugin_version) {
            // version mismatch, run the update logic...
            // add any missing options and set a default (usable) value:
            $this->qmwp_add_missing_settings();
            // set the new version so we don't trigger the update again:
            update_option("qmwp_plugin_version", $plugin_version);
            // create an admin notice:
            add_action('admin_notices', array($this, 'qmwp_update_notice'));
        }
    }

    /**
     * indicate to the admin that the plugin has been updated
     */
    function qmwp_update_notice()
    {
        $settings_link = "<a href='options-general.php?page=QuantiModo.php'>Settings Page</a>"; // CASE SeNsItIvE filename!
        ?>
        <div class="updated">
            <p>QuantiModo has been updated! Please review the <?php echo $settings_link ?>.</p>
        </div>
        <?php
    }

    /**
     *  adds any missing settings and their default values
     */
    function qmwp_add_missing_settings()
    {
        foreach ($this->settings as $setting_name => $default_value) {
            // call add_option() which ensures that we only add NEW options that don't exist:
            if (is_array($this->settings[$setting_name])) {
                $default_value = json_encode($default_value);
            }
            $added = add_option($setting_name, $default_value);
        }
    }

    /**
     * restores the default plugin settings
     */
    function qmwp_restore_default_settings()
    {
        foreach ($this->settings as $setting_name => $default_value) {
            // call update_option() which ensures that we update the setting's value:
            if (is_array($this->settings[$setting_name])) {
                $default_value = json_encode($default_value);
            }
            update_option($setting_name, $default_value);
        }
        add_action('admin_notices', array($this, 'qmwp_restore_default_settings_notice'));
    }

    /**
     *  indicate to the admin that the plugin has been updated
     */
    function qmwp_restore_default_settings_notice()
    {
        $settings_link = "<a href='options-general.php?page=QuantiModo.php'>Settings Page</a>"; // CASE SeNsItIvE filename!
        ?>
        <div class="updated">
            <p>The default settings have been restored. You may review the <?php echo $settings_link ?>.</p>
        </div>
        <?php
    }

    /**
     *  init scripts and styles for use on FRONTEND PAGES
     */
    function qmwp_init_frontend_scripts_styles()
    {
        // here we "localize" php variables, making them available as a js variable in the browser:
        $qmwp_cvars = array(
            // basic info:
            'ajaxurl' => admin_url('admin-ajax.php'),
            'template_directory' => get_bloginfo('template_directory'),
            'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
            'plugins_url' => plugins_url(),
            'plugin_dir_url' => plugin_dir_url(__FILE__),
            'url' => get_bloginfo('url'),
            'logout_url' => wp_logout_url(),
            // other:
            'show_login_messages' => get_option('qmwp_show_login_messages'),
            'logout_inactive_users' => get_option('qmwp_logout_inactive_users'),
            'logged_in' => is_user_logged_in(),
        );
        wp_enqueue_script('qmwp-cvars', plugins_url('/js/cvars.js', __FILE__));
        wp_localize_script('qmwp-cvars', 'qmwp_cvars', $qmwp_cvars);
        // we always need jquery:
        wp_enqueue_script('jquery');
        // load the core plugin scripts/styles:
        wp_enqueue_script('qmwp-script', plugin_dir_url(__FILE__) . 'js/qmwp.js', array());
        wp_enqueue_style('qmwp-style', plugin_dir_url(__FILE__) . 'css/qmwp.css', array());
    }

    /**
     *  init scripts and styles for use on BACKEND PAGES
     */
    function qmwp_init_backend_scripts_styles()
    {
        // here we "localize" php variables, making them available as a js variable in the browser:
        $qmwp_cvars = array(
            // basic info:
            'ajaxurl' => admin_url('admin-ajax.php'),
            'template_directory' => get_bloginfo('template_directory'),
            'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
            'plugins_url' => plugins_url(),
            'plugin_dir_url' => plugin_dir_url(__FILE__),
            'url' => get_bloginfo('url'),
            // other:
            'show_login_messages' => get_option('qmwp_show_login_messages'),
            'logout_inactive_users' => get_option('qmwp_logout_inactive_users'),
            'logged_in' => is_user_logged_in(),
        );
        wp_enqueue_script('qmwp-cvars', plugins_url('/js/cvars.js', __FILE__));
        wp_localize_script('qmwp-cvars', 'qmwp_cvars', $qmwp_cvars);
        // we always need jquery:
        wp_enqueue_script('jquery');
        // load the core plugin scripts/styles:
        wp_enqueue_script('qmwp-script', plugin_dir_url(__FILE__) . 'js/qmwp.js', array());
        wp_enqueue_style('qmwp-style', plugin_dir_url(__FILE__) . 'css/qmwp.css', array());
        // load the default wordpress media screen:
        wp_enqueue_media();
    }

    // init scripts and styles for use on the LOGIN PAGE:
    function qmwp_init_login_scripts_styles()
    {
        // here we "localize" php variables, making them available as a js variable in the browser:
        $qmwp_cvars = array(
            // basic info:
            'ajaxurl' => admin_url('admin-ajax.php'),
            'template_directory' => get_bloginfo('template_directory'),
            'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
            'plugins_url' => plugins_url(),
            'plugin_dir_url' => plugin_dir_url(__FILE__),
            'url' => get_bloginfo('url'),
            // login specific:
            'hide_login_form' => get_option('qmwp_hide_wordpress_login_form'),
            'logo_image' => get_option('qmwp_logo_image'),
            'bg_image' => get_option('qmwp_bg_image'),
            'login_message' => $_SESSION['QMWP']['RESULT'],
            'show_login_messages' => get_option('qmwp_show_login_messages'),
            'logout_inactive_users' => get_option('qmwp_logout_inactive_users'),
            'logged_in' => is_user_logged_in(),
        );
        wp_enqueue_script('qmwp-cvars', plugins_url('/js/cvars.js', __FILE__));
        wp_localize_script('qmwp-cvars', 'qmwp_cvars', $qmwp_cvars);
        // we always need jquery:
        wp_enqueue_script('jquery');
        // load the core plugin scripts/styles:
        wp_enqueue_script('qmwp-script', plugin_dir_url(__FILE__) . 'js/qmwp.js', array());
        wp_enqueue_style('qmwp-style', plugin_dir_url(__FILE__) . 'css/qmwp.css', array());
    }

    // add a settings link to the plugins page:
    function qmwp_settings_link($links)
    {
        $qmwp_settings_links = array(
            'settings' => "<a href='options-general.php?page=QuantiModo.php'>Settings</a>",
        );
        return array_merge($qmwp_settings_links, $links);
    }

    // ===================
    // LOGIN FLOW HANDLING
    // ===================

    /**
     * define the querystring variables that should trigger an action
     * @param $vars
     * @return array
     */
    function qmwp_qvar_triggers($vars)
    {
        $vars[] = 'connect';
        $vars[] = 'code';
        $vars[] = 'error_description';
        $vars[] = 'error_message';
        return $vars;
    }

    /**
     *  handle the querystring triggers
     */
    function qmwp_qvar_handlers()
    {
        if (get_query_var('connect')) {
            $provider = get_query_var('connect');
            $this->qmwp_include_connector($provider);
        } elseif (get_query_var('code')) {
            $provider = $_SESSION['QMWP']['PROVIDER'];
            $this->qmwp_include_connector($provider);
        } elseif (get_query_var('error_description') || get_query_var('error_message')) {
            $provider = $_SESSION['QMWP']['PROVIDER'];
            $this->qmwp_include_connector($provider);
        }
    }

    /**
     * load the provider script that is being requested by the user or being called back after authentication
     * @param $provider
     */
    function qmwp_include_connector($provider)
    {
        // normalize the provider name (no caps, no spaces):
        $provider = strtolower($provider);
        $provider = str_replace(" ", "", $provider);
        $provider = str_replace(".", "", $provider);
        // include the provider script:
        include 'includes/login-providers/login-' . $provider . '.php';
    }

    // =======================
    // LOGIN / LOGOUT HANDLING
    // =======================

    /**
     * match the oauth identity to an existing wordpress user account
     *
     * @param $oauth_identity
     * @return bool|WP_User
     */
    function qmwp_match_wordpress_user($oauth_identity)
    {
        // attempt to get a wordpress user id from the database that matches the $oauth_identity['id'] value:
        global $wpdb;
        $usermeta_table = $wpdb->usermeta;
        $query_string = "SELECT $usermeta_table.user_id FROM $usermeta_table WHERE $usermeta_table.meta_key = 'qmwp_identity' AND $usermeta_table.meta_value LIKE '%" . $oauth_identity['provider'] . "|" . $oauth_identity['id'] . "%'";
        $query_result = $wpdb->get_var($query_string);
        // attempt to get a wordpress user with the matched id:
        $user = get_user_by('id', $query_result);
        return $user;
    }

    /**
     * login (or register and login) a wordpress user based on their oauth identity
     *
     * @param $oauth_identity
     */
    function qmwp_login_user($oauth_identity)
    {
        // store the user info in the user session so we can grab it later if we need to register the user:
        $_SESSION["QMWP"]["USER_ID"] = $oauth_identity["id"];
        // try to find a matching wordpress user for the now-authenticated user's oauth identity:
        $matched_user = $this->qmwp_match_wordpress_user($oauth_identity);
        // handle the matched user if there is one:
        if ($matched_user) {
            // there was a matching wordpress user account, log it in now:
            $user_id = $matched_user->ID;
            $user_login = $matched_user->user_login;
            wp_set_current_user($user_id, $user_login);
            wp_set_auth_cookie($user_id);
            do_action('wp_signon', $user_login, $matched_user);
            // after login, redirect to the user's last location
            $this->update_user_tokens($user_id);
            $this->qmwp_end_login("Logged in successfully!");
        }
        // handle the already logged in user if there is one:
        if (is_user_logged_in()) {
            // there was a wordpress user logged in, but it is not associated with the now-authenticated user's email address, so associate it now:
            global $current_user;
            get_currentuserinfo();
            $user_id = $current_user->ID;
            $this->qmwp_link_account($user_id);
            // after linking the account, redirect user to their last url
            $this->qmwp_end_login("Your account was linked successfully with your third party authentication provider.");
        }
        // handle the logged out user or no matching user (register the user):
        if (!is_user_logged_in() && !$matched_user) {
            // this person is not logged into a wordpress account and has no third party authentications registered, so proceed to register the wordpress user:
            include 'includes/qmwp-register.php';
        }
        // we shouldn't be here, but just in case...
        $this->qmwp_end_login("Sorry, we couldn't log you in. The login flow terminated in an unexpected way. Please notify the admin or try again later.");
    }

    /**
     * ends the login request by clearing the login state and redirecting the user to the desired page
     *
     * @param $msg
     */
    function qmwp_end_login($msg)
    {
        $last_url = isset($_SESSION["QMWP"]["LAST_URL"]) ? $_SESSION["QMWP"]["LAST_URL"] : null;
        unset($_SESSION["QMWP"]["LAST_URL"]);
        $_SESSION["QMWP"]["RESULT"] = $msg;
        $this->qmwp_clear_login_state();
        $redirect_method = get_option("qmwp_login_redirect");
        $redirect_url = "";
        switch ($redirect_method) {
            case "home_page":
                $redirect_url = site_url();
                break;
            case "last_page":
                $redirect_url = $last_url;
                break;
            case "specific_page":
                $redirect_url = get_permalink(get_option('qmwp_login_redirect_page'));
                break;
            case "admin_dashboard":
                $redirect_url = admin_url();
                break;
            case "user_profile":
                $redirect_url = get_edit_user_link();
                break;
            case "custom_url":
                $redirect_url = get_option('qmwp_login_redirect_url');
                break;
        }
        //header("Location: " . $redirect_url);
        if (!empty($redirect_url)) {
            wp_safe_redirect($redirect_url);
        }
        die();
    }

    /**
     * logout the wordpress user
     *
     */
    function qmwp_logout_user()
    {
        //TODO: this is usually called from a custom logout button, but we could have the button call /wp-logout.php?action=logout for more consistency...
        // logout the user:
        $user = null;        // nullify the user
        session_destroy();    // destroy the php user session
        wp_logout();        // logout the wordpress user...this gets hooked and diverted to qmwp_end_logout() for final handling
    }

    /**
     * ends the logout request by redirecting the user to the desired page
     * @return bool
     */
    function qmwp_end_logout()
    {
        $_SESSION["QMWP"]["RESULT"] = 'Logged out successfully.';
        if (is_user_logged_in()) {
            // user is logged in and trying to logout...get their Last Page:
            $last_url = $_SERVER['HTTP_REFERER'];
        } else {
            // user is NOT logged in and trying to logout...get their Last Page minus the querystring so we don't trigger the logout confirmation:
            $last_url = strtok($_SERVER['HTTP_REFERER'], "?");
        }
        unset($_SESSION["QMWP"]["LAST_URL"]);
        $this->qmwp_clear_login_state();
        $redirect_method = get_option("qmwp_logout_redirect");
        $redirect_url = "";
        switch ($redirect_method) {
            case "default_handling":
                return false;
            case "home_page":
                $redirect_url = site_url();
                break;
            case "last_page":
                $redirect_url = $last_url;
                break;
            case "specific_page":
                $redirect_url = get_permalink(get_option('qmwp_logout_redirect_page'));
                break;
            case "admin_dashboard":
                $redirect_url = admin_url();
                break;
            case "user_profile":
                $redirect_url = get_edit_user_link();
                break;
            case "custom_url":
                $redirect_url = get_option('qmwp_logout_redirect_url');
                break;
        }
        //header("Location: " . $redirect_url);
        wp_safe_redirect($redirect_url);
        die();
    }

    /**
     * links a third-party account to an existing wordpress user account
     * @param $user_id
     */
    function qmwp_link_account($user_id)
    {
        if ($_SESSION['QMWP']['USER_ID'] != '') {
            add_user_meta($user_id, 'qmwp_identity', $_SESSION['QMWP']['PROVIDER'] . '|' . $_SESSION['QMWP']['USER_ID'] . '|' . time());
            add_user_meta($user_id, 'qmwp_access_token', $_SESSION['QMWP']['ACCESS_TOKEN']);
            add_user_meta($user_id, 'qmwp_refresh_token', $_SESSION['QMWP']['REFRESH_TOKEN']);
            add_user_meta($user_id, 'qmwp_token_expires_at', $_SESSION['QMWP']['EXPIRES_AT']);
        }
    }

    /**
     *  unlinks a third-party provider from an existing wordpress user account
     */
    function qmwp_unlink_account()
    {
        // get qmwp_identity row index that the user wishes to unlink:
        $qmwp_identity_row = $_POST['qmwp_identity_row']; // SANITIZED via $wpdb->prepare()
        // get the current user:
        global $current_user;
        get_currentuserinfo();
        $user_id = $current_user->ID;
        // delete the qmwp_identity record from the wp_usermeta table:
        global $wpdb;
        $usermeta_table = $wpdb->usermeta;
        $query_string = $wpdb->prepare("DELETE FROM $usermeta_table WHERE $usermeta_table.user_id = $user_id AND $usermeta_table.meta_key = 'qmwp_identity' AND $usermeta_table.umeta_id = %d", $qmwp_identity_row);
        $query_result = $wpdb->query($query_string);
        // notify client of the result;
        if ($query_result) {
            echo json_encode(array('result' => 1));
        } else {
            echo json_encode(array('result' => 0));
        }
        // wp-ajax requires death:
        die();
    }

    /**
     * tries to retrieve or refresh and retrieve access_token
     * @return mixed|null
     */
    function access_token()
    {
        $user_id = get_current_user_id();
        $tokenValidityTime = get_user_meta($user_id, 'qmwp_token_expires_at', true);

        if ($tokenValidityTime != "" && time() < $tokenValidityTime) {
            return get_user_meta($user_id, 'qmwp_access_token', true);
        } elseif ($tokenValidityTime != "" && time() >= $tokenValidityTime) {
            $refreshToken = get_user_meta($user_id, 'qmwp_refresh_token', true);
            $authenticator = new QMWPAuth();
            $authenticator->refresh_oauth_token($this, $refreshToken);
            $this->update_user_tokens($user_id);
            return get_user_meta($user_id, 'qmwp_access_token', true);
        } else {
            return null;
        }
    }

    /**
     *  pushes login messages into the dom where they can be extracted by javascript
     */
    function qmwp_push_login_messages()
    {
        $result = $_SESSION['QMWP']['RESULT'];
        $_SESSION['QMWP']['RESULT'] = '';
        echo "<div id='qm-result'>" . $result . "</div>";
    }

    /**
     *  clears the login state
     */
    function qmwp_clear_login_state()
    {
        unset($_SESSION["QMWP"]["USER_ID"]);
        unset($_SESSION["QMWP"]["USER_EMAIL"]);
        unset($_SESSION["QMWP"]["ACCESS_TOKEN"]);
        unset($_SESSION["QMWP"]["EXPIRES_IN"]);
        unset($_SESSION["QMWP"]["EXPIRES_AT"]);
        unset($_SESSION["QM"]["REFRESH_TOKEN"]);
        //unset($_SESSION["QMWP"]["LAST_URL"]);
    }

    // ===================================
    // DEFAULT LOGIN SCREEN CUSTOMIZATIONS
    // ===================================

    /**
     * force the login screen logo to point to the site instead of wordpress.org
     * @return string|void
     */
    function qmwp_logo_link()
    {
        return get_bloginfo('url');
    }

    /**
     *  show a custom login form on the default login screen
     */
    function qmwp_customize_login_screen($message)
    {
        if (empty($message)) {
            $html = "";
            $design = get_option('qmwp_login_form_show_login_screen');
            if ($design != "None") {
                // TODO: we need to use $settings defaults here, not hard-coded defaults...
                $html .= $this->qmwp_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
            }
            return $html;
        } else {
            return $message;
        }
    }


    /**
     * show a custom login form at the top of the default comment form
     * @param $fields
     * @return mixed
     */
    function qmwp_customize_comment_form_fields($fields)
    {
        $html = "";
        $design = get_option('qmwp_login_form_show_comments_section');
        if ($design != "None") {
            // TODO: we need to use $settings defaults here, not hard-coded defaults...
            $html .= $this->qmwp_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
            $fields['logged_in_as'] = $html;
        }
        return $fields;
    }

    /**
     *  show a custom login form at the top of the default comment form
     */
    function qmwp_customize_comment_form()
    {
        $html = "";
        $design = get_option('qmwp_login_form_show_comments_section');
        if ($design != "None") {
            // TODO: we need to use $settings defaults here, not hard-coded defaults...
            $html .= $this->qmwp_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
        }
        echo $html;
    }

    // =========================
    // LOGIN / LOGOUT COMPONENTS
    // =========================

    /**
     * shortcode which allows adding the qmwp login form to any post or page
     * @param $atts
     * @return string
     */
    function qmwp_login_form($atts)
    {
        $a = shortcode_atts(array(
            'design' => '',
            'icon_set' => 'none',
            'button_prefix' => '',
            'layout' => 'links-column',
            'align' => 'left',
            'show_login' => 'conditional',
            'show_logout' => 'conditional',
            'logged_out_title' => 'Please login:',
            'logged_in_title' => 'You are already logged in.',
            'logging_in_title' => 'Logging in...',
            'logging_out_title' => 'Logging out...',
            'style' => '',
            'class' => '',
        ), $atts);
        // convert attribute strings to proper data types:
        //$a['show_login'] = filter_var($a['show_login'], FILTER_VALIDATE_BOOLEAN);
        //$a['show_logout'] = filter_var($a['show_logout'], FILTER_VALIDATE_BOOLEAN);
        // get the shortcode content:
        $html = $this->qmwp_login_form_content($a['design'], $a['icon_set'], $a['layout'], $a['button_prefix'], $a['align'], $a['show_login'], $a['show_logout'], $a['logged_out_title'], $a['logged_in_title'], $a['logging_in_title'], $a['logging_out_title'], $a['style'], $a['class']);
        return $html;
    }


    /**
     * gets the content to be used for displaying the login/logout form
     *
     * @param string $design
     * @param string $icon_set
     * @param string $layout
     * @param string $button_prefix
     * @param string $align
     * @param string $show_login
     * @param string $show_logout
     * @param string $logged_out_title
     * @param string $logged_in_title
     * @param string $logging_in_title
     * @param string $logging_out_title
     * @param string $style
     * @param string $class
     * @return string
     */
    function qmwp_login_form_content($design = '', $icon_set = 'icon_set', $layout = 'links-column', $button_prefix = '', $align = 'left', $show_login = 'conditional', $show_logout = 'conditional', $logged_out_title = 'Please login:', $logged_in_title = 'You are already logged in.', $logging_in_title = 'Logging in...', $logging_out_title = 'Logging out...', $style = '', $class = '')
    { // even though qmwp_login_form() will pass a default, we might call this function from another method so it's important to re-specify the default values
        // if a design was specified and that design exists, load the shortcode attributes from that design:
        if ($design != '' && QMWP::qmwp_login_form_design_exists($design)) { // TODO: remove first condition not needed
            $a = QMWP::qmwp_get_login_form_design($design);
            $icon_set = $a['icon_set'];
            $layout = $a['layout'];
            $button_prefix = $a['button_prefix'];
            $align = $a['align'];
            $show_login = $a['show_login'];
            $show_logout = $a['show_logout'];
            $logged_out_title = $a['logged_out_title'];
            $logged_in_title = $a['logged_in_title'];
            $logging_in_title = $a['logging_in_title'];
            $logging_out_title = $a['logging_out_title'];
            $style = $a['style'];
            $class = $a['class'];
        }
        // build the shortcode markup:
        $html = "";
        $html .= "<div class='qmwp-login-form qmwp-layout-$layout qmwp-layout-align-$align $class' style='$style' data-logging-in-title='$logging_in_title' data-logging-out-title='$logging_out_title'>";
        $html .= "<nav>";
        if (is_user_logged_in()) {
            if ($logged_in_title) {
                $html .= "<p id='qmwp-title'>" . $logged_in_title . "</p>";
            }
            if ($show_login == 'always') {
                $html .= $this->qmwp_login_buttons($icon_set, $button_prefix);
            }
            if ($show_logout == 'always' || $show_logout == 'conditional') {
                $html .= "<a class='qmwp-logout-button' href='" . wp_logout_url() . "' title='Logout'>Logout</a>";
            }
        } else {
            if ($logged_out_title) {
                $html .= "<p id='qmwp-title'>" . $logged_out_title . "</p>";
            }
            if ($show_login == 'always' || $show_login == 'conditional') {
                $html .= $this->qmwp_login_buttons($icon_set, $button_prefix);
            }
            if ($show_logout == 'always') {
                $html .= "<a class='qmwp-logout-button' href='" . wp_logout_url() . "' title='Logout'>Logout</a>";
            }
        }
        $html .= "</nav>";
        $html .= "</div>";
        return $html;
    }

    /**
     * generate and return the login buttons, depending on available providers
     *
     * @param $icon_set
     * @param $button_prefix
     * @return string
     */
    function qmwp_login_buttons($icon_set, $button_prefix)
    {
        // generate the atts once (cache them), so we can use it for all buttons without computing them each time:
        $site_url = get_bloginfo('url');
        $redirect_to = isset($_GET['redirect_to']) ? urlencode($_GET['redirect_to']) : null;
        if ($redirect_to) {
            $redirect_to = "&redirect_to=" . $redirect_to;
        }
        // get shortcode atts that determine how we should build these buttons:
        $icon_set_path = plugins_url('icons/' . $icon_set . '/', __FILE__);
        $atts = array(
            'site_url' => $site_url,
            'redirect_to' => $redirect_to,
            'icon_set' => $icon_set,
            'icon_set_path' => $icon_set_path,
            'button_prefix' => $button_prefix,
        );
        // generate the login buttons for available providers:
        // TODO: don't hard-code the buttons/providers here, we want to be able to add more providers without having to update this function...
        $html = "";
        $html .= $this->qmwp_login_button("google", "Google", $atts);
        $html .= $this->qmwp_login_button("quantimodo", "QuantiModo", $atts);
        $html .= $this->qmwp_login_button("facebook", "Facebook", $atts);
        $html .= $this->qmwp_login_button("linkedin", "LinkedIn", $atts);
        $html .= $this->qmwp_login_button("github", "GitHub", $atts);
        $html .= $this->qmwp_login_button("reddit", "Reddit", $atts);
        $html .= $this->qmwp_login_button("windowslive", "Windows Live", $atts);
        $html .= $this->qmwp_login_button("paypal", "PayPal", $atts);
        $html .= $this->qmwp_login_button("instagram", "Instagram", $atts);
        $html .= $this->qmwp_login_button("battlenet", "Battlenet", $atts);
        if ($html == '') {
            $html .= 'Sorry, no login providers have been enabled.';
        }
        return $html;
    }

    /**
     * generates and returns a login button for a specific provider
     * @param $provider
     * @param $display_name
     * @param $atts
     * @return string
     */
    function qmwp_login_button($provider, $display_name, $atts)
    {
        $html = "";
        if (get_option("qmwp_" . $provider . "_api_enabled")) {
            $html .= "<a id='qmwp-login-" . $provider . "' class='qmwp-login-button' href='" . $atts['site_url'] . "?connect=" . $provider . $atts['redirect_to'] . "'>";
            if ($atts['icon_set'] != 'none') {
                $html .= "<img src='" . $atts['icon_set_path'] . $provider . ".png' alt='" . $display_name . "' class='icon'></img>";
            }
            $html .= $atts['button_prefix'] . " " . $display_name;
            $html .= "</a>";
        }
        return $html;
    }

    /**
     * output the custom login form design selector
     * @param string $id
     * @param bool|false $master
     * @return string
     */
    function qmwp_login_form_designs_selector($id = '', $master = false)
    {
        $html = "";
        $designs_json = get_option('qmwp_login_form_designs');
        $designs_array = json_decode($designs_json);
        $name = str_replace('-', '_', $id);
        $html .= "<select id='" . $id . "' name='" . $name . "'>";
        if ($master == true) {
            foreach ($designs_array as $key => $val) {
                $html .= "<option value=''>" . $key . "</option>";
            }
            $html .= "</select>";
            $html .= "<input type='hidden' id='qmwp-login-form-designs' name='qmwp_login_form_designs' value='" . $designs_json . "'>";
        } else {
            $html .= "<option value='None'>" . 'None' . "</option>";
            foreach ($designs_array as $key => $val) {
                $html .= "<option value='" . $key . "' " . selected(get_option($name), $key, false) . ">" . $key . "</option>";
            }
            $html .= "</select>";
        }
        return $html;
    }

    /**
     * returns a saved login form design as a shortcode atts string or array for direct use via the shortcode
     * @param $design_name
     * @param bool|false $as_string
     * @return mixed|string|void
     */
    function qmwp_get_login_form_design($design_name, $as_string = false)
    {
        $designs_json = get_option('qmwp_login_form_designs');
        $designs_array = json_decode($designs_json, true);
        foreach ($designs_array as $key => $val) {
            if ($design_name == $key) {
                $found = $val;
                break;
            }
        }

        //echo print_r($found);
        if ($found) {
            if ($as_string) {
                $atts = json_encode($found);
            } else {
                $atts = $found;
            }
        }
        return $atts;
    }

    /**
     * @param $design_name
     * @return bool
     */
    function qmwp_login_form_design_exists($design_name)
    {
        $designs_json = get_option('qmwp_login_form_designs');
        $designs_array = json_decode($designs_json, true);
        foreach ($designs_array as $key => $val) {
            if ($design_name == $key) {
                $found = $val;
                break;
            }
        }
        if ($found) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  shows the user's linked providers, used on the 'Your Profile' page
     */
    function qmwp_linked_accounts()
    {
        include_once('includes/modules/linked-accounts.php');
    }

    // ====================
    // PLUGIN SETTINGS PAGE
    // ====================

    /**
     *  registers all settings that have been defined at the top of the plugin
     */
    function qmwp_register_settings()
    {
        foreach ($this->settings as $setting_name => $default_value) {
            register_setting('qmwp_settings', $setting_name);
        }
    }

    /**
     *  add the main settings page
     */
    function qmwp_settings_page()
    {
        add_options_page('QuantiModo Options', 'QuantiModo', 'manage_options', 'QuantiModo', array($this, 'qmwp_settings_page_content'));
    }

    /**
     *  render the main settings page content
     */
    function qmwp_settings_page_content()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        $blog_url = rtrim(site_url(), "/") . "/";
        include_once 'includes/qmwp-settings.php';
    }

    /**
     * @param $user_id
     */
    private function update_user_tokens($user_id)
    {
        update_user_meta($user_id, 'qmwp_access_token', $_SESSION['QMWP']['ACCESS_TOKEN']);
        update_user_meta($user_id, 'qmwp_refresh_token', $_SESSION['QMWP']['REFRESH_TOKEN']);
        update_user_meta($user_id, 'qmwp_token_expires_at', $_SESSION['QMWP']['EXPIRES_AT']);
    }

    // ====================
    // HELPER FUNCTIONS
    // ====================

    /**
     * Adds script tag to an html string with variables definition
     * variables should be passed as an associative array
     * Example:
     * set_js_variables('<div></div>', array(foo => 'bar'));
     * will return:
     * <div><script> var foo = 'bar'; <script></div>
     *
     * @param $templateContent
     * @param $variables
     * @return string
     */
    private function set_js_variables($templateContent, $variables)
    {
        if (count($variables) > 0) {
            $scriptHtmlString = '<script id="qmwp-service-variables" type="application/javascript">';
            foreach ($variables as $variableName => $variableValue) {
                if (!is_null($variableValue)) {
                    $scriptHtmlString .= "var $variableName = '" . $variableValue . "';";
                } else {
                    $scriptHtmlString .= "var $variableName = null;";
                }
                $scriptHtmlString .= "\n";
            }
            $scriptHtmlString .= '</script>';

            return $scriptHtmlString . $templateContent;
        }
    }

    /**
     * Picks up plugin content template, renders it and return string with HTML
     * @param $shortCodeName - shortCode name
     * @param $version - template version to load
     * @return string - rendered template HTML string
     */
    private function get_plugin_template_html($shortCodeName, $version)
    {
        ob_start();
        include('includes/' . $shortCodeName . '/' . $shortCodeName . '-v' . $version . '.php');
        $templateContent = ob_get_contents();
        ob_end_clean();

        return $templateContent;
    }

    /**
     * Will add script with JS alert if variable is null or undefined
     *
     * call: add_null_variable_alert(array(false => 'Ops!'), '<div></div>');
     * will return: <div><script>alert("Ops!");</script></div>
     */
    private function add_null_global_variable_alerts($variables, $templateContent)
    {
        $alertsHtmlString = "<script id='null-global-vars-alerts'>\n";
        foreach ($variables as $value => $message) {

            if (empty($value)) {
                $alertsHtmlString .= "alert('" . $message . "');\n";
            }

        }
        $alertsHtmlString .= "</script>\n";

        return $alertsHtmlString .= $templateContent;

    }

    /**
     * Do some decoration of a template before returning HTML
     * Currently it sets global JS variables anb adds an alerts
     *
     * @param $template
     * @return string
     */
    private function process_template($template)
    {

        $access_token = $this->access_token();

        $template_content = $this->set_js_variables($template, array(
            'accessToken' => $access_token,
            'apiHost' => QMWPAuth::API_HOST,
            'mashapeKey' => get_option('qmwp_x_mashape_key')    //from settings
        ));

        $template_content = $this->add_null_global_variable_alerts(array(
            get_option('qmwp_x_mashape_key') =>
                'Please go to:\nhttps://market.mashape.com/quantimodo/quantimodo\n' .
                'and sign up to get an X-Mashape-Key.\nThen enter it in:\n' .
                $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] .
                '/wp-admin/options-general.php?page=QuantiModo'
        ), $template_content);

        return $template_content;

    }

    /**
     * Adds page for current WP instance with given title and content string
     * @param $pageTitle
     * @param $pageContent
     */
    private function create_page($pageTitle, $pageContent)
    {
        $page = array(
            'post_title' => $pageTitle,
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'page',
            'post_name' => $pageTitle,
            'post_content' => $pageContent
        );

        // Insert the post into the database
        wp_insert_post($page);
    }

    /**
     * Creates plugin related posts and pages
     * @param $pages array
     */
    private function create_plugin_pages($pages)
    {

        foreach ($pages as $pageTitle => $pageContent) {
            if (is_null(get_page_by_title($pageTitle))) {
                $this->create_page($pageTitle, $pageContent);
            }
        }

    }

    /**
     * Deletes plugin related posts or pages
     * @param $pages array
     */
    private function delete_plugin_pages($pages)
    {

        foreach ($pages as $pageTitle => $pageContent) {
            $page = get_page_by_title($pageTitle);
            if (!is_null($page)) {
                wp_delete_post($page->ID, true);
            }
        }

    }

    // ====================
    // PLUGIN SHORT CODES
    // ====================

    /**
     * Return rendered html string with plugin content
     * @param $attributes
     * @return string
     */
    function qmwp_mood_tracker($attributes)
    {
        $attributes = shortcode_atts(array('version' => 1), $attributes, 'qmwp_mood_tracker');

        $version = $attributes['version'];

        $pluginContentHTML = $this->get_plugin_template_html('qmwp-mood-tracker', $version);

        $template_content = $this->process_template($pluginContentHTML);

        return $template_content;
    }

    /**
     * Renders QuantiModo connectors plugin content
     *
     * @param $attributes - short code attributes
     * @return string
     */
    function qmwp_connectors($attributes)
    {
        $attributes = shortcode_atts(array('version' => 3), $attributes, 'qmwp_connectors');

        $version = $attributes['version'];

        $pluginContentHTML = $this->get_plugin_template_html('qmwp-connectors', $version);

        $template_content = $this->process_template($pluginContentHTML);

        return $template_content;

    }

    /**
     * Renders QuantiModo manage accounts plugin content
     * @param $attributes
     * @return string
     */
    function qmwp_manage_accounts($attributes)
    {
        $attributes = shortcode_atts(array('version' => 2), $attributes, 'qmwp_manage_accounts');

        $version = $attributes['version'];

        $pluginContentHTML = $this->get_plugin_template_html('qmwp-manage-accounts', $version);

        $template_content = $this->process_template($pluginContentHTML);

        return $template_content;
    }

    /**
     * Renders QuantiModo bargraph_scatterplot_timeline shortcode content
     * @param $attributes
     * @return string
     */
    function qmwp_bargraph_scatterplot_timeline($attributes)
    {
        $attributes = shortcode_atts(array('version' => 1), $attributes, 'qmwp_bargraph_scatterplot_timeline');

        $version = $attributes['version'];

        $pluginContentHTML = $this->get_plugin_template_html('qmwp-bargraph-scatterplot-timeline', $version);

        $template_content = $this->process_template($pluginContentHTML);

        return $template_content;

    }

    /**
     * Renders QuantiModo analyze plugin content
     * @param $attributes
     * @return string
     */
    function qmwp_timeline($attributes)
    {
        $attributes = shortcode_atts(array('version' => 1), $attributes, 'qmwp_timeline');

        $version = $attributes['version'];

        $pluginContentHTML = $this->get_plugin_template_html('qmwp-timeline', $version);

        $template_content = $this->process_template($pluginContentHTML);

        return $template_content;

    }

    /**
     * Return rendered html string with plugin content
     * @param $attributes
     * @return string
     */
    function qmwp_search_correlations($attributes)
    {
        $attributes = shortcode_atts(array('version' => 1), $attributes, 'qmwp_search_correlations');

        $version = $attributes['version'];

        $pluginContentHTML = $this->get_plugin_template_html('qmwp-search-correlations', $version);

        $template_content = $this->process_template($pluginContentHTML);

        return $template_content;
    }

    /**
     * Return rendered html string with plugin content
     * @param $attributes
     * @return string
     */
    function qmwp_add_measurement($attributes)
    {
        $attributes = shortcode_atts(array('version' => 1), $attributes, 'qmwp_add_measurement');

        $version = $attributes['version'];

        $pluginContentHTML = $this->get_plugin_template_html('qmwp-add-measurement', $version);

        $template_content = $this->process_template($pluginContentHTML);

        return $template_content;
    }

}

$GLOBALS['QuantiModo'] = new QMWP();

QMWP::get_instance();






