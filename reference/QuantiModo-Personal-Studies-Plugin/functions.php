<?php

function check_empty_table($tablename, &$response) {

global $wpdb;

$result = $wpdb->get_results("SELECT * FROM $tablename");

   if(empty($result)){
    $response = '';
    return $response;
   }
 
  if(!empty($result)){
   $response = $result;
   return $response;
  }
}

function importdata($file) {

global $wpdb;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

// Name of the file
$plugindir = dirname( __FILE__ );
$filename =  $plugindir . '/metaboxes/database/' . $file;

if(file_exists($filename)){

// Temporary variable, used to store current query
$templine = '';
// Read in entire file
$lines = file($filename);

// Loop through each line
foreach ($lines as $line)
{
// Skip it if it's a comment
if (substr($line, 0, 2) == '--' || $line == '')
continue;

// Add this line to the current segment
$templine .= $line;

// If it has a semicolon at the end, it's the end of the query
if (substr(trim($line), -1, 1) == ';')
{
    // Perform the query
    $wpdb->query($templine);
    // Reset temp variable to empty
    $templine = '';
    }
 }
echo 'Importing the data now, this page will be auto refreshed in a while ..';
echo '<META HTTP-EQUIV="REFRESH" CONTENT="8">' ;
 }
 else 
   echo "Error Importing: Cannot find SQL File ...";
}
 
 
 
?>
