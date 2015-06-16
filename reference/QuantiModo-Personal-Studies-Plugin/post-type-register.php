<?php
if ( ! function_exists('register_qm_studies') ) {

// Register Custom Post Type
function register_qm_studies() {

	$labels = array(
		'name'                => _x( 'Studies', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Study', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Study', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Studies', 'text_domain' ),
		'view_item'           => __( 'All Studies', 'text_domain' ),
		'add_new_item'        => __( 'Add New ', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'search_items'        => __( 'Search Item', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'personal-study', 'text_domain' ),
		'description'         => __( 'Create Studies on different Variables', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'post-formats', ),
		'taxonomies'          => array( 'post_tag', 'study-tax' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'personal-study', $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_qm_studies', 0 );

// Register Custom Taxonomy
function study_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Studies', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Study', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Categories', 'text_domain' ),
		'all_items'                  => __( 'All Items', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Item Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Item', 'text_domain' ),
		'edit_item'                  => __( 'Edit Item', 'text_domain' ),
		'update_item'                => __( 'Update Item', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
		'search_items'               => __( 'Search Items', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used items', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'study-tax', array( 'personal-study' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'study_taxonomy', 0 );
}


function animation_effects_ps(){
        $animate=array(
                        ''=>'none',
                        'animate cssanim flash'=> 'Flash',
                        'animate zoom' => 'Zoom',
                        'animate scale' => 'Scale',
                        'animate slide' => 'Slide (Height)', 
                        'animate expand' => 'Expand (Width)',
                        'animate cssanim shake'=> 'Shake',
                        'animate cssanim bounce'=> 'Bounce',
                        'animate cssanim tada'=> 'Tada',
                        'animate cssanim swing'=> 'Swing',
                        'animate cssanim wobble'=> 'Flash',
                        'animate cssanim wiggle'=> 'Flash',
                        'animate cssanim pulse'=> 'Flash',
                        'animate cssanim flip'=> 'Flash',
                        'animate cssanim flipInX'=> 'Flip Left',
                        'animate cssanim flipInY'=> 'Flip Top',
                        'animate cssanim fadeIn'=> 'Fade',
                        'animate cssanim fadeInUp'=> 'Fade Up',
                        'animate cssanim fadeInDown'=> 'Fade Down',
                        'animate cssanim fadeInLeft'=> 'Fade Left',
                        'animate cssanim fadeInRight'=> 'Fade Right',
                        'animate cssanim fadeInUptBig'=> 'Fade Big Up',
                        'animate cssanim fadeInDownBig'=> 'Fade Big Down',
                        'animate cssanim fadeInLeftBig'=> 'Fade Big Left',
                        'animate cssanim fadeInRightBig'=> 'Fade Big Right',
                        'animate cssanim bounceInUp'=> 'Bounce Up',
                        'animate cssanim bounceInDown'=> 'Bounce Down',
                        'animate cssanim bounceInLeft'=> 'Bounce Left',
                        'animate cssanim bounceInRight'=> 'Bounce Right',
                        'animate cssanim rotateIn'=> 'Rotate',
                        'animate cssanim rotateInUpLeft'=> 'Rotate Up Left',
                        'animate cssanim rotateInUpRight'=> 'Rotate Up Right',
                        'animate cssanim rotateInDownLeft'=> 'Rotate Down Left',
                        'animate cssanim rotateInDownRight'=> 'Rotate Down Right',
                        'animate cssanim speedIn'=> 'Speed In',
                        'animate cssanim rollIn'=> 'Roll In',
                        'animate ltr'=> 'Left To Right',
                        'animate rtl' => 'Right to Left', 
                        'animate btt' => 'Bottom to Top',
                        'animate ttb'=>'Top to Bottom',
                        'animate smallspin'=> 'Small Spin',
                        'animate spin'=> 'Infinite Spin'
                        );
    return $animate;
    }


?>