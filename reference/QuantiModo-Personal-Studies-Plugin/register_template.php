<?php

//Template fallback
add_action("template_redirect", 'my_theme_redirect');

function my_theme_redirect() {
    global $wp;
    $plugindir = dirname( __FILE__ );
    $pageid = get_the_ID();
	$pagename ='personal-study' . '/' . get_query_var('pagename');
		
     //A Specific Custom Post Type
    if ($wp->query_vars["post_type"] == 'personal-study') {
        $templatefilename = 'BarGraph.php';
		
		
		if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/themefiles/' . $templatefilename;
        }
        do_theme_redirect($return_template);
    } 

	if ($wp->query_vars["pagename"] == $pagename) {
        $templatefilename = 'BarGraph.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/themefiles/' . $templatefilename;
        }
        do_theme_redirect($return_template);
    }
}


function do_theme_redirect($url) {
    global $post, $wp_query;
    if (have_posts()) {
        include($url);
        die();
    } else {
        $wp_query->is_404 = true;
    }
}

?>