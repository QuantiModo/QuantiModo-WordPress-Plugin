<?php
/*
Template Name: user-id
*/
$user_id = get_current_user_id();
if ($user_id == 0) {
    echo 'none';
} else {
    if (is_super_admin()) echo "admin";
    echo $user_id;
}
?>
