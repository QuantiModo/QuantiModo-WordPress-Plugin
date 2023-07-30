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
    $jsUrl = plugins_url( '/integration.js', __FILE__ );
    $jsText = '<script src="'.$jsUrl.'"></script> <script> window.QuantiModoIntegration.options = {';
    $wpUserId = get_current_user_id();
    if($wpUserId){
        $jsText      .= "clientUserId: encodeURIComponent('".$wpUserId."'),";
	    //$jsText      .= "clientUser: encodeURIComponent('" . json_encode( $userData ) . "'),";
        $accessToken = get_qm_access_token();
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
        if ( $qmClientId && '' != $qmClientId )
        {
            echo "<!-- Start QuantiModo By WP-Plugin: QuantiModo -->\n";
            echo $jsText;
            //echo $quantimodo_tag;
            echo"<!-- end: QuantiModo Code. -->\n";
        }
    }
}
