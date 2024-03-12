<?php
/**
 * Description: Allows users to sign a petition, creates a new subscriber user, and stores address information.
 * Version:     1.0
 * Author:      Mike Sinn
 */

function petition_the_government_register_block() {
    wp_register_script(
        'petition-the-government-editor',
        plugins_url('build/index.js', __FILE__),
        ['wp-blocks', 'wp-element', 'wp-editor'],
        filemtime(plugin_dir_path(__FILE__) . 'build/index.js')
    );

    register_block_type('petition-the-government/petition-form', [
        'editor_script' => 'petition-the-government-editor',
        'render_callback' => 'petition_the_government_render_block',
    ]);
}

function petition_the_government_render_block() {

    $countryOptions = require plugin_dir_path(__FILE__) . 'countries.php';
    $countryOptionsHtml = '';
    foreach ($countryOptions as $code => $name) {
		$countryOptionsHtml .= "<option value=\"$code\">$name</option>";
	}

$form_html = <<<HTML
<form id="petition-form"
class="petition-form"
action="index.php?rest_route=/petition-the-government/v1/submit"
 method="POST">
	<div class="petition-field petition-field-inline">
	    <label for="petition-first-name">First Name</label>
	    <input type="text" id="petition-first-name" name="first_name" required>
	</div>
	<div class="petition-field petition-field-inline">
	    <label for="petition-last-name">Last Name</label>
	    <input type="text" id="petition-last-name" name="last_name" required>
	</div>

    <div class="petition-field">
        <label for="petition-email">Email</label>
        <input type="email" id="petition-email" name="email" required>
    </div>

    <div class="petition-field petition-field-inline">
        <label for="petition-postal">Postal Code</label>
        <input type="text" id="petition-postal" name="postal">
    </div>

<!--    <div class="petition-field">
        <label for="petition-street">Street Address (Optional)</label>
        <input type="text" id="petition-street" name="street">
    </div>

    <div class="petition-field">
        <label for="petition-organization">Organization (Optional)</label>
        <input type="text" id="petition-organization" name="organization">
    </div>

    <div class="petition-field">
        <label for="petition-phone">Phone Number (Optional)</label>
        <input type="tel" id="petition-phone" name="phone">
    </div>-->

    <div class="petition-field petition-field-inline">
        <label for="petition-country">Country</label>
        <select id="petition-country" name="country" onchange="toggleStateField()">
$countryOptionsHtml
        </select>
    </div>

<!--    <div id="state-field" style="display: none;">
        <label for="petition-state">State</label>
        <select id="petition-state" name="state">
            <option value="AL">Alabama</option>
            <option value="AK">Alaska</option>
            &lt;!&ndash; Add more states as needed &ndash;&gt;
        </select>
    </div>-->
    <div class="petition-button-container">
	    <button type="submit">
	        Sign Petition
	    </button>
    </div>
</form>
<script>
    function toggleStateField() {
        var country = document.getElementById('petition-country').value;
        var stateField = document.getElementById('state-field');
        if(stateField){
        	stateField.style.display = country === 'US' ? 'block' : 'none';
        }
    }
    // Call toggleStateField on page load in case USA is preselected
    document.addEventListener('DOMContentLoaded', toggleStateField);
</script>
HTML;

    return $form_html;
}


add_action('init', 'petition_the_government_register_block');

function petition_the_government_enqueue_assets() {
    wp_enqueue_style('petition-the-government-style', plugins_url('/petition-style.css', __FILE__));
}

// Enqueue CSS for both frontend and backend.
add_action('enqueue_block_assets', 'petition_the_government_enqueue_assets');


function petition_the_government_handle_submit($request)
{
    $first_name = sanitize_text_field($request['first_name']);
	$last_name = sanitize_text_field($request['last_name']);
    $email = sanitize_email($request['email']);
    $street = sanitize_text_field($request['street']); // Optional street address
    $organization = sanitize_text_field($request['organization']);
    $phone = sanitize_text_field($request['phone']);
    $country = sanitize_text_field($request['country']);
    $state = sanitize_text_field($request['state']);

	$page = get_page_by_path('petition-thank-you', OBJECT, 'page');
	if ($page) {
		$redirect = get_permalink($page->ID);
	} else {
		$redirect = 'Page not found';
	}

    $user_id = wp_insert_user([
        'user_login' => $email,
        'user_email' => $email,
        'display_name' => $first_name . ' ' . $last_name,
        'user_pass' => wp_generate_password(),
        'role' => 'subscriber',

    ]);

    if (is_wp_error($user_id)) {
	    error_log($user_id->get_error_message());
	    wp_redirect($redirect);
	    exit;
        //return new WP_Error('user_create_failed', 'Failed to create user.', ['status' => 500]);
    }

    // Store the additional information in wp_usermeta
	wp_update_user([
		'ID' => $user_id,
		'first_name' => $first_name,
		'last_name' => $last_name
	]);
    if (!empty($street)) {
        add_user_meta($user_id, 'street', $street, true);
    }
    if (!empty($organization)) {
        add_user_meta($user_id, 'organization', $organization, true);
    }
    if (!empty($phone)) {
        add_user_meta($user_id, 'phone', $phone, true);
    }
	if(!empty($state)) {
		add_user_meta($user_id, 'state', $state, true);
	}
	if(!empty($country)) {
		add_user_meta($user_id, 'country', $country, true);
	}

	$user = wp_get_current_user();
	// If user is already logged in, redirect them to the thank you page
	if (is_user_logged_in()) {
		wp_redirect($redirect);
		exit;
	}


	$user = get_user_by('email', $email);
	if ($user) {
		wp_set_current_user($user->ID, $user->user_login);
		wp_set_auth_cookie($user->ID);
		do_action('wp_login', $user->user_login, $user);
		// Redirect to the thank-you page
		wp_redirect($redirect);
		exit;
	}


	return new WP_REST_Response('Petition signed successfully. User created and data stored.', 200);
}

function petition_the_government_register_rest_route()
{
    register_rest_route('petition-the-government/v1', '/submit', [
        'methods' => 'POST',
        'callback' => 'petition_the_government_handle_submit',
        'permission_callback' => '__return_true',
    ]);
}

add_action('rest_api_init', 'petition_the_government_register_rest_route');

function petition_the_government_create_thank_you_page() {
	$the_page_title = 'Thank You for Signing the Petition!';
	$the_page_content = 'Thank you for signing the petition. Please share it with your friends on social media.';

	$args = array(
		'post_type' => 'page',
		'name' => 'petition-thank-you',
		'posts_per_page' => 1,
	);

	$query = new WP_Query($args);

	if ($query->have_posts()) {
		return;
	} else {
		$the_page = null;
	}

	$page_url = home_url();
	$sharing_text = urlencode("Please sign this petition to give everyone a super-intelligent AI doctor!");

// Create the social sharing URLs
	$twitter_url = 'https://twitter.com/intent/tweet?text=' . $sharing_text. '&url=' . urlencode($page_url);
	$reddit_url = 'https://www.reddit.com/submit?title=' . $sharing_text . '&url=' . urlencode($page_url);
	$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($page_url);

// Add the social sharing buttons to the page content
	$the_page_content .= '<h3>Share the petition:</h3>
<a href="' . $twitter_url . '" target="_blank">Share on Twitter</a>
<a href="' . $reddit_url . '" target="_blank">Share on Reddit</a>
<a href="' . $facebook_url . '" target="_blank">Share on Facebook</a>';

$the_page_content = '
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Now share it with all your friends!</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--50)">';

$socials = [
    'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $page_url . '&t=Please sign our petition to give everyone a free super-intelligent robot doctor and accelerate clinical discovery!',
    'Tweet' => 'https://twitter.com/intent/tweet?text=Please sign our petition to give everyone a free super-intelligent robot doctor and accelerate clinical discovery!&url=' . $page_url,
    'WhatsApp' => 'https://wa.me/?text=Please sign our petition to give everyone a free super-intelligent robot doctor and accelerate clinical discovery! ' . $page_url,
    'Telegram' => 'https://telegram.me/share/url?text=Please sign our petition to give everyone a free super-intelligent robot doctor and accelerate clinical discovery!&url=' . $page_url,
    'Reddit' => 'https://reddit.com/submit?url=' . $page_url . '&title=Please sign our petition to give everyone a free super-intelligent robot doctor and accelerate clinical discovery!',
    'LinkedIn' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . $page_url . '&title=Please sign our petition to give everyone a free super-intelligent robot doctor and accelerate clinical discovery!&summary=Join us in our mission to revolutionize healthcare and clinical discovery. Sign the petition today!'
];

foreach ($socials as $name => $link) {
    $the_page_content .= '<!-- wp:button -->
    <div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . $link . '" target="_blank" rel="noopener">' . $name . '</a></div>
    <!-- /wp:button -->';
}

$the_page_content .= '</div>
<!-- /wp:buttons --></div>
';

	if (!$the_page) {
		// Create post object
		$_p = array();
		$_p['post_title'] = $the_page_title;
		$_p['post_content'] = $the_page_content;
		$_p['post_status'] = 'publish';
		$_p['post_type'] = 'page';
		$_p['comment_status'] = 'closed';
		$_p['ping_status'] = 'closed';
		$_p['post_category'] = array(1); // the default 'Uncategorized'
		$_p['post_name'] = 'petition-thank-you';

		// Insert the post into the database
		$the_page_id = wp_insert_post($_p);
	}
}
register_activation_hook(__FILE__, 'petition_the_government_create_thank_you_page');
