<?php

function quantimodo_iframe_func($atts) {
    // Extract the attributes
    $atts = shortcode_atts(
        array(), // Default values for attributes
        $atts,
        'quantimodo_iframe' // Shortcode name
    );

    $qmAccessToken = get_qm_access_token();
    // Base URL
    $url = qm_api_hostname()."/app/public/#?qmAccessToken=" . $qmAccessToken;

    // Add any extra parameters from the shortcode attributes
    foreach ($atts as $key => $value) {
        $url .= '&' . esc_attr($key) . '=' . urlencode($value);
    }

    $iframe = '<iframe src="' . esc_url($url) . '" style="border: none; width: 100%; height: 100vh;"></iframe>';
    return $iframe;
}
add_shortcode('quantimodo_iframe', 'quantimodo_iframe_func');
