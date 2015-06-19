<?php 
global $wpdb;
$postid = $wpdb->get_results('SELECT post_id FROM qm_postmeta');
$metaid = $wpdb->get_results('SELECT meta_id FROM qm_postmeta');
$metakey = $wpdb->get_results('SELECT meta_key FROM qm_postmeta');
$metavalue = $wpdb->get_results('SELECT meta_value FROM qm_postmeta');
$currentpostid = get_the_ID();

if(!empty($metavalue)){
  while($postid == $currentpostid){
    $metavalue = $variable;
    $variable == 'variableName';
    return $$variable;
  }
}
?>