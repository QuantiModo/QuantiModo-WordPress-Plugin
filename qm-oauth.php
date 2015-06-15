<?php

/*
Plugin Name: QM-OAuth
Plugin URI: http://github.com/QuantiModo/qm-oauth
Description: A WordPress plugin that allows users to login or register by authenticating with an existing Quantimodo account via OAuth 2.0. Easily drops into new or existing sites, integrates with existing users.
Version: 0.1
Author: QuantiModo
Author URI: http://quantimo.do
License: GPL2
*/

// start the user session for persisting user/login state during ajax, header redirect, and cross domain calls:
session_start();

// plugin class:
Class WPOA {

	// ==============
	// INITIALIZATION
	// ==============

	// set a version that we can use for performing plugin updates, this should always match the plugin version:
	const PLUGIN_VERSION = "0.4";
	
	// singleton class pattern:
	protected static $instance = NULL;
	public static function get_instance() {
		NULL === self::$instance and self::$instance = new self;
		return self::$instance;
	}

	// define the settings used by this plugin; this array will be used for registering settings, applying default values, and deleting them during uninstall:
	private $settings = array(
		'qmoa_show_login_messages' => 0,								// 0, 1
		'qmoa_login_redirect' => 'home_page',							// home_page, last_page, specific_page, admin_dashboard, profile_page, custom_url
		'qmoa_login_redirect_page' => 0,								// any whole number (wordpress page id)
		'qmoa_login_redirect_url' => '',								// any string (url)
		'qmoa_logout_redirect' => 'home_page',							// home_page, last_page, specific_page, admin_dashboard, profile_page, custom_url, default_handling
		'qmoa_logout_redirect_page' => 0,								// any whole number (wordpress page id)
		'qmoa_logout_redirect_url' => '',								// any string (url)
		'qmoa_logout_inactive_users' => 0,								// any whole number (minutes)
		'qmoa_hide_wordpress_login_form' => 0,							// 0, 1
		'qmoa_logo_links_to_site' => 0,									// 0, 1
		'qmoa_logo_image' => '',										// any string (image url)
		'qmoa_bg_image' => '',											// any string (image url)
		'qmoa_login_form_show_login_screen' => 'Login Screen',			// any string (name of a custom login form shortcode design)
		'qmoa_login_form_show_profile_page' => 'Profile Page',			// any string (name of a custom login form shortcode design)
		'qmoa_login_form_show_comments_section' => 'None',				// any string (name of a custom login form shortcode design)
		'qmoa_login_form_designs' => array(								// array of shortcode designs to be included by default; same array signature as the shortcode function uses
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
		'qmoa_suppress_welcome_email' => 0,								// 0, 1
		'qmoa_new_user_role' => 'contributor',							// role
		'qmoa_google_api_enabled' => 0,									// 0, 1
		'qmoa_google_api_id' => '',										// any string
		'qmoa_google_api_secret' => '',									// any string
        'qmoa_quantimodo_api_enabled' => 0,									// 0, 1
        'qmoa_quantimodo_api_id' => '',										// any string
        'qmoa_quantimodo_api_secret' => '',									// any string
		'qmoa_facebook_api_enabled' => 0,								// 0, 1
		'qmoa_facebook_api_id' => '',									// any string
		'qmoa_facebook_api_secret' => '',								// any string
		'qmoa_linkedin_api_enabled' => 0,								// 0, 1
		'qmoa_linkedin_api_id' => '',									// any string
		'qmoa_linkedin_api_secret' => '',								// any string
		'qmoa_github_api_enabled' => 0,									// 0, 1
		'qmoa_github_api_id' => '',										// any string
		'qmoa_github_api_secret' => '',									// any string
		'qmoa_reddit_api_enabled' => 0,									// 0, 1
		'qmoa_reddit_api_id' => '',										// any string
		'qmoa_reddit_api_secret' => '',									// any string
		'qmoa_windowslive_api_enabled' => 0,							// 0, 1
		'qmoa_windowslive_api_id' => '',								// any string
		'qmoa_windowslive_api_secret' => '',							// any string
		'qmoa_paypal_api_enabled' => 0,									// 0, 1
		'qmoa_paypal_api_id' => '',										// any string
		'qmoa_paypal_api_secret' => '',									// any string
		'qmoa_paypal_api_sandbox_mode' => 0,							// 0, 1
		'qmoa_instagram_api_enabled' => 0,								// 0, 1
		'qmoa_instagram_api_id' => '',									// any string
		'qmoa_instagram_api_secret' => '',								// any string
		'qmoa_battlenet_api_enabled' => 0,								// 0, 1
		'qmoa_battlenet_api_id' => '',									// any string
		'qmoa_battlenet_api_secret' => '',								// any string
		'qmoa_http_util' => 'curl',										// curl, stream-context
		'qmoa_http_util_verify_ssl' => 1,								// 0, 1
		'qmoa_restore_default_settings' => 0,							// 0, 1
		'qmoa_delete_settings_on_uninstall' => 0,						// 0, 1
	);
	
	// when the plugin class gets created, fire the initialization:
	function __construct() {
		// hook activation and deactivation for the plugin:
		register_activation_hook(__FILE__, array($this, 'qmoa_activate'));
		register_deactivation_hook(__FILE__, array($this, 'qmoa_deactivate'));
		// hook load event to handle any plugin updates:
		add_action('plugins_loaded', array($this, 'qmoa_update'));
		// hook init event to handle plugin initialization:
		add_action('init', array($this, 'init'));
	}
	
	// a wrapper for wordpress' get_option(), this basically feeds get_option() the setting's correct default value as specified at the top of this file:
	/*
	function qmoa_option($name) {
		// TODO: create the option with a default value if it doesn't exist?
		$val = get_option($name, $settings[$name]);
		return $val;
	}
	*/
	
	// do something during plugin activation:
	function qmoa_activate() {
	}
	
	// do something during plugin deactivation:
	function qmoa_deactivate() {
	}
	
	// do something during plugin update:
	function qmoa_update() {
		$plugin_version = WPOA::PLUGIN_VERSION;
		$installed_version = get_option("qmoa_plugin_version");
		if (!$installed_version || $installed_version <= 0 || $installed_version != $plugin_version) {
			// version mismatch, run the update logic...
			// add any missing options and set a default (usable) value:
			$this->qmoa_add_missing_settings();
			// set the new version so we don't trigger the update again:
			update_option("qmoa_plugin_version", $plugin_version);
			// create an admin notice:
			add_action('admin_notices', array($this, 'qmoa_update_notice'));
		}
	}
	
	// indicate to the admin that the plugin has been updated:
	function qmoa_update_notice() {
		$settings_link = "<a href='options-general.php?page=QM-OAuth.php'>Settings Page</a>"; // CASE SeNsItIvE filename!
		?>
		<div class="updated">
			<p>QM-OAuth has been updated! Please review the <?php echo $settings_link ?>.</p>
		</div>
		<?php
	}
	
	// adds any missing settings and their default values:
	function qmoa_add_missing_settings() {
		foreach($this->settings as $setting_name => $default_value) {
			// call add_option() which ensures that we only add NEW options that don't exist:
			if (is_array($this->settings[$setting_name])) {
				$default_value = json_encode($default_value);
			}
			$added = add_option($setting_name, $default_value);
		}
	}
	
	// restores the default plugin settings:
	function qmoa_restore_default_settings() {
		foreach($this->settings as $setting_name => $default_value) {
			// call update_option() which ensures that we update the setting's value:
			if (is_array($this->settings[$setting_name])) {
				$default_value = json_encode($default_value);
			}
			update_option($setting_name, $default_value);
		}
		add_action('admin_notices', array($this, 'qmoa_restore_default_settings_notice'));
	}
	
	// indicate to the admin that the plugin has been updated:
	function qmoa_restore_default_settings_notice() {
		$settings_link = "<a href='options-general.php?page=QM-OAuth.php'>Settings Page</a>"; // CASE SeNsItIvE filename!
		?>
		<div class="updated">
			<p>The default settings have been restored. You may review the <?php echo $settings_link ?>.</p>
		</div>
		<?php
	}

	// initialize the plugin's functionality by hooking into wordpress:
	function init() {
		// restore default settings if necessary; this might get toggled by the admin or forced by a new version of the plugin:
		if (get_option("qmoa_restore_default_settings")) {$this->qmoa_restore_default_settings();}
		// hook the query_vars and template_redirect so we can stay within the wordpress context no matter what (avoids having to use wp-load.php)
		add_filter('query_vars', array($this, 'qmoa_qvar_triggers'));
		add_action('template_redirect', array($this, 'qmoa_qvar_handlers'));
		// hook scripts and styles for frontend pages:
		add_action('wp_enqueue_scripts', array($this, 'qmoa_init_frontend_scripts_styles'));
		// hook scripts and styles for backend pages:
		add_action('admin_enqueue_scripts', array($this, 'qmoa_init_backend_scripts_styles'));
		add_action('admin_menu', array($this, 'qmoa_settings_page'));
		add_action('admin_init', array($this, 'qmoa_register_settings'));
		$plugin = plugin_basename(__FILE__);
		add_filter("plugin_action_links_$plugin", array($this, 'qmoa_settings_link'));
		// hook scripts and styles for login page:
		add_action('login_enqueue_scripts', array($this, 'qmoa_init_login_scripts_styles'));
		if (get_option('qmoa_logo_links_to_site') == true) {add_filter('login_headerurl', array($this, 'qmoa_logo_link'));}
		add_filter('login_message', array($this, 'qmoa_customize_login_screen'));
		// hooks used globally:
		add_filter('comment_form_defaults', array($this, 'qmoa_customize_comment_form_fields'));
		//add_action('comment_form_top', array($this, 'qmoa_customize_comment_form'));
		add_action('show_user_profile', array($this, 'qmoa_linked_accounts'));
		add_action('wp_logout', array($this, 'qmoa_end_logout'));
		add_action('wp_ajax_qmoa_logout', array($this, 'qmoa_logout_user'));
		add_action('wp_ajax_qmoa_unlink_account', array($this, 'qmoa_unlink_account'));
		add_action('wp_ajax_nopriv_qmoa_unlink_account', array($this, 'qmoa_unlink_account'));
		add_shortcode('qmoa_login_form', array($this, 'qmoa_login_form'));
		// push login messages into the DOM if the setting is enabled:
		if (get_option('qmoa_show_login_messages') !== false) {
			add_action('wp_footer', array($this, 'qmoa_push_login_messages'));
			add_filter('admin_footer', array($this, 'qmoa_push_login_messages'));
			add_filter('login_footer', array($this, 'qmoa_push_login_messages'));
		}
	}
	
	// init scripts and styles for use on FRONTEND PAGES:
	function qmoa_init_frontend_scripts_styles() {
		// here we "localize" php variables, making them available as a js variable in the browser:
		$qmoa_cvars = array(
			// basic info:
			'ajaxurl' => admin_url('admin-ajax.php'),
			'template_directory' => get_bloginfo('template_directory'),
			'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
			'plugins_url' => plugins_url(),
			'plugin_dir_url' => plugin_dir_url(__FILE__),
			'url' => get_bloginfo('url'),
			'logout_url' => wp_logout_url(),
			// other:
			'show_login_messages' => get_option('qmoa_show_login_messages'),
			'logout_inactive_users' => get_option('qmoa_logout_inactive_users'),
			'logged_in' => is_user_logged_in(),
		);
		wp_enqueue_script('qmoa-cvars', plugins_url('/cvars.js', __FILE__));
		wp_localize_script('qmoa-cvars', 'qmoa_cvars', $qmoa_cvars);
		// we always need jquery:
		wp_enqueue_script('jquery');
		// load the core plugin scripts/styles:
		wp_enqueue_script('qmoa-script', plugin_dir_url( __FILE__ ) . 'qm-oauth.js', array());
		wp_enqueue_style('qmoa-style', plugin_dir_url( __FILE__ ) . 'qm-oauth.css', array());
	}
	
	// init scripts and styles for use on BACKEND PAGES:
	function qmoa_init_backend_scripts_styles() {
		// here we "localize" php variables, making them available as a js variable in the browser:
		$qmoa_cvars = array(
			// basic info:
			'ajaxurl' => admin_url('admin-ajax.php'),
			'template_directory' => get_bloginfo('template_directory'),
			'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
			'plugins_url' => plugins_url(),
			'plugin_dir_url' => plugin_dir_url(__FILE__),
			'url' => get_bloginfo('url'),
			// other:
			'show_login_messages' => get_option('qmoa_show_login_messages'),
			'logout_inactive_users' => get_option('qmoa_logout_inactive_users'),
			'logged_in' => is_user_logged_in(),
		);
		wp_enqueue_script('qmoa-cvars', plugins_url('/cvars.js', __FILE__));
		wp_localize_script('qmoa-cvars', 'qmoa_cvars', $qmoa_cvars);
		// we always need jquery:
		wp_enqueue_script('jquery');
		// load the core plugin scripts/styles:
		wp_enqueue_script('qmoa-script', plugin_dir_url( __FILE__ ) . 'qm-oauth.js', array());
		wp_enqueue_style('qmoa-style', plugin_dir_url( __FILE__ ) . 'qm-oauth.css', array());
		// load the default wordpress media screen:
		wp_enqueue_media();
	}
	
	// init scripts and styles for use on the LOGIN PAGE:
	function qmoa_init_login_scripts_styles() {
		// here we "localize" php variables, making them available as a js variable in the browser:
		$qmoa_cvars = array(
			// basic info:
			'ajaxurl' => admin_url('admin-ajax.php'),
			'template_directory' => get_bloginfo('template_directory'),
			'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
			'plugins_url' => plugins_url(),
			'plugin_dir_url' => plugin_dir_url(__FILE__),
			'url' => get_bloginfo('url'),
			// login specific:
			'hide_login_form' => get_option('qmoa_hide_wordpress_login_form'),
			'logo_image' => get_option('qmoa_logo_image'),
			'bg_image' => get_option('qmoa_bg_image'),
			'login_message' => $_SESSION['WPOA']['RESULT'],
			'show_login_messages' => get_option('qmoa_show_login_messages'),
			'logout_inactive_users' => get_option('qmoa_logout_inactive_users'),
			'logged_in' => is_user_logged_in(),
		);
		wp_enqueue_script('qmoa-cvars', plugins_url('/cvars.js', __FILE__));
		wp_localize_script('qmoa-cvars', 'qmoa_cvars', $qmoa_cvars);
		// we always need jquery:
		wp_enqueue_script('jquery');
		// load the core plugin scripts/styles:
		wp_enqueue_script('qmoa-script', plugin_dir_url( __FILE__ ) . 'qm-oauth.js', array());
		wp_enqueue_style('qmoa-style', plugin_dir_url( __FILE__ ) . 'qm-oauth.css', array());
	}
	
	// add a settings link to the plugins page:
	function qmoa_settings_link($links) {
		$settings_link = "<a href='options-general.php?page=QM-OAuth.php'>Settings</a>"; // CASE SeNsItIvE filename!
		array_unshift($links, $settings_link); 
		return $links; 
	}
	
	// ===============
	// GENERIC HELPERS
	// ===============
	
	// adds basic http auth to a given url string:
	function qmoa_add_basic_auth($url, $username, $password) {
		$url = str_replace("https://", "", $url);
		$url = "https://" . $username . ":" . $password . "@" . $url;
		return $url;
	}
	
	// ===================
	// LOGIN FLOW HANDLING
	// ===================

	// define the querystring variables that should trigger an action:
	function qmoa_qvar_triggers($vars) {
		$vars[] = 'connect';
		$vars[] = 'code';
		$vars[] = 'error_description';
		$vars[] = 'error_message';
		return $vars;
	}
	
	// handle the querystring triggers:
	function qmoa_qvar_handlers() {
		if (get_query_var('connect')) {
			$provider = get_query_var('connect');
			$this->qmoa_include_connector($provider);
		}
		elseif (get_query_var('code')) {
			$provider = $_SESSION['WPOA']['PROVIDER'];
			$this->qmoa_include_connector($provider);
		}
		elseif (get_query_var('error_description') || get_query_var('error_message')) {
			$provider = $_SESSION['WPOA']['PROVIDER'];
			$this->qmoa_include_connector($provider);
		}
	}
	
	// load the provider script that is being requested by the user or being called back after authentication:
	function qmoa_include_connector($provider) {
		// normalize the provider name (no caps, no spaces):
		$provider = strtolower($provider);
		$provider = str_replace(" ", "", $provider);
		$provider = str_replace(".", "", $provider);
		// include the provider script:
		include 'login-' . $provider . '.php';
	}
	
	// =======================
	// LOGIN / LOGOUT HANDLING
	// =======================

	// match the oauth identity to an existing wordpress user account:
	function qmoa_match_wordpress_user($oauth_identity) {
		// attempt to get a wordpress user id from the database that matches the $oauth_identity['id'] value:
		global $wpdb;
		$usermeta_table = $wpdb->usermeta;
		$query_string = "SELECT $usermeta_table.user_id FROM $usermeta_table WHERE $usermeta_table.meta_key = 'qmoa_identity' AND $usermeta_table.meta_value LIKE '%" . $oauth_identity['provider'] . "|" . $oauth_identity['id'] . "%'";
		$query_result = $wpdb->get_var($query_string);
		// attempt to get a wordpress user with the matched id:
		$user = get_user_by('id', $query_result);
		return $user;
	}
	
	// login (or register and login) a wordpress user based on their oauth identity:
	function qmoa_login_user($oauth_identity) {
		// store the user info in the user session so we can grab it later if we need to register the user:
		$_SESSION["WPOA"]["USER_ID"] = $oauth_identity["id"];
		// try to find a matching wordpress user for the now-authenticated user's oauth identity:
		$matched_user = $this->qmoa_match_wordpress_user($oauth_identity);
		// handle the matched user if there is one:
		if ( $matched_user ) {
			// there was a matching wordpress user account, log it in now:
			$user_id = $matched_user->ID;
			$user_login = $matched_user->user_login;
			wp_set_current_user( $user_id, $user_login );
			wp_set_auth_cookie( $user_id );
			do_action( 'wp_login', $user_login, $matched_user );
			// after login, redirect to the user's last location
			$this->qmoa_end_login("Logged in successfully!");
		}
		// handle the already logged in user if there is one:
		if ( is_user_logged_in() ) {
			// there was a wordpress user logged in, but it is not associated with the now-authenticated user's email address, so associate it now:
			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
			$this->qmoa_link_account($user_id);
			// after linking the account, redirect user to their last url
			$this->qmoa_end_login("Your account was linked successfully with your third party authentication provider.");
		}
		// handle the logged out user or no matching user (register the user):
		if ( !is_user_logged_in() && !$matched_user ) {
			// this person is not logged into a wordpress account and has no third party authentications registered, so proceed to register the wordpress user:
			include 'register.php';
		}
		// we shouldn't be here, but just in case...
		$this->qmoa_end_login("Sorry, we couldn't log you in. The login flow terminated in an unexpected way. Please notify the admin or try again later.");
	}
	
	// ends the login request by clearing the login state and redirecting the user to the desired page:
	function qmoa_end_login($msg) {
		$last_url = $_SESSION["WPOA"]["LAST_URL"];
		unset($_SESSION["WPOA"]["LAST_URL"]);
		$_SESSION["WPOA"]["RESULT"] = $msg;
		$this->qmoa_clear_login_state();
		$redirect_method = get_option("qmoa_login_redirect");
		$redirect_url = "";
		switch ($redirect_method) {
			case "home_page":
				$redirect_url = site_url();
				break;
			case "last_page":
				$redirect_url = $last_url;
				break;
			case "specific_page":
				$redirect_url = get_permalink(get_option('qmoa_login_redirect_page'));
				break;
			case "admin_dashboard":
				$redirect_url = admin_url();
				break;
			case "user_profile":
				$redirect_url = get_edit_user_link();
				break;
			case "custom_url":
				$redirect_url = get_option('qmoa_login_redirect_url');
				break;
		}
		//header("Location: " . $redirect_url);
		wp_safe_redirect($redirect_url);
		die();
	}
	
	// logout the wordpress user:
	// TODO: this is usually called from a custom logout button, but we could have the button call /wp-logout.php?action=logout for more consistency...
	function qmoa_logout_user() {
		// logout the user:
		$user = null; 		// nullify the user
		session_destroy(); 	// destroy the php user session
		wp_logout(); 		// logout the wordpress user...this gets hooked and diverted to qmoa_end_logout() for final handling
	}
	
	// ends the logout request by redirecting the user to the desired page:
	function qmoa_end_logout() {
		$_SESSION["WPOA"]["RESULT"] = 'Logged out successfully.';
		if (is_user_logged_in()) {
			// user is logged in and trying to logout...get their Last Page:
			$last_url = $_SERVER['HTTP_REFERER'];
		}
		else {
			// user is NOT logged in and trying to logout...get their Last Page minus the querystring so we don't trigger the logout confirmation:
			$last_url = strtok($_SERVER['HTTP_REFERER'], "?");
		}
		unset($_SESSION["WPOA"]["LAST_URL"]);
		$this->qmoa_clear_login_state();
		$redirect_method = get_option("qmoa_logout_redirect");
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
				$redirect_url = get_permalink(get_option('qmoa_logout_redirect_page'));
				break;
			case "admin_dashboard":
				$redirect_url = admin_url();
				break;
			case "user_profile":
				$redirect_url = get_edit_user_link();
				break;
			case "custom_url":
				$redirect_url = get_option('qmoa_logout_redirect_url');
				break;
		}
		//header("Location: " . $redirect_url);
		wp_safe_redirect($redirect_url);
		die();
	}
	
	// links a third-party account to an existing wordpress user account:
	function qmoa_link_account($user_id) {
		if ($_SESSION['WPOA']['USER_ID'] != '') {
			add_user_meta( $user_id, 'qmoa_identity', $_SESSION['WPOA']['PROVIDER'] . '|' . $_SESSION['WPOA']['USER_ID'] . '|' . time());
		}
	}

	// unlinks a third-party provider from an existing wordpress user account:
	function qmoa_unlink_account() {
		// get qmoa_identity row index that the user wishes to unlink:
		$qmoa_identity_row = $_POST['qmoa_identity_row']; // SANITIZED via $wpdb->prepare()
		// get the current user:
		global $current_user;
		get_currentuserinfo();
		$user_id = $current_user->ID;
		// delete the qmoa_identity record from the wp_usermeta table:
		global $wpdb;
		$usermeta_table = $wpdb->usermeta;
		$query_string = $wpdb->prepare("DELETE FROM $usermeta_table WHERE $usermeta_table.user_id = $user_id AND $usermeta_table.meta_key = 'qmoa_identity' AND $usermeta_table.umeta_id = %d", $qmoa_identity_row);
		$query_result = $wpdb->query($query_string);
		// notify client of the result;
		if ($query_result) {
			echo json_encode( array('result' => 1) );
		}
		else {
			echo json_encode( array('result' => 0) );
		}
		// wp-ajax requires death:
		die();
	}
	
	// pushes login messages into the dom where they can be extracted by javascript:
	function qmoa_push_login_messages() {
		$result = $_SESSION['WPOA']['RESULT'];
		$_SESSION['WPOA']['RESULT'] = '';
		echo "<div id='qmoa-result'>" . $result . "</div>";
	}
	
	// clears the login state:
	function qmoa_clear_login_state() {
		unset($_SESSION["WPOA"]["USER_ID"]);
		unset($_SESSION["WPOA"]["USER_EMAIL"]);
		unset($_SESSION["WPOA"]["ACCESS_TOKEN"]);
		unset($_SESSION["WPOA"]["EXPIRES_IN"]);
		unset($_SESSION["WPOA"]["EXPIRES_AT"]);
		//unset($_SESSION["WPOA"]["LAST_URL"]);
	}
	
	// ===================================
	// DEFAULT LOGIN SCREEN CUSTOMIZATIONS
	// ===================================

	// force the login screen logo to point to the site instead of wordpress.org:
	function qmoa_logo_link() {
		return get_bloginfo('url');
	}
	
	// show a custom login form on the default login screen:
	function qmoa_customize_login_screen() {
		$html = "";
		$design = get_option('qmoa_login_form_show_login_screen');
		if ($design != "None") {
			// TODO: we need to use $settings defaults here, not hard-coded defaults...
			$html .= $this->qmoa_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
		}
		echo $html;
	}

	// ===================================
	// DEFAULT COMMENT FORM CUSTOMIZATIONS
	// ===================================
	
	// show a custom login form at the top of the default comment form:
	function qmoa_customize_comment_form_fields($fields) {
		$html = "";
		$design = get_option('qmoa_login_form_show_comments_section');
		if ($design != "None") {
			// TODO: we need to use $settings defaults here, not hard-coded defaults...
			$html .= $this->qmoa_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
			$fields['logged_in_as'] = $html;
		}
		return $fields;
	}
	
	// show a custom login form at the top of the default comment form:
	function qmoa_customize_comment_form() {
		$html = "";
		$design = get_option('qmoa_login_form_show_comments_section');
		if ($design != "None") {
			// TODO: we need to use $settings defaults here, not hard-coded defaults...
			$html .= $this->qmoa_login_form_content($design, 'none', 'buttons-column', 'Connect with', 'center', 'conditional', 'conditional', 'Please login:', 'You are already logged in.', 'Logging in...', 'Logging out...');
		}
		echo $html;
	}

	// =========================
	// LOGIN / LOGOUT COMPONENTS
	// =========================
	
	// shortcode which allows adding the qmoa login form to any post or page:
	function qmoa_login_form( $atts ){
		$a = shortcode_atts( array(
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
		), $atts );
		// convert attribute strings to proper data types:
		//$a['show_login'] = filter_var($a['show_login'], FILTER_VALIDATE_BOOLEAN);
		//$a['show_logout'] = filter_var($a['show_logout'], FILTER_VALIDATE_BOOLEAN);
		// get the shortcode content:
		$html = $this->qmoa_login_form_content($a['design'], $a['icon_set'], $a['layout'], $a['button_prefix'], $a['align'], $a['show_login'], $a['show_logout'], $a['logged_out_title'], $a['logged_in_title'], $a['logging_in_title'], $a['logging_out_title'], $a['style'], $a['class']);
		return $html;
	}
	
	// gets the content to be used for displaying the login/logout form:
	function qmoa_login_form_content($design = '', $icon_set = 'icon_set', $layout = 'links-column', $button_prefix = '', $align = 'left', $show_login = 'conditional', $show_logout = 'conditional', $logged_out_title = 'Please login:', $logged_in_title = 'You are already logged in.', $logging_in_title = 'Logging in...', $logging_out_title = 'Logging out...', $style = '', $class = '') { // even though qmoa_login_form() will pass a default, we might call this function from another method so it's important to re-specify the default values
		// if a design was specified and that design exists, load the shortcode attributes from that design:
		if ($design != '' && WPOA::qmoa_login_form_design_exists($design)) { // TODO: remove first condition not needed
			$a = WPOA::qmoa_get_login_form_design($design);
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
		$html .= "<div class='qmoa-login-form qmoa-layout-$layout qmoa-layout-align-$align $class' style='$style' data-logging-in-title='$logging_in_title' data-logging-out-title='$logging_out_title'>";
		$html .= "<nav>";
		if (is_user_logged_in()) {
			if ($logged_in_title) {
				$html .= "<p id='qmoa-title'>" . $logged_in_title . "</p>";
			}
			if ($show_login == 'always') {
				$html .= $this->qmoa_login_buttons($icon_set, $button_prefix);
			}
			if ($show_logout == 'always' || $show_logout == 'conditional') {
				$html .= "<a class='qmoa-logout-button' href='" . wp_logout_url() . "' title='Logout'>Logout</a>";
			}
		}
		else {
			if ($logged_out_title) {
				$html .= "<p id='qmoa-title'>" . $logged_out_title . "</p>";
			}
			if ($show_login == 'always' || $show_login == 'conditional') {
				$html .= $this->qmoa_login_buttons($icon_set, $button_prefix);
			}
			if ($show_logout == 'always') {
				$html .= "<a class='qmoa-logout-button' href='" . wp_logout_url() . "' title='Logout'>Logout</a>";
			}
		}
		$html .= "</nav>";
		$html .= "</div>";
		return $html;
	}
	
	// generate and return the login buttons, depending on available providers:
	function qmoa_login_buttons($icon_set, $button_prefix) {
		// generate the atts once (cache them), so we can use it for all buttons without computing them each time:
		$site_url = get_bloginfo('url');
		$redirect_to = urlencode($_GET['redirect_to']);
		if ($redirect_to) {$redirect_to = "&redirect_to=" . $redirect_to;}
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
		$html .= $this->qmoa_login_button("google", "Google", $atts);
        $html .= $this->qmoa_login_button("quantimodo", "QuantiModo", $atts);
		$html .= $this->qmoa_login_button("facebook", "Facebook", $atts);
		$html .= $this->qmoa_login_button("linkedin", "LinkedIn", $atts);
		$html .= $this->qmoa_login_button("github", "GitHub", $atts);
		$html .= $this->qmoa_login_button("reddit", "Reddit", $atts);
		$html .= $this->qmoa_login_button("windowslive", "Windows Live", $atts);
		$html .= $this->qmoa_login_button("paypal", "PayPal", $atts);
		$html .= $this->qmoa_login_button("instagram", "Instagram", $atts);
		$html .= $this->qmoa_login_button("battlenet", "Battlenet", $atts);
		if ($html == '') {
			$html .= 'Sorry, no login providers have been enabled.';
		}
		return $html;
	}

	// generates and returns a login button for a specific provider:
	function qmoa_login_button($provider, $display_name, $atts) {
		$html = "";
		if (get_option("qmoa_" . $provider . "_api_enabled")) {
			$html .= "<a id='qmoa-login-" . $provider . "' class='qmoa-login-button' href='" . $atts['site_url'] . "?connect=" . $provider . $atts['redirect_to'] . "'>";
			if ($atts['icon_set'] != 'none') {
				$html .= "<img src='" . $atts['icon_set_path'] . $provider . ".png' alt='" . $display_name . "' class='icon'></img>";
			}
			$html .= $atts['button_prefix'] . " " . $display_name;
			$html .= "</a>";
		}
		return $html;
	}
	
	// output the custom login form design selector:
	function qmoa_login_form_designs_selector($id = '', $master = false) {
		$html = "";
		$designs_json = get_option('qmoa_login_form_designs');
		$designs_array = json_decode($designs_json);
		$name = str_replace('-', '_', $id);
		$html .= "<select id='" . $id . "' name='" . $name . "'>";
		if ($master == true) {
			foreach($designs_array as $key => $val) {
				$html .= "<option value=''>" . $key . "</option>";
			}
			$html .= "</select>";
			$html .= "<input type='hidden' id='qmoa-login-form-designs' name='qmoa_login_form_designs' value='" . $designs_json . "'>";
		}
		else {
			$html .= "<option value='None'>" . 'None' . "</option>";
			foreach($designs_array as $key => $val) {
				$html .= "<option value='" . $key . "' " . selected(get_option($name), $key, false) . ">" . $key . "</option>";
			}
			$html .= "</select>";
		}
		return $html;
	}
	
	// returns a saved login form design as a shortcode atts string or array for direct use via the shortcode
	function qmoa_get_login_form_design($design_name, $as_string = false) {
		$designs_json = get_option('qmoa_login_form_designs');
		$designs_array = json_decode($designs_json, true);
		foreach($designs_array as $key => $val) {
			if ($design_name == $key) {
				$found = $val;
				break;
			}
		}
		$atts;
		//echo print_r($found);
		if ($found) {
			if ($as_string) {
				$atts = json_encode($found);
			}
			else {
				$atts = $found;
			}
		}
		return $atts;
	}
	
	function qmoa_login_form_design_exists($design_name) {
		$designs_json = get_option('qmoa_login_form_designs');
		$designs_array = json_decode($designs_json, true);
		foreach($designs_array as $key => $val) {
			if ($design_name == $key) {
				$found = $val;
				break;
			}
		}
		if ($found) {
			return true;
		}
		else {
			return false;
		}
	}
	
	// shows the user's linked providers, used on the 'Your Profile' page:
	function qmoa_linked_accounts() {
		// get the current user:
		global $current_user;
		get_currentuserinfo();
		$user_id = $current_user->ID;
		// get the qmoa_identity records:
		global $wpdb;
		$usermeta_table = $wpdb->usermeta;
		$query_string = "SELECT * FROM $usermeta_table WHERE $user_id = $usermeta_table.user_id AND $usermeta_table.meta_key = 'qmoa_identity'";
		$query_result = $wpdb->get_results($query_string);
		// list the qmoa_identity records:
		echo "<div id='qmoa-linked-accounts'>";
		echo "<h3>Linked Accounts</h3>";
		echo "<p>Manage the linked accounts which you have previously authorized to be used for logging into this website.</p>";
		echo "<table class='form-table'>";
		echo "<tr valign='top'>";
		echo "<th scope='row'>Your Linked Providers</th>";
		echo "<td>";
		if ( count($query_result) == 0) {
			echo "<p>You currently don't have any accounts linked.</p>";
		}
		echo "<div class='qmoa-linked-accounts'>";
		foreach ($query_result as $qmoa_row) {
			$qmoa_identity_parts = explode('|', $qmoa_row->meta_value);
			$oauth_provider = $qmoa_identity_parts[0];
			$oauth_id = $qmoa_identity_parts[1]; // keep this private, don't send to client
			$time_linked = $qmoa_identity_parts[2];
			$local_time = strtotime("-" . $_COOKIE['gmtoffset'] . ' hours', $time_linked);
			echo "<div>" . $oauth_provider . " on " . date('F d, Y h:i A', $local_time) . " <a class='qmoa-unlink-account' data-qmoa-identity-row='" . $qmoa_row->umeta_id . "' href='#'>Unlink</a></div>";
		}
		echo "</div>";
		echo "</td>";
		echo "</tr>";
		echo "<tr valign='top'>";
		echo "<th scope='row'>Link Another Provider</th>";
		echo "<td>";
		$design = get_option('qmoa_login_form_show_profile_page');
		if ($design != "None") {
			// TODO: we need to use $settings defaults here, not hard-coded defaults...
			echo $this->qmoa_login_form_content($design, 'none', 'buttons-row', 'Link', 'left', 'always', 'never', 'Select a provider:', 'Select a provider:', 'Authenticating...', '');
		}
		echo "</div>";
		echo "</td>";
		echo "</td>";
		echo "</table>";
	}
	
	// ====================
	// PLUGIN SETTINGS PAGE
	// ====================
	
	// registers all settings that have been defined at the top of the plugin:
	function qmoa_register_settings() {
		foreach ($this->settings as $setting_name => $default_value) {
			register_setting('qmoa_settings', $setting_name);
		}
	}
	
	// add the main settings page:
	function qmoa_settings_page() {
		add_options_page( 'QM-OAuth Options', 'QM-OAuth', 'manage_options', 'QM-OAuth', array($this, 'qmoa_settings_page_content') );
	}

	// render the main settings page content:
	function qmoa_settings_page_content() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$blog_url = rtrim(site_url(), "/") . "/";
		include 'qm-oauth-settings.php';
	}
} // END OF WPOA CLASS

// instantiate the plugin class ONCE and maintain a single instance (singleton):
WPOA::get_instance();
?>