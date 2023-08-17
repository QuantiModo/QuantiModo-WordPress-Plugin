<?php
include 'notifications.php';
include 'settings.php';
include 'api.php';
include 'ui.php';

/**
 * @param string $message
 */
function qm_error(string $message): void {
    $pluginLog = plugin_dir_path(__FILE__).'debug.log';
    error_log($message.PHP_EOL, 3, $pluginLog);
}