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
    $floating_button_enabled = isset($options['floating_button_enabled']) && $options['floating_button_enabled'] == "on";

  if ($floating_button_enabled) {
    //wp_cache_flush();  // What is this for?
  }
    $quantimodo_client_id = (isset($options['quantimodo_client_id'])) ? $options['quantimodo_client_id'] : '';
  $builderUrl = 'https://builder.quantimo.do';
  if($quantimodo_client_id){
      $builderUrl = $builderUrl . '?clientId=' . $quantimodo_client_id;
  }
  $introUrl = 'https://app.quantimo.do/app/public/#/app/intro?clientId='.$quantimodo_client_id;

?>
        <div class="wrap">
        <form name="QuantiModo-form" action="options.php" method="post" enctype="multipart/form-data">
          <?php settings_fields( 'QuantiModo_settings_group' ); ?>

            <h1>QuantiModo Settings</h1>
            <?php
            if ($quantimodo_client_id) { ?>
                <h3>You can modify and design your app in the
                    <a href="<?php echo $builderUrl;  ?>" target="_blank" title="Open QuantiModo app builder">QuantiModo app
                        builder</a>.
                </h3>
                <?php if ( $floating_button_enabled ) { ?>
                    <h3>Click the icon in the lower right hand corner of
                        <a href="<?php echo get_bloginfo('wpurl');  ?>" target="_blank" title="Open WP Homepage">your homepage</a>
                        to see your app in action!
                    </h3>
                <?php } ?>
            <?php } ?>
            <?php if ( ! $floating_button_enabled ) { ?>
                <div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
                Floating button is currently <strong>DISABLED</strong>.
                </div>
            <?php } ?>
            <?php do_settings_sections( 'QuantiModo_settings_group' ); ?>

            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
                <tr>
                    <th width="30%" valign="top" style="padding-top: 10px;">
                        <label for="floating_button_enabled">Floating button is:</label>
                    </th>
                    <td>
                      <?php
                          echo "<select name=\"QuantiModo_settings[floating_button_enabled]\"  id=\"floating_button_enabled\">\n";

                          echo "<option value=\"on\"";
                          if ( $floating_button_enabled ) { echo " selected='selected'"; }
                          echo ">Enabled</option>\n";

                          echo "<option value=\"off\"";
                          if ( ! $floating_button_enabled ) { echo" selected='selected'"; }
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
                    <label for="quantimodo_client_id">QuantiModo Client Id</label>
                </th>
                <td>
                  <input type='text' placeholder="Enter client id" name="QuantiModo_settings[quantimodo_client_id]"
                         value='<?php echo esc_attr( $options['quantimodo_client_id'] ?? '' );  ?>'/>
                </td>
            </tr>
            <tr>
                <th valign="top" style="padding-top: 10px;">
                    <label for="quantimodo_client_secret">QuantiModo Client Secret</label>
                </th>
                <td>
                    <input type='text' placeholder="Enter client secret" name="QuantiModo_settings[quantimodo_client_secret]"
                           value='<?php echo esc_attr( $options['quantimodo_client_secret'] ?? ''  );  ?>'/>
                </td>
            </tr>
            </table>
            <?php echo quantimodo_get_client_secret_instructions(); ?>
            <?php if ( ! $quantimodo_client_id ) { ?>
                <h3>You can find your QuantiModo client id after
                    <a href="https://builder.quantimo.do" target="_blank" title="Open QuantiModo Settings">
                        creating your free app in the App Builder</a>.
                </h3>
            <?php } ?>
            <p class="submit">
                <?php echo submit_button('Save Changes'); ?>
            </p>
            <h3>To embed a specific page of your QuantiModo app in a WordPress page or post:</h3>
            <ol>
                <li>Go to your QuantiModo web app at
<!--                    <a href="https://--><?php //echo esc_attr( $options['quantimodo_client_id'] );  ?><!--.quantimo.do" target="_blank" title="Open Web App">-->
<!--                        https://--><?php //echo esc_attr( $options['quantimodo_client_id'] );  ?><!--.quantimo.do-->
<!--                    </a>.-->
                    <a href="<?php echo $introUrl;  ?>" target="_blank" title="Open Web App">
                        <?php echo $introUrl;  ?>
                    </a>.
                </li>
                <li>Go to the QuantiModo page you want to embed and copy the url.</li>
                <li>Go to the WordPress page or post-editor "text" section where you want the embed.</li>
                <li>Paste
<!--                    <xmp>-->
<!--                        <iframe src="https://--><?php //echo $quantimodo_client_id;  ?><!--.quantimo.do/WHATEVER_YOU_WANT_TO_EMBED" width="100%" height="650px" frameborder="1" scrolling="yes" align="left"></iframe>-->
<!--                    </xmp>-->
                    <xmp>
                        <iframe src="THE_URL_YOU_COPIED_WITH_HTTPS_AND_WITHOUT_ANY_TRAILING_URL_PARAMS?clientId=<?php echo $quantimodo_client_id;  ?>"
                                width="100%" height="650px" frameborder="1" scrolling="yes" align="left">
                        </iframe>
                    </xmp>
                </li>
<!--                <li>
                    Replace src="https://<?php /*echo $quantimodo_client_id;  */?>.quantimo.do/WHATEVER_YOU_WANT_TO_EMBED" with your actual link you want to embed.
                </li>-->
                <li>
                    Replace src="THE_URL_YOU_COPIED_WITH_HTTPS_AND_WITHOUT_ANY_TRAILING_URL_PARAMS" with your actual link you want to embed.
                    Make sure to delete anything after a question mark in the url if there is one.
                    Then add "?clientId=<?php echo $quantimodo_client_id; ?>" to the end of the url.
                </li>
                <li>Adjust or remove the iFrame settings as needed.</li>
            </ol>
        </div>
        </form>

<?php
}
?>
