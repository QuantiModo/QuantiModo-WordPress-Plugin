<?php
/*
 * Plugin Name: QuantiModo
 * Version: 1.8.3
 * Description: Adds 100% free live chat & targeted messages to your website. Designed for internet businesses like yours to increase sales, conversions and better support your customers.
 * Author: QuantiModo
 * Author URI: https://app.quantimo.do/?ref=wordpress
 * Plugin URI: https://app.quantimo.do/?ref=wordpress
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

require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/core.php');
require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/menus.php');
require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/admin.php');
require_once(QUANTIMODO_4f050d29b8BB9_DIR . 'includes/embed.php');


?>
