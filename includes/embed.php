<?php

// Add the QuantiModo Javascript
add_action('wp_head', 'add_quantimodo_floating_action_button');
// The guts of the QuantiModo script
function add_quantimodo_floating_action_button()
{
  // Ignore admin, feed, robots or trackbacks
  if ( is_feed() || is_robots() || is_trackback() ){return;}
  // If options are empty, then exit
  if( empty( qm_settings() ) ){return;}
  // Check to see if QuantiModo is enabled
  if ( qm_floating_button_enabled() ){
    $qmClientId = qm_api_client_id();
    $jsUrl = plugins_url( '/integration.js', __FILE__ );
    $jsText = '<script src="'.$jsUrl.'"></script> <script> window.QuantiModoIntegration.options = {';
    if($wpUserId = get_current_user_id()){
        $jsText      .= "clientUserId: encodeURIComponent('".$wpUserId."'),";
	    //$jsText      .= "clientUser: encodeURIComponent('" . json_encode( $userData ) . "'),";
        if($accessToken = get_qm_access_token()){$jsText .= 'qmAccessToken: "'.$accessToken.'",';}
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
    // If options are empty, then exit
    if( empty( qm_settings() ) ){return;}
    // Check to see if QuantiModo is enabled
    if (qm_floating_button_enabled()){
        $qmClientId = qm_api_client_id();
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
