<?php

function qm_iframe_func($atts) {
	redirect_to_login_if_necessary();
    // Extract the attributes
    $atts = shortcode_atts(
        [], // Default values for attributes
        $atts,
        'qm_iframe' // Shortcode name
    );

    // Base URL
    $url = qm_url($atts);

    $iframe = '<iframe src="' . $url . '" style="border: none; width: 100%; height: 100vh;"></iframe>';
    return $iframe;
}
add_shortcode('qm_iframe', 'qm_iframe_func');
