<?php
// settings.php

/**
 * Register QuantiModo settings
 */
function QuantiModo_register_settings()
{
    register_setting('QuantiModo_settings_group', 'QuantiModo_settings');
}
add_action('admin_init', 'QuantiModo_register_settings');

/**
 * Uninstall QuantiModo settings
 */
function QuantiModo_uninstall()
{
    delete_option('QuantiModo_settings');  // Delete options on uninstall
}
register_uninstall_hook(__FILE__, 'QuantiModo_uninstall');

/**
 * Get QuantiModo API client ID
 * @return string|null
 */
function qm_api_client_id() {
    $options = qm_settings();
    $quantimodo_client_id = (isset($options['quantimodo_client_id'])) ? $options['quantimodo_client_id'] : null;
    return $quantimodo_client_id;
}

/**
 * Get QuantiModo settings
 * @return array
 */
function qm_settings() {
    $settings = get_option('QuantiModo_settings');
    if(!$settings){$settings = [];}
    return $settings;
}

/**
 * Get QuantiModo option
 * @param string $name
 * @param mixed $default
 * @return mixed
 */
function get_qm_option(string $name, $default = null){
    $options = qm_settings();
    $value = (isset($options[$name])) ? $options[$name] : $default;
    return $value;
}

/**
 * Check if QuantiModo floating button is enabled
 * @return bool
 */
function qm_floating_button_enabled(): bool
{
    $floating_button_enabled = get_qm_option('floating_button_enabled') == "on";
    return $floating_button_enabled;
}