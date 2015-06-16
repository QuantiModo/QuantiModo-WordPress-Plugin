<?php
/*
Plugin Name: QuantiModo Personal Studies
Description: This plugin creates Studies as a post types and render's its components
Version: 1.0
Author: Zain Sohail
*/

include_once('functions.php');
include_once('post-type-register.php');
include_once('register_template.php');
include_once('metaboxes/meta_box_studies.php');
include_once('metaboxes/library/editor.php');
include_once('custom_meta_boxes_studies.php');
include_once('metaboxes/library/addtables.php');

register_activation_hook( __FILE__, 'create_variable_table' );
register_activation_hook( __FILE__, 'create_variable_categories_table' );




?>