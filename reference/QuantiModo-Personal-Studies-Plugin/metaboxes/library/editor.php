<?php
/**
 * FILE: qm-layout-editor.php 
 * Created on Oct 29, 2012 at 2:22:06 PM 
 * Credits: www.QuantiModo.do
 * Project: vEstate
 */


add_action( 'init', 'qm_layout_editor' );
function qm_layout_editor(){
	add_action( 'admin_enqueue_scripts', 'qm_scripts_styles', 10, 1 );
	function qm_scripts_styles( $hook ) {
		if ( in_array( $hook, array( 'post-new.php', 'post.php' ) ) ){
      if( (isset($_GET['post_type']) && ($_GET['post_type'] == 'page')) || (isset($_GET['post']) && (get_post_type($_GET['post']) == 'page'))){
        qm_new_settings_page_js();
        qm_new_settings_page_css();
      }
		}
	}

}


//AJAX CALLS

add_action( 'wp_ajax_add_slider_item', 'qm_add_slider_item' );
  function qm_add_slider_item(){
    if ( ! wp_verify_nonce( $_POST['load_nonce'], 'load_nonce' ) ) die(-1);
    
    $attachment_class = $_POST['attachment_class'];
    $change_image = (bool) $_POST['change_image'];

    preg_match( '/wp-image-([\d])+/', $attachment_class, $matches );
    $attachment_id = str_replace( 'wp-image-', '', $matches[0] );
    $attachment_image = wp_get_attachment_image( $attachment_id );
    
    if ( $change_image ) {
      echo json_encode( array( 'attachment_image' => $attachment_image, 'attachment_id' => $attachment_id ) );
    } else {
      echo '<div class="attachment clearfix" data-attachment="' . esc_attr( $attachment_id ) .'">' 
          . $attachment_image
          . '<div class="attachment_options">'
            . '<p class="clearfix">' . '<label>' . esc_html__('Description (HTML & Shortcodes allowed)', 'qm') . ': </label>' . '<textarea name="attachment_description[]" class="attachment_description"></textarea> </p>'
            . '<p class="clearfix">' . '<label>' . esc_html__('Link', 'qm') . ': </label>'. '<input name="attachment_link[]" class="attachment_link" /> </p>'
          . '</div>'
          . '<a href="#" class="delete_attachment">' . esc_html__('Delete this slide', 'qm') . '</a>'
          . '<a href="#" class="change_attachment_image">' . esc_html__('Change image', 'qm') . '</a>'
        . '</div>';
    }
    
    die();
  }
        
add_action( 'wp_ajax_qm_delete_layout', 'qm_delete_layout' );
  function qm_delete_layout(){
                $name = stripslashes($_POST['name']);
                  
                $value = get_option('qm_builder_sample_layouts');
                if(isset($value)){
                    
                    if(is_string($value))
                    $value=  unserialize($value);
                    
                    for($i=0;$i<count($value);$i++){
                        if($name == $value[$i]['name']){
                            unset($value[$i]);
                            $value = array_values($value);
                                $value=serialize($value);
                                update_option('qm_builder_sample_layouts',$value);
                            }
                        }
                    }
                die();
            }
            
add_action( 'wp_ajax_show_module_options', 'qm_new_show_module_options' );
  function qm_new_show_module_options(){
    if ( ! wp_verify_nonce( $_POST['load_nonce'], 'load_nonce' ) ) die(-1);
    
    $module_class = $_POST['module_class'];
    $v_module_exact_name = $_POST['module_exact_name'];
    $module_window = (int) $_POST['modal_window'];
    
    preg_match( '/m_([^\s])+/', $module_class, $matches );
    $module_name = str_replace( 'm_', '', $matches[0] );
    
    $paste_to_editor_id = isset( $_POST['paste_to_editor_id'] ) ? $_POST['paste_to_editor_id'] : '';
    
    generate_module_options( $module_name, $module_window, $paste_to_editor_id, $v_module_exact_name );
    
    die();
  }

add_action( 'wp_ajax_show_column_options', 'qm_new_show_column_options' );
  function qm_new_show_column_options(){
    if ( ! wp_verify_nonce( $_POST['load_nonce'], 'load_nonce' ) ) die(-1);
    
    $module_class = $_POST['et_module_class'];
    
    preg_match( '/m_column_([^\s])+/', $module_class, $matches );
    $module_name = str_replace( 'm_column_', '', $matches[0] );
    
    $paste_to_editor_id = isset( $_POST['paste_to_editor_id'] ) ? $_POST['paste_to_editor_id'] : '';
    
    generate_column_options( $module_name, $paste_to_editor_id );
    
    die();
  }


add_shortcode('v_carousel', 'qm_custom_post_carousel');
function qm_custom_post_carousel($atts, $content = null) {
       global $qm_options; 
       
       
        $error = new qmErrors();
        if(!isset($atts) || !isset($atts['post_type'])){
          return $error->get_error('unsaved_editor');
        }
       
       
	$attributes = v_get_attributes( $atts, "qm_custom_post_carousel" );
	
        if(!isset($atts['auto_slide']))
            $atts['auto_slide']='';

        if($atts['custom_css'] && strlen($atts['custom_css'])>5)    
            $output = '<style>'.$atts['custom_css'].'</style>';
        else
            $output= '';

	$output .= "<div {$attributes['class']}{$attributes['inline_styles']}>";

	if(!isset($atts['post_ids']) || count($atts['post_ids']) > 0){
        
        if(isset($atts['term']) && isset($atts['taxonomy']) && $atts['term'] !='nothing_selected'){
            
            if(isset($atts['taxonomy']) && $atts['taxonomy']!=''){ 
                    
                        $check=term_exists($atts['term'], $atts['taxonomy']);
                    if($atts['term'] !='nothing_selected'){    
                   if ($check == 0 || $check == null || !$check) {
                           $error = new qmErrors();
                          $output .= $error->get_error('term_taxonomy_mismatch');
                          $output .='</div>';
                          return $output;
                       } 
                    }
                       $check=is_object_in_taxonomy($atts['post_type'], $atts['taxonomy']);
                   if ($check == 0 || $check == null || !$check) {
                           $error = new qmErrors();
                           $output .= $error->get_error('term_postype_mismatch');
                           $output .='</div>';
                           return $output;
                       }
                    }
                    
            if(isset($atts['taxonomy']) && $atts['taxonomy']!=''){
                         if($atts['taxonomy'] == 'category'){
                             $atts['taxonomy']='category_name'; 
                             }
                          if($atts['taxonomy'] == 'tag'){
                             $atts['taxonomy']='tag_name'; 
                             }   
                     }
                     
          $query_args=array( 'post_type' => $atts['post_type'],$atts['taxonomy'] => $atts['term'], 'posts_per_page' => $atts['carousel_number']);
          
        }else
           $query_args=array('post_type'=>$atts['post_type'], 'posts_per_page' => $atts['carousel_number']);
        
        if($atts['post_type'] == 'course'){
            switch($atts['course_style']){
                case 'popular':
                  $query_args['orderby'] = 'meta_value_num';
                  $query_args['meta_key'] = 'qm_students';
                break;
                case 'rated':
                  $query_args['orderby'] = 'meta_value_num';
                  $query_args['meta_key'] = 'average_rating';
                break;
                case 'reviews':
                  $query_args['orderby'] = 'comment_count';
                break;
                case 'random':
                   $query_args['orderby'] = 'rand';
                break;
            }
            $query_args['order'] = 'DESC';
        }
        
        
        $the_query = new WP_Query($query_args);

        }else{
                $cus_posts_ids=explode(",",$atts['post_ids']);
        	$query_args=array( 'post_type' => $atts['post_type'], 'post__in' => $cus_posts_ids , 'orderby' => 'post__in'); 
        	$the_query = new WP_Query($query_args);
        }
        
        
        if(isset($atts['title']) && $atts['title'] && $atts['title'] != 'Content'){
            $ntitle= $atts['title'];
            $ntitle = preg_replace('/[^a-zA-Z0-9\']/', '_', $ntitle);
            $ntitle = str_replace("'", '', $ntitle);
            $output .='<div id="'.$ntitle.'"></div>';
        }
        
        $more= '';
        if(isset($atts['show_more']) && $atts['show_more']) {
            $more = ' <a href="'.$atts['more_link'].'" class="heading_more">+</a>';
        }
        $noheading='';
        
        if($atts['show_title'])
            $output .='<h3 class="heading"><span>'.$atts['title'].'</span></h3>'.$more;
        else
            $noheading='noheading';
        
        global $script;
        $rand='carousel'.rand(1,999);
    
        echo '<script> var op'.$rand.' = {
           "directionNav" : '.(($atts['show_controls'])? 'true':'false').',
           "animationLoop" : '.((isset($atts['auto_slide']) && $atts['auto_slide'])? 'true':'false').',
           "slideshow" : '.((isset($atts['auto_slide']) && $atts['auto_slide'])? 'true':'false').'
        };</script>';

        $class='slides';
        

        $output .= '<div id="'.$rand.'" class="qm_carousel flexslider loading '.(($atts['carousel_max']==1)?'onecol':'').' '.$noheading.' '.((isset($atts['show_more']) && $atts['show_more'])?'more_heading':'').'" data-block-width="'.$atts['column_width'].'" data-block-max="'.$atts['carousel_max'].'" data-block-min="'.$atts['carousel_min'].'">
  	            <ul class="'.$class.'">';
  	     $links='';
         $excerpt='';
         $thumb='';
         
         
         if($atts['column_width'] < 311)
             $cols = 'small';
         
         if(($atts['column_width'] >= 311) && ($atts['column_width'] < 460))    
             $cols='medium';
         
         if(($atts['column_width'] >= 460) && ($atts['column_width'] < 769))    
             $cols='big';
         
         if($atts['column_width'] >= 769)    
             $cols='full';

        if( $the_query->have_posts() ) {
          
        while ( $the_query->have_posts() ) : $the_query->the_post();
        global $post;
        $output .= '<li>';
        $output .= thumbnail_generator($post,$atts['featured_style'],$cols,$atts['carousel_excerpt_length'],$atts['carousel_link'],$atts['carousel_lightbox']);
        $output .= '</li>';
        endwhile;
        }else{
          $error = new qmErrors();
          $output .= $error->get_error('no_posts');
        }
        wp_reset_postdata();
        $output .= "</ul></div></div>";

	return $output;
}


add_shortcode('v_filterable', 'qm_custom_post_filterable');
function qm_custom_post_filterable($atts, $content = null) {
        global $qm_options;
        
        $error = new qmErrors();
        if(!isset($atts) || !isset($atts['post_type'])){
          return $error->get_error('unsaved_editor');
        }
       
        
        if(!isset($qm_options['default_hover']))$qm_options['default_hover']='hover_fade_in';
	$attributes = v_get_attributes( $atts, "qm_custom_post_filterable" );
        
        if($atts['custom_css'] && strlen($atts['custom_css'])>5)    
            $output = '<style>'.$atts['custom_css'].'</style>';
        else
            $output= '';
	$output .= "<div {$attributes['class']}{$attributes['inline_styles']}>";
	
        if(isset($atts['title']) && $atts['title'] && $atts['title'] != 'Content'){
            $ntitle= $atts['title'];
            $ntitle = preg_replace('/[^a-zA-Z0-9\']/', '_', $ntitle);
            $ntitle = str_replace("'", '', $ntitle);
            $output .='<div id="'.$ntitle.'"></div>';
        }
         
         if(isset($atts['taxonomy']) && $atts['taxonomy']!=''){ 
                       $check=is_object_in_taxonomy($atts['post_type'], $atts['taxonomy']);
                   if ($check == 0 || $check == null || !$check) {
                           $error = new qmErrors();
                           $output .= $error->get_error('term_postype_mismatch');
                           $output .='</div>';
                           return $output;
                       }
                    }
         
         if($atts['column_width'] < 311)
             $cols = 'small';
         
         if(($atts['column_width'] >= 311) && ($atts['column_width'] < 460))    
             $cols='medium';
         
         if(($atts['column_width'] >= 460) && ($atts['column_width'] < 769))    
             $cols='big';
         
         if($atts['column_width'] >= 769)    
             $cols='full';
         
        global $paged,$wp_query;
          
        $temp_query = $wp_query;
        
        $wp_query = null;
        $query_args=array('post_type'=>$atts['post_type'], 'posts_per_page' => intval($atts['filterable_number']));
        
        if($atts['show_pagination']) 
        $query_args['paged']=$paged;  
        
        
        if($atts['post_type'] == 'course'){
            switch($atts['course_style']){
                case 'popular':
                  $query_args['orderby'] = 'meta_value_num';
                  $query_args['meta_key'] = 'qm_students';
                break;
                case 'rated':
                  $query_args['orderby'] = 'meta_value_num';
                  $query_args['meta_key'] = 'average_rating';
                break;
                case 'reviews':
                  $query_args['orderby'] = 'comment_count';
                break;
                case 'random':
                   $query_args['orderby'] = 'rand';
                break;
            }
        }
        query_posts($query_args);
        
        if($atts['show_title'])
        $output .='<h3 class="heading"><span>'.$atts['title'].'</span></h3>';
        
        $output .= '<div class="filterable_columns">
  			 	  <ul class="qm_filterable">';
        if($atts['show_all'])                
        $output .='<li class="active"><a href="javascript:void();" data-filter="*" class="all">'.__('All','qm').'</a></li>';
        
        
        while ( have_posts() ) : the_post();
        global $post;
        $cats=get_the_terms($post->ID,$atts['taxonomy']);
        if(is_array($cats))
        foreach($cats as $cat){
        $categories[$post->ID][]=$cat->slug;
        $all_categories[$cat->slug]=$cat->name;
        }
        endwhile;
  	wp_reset_query();
  	    
        if(is_Array($all_categories)){
          $all_categories=  array_unique($all_categories);
          foreach($all_categories as $slug=>$name){
              $output .='<li><a href="javascript:void();" data-filter=".'.$slug.'">'.$name.'</a></li>';
          }
        }
        $output .='</ul><div class="filterableitems_container">';
        
        
         $query_args=array('post_type'=>$atts['post_type'], 'posts_per_page' => intval($atts['filterable_number']));
         
         if($atts['show_pagination'])
         $query_args['paged']=$paged;  
            
            $wp_query = null;
            query_posts($query_args);
            while ( have_posts() ) : the_post();
            global $post;
                if(isset($categories[$post->ID]) && is_Array($categories[$post->ID])){
                  foreach($categories[$post->ID] as $cat)
                  $classes = $cat.' ';
                }
                $output .='<div class="filteritem '.$classes.'" style="max-width:'.$atts['column_width'].'px;width:100%;">'; 
                $output .= thumbnail_generator($post,$atts['featured_style'],$cols,$atts['filterable_excerpt_length'],$atts['filterable_link'],$atts['filterable_lightbox']);
                 $output .='</div>';
            endwhile;
           
            $output .='</div>';
             if($atts['show_pagination']) {
                    ob_start(); 
                    pagination();
                    $output .= ob_get_contents();
                    ob_end_clean();
                }
            $output .='</div>';
            
            $output .='</div>';
           wp_reset_query();
            $wp_query = $temp_query;
       
	return $output;
}


/*==== FlexSlider ====*/

add_shortcode('v_slider', 'qm_custom_slider');
function qm_custom_slider($atts, $content) {
       extract(shortcode_atts(array(
				'title' => '',
        'slide_style' =>'slide1',
        'animation' => "fade",
        'auto_slide' => 1,
        'loop' => 1,
        'randomize' => 1,
        'show_directionnav'=>1,
        'show_controlnav' => 1,
        'animation_duration' => 700,
        'auto_speed' => 7000,
        'pause_on_hover' =>1 ,
        'css_class' => '',
        'custom_css' => '',
        'container_css' => ''
			), $atts));
       if($atts['custom_css'] && strlen($atts['custom_css'])>5)    
            $output = '<style>'.$atts['custom_css'].'</style>';
        else
            $output= '';
       
       $title = preg_replace('/[^a-zA-Z0-9\']/', '_', $title);
       $title = str_replace("'", '', $title).rand(1,999);;
       echo '<script>jQuery(document).ready(function(){
         jQuery("#'.$title.'").flexslider({
           animation:"'.$animation.'",
           animationLoop:'.(($loop)?'true':'false').',
           smoothHeight: true,
           slideshow:'.(($auto_slide)?'true':'false').',
           slideshowSpeed:'.$auto_speed.',
           animationSpeed:'.$animation_duration.',
           randomize : '.(($randomize)? 'true':'false').',
           directionNav: '.(($show_directionnav)? 'true':'false').',
           controlNav: '.(($show_controlnav)? 'true':'false').',
           pauseOnHove: '.(($pause_on_hover)? 'true':'false').',   
           prevText: \'<i class="icon-arrow-1-left"></i>\',
           nextText: \'<i class="icon-arrow-1-right"></i>\'    
           });
        });</script>';
        $attributes = v_get_attributes( $atts, "qm_custom_slider" );
	$output .= "<div {$attributes['class']}{$attributes['inline_styles']}>";
        if(isset($atts['title']) && $atts['title'] && $atts['title'] != 'Content'){
            $ntitle= $atts['title'];
            $ntitle = preg_replace('/[^a-zA-Z0-9\']/', '_', $ntitle);
            $ntitle = str_replace("'", '', $ntitle);
            $output .='<div id="'.$ntitle.'"></div>';
        }
        
        $output .= '<div id="'.$title.'" class="image_slider '.$slide_style.'">';
        $output .= '<ul class="slides">';
        $output .= $content;
        $output .= "</ul>";
        $output .= "</div>";
        $output .= "</div>";
       return $output;
}
add_shortcode('v_slides', 'qm_custom_attachment');
function qm_custom_attachment($atts, $content) {
       extract(shortcode_atts(array(
				'attachment_id' => '',
				'link' => ''
			), $atts));
       if(isset($attachment_id) && $attachment_id){
       $image = wp_get_attachment_image_src( $attachment_id, 'full' );
       $output  = '<li>';
       $output .= '<a href="'.$link.'">';
       $output .= '<img src="'.$image[0].'" />';
       $output .= '</a>';
       $output .= ($content)?'<div class="flex-caption">'.html_entity_decode($content).'</div>':'';
       $output .= '</li>';
       return $output;
       }
}




add_shortcode('v_grid', 'qm_post_grid');
function qm_post_grid($atts, $content = null) {
       global $qm_options; 
       
       
        $error = new qmErrors();
        if(!isset($atts) || !isset($atts['post_type'])){
          return $error->get_error('unsaved_editor');
        }
       
       
	$attributes = v_get_attributes( $atts, "qm_post_grid" );
	
        if(isset($atts['masonry']) && $atts['masonry']){
            $atts['custom_css'] .= '.grid.masonry li .block { margin-bottom:'.(isset($atts['gutter'])?$atts['gutter']:'30').'px;}';
        }  
        
        if($atts['custom_css'] && strlen($atts['custom_css'])>5)    
            $output = '<style>'.$atts['custom_css'].'</style>';
        else
            $output= '';
        
	$output .= "<div {$attributes['class']}{$attributes['inline_styles']}>";
        
	if(!isset($atts['post_ids']) || $atts['post_id'] ==''){
        
        if(isset($atts['term']) && isset($atts['taxonomy']) && $atts['term'] !='nothing_selected'){
            
            if(isset($atts['taxonomy']) && $atts['taxonomy']!=''){ 
                    
                        $check=term_exists($atts['term'], $atts['taxonomy']);
                    if($atts['term'] !='nothing_selected'){    
                   if ($check == 0 || $check == null || !$check) {
                           $error = new qmErrors();
                          $output .= $error->get_error('term_taxonomy_mismatch');
                          $output .='</div>';
                          return $output;
                       } 
                    }
                       $check=is_object_in_taxonomy($atts['post_type'], $atts['taxonomy']);
                   if ($check == 0 || $check == null || !$check) {
                           $error = new qmErrors();
                           $output .= $error->get_error('term_postype_mismatch');
                           $output .='</div>';
                           return $output;
                       }
                    }


            if($atts['column_width'] < 311)
             $cols = 'small';
         
         if(($atts['column_width'] >= 311) && ($atts['column_width'] < 460))    
             $cols='medium';
         
         if(($atts['column_width'] >= 460) && ($atts['column_width'] < 769))    
             $cols='big';
         
         if($atts['column_width'] >= 769)    
             $cols='full';
         
            if(isset($atts['taxonomy']) && $atts['taxonomy']!=''){
                         if($atts['taxonomy'] == 'category'){
                             $atts['taxonomy']='category_name'; 
                             }
                          if($atts['taxonomy'] == 'tag'){
                             $atts['taxonomy']='tag_name'; 
                             }   
                     }
           
                             
          $query_args=array( 'post_type' => $atts['post_type'],$atts['taxonomy'] => $atts['term'], 'posts_per_page' => $atts['grid_number']);
          
        }else
           $query_args=array('post_type'=>$atts['post_type'], 'posts_per_page' => $atts['grid_number']);
        
        

        if($atts['post_type'] == 'course'){
            switch($atts['course_style']){
                case 'popular':
                  $query_args['orderby'] = 'meta_value_num';
                  $query_args['meta_key'] = 'qm_students';
                break;
                case 'rated':
                  $query_args['orderby'] = 'meta_value_num';
                  $query_args['meta_key'] = 'average_rating';
                break;
                case 'reviews':
                  $query_args['orderby'] = 'comment_count';
                break;
                case 'random':
                   $query_args['orderby'] = 'rand';
                break;
            }
        }

        }else{
                $cus_posts_ids=explode(",",$atts['post_ids']);
        	$query_args=array( 'post_type' => $atts['post_type'], 'post__in' => $cus_posts_ids ); 
        }
        global $paged;
        if(isset($atts['pagination']) && $atts['pagination']){
                  
                  $query_args['paged']=$paged;       
               }
        $istyle='';       
        query_posts($query_args);
        $masonry=$style=$rel='';
        if(isset($atts['masonry']) && $atts['masonry']){
            $atts['grid_columns'] =' grid-item';
            $style= 'style="width:'.$atts['column_width'].'px;"'; 
            $masonry= 'masonry';
            $istyle .= ' data-width="'.$atts['column_width'].'" data-gutter="'.(isset($atts['gutter'])?$atts['gutter']:'30').'"';// Rel-width used in Masonry+infinite scroll
        }else{
                $cols = $atts['grid_columns'];
        }
        $infinite='';
        if(isset($atts['infinite']) && $atts['infinite']){
            $infinite=' inifnite_scroll';
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;
            $rel = 'data-page='.$paged;
        }
        
        if(isset($atts['title']) && $atts['title'] && $atts['title'] != 'Content'){
            $ntitle= $atts['title'];
            $ntitle = preg_replace('/[^a-zA-Z0-9\']/', '_', $ntitle);
            $ntitle = str_replace("'", '', $ntitle);
            $output .='<div id="'.$ntitle.'"></div>';
        }
        
        global $wp_query;
        if($atts['show_title']){
        $output .='<h3 class="heading"><span>'.$atts['title'].'</span></h3>'; 
        }
        $output .= '<div class="qm_grid '.$infinite.' '.$masonry.'" '.$rel.'><div class="wp_query_args" data-max-pages="'.$wp_query->max_num_pages.'">'.  json_encode($atts).'</div>';
  	
        if( have_posts() ) {
        
        $output .= '<ul class="grid '.$masonry.'" '.$istyle.'>';
        
        while ( have_posts() ) : the_post();
        global $post;
        
        
        $output .= '<li class="'.$atts['grid_columns'].'" '.$style.'>';
        $output .= thumbnail_generator($post,$atts['featured_style'],$cols,$atts['grid_excerpt_length'],$atts['grid_link'],$atts['grid_lightbox']);
        $output .= '</li>';
        
        endwhile;
       
        $output .= '</ul>';
        }else{
          $error = new qmErrors();
          $output .= $error->get_error('no_posts');
        }
        wp_reset_postdata();
        $output .= '</div>';
        
        if(isset($atts['infinite']) && $atts['infinite']){
            $output .= '<div class="load_grid"><span>'.__('Loading..','qm').'</i></span></div>
                        <div class="end_grid"><span>'.__('No more to load','qm').'</i></span></div>';
        }
        $output .="</div>";
        if(isset($atts['pagination']) && $atts['pagination']){
        ob_start();
        pagination();
        $output .=ob_get_contents();
        ob_end_clean();
        }
        wp_reset_query();
        wp_reset_postdata();
	return $output;
}


add_shortcode('v_layerslider', 'qm_layerslider');
function qm_layerslider($atts, $content = null) {
       if($atts['custom_css'] && strlen($atts['custom_css'])>5)    
            $output = '<style>'.$atts['custom_css'].'</style>';
        else
            $output= '';
       $attributes = v_get_attributes( $atts, "qm_custom_post_carousel" );
       $output .= "<div {$attributes['class']}{$attributes['inline_styles']}>";

        if(isset($atts['title']) && $atts['title'] && $atts['title'] != 'Content'){
            $ntitle= $atts['title'];
            $ntitle = preg_replace('/[^a-zA-Z0-9\']/', '_', $ntitle);
            $ntitle = str_replace("'", '', $ntitle);
            $output .='<div id="'.$ntitle.'"></div>';
        }
        
       $output .=do_shortcode('[layerslider id="'.$atts['id'].'"]');
       
       $output .= '</div>';
	return $output;
}

add_shortcode('v_revslider', 'qm_revslider');
function qm_revslider($atts, $content = null) {
       if($atts['custom_css'] && strlen($atts['custom_css'])>5)    
            $output = '<style>'.$atts['custom_css'].'</style>';
        else
            $output= '';
       $attributes = v_get_attributes( $atts, "qm_custom_post_carousel" );
       $output .= "<div {$attributes['class']}{$attributes['inline_styles']}>";
       
        if(isset($atts['title']) && $atts['title'] && $atts['title'] != 'Content'){
            $ntitle= $atts['title'];
            $ntitle = preg_replace('/[^a-zA-Z0-9\']/', '_', $ntitle);
            $ntitle = str_replace("'", '', $ntitle);
            $output .='<div id="'.$ntitle.'"></div>';
        }
        
       $output .=do_shortcode('[rev_slider '.$atts['alias'].']');

       $output .= '</div>';
	return $output;
}






function qm_new_column( $atts, $content = null, $name = '' ){
    global $post;
    $content_span='';
    
    //$post_layout = get_post_custom_values('qm_sidebar_layout',$post->ID);
    //$content_span = $post_layout[0];
    
    switch($name){
        case 'v_1_2': $name='col-md-6 col-sm-6';
        break;
        case 'v_1_3': $name='col-md-4 col-sm-4';
        break;
        case 'v_1_4': $name='col-md-3 col-sm-3';
        break;
        case 'v_2_3': $name='col-md-8 col-sm-8';
        break;
        case 'v_3_4': $name='col-md-9 col-sm-9';
        break;
        case 'v_resizable': $name='col-md-12 fullwidth';
        break;
        case 'v_stripe':$name='stripe';
        break;
        case 'v_stripe_container':
                            $name='stripe_container';
        break;
    }
        if($name != 'stripe' && $name != 'stripe_container'){

	           $attributes = v_get_attributes( $atts, "v_column {$name}" );	

          	$output = 	"<div {$attributes['class']}{$attributes['inline_styles']}>"
          					     . do_shortcode( v_fix_shortcodes($content) ) .
          				      "</div> <!-- end .v_column_{$name} -->";

        }elseif( $name == 'stripe'){

            $name .=' fullwidth';
            $attributes = v_get_attributes( $atts, "v_column {$name}" );	
            
            $output = 	"</div></div>
                          </section>
                          <section class='stripe'>
                              <!-- Begin Stripe {$name} -->
                                    <div {$attributes['class']}{$attributes['inline_styles']}>"
					                         . do_shortcode( v_fix_shortcodes($content) ) .
                                    "</div> 
                                <!-- End Stripe{$name} -->
                          </section>          
                          <section class='main'>
                            <div class='container'>
                                <div class='full-width'>
                                    <div class='qm_editor clearfix'>";
                    }else{ // Stripe with Container

                        $name .=' fullwidth';
                        $attributes = v_get_attributes( $atts, "v_column {$name}" );	
                        $output = 	"</div></div>
                                    </section>
                                    <section class='stripe'>
                                      <div class='container'>
                                          <!-- Begin Stripe {$name} -->
                                          <div {$attributes['class']}{$attributes['inline_styles']}>"
      					                           . do_shortcode( v_fix_shortcodes($content) ) .
                                          "</div> 
                                          <!-- End Stripe{$name} -->    
                                       </div>
                                    </section>          
                                      <section class='main nextstripe'>
                                          <div class='container'>
                                            <div class='full-width'>
                                              <div class='qm_editor clearfix'>";
        }
	return $output;
}

// dialog box columns
function qm_new_alt_column( $atts, $content = null, $name = '' ){
	$name = str_replace( 'alt_', '', $name );
	$attributes = v_get_attributes( $atts, "v_column {$name}" );
		
	$output = 	"<div {$attributes['class']}{$attributes['inline_styles']}>"
					. do_shortcode( v_fix_shortcodes($content) ) .
				"</div> <!-- end .v_column_{$name} -->";

	return $output;
}

add_shortcode('v_text_block', 'qm_qm__text_block');
function qm_qm__text_block($atts, $content = null) {
        
	$attributes = v_get_attributes( $atts, "v_text_block" );
	if(isset($atts['custom_css'] ) && $atts['custom_css'] && strlen($atts['custom_css'])>5)    
            $output = '<style>'.$atts['custom_css'].'</style>';
        else
            $output= '';  	
	$output .= 	"<div {$attributes['class']}{$attributes['inline_styles']}>";
        
        if(isset($atts['title']) && $atts['title'] && $atts['title'] != 'Content'){
        $ntitle= $atts['title'];
        $ntitle = preg_replace('/[^a-zA-Z0-9\']/', '_', $ntitle);
        $ntitle = str_replace("'", '', $ntitle);
        $output .='<div id="'.$ntitle.'"></div>';
        }
        
	$output .= do_shortcode( v_fix_shortcodes($content) ) .
				"</div>";

	return $output;
}


add_shortcode('v_parallax_block', 'qm_parallax_block');
function qm_parallax_block($atts, $content = null) {
	$attributes = v_get_attributes( $atts, "v_parallax_block" );
        $rand ='paralax'.rand(1,999);
	$output = '<style>#'.$rand.' {
            background: url('.$atts['bg_image'].') 50% -50px;
            position:relative;
            height: '.$atts['height'].'px;
            } '.$atts['custom_css'].'</style>'; 
        
        $scroll = ($atts['scroll'])?$atts['scroll']:2;
        $rev = ($atts['rev'])?$atts['rev']:'0';
	
	$output .= 	"<div id='$rand' data-rev={$rev} data-scroll={$scroll} {$attributes['class']}{$attributes['inline_styles']} >
                            <div class='parallax_content'>";
        
	if(isset($atts['title']) && $atts['title'] && $atts['title'] != 'Content'){
        $ntitle= $atts['title'];
        $ntitle = preg_replace('/[^a-zA-Z0-9\']/', '_', $ntitle);
        $ntitle = str_replace("'", '', $ntitle);
        $output .='<div id="'.$ntitle.'"></div>';
        }
        
	$output .= do_shortcode( v_fix_shortcodes($content) ) .
				"</div></div>";

	return $output;
}

add_shortcode('v_widget_area', 'qm_qm_new_widget_area');
function qm_qm_new_widget_area($atts, $content = null) {
	extract(shortcode_atts(array(
				'area' => 'mainsidebar'
			), $atts));
			
	$attributes = v_get_attributes( $atts, "qm_sidebar" );
	
	ob_start();
	dynamic_sidebar($area);
	$widgets = ob_get_contents();
	ob_end_clean();
	if($atts['custom_css'] && strlen($atts['custom_css'])>5)    
            $output = '<style>'.$atts['custom_css'].'</style>';
        else
            $output= '';
	$output .= 	"<div {$attributes['class']}{$attributes['inline_styles']}>"
					. $widgets .
				"</div> <!-- end sidebar -->";

	return $output;
}


	function qm_new_load_convertible_scripts( $scripts_to_load ){
		
	}

	function qm_new_settings_page_css(){
		wp_enqueue_style( 'v_admin_css', plugins_url( 'css/v_admin.css' , __FILE__ ) );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_style( 'thickbox' );
	}

	function qm_new_settings_page_js(){	
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-resizable' );
		
    if(defined('qm_URL'))
      wp_enqueue_script( 'chosen-js', qm_URL . '/js/chosen.jquery.min.js');        

    
    
    if ( floatval(get_bloginfo('version')) >= 3.9){
      wp_enqueue_script( 'v_admin_js',plugins_url( 'js/v_admin.js' , __FILE__ ), array('jquery','jquery-ui-core','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable','jquery-ui-resizable'), '1.0' );
    }else{
      wp_enqueue_script( 'v_admin_js',plugins_url( 'js/v_admin_old.js' , __FILE__ ), array('jquery','jquery-ui-core','jquery-ui-sortable','jquery-ui-draggable','jquery-ui-droppable','jquery-ui-resizable'), '1.0' );
    }
		wp_localize_script( 'v_admin_js', 'v_options', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'load_nonce' => wp_create_nonce( 'load_nonce' ), 'confirm_message' => __('Confirm Delete?', 'qm'), 'confirm_message_yes' => __('Yes', 'qm'), 'confirm_message_no' => __('No', 'qm'), 'saving_text' => __('Saving...', 'qm'), 'saved_text' => __('Saved.', 'qm') ) );
	}

	add_action('init','qm_new_modules_init');
	function qm_new_modules_init(){
		global $v_modules, $v_columns, $v_sample_layouts,$wp_registered_sidebars;
		
		$v_widget_areas =$v_post_types =array();
                
		foreach($wp_registered_sidebars as $sidebar){
		$v_widget_areas[$sidebar['id']]=$sidebar['id'];
		};
              
                
    $post_types=get_post_types('','objects'); 

    foreach ( $post_types as $post_type ){
        if( !in_array($post_type->name, array('attachment','revision','nav_menu_item','sliders','modals','shop','shop_order','shop_coupon','page','forum','topic','reply','unit','question','quiz')))
           $v_post_types[$post_type->name]=$post_type->label;
    }
     
     //Get List of All Products
     
    
    $v_thumb_styles = array(
                            ''=> plugins_url('images/thumb_1.png',__FILE__),
                            'course'=> plugins_url('images/thumb_2.png',__FILE__),
                            'side'=> plugins_url('images/thumb_3.png',__FILE__),
                            'blogpost'=> plugins_url('images/thumb_6.png',__FILE__),
                            'images_only'=> plugins_url('images/thumb_4.png',__FILE__),
                            'testimonial'=> plugins_url('images/thumb_5.png',__FILE__),
                                );
                
/* ===== Declaring the Modules =======  */                
$v_modules['carousel'] = array(
			'name' => __('Carousels/Rotating Blocks', 'qm'),
			'options' => array(

        'title' => array(
        	'title' => __('Title/Heading', 'qm'),
        	'type' => 'text',
        	'std' => __('Heading', 'qm')
        ), 

        'show_title' => array(
					'title' => __('Show Title', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				),

        'show_more' => array(
					'title' => __('Show Read More link', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(0, 'qm')
				),            

        'more_link' => array(
					'title' => __('More Link (User redirected to this page on click)', 'qm'),
					'type' => 'text',
					'std' => ''
				), 

        'show_controls' => array(
					'title' => __('Show Controls', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				), 

        'post_type' => array(
					'title' => __('Enter Post Type<br /><span style="font-size:11px;">(Select Post Type from Posts/Courses/Clients/Products ...)</span>', 'qm'),
					'type' => 'select',
					'options' => $v_post_types,
					'std' => __('post', 'qm')
				),

        'taxonomy' => array(
					'title' => __('Enter Taxonomy Slug (optional)<br /><span style="font-size:11px;">(A "Taxonomy" is a grouping mechanism for posts. Like Category for Posts, Tags for Posts, Portfolio Type for Portfolio etc.. <a href="http://codex.wordpress.org/Taxonomies">more</a>)</span> ', 'qm'),
					'type' => 'text',
					'std' => ''
				), 

		    'term' => array(
					'title' => __('Enter Taxonomy Term Name (optional, only if above is selected): ', 'qm'),
					'type' => 'text',
					'std' => ''
				),   
        'post_ids' => array(
					'title' => __('Or Enter Specific Post Ids', 'qm'),
					'type' => 'text',
          'std'=>''
				),   
        'course_style' => array(
          'title' => __('Course Types [Only for Post type = Course]', 'qm'),
          'type' => 'select',
          'options' => array(
            'recent' => 'Recently published',
            'popular' => 'Most Students',
            'rated'  => 'Highest Rated',
            'reviews' => 'Most Reviews',
            'random' => 'Random'
            ),
          'std' => __('recent', 'qm')
        ),    
        'featured_style' => array(
					'title' => __('Carousel/Rotating Block Style', 'qm'),
					'type' => 'radio_images',
					'options' => $v_thumb_styles,
					'std' => __('excerpt', 'qm')
				),
        'auto_slide' => array(
					'title' => __('Auto slide/rotate', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				),            
		    'column_width' => array(
					'title' => __('Width each crousel block', 'qm'),
					'type' => 'text',
					'std' => __('268', 'qm')
				), 
        'carousel_max' => array(
          'title' => __('Maximum Number of blocks in One screen', 'qm'),
          'type' => 'text',
          'std' => __('4', 'qm')
        ), 
        'carousel_min' => array(
          'title' => __('Minimum Number of blocks in one Screen', 'qm'),
          'type' => 'text',
          'std' => __('2', 'qm')
        ),           
        'carousel_number' => array(
					'title' => __('Total Number of Blocks', 'qm'),
					'type' => 'text',
          'std' => __('6', 'qm')
				), 
		
        'carousel_excerpt_length' => array(
					'title' => __('Excerpt Length in Block (in characters)', 'qm'),
					'type' => 'text',
					'std' => __('100', 'qm')
				),  
        'carousel_lightbox' => array(
					'title' => __('Show Lightbox button on image hover[Opens Full image]', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				),
        'carousel_link' => array(
					'title' => __('Show Link button on image hover', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				), 
        'advanced_settings' => array(
			      'title' => __('Show Advanced settings', 'qm'),
			      'type' => 'divider',
            'std' => 3
		    ),             
        'css_class' => array(
           'title' => __('* Custom Class name (Add Custom Class to this Block)', 'qm'),
           'type' => 'text'
        ),
        'container_css' => array(
            'title' => __('* Class for on containing Layout column', 'qm'),
            'type' => 'text'
        ),
        'custom_css' => array(
	           'title' => __('* Add Custom CSS (Use <strong>.</strong> for class name, <strong>:hover</strong> for hover styles etc..)', 'qm'),
			       'type' => 'textarea'
        ),             
		  ),
		);

                
/* ====== Filterable ===== */
                
		$v_modules['filterable'] = array(
			'name' => __('Filterable Posts', 'qm'),
			'options' => array(
                   
        'title' => array(
          	'title' => __('Filterable Block Title', 'qm'),
          	'type' => 'text',
          	'std' => __('Heading', 'qm')
          ), 
        'show_title' => array(
					'title' => __('Show Title', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				), 
        'post_type' => array(
					'title' => __('Select a Post Type', 'qm'),
					'type' => 'select',
					'options' => $v_post_types,
					'std' => __('post', 'qm')
				),    
        'taxonomy' => array(
					'title' => __('Enter relevant Taxonomy name used for Filter buttons', 'qm'),
					'type' => 'text',
					'std' => ''
				),
        'course_style' => array(
          'title' => __('Course Types [Only for Post type = Course]', 'qm'),
          'type' => 'select',
          'options' => array(
            'recent' => 'Recently published',
            'popular' => 'Most Students',
            'rated'  => 'Highest Rated',
            'reviews' => 'Most Reviews',
            'random' => 'Random'
            ),
          'std' => __('recent', 'qm')
        ), 
        'featured_style' => array(
					'title' => __('Featured Media Block Style', 'qm'),
					'type' => 'radio_images',
					'options' => $v_thumb_styles,
					'std' => __('excerpt', 'qm')
				), 
        'show_all' => array(
					'title' => __('Show All link', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				),   
        'column_width' => array(
					'title' => __('Column Width (in px)', 'qm'),
					'type' => 'text',
					'std' => '200'
				),           
        'filterable_excerpt_length' => array(
					'title' => __('Excerpt Length (in characters)', 'qm'),
					'type' => 'text',
					'std' => __('100', 'qm')
				),              
        'filterable_number' => array(
					'title' => __('Total Number of blocks', 'qm'),
					'type' => 'text',
					'std' => __('6', 'qm')
				), 
        'show_pagination' => array(
					'title' => __('Show Pagination', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				),  
                            
        'filterable_lightbox' => array(
					'title' => __('Show Lightbox [Opens Full image]', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				),
        'filterable_link' => array(
					'title' => __('Show Link [Links to Post]', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				), 
        'advanced_settings' => array(
			     'title' => __('Show Advanced settings', 'qm'),
			     'type' => 'divider',
           'std' => 3
		    ),             
        'css_class' => array(
                 'title' => __('* Custom Class name (Add Custom Class to this Block)', 'qm'),
                 'type' => 'text'
                   ),
        'container_css' => array(
                  'title' => __('* Class for on containing Layout column', 'qm'),
                  'type' => 'text'
                   ),
        'custom_css' => array(
		           'title' => __('* Add Custom CSS (Use <strong>.</strong> for class name, <strong>:hover</strong> for hover styles etc..)', 'qm'),
			         'type' => 'textarea'
		          ),            
		   ),
		);

          
/* ===== Grid =======  */                
		
		$v_modules['grid'] = array(
			'name' => __('Post Grid', 'qm'),
			'options' => array(
                   
        'title' => array(
        	'title' => __('Grid Title', 'qm'),
        	'type' => 'text',
        	'std' => __('Heading', 'qm')
        ), 
        'show_title' => array(
					'title' => __('Show Title', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				),    
        'post_type' => array(
					'title' => __('Custom Post Type', 'qm'),
					'type' => 'select',
					'options' => $v_post_types,
					'std' => __('post', 'qm')
				),
        
        'taxonomy' => array(
          'title' => __('Enter Taxonomy Slug (optional)<br /><span style="font-size:11px;">(A "Taxonomy" is a grouping mechanism for posts. Like Category for Posts, Tags for Posts, Portfolio Type for Portfolio etc.. <a href="http://codex.wordpress.org/Taxonomies">more</a>)</span> ', 'qm'),
          'type' => 'text',
          'std' => ''
        ), 

        'term' => array(
          'title' => __('Enter Taxonomy Term Name (optional, only if above is selected): ', 'qm'),
          'type' => 'text',
          'std' => ''
        ),   

        'post_ids' => array(
          'title' => __('Or Enter Specific Post Ids (comma saperated)', 'qm'),
          'type' => 'text',
          'std'=>''
        ),             
        'course_style' => array(
          'title' => __('Course Types [Only for Post type = Course]', 'qm'),
          'type' => 'select',
          'options' => array(
            'recent' => 'Recently published',
            'popular' => 'Most Students',
            'rated'  => 'Highest Rated',
            'reviews' => 'Most Reviews',
            'random' => 'Random'
            ),
          'std' => __('recent', 'qm')
        ),  
        'featured_style' => array(
					'title' => __('Featured Media Block Style', 'qm'),
					'type' => 'radio_images',
					'options' => $v_thumb_styles,
					'std' => __('excerpt', 'qm')
				), 
        
        'masonry' => array(
					'title' => __('Grid Masonry Layout', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(0, 'qm')
				),     

		    'grid_columns' => array(
					'title' => __('Grid Columns', 'qm'),
					'type' => 'select',
					'options' => array(
            'clear1 col-md-12'=>'1 Columns in FullWidth',
            'clear2 col-md-6'=>'2 Columns in FullWidth',
            'clear3 col-md-4'=>'3 Columns in FullWidth',
            'clear4 col-md-3'=>'4 Columns in FullWidth',
            'clear6 col-md-2'=>'6 Columns in FullWidth'),
					'std' => 'clear3 col-md-4'
				), 

        'column_width' => array(
					'title' => __('Masonry Grid Column Width(in px)', 'qm'),
					'type' => 'text',
					'std' => '200'
				), 
        'gutter' => array(
					'title' => __('Spacing between Columns (in px)', 'qm'),
					'type' => 'text',
					'std' => '30'
				),             
        'grid_number' => array(
					'title' => __('Total Number of Blocks in Grid', 'qm'),
					'type' => 'text',
          'std' => __('6', 'qm')
				), 
                            
		    'infinite' => array(
					'title' => __('Infinite Scroll', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				), 

        'pagination' => array(
					'title' => __('Enable Pagination (If infinite scroll is off)', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				),            

        'grid_excerpt_length' => array(
					'title' => __('Excerpt Length (in characters)', 'qm'),
					'type' => 'text',
					'std' => __('100', 'qm')
				),  

        'grid_lightbox' => array(
					'title' => __('Show Lightbox [Opens Full image]', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				),

        'grid_link' => array(
					'title' => __('Show Link', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				), 

        'advanced_settings' => array(
			     'title' => __('Show Advanced settings', 'qm'),
			     'type' => 'divider',
           'std' => 3
		    ),             
        'css_class' => array(
           'title' => __('* Custom Class name (Add Custom Class to this Block)', 'qm'),
           'type' => 'text'
         ),
        'container_css' => array(
            'title' => __('* Class for on containing Layout column', 'qm'),
            'type' => 'text'
         ),
        'custom_css' => array(
           'title' => __('* Add Custom CSS (Use <strong>.</strong> for class name, <strong>:hover</strong> for hover styles etc..)', 'qm'),
	         'type' => 'textarea'
        ),   
			),
	);
		
                
/* ====== Editor ===== */                
	$v_modules['text_block'] = array(
			'name' => __('WP Editor', 'qm'),
			'options' => array(
            'title' => array(
                	'title' => __('Reference Title', 'qm'),
                	'type' => 'text',
                	'std' => __('Content', 'qm')
                         ), 
    				'text_block_content' => array(
    					'title' => __('Content', 'qm'),
    					'type' => 'wp_editor',
    					'is_content' => true
    				),
            'advanced_settings' => array(
        			'title' => __('Show Advanced settings', 'qm'),
        			'type' => 'divider',
              'std' => 4
		          ),             
            'animation_effect' => array(
               'title' => __('* On-Load CSS3 Animation effect on the block (<a href="http://quantimodo.com/forums/showthread.php?914-CSS3-Animation-Effects&p=2488" target="_blank">more</a>)', 'qm'),
               'type' => 'select',
               'options' => animation_effects(),
               'std' => ''
             ),             
            'css_class' => array(
               'title' => __('* Custom Class name (Add Custom Class to this Block)', 'qm'),
               'type' => 'text'
             ),
            'container_css' => array(
              'title' => __('* Class for on containing Layout column', 'qm'),
              'type' => 'text'
             ),
            'custom_css' => array(
		           'title' => __('* Add Custom CSS (Use <strong>.</strong> for class name, <strong>:hover</strong> for hover styles etc..)', 'qm'),
			         'type' => 'textarea'
		          ),     
			)
		);


/* ====== Parallax ===== */                
    $v_modules['parallax_block'] = array(
      'name' => __('Parallax Content', 'qm'),
      'options' => array(
            'title' => array(
                  'title' => __('Reference Title', 'qm'),
                  'type' => 'text',
                  'std' => __('Parallax Title', 'qm')
                ), 
              'text_block_content' => array(
                'title' => __('Content', 'qm'),
                'type' => 'wp_editor',
                'is_content' => true
              ),
              'bg_image' => array(
                    'title' => __('Upload Parallax Background image', 'qm'),
                    'type' => 'upload',
                    'std' => ''
                ), 
             'rev' => array(
                  'title' => __('Background Effect', 'qm'),
                  'type' => 'select',
                  'options' => array(
                        ''=>'Image Scrolls with scroll',
                        1=>'Image Static with Scroll'),
                  'std' => ''
                ),  
              'height' => array(
                  'title' => __('Parallax Block Height (in px)', 'qm'),
                  'type' => 'text',
                  'std' => '200'
                ), 
               'scroll' => array(
                    'title' => __('Parallax value (Scroll senstivity, lower value means higher scroll)', 'qm'),
                    'type' => 'text',
                    'std' => '2'
                ),           
               'advanced_settings' => array(
                      'title' => __('Show Advanced settings', 'qm'),
                      'type' => 'divider',
                        'std' => 4
                    ),       
                'animation_effect' => array(
                         'title' => __('* On-Load CSS3 Animation effect on the block (<a href="http://quantimodo.com/forums/showthread.php?914-CSS3-Animation-Effects&p=2488" target="_blank">more</a>)', 'qm'),
                         'type' => 'select',
                         'options' => animation_effects(),
                         'std' => ''
                           ),            
                'css_class' => array(
                         'title' => __('* Custom Class name (Add Custom Class to this Block)', 'qm'),
                         'type' => 'text'
                           ),
                'container_css' => array(
                          'title' => __('* Class for on containing Layout column', 'qm'),
                          'type' => 'text'
                           ),
                'custom_css' => array(
                         'title' => __('* Add Custom CSS (Use <strong>.</strong> for class name, <strong>:hover</strong> for hover styles etc..)', 'qm'),
                          'type' => 'textarea'
                        ),     
      )
    );                
            
/* ====== RevSlider ===== */
                
if ( in_array( 'revslider/revslider.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  $revsliders = array();
  // Fetch all Revolution Slider list
  global $wpdb;
  $table_name = $wpdb->prefix . "revslider_sliders"; 
   $querystr = "
          SELECT title,alias
          FROM $table_name";

           $rev_sliders = $wpdb->get_results($querystr, OBJECT);
           
foreach($rev_sliders as $sliders){ 
  $revsliders[$sliders->alias] = $sliders->title;
}

           
$v_modules['revslider'] = array(
			'name' => __('Revolution Slider', 'qm'),
			'options' => array(
             'alias' => array(
    		             'title' => __('Select Slider', 'qm'),
    		             'type' => 'select',
                     'options' => $revsliders
              ),  
              'advanced_settings' => array(
                			'title' => __('Show Advanced settings', 'qm'),
                			'type' => 'divider',
                      'std' => 3
              ),             
              'css_class' => array(
                       'title' => __('* Custom Class name (Add Custom Class to this Block)', 'qm'),
                       'type' => 'text'
               ),
              'container_css' => array(
                        'title' => __('* Class for on containing Layout column', 'qm'),
                        'type' => 'text'
               ),
              'custom_css' => array(
                        'title' => __('* Add Custom CSS (Use <strong>.</strong> for class name, <strong>:hover</strong> for hover styles etc..)', 'qm'),
			                   'type' => 'textarea'
		          ),    
			)
    ); 
}
                
if ( in_array( 'LayerSlider/layerslider.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                    
                    // Fetch all Layer Slider list
$layersliders = array();
global $wpdb;
$table_name = $wpdb->prefix . "layerslider"; 
$querystr = "
      SELECT id,name
      FROM $table_name";
       $layer_sliders = $wpdb->get_results($querystr, OBJECT);
       
       foreach($layer_sliders as $sliders){ 
          $layersliders[$sliders->id] = $sliders->name;
       }
$v_modules['layerslider'] = array(
			'name' => __('Layer Slider', 'qm'),
			'options' => array(
				'id' => array(
      					'title' => __('Select Slider', 'qm'),
      					'type' => 'select',
                'options' => $layersliders
				        ),  
        'advanced_settings' => array(
          			'title' => __('Show Advanced settings', 'qm'),
          			'type' => 'divider',
                'std' => 3
		            ),             
        'css_class' => array(
                 'title' => __('* Custom Class name (Add Custom Class to this Block)', 'qm'),
                 'type' => 'text'
               ),
        'container_css' => array(
                  'title' => __('* Class for on containing Layout column', 'qm'),
                  'type' => 'text'
               ),
        'custom_css' => array(
		           'title' => __('* Add Custom CSS (Use <strong>.</strong> for class name, <strong>:hover</strong> for hover styles etc..)', 'qm'),
			           'type' => 'textarea'
		          ),    
			   )
  ); 
}
                
                
                //Sidebars
$v_modules['widget_area'] = array(
  'name' => __('Sidebar', 'qm'),
  'options' => array(
      	'area' => array(
          		'title' => __('Select a Sidebar', 'qm'),
          		'type' => 'select',
          		'options' => $v_widget_areas,
          		'std' => __('MainSidebar', 'qm')
          	),
          'advanced_settings' => array(
              'title' => __('Show Advanced settings', 'qm'),
              'type' => 'divider',
              'std' => 3
             ),             
          'css_class' => array(
             'title' => __('* Custom Class name (Add Custom Class to this Block)', 'qm'),
             'type' => 'text'
           ),
          'container_css' => array(
              'title' => __('* Class for on containing Layout column', 'qm'),
              'type' => 'text'
           ),
          'custom_css' => array(
               'title' => __('* Add Custom CSS (Use <strong>.</strong> for class name, <strong>:hover</strong> for hover styles etc..)', 'qm'),
                'type' => 'textarea'
          ),   
      )
);
		
		
$v_modules['slider'] = array(
			'name' => __('FlexSlider', 'qm'),
			'options' => array(
          'title' => array(
      					'title' => __('Slider ID (for reference & Css)', 'qm'),
      					'type' => 'text',
                'std' => 'FlexSlider'
				    ),
            'slide_style' => array(
                  'title' => __('Slide Style', 'qm'),
				          'type' => 'radio_images',
                  'options'=>array(
                                  'slide1'=> plugins_url('images/slider_1.png',__FILE__),
                                  'slide2'=> plugins_url('images/slider_2.png',__FILE__),
                                  'slide3'=> plugins_url('images/slider_3.png',__FILE__),
                                  'slide4'=> plugins_url('images/slider_4.png',__FILE__),
                                  'slide5'=> plugins_url('images/slider_5.png',__FILE__),
                          ),
                  'std' => 'slide1'
              ),
              'animation' => array(
        					'title' => __('Animation Effect', 'qm'),
        					'type' => 'select',
        					'options' => array( 'fade'=>__('fade', 'qm'),'slide'=> __('slide', 'qm') ),
        					'std' => 'fade'
        				),
                                
              'slider_settings' => array(
                  'title' => __('Slider settings', 'qm'),
                  'type' => 'divider',
                  'std' => 12
              ),
      				'auto_slide' => array(
      					'title' => __('Auto slide Images', 'qm'),
      					'type' => 'select_yesno',
      					'options' => array(0=>'No',1=>'Yes'),
      					'std' => __(1, 'qm')
      				),
              'loop' => array(
        					'title' => __('Loop Slides', 'qm'),
        					'type' => 'select_yesno',
        					'options' => array(0=>'No',1=>'Yes'),
        					'std' => __(1, 'qm')
        				),
                'randomize' => array(
        					'title' => __('Randomize Slides', 'qm'),
        					'type' => 'select_yesno',
        					'options' => array(0=>'No',1=>'Yes'),
        					'std' => __(1, 'qm')
        				),
                'show_directionnav' => array(
            					'title' => __('Show Slider Direction arrows', 'qm'),
            					'type' => 'select_yesno',
            					'options' => array(0=>'No',1=>'Yes'),
            					'std' => __(1, 'qm')
            				),
                'show_controlnav' => array(
          					'title' => __('Show Slider Control buttons', 'qm'),
          					'type' => 'select_yesno',
          					'options' => array(0=>'No',1=>'Yes'),
          					'std' => __(1, 'qm')
          				),
				'animation_duration' => array(
					'title' => __('Animation Duration (in ms)', 'qm'),
					'type' => 'text',
					'std' => '600'
				),
				
				'auto_speed' => array(
					'title' => __('Auto Animation Speed (in ms)', 'qm'),
					'type' => 'text',
					'std' => '7000'
				),
				'pause_on_hover' => array(
					'title' => __('Pause Slider On Hover', 'qm'),
					'type' => 'select_yesno',
					'options' => array(0=>'No',1=>'Yes'),
					'std' => __(1, 'qm')
				),
                                
        'css_class' => array(
            'title' => __('* Custom Class name (Add Custom Class to this Block)', 'qm'),
            'type' => 'text'
       ),
        'container_css' => array(
          'title' => __('* Class for on containing Layout column', 'qm'),
          'type' => 'text'
       ),
        'custom_css' => array(
          'title' => __('* Add Custom CSS (Use <strong>.</strong> for class name, <strong>:hover</strong> for hover styles etc..)', 'qm'),
          'type' => 'textarea'
      ), 
                                
  		'images' => array(
  			'type' => 'slider_images',
        'std' => 'slides'
  		),
      'advanced_settings' => array(
         ),            
                               
			)
);
        
                
		$v_modules = apply_filters( 'v_modules', $v_modules );
		
		$v_columns['1_2'] = array( 'name' => __('1/2 Column', 'qm') );
		$v_columns['1_3'] = array( 'name' => __('1/3 Column', 'qm') );
		$v_columns['1_4'] = array( 'name' => __('1/4 Column', 'qm') );
		$v_columns['2_3'] = array( 'name' => __('2/3 Column', 'qm') );
		$v_columns['3_4'] = array( 'name' => __('3/4 Column', 'qm') );
		$v_columns['resizable'] = array( 'name' => __('Full-Width Resizable Column', 'qm') );
		$v_columns['stripe_container'] = array( 'name' => __('FullScreen Stripe with Container', 'qm') );
    $v_columns['stripe'] = array( 'name' => __('FullScreen Stripe', 'qm') );
                
		$v_columns = apply_filters( 'v_columns', $v_columns );
		$v_sample_layouts='';
		$v_sample_layouts = get_option('qm_builder_sample_layouts');
                if(is_string($v_sample_layouts))
                    $v_sample_layouts = unserialize($v_sample_layouts);
                
		foreach( $v_columns as $v_column_key => $v_column ){
			add_shortcode("v_{$v_column_key}", 'qm_new_column');
			add_shortcode("v_alt_{$v_column_key}", 'qm_new_alt_column');
		}
		
	}

	function qm_qm_layout_editor(){
		global $v_modules, $v_columns, $v_sample_layouts, $post;
		$v_helper_class = '';
		$v_convertible_settings = get_post_meta( $post->ID, '_builder_settings', true );

	?>
		<?php do_action( 'before_page_builder' ); ?>
		
		<div id="page_builder">
			<div id="qm_editor_controls" class="clearfix">
				<a href="#" class="add_element add_column"><span><i class="dashicons dashicons-screenoptions"></i> <?php _e('COLUMNS', 'qm'); ?></span></a>
				<a href="#" class="add_element add_module"><span><i class="dashicons dashicons-welcome-widgets-menus"></i> <?php _e('CONTENT', 'qm'); ?></span></a>
				<a href="#" class="add_element add_sample_layout"><span><i class="dashicons dashicons-feedback"></i> <?php _e('SAVED LAYOUTS', 'qm'); ?></span></a>
			</div> <!-- #qm_editor_controls -->
			
			<div id="modules">
				<?php

					foreach ( $v_modules as $module_key => $module_settings ){
						$class = "module m_{$module_key}";
						if ( isset( $module_settings['full_width'] ) && $module_settings['full_width'] ) $class .= ' full_width';
						
						echo "<div data-placeholder='" . esc_attr( $module_settings['name'] ) . "' data-name='" . esc_attr( $module_key ) . "' class='" . esc_attr( $class ) . "'>" . '<span class="module_name">' . esc_html( $module_settings['name'] ) . '</span>' .
						'<span class="move"></span><span class="delete"></span><span class="settings_arrow"></span><div class="module_settings"></div></div>';
					}
					
					foreach ( $v_columns as $column_key => $column_settings ){
						echo "<div data-placeholder='" . esc_attr( $column_settings['name'] ) . "' data-name='" . esc_attr( $column_key ) . "' class='" . esc_attr( "module m_column m_column_{$column_key}" ) . "'>" . 
						'<span class="module_name column_name">' . esc_html( $column_settings['name'] ) . '</span>' .
						'<span class="move"></span> <span class="delete_column delete"></span></div>';
					}

					if(is_array($v_sample_layouts))
					foreach ( $v_sample_layouts as $layout_key => $layout_settings ){
						echo "<div data-placeholder='" . esc_attr( $layout_settings['name'] ) . "' data-name='" . esc_attr( $layout_key ) . "' class='" . esc_attr( "module sample_layout" ) . "'>" . 
						'<span class="module_name">' . esc_html( $layout_settings['name'] ) . '</span>' .
						'<span class="move"></span></div>';
					}
				?>
				<div id="module_separator"></div>
				<div id="active_module_settings"></div>
			</div> <!-- #modules -->
			
			<div id="layout_container">
				<div id="layout" class="clearfix">
					<?php 
						if ( is_array( $v_convertible_settings ) && $v_convertible_settings['layout_html'] ) {
							echo stripslashes( $v_convertible_settings['layout_html'] );
							$v_helper_class = ' class="hidden"';
						}
					?>
				</div> <!-- #layout -->
				<div id="v_helper"<?php echo $v_helper_class; ?>><?php esc_html_e('Drag & Drop Layout Columns and then Drag & Drop Content Blocks to each column', 'qm'); ?></div>
			</div> <!-- #layout_container -->
			
			<div style="display: none;">
				<?php
					wp_editor( ' ', 'v_hidden_editor');
					do_action( 'v_hidden_editor' );
				?>
			</div>
		</div> <!-- #page_builder -->
                <div class="overlay">
                                <label><?php _e('Enter name of Sample Layout','qm'); ?></label><input type="text" class="text" id="new_sample_layout_name" name="new_sample_layout_name" data-id="<?php global $post; echo $post->ID;?>"/>
                                <a id="save_new_sample_layout" class="qm-button-save-new-layout"><?php _e('Save Layout', 'qm') ?></a>
                                <span class="remove"></span>
                </div>
		<div id="v_ajax_save">
			<img src="<?php echo plugins_url('images/loading.gif',__FILE__ ); ?>" alt="loading" id="loading" />
			<span><?php esc_html_e( 'Saving...', 'qm' ); ?></span>
		</div>
		
		<?php
			echo '<div id="v_save">';
                        submit_button( __('Save Changes', 'qm'), 'qm-button-save', 'v_main_save' );
			echo '<a id="new_sample_layout" class="qm-button-save-new-layout" style="display:none;">'. __('Save as New Layout', 'qm').'</a>';
			echo '</div> <!-- end #v_save -->';
	}

	add_action( 'wp_ajax_qm_save_layout', 'qm_save_layout' );
  //add_action( 'save_post',  'qm_save_layout');
        
	function qm_save_layout(){
		if ( ! wp_verify_nonce( $_POST['load_nonce'], 'load_nonce' ) ) die(-1);
		
		$v_convertible_settings = array();
		
		$v_convertible_settings['layout_html'] = trim( $_POST['layout_html'] );
		$v_convertible_settings['layout_shortcode'] = $_POST['layout_shortcode'];
		$v_post_id = (int) $_POST['post_id'];

		if ( get_post_meta( $v_post_id, '_builder_settings', true ) ) 
      update_post_meta( $v_post_id, '_builder_settings', $v_convertible_settings );
		else 
      add_post_meta( $v_post_id, '_builder_settings', $v_convertible_settings, true );
		
		die();
	}

	add_action( 'wp_ajax_append_layout', 'qm_new_append_layout' );
	function qm_new_append_layout(){
		global $v_sample_layouts;
		
		if ( ! wp_verify_nonce( $_POST['load_nonce'], 'load_nonce' ) ) die(-1);
		
		$layout_name = $_POST['layout_name'];
		if ( isset( $v_sample_layouts[$layout_name] ) ) echo stripslashes( $v_sample_layouts[$layout_name]['content'] );
		
		die();
	}
        
        add_action( 'wp_ajax_save_new_layout', 'qm_save_new_layout' );
	function qm_save_new_layout(){
		if ( ! wp_verify_nonce( $_POST['load_nonce'], 'load_nonce' ) ) die(-1);
		global $qm_options;
                $name = stripslashes($_POST['name']);
                $postid = stripslashes($_POST['id']);
                
                $layout = get_post_meta($postid,'_builder_settings');
                
                echo $layout[0]['layout_html'];
                
                if(isset($layout[0]['layout_html'])){
                $n = count($qm_options['sample_layouts']);
                $qm_options['sample_layouts'][$n]=$name;   
                $value = get_option('qm_builder_sample_layouts');
                if(isset($value)){
                    
                    if(is_string($value))
                    $value=  unserialize($value);
                    $value[]=array('name'=>$name,
                                    'content'=>$layout[0]['layout_html']);
                    
                    $value=serialize($value);
                    update_option('qm_builder_sample_layouts',$value);
                }else{
                    $value[]=array('name'=>$name,
                                    'content'=>$layout[0]['layout_html']);
                    $value=serialize($value);
                    add_option('qm_builder_sample_layouts',$value);
                }
                update_option('qm_builder_sample_layouts',$value);
                }else{
                    echo 'unable to save';
                }
                die();
            }
        


	if ( ! function_exists('generate_column_options') ){
		function generate_column_options( $column_name, $paste_to_editor_id ){
			global $v_columns;
			
			$module_name = $v_columns[$column_name]['name'];
			echo '<form id="dialog_settings">'
					. '<span id="settings_title">' . esc_html( ucfirst( $module_name ) . ' ' . __('Settings', 'qm') ) . '</span>'
					. '<a href="#" id="close_dialog_settings"></a>'
					. '<p class="clearfix"><input type="checkbox" id="dialog_first_class" name="dialog_first_class" value="" class="v_option" /> ' . esc_html__('This is the first column in the row', 'qm') . '</p>';
			
			if ( 'resizable' == $column_name ) echo '<p class="clearfix"><label>' . esc_html__('Column width (%)', 'qm') . ':</label> <input name="dialog_width" type="text" id="dialog_width" value="100" class="regular-text v_option" /></p>';
			
			submit_button(__('Save Changes', 'qm'), 'qm-button-save');
			
			echo '<input type="hidden" id="saved_module_name" value="' . esc_attr( "alt_{$column_name}" ) . '" />';
			
			if ( '' != $paste_to_editor_id ) echo '<input type="hidden" id="paste_to_editor_id" value="' . esc_attr( $paste_to_editor_id ) . '" />';
			
			echo '</form>';
		}
	}

	if ( ! function_exists('generate_module_options') ){
		function generate_module_options( $module_name, $module_window, $paste_to_editor_id, $v_module_exact_name ){
			global $v_modules;
			
			$i = 1;
			$form_id = ( 0 == $module_window ) ? 'module_settings' : 'dialog_settings';
			
			echo '<form id="' . esc_attr( $form_id ) . '">';
			echo '<span id="settings_title">' . esc_html( $v_module_exact_name . ' ' . __('Settings', 'qm') ) . '</span>';
			
			if ( 0 == $module_window ) echo '<a href="#" id="close_module_settings"></a>';
			else echo '<a href="#" id="close_dialog_settings"></a>';
			
			foreach ( $v_modules[$module_name]['options'] as $option_slug => $option_settings ){
				$content_class = isset( $option_settings['is_content'] ) && $option_settings['is_content'] ? ' v_module_content' : '';
				
				echo '<p class="clearfix">';
				if ( isset( $option_settings['title'] ) ) echo "<label>{$option_settings['title']}</label>";
				
				if ( 1 == $module_window ) $option_slug = 'dialog_' . $option_slug;
				
				switch ( $option_settings['type'] ) {
					case 'wp_editor': 

						wp_editor( '', $option_slug, array(
              'editor_class' => 'wp_editor_area v_wp_editor v_option' . $content_class,
              'media_buttons' => true,
              'quicktags'     => TRUE,
            ));

						break;
					
					case 'select':
						$std = isset( $option_settings['std'] ) ? $option_settings['std'] : '';
						echo
						'<select name="' . esc_attr( $option_slug ) . '" id="' . esc_attr( $option_slug ) . '" class="chzn-select v_option' . $content_class . '">'
							. ( ( '' == $std ) ? '<option value="nothing_selected">  ' . esc_html__('Select', 'qm') . '  </option>' : '' );
							
              foreach ( $option_settings['options'] as $key=>$setting_value ){ 
								echo '<option value="' . esc_attr( $key ) . '"' . selected( $key, $std, false ) . '>' . esc_html( $setting_value ) . '</option>';
							}
						echo '</select>';
						break;
            
            case 'multiselect':
						$std = isset( $option_settings['std'] ) ? $option_settings['std'] : '';
						echo
						'<select name="' . esc_attr( $option_slug ) . '" id="' . esc_attr( $option_slug ) . '" class="chzn-select v_option' . $content_class . '" multiple=multiple style="min-width:300px;" data-placeholder="Choose options...">'
							. ( ( '' == $std ) ? '<option value="nothing_selected">  ' . esc_html__('Select', 'qm') . '  </option>' : '' );
							
                                                foreach ( $option_settings['options'] as $key=>$setting_value ){ 
                                                    $value_array=explode(',',$std);
								echo '<option value="' . esc_attr( $key ) . '"' . (in_array( $key, $value_array )?'selected="SELECTED"':'') . '>' . esc_html( $setting_value ) . '</option>';
							}
						echo '</select>';
						break;        
            
            case 'radio_images':
						$std = isset( $option_settings['std'] ) ? $option_settings['std'] : '';
						foreach ( $option_settings['options'] as $key=>$setting_value ){ 
                                                    echo '<label class="radio_images" data-value="'.$key.'"><img src="' . esc_html( $setting_value ) . '" for="' . esc_attr( $option_slug ) . '" />
                                                                      </label>';
							}
                                                echo '<input name="' . esc_attr( $option_slug ) . '" type="hidden" id="' . esc_attr( $option_slug ) . '" value="'.( '' != $std ? esc_attr( $std ) : '' ).'" class="image_value v_option' . $content_class . '" />';
						break; 
            
            case 'select_yesno':
						$std = isset( $option_settings['std'] ) ? $option_settings['std'] : '';
						echo
						'<span class="select_yesno_button"></span>
                                                    <select name="' . esc_attr( $option_slug ) . '" id="' . esc_attr( $option_slug ) . '" class="select_yesno_val v_option' . $content_class . '">'
							. ( ( '' == $std ) ? '<option value="nothing_selected">  ' . esc_html__('Select', 'qm') . '  </option>' : '' );
							
                                                foreach ( $option_settings['options'] as $key=>$setting_value ){ 
								echo '<option value="' . esc_attr( $key ) . '"' . selected( $key, $std, false ) . '>' . esc_html( $setting_value ) . '</option>';
							}
						echo '</select>';
						break;  

					 case 'text':
						$std = isset( $option_settings['std'] ) ? $option_settings['std'] : '';
						echo '<input name="' . esc_attr( $option_slug ) . '" type="text" id="' . esc_attr( $option_slug ) . '" value="'.( '' != $std ? esc_attr( $std ) : '' ).'" class="text regular-text v_option' . $content_class . '" />';
						break;
            
            case 'textarea':
						$std = isset( $option_settings['std'] ) ? $option_settings['std'] : '';
						echo '<textarea name="' . esc_attr( $option_slug ) . '" id="' . esc_attr( $option_slug ) . '"  class="textarea regular-text v_option' . $content_class . '" row="5">'.( '' != $std ? esc_attr( $std ) : '' ).'</textarea>';
						break; 
            
            case 'divider':
            $std = isset( $option_settings['std'] ) ? $option_settings['std'] : '';
						echo '<span class="divider" rel-hide="'.$std.'"></span><i class="toggle closed"></i>';
						break; 
            
            case 'upload':
						echo '<input name="' . esc_attr( $option_slug ) . '" type="hidden" id="' . esc_attr( $option_slug ) . '" value="" class="regular-text v_option v_upload_field' . $content_class . '" />' . '<img src="'.qm_URL.'/includes/metaboxes/images/image.png" class="uploaded_image" /><a href="#" rel-default="'.qm_URL.'/includes/metaboxes/images/image.png" class="remove_uploaded">cancel</a><a href="#" class="v_upload_button button">' . esc_html__('Upload', 'qm') . '</a>';
						break;
					 case 'slider_images':
            $std = isset( $option_settings['std'] ) ? $option_settings['std'] : '';
						echo '<div id="v_slider_images">' . '<div id="'.$std.'" class="slides v_option "></div>' . '<a href="#" id="v_add_slider_images" class="button button-primary button-large">' . esc_html__('Add Slider Image', 'Convertible') . '</a>' . '</div>';
						break;      
				}
				
				echo '</p>';
				
				++$i;
			}
			
			submit_button(__('Save Changes', 'qm'), 'qm-button-save');
			
			echo '<input type="hidden" id="saved_module_name" value="' . esc_attr( $module_name ) . '" />';
			
			if ( '' != $paste_to_editor_id ) echo '<input type="hidden" id="paste_to_editor_id" value="' . esc_attr( $paste_to_editor_id ) . '" />';
			
			echo '</form>';
		}
	}

	if ( ! function_exists('v_get_attributes') ){
		function v_get_attributes( $atts, $additional_classes = '', $additional_styles = '' ){
			extract( shortcode_atts(array(
            'container_css'=>'',
						'css_class' => '',
						'first_class' => '0',
						'width' => ''
					), $atts));
			$attributes = array( 'class' => '', 'inline_styles' => '' );
                        
			if ( '' != $css_class ) $css_class = ' ' . $css_class;
                        if ( '' != $container_css ) $container_css = 'data-class="' . $container_css.'"';
                        
			if ( '' != $additional_classes ) $additional_classes = ' ' . $additional_classes;
			$first_class = ( '1' == $first_class ) ? ' v_first' : ' ';
            
            $animation ='';
            if(isset($atts['animation_effect']) && $atts['animation_effect']){
            $animation = ' '.$atts['animation_effect'].'';
            }
                        
			$attributes['class'] = ' class="' . esc_attr( "v_module{$additional_classes}{$first_class}{$css_class}{$animation}" ) . '" '.$container_css.'';
			
			if ( '' != $width ) $attributes['inline_styles'] .= " width: {$width}%;";
			$attributes['inline_styles'] .= $additional_styles;
			if ( '' != $attributes['inline_styles'] ) $attributes['inline_styles'] = ' style="' . esc_attr( $attributes['inline_styles'] ) .'"';
			
			return $attributes;
		}
	}

	if ( ! function_exists('v_fix_shortcodes') ){
		function v_fix_shortcodes($content){   
			/*$replace_tags_from_to = array (
				'<p>[' => '[', 
				']</p>' => ']', 
				']<br />' => ']'
			);
			return strtr( $content, $replace_tags_from_to );*/
      return $content;
		}
	}
	
add_action( 'before_page_builder', 'qm_disable_builder_option' );
function qm_disable_builder_option(){
	global $post;
	
	$v_builder_enable = get_post_meta( $post->ID, '_enable_builder', true );
	
	wp_nonce_field( basename( __FILE__ ), 'qm_editor_settings_nonce' );

	echo '<p class="qm_editor_option">'
			. '<label for="builder_disable" class="builder_enable">'
				. '<input name="builder_enable" type="checkbox" id="builder_enable" ' . checked( $v_builder_enable, 1, false ) . ' /></label>'
		. '</p>';
}

add_action( 'before_page_builder', 'qm_add_content_option' );
function qm_add_content_option(){
	global $post;
	
	$v_add_content = get_post_meta( $post->ID, '_add_content', true );
	
	wp_nonce_field( basename( __FILE__ ), 'qm_editor_settings_nonce' );

	echo '<p class="qm_editor_option content_addon">'
			. '<label for="add_content">'
				. __('Show Page Content','qm').'<select name="add_content" id="add_content" ><option value="no" '. selected($v_add_content, 'no', false).'> No</option><option value="yes_top" '. selected($v_add_content, 'yes_top', false).'> Yes, above Page Builder</option><option value="yes_below" '. selected($v_add_content, 'yes_below', false).'> Yes, Below Page Builder</option></select></label>'
		. '</p>';
}

add_action( 'save_post', 'qm_editor_save_details', 10, 2 );
function qm_editor_save_details( $post_id, $post ){
	global $pagenow;

	if ( 'post.php' != $pagenow ) return $post_id;
		
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;
		
	if ( ! isset( $_POST['qm_editor_settings_nonce'] ) || ! wp_verify_nonce( $_POST['qm_editor_settings_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	if ( isset( $_POST['builder_enable'] ) )
		update_post_meta( $post_id, '_enable_builder', 1 );
	else
		update_post_meta( $post_id, '_enable_builder', 0 );
        
        if ( isset( $_POST['add_content'] ) )
		update_post_meta( $post_id, '_add_content', $_POST['add_content'] );
	else
		update_post_meta( $post_id, '_add_content', 'no' );
       
}

add_filter( 'the_content', 'qm_show_builder_layout');
function qm_show_builder_layout( $content ){
	global $post;
	
	$builder_enable = get_post_meta( $post->ID, '_enable_builder', true );
  $builder_layout = get_post_meta( $post->ID, '_builder_settings', true );
  $add_content = get_post_meta( $post->ID, '_add_content', true );
	
        
            
	if ( ! is_singular() || ! $builder_layout || ! is_main_query() || 0 == $builder_enable ) return $content;
	
       
        
        
	if ( isset($builder_layout) && '' != $builder_layout['layout_shortcode'] && $add_content == 'no') { 
           
            $content = '<div class="qm_editor clearfix">' . 
                do_shortcode( stripslashes( $builder_layout['layout_shortcode'] ) ) . 
                '</div>';
          
        }
        
        if ( $builder_layout && '' != $builder_layout['layout_shortcode'] && $add_content == 'yes_top') {
            $content = $content.'<div class="qm_editor clearfix">' . 
                do_shortcode( stripslashes( $builder_layout['layout_shortcode'] ) ) . 
                '</div>';
        }
        
        if ( $builder_layout && '' != $builder_layout['layout_shortcode'] && $add_content == 'yes_below') {
            $content = '<div class="qm_editor clearfix">' . 
                do_shortcode( stripslashes( $builder_layout['layout_shortcode'] ) ) . 
                '</div>'.$content;
        }
        
        
	return $content;
} 

?>