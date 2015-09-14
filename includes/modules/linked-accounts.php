<?php
// get the current user:
global $current_user;
get_currentuserinfo();
$user_id = $current_user->ID;
// get the qm_identity records:
global $wpdb;
$usermeta_table = $wpdb->usermeta;
$query_string =
    "SELECT * FROM $usermeta_table WHERE $user_id = $usermeta_table.user_id AND" .
    " $usermeta_table.meta_key = 'qmwp_identity'";
$query_result = $wpdb->get_results($query_string);

?>

<div id="qmwp-linked-accounts">
    <h3>Linked Accounts</h3>

    <p>Manage the linked accounts which you have previously authorized to be used for logging into this website</p>
    <table class="form-table">

        <tr valign="top">
            <th scope="row">Your Linked Providers</th>
            <td>

                <?php if (count($query_result) == 0) : ?>
                    <p>You currently don't have any accounts linked</p>
                <?php endif; ?>

                <div class='qmwp-linked-accounts'>

                    <?php foreach ($query_result as $qmwp_row) {
                        $qm_identity_parts = explode('|', $qmwp_row->meta_value);
                        $oauth_provider = $qm_identity_parts[0];
                        $oauth_id = $qm_identity_parts[1]; // keep this private, don't send to client
                        $time_linked = $qm_identity_parts[2];
                        $local_time = strtotime("-" . $_COOKIE['gmtoffset'] . ' hours', $time_linked);
                        echo "<div>" .
                            $oauth_provider .
                            " on " .
                            date('F d, Y h:i A', $local_time) .
                            " <a class='qmwp-unlink-account' data-qmwp-identity-row='" .
                            $qmwp_row->umeta_id . "' href='#'>Unlink</a></div>";
                    } ?>

                </div>

            </td>
        </tr>

        <tr valign="top">
            <th scope="row">Link Another Provider</th>
            <td>
                <?php
                $design = get_option('qmwp_login_form_show_profile_page');
                if ($design != "None") {
                    // TODO: we need to use $settings defaults here, not hard-coded defaults...
                    echo $this->qmwp_login_form_content(
                        $design, 'none', 'buttons-row', 'Link', 'left', 'always', 'never', 'Select a provider:',
                        'Select a provider:', 'Authenticating...', '');
                } ?>
            </td>
        </tr>

    </table>
</div>
