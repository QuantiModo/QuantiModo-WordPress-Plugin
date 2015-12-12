<?php

// TODO: very important that we sanitize all $_POST variables here before using them!
// TODO: this doesn't call qmwp_end_login() which might result in the LAST_URL not being cleared...
/*
 *
 *
*/

class QMWPUserReg
{
    public $qmwp;

    function __construct($qmwp)
    {
        $this->qmwp = $qmwp;
    }

    public function registerUserFromIdentity($qmwpIdentity)
    {
        global $wpdb;

// initiate the user session:
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

// prevent users from registering if the option is turned off in the dashboard:
        if (!get_option("users_can_register")) {
            $this->qmwp->qmwp_end_login("Sorry, user registration is disabled at this time. Your account could not be registered. Please create a ticket at https://help.quantimo.do", true);
        }

// registration was initiated from an oauth provider, set the username and password automatically.
        if ($_SESSION["QMWP"]["USER_ID"] != "") {
            $userLoginName = $qmwpIdentity['loginName'];
            $password = wp_generate_password();
        }

// registration was initiated from the standard sign up form, set the username and password that was requested by the user.
        if ($_SESSION["QMWP"]["USER_ID"] == "") {
            // this registration was initiated from the standard Registration page, create account and login the user automatically
            $userLoginName = $_POST['identity'];
            $password = $_POST['password'];
        }

// now attempt to generate the user and get the user id:
        $user_id = wp_create_user($userLoginName, $password, $qmwpIdentity['email']); // we use wp_create_user instead of wp_insert_user so we can handle the error when the user being registered already exists

// check if the user was actually created:
        if (is_wp_error($user_id)) {
            // there was an error during registration, redirect and notify the user:
            $this->qmwp->qmwp_end_login($user_id->get_error_message(), true);
        }

// now try to update the username to something more permanent and recognizable:
        $userDisplayName = $qmwpIdentity['displayName'];
        $update_username_result = $wpdb->update($wpdb->users, array(
            'user_login' => $userLoginName,
            'user_nicename' => $userLoginName,
            'display_name' => $userDisplayName),
            array('ID' => $user_id));
        //$update_nickname_result = update_user_meta($user_id, 'nickname', $userLoginName);

// apply the custom default user role:
        $role = get_option('qmwp_new_user_role');
        $update_role_result = wp_update_user(array('ID' => $user_id, 'role' => $role));

// proceed if no errors were detected:
        //if ($update_username_result == false) {
            // there was an error during registration, redirect and notify the user:
        //    $this->qmwp->qmwp_end_login("Could not rename the username during registration. Please create a ticket at https://help.quantimo.do", true);
        //} elseif ($update_role_result == false) {
            // there was an error during registration, redirect and notify the user:
        //    $this->qmwp->qmwp_end_login("Could not assign default user role during registration. Please create a ticket at https://help.quantimo.do", true);
        //} else {
            // registration was successful, the user account was created, proceed to login the user automatically...
            // associate the wordpress user account with the now-authenticated third party account:
            $this->qmwp->qmwp_link_account($user_id);
            // attempt to login the new user (this could be error prone):

            $matched_user = $this->qmwp->qmwp_match_wordpress_user($qmwpIdentity);

            $user_id = $matched_user->ID;
            $user_login = $matched_user->user_login;
            wp_set_current_user($user_id, $user_login);
            wp_set_auth_cookie($user_id);
            do_action('wp_signon', $user_login, $matched_user);
            // after login, redirect to the user's last location
            $this->qmwp->update_user_tokens($user_id);
            $this->qmwp->qmwp_end_login("Logged in successfully!");

        //}
    }
}

?>
