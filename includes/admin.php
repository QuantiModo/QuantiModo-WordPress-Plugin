<?php
/**
 * @return string
 */
function quantimodo_get_client_secret_instructions(){
    return 'Get your client secret from the
    <a href="https://builder.quantimo.do" target="_blank" title="Open QuantiModo app builder">QuantiModo app builder</a>
    by clicking SETTINGS -> OAUTH.';
}
// Output the options page
function quantimodo_options_page()
{
  // Get options
  $options = get_option('QuantiModo_settings');
  if(!isset($options['quantimodo_client_secret'])){
      $options['quantimodo_client_secret'] = null;
  }

  // Check to see if QuantiModo is enabled
  $quantimodo_activated = false;
  if ( esc_attr( $options['quantimodo_enabled'] ) == "on" ) {
    $quantimodo_activated = true;
    //wp_cache_flush();  // What is this for?
  }

?>
        <div class="wrap">
        <form name="QuantiModo-form" action="options.php" method="post" enctype="multipart/form-data">
          <?php settings_fields( 'QuantiModo_settings_group' ); ?>

            <h1>QuantiModo Settings</h1>
            <?php if ( esc_attr( $options['quantimodo_widget_code'] ) ) { ?>
                <h3>You can modify and design your app in the
                    <a href="https://builder.quantimo.do" target="_blank" title="Open QuantiModo app builder">QuantiModo app builder</a>.
                </h3>
                <?php if ( $quantimodo_activated ) { ?>
                    <h3>Click the icon in the lower right hand corner of
                        <a href="<?php echo get_bloginfo('wpurl');  ?>" target="_blank" title="Open WP Homepage">your homepage</a>
                        to see your app in action!
                    </h3>
                <?php } ?>
            <?php } ?>
            <?php if ( ! $quantimodo_activated ) { ?>
                <div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
                Floating button is currently <strong>DISABLED</strong>.
                </div>
            <?php } ?>
            <?php do_settings_sections( 'QuantiModo_settings_group' ); ?>

            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
                <tr>
                    <th width="30%" valign="top" style="padding-top: 10px;">
                        <label for="quantimodo_enabled">Floating button is:</label>
                    </th>
                    <td>
                      <?php
                          echo "<select name=\"QuantiModo_settings[quantimodo_enabled]\"  id=\"quantimodo_enabled\">\n";

                          echo "<option value=\"on\"";
                          if ( $quantimodo_activated ) { echo " selected='selected'"; }
                          echo ">Enabled</option>\n";

                          echo "<option value=\"off\"";
                          if ( ! $quantimodo_activated ) { echo" selected='selected'"; }
                          echo ">Disabled</option>\n";
                          echo "</select>\n";
                        ?>
                    </td>
                </tr>
            </table>
<!--            <label for="quantimodo_identify">QuantiModo Identify: &nbsp;</label>-->
<!--            <input type="checkbox" name="QuantiModo_settings[quantimodo_identify]" --><?php //if($quantimodo_identify) { echo " checked='checked'"; } ?><!-- />-->

            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
            <tr>
                <th valign="top" style="padding-top: 10px;">
                    <label for="QuantiModo_widget_code">QuantiModo Client Id</label>
                </th>
                <td>
                  <input type='text' placeholder="Enter client id" name="QuantiModo_settings[quantimodo_widget_code]"
                         value='<?php echo esc_attr( $options['quantimodo_widget_code'] );  ?>'/>
                </td>
            </tr>
            <tr>
                <th valign="top" style="padding-top: 10px;">
                    <label for="quantimodo_client_secret">QuantiModo Client Secret</label>
                </th>
                <td>
                    <input type='text' placeholder="Enter client secret" name="QuantiModo_settings[quantimodo_client_secret]"
                           value='<?php echo esc_attr( $options['quantimodo_client_secret'] );  ?>'/>
                </td>
            </tr>
            </table>
            <?php echo quantimodo_get_client_secret_instructions(); ?>
            <?php if ( ! esc_attr( $options['quantimodo_widget_code'] ) ) { ?>
                <h3>You can find your QuantiModo client id after
                    <a href="https://builder.quantimo.do" target="_blank" title="Open QuantiModo Settings">creating your free app in the App Builder</a>.
                </h3>
            <?php } ?>
            <p class="submit">
                <?php echo submit_button('Save Changes'); ?>
            </p>
            <h3>To embed a specific page of your QuantiModo app in a WordPress page or post:</h3>
            <ol>
                <li>Go to your QuantiModo web app at
                    <a href="https://<?php echo esc_attr( $options['quantimodo_widget_code'] );  ?>.quantimo.do" target="_blank" title="Open Web App">
                        https://<?php echo esc_attr( $options['quantimodo_widget_code'] );  ?>.quantimo.do
                    </a>.
                </li>
                <li>Go to the page you want to embed and copy the url.</li>
                <li>Go to the WordPress page or post editor "text" section.</li>
                <li>Paste
                    <xmp>
                        <iframe src="https://<?php echo esc_attr( $options['quantimodo_widget_code'] );  ?>.quantimo.do/WHATEVER_YOU_WANT_TO_EMBED" width="100%" height="650px" frameborder="1" scrolling="yes" align="left"></iframe>
                    </xmp>
                </li>
                <li>
                    Replace src="https://<?php echo esc_attr( $options['quantimodo_widget_code'] );  ?>.quantimo.do/WHATEVER_YOU_WANT_TO_EMBED" with your actual link you want to embed.
                </li>
                <li>Adjust or remove the iFrame settings as needed.</li>
            </ol>
        </div>
        </form>

<?php
}
?>
