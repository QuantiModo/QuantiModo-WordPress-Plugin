<?php


wp_enqueue_style("manageaccounts", plugins_url('../../', __FILE__) . "css/qmwp-connectors.css");
wp_enqueue_style("jquery-ui-flick", plugins_url('../../', __FILE__) . "css/jquery-ui-flick.css");
wp_enqueue_style("jquery-dropdown", plugins_url('../../', __FILE__) . "css/jquery.dropdown.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-dialog");
wp_enqueue_script("jquery-dropdown", plugins_url('../../', __FILE__) . "js/libs/jquery.dropdown.min.js", "jquery");
wp_enqueue_script("mustache", plugins_url('../../', __FILE__) . "js/libs/mustache.js");

wp_enqueue_script("qm-sdk", plugins_url('../../', __FILE__) . "js/libs/quantimodo-api.js", "jquery", false, true);
wp_enqueue_script("quantimodo-intercom", plugins_url('../../', __FILE__) . "js/intercom.js", array('jquery', 'qm-sdk'));

?>

<div id="content" style="margin: 0; padding: 0;">
    <div class="my-location"></div>
    <script>

        var loadHandler = function () {
            console.debug('Connect JS loaded');
            if (!executed && typeof qmSetupOnPage === 'function') {
                console.debug('Calling "qmSetupOnPage" function from connect.js');
                qmSetupOnPage('.my-location');
                executed = true;
            }
        };

        var content = document.getElementById('content');
        var connectJs = document.createElement('script');
        connectJs.type = 'text/javascript';
        if(accessToken) {
            connectJs.src = apiHost + '/api/v1/connect.js?access_token=' + accessToken;
        } else {
            connectJs.src = apiHost + '/api/v1/connect.js';
        }

        connectJs.onreadystatechange = loadHandler;
        connectJs.onload = loadHandler;

        var executed = false;

        content.appendChild(connectJs);

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


