<?php
if ( !defined( 'ABSPATH' ) ) exit;

function add_qm_metaboxes_study(){
	$prefix = 'quantimodo_';
	$perfixtwo = 'study-options-';
	$sidebars=$GLOBALS['wp_registered_sidebars'];
	$sidebararray=array();
	foreach($sidebars as $sidebar){
	    $sidebararray[]= array('label'=>$sidebar['name'],'value'=>$sidebar['id']);
	}

	$post_metabox = array(
		 
		
		 array( // Single checkbox
			'label'	=> __('Post Sub-Title','qm'), // <label>
			'desc'	=> __('Post Sub- Title.','qm'), // description
			'id'	=> $prefix.'subtitle', // field id and name
			'type'	=> 'textarea', // type of field
	        'std'   => ''
	                ), 

	     array( // Single checkbox
			'label'	=> __('Post Template','qm'), // <label>
			'desc'	=> __('Select a post template for showing content.','qm'), // description
			'id'	=> $prefix.'template', // field id and name
			'type'	=> 'select', // type of field
	        'options' => array(
	                    1=>array('label'=>'Default','value'=>''),
	                    2=>array('label'=>'Content on Right','value'=>'right'),
	                    3=>array('label'=>'Content on Left','value'=>'left'),
	        ),
	        'std'   => ''
		),
	     array( // Single checkbox
			'label'	=> __('Sidebar','qm'), // <label>
			'desc'	=> __('Select a Sidebar | Default : mainsidebar','qm'), // description
			'id'	=> $prefix.'sidebar', // field id and name
			'type'	=> 'select',
	                'options' => $sidebararray
	                ),
	    array( // Single checkbox
			'label'	=> __('Show Page Title','qm'), // <label>
			'desc'	=> __('Show Page/Post Title.','qm'), // description
			'id'	=> $prefix.'title', // field id and name
			'type'	=> 'showhide', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	                'std'   => 'S'
	                ),
	    array( // Single checkbox
			'label'	=> __('Show Author Information','qm'), // <label>
			'desc'	=> __('Author information below post content.','qm'), // description
			'id'	=> $prefix.'author', // field id and name
			'type'	=> 'showhide', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	                'std'   => 'H'
		),    
	     
	    array( // Single checkbox
			'label'	=> __('Show Breadcrumbs','qm'), // <label>
			'desc'	=> __('Show breadcrumbs.','qm'), // description
			'id'	=> $prefix.'breadcrumbs', // field id and name
			'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	                'std'   => 'S'
	            ),
	    array( // Single checkbox
			'label'	=> __('Show Prev/Next Arrows','qm'), // <label>
			'desc'	=> __('Show previous/next links on top below the Subheader.','qm'), // description
			'id'	=> $prefix.'prev_next', // field id and name
			'type'	=> 'showhide', // type of field
	         'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	                'std'   => 'H'
		),
	);

	$page_metabox = array(
			

	        0 => array( // Single checkbox
			'label'	=> __('Show Page Title','qm'), // <label>
			'desc'	=> __('Show Page/Post Title.','qm'), // description
			'id'	=> $prefix.'title', // field id and name
			'type'	=> 'showhide', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	                'std'   => 'S'
	                ),


	        1 => array( // Single checkbox
			'label'	=> __('Page Sub-Title','qm'), // <label>
			'desc'	=> __('Page Sub- Title.','qm'), // description
			'id'	=> $prefix.'subtitle', // field id and name
			'type'	=> 'textarea', // type of field
	        'std'   => ''
	                ),

	        2 => array( // Single checkbox
			'label'	=> __('Show Breadcrumbs','qm'), // <label>
			'desc'	=> __('Show breadcrumbs.','qm'), // description
			'id'	=> $prefix.'breadcrumbs', // field id and name
			'type'	=> 'showhide', // type of field
	         'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	                'std'   => 'S'
	            ),
	    3 => array( // Single checkbox
			'label'	=> __('Sidebar','qm'), // <label>
			'desc'	=> __('Select Sidebar | Sidebar : mainsidebar','qm'), // description
			'id'	=> $prefix.'sidebar', // field id and name
			'type'	=> 'select',
	                'options' => $sidebararray
	                ),
	    );



	$featured_metabox = array(
	     array( // Select box
			'label'	=> __('Media','qm'), // <label>
			'id'	=> $prefix.'select_featured', // field id and name
			'type'	=> 'select', // type of field
			'options' => array ( // array of options
	                        'zero' => array ( // array key needs to be the same as the option value
					'label' => __('Disable','qm'), // text displayed as the option
					'value'	=> 'disable' // value stored for the option
				),
				'one' => array ( // array key needs to be the same as the option value
					'label' => __('Gallery','qm'), // text displayed as the option
					'value'	=> 'gallery' // value stored for the option
				),
				'two' => array (
					'label' => __('Self Hosted Video','qm'),
					'value'	=> 'video'
				),
	                        'three' => array (
					'label' => __('IFrame Video','qm'),
					'value'	=> 'iframevideo'
				),
				'four' => array (
					'label' => __('Self Hosted Audio','qm'),
					'value'	=> 'audio'
				),
	                        'five' => array (
					'label' => __('Other','qm'),
					'value'	=> 'other'
				)
			)
		),
	    
	        
	        array( // Repeatable & Sortable Text inputs
			'label'	=> __('Gallery','qm'), // <label>
			'desc'	=> __('Create a Gallery in post.','qm'), // description
			'id'	=> $prefix.'slider', // field id and name
			'type'	=> 'gallery' // type of field
		),
	        
		array( // Textarea
			'label'	=> __('Self Hosted Video','qm'), // <label>
			'desc'	=> __('Select video files (of same Video): xxxx.mp4,xxxx.ogv,xxxx.ogg for max. browser compatibility','qm'), // description
			'id'	=> $prefix.'featuredvideo', // field id and name
			'type'	=> 'video' // type of field
		),
	        array( // Textarea
			'label'	=> __('IFRAME Video','qm'), // <label>
			'desc'	=> __('Insert Iframe (Youtube,Vimeo..) embed code of video ','qm'), // description
			'id'	=> $prefix.'featurediframevideo', // field id and name
			'type'	=> 'textarea' // type of field
		),
	        array( // Text Input
			'label'	=> __('Audio','qm'), // <label>
			'desc'	=> __('Select audio files (of same Audio): xxxx.mp3,xxxx.wav,xxxx.ogg for max. browser compatibility','qm'), // description
			'id'	=> $prefix.'featured_audio', // field id and name
			'type'	=> 'audio' // type of field
		),
	        array( // Textarea
			'label'	=> __('Other','qm'), // <label>
			'desc'	=> __('Insert Shortcode or relevant content.','qm'), // description
			'id'	=> $prefix.'featuredother', // field id and name
			'type'	=> 'textarea' // type of field
		)
		
	    );




	$study_metabox = array(  
		array( // Single checkbox
			'label'	=> __('Sidebar','qm'), // <label>
			'desc'	=> __('Select a Sidebar | Default : mainsidebar','qm'), // description
			'id'	=> $prefix.'sidebar', // field id and name
			'type'	=> 'select',
	        'options' => $sidebararray,
	        'std'=>'studysidebar'
	        ),
		array( // Text Input
			'label'	=> __('Total Duration of Study','qm'), // <label>
			'desc'	=> __('Duration of Study (in days).','qm'), // description
			'id'	=> $prefix.'duration', // field id and name
			'type'	=> 'number', // type of field
			'std'	=> 0,
		),

		array( // Text Input
			'label'	=> __('Total number of Participants in Study','qm'), // <label>
			'desc'	=> __('Total number of Students who have taken this Study.','qm'), // description
			'id'	=> $prefix.'students', // field id and name
			'type'	=> 'number', // type of field
			'std'	=> 0,
		),
		array( // Text Input
			'label'	=> __('Auto Evaluation','qm'), // <label>
			'desc'	=> __('Evalute Studys based on Quizes scores available in Study (* Requires atleast 1 Quiz in study)','qm'), // description
			'id'	=> $prefix.'study_auto_eval', // field id and name
			'type'	=> 'yesno', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	        'std'   => 'H'
		),
		array( // Text Input
			'label'	=> __('Excellence Badge','qm'), // <label>
			'desc'	=> __('Upload badge image which Students recieve upon study completion','qm'), // description
			'id'	=> $prefix.'study_badge', // field id and name
			'type'	=> 'image' // type of field
		),

		/*array( // Text Input
			'label'	=> __('Badge Percentage','qm'), // <label>
			'desc'	=> __('Badge is given to people passing above percentage (out of 100)','qm'), // description
			'id'	=> $prefix.'study_badge_percentage', // field id and name
			'type'	=> 'number' // type of field
		),*/

		array( // Text Input
			'label'	=> __('Badge Title','qm'), // <label>
			'desc'	=> __('Title is shown on hovering the badge.','qm'), // description
			'id'	=> $prefix.'study_badge_title', // field id and name
			'type'	=> 'text' // type of field
		),

		array( // Text Input
			'label'	=> __('Completion Certificate','qm'), // <label>
			'desc'	=> __('Enable Certificate image which Students recieve upon study completion (out of 100)','qm'), // description
			'id'	=> $prefix.'study_certificate', // field id and name
			'type'	=> 'showhide', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	        'std'   => 'H'
		),

		array( // Text Input
			'label'	=> __('Certificate Template','qm'), // <label>
			'desc'	=> __('Select a Certificate Template','qm'), // description
			'id'	=> $prefix.'certificate_template', // field id and name
			'type'	=> 'selectcpt', // type of field
	        'post_type' => 'certificate'
		),

		array( // Text Input
			'label'	=> __('Required Measurements','qm'), // <label>
			'desc'	=> __('Minimum number of measurements to be included in pool','qm'), // description
			'id'	=> $prefix.'study_passing_percentage', // field id and name
			'type'	=> 'number' // type of field
		),
		array( // Text Input
			'label'	=> __('Tracking Surveys','qm'), // <label>
			'desc'	=> __('Tracking Surveys for Study','qm'), // description
			'id'	=> $prefix.'study_drip', // field id and name
			'type'	=> 'yesno', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	        'std'   => 'H'
		),
		array( // Text Input
			'label'	=> __('Surveys Frequency','qm'), // <label>
			'desc'	=> __('Duration between consecutive surveys (in hours)','qm'), // description
			'id'	=> $prefix.'study_drip_duration', // field id and name
			'type'	=> 'number', // type of field
		),

		

		array( // Text Input
			'label'	=> __('Study Curriculum','qm'), // <label>
			'desc'	=> __('Set Study Curriculum, prepare units and quizes before setting up curriculum','qm'), // description
			'id'	=> $prefix.'study_curriculum', // field id and name
			'post_type1' => 'unit',
			'post_type2' => 'quiz',
			'type'	=> 'curriculum2' // type of field
		),
		 
		array( // Text Input
			'label'	=> __('Examined Variable','qm'), // <label>
			'desc'	=> __('Variable to be examined in this study','qm'), // description
			'id'	=> $prefix.'pre_study', // field id and name
			'type'	=> 'selectcpt', // type of field
			'post_type' => 'study'
		), 
		array( // Text Input
			'label'	=> __('Study Forum','qm'), // <label>
			'desc'	=> __('Connect Forum with Study.','qm'), // description
			'id'	=> $prefix.'forum', // field id and name
			'type'	=> 'selectcpt', // type of field
			'post_type' => 'forum'
		),
		array( // Text Input
			'label'	=> __('Inclusion Pool','qm'), // <label>
			'desc'	=> __('Participants must be in this demographic pool','qm'), // description
			'id'	=> $prefix.'group', // field id and name
			'type'	=> 'groups', // type of field
		),
		array( // Text Input
			'label'	=> __('Study Completion Message','qm'), // <label>
			'desc'	=> __('This message is shown to users when they complete the study','qm'), // description
			'id'	=> $prefix.'study_message', // field id and name
			'type'	=> 'editor', // type of field
			'std'	=> 'Thank you for Finish the Study.'
		),
	);

	$study_product_metabox = array(
		array( // Text Input
			'label'	=> __('Study on a consumer Product','qm'), // <label>
			'desc'	=> __('','qm'), // description
			'id'	=> $prefix.'study_free', // field id and name
			'type'	=> 'yesno', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	        'std'   => 'H'
		)
	);
if ( in_array( 'paid-memberships-pro/paid-memberships-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && function_exists('pmpro_getAllLevels')) {	
	$levels=pmpro_getAllLevels();
	foreach($levels as $level){
		$level_array[]= array('value' =>$level->id,'label'=>$level->name);
	}
	$study_product_metabox[] =array(
			'label'	=> __('PMPro Membership','qm'), // <label>
			'desc'	=> __('Required Membership levle for this study','qm'), // description
			'id'	=> $prefix.'pmpro_membership', // field id and name
			'type'	=> 'multiselect', // type of field
			'options' => $level_array,
		);
}
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || (function_exists('is_plugin_active_for_network') && is_plugin_active_for_network( 'woocommerce/woocommerce.php'))) {
	
	$study_product_metabox[] =array(
			'label'	=> __('Studied Product','qm'), // <label>
			'desc'	=> __('Associated consumer product being studied.','qm'), // description
			'id'	=> $prefix.'product', // field id and name
			'type'	=> 'selectcpt2', // type of field
			'post_type'=> 'product',
	        'std'   => ''
		);
}

$unit_types = apply_filters('quantipress_unit_types',array(
                      array( 'label' =>__('Video','qm'),'value'=>'play'),
                      array( 'label' =>__('Audio','qm'),'value'=>'music-file-1'),
                      array( 'label' =>__('Podcast','qm'),'value'=>'podcast'),
                      array( 'label' =>__('General','qm'),'value'=>'text-document'),
                    ));

	$unit_metabox = array(  
		array( // Single checkbox
			'label'	=> __('Unit Description','qm'), // <label>
			'desc'	=> __('Small Description.','qm'), // description
			'id'	=> $prefix.'subtitle', // field id and name
			'type'	=> 'textarea', // type of field
	        'std'   => ''
	        ),
		array( // Text Input
			'label'	=> __('Unit Type','qm'), // <label>
			'desc'	=> __('Select Unit type from Video , Audio , Podcast, General , ','qm'), // description
			'id'	=> $prefix.'type', // field id and name
			'type'	=> 'select', // type of field
			'options' => $unit_types,
	        'std'   => 'text-document'
		),
		array( // Text Input
			'label'	=> __('Free Unit','qm'), // <label>
			'desc'	=> __('Set Free unit, viewable to all','qm'), // description
			'id'	=> $prefix.'free', // field id and name
			'type'	=> 'showhide', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	        'std'   => 'H'
		),
		array( // Text Input
			'label'	=> __('Unit Duration','qm'), // <label>
			'desc'	=> __('Duration in Minutes','qm'), // description
			'id'	=> $prefix.'duration', // field id and name
			'type'	=> 'number' // type of field
		),
		array( // Text Input
			'label'	=> __('Connect an Assignment','qm'), // <label>
			'desc'	=> __('Select an Assignment which you can connect with this Unit','qm'), // description
			'id'	=> $prefix.'assignment', // field id and name
			'type'	=> 'selectcpt', // type of field
			'post_type' => 'quantipress-assignment'
		),
		array( // Text Input
			'label'	=> __('Unit Forum','qm'), // <label>
			'desc'	=> __('Connect Forum with Unit.','qm'), // description
			'id'	=> $prefix.'forum', // field id and name
			'type'	=> 'selectcpt', // type of field
			'post_type' => 'forum'
		),
	);


	$question_metabox = array(  
		array( // Text Input
			'label'	=> __('Question Type','qm'), // <label>
			'desc'	=> __('Select Question type, ','qm'), // description
			'id'	=> $prefix.'question_type', // field id and name
			'type'	=> 'select', // type of field
			'options' => array(
	          array( 'label' =>'Single Choice','value'=>'single'),
	          array( 'label' =>'Multiple Choice','value'=>'multiple'),
	          array( 'label' =>'Sort Answers','value'=>'sort'),
	          array( 'label' =>'Small Text','value'=>'smalltext'),
	          array( 'label' =>'Large Text','value'=>'largetext'),
	        ),
	        'std'   => 'single'
		),
		array( // Text Input
			'label'	=> __('Question Options (For Single/Multiple/Sort/Match Question types)','qm'), // <label>
			'desc'	=> __('Single/Mutiple Choice question options','qm'), // description
			'id'	=> $prefix.'question_options', // field id and name
			'type'	=> 'repeatable_count' // type of field
		),
	    array( // Text Input
			'label'	=> __('Correct Answer','qm'), // <label>
			'desc'	=> __('Enter Choice Number (1,2..) or comma saperated Choice numbers (1,2..) or Correct Answer for small text (All possible answers comma saperated) | 0 for No Answer or Manual Check','qm'), // description
			'id'	=> $prefix.'question_answer', // field id and name
			'type'	=> 'text', // type of field
			'std'	=> 0
		),
	);

	$quiz_metabox = array(  
		array( // Text Input
			'label'	=> __('Quiz Subtitle','qm'), // <label>
			'desc'	=> __('Quiz Subtitle.','qm'), // description
			'id'	=> $prefix.'subtitle', // field id and name
			'type'	=> 'text', // type of field
			'std'	=> ''
		),
        array( // Text Input
			'label'	=> __('Connected Study','qm'), // <label>
			'desc'	=> __('Adds a Back to Study button, on quiz submission.','qm'), // description
			'id'	=> $prefix.'quiz_study', // field id and name
			'type'	=> 'selectcpt', // type of field
			'post_type' => 'study'
		),
		array( // Text Input
			'label'	=> __('Quiz Duration','qm'), // <label>
			'desc'	=> __('Quiz duration in minutes. Enables Timer & auto submits on expire. 0 to disable.','qm'), // description
			'id'	=> $prefix.'duration', // field id and name
			'type'	=> 'number', // type of field
			'std'	=> 0
		),
		
		array( // Text Input
			'label'	=> __('Auto Evatuate Results','qm'), // <label>
			'desc'	=> __('Evaluate results as soon as quiz is complete. (* No Large text questions ), Diable for manual evaluate','qm'), // description
			'id'	=> $prefix.'quiz_auto_evaluate', // field id and name
			'type'	=> 'yesno', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	        'std'   => 'H'
		), 
		
		array( // Text Input
			'label'	=> __('Number of Extra Quiz Retakes','qm'), // <label>
			'desc'	=> __('Student can reset and start the quiz all over again. Number of Extra retakes a student can take.','qm'), // description
			'id'	=> $prefix.'quiz_retakes', // field id and name
			'type'	=> 'number', // type of field
	        'std'   => 0
		), 
		array( // Text Input
			'label'	=> __('Send Notification upon evaluation','qm'), // <label>
			'desc'	=> __('Student recieve notification when quiz is evaluated.','qm'), // description
			'id'	=> $prefix.'quiz_notification', // field id and name
			'type'	=> 'showhide', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	        'std'   => 'H'
		),
		array( // Text Input
			'label'	=> __('Post Quiz Message','qm'), // <label>
			'desc'	=> __('This message is shown to users when they submit the quiz','qm'), // description
			'id'	=> $prefix.'quiz_message', // field id and name
			'type'	=> 'editor', // type of field
			'std'	=> 'Thank you for Submitting the Quiz. Check Results in your Profile.'
		),
		
	    array( // Text Input
			'label'	=> __('Quiz Questions','qm'), // <label>
			'desc'	=> __('Quiz questions','qm'), // description
			'id'	=> $prefix.'quiz_questions', // field id and name
			'type'	=> 'repeatable_selectcpt', // type of field
			'post_type' => 'question',
			'std'	=> 0
		),
	    
		
	);

	$testimonial_metabox = array(  
		array( // Text Input
			'label'	=> __('Author Name','qm'), // <label>
			'desc'	=> __('Enter the name of the testimonial author.','qm'), // description
			'id'	=> $prefix.'testimonial_author_name', // field id and name
			'type'	=> 'text' // type of field
		),
	        array( // Text Input
			'label'	=> __('Designation','qm'), // <label>
			'desc'	=> __('Enter the testimonial author\'s designation.','qm'), // description
			'id'	=> $prefix.'testimonial_author_designation', // field id and name
			'type'	=> 'text' // type of field
		),
	);




	$product_metabox = array(  
		array( // Text Input
			'label'	=> __('Associated Studys','qm'), // <label>
			'desc'	=> __('Associated Studys with this product. Enables access to the study.','qm'), // description
			'id'	=> $prefix.'studies', // field id and name
			'type'	=> 'selectmulticpt', // type of field
			'post_type'=>'study'
		),
	    array( // Text Input
			'label'	=> __('Subscription ','qm'), // <label>
			'desc'	=> __('Enable if Product is Subscription Type (Price per month)','qm'), // description
			'id'	=> $prefix.'subscription', // field id and name
			'type'	=> 'showhide', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	                'std'   => 'H'
		),
	    array( // Text Input
			'label'	=> __('Subscription Duration','qm'), // <label>
			'desc'	=> __('Duration for Subscription Products (in days)','qm'), // description
			'id'	=> $prefix.'duration', // field id and name
			'type'	=> 'number' // type of field
		),
	);


$quantipress_events_metabox = array(  
		array( // Single checkbox
			'label'	=> __('Event Sub-Title','qm'), // <label>
			'desc'	=> __('Event Sub-Title.','qm'), // description
			'id'	=> $prefix.'subtitle', // field id and name
			'type'	=> 'textarea', // type of field
	        'std'   => ''
	                ), 
		array( // Text Input
			'label'	=> __('Study','qm'), // <label>
			'desc'	=> __('Select Study for which the event is valid','qm'), // description
			'id'	=> $prefix.'event_study', // field id and name
			'type'	=> 'selectcpt', // type of field
			'post_type' => 'study'
		),
		array( // Text Input
			'label'	=> __('Connect an Assignment','qm'), // <label>
			'desc'	=> __('Select an Assignment which you can connect with this Event','qm'), // description
			'id'	=> $prefix.'assignment', // field id and name
			'type'	=> 'selectcpt', // type of field
			'post_type' => 'quantipress-assignment'
		),
		array( // Text Input
			'label'	=> __('Event Icon','qm'), // <label>
			'desc'	=> __('Click on icon to  select an icon for the event','qm'), // description
			'id'	=> $prefix.'icon', // field id and name
			'type'	=> 'icon', // type of field
		),
		array( // Text Input
			'label'	=> __('Event Color','qm'), // <label>
			'desc'	=> __('Select color for Event','qm'), // description
			'id'	=> $prefix.'color', // field id and name
			'type'	=> 'color', // type of field
		),
		array( // Text Input
			'label'	=> __('Start Date','qm'), // <label>
			'desc'	=> __('Date from which Event Begins','qm'), // description
			'id'	=> $prefix.'start_date', // field id and name
			'type'	=> 'date', // type of field
		),
		array( // Text Input
			'label'	=> __('End Date','qm'), // <label>
			'desc'	=> __('Date on which Event ends.','qm'), // description
			'id'	=> $prefix.'end_date', // field id and name
			'type'	=> 'date', // type of field
		),
		array( // Text Input
			'label'	=> __('Start Time','qm'), // <label>
			'desc'	=> __('Date from which Event Begins','qm'), // description
			'id'	=> $prefix.'start_time', // field id and name
			'type'	=> 'time', // type of field
		),
		array( // Text Input
			'label'	=> __('End Time','qm'), // <label>
			'desc'	=> __('Date on which Event ends.','qm'), // description
			'id'	=> $prefix.'end_time', // field id and name
			'type'	=> 'time', // type of field
		),
		array( // Text Input
			'label'	=> __('Show Location','qm'), // <label>
			'desc'	=> __('Show Location and Google map with the event','qm'), // description
			'id'	=> $prefix.'show_location', // field id and name
			'type'	=> 'yesno', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	        'std'   => 'H'
		),
	    array( // Text Input
			'label'	=> __('Location','qm'), // <label>
			'desc'	=> __('Location of event','qm'), // description
			'id'	=> $prefix.'location', // field id and name
			'type'	=> 'gmap' // type of field
		),
		array( // Text Input
			'label'	=> __('Additional Information','qm'), // <label>
			'desc'	=> __('Point wise Additional Information regarding the event','qm'), // description
			'id'	=> $prefix.'additional_info', // field id and name
			'type'	=> 'repeatable' // type of field
		),
		array( // Text Input
			'label'	=> __('More Information','qm'), // <label>
			'desc'	=> __('Supports HTML and shortcodes','qm'), // description
			'id'	=> $prefix.'more_info', // field id and name
			'type'	=> 'editor' // type of field
		),
		array( // Text Input
			'label'	=> __('Private Event','qm'), // <label>
			'desc'	=> __('Only Invited participants can see the Event','qm'), // description
			'id'	=> $prefix.'private_event', // field id and name
			'type'	=> 'yesno', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	        'std'   => 'H'
		),
	);

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || (function_exists('is_plugin_active_for_network') && is_plugin_active_for_network( 'woocommerce/woocommerce.php'))) {
	
	$quantipress_events_metabox[] =array(
			'label'	=> __('Associated Product for Event Access','qm'), // <label>
			'desc'	=> __('Purchase of this product grants Event access to the member.','qm'), // description
			'id'	=> $prefix.'product', // field id and name
			'type'	=> 'selectcpt', // type of field
			'post_type'=> 'product',
	        'std'   => ''
		);
}

$payments_metabox = array(  
		array( // Text Input
			'label'	=> __('From','qm'), // <label>
			'desc'	=> __('Date on which Payment was done.','qm'), // description
			'id'	=> $prefix.'date_from', // field id and name
			'type'	=> 'text', // type of field
		),
		array( // Text Input
			'label'	=> __('To','qm'), // <label>
			'desc'	=> __('Date on which Payment was done.','qm'), // description
			'id'	=> $prefix.'date_to', // field id and name
			'type'	=> 'text', // type of field
		),
	    array( // Text Input
			'label'	=> __('Instructor and Commissions','qm'), // <label>
			'desc'	=> __('Instructor commissions','qm'), // description
			'id'	=> $prefix.'instructor_commissions', // field id and name
			'type'	=> 'payments' // type of field
		),
	);

$certificate_metabox = array(  
		array( // Text Input
			'label'	=> __('Background Image/Pattern','qm'), // <label>
			'desc'	=> __('Add background image','qm'), // description
			'id'	=> $prefix.'background_image', // field id and name
			'type'	=> 'image', // type of field
		),
		array( // Text Input
			'label'	=> __('Enable Print','qm'), // <label>
			'desc'	=> __('Displays a Print Button on top right corner of certificate','qm'), // description
			'id'	=> $prefix.'print', // field id and name
			'type'	=> 'yesno', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	        'std'   => 'H'
		),
		array( // Text Input
			'label'	=> __('Custom Class','qm'), // <label>
			'desc'	=> __('Add Custom Class over Certificate container.','qm'), // description
			'id'	=> $prefix.'custom_class', // field id and name
			'type'	=> 'text', // type of field
		),
		array( // Text Input
			'label'	=> __('Custom CSS','qm'), // <label>
			'desc'	=> __('Add Custom CSS for Certificate.','qm'), // description
			'id'	=> $prefix.'custom_css', // field id and name
			'type'	=> 'textarea', // type of field
		),
		array( // Text Input
			'label'	=> __('NOTE:','qm'), // <label>
			'desc'	=> __(' USE FOLLOWING SHORTCODES TO DISPLAY RELEVANT DATA : <br />1. <strong>[certificate_student_name]</strong> : Displays Students Name<br />2. <strong>[certificate_study]</strong> : Displays Study Name<br />3. <strong>[certificate_student_marks]</strong> : Displays Students Marks in Study<br />4. <strong>[certificate_student_date]</strong>: Displays date on which Certificate was awarded to the Student<br />5. <strong>[certificate_student_email]</strong>: Displays registered email of the Student<br />6. <strong>[certificate_code]</strong>: Generates unique code for Student which can be validated from Certificate page.','qm'), // description
			'id'	=> $prefix.'note', // field id and name
			'type'	=> 'note', // type of field
		),
	);	


$quantipress_assignments_metabox = array(  
	array( // Single checkbox
			'label'	=> __('Assignment Sub-Title','qm'), // <label>
			'desc'	=> __('Assignment Sub-Title.','qm'), // description
			'id'	=> $prefix.'subtitle', // field id and name
			'type'	=> 'textarea', // type of field
	        'std'   => ''
	                ), 
	array( // Single checkbox
			'label'	=> __('Sidebar','qm'), // <label>
			'desc'	=> __('Select a Sidebar | Default : mainsidebar','qm'), // description
			'id'	=> $prefix.'sidebar', // field id and name
			'type'	=> 'select',
	                'options' => $sidebararray
	                ),
	array( // Text Input
		'label'	=> __('Assignment Maximum Marks','qm'), // <label>
		'desc'	=> __('Set Maximum marks for the assignment','qm'), // description
		'id'	=> $prefix.'assignment_marks', // field id and name
		'type'	=> 'number', // type of field
		'std' => '10'
	),
	array( // Text Input
		'label'	=> __('Assignment Maximum Time limit','qm'), // <label>
		'desc'	=> __('Set Maximum Time limit for Assignment ( in Days )','qm'), // description
		'id'	=> $prefix.'assignment_duration', // field id and name
		'type'	=> 'number', // type of field
		'std' => '10'
	),
	array( // Text Input
			'label'	=> __('Include in Study Evaluation','qm'), // <label>
			'desc'	=> __('Include assignment marks in Study Evaluation','qm'), // description
			'id'	=> $prefix.'assignment_evaluation', // field id and name
			'type'	=> 'yesno', // type of field
	        'options' => array(
	          array('value' => 'H',
	                'label' =>'Hide'),
	          array('value' => 'S',
	                'label' =>'Show'),
	        ),
	        'std'   => 'H'
		),
	array( // Text Input
			'label'	=> __('Include in Study','qm'), // <label>
			'desc'	=> __('Assignments marks will be shown/used in study evaluation','qm'), // description
			'id'	=> $prefix.'assignment_study', // field id and name
			'type'	=> 'selectcpt', // type of field
			'post_type' => 'study'
		),
	array( // Single checkbox
			'label'	=> __('Assignment Submissions','qm'), // <label>
			'desc'	=> __('Select type of assignment submissions','qm'), // description
			'id'	=> $prefix.'assignment_submission_type', // field id and name
			'type'	=> 'select', // type of field
	        'options' => array(
	                    1=>array('label'=>'Upload file','value'=>'upload'),
	                    2=>array('label'=>'Text Area','value'=>'textarea'),
	        ),
	        'std'   => ''
		),
	array( // Text Input
			'label'	=> __('Attachment Type','qm'), // <label>
			'desc'	=> __('Select valid attachment types ','qm'), // description
			'id'	=> $prefix.'attachment_type', // field id and name
			'type'	=> 'multiselect', // type of field
			'options' => array(
				array('value'=> 'JPG','label' =>'JPG'),
				array('value'=> 'GIF','label' =>'GIF'),
				array('value'=> 'PNG','label' =>'PNG'),
				array('value'=> 'PDF','label' =>'PDF'),
				array('value'=> 'DOC','label' =>'DOC'),
				array('value'=> 'DOCX','label' => 'DOCX'),
				array('value'=> 'PPT','label' =>'PPT'),
				array('value'=> 'PPTX','label' => 'PPTX'),
				array('value'=> 'PPS','label' =>'PPS'),
				array('value'=> 'PPSX','label' => 'PPSX'),
				array('value'=> 'ODT','label' =>'ODT'),
				array('value'=> 'XLS','label' =>'XLS'),
				array('value'=> 'XLSX','label' => 'XLSX'),
				array('value'=> 'MP3','label' =>'MP3'),
				array('value'=> 'M4A','label' =>'M4A'),
				array('value'=> 'OGG','label' =>'OGG'),
				array('value'=> 'WAV','label' =>'WAV'),
				array('value'=> 'WMA','label' =>'WMA'),
				array('value'=> 'MP4','label' =>'MP4'),
				array('value'=> 'M4V','label' =>'M4V'),
				array('value'=> 'MOV','label' =>'MOV'),
				array('value'=> 'WMV','label' =>'WMV'),
				array('value'=> 'AVI','label' =>'AVI'),
				array('value'=> 'MPG','label' =>'MPG'),
				array('value'=> 'OGV','label' =>'OGV'),
				array('value'=> '3GP','label' =>'3GP'),
				array('value'=> '3G2','label' =>'3G2'),
				array('value'=> 'FLV','label' =>'FLV'),
				array('value'=> 'WEBM','label' =>'WEBM'),
				array('value'=> 'APK','label' =>'APK '),
				array('value'=> 'RAR','label' =>'RAR'),
				array('value'=> 'ZIP','label' =>'ZIP'),
	        ),
	        'std'   => 'single'
		),
);

	$api_metabox = array(
			array( // Text Input
			'label'	=> __('Variable Category','qm'), // <label>
			'desc'	=> __('Select Variable Category','qm'), // description
			'id'	=> $perfixtwo.'variable-category', // field id and name
			'type'	=> 'api_variable_category',
			),
			
			array( // Text Input
			'label'	=> __('Variable','qm'), // <label>
			'desc'	=> __('Select Variable','qm'), // description
			'id'	=> $perfixtwo.'variable', // field id and name
			'type'	=> 'api_variable',
			),
			
			array( // Text Input
			'label'	=> __('Cause or Effect','qm'), // <label>
			'desc'	=> __('Select cause of effect','qm'), // description
			'id'	=> $perfixtwo.'causeoreffect', // field id and name
			'type'	=> 'causeoreffect',
			'options' => array(
				array('value'=> 'as-cause','label' => 'As Cause'),
				array('value'=> 'as-effect','label' => 'As Effect'),
				),
		     ),
			
			array( // Text Input
			'label'	=> __('Unit','qm'), // <label>
			'desc'	=> __('Select Unit','qm'), // description
			'id'	=> $perfixtwo.'variable-unit', // field id and name
			'type'	=> 'api_variable_unit',
			'options' => array(
				array('value'=> '1to5','label' => '1 to 5 rating'),
				array('value'=> '0to1','label' => '0 to 1 rating'),
				array('value'=> 'percent','label' => 'Percent'),
				array('value'=> '-4to4','label' => '-4 to 4 rating'),
				array('value'=> '0to5','label' => '0 to 5 rating'),
			    ),
		    ),
	);
	
	$api_data_optimization = array(
			
			array( // Text Input
			'label'	=> __('Minimum Value','qm'), // <label>
			'desc'	=> __('Add Minimum Value','qm'), // description
			'id'	=> $perfixtwo.'variable-min-value', // field id and name
			'type'	=> 'api_do_min', // type of field
		),
			
		array( // Text Input
			'label'	=> __('Maximum Value','qm'), // <label>
			'desc'	=> __('Add Minimum Value','qm'), // description
			'id'	=> $perfixtwo.'variable-max-value', // field id and name
			'type'	=> 'api_do_max', // type of field
		),
		
		array( // Text Input
			'label'	=> __('Delay Before Onset of Action','qm'), // <label>
			'desc'	=> __('In Hours','qm'), // description
			'id'	=> $perfixtwo.'variable-onset-delay', // field id and name
			'type'	=> 'api_do_delay', // type of field
		),
		
		array( // Text Input
			'label'	=> __('Duration of Action','qm'), // <label>
			'desc'	=> __('In Hours','qm'), // description
			'id'	=> $perfixtwo.'variable-duration-value', // field id and name
			'type'	=> 'api_do_duration', // type of field
		),
		
		array( // Text Input
			'label'	=> __('If there is data','qm'), // <label>
			'desc'	=> __('If you leave this field empty, it is assumed that there is no data','qm'), // description
			'id'	=> $perfixtwo.'variable-filling-value', // field id and name
			'type'	=> 'api_do_data', // type of field
			),
	    
	);
	
	
	$study_api = new custom_add_meta_box_study( 'api-settings', __('Examined Variable','qm'), $api_metabox, 'personal-study', true );
	$api_do = new custom_add_meta_box_study( 'api-do', __('Data Optimization','qm'), $api_data_optimization, 'personal-study', true );
	
	$post_metabox = new custom_add_meta_box_study( 'post-settings', __('Post Settings','qm'), $post_metabox, 'post', true );
	$page_metabox = new custom_add_meta_box_study( 'page-settings', __('Page Settings','qm'), $page_metabox, 'page', true );

//	$study_box = new custom_add_meta_box_study( 'page-settings', __('Study Settings','qm'), $study_metabox, 'personal-study', true );

	$study_product = __('Product Studies','qm');
	if(function_exists('pmpro_getAllLevels')){
		$study_product = __('Study Membership','qm');
	}
//	$study_product_box = new custom_add_meta_box_study( 'post-settings', $study_product, $study_product_metabox, 'personal-study', true );
// Initiator for Study



	
	$testimonial_box = new custom_add_meta_box_study( 'testimonial-info', __('Testimonial Author Information','qm'), $testimonial_metabox, 'testimonials', true );
	$payments_metabox = new custom_add_meta_box_study( 'page-settings', __('Payments Settings','qm'), $payments_metabox, 'payments', true );
	$certificates_metabox = new custom_add_meta_box_study( 'page-settings', __('Certificate Template Settings','qm'), $certificate_metabox, 'certificate', true );
	
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || (function_exists('is_plugin_active_for_network') && is_plugin_active_for_network( 'woocommerce/woocommerce.php'))) {
		$product_box = new custom_add_meta_box_study( 'page-settings', __('Product Study Settings','qm'), $product_metabox, 'product', true );
	}

	if ( in_array( 'quantipress-events/quantipress-events.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$events_metabox = new custom_add_meta_box_study( 'page-settings', __('QuantiPress Events Settings','qm'), $quantipress_events_metabox, 'quantipress-event', true );
	}

	
	if ( in_array( 'quantipress-assignments/quantipress-assignments.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$eassignments_metabox = new custom_add_meta_box_study( 'page-settings', __('QuantiPress Assignments Settings','qm'), $quantipress_assignments_metabox, 'quantipress-assignment', true );
	}
}
add_action('init','add_qm_metaboxes_study');


add_action( 'add_meta_boxes', 'add_qm_editor' );
if(!function_exists('add_qm_editor')){
	function add_qm_editor(){
	    add_meta_box( 'qm-editor', __( 'Page Builder', 'qm' ), 'qm_layout_editor', 'page', 'normal', 'high' );
	}
}

// Function for the title of study
function change_default_title( $title ){
    $screen = get_current_screen();

    // For CPT 1
    if  ( 'personal-study' == $screen->post_type ) {
        $title = 'Enter research question here';}
    
	return $title;
}
add_filter( 'enter_title_here', 'change_default_title' );

function attachment_getMaximumUploadFileSize_study(){
    $maxUpload      = (int)(ini_get('upload_max_filesize'));
    $maxPost        = (int)(ini_get('post_max_size'));
    $memoryLimit    = (int)(ini_get('memory_limit'));
    return min($maxUpload, $maxPost, $memoryLimit);
}
