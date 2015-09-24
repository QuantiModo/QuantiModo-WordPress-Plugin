<?php

require_once('QMWPAuth.php');

// config check security
function qmwp_cc_security()
{
    $points = 0;
    if (strpos(site_url(), "https://")) {
        $points += 2;
    }
    if (get_option('qmwp_hide_wordpress_login_form') == 1) {
        $points += 1;
    }
    if (get_option('qmwp_logout_inactive_users') > 0) {
        $points += 1;
    }
    if (get_option('qmwp_http_util_verify_ssl') == 1) {
        $points += 1;
    }
    if (get_option('qmwp_http_util') == 'curl') {
        $points += 1;
    }
    $points_max = 6;
    return floor(($points / $points_max) * 100);
}

// config check privacy
function qmwp_cc_privacy()
{
    $points = 0;
    if (get_option('qmwp_logout_inactive_users') > 0) {
        $points += 1;
    }
    // TODO: +1 for NOT using email address matching
    $points_max = 1;
    return floor(($points / $points_max) * 100);
}

// config check user experience
function qmwp_cc_ux()
{
    $points = 0;
    if (get_option('qmwp_logo_links_to_site') == 1) {
        $points += 1;
    }
    if (get_option('qmwp_show_login_messages') == 1) {
        $points += 1;
    }
    $points_max = 2;
    return floor(($points / $points_max) * 100);
}

// cache the config check ratings:
$cc_security = qmwp_cc_security();
$cc_privacy = qmwp_cc_privacy();
$cc_ux = qmwp_cc_ux();
?>


<div class='wrap qmwp-settings'>
    <div id="qmwp-settings-meta">Toggle tips:
        <ul>
            <li><a id="qmwp-settings-tips-on" href="#">On</a></li>
            <li><a id="qmwp-settings-tips-off" href="#">Off</a></li>
        </ul>
        <div class="nav-splitter"></div>
        Toggle sections:
        <ul>
            <li><a id="qmwp-settings-sections-on" href="#">On</a></li>
            <li><a id="qmwp-settings-sections-off" href="#">Off</a></li>
        </ul>
    </div>
    <h2>QuantiModo for WordPress Settings</h2>
    <!-- START Settings Header -->
    <div id="qmwp-settings-header"></div>
    <!-- END Settings Header -->
    <!-- START Settings Body -->
    <div id="qmwp-settings-body">
        <!-- START Settings Column 2 -->
        <div id="qmwp-settings-col2" class="qmwp-settings-column">
            <div id="qmwp-settings-section-about" class="qmwp-settings-section">
                <h3>About</h3>

                <div class='form-padding'>
                    <div id="qmwp-logo" style="width:64px; height:64px; float:right; background-size:100% 100%;"></div>
                    <p>
                        <span
                            style="font-size:1.1em;"><strong>QuantiModo <?php echo QMWP::PLUGIN_VERSION; ?></strong></span><br/>by
                        <a href="https://app.quantimo.do" target="_blank"><strong>QuantiModo</strong></a>
                    </p>

                    <!-- <p>Rate it 5 stars: <a id="qmwp-rate-5stars"
                                           href="https://wordpress.org/support/view/plugin-reviews/quantimodo?rate=5"
                                           target="_blank"><img src="//ps.w.org/qm-oauth/assets/5stars.png"
                                                                style="vertical-align:text-top;"></a></p> -->
                    <nav>
                        <ul>
                            <!-- <li><a href="https://wordpress.org/plugins/qm-oauth/" target="_blank">QuantiModo at
                                    WordPress.org</a></li>
                            <li><a href="https://github.com/Abolitionist-Project/QuantiModo-WordPress-Plugin"
                                   target="_blank">QuantiModo at Github</a></li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- <div id="qmwp-settings-section-news" class="qmwp-settings-section">
                <h3>News</h3>

                <div class='form-padding'>
                    <?php
                    $rss = fetch_feed("http://glassocean.net/tag/qm-oauth/feed/");
                    if (!is_wp_error($rss)) {
                        $maxitems = $rss->get_item_quantity(5);
                        $rss_items = $rss->get_items(0, $maxitems);
                    }
                    ?>
                    <?php if (isset($maxitems) && $maxitems == 0) : ?>
                        <p><?php _e("Sorry, news was inaccessible or does not exist.", 'my-text-domain'); ?></p>
                    <?php elseif (isset($rss_items) && count($rss_items) > 0) : ?>
                        <ul>
                            <?php foreach ($rss_items as $item) : ?>
                                <li>
                                    <a href="<?php echo esc_url($item->get_permalink()); ?>"
                                       title="<?php printf(__('Posted %s', 'my-text-domain'), $item->get_date('j F Y | g:i a')); ?>"
                                       target="_blank">
                                        <?php echo esc_html($item->get_title()); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div> -->
            <div id="qmwp-settings-section-config-check" class="qmwp-settings-section">
                <h3>Config Check</h3>

                <div class='form-padding'>
                    <p>These ratings are an estimate of <em>this plugin's current configuration</em> when compared to an
                        optimum configuration.</p>

                    <div id='qmwp-measurements'>
                        <div class="has-tip">
                            <div class="qmwp-measurement-label">Security: <?php echo $cc_security; ?>% <a href="#"
                                                                                                          class="tip-button">[?]</a>
                            </div>
                            <div class="qmwp-measurement">
                                <div class="qmwp-measurement-rating" style="width:<?php echo $cc_security; ?>%;"></div>
                            </div>
                            <p class="tip-message">
                                +2 if site is secured with an SSL certificate<br/>
                                +1 if Verify Peer/Host SSL Certificates = True<br/>
                                +1 if Hide the WordPress login form = True<br/>
                                +1 if Automatically logout inactive users = True<br/>
                                +1 if HTTP Utility = cURL<br/>
                            </p>
                        </div>
                        <div class="has-tip">
                            <div class="qmwp-measurement-label">Privacy: <?php echo $cc_privacy; ?>% <a href="#"
                                                                                                        class="tip-button">[?]</a>
                            </div>
                            <div class="qmwp-measurement">
                                <div class="qmwp-measurement-rating" style="width:<?php echo $cc_privacy; ?>%;"></div>
                            </div>
                            <p class="tip-message">
                                +1 if Automatically logout inactive users = True<br/>
                            </p>
                        </div>
                        <div class="has-tip">
                            <div class="qmwp-measurement-label">User Experience: <?php echo $cc_ux; ?>% <a href="#"
                                                                                                           class="tip-button">[?]</a>
                            </div>
                            <div class="qmwp-measurement">
                                <div class="qmwp-measurement-rating" style="width:<?php echo $cc_ux; ?>%;"></div>
                            </div>
                            <p class="tip-message">
                                +1 if Logo links to site = True<br/>
                                +1 if Show login messages = True<br/>
                            </p>
                        </div>
                    </div>

                    <p>
                        Current API Host: <?php echo QMWPAuth::API_HOST; ?>
                    </p>
                </div>
            </div>
            <div id="qmwp-settings-section-donate" class="qmwp-settings-section">
                <h3>Donate</h3>

                <div class='form-padding'>
                    <div id="qmwp-heart"></div>
                    <p>Do you enjoy using this plugin? Do you want to help improve it? Consider donating via <strong>PayPal</strong>!
                    </p>

                    <p>The QuantiModo WP plugin remains free for open source projects. <em>Your donations help make this
                            possible.</em></p>

                    <form action="https://www.paypal.com/cgi-bin/webscr" target="_blank" method="post">
                        $ <input type="text" style="width: 50px" name="amount" value="5">
                        <span>USD</span>
                        <input type="hidden" value="_xclick" name="cmd">
                        <input type="hidden" value="abolish-suffering@quantimo.do" name="business">
                        <input type="hidden" value="QuantiModo" name="item_name">
                        <input type="hidden" value="0" name="no_shipping">
                        <input type="hidden" value="1" name="no_note">
                        <input type="hidden" value="Return to your dashboard" name="cbt">
                        <input type="hidden" value="USD" name="currency_code">
                        <input type="submit" id="qmwp-paypal-button" class="button" value="Donate">
                    </form>
                </div>
            </div>
            <div id="qmwp-settings-section-live-demo" class="qmwp-settings-section">
                <h3 id="bookmark-live-demo">Live Demo</h3>

                <div class='form-padding'>
                    <p>A live demo is available at <a href="https://quantipress.quantimo.do" target="_blank">quantipress.quantimo.do</a>.
                    </p>
                </div>
            </div>
            <div id="qmwp-settings-section-support" class="qmwp-settings-section">
                <h3 id="bookmark-login-page-form-customization">Support</h3>

                <div class='form-padding'>
                    <p>Your general questions can be asked in the <a
                            href="http://help.quantimo.do" target="_blank">support forum</a>.</p>
                </div>
            </div>
        </div>
        <!-- END Settings Column 2 -->
        <!-- START Settings Column 1 -->
        <div id="qmwp-settings-col1" class="qmwp-settings-column">
            <form method='post' action='options.php'>
                <?php settings_fields('qmwp_settings'); ?>
                <?php do_settings_sections('qmwp_settings'); ?>

                <!--  START Shortcodes section-->
                <div id="qmwp-settings-section-shortcodes" class="qmwp-settings-section">
                    <h3>Plugin Short Codes</h3>

                    <div class='form-padding'>
                        <p>QuantiModo WordPress plugin gives capability to render shortcodes at the posts and pages.</p>

                        <p>Currently plugin supports following shortcodes:</p>
                        <ul>
                            <li>
                                <strong>[qmwp_mood_tracker]</strong> - Allow users to rate their moods
                                <a target="_blank" href="/qmwp-mood-tracker">Take a look</a>)
                            </li>
                            <li>
                                <strong>[qmwp_timeline]</strong> - Graph any variable over time
                                <a href="/qmwp-timeline" target="_blank">Take a look</a>
                            </li>
                            <li><strong>[qmwp_connectors]</strong> - Allow users to import their data from 3rd party sources
                                <a href="/qmwp-connectors" target="_blank">Take a look</a>
                            </li>
                            <!-- <li><strong>[qmwp_manage_accounts]</strong> - renders accounts management shortcode
                                <a href="/qmwp-manage-accounts" target="_blank">Take a look</a>
                            </li> -->
                            <li><strong>[qmwp_bargraph_scatterplot_timeline]</strong> - Find out the top predictors for mood
                                <a href="/qmwp-bargraph-scatterplot-timeline">Take a look</a>
                            </li>
                            <li><strong>[qmwp_search_correlations]</strong> - Search for predictors or likely effects of a given variable
                                <a href="/qmwp-search-correlations">Take a look</a>
                            </li>
                            <li><strong>[qmwp_add_measurement]</strong> - Track anything!
                                measurements
                                <a href="/qmwp-add-measurement">Take a look</a>
                            </li>
                        </ul>

                    </div>

                </div>
                <!--  END Shortcodes section-->

                <!-- START General Settings section -->
                <div id="qmwp-settings-section-general-settings" class="qmwp-settings-section">
                    <h3>General Settings</h3>

                    <div class='form-padding'>
                        <table class='form-table'>
                            <tr valign='top' class='has-tip' class="has-tip">
                                <th scope='row'>Show login messages: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <input type='checkbox' name='qmwp_show_login_messages'
                                           value='1' <?php checked(get_option('qmwp_show_login_messages') == 1); ?> />

                                    <p class="tip-message">Shows a short-lived notification message to the user which
                                        indicates whether or not the login was successful, and if there was an
                                        error.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Login redirects to: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <select name='qmwp_login_redirect'>
                                        <option
                                            value='home_page' <?php selected(get_option('qmwp_login_redirect'), 'home_page'); ?>>
                                            Home Page
                                        </option>
                                        <option
                                            value='last_page' <?php selected(get_option('qmwp_login_redirect'), 'last_page'); ?>>
                                            Last Page
                                        </option>
                                        <option
                                            value='specific_page' <?php selected(get_option('qmwp_login_redirect'), 'specific_page'); ?>>
                                            Specific Page
                                        </option>
                                        <option
                                            value='admin_dashboard' <?php selected(get_option('qmwp_login_redirect'), 'admin_dashboard'); ?>>
                                            Admin Dashboard
                                        </option>
                                        <option
                                            value='user_profile' <?php selected(get_option('qmwp_login_redirect'), 'user_profile'); ?>>
                                            User's Profile Page
                                        </option>
                                        <option
                                            value='custom_url' <?php selected(get_option('qmwp_login_redirect'), 'custom_url'); ?>>
                                            Custom URL
                                        </option>
                                    </select>
                                    <?php wp_dropdown_pages(array("id" => "qmwp_login_redirect_page", "name" => "qmwp_login_redirect_page", "selected" => get_option('qmwp_login_redirect_page'))); ?>
                                    <input type="text" name="qmwp_login_redirect_url"
                                           value="<?php echo get_option('qmwp_login_redirect_url'); ?>"
                                           style="display:none;"/>

                                    <p class="tip-message">Specifies where to redirect a user after they log in.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Logout redirects to: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <select name='qmwp_logout_redirect'>
                                        <option
                                            value='default_handling' <?php selected(get_option('qmwp_logout_redirect'), 'default_handling'); ?>>
                                            Let WordPress handle it
                                        </option>
                                        <option
                                            value='home_page' <?php selected(get_option('qmwp_logout_redirect'), 'home_page'); ?>>
                                            Home Page
                                        </option>
                                        <option
                                            value='last_page' <?php selected(get_option('qmwp_logout_redirect'), 'last_page'); ?>>
                                            Last Page
                                        </option>
                                        <option
                                            value='specific_page' <?php selected(get_option('qmwp_logout_redirect'), 'specific_page'); ?>>
                                            Specific Page
                                        </option>
                                        <option
                                            value='admin_dashboard' <?php selected(get_option('qmwp_logout_redirect'), 'admin_dashboard'); ?>>
                                            Admin Dashboard
                                        </option>
                                        <option
                                            value='user_profile' <?php selected(get_option('qmwp_logout_redirect'), 'user_profile'); ?>>
                                            User's Profile Page
                                        </option>
                                        <option
                                            value='custom_url' <?php selected(get_option('qmwp_logout_redirect'), 'custom_url'); ?>>
                                            Custom URL
                                        </option>
                                    </select>
                                    <?php wp_dropdown_pages(array("id" => "qmwp_logout_redirect_page", "name" => "qmwp_logout_redirect_page", "selected" => get_option('qmwp_logout_redirect_page'))); ?>
                                    <input type="text" name="qmwp_logout_redirect_url"
                                           value="<?php echo get_option('qmwp_logout_redirect_url'); ?>"
                                           style="display:none;"/>

                                    <p class="tip-message">Specifies where to redirect a user after they log out.</p>
                                </td>
                            </tr>


                        </table>
                        <!-- .form-table -->
                        <?php submit_button('Save all settings'); ?>
                    </div>
                    <!-- .form-padding -->
                </div>
                <!-- .qmwp-settings-section -->
                <!-- END General Settings section -->


                <!-- START User Registration section -->
                <div id="qmwp-settings-section-user-registration" class="qmwp-settings-section">
                    <h3>User Registration</h3>

                    <div class='form-padding'>
                        <table class='form-table'>
                            <tr valign='top' class="has-tip">
                                <th scope='row'>Suppress default welcome email: <a href="#" class="tip-button">[?]</a>
                                </th>
                                <td>
                                    <input type='checkbox' name='qmwp_suppress_welcome_email'
                                           value='1' <?php checked(get_option('qmwp_suppress_welcome_email') == 1); ?> />

                                    <p class="tip-message">Prevents WordPress from sending an email to newly registered
                                        users by default, which contains their username and password.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Assign new users to the following role: <a href="#" class="tip-button">[?]</a>
                                </th>
                                <td>
                                    <select
                                        name="qmwp_new_user_role"><?php wp_dropdown_roles(get_option('qmwp_new_user_role')); ?></select>

                                    <p class="tip-message">Specifies what user role will be assigned to newly registered
                                        users.</p>
                                </td>
                            </tr>
                        </table>
                        <!-- .form-table -->
                        <?php submit_button('Save all settings'); ?>
                    </div>
                    <!-- .form-padding -->
                </div>
                <!-- .qmwp-settings-section -->
                <!-- END User Registration section -->

                <!-- START Login with QuantiModo section -->
                <div id="qmwp-settings-section-login-with-quantimodo" class="qmwp-settings-section">
                    <h3>Credentials</h3>

                    <div class='form-padding'>
                        <table class='form-table'>
                            <tr valign='top'>
                                <th scope='row'>Enabled:</th>
                                <td>
                                    <input type='checkbox' name='qmwp_quantimodo_api_enabled'
                                           value='1' <?php checked(get_option('qmwp_quantimodo_api_enabled') == 1); ?> />
                                </td>
                            </tr>

                            <tr valign='top'>
                                <th scope='row'>Client ID:</th>
                                <td>
                                    <input type='text' name='qmwp_quantimodo_api_id'
                                           value='<?php echo get_option('qmwp_quantimodo_api_id'); ?>'/>
                                </td>
                            </tr>

                            <tr valign='top'>
                                <th scope='row'>Client Secret:</th>
                                <td>
                                    <input type='text' name='qmwp_quantimodo_api_secret'
                                           value='<?php echo get_option('qmwp_quantimodo_api_secret'); ?>'/>
                                </td>
                            </tr>

                            <tr valign='top'>
                                <th scope='row'>X-Mashape-Key:</th>
                                <td>
                                    <input type='text' name='qmwp_x_mashape_key'
                                           value='<?php echo get_option('qmwp_x_mashape_key'); ?>'/>
                                </td>
                            </tr>

                        </table>
                        <!-- .form-table -->
                        <p>
                            <strong>Instructions:</strong>
                        <ol>
                            <li>Visit the QuantiModo website for developers <a
                                    href='https://developer.quantimo.do' target="_blank">developer.quantimo.do</a>.
                            </li>
                            <li>Create a new app on Mashape and enable the QuantiModo API. This will enable
                                your site to access the QuantiModo API.
                            </li>
                            <li>At QuantiModo, provide your site's homepage URL (<?php echo $blog_url; ?>) for the new
                                Project's Redirect URI. Don't forget the trailing slash!
                            </li>
                            <li>At QuantiModo, you must also configure the Consent Screen with your Email Address and
                                Product Name. This is what QuantiModo will display to users when they are asked to grant
                                access to your site/app.
                            </li>
                            <li>Paste your Client ID/Secret provided by QuantiModo into the fields above, then click the
                                Save all settings button.
                            </li>
                        </ol>
                        </p>
                        <?php submit_button('Save all settings'); ?>
                    </div>
                    <!-- .form-padding -->
                </div>
                <!-- .qmwp-settings-section -->
                <!-- END Login with QuantiModo section -->


                <!-- START Back Channel Configuration section -->
<!--                <div id="qmwp-settings-section-back-channel=configuration" class="qmwp-settings-section">
                    <h3>Back Channel Configuration</h3>

                    <div class='form-padding'>
                        <p>These settings are for troubleshooting and/or fine tuning the back channel communication this
                            plugin utilizes between your server and the third-party providers.</p>
                        <table class='form-table'>
                            <tr valign='top' class="has-tip">
                                <th scope='row'>HTTP utility: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <select name='qmwp_http_util'>
                                        <option value='curl' <?php /*selected(get_option('qmwp_http_util'), 'curl'); */?>>
                                            cURL
                                        </option>
                                        <option
                                            value='stream-context' <?php /*selected(get_option('qmwp_http_util'), 'stream-context'); */?>>
                                            Stream Context
                                        </option>
                                    </select>

                                    <p class="tip-message">The method used by the web server for performing HTTP
                                        requests to the third-party providers. Most servers support cURL, but some
                                        servers may require Stream Context instead.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Verify Peer/Host SSL Certificates: <a href="#"
                                                                                      class="tip-button">[?]</a></th>
                                <td>
                                    <input type='checkbox' name='qmwp_http_util_verify_ssl'
                                           value='1' <?php /*checked(get_option('qmwp_http_util_verify_ssl') == 1); */?> />

                                    <p class="tip-message">Determines whether or not to validate peer/host SSL
                                        certificates during back channel HTTP calls to the third-party login providers.
                                        If your server has an incorrect SSL configuration or doesn't support SSL, you
                                        may try disabling this setting as a workaround.</p>

                                    <p class="tip-message tip-warning"><strong>Warning:</strong> Disabling this is not
                                        recommended. For maximum security it would be a good idea to get your server's
                                        SSL configuration fixed and keep this setting enabled.</p>
                                </td>
                            </tr>
                        </table>
                        <!-- .form-table -->
                        <?php /*submit_button('Save all settings'); */?>
                    </div>
                    <!-- .form-padding -->
                </div>-->
                <!-- .qmwp-settings-section -->
                <!-- END Back Channel Configuration section -->

                <!-- START Maintenance & Troubleshooting section -->
<!--                <div id="qmwp-settings-section-maintenance-troubleshooting" class="qmwp-settings-section">
                    <h3>Maintenance & Troubleshooting</h3>

                    <div class='form-padding'>
                        <table class='form-table'>
                            <tr valign='top' class="has-tip">
                                <th scope='row'>Restore default settings: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <input type='checkbox' name='qmwp_restore_default_settings'
                                           value='1' <?php /*checked(get_option('qmwp_restore_default_settings') == 1); */?> />

                                    <p class="tip-message"><strong>Instructions:</strong> Check the box above, click the
                                        Save all settings button, and the settings will be restored to default.</p>

                                    <p class="tip-message tip-warning"><strong>Warning:</strong> This will restore the
                                        default settings, erasing any API keys/secrets that you may have entered above.
                                    </p>
                                </td>
                            </tr>
                            <tr valign='top' class="has-tip">
                                <th scope='row'>Delete settings on uninstall: <a href="#" class="tip-button">[?]</a>
                                </th>
                                <td>
                                    <input type='checkbox' name='qmwp_delete_settings_on_uninstall'
                                           value='1' <?php /*checked(get_option('qmwp_delete_settings_on_uninstall') == 1); */?> />

                                    <p class="tip-message"><strong>Instructions:</strong> Check the box above, click the
                                        Save all settings button, then uninstall this plugin as normal from the Plugins
                                        page.</p>

                                    <p class="tip-message tip-warning"><strong>Warning:</strong> This will delete all
                                        settings that may have been created in your database by this plugin, including
                                        all linked third-party login providers. This will not delete any WordPress user
                                        accounts, but users who may have registered with or relied upon their
                                        third-party login providers may have trouble logging into your site. Make
                                        absolutely sure you won't need the values on this page any time in the future,
                                        because they will be deleted permanently.</p>
                                </td>
                            </tr>
                        </table>
                        <!-- .form-table -->
                        <?php /*submit_button('Save all settings'); */?>
                    </div>
                    <!-- .form-padding -->
                </div>-->
                <!-- .qmwp-settings-section -->
                <!-- END  Maintenance & Troubleshooting section -->
            </form>
            <!-- form -->
        </div>
        <!-- END Settings Column 1 -->
    </div>
    <!-- #qmwp-settings-body -->
    <!-- END Settings Body -->
</div> <!-- .wrap .qmwp-settings -->
