<?php
/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// delete all plugin settings ONLY if the user requested it:
global $wpdb;
$delete_settings = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'qmwp_delete_settings_on_uninstall'");
if ($delete_settings) {
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'qmwp_%';");
    $wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'qmwp_%';");
}
