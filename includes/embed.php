<?php

// Add the QuantiModo Javascript
add_action('wp_head', 'add_quantimodo');

// If we can indentify the current user output
function get_quantimodo_identify()
{
  $current_user = wp_get_current_user();
  //print_r($current_user->roles[0]);
  //print_r(sanitize_text_field($current_user->roles[0]));

  if ($current_user->user_email) {
    $sanitized_email = sanitize_email($current_user->user_email);
    echo "<!-- Start Identify call for QuantiModo -->\n";
    echo "<script>\n";
    echo "quantimodo.identify(\"".md5($sanitized_email)."\", { email: \"".$sanitized_email."\", name: \"".sanitize_text_field($current_user->user_login)."\", userRole: \"".sanitize_text_field($current_user->roles[0])."\" });\n";
    echo "</script>\n";
    echo "<!-- End Identify call for QuantiModo -->\n";
  } else {
    // See if current user is a commenter
    $commenter = wp_get_current_commenter();
    if ($commenter['comment_author_email']) {
      echo "<!-- Start Identify call for QuantiModo -->\n";
      echo "<script>\n";
      echo "quantimodo.identify(\"".md5(sanitize_email($commenter['comment_author_email']))."\", { email: \"".sanitize_email($commenter['comment_author_email'])."\", name: \"".sanitize_text_field($commenter['comment_author'])."\" });\n";
      echo "</script>\n";
      echo "<!-- End Identify call for QuantiModo -->\n";
    }
  }
}

// The guts of the QuantiModo script
function add_quantimodo()
{
  // Ignore admin, feed, robots or trackbacks
  if ( is_feed() || is_robots() || is_trackback() )
  {
    return;
  }

  $options = get_option('QuantiModo_settings');

  // If options is empty then exit
  if( empty( $options ) )
  {
    return;
  }

  // Check to see if QuantiModo is enabled
  if ( esc_attr( $options['quantimodo_enabled'] ) == "on" )
  {
    $quantimodo_tag = $options['quantimodo_widget_code'];
    
    // Insert tracker code
    if ( '' != $quantimodo_tag )
    {
      echo "<!-- Start QuantiModo By WP-Plugin: QuantiModo -->\n";
      echo $quantimodo_tag;
      echo"<!-- end: QuantiModo Code. -->\n";

      // Optional
      if ( esc_attr( $options['quantimodo_identify'] ) == "on" ){
        get_quantimodo_identify();
      }

    }
  }
}
?>