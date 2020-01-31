<?php

// Create a option page for settings
add_action('admin_menu', 'add_quantimodo_option_page');

// Add top-level admin bar link
add_action('admin_bar_menu', 'add_quantimodo_link_to_admin_bar', 999);

// Adds QuantiModo link to top-level admin bar
function add_quantimodo_link_to_admin_bar()
{
  global $wp_version;
    global /** @var WP_Admin_Bar $wp_admin_bar */
    $wp_admin_bar;

  $quantimodo_icon = '<img src="' . QUANTIMODO_4f050d29b8BB9_PATH . '/assets-wp-repo/quantimodo-icon-16x16-white.png' . '">';

  $args = array(
    'id' => 'quantimodo-admin-menu',
    'title' => '<span class="ab-icon" ' . ($wp_version < 3.8 && !is_plugin_active('mp6/mp6.php') ? ' style="margin-top: 3px;"' : '') . '>' . $quantimodo_icon . '</span><span class="ab-label">QuantiModo</span>', // alter the title of existing node
    'parent' => FALSE,   // set parent to false to make it a top level (parent) node
    'href' => get_bloginfo('wpurl') . '/wp-admin/admin.php?page=menus.php',
    'meta' => array('title' => 'QuantiModo')
  );

  $wp_admin_bar->add_node($args);
}

// Hook in the options page
function add_quantimodo_option_page()
{
  add_options_page('QuantiModo Options', 'QuantiModo', 'activate_plugins', basename(__FILE__), 'quantimodo_options_page');
}
