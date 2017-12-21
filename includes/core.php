<?php

// Register settings
function QuantiModo_register_settings()
{
  register_setting( 'QuantiModo_settings_group', 'QuantiModo_settings' );
}
add_action( 'admin_init', 'QuantiModo_register_settings' );

// Delete options on uninstall
function QuantiModo_uninstall()
{
  delete_option( 'QuantiModo_settings' );
}
register_uninstall_hook( __FILE__, 'QuantiModo_uninstall' );


?>
