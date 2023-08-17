<?php
// ui.php

if(stripos(WP_SITEURL, '.quantimo.do') !== false){
    function annointed_admin_bar_remove() {
        global $wp_admin_bar;
        if($wp_admin_bar && method_exists($wp_admin_bar, 'remove_menu')){$wp_admin_bar->remove_menu('wp-logo');}
    }
    add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0);

    function change_footer_admin (): string
    {return ' ';} //Hide admin footer from admin
    add_filter('admin_footer_text', 'change_footer_admin', 9999);

    function change_footer_version(): string
    {return ' ';}
    add_filter( 'update_footer', 'change_footer_version', 9999);
}