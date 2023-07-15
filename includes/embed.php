<?php
/**
 * @param $message
 */
function qm_error($message){
    $pluginLog = plugin_dir_path(__FILE__).'debug.log';
    error_log($message.PHP_EOL, 3, $pluginLog);
}
// Add the QuantiModo Javascript
add_action('wp_head', 'add_quantimodo');
// The guts of the QuantiModo script
function add_quantimodo()
{
  // Ignore admin, feed, robots or trackbacks
  if ( is_feed() || is_robots() || is_trackback() ){return;}
  $options = get_option('QuantiModo_settings');
  // If options are empty, then exit
  if( empty( $options ) ){return;}
  // Check to see if QuantiModo is enabled
  if ( esc_attr( $options['floating_button_enabled'] ) == "on" ){
    $qmClientId = $options['quantimodo_client_id'];
    $qmClientSecret = isset($options['quantimodo_client_secret']) ? $options['quantimodo_client_secret'] : null;
    $apiHostName = "https://app.quantimo.do";
    $env = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : getenv('APP_HOST_NAME');
    if(!$env){$env = "https://".$_SERVER["HTTP_HOST"];}
    if(stripos($env, "https://utopia.quantimo.do") === 0 || stripos($env, "https://app.quantimo.do") === 0){
        $apiHostName = "https://utopia.quantimo.do";
    }
    $jsUrl = plugins_url( '/integration.js', __FILE__ );
    $jsText = '<script src="'.$jsUrl.'"></script> <script> window.QuantiModoIntegration.options = {';
    $wpUserId = get_current_user_id();
    if($wpUserId){
        $jsText      .= "clientUserId: encodeURIComponent('".$wpUserId."'),";
        $wpUser      = wp_get_current_user();
	    $userData   = clone $wpUser->data;
		$userData->username = $userData->user_login = $qmClientId.'-'.$userData->ID;
        $userData->client_id = $qmClientId;
        $userData->client_secret = $qmClientSecret;
        $userData->client_user_id = $wpUserId;
        unset($userData->ID);
	    $jsText      .= "clientUser: encodeURIComponent('" . json_encode( $userData ) . "'),";
        $accessToken = get_user_meta($wpUserId, 'qm_access_token', true);
        if(!$accessToken){
            $args = [
                'timeout' => 10,
                'body' => [
                    'clientUserId' => $wpUserId,
                    'clientUser' => $userData,
                    'client_id' => $qmClientId,
                    'client_secret' => $qmClientSecret
                ]
            ];
            $url = $apiHostName.'/api/v1/user';
            if(!$qmClientSecret){
//                add_settings_error('no_quantimodo_client_secret', 'no_quantimodo_client_secret',
//                    quantimodo_get_client_secret_instructions(), 'error');
                qm_error('Cannot get quantimodo user because no_quantimodo_client_secret! '.quantimodo_get_client_secret_instructions());
                return;
            }
            $response = wp_remote_post( $url, $args );
            if($response instanceof WP_Error){
//                add_settings_error('get_quantimodo_user_failed', 'get_quantimodo_user_failed',
//                    $response->get_error_message(), 'error');
                qm_error($response->get_error_message());
                return;
            }
            $body = json_decode($response['body'], false);
            if(isset($body->user)){
                $qmUser = $body->user;
                $jsText .= "qmUser: ".json_encode($qmUser).",";
                if(isset($qmUser->accessToken)){
                    $accessToken = $qmUser->accessToken;
                    add_user_meta($wpUserId, 'qm_access_token', $accessToken);
                }
            }
        }
        if($accessToken){$jsText .= 'qmAccessToken: "'.$accessToken.'",';}
    }
    $jsText .= "
                clientId: '".$qmClientId."',
                //publicToken: '',
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
      ";
    // Insert tracker code
    if ( $qmClientId && '' != $qmClientId )
    {
      echo "<!-- Start QuantiModo By WP-Plugin: QuantiModo -->\n";
      echo $jsText;
      //echo $quantimodo_tag;
      echo"<!-- end: QuantiModo Code. -->\n";
    }
  }
}


// Add the QuantiModo Javascript
add_action('login_head', 'quantimodo_logout');
// The guts of the QuantiModo script
function quantimodo_logout() {
    // Ignore admin, feed, robots or trackbacks
    if ( is_feed() || is_robots() || is_trackback() ){return;}
    $options = get_option('QuantiModo_settings');
    // If options are empty, then exit
    if( empty( $options ) ){return;}
    // Check to see if QuantiModo is enabled
    if ( esc_attr( $options['floating_button_enabled'] ) == "on" ){
        $qmClientId = $options['quantimodo_client_id'];
        $jsUrl = plugins_url( '/integration.js', __FILE__ );
        $jsText = '<script src="'.$jsUrl.'"></script> <script> window.QuantiModoIntegration.options = {';
        $jsText .= "
                clientId: '".$qmClientId."',
                logout: true,
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
      ";
        // Insert tracker code
        if ( $qmClientId && '' != $qmClientId )
        {
            echo "<!-- Start QuantiModo By WP-Plugin: QuantiModo -->\n";
            echo $jsText;
            //echo $quantimodo_tag;
            echo"<!-- end: QuantiModo Code. -->\n";
        }
    }
}
