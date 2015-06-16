<?php

//Template fallback
add_action("template_redirect", 'qm_search_template_redirect');

function qm_search_template_redirect() {
    global $wp;
    $page = get_page_by_title( 'QM Search' );
	
    $plugindir = dirname( __FILE__ );
    
    if ( is_page($page->ID)  ) {
		$templatefilename = 'qm-search.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/themefiles/' . $templatefilename;
        }
        theme_redirect_search($return_template);
    }
}

function theme_redirect_search($url) {
    global $post, $wp_query;
    if (have_posts()) {
        include($url);
        die();
    } else {
        $wp_query->is_404 = true;
    }
}

// Create 'Ask QuantiModo' page if not exists.
function check_pages_live(){
        if( get_page_by_title( 'QM Search' ) == NULL )
        create_pages_fly( 'QM Search' );
    }
    add_action('init','check_pages_live');

	function create_pages_fly($pageName) {
        $createPage = array(
          'post_title'    => $pageName,
          'post_status'   => 'publish',
          'post_author'   => 1,
          'post_type'     => 'page',
          'post_name'     => $pageName
        );

        // Insert the post into the database
        wp_insert_post( $createPage );
    }
