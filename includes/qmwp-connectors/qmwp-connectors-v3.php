<?php


wp_enqueue_style("manageaccounts", plugins_url('../../', __FILE__) . "css/qmwp-connectors.css");
wp_enqueue_style("jquery-ui-flick", plugins_url('../../', __FILE__) . "css/jquery-ui-flick.css");
wp_enqueue_style("jquery-dropdown", plugins_url('../../', __FILE__) . "css/jquery.dropdown.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-dialog");
wp_enqueue_script("jquery-dropdown", plugins_url('../../', __FILE__) . "js/libs/jquery.dropdown.min.js", "jquery");
wp_enqueue_script("mustache", plugins_url('../../', __FILE__) . "js/libs/mustache.js");

?>

<?php if (!is_user_logged_in()): ?>
    <div
        class="dialog-background" id="login-dialog-background"></div>
    <div class="dialog" id="login-dialog">
        <?php login_with_ajax(); ?>
    </div>
<?php endif; ?>

<div id="content" style="margin: 0; padding: 0;">
    <div class="my-location" style="width: 800px; padding: 15px; background-color: #fff; margin: 0 auto;"></div>
    <script>
        if (access_token) {

            var loadHandler = function () {
                console.debug('Connect JS loaded');
                if (!executed) {
                    console.debug('Calling "qmSetupOnPage" function from connect.js');
                    qmSetupOnPage('.my-location');
                    executed = true;
                }
            };

            var content = document.getElementById('content');
            var connectJs = document.createElement('script');
            connectJs.type = 'text/javascript';
            connectJs.src = api_host + '/api/v1/connect.js?access_token=' + access_token;

            connectJs.onreadystatechange = loadHandler;
            connectJs.onload = loadHandler;

            var executed = false;

            content.appendChild(connectJs);

        } else {
            window.location.href = "?connect=quantimodo";
        }

    </script>
</div>

<div style="display: none;">
    <table class="updates-table">
        <thead>
        <th>Status</th>
        <th>Time</th>
        <th>Measurements</th>
        </thead>
    </table>
</div>


