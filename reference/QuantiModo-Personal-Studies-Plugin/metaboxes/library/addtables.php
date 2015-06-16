<?php
/*******************************/
/*     Create tables           */
/*******************************/

function create_variable_table()
{
        // do NOT forget this global
    global $wpdb;

    // this if statement makes sure that the table doe not exist already
    if($wpdb->get_var("show tables like variables") != 'variables') 
    {
        $variablesql = "CREATE TABLE variables (
        `id` int(10) unsigned NOT NULL,
        `parent` int(10) unsigned DEFAULT NULL,
        `name` varchar(125) NOT NULL COMMENT 'Name of the variable',
        `variable-category` tinyint(3) unsigned NOT NULL COMMENT 'Category of the variable',
        `default-unit` smallint(5) unsigned NOT NULL COMMENT 'ID of the default unit of measurement to use for this variable',
        `combination-operation` tinyint(3) unsigned DEFAULT NULL COMMENT 'How to combine values of this variable (for instance, to see a summary of the values over a month) 0 for sum OR 1 for mean',
        `filling-value` double DEFAULT '-1',
        `maximum-value` double DEFAULT NULL COMMENT 'per 24 hour period',
        `minimum-value` double DEFAULT NULL,
        `onset-delay` int(10) unsigned NOT NULL DEFAULT '0',
        `duration-of-action` int(10) unsigned NOT NULL DEFAULT '86400',
        `updated` int(11) NOT NULL DEFAULT '1',
        `public` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Public Variables (1) appear in autocomplete',
        `cause-only` tinyint(1) DEFAULT NULL,
        `filling-type` enum('value','none') DEFAULT NULL COMMENT '0 -> No filling, 1 -> Use filling-value'
       ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($variablesql);
    }
}

function create_variable_categories_table()
{
        // do NOT forget this global
    global $wpdb;

    // this if statement makes sure that the table doe not exist already
    if($wpdb->get_var("show tables like variable_categories") != 'variable_categories') 
    {
        $categoriessql = "CREATE TABLE variable_categories (
        `id` tinyint(3) unsigned NOT NULL,
        `name` varchar(64) NOT NULL COMMENT 'Name of the category',
        `filling-value` double DEFAULT NULL,
        `maximum-value` double DEFAULT NULL,
        `minimum-value` double DEFAULT NULL,
        `duration-of-action` int(10) unsigned NOT NULL DEFAULT '86400',
        `onset-delay` int(10) unsigned NOT NULL DEFAULT '0',
        `combination-operation` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'How to combine values of this variable (for instance, to see a summary of the values over a month) 0 for sum OR 1 for mean',
        `updated` int(11) NOT NULL DEFAULT '1',
        `cause-only` tinyint(1) NOT NULL DEFAULT '0',
        `public` tinyint(1) NOT NULL DEFAULT '0',
        `filling-type` enum('none','value') DEFAULT 'none' COMMENT '0 -> No filling, 1 -> Use filling-value'
       ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($categoriessql);
    }
}
?>