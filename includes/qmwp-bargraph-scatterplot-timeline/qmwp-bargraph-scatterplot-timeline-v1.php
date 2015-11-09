<?php
/**
 *    Template Name: bargraph-gauge-scatterplot-timeline
 *    Description: List of correlations and relationship/longitudinal visualization
 */

wp_enqueue_style("correlate", plugins_url('../../', __FILE__) . "css/qmwp-bargraph-scatterplot-timeline.css");
wp_enqueue_style("shared-styles", plugins_url('../../', __FILE__) . "css/_shared_styles.css");
wp_enqueue_style("jquery-ui-flick", plugins_url('../../', __FILE__) . "css/jquery-ui-flick.css");
wp_enqueue_style("jquery-dropdown", plugins_url('../../', __FILE__) . "css/jquery.dropdown.css");
wp_enqueue_style("jquery-tip", plugins_url('../../', __FILE__) . "css/simpletip.css");
wp_enqueue_style("jquery-datetimepicker", plugins_url('../../', __FILE__) . "css/jquery.datetimepicker.css");
wp_enqueue_style("jquery-fancybox", plugins_url('../../', __FILE__) . "js/libs/fancybox/jquery.fancybox.css");
wp_enqueue_style("font-awesome", "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-dialog");
wp_enqueue_script("jquery-ui-datepicker");
wp_enqueue_script("jquery-ui-button");
wp_enqueue_script("jquery-ui-sortable");
wp_enqueue_script("jquery-ui-autocomplete");
wp_enqueue_script("jquery-ui-tooltip");
wp_enqueue_script("jquery-dropdown", plugins_url('../../', __FILE__) . "js/libs/jquery.dropdown.min.js", "jquery");
wp_enqueue_script("jquery-datetimepicker", plugins_url('../../', __FILE__) . "js/libs/jquery.datetimepicker.js", "jquery");
wp_enqueue_script("jquery-touch", plugins_url('../../', __FILE__) . "js/libs/jquery.ui.touch-punch.min.js", "jquery");
wp_enqueue_script("jquery-fancybox", plugins_url('../../', __FILE__) . "js/libs/fancybox/jquery.fancybox.pack.js", "jquery");

wp_enqueue_script("qm-math", plugins_url('../../', __FILE__) . "js/math.js", "jquery", false, true);
wp_enqueue_script("timezone", plugins_url('../../', __FILE__) . "js/jstz.min.js", "jquery", false, true);
wp_enqueue_script("moment", plugins_url('../../', __FILE__) . "js/libs/moment.min.js", "jquery", false, true);
wp_enqueue_script("qm-sdk", plugins_url('../../', __FILE__) . "js/libs/quantimodo-api.js", "jquery", false, true);
wp_enqueue_script("jquery-simpletip", plugins_url('../../', __FILE__) . "js/libs/jquery.simpletip-1.3.1.js", "jquery", false, true);


wp_enqueue_script("highcharts", plugins_url('../../', __FILE__) . "js/libs/highstock.js", "jquery", false, true);
wp_enqueue_script("highcharts-more", plugins_url('../../', __FILE__) . "js/libs/highcharts-more.js", "highcharts", false, true);
wp_enqueue_script("highcharts-fix", plugins_url('../../', __FILE__) . "js/highcharts-fix.js", "highcharts", false, true);

wp_enqueue_script("highcharts-exporting", plugins_url('../../', __FILE__) . "js/libs/exporting.js", "highcharts", false, true);
wp_enqueue_script("canvas-tools", plugins_url('../../', __FILE__) . "js/libs/canvas-tools.js", "highcharts", false, true);
wp_enqueue_script("highcharts-export-csv", plugins_url('../../', __FILE__) . "js/libs/export-csv.js", "highcharts", false, true);
wp_enqueue_script("js-pdf", plugins_url('../../', __FILE__) . "js/libs/jspdf/jspdf.debug.js", "jquery", false, true);
wp_enqueue_script("highcharts-export", plugins_url('../../', __FILE__) . "js/libs/highcharts-export-clientside.js", "highcharts", false, true);


wp_enqueue_script("correlate-charts", plugins_url('../../', __FILE__) . "js/qmwp-bargraph-gauge-scatterplot-timeline_charts.js", array("highcharts-more", "qm-sdk", "qm-math"), false, true);
wp_enqueue_script("other-shared", plugins_url('../../', __FILE__) . "js/_other_shared.js", array("jquery"), false, true);
wp_enqueue_script("variable-settings", plugins_url('../../', __FILE__) . "js/_variable_settings.js", array("jquery"), false, true);
wp_enqueue_script("refresh-shared", plugins_url('../../', __FILE__) . "js/_data_refresh.js", array("jquery"), false, true);

wp_enqueue_script("correlate", plugins_url('../../', __FILE__) . "js/qmwp-bargraph-gauge-scatterplot-timeline.js", array("correlate-charts", "jquery-ui-datepicker", "jquery-ui-button"), false, true);

wp_enqueue_script("quantimodo-intercom", plugins_url('../../', __FILE__) . "js/intercom.js", array('jquery', 'qm-sdk'));

?>

<?php require plugin_dir_path(__FILE__) . "../modules/dialog_add_measurement.php"; ?>
<?php require plugin_dir_path(__FILE__) . "../modules/dialog_delete_measurements.php"; ?>
<?php require plugin_dir_path(__FILE__) . "../modules/dialog_share.php"; ?>
<?php require plugin_dir_path(__FILE__) . "../modules/variable_settings.php"; ?>


<div id="content">
    <section id="section-configure">
        <div class="outstate">
            <div class="accordion-header" id="accordion-output-header">
                <div class="generalHeader resolutionHeader">
                    Examined Variable
                </div>
                <div class="icon-question-sign icon-large questionMark questionMarkAlone"
                     title="This is the variable to be examined. It can be considered to be a hypothetical cause for the variables in the bar graph or hypothetical effect of the variables in the bar graph by changing the setting below."></div>
            </div>
            <div class="accordion-content closed" id="accordion-output-content">
                <div class="inner">
                    <select id="selectOutputCategory"></select>
                    <!--<select id="selectOutputVariable"></select>-->
                    <input type="text" id="selectOutputVariable">
                    <select id="selectOutputAsType">
                        <option value="effect">As Effect</option>
                        <option value="cause">As Cause</option>
                    </select>
                    <button id="button-output-varsettings">Settings</button>
                </div>
            </div>
        </div>

        <div id="bar-graph">
            <header class="graph-header" id="bar-graph-header">
                <div class="generalHeader bargraphHeader">
                    Please wait...
                </div>
                <div class="icon-question-sign icon-large questionMark"
                     title="This is the list of variables in order of their correlation with your examined variable."></div>
            </header>
            <div class="graph-content" style="height: 596px; overflow-y: scroll;">
                <img src="https://i.imgur.com/73BFcje.gif" class="barloading"
                     style="margin-left: 4%; margin-top: 20%; display:none"/>
                <span class="no-data" style="display:none"> <br/>  <center><h2>Hi!</h2>
                        <h2>We don't have enough data to determine your top predictors and outcomes. &nbsp;:(</h2>
                        <h2>Please check out the <a href="/getting-started" target="_blank">Getting Started</a> page to see how to add more data!</h2></center><br/><br/></span>

                <div id="graph-bar" class="graph-content">
                </div>
                <input type="hidden" id="selectBargraphInputVariable" value=""/>
                <input type="hidden" id="selectBargraphInputCategory" value=""/>

            </div>
        </div>
    </section>

    <section id="section-analyze">

        <!-- <div class="inoutstate">
          <div class="daterange">
                            <div class="accordion-header" id="accordion-date-header">
                                    <div class="generalHeader resolutionHeader">
                                            Resolution
                                    </div>
                                    <div class="icon-question-sign icon-large questionMark questionMarkAlone"></div>
                            </div>
                            <div class="accordion-content closed" id="accordion-date-content">
                                    <div class="inner">						
                                            <div id="accordion-content-rangepickers">
                                                    <input type="radio" value="Hour" id="radio3" name="radio" /><label for="radio3">Hour</label>
                                                    <input type="radio" value="Day" id="radio4" name="radio" checked='checked' /><label for="radio4">Day</label>
                                                    <input type="radio" value="Week" id="radio5" name="radio" /><label for="radio5">Week</label>
                                                    <input type="radio" value="Month" id="radio6" name="radio" /><label for="radio6">Month</label>
                                            </div>
                                    </div>
                            </div
            </div>
                 
             
          </div>-->
        <div class="open" id="correlation-gauge" style="float:left">
            <div class="inner">
                <header class="graph-header" id="correlation-gauge-header">
                    <div class="generalHeader correlationHeader">
                        Scatterplot
                    </div>
                    <!--  <div id="gauge-correlation-settingsicon" data-dropdown="#dropdown-gauge-settings" class="gear-icon"></div>
                      <div class="icon-question-sign icon-large questionMark"></div> -->
                </header>
                <div class="graph-content" style="width: 100%; overflow: hidden;">
                    <div
                        style="float: right; width: 155px; padding-left: 12px; position:relative; height:100%; border-left: solid thin #F5FBFB;"
                        id="gauge-correlation"></div>
                    <div style="overflow: hidden; height: 100%;">
                        <table style="height: 100%;">
                            <tr>
                                <td>
                                    <strong>Statistical Relationship</strong>
                                </td>
                                <td id="statisticalRelationshipValue">
                                    Significant
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Effect Size</strong>
                                </td>
                                <td id="effectSizeValue">
                                    Large
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="open" id="scatterplot-graph">
            <div class="inner">
                <header class="graph-header" id="scatterplot-graph-header">
                    <!-- <div class="generalHeader scatterplotHeader">
                         Scatterplot
                     </div>-->
                    <div class="keepInline">
                        <div id="graph-scatterplot-settingsicon" data-dropdown="#dropdown-scatterplot-settings"
                             class="gear-icon"></div>
                        <div class="icon-plus-sign icon-2x plusMark" title="Click to add a measurement."></div>
                        <div class="icon-question-sign icon-large questionMark questionMarkPlus"
                             title="Displays the collection of measurement points, each having the value of examined variable on the horizontal axis and the value of the other variable on the vertical axis."></div>
                    </div>
                </header>

                <div class="graph-content" id="graph-scatterplot"></div>

            </div>
        </div>
        <div id="timeline-graph">
            <header class="graph-header" id="timeline-graph-header">
                <div class="generalHeader timelineHeader">
                    Timeline
                </div>
                <!--<div id="gauge-timeline-settingsicon" data-dropdown="#dropdown-timeline-settings"
                     class="gear-icon"></div>-->
                <div class="icon-plus-sign icon-2x plusMark" title="Click to add a measurement."></div>
                <div class="icon-question-sign icon-large questionMark questionMarkPlus"
                     title="Shows the measurement data in the order of measurement dates."></div>
            </header>
            <div class="graph-content" id="graph-timeline"></div>
        </div>

    </section>
</div>

<!-- Menu for correlation gauge settings -->
<div id="dropdown-gauge-settings" class="dropdown dropdown-tip">
    <ul class="dropdown-menu">
        <li><a id="shareCorrelationGauge">Share graph</a></li>
    </ul>
</div>
<!-- Menu for timeline settings -->
<div id="dropdown-timeline-settings" class="dropdown dropdown-tip dropdown-anchor-right">
    <ul class="dropdown-menu">
        <li><label><input name="tl-enable-markers" type="checkbox"/> Show markers</label></li>
        <li><label><input name="tl-smooth-graph" type="checkbox"/> Smoothen graph</label></li>
        <li><label><input name="tl-enable-horizontal-guides" type="checkbox"/> Show horizontal guides</label></li>
        <li class="dropdown-divider"></li>
        <li><a id="shareTimeline">Share graph</a></li>
    </ul>
</div>

<!-- Menu for timeline settings -->
<div id="dropdown-scatterplot-settings" class="dropdown dropdown-tip dropdown-anchor-right">
    <ul class="dropdown-menu">
        <li><label><input name="sp-show-linear-regression" type="checkbox"/> Show linear regression</label></li>
        <li class="dropdown-divider"></li>
        <li><a id="shareScatterplot">Share graph</a></li>
    </ul>
</div>

<!-- Menu for barchart settings -->
<div id="dropdown-barchart-settings" class="dropdown dropdown-tip dropdown-anchor-right">
    <ul class="dropdown-menu">
        <li><a id="" onclick="sortByCorrelation()">Sort By Correlation</a></li>
        <li><a id="shareScatterplot" onclick="sortByCausality()">Sort By Causality Factor</a></li>
        <li style="padding:3px 15px;"><input type="text" id="minimumNumberOfSamples"
                                             placeholder="Min. Number of Samples"></li>
    </ul>
</div>


<div id="please-wait">
    <div id="please-wait-overlay">&nbsp;</div>
    <div class="please-wait-content">
        <img src="<?php echo plugins_url('../../', __FILE__) . 'css/images/ajax-loader.gif' ?>" alt="">
        <span>please wait...</span>
    </div>
</div>

