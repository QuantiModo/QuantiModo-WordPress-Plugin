<?php

require_once(plugin_dir_path(__FILE__) . '../QMWPAuth.php');

$authenticator = new QMWPAuth();

// start the user session for maintaining individual user states during the multi-stage authentication flow:
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

# DEFINE THE OAUTH PROVIDER TO USE #
$_SESSION['QMWP']['PROVIDER'] = 'QuantiModo';

// remember the user's last url so we can redirect them back to there after the login ends:
if (!isset($_SESSION['QMWP']['LAST_URL']) || !$_SESSION['QMWP']['LAST_URL']) {
    // try to obtain the redirect_url from the default login page:
    $redirect_url = esc_url($_GET['redirect_to']);
    // if no redirect_url was found, set it to the user's last page:
    if (!$redirect_url) {
        $redirect_url = strtok($_SERVER['HTTP_REFERER'], "?");
    }
    // set the user's last page so we can return that user there after they login:
    $_SESSION['QMWP']['LAST_URL'] = $redirect_url;
}

# AUTHENTICATION FLOW #
// the oauth 2.0 authentication flow will start in this script and make several calls to the third-party authentication provider which in turn will make callbacks to this script that we continue to handle until the login completes with a success or failure:
if (!$authenticator->clientEnabled) {
    $this->qmwp_end_login("This third-party authentication provider has not been enabled. Please notify the admin or try again later.");
} elseif (!$authenticator->clientId || !$authenticator->clientSecret) {
    // do not proceed if id or secret is null:
    $this->qmwp_end_login("This third-party authentication provider has not been configured with an API key/secret. Please notify the admin or try again later.");
} elseif (isset($_GET['error_description'])) {
    // do not proceed if an error was detected:
    $this->qmwp_end_login($_GET['error_description']);
} elseif (isset($_GET['error_message'])) {
    // do not proceed if an error was detected:
    $this->qmwp_end_login($_GET['error_message']);
} elseif (isset($_GET['code'])) {
    // post-auth phase, verify the state:
    if ($_SESSION['QMWP']['STATE'] == $_GET['state']) {
        // get an access token from the third party provider:
        $authenticator->get_oauth_token($this, $_GET['code']);
        // get the user's third-party identity and attempt to login/register a matching wordpress user account:
        $oauth_identity = $authenticator->get_oauth_identity($this);
        $this->qmwp_login_user($oauth_identity);
    } else {
        // possible CSRF attack, end the login with a generic message to the user and a detailed message to the admin/logs in case of abuse:
        // TODO: report detailed message to admin/logs here...
        $this->qmwp_end_login("Sorry, we couldn't log you in. Please notify the admin or try again later.");
    }
} else {
    // pre-auth, start the auth process:
    if ((empty($_SESSION['QMWP']['EXPIRES_AT'])) || (time() > $_SESSION['QMWP']['EXPIRES_AT'])) {
        // expired token; clear the state:
        $this->qmwp_clear_login_state();
    }
    $authenticator->get_oauth_code($this);
}
// we shouldn't be here, but just in case...
$this->qmpw_end_login("Sorry, we couldn't log you in. The authentication flow terminated in an unexpected way. Please notify the admin or try again later.");
# END OF AUTHENTICATION FLOW #
?>