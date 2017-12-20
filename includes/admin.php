<?php

// Output the options page
function quantimodo_options_page()
{
  // Get options
  $options = get_option('QuantiModo_settings');

  // Check to see if QuantiModo is enabled
  $quantimodo_activated = false;
  if ( esc_attr( $options['quantimodo_enabled'] ) == "on" ) {
    $quantimodo_activated = true;
    wp_cache_flush();
  }

  // Check to see if QuantiModo identify is checked
  $quantimodo_identify = false;
  if ( isset($options['quantimodo_identify']) && esc_attr( $options['quantimodo_identify'] ) == "on" ) {
    $quantimodo_identify = true;
    wp_cache_flush();
  }

?>
        <div class="wrap">
        <form name="QuantiModo-form" action="options.php" method="post" enctype="multipart/form-data">
          <?php settings_fields( 'QuantiModo_settings_group' ); ?>

            <h1>QuantiModo</h1>
            <h3>Basic Options</h3>
            <?php if ( ! $quantimodo_activated ) { ?>
                <div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
                QuantiModo Live Chat is currently <strong>DISABLED</strong>.
                </div>
            <?php } ?>
            <?php do_settings_sections( 'QuantiModo_settings_group' ); ?>

            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
                <tr>
                    <th width="30%" valign="top" style="padding-top: 10px;">
                        <label for="quantimodo_enabled">QuantiModo (Live Chat) is:</label>
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
            <label for="quantimodo_identify">QuantiModo Identify: &nbsp;</label>
            <input type="checkbox" name="QuantiModo_settings[quantimodo_identify]" <?php if($quantimodo_identify) { echo " checked='checked'"; } ?> />
                <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
                <tr>
                    <th valign="top" style="padding-top: 10px;">
                        <label for="QuantiModo_widget_code">QuantiModo JS code snippet:</label>
                    </th>
                    <td>
                      <textarea rows="15" cols="100" placeholder="<!-- Insert the QuantiModo tag here -->" name="QuantiModo_settings[quantimodo_widget_code]"><?php echo esc_attr( $options['quantimodo_widget_code'] ); ?></textarea>
                        <p style="margin: 5px 10px;">Enter your QuantiModo JS code snippet.  You can find your <a href="https://app.quantimo.do/api/v2/apps" target="_blank" title="Open QuantiModo Settings">QuantiModo JS code snippet by clicking Integrate here</a>. A Free QuantiModo account is required to use this plugin.</p>
                    </td>
                </tr>
                </table>
            <p class="submit">
                <?php echo submit_button('Save Changes'); ?>
            </p>
        </div>
        </form>

<?php
}
?>
