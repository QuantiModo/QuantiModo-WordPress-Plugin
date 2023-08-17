<?php

function qm_redirect_func($atts) {

    // Extract the attributes
    $atts = shortcode_atts(
        [], // Default values for attributes
        $atts,
        'qm_redirect' // Shortcode name
    );
	//xdebug_break();

    // Base URL
    $url = qm_url($atts);

    $redirect = '<script>window.location.href = "' . $url . '";</script>';

    return $redirect;
}
add_shortcode('qm_redirect', 'qm_redirect_func');
