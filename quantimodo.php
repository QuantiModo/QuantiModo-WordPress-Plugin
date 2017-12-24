<?php
/*
 * Plugin Name: QuantiModo
 * Version: 0.5.3
 * Description: Allow your users to record, aggregate, analyze and visualize their health and life-tracking data.
 * Author: QuantiModo
 * Author URI: https://quantimo.do
 * Plugin URI: https://quantimo.do
 */

// Prevent Direct Access
defined('ABSPATH') or die("Restricted access!");

/*
* Define
*/
define('QUANTIMODO_4f050d29b8BB9_VERSION', '1.5');
define('QUANTIMODO_4f050d29b8BB9_DIR', plugin_dir_path(__FILE__));
define('QUANTIMODO_4f050d29b8BB9_URL', plugin_dir_url(__FILE__));
defined('QUANTIMODO_4f050d29b8BB9_PATH') or define('QUANTIMODO_4f050d29b8BB9_PATH', untrailingslashit(plugins_url('', __FILE__)));
define('APP_BUILDER_URL', 'https://app.quantimo.do/builder');

require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/core.php');
require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/menus.php');
require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/admin.php');
require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/embed.php');


?>
