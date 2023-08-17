<?php

function generate_js_code($logout = false) {
	$logout = $logout ? 'true' : 'false';
    $qmClientId = qm_api_client_id();
    $jsUrl = plugins_url( '/integration.js', __FILE__ );
    $api_host = qm_api_host();
    $wpUserId = get_current_user_id();
    $accessToken = get_qm_access_token();

    $jsText = <<<JS
    <script src="$jsUrl"></script>
    <script>
        window.QuantiModoIntegration.options = {
            clientId: '$qmClientId',
            apiUrl: '$api_host',
            logout: $logout,
            clientUserId: encodeURIComponent('$wpUserId'),
            qmAccessToken: '$accessToken',
            finish: function( sessionTokenObject) {
                /* Called after user finishes connecting */
                //POST sessionTokenObject to your server
                // Include code here to refresh the page.
            },
            close: function() {
                /* (optional) Called when a user closes the popup without connecting any data sources */
            },
            error: function(err) {
                /* (optional) Called if an error occurs when loading the popup. */
            }
        }
        window.QuantiModoIntegration.createSingleFloatingActionButton();
    </script>
JS;

    return $jsText;
}

function add_quantimodo_floating_action_button() {
    if ( should_not_render_fab() ){return;}
    echo generate_js_code();
}

/**
 * @return bool
 */
function should_not_render_fab(): bool {
	return is_feed() || is_robots() || is_trackback() || empty( qm_settings() ) || ! qm_floating_button_enabled();
}

function quantimodo_logout() {
	if ( should_not_render_fab() ){return;}
    echo generate_js_code(true);
}

add_action('wp_head', 'add_quantimodo_floating_action_button');
add_action('login_head', 'quantimodo_logout'); // Logs out the user when they log out of WordPress
