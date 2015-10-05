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
                                   target="_blank">QuantiModo at GitHub.com</a></li> -->
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
                        <table class="available-shortcodes">
                            <thead>
                            <tr>
                                <th>Shortcode</th>
                                <th>Supported attributes</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="shortcode">[qmwp_mood_tracker]</td>
                                <td>
                                    <ul>
                                        <li>tracked_variable_name</li>
                                    </ul>
                                    <p>
                                        Specify variable name to track. This kind of tracker supports
                                        variables which can be measured by rate 1..5
                                    </p>

                                    <p>
                                        Example: <br>
                                        [qmwp_mood_tracker tracked_variable_name="Overall Mood"]
                                    </p>
                                </td>
                                <td>
                                    Allow users to rate their moods
                                    <a target="_blank" href="qmwp-mood-tracker">Take a look</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="shortcode">[qmwp_timeline]</td>
                                <td>
                                    <ul>
                                        <li>
                                            variables
                                        </li>
                                    </ul>
                                    <p>
                                        Specify variable(s) to be displayed. Separate variable names by a semicolon
                                    </p>

                                    <p>
                                        Example: <br>
                                        [qmwp_timeline examined_variable_names="Overall Mood;Sleep Quality"]
                                    </p>
                                </td>
                                <td>
                                    Graph any variable over time
                                    <a href="/qmwp-timeline" target="_blank">Take a look</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="shortcode">[qmwp_connectors]</td>
                                <td>

                                </td>
                                <td>
                                    Allow users to import their data from 3rd party sources
                                    <a href="/qmwp-connectors" target="_blank">Take a look</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="shortcode">[qmwp_bargraph_scatterplot_timeline]</td>
                                <td>
                                    <ul>
                                        <li>
                                            examined_variable_name
                                        </li>
                                        <li>
                                            show_predictors_or_outcomes
                                        </li>
                                    </ul>
                                    <p>
                                        Specify variable and how it should be considered (cause or effect)
                                    </p>

                                    <p>
                                        Example: <br>
                                        [qmwp_bargraph_scatterplot_timeline examined_variable_name="Sleep Quality"
                                        show_predictors_or_outcomes="predictors"]
                                    </p>
                                </td>
                                <td>
                                    Search for predictors or likely effects of a given variable
                                    <a target="_blank" href="/qmwp-bargraph-scatterplot-timeline">Take a look</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="shortcode">[qmwp_search_correlations]</td>
                                <td>
                                    <ul>
                                        <li>
                                            examined_variable_name
                                        </li>
                                        <li>
                                            show_predictors_or_outcomes
                                        </li>
                                    </ul>
                                    <p>
                                        The optional "examined_variable_name" parameter is used to pre-specify the
                                        variable name. If absent, the user may select it from a menu.
                                        The optional "show_predictors_or_outcomes" parameter whether the chart should
                                        display predictors or outcomes of the examined variable.
                                    </p>

                                    <p>
                                        Example: <br>
                                        This shortcode would display a graph exploring all possible predictors of the
                                        user's Attention ratings.
                                        [qmwp_search_correlations examined_variable_name="Attention"
                                        show_predictors_or_outcomes="predictors"]
                                    </p>
                                </td>
                                <td>
                                    Search for predictors or likely effects of a given variable
                                    <a href="/qmwp-search-correlations">Take a look</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="shortcode">[qm_numbers_rating]</td>
                                <td>
                                    <ul>
                                        <li>tracked_variable_name</li>
                                        <li>show_symptom_labels</li>
                                        <li>negative</li>
                                    </ul>
                                    <p>
                                        Set variable to track. Tune up tracker accordingly
                                    </p>

                                    <p>
                                        Example: <br>
                                        [qm_numbers_rating tracked_variable_name="Overall Mood"
                                        show_symptom_labels="true" negative="false"]
                                    </p>
                                </td>
                                <td>
                                    Track anything!
                                </td>
                            </tr>

                            <tr>
                                <td class="shortcode">[qmwp_add_measurement]</td>
                                <td>
                                    <ul>
                                        <li>
                                            category
                                        </li>
                                    </ul>
                                    <p>
                                        Narrow a list of variables by category
                                    </p>

                                    <p>
                                        Example: <br>
                                        [qmwp_add_measurement category="Environment"]
                                    </p>
                                </td>
                                <td>
                                    Track anything!
                                    <a href="/qmwp-add-measurement">Take a look</a>
                                </td>
                            </tr>

                            </tbody>

                        </table>

                        <p>
                            Usage examples can be found at automatically created pages. Check them out
                            <a href="/wp-admin/edit.php?post_type=page">here</a>
                        </p>

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

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Automatically logout inactive users: <a href="#"
                                                                                        class="tip-button">[?]</a></th>
                                <td>
                                    <select name='qmwp_logout_inactive_users'>
                                        <option
                                            value='0' <?php selected(get_option('qmwp_logout_inactive_users'), '0'); ?>>
                                            Never
                                        </option>
                                        <option
                                            value='1' <?php selected(get_option('qmwp_logout_inactive_users'), '1'); ?>>
                                            After 1
                                            minute
                                        </option>
                                        <option
                                            value='5' <?php selected(get_option('qmwp_logout_inactive_users'), '5'); ?>>
                                            After 5
                                            minutes
                                        </option>
                                        <option
                                            value='15' <?php selected(get_option('qmwp_logout_inactive_users'), '15'); ?>>
                                            After 15
                                            minutes
                                        </option>
                                        <option
                                            value='30' <?php selected(get_option('qmwp_logout_inactive_users'), '30'); ?>>
                                            After 30
                                            minutes
                                        </option>
                                        <option
                                            value='60' <?php selected(get_option('qmwp_logout_inactive_users'), '60'); ?>>
                                            After 1
                                            hour
                                        </option>
                                        <option
                                            value='120' <?php selected(get_option('qmwp_logout_inactive_users'), '120'); ?>>
                                            After 2
                                            hours
                                        </option>
                                        <option
                                            value='240' <?php selected(get_option('qmwp_logout_inactive_users'), '240'); ?>>
                                            After 4
                                            hours
                                        </option>
                                    </select>

                                    <p class="tip-message">Specifies whether to log out users automatically after a
                                        period of inactivity.</p>

                                    <p class="tip-message tip-warning"><strong>Warning:</strong> When a user logs out of
                                        WordPress, they will remain logged into their third-party provider until they
                                        close their browser. Logging out of WordPress DOES NOT log you out of
                                        QuantiModo...</p>
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

                <!-- START Login Page & Form Customization section -->
                <div id="qmwp-settings-section-login-forms" class="qmwp-settings-section">
                    <h3>Login Forms</h3>

                    <div class='form-padding'>
                        <table class='form-table'>

                            <tr valign='top'>
                                <th colspan="2">
                                    <h4>Default Login Form / Page / Popup</h4>
                                </th>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Hide the WordPress login form: <a href="#" class="tip-button">[?]</a>
                                </th>
                                <td>
                                    <input type='checkbox' name='qmwp_hide_wordpress_login_form'
                                           value='1' <?php checked(get_option('qmwp_hide_wordpress_login_form') == 1); ?> />

                                    <p class="tip-message">Use this to hide the WordPress username/password login form
                                        that is shown by default on the Login Screen and Login Popup.</p>

                                    <p class="tip-message tip-warning"><strong>Warning: </strong>Hiding the WordPress
                                        login form may prevent you from being able to login. If you normally rely on
                                        this method, DO NOT enable this setting. Furthermore, please make sure your
                                        login provider(s) are active and working BEFORE enabling this setting.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Logo links to site: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <input type='checkbox' name='qmwp_logo_links_to_site'
                                           value='1' <?php checked(get_option('qmwp_logo_links_to_site') == 1); ?> />

                                    <p class="tip-message">Forces the logo image on the login form to link to your site
                                        instead of WordPress.org.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Logo image: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <p>
                                        <input id='qmwp_logo_image' type='text' size='' name='qmwp_logo_image'
                                               value="<?php echo get_option('qmwp_logo_image'); ?>"/>
                                        <input id='qmwp_logo_image_button' type='button' class='button' value='Select'/>
                                    </p>

                                    <p class="tip-message">Changes the default WordPress logo on the login form to an
                                        image of your choice. You may select an image from the Media Library, or specify
                                        a custom URL.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Background image: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <p>
                                        <input id='qmwp_bg_image' type='text' size='' name='qmwp_bg_image'
                                               value="<?php echo get_option('qmwp_bg_image'); ?>"/>
                                        <input id='qmwp_bg_image_button' type='button' class='button' value='Select'/>
                                    </p>

                                    <p class="tip-message">Changes the background on the login form to an image of your
                                        choice. You may select an image from the Media Library, or specify a custom
                                        URL.</p>
                                </td>
                            </tr>

                            <tr valign='top'>
                                <th colspan="2">
                                    <h4>Custom Login Forms</h4>
                                </th>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Custom form to show on the login screen: <a href="#" class="tip-button">[?]</a>
                                </th>
                                <td>
                                    <?php echo QMWP::qmwp_login_form_designs_selector('qmwp-login-form-show-login-screen'); ?>
                                    <p class="tip-message">Create or manage these login form designs in the CUSTOM LOGIN
                                        FORM DESIGNS section.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Custom form to show on the user's profile page: <a href="#"
                                                                                                   class="tip-button">[?]</a>
                                </th>
                                <td>
                                    <?php echo QMWP::qmwp_login_form_designs_selector('qmwp-login-form-show-profile-page'); ?>
                                    <p class="tip-message">Create or manage these login form designs in the CUSTOM LOGIN
                                        FORM DESIGNS section.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Custom form to show in the comments section: <a href="#"
                                                                                                class="tip-button">[?]</a>
                                </th>
                                <td>
                                    <?php echo QMWP::qmwp_login_form_designs_selector('qmwp-login-form-show-comments-section'); ?>
                                    <p class="tip-message">Create or manage these login form designs in the CUSTOM LOGIN
                                        FORM DESIGNS section.</p>
                                </td>
                            </tr>
                        </table>
                        <!-- .form-table -->
                        <?php submit_button('Save all settings'); ?>
                    </div>
                    <!-- .form-padding -->
                </div>
                <!-- .qmwp-settings-section -->
                <!-- END Login Page & Form Customization section -->

                <!-- START Custom Login Form Designs section -->
                <div id="qmwp-settings-section-custom-login-form-designs" class="qmwp-settings-section">
                    <h3>Custom Login Form Designs</h3>

                    <div class='form-padding'>
                        <p>You may create multiple login form <strong><em>designs</em></strong> and use them throughout
                            your
                            site. A design is essentially a re-usable <em>shortcode preset</em>. Instead of writing out
                            the
                            login form shortcode ad-hoc each time you want to use it, you can build a design here, save
                            it, and
                            then specify that design in the shortcode's <em>design</em> attribute. For example:
                        <pre><code>[qmwp_login_form design='CustomDesign1']</code></pre>
                        </p>
                        <table class='form-table'>
                            <tr valign='top' class="has-tip">
                                <th scope='row'>Design: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <?php echo QMWP::qmwp_login_form_designs_selector('qmwp-login-form-design', true); ?>
                                    <p>
                                        <input type="button" id="qmwp-login-form-new" class="button" value="New">
                                        <input type="button" id="qmwp-login-form-edit" class="button" value="Edit">
                                        <input type="button" id="qmwp-login-form-delete" class="button" value="Delete">
                                    </p>

                                    <p class="tip-message">Here you may create a new design, select an existing design
                                        to edit, or delete an existing design.</p>

                                    <p class="tip-message tip-info"><strong>Tip: </strong>Make sure to click the <em>Save
                                            all settings</em> button after making changes here.</p>
                                </td>
                            </tr>
                        </table>
                        <!-- .form-table -->

                        <table class="form-table" id="qmwp-login-form-design-form">
                            <tr valign='top'>
                                <th colspan="2">
                                    <h4>Edit Design</h4>
                                </th>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Design name: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <input id='qmwp-login-form-design-name' type='text' size='36'
                                           name='qmwp_login_form_design_name'
                                           value=""/>

                                    <p class="tip-message">Sets the name to use for this design.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Icon set: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <select name='qmwp_login_form_icon_set'>
                                        <option value='none'>None</option>
                                        <option value='hex'>Hex</option>
                                    </select>

                                    <p class="tip-message">Specifies which icon set to use for displaying provider icons
                                        on the login buttons.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Show login buttons: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <select name='qmwp_login_form_show_login'>
                                        <option value='always'>Always</option>
                                        <option value='conditional'>Conditional</option>
                                        <option value='never'>Never</option>
                                    </select>

                                    <p class="tip-message">Determines when the login buttons should be shown.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Show logout button: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <select name='qmwp_login_form_show_logout'>
                                        <option value='always'>Always</option>
                                        <option value='conditional'>Conditional</option>
                                        <option value='never'>Never</option>
                                    </select>

                                    <p class="tip-message">Determines when the logout button should be shown.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Layout: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <select name='qmwp_login_form_layout'>
                                        <option value='links-row'>Links Row</option>
                                        <option value='links-column'>Links Column</option>
                                        <option value='buttons-row'>Buttons Row</option>
                                        <option value='buttons-column'>Buttons Column</option>
                                    </select>

                                    <p class="tip-message">Sets vertical or horizontal layout for the buttons.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Login button prefix: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <input id='qmwp_login_form_button_prefix' type='text' size='36'
                                           name='qmwp_login_form_button_prefix'
                                           value=""/>

                                    <p class="tip-message">Sets the text prefix to be displayed on the social login
                                        buttons.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Logged out title: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <input id='qmwp_login_form_logged_out_title' type='text' size='36'
                                           name='qmwp_login_form_logged_out_title' value=""/>

                                    <p class="tip-message">Sets the text to be displayed above the login form for logged
                                        out users.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Logged in title: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <input id='qmwp_login_form_logged_in_title' type='text' size='36'
                                           name='qmwp_login_form_logged_in_title' value=""/>

                                    <p class="tip-message">Sets the text to be displayed above the login form for logged
                                        in users.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Logging in title: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <input id='qmwp_login_form_logging_in_title' type='text' size='36'
                                           name='qmwp_login_form_logging_in_title' value=""/>

                                    <p class="tip-message">Sets the text to be displayed above the login form for users
                                        who are logging in.</p>
                                </td>
                            </tr>

                            <tr valign='top' class="has-tip">
                                <th scope='row'>Logging out title: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <input id='qmwp_login_form_logging_out_title' type='text' size='36'
                                           name='qmwp_login_form_logging_out_title' value=""/>

                                    <p class="tip-message">Sets the text to be displayed above the login form for users
                                        who are logging out.</p>
                                </td>
                            </tr>

                            <tr valign='top' id='qmwp-login-form-actions'>
                                <th scope='row'>
                                    <input type="button" id="qmwp-login-form-ok" name="qmwp_login_form_ok"
                                           class="button" value="OK">
                                    <input type="button" id="qmwp-login-form-cancel" name="qmwp_login_form_cancel"
                                           class="button"
                                           value="Cancel">
                                </th>
                                <td>

                                </td>
                            </tr>
                        </table>
                        <!-- .form-table -->
                        <?php submit_button('Save all settings'); ?>
                    </div>
                    <!-- .form-padding -->
                </div>
                <!-- .qmwp-settings-section -->
                <!-- END Login Buttons section -->

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
                <div id="qmwp-settings-section-back-channel=configuration" class="qmwp-settings-section">
                    <h3>Back Channel Configuration</h3>

                    <div class='form-padding'>
                        <p>These settings are for troubleshooting and/or fine tuning the back channel communication this
                            plugin utilizes between your server and the third-party providers.</p>
                        <table class='form-table'>
                            <tr valign='top' class="has-tip">
                                <th scope='row'>HTTP utility: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <select name='qmwp_http_util'>
                                        <option value='curl' <?php selected(get_option('qmwp_http_util'), 'curl'); ?>>
                                            cURL
                                        </option>
                                        <option
                                            value='stream-context' <?php selected(get_option('qmwp_http_util'), 'stream-context'); ?>>
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
                                           value='1' <?php checked(get_option('qmwp_http_util_verify_ssl') == 1); ?> />

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
                        <?php submit_button('Save all settings'); ?>
                    </div>
                    <!-- .form-padding -->
                </div>
                <!-- .qmwp-settings-section -->
                <!-- END Back Channel Configuration section -->

                <!-- START Maintenance & Troubleshooting section -->
                <div id="qmwp-settings-section-maintenance-troubleshooting" class="qmwp-settings-section">
                    <h3>Maintenance & Troubleshooting</h3>

                    <div class='form-padding'>
                        <table class='form-table'>
                            <tr valign='top' class="has-tip">
                                <th scope='row'>Restore default settings: <a href="#" class="tip-button">[?]</a></th>
                                <td>
                                    <input type='checkbox' name='qmwp_restore_default_settings'
                                           value='1' <?php checked(get_option('qmwp_restore_default_settings') == 1); ?> />

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
                                           value='1' <?php checked(get_option('qmwp_delete_settings_on_uninstall') == 1); ?> />

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
                        <?php submit_button('Save all settings'); ?>
                    </div>
                    <!-- .form-padding -->
                </div>
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
