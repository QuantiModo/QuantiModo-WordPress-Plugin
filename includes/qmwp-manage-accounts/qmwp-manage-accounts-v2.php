<?php

wp_enqueue_style("manageaccounts", plugins_url('../../', __FILE__) . "css/qmwp-manage-accounts.css");
wp_enqueue_style("jquery-ui-flick", plugins_url('../../', __FILE__) . "css/jquery-ui-flick.css");
wp_enqueue_style("jquery-dropdown", plugins_url('../../', __FILE__) . "css/jquery.dropdown.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-dialog");

wp_enqueue_script("jquery-dropdown", plugins_url('../../', __FILE__) . "js/libs/jquery.dropdown.min.js", "jquery");
wp_enqueue_script("mustache", plugins_url('../../', __FILE__) . "js/libs/mustache.js");
wp_enqueue_script("moment", plugins_url('../../', __FILE__) . "js/libs/moment.min.js", "jquery", false, true);
wp_enqueue_script("qm-sdk", plugins_url('../../', __FILE__) . "js/libs/quantimodo-api.js", "jquery", false, true);

wp_enqueue_script("manageaccounts", plugins_url('../../', __FILE__) . "js/qmwp-manage-accounts.js", "qm-sdk", false, true);

?>

<div id="content">
    <div class="modal-body">
        <div>
            <div id="connectorInfoTable">
                <script type="text/html" id="connectorsTemplate">
                    {{#connectors}}
                    <div class="connectorBlock" id="connector-{{name}}">
                        <div class="connectors" style="text-align: center; vertical-align: middle;"
                             id="connectorName-{{name}}">
                            <img class="connectorLogo {{^connected}}grayout{{/connected}}" src="{{image}}"
                                 alt="{{name}}"
                                 {{^connected}}style="filter: grayscale(100%);-webkit-filter: grayscale(100%);filter: gray;-webkit-transition: all .6s ease;"
                                 {{/connected}}>
                            <h6>{{displayName}}</h6>
                        </div>
                    </div>

                    <div class="connectorDialog" id="showDialog-{{name}}">
                        <div class="connectNotificationContainer" id="connectNotificationContainer-{{name}}"
                             style="height: 0px;"></div>
                        <div class="clearfix">
                            <div style="float: left;width: 140px;">
                                <div style="height: 150px;">
                                    <img class="connectorLogo" src="{{image}}" alt="{{name}}"
                                         style="width: 140px; height: 140px;">
                                    {{^connected}}
                                    <img class="connectorStatus" src="https://i.imgur.com/tvNH2wA.png" height="30px"
                                         width="30px" style="position: relative; top: -40px; left: 110px;">
                                    {{/connected}}
                                    {{#connected}}
                                    <img class="connectorStatus" src="https://i.imgur.com/Rvv8Ujo.png" height="30px"
                                         width="30px" style="position: relative; top: -40px; left: 110px;">
                                    {{/connected}}
                                </div>
                                {{#connected}}
                                <div class="buttons">
                                    <button class="disconnect-button" id="update-{{name}}"
                                            style="display: block; width: 120px; margin: auto; margin-bottom: 5px;">Sync
                                    </button>
                                    <button class="disconnect-button" id="disconnect-{{name}}"
                                            style="display: block; width: 120px; margin: auto;">Disconnect
                                    </button>
                                </div>
                                {{/connected}}
                            </div>
                            <div class="connectorDialog-desc">{{text}}
                                {{#connectInstructions}}
                                <form id="connectform-{{name}}" style="{{#connected}}display:none{{/connected}}">
                                    {{#parameters}}
                                    <label for="{{name}}-{{key}}">{{displayName}}</label>
                                    <input type="{{type}}" name="{{key}}" id="{{name}}-{{key}}"
                                           placeholder="{{placeholder}}">{{defaultValue}}</input>
                                    {{/parameters}}
                                    <input type="submit" value="Connect" style="margin-top: 10px; float:right;">
                                </form>
                                {{/connectInstructions}}


                                {{^noDataYet}}
                                <table class="connectorInfoTable" style="{{^connected}}display:none{{/connected}}">
                                    <tr class="tabletr">
                                        <td class="bold">Last Sync</td>
                                        <td id="connectorDialog-lastUpdate-{{name}}">{{^syncing}}{{lastUpdate}}
                                            {{/syncing}}{{#syncing}}Now synchronizing{{/syncing}}
                                        </td>
                                    </tr>
                                    <tr class="tabletr">
                                        <td class="bold">Latest Data</td>
                                        <td id="connectorDialog-latestData-{{name}}">{{latestData}}</td>
                                    </tr>
                                </table>
                                {{/noDataYet}}

                                {{#noDataYet}}
                                <b>Retrieving Data Check back soon!</b>
                                {{/noDataYet}}

                                {{#connected}}
                                {{^noDataYet}}
                                <a class="view-updates-button" id="viewUpdates-{{name}}" href="#"><i
                                        class="icon-table"></i> View Updates</a>
                                {{/noDataYet}}
                                {{/connected}}
                            </div>
                        </div>
                        {{#showGetItButton}}
                        <div class="getitnow-container">
                            <a href="{{getItUrl}}" target="_blank">GET IT HERE</a>
                        </div>
                        {{/showGetItButton}}
                    </div>
                    {{/connectors}}
                </script>
            </div>
        </div>
    </div>
    <div class="modal-footer">
    </div>
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

