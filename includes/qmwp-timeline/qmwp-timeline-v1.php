<?php
/**
 *    Template Name: Analyze Page (v2, no correlations)
 *    Description: Page based on original PHP website
 */
wp_enqueue_style("timeline", plugins_url('../../', __FILE__) . "css/qmwp-timeline.css");
wp_enqueue_style("shared-styles", plugins_url('../../', __FILE__) . "/css/_shared_styles.css");
wp_enqueue_style("jquery-ui-flick", plugins_url('../../', __FILE__) . "/css/jquery-ui-flick.css");
wp_enqueue_style("jquery-dropdown", plugins_url('../../', __FILE__) . "/css/jquery.dropdown.css");
wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
wp_enqueue_style("jquery-fancybox", plugins_url('../../', __FILE__) . "js/libs/fancybox/jquery.fancybox.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-dialog");
wp_enqueue_script("jquery-ui-datepicker");
wp_enqueue_script("jquery-ui-button");
wp_enqueue_script("jquery-ui-sortable");
wp_enqueue_script("jquery-ui-menu");
wp_enqueue_script("jquery-ui-autocomplete");
wp_enqueue_script("jquery-dropdown", plugins_url('../../', __FILE__) . "js/libs/jquery.dropdown.min.js", "jquery");
wp_enqueue_script("jquery-touch", plugins_url('../../', __FILE__) . "js/libs/jquery.ui.touch-punch.min.js", "jquery");
wp_enqueue_script("qm-math", plugins_url('../../', __FILE__) . "js/math.js", "jquery", false, true);
wp_enqueue_script("timezone", plugins_url('../../', __FILE__) . "js/jstz.min.js", "jquery", false, true);
wp_enqueue_script("moment", plugins_url('../../', __FILE__) . "js/libs/moment.min.js", "jquery", false, true);

wp_enqueue_script("qm-sdk", plugins_url('../../', __FILE__) . "js/libs/quantimodo-sdk-javascript/quantimodo-api.js", "jquery", false, true);

wp_enqueue_script("highcharts", plugins_url('../../', __FILE__) . "js/libs/highstock.js", "jquery", false, true);
wp_enqueue_script("highcharts-more", plugins_url('../../', __FILE__) . "js/libs/highcharts-more.js", "highcharts", false, true);
wp_enqueue_script("highcharts-fix", plugins_url('../../', __FILE__) . "js/highcharts-fix.js", "highcharts", false, true);

wp_enqueue_script("highcharts-exporting", plugins_url('../../', __FILE__) . "js/libs/exporting.js", "highcharts", false, true);
wp_enqueue_script("canvas-tools", plugins_url('../../', __FILE__) . "js/libs/canvas-tools.js", "highcharts", false, true);
wp_enqueue_script("highcharts-export-csv", plugins_url('../../', __FILE__) . "js/libs/export-csv.js", "highcharts", false, true);
wp_enqueue_script("js-pdf", plugins_url('../../', __FILE__) . "js/libs/jspdf/jspdf.debug.js", "jquery", false, true);
wp_enqueue_script("highcharts-export", plugins_url('../../', __FILE__) . "js/libs/highcharts-export-clientside.js", "highcharts", false, true);



wp_enqueue_script("timeline-charts", plugins_url('../../', __FILE__) . "js/timeline_charts.js", array("highcharts-more", "qm-sdk", "qm-math"), false, true);
wp_enqueue_script("other-shared", plugins_url('../../', __FILE__) . "js/_other_shared.js", array("jquery"), false, true);
wp_enqueue_script("variable-settings", plugins_url('../../', __FILE__) . "js/_variable_settings.js", array("jquery"), false, true);
wp_enqueue_script("refresh-shared", plugins_url('../../', __FILE__) . "js/_data_refresh.js", array("jquery"), false, true);
wp_enqueue_script("jquery-fancybox", plugins_url('../../', __FILE__) . "js/libs/fancybox/jquery.fancybox.pack.js", "jquery");

wp_enqueue_script("timeline", plugins_url('../../', __FILE__) . "js/qmwp-timeline.js",
    array(
        "timeline-charts",
        "jquery-ui-datepicker",
        "jquery-ui-button",
        "jquery-ui-autocomplete"
    ), false, true);

wp_enqueue_script("quantimodo-intercom", plugins_url('../../', __FILE__) . "js/intercom.js", array('jquery', 'qm-sdk'));

?>

<?php require plugin_dir_path(__FILE__) . "../modules/dialog_delete_measurements.php"; ?>
<?php require plugin_dir_path(__FILE__) . "../modules/dialog_share.php"; ?>
<?php require plugin_dir_path(__FILE__) . "../modules/variable_settings.php"; ?>

<div id="content">
    <section id="section-configure">
        <div id="section-configure-input" class="open">
            <div class="inner">

                <!--<div class="card-header accordion-header" id="accordion-date-header">
                    <div style="float: left; line-height: 42px;">
                        Date range
                    </div>
                </div>
                <div class="accordion-content closed" id="accordion-date-content">
                    <div class="inner">
                        <div id="accordion-content-rangepickers">
                            <input type="radio" value="Hour" id="radio3" name="radio"/><label for="radio3">Hour</label>
                            <input type="radio" value="Day" id="radio4" name="radio" checked='checked'/><label
                                for="radio4">Day</label>
                            <input type="radio" value="Week" id="radio5" name="radio"/><label for="radio5">Week</label>
                            <input type="radio" value="Month" id="radio6" name="radio"/><label
                                for="radio6">Month</label>
                        </div>
                    </div>
                </div>-->

                <div class="card-header accordion-header" id="accordion-input-header">
                    <div style="float: left; line-height: 42px;">
                        Variables
                    </div>
                </div>
                <div class="accordion-content closed" id="accordion-input-content" style="overflow: visible;">
                    <div class="inner">
                        <!--<ul id="addVariableMenu">
                            <li>
                                <a>Add a Variable</a>
                                <ul id="addVariableMenuCategories" style="z-index: 999999">
                                </ul>
                            </li>
                        </ul>-->
                        <input id="variable-selector" type="text" placeholder="Start typing variable name..."/>
                        <ul id="selectedVariables"></ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="section-analyze">
        <div style="width: 1px; overflow: hidden;"></div>
        <!-- Dirty hack for <768px -->

        <div id="timeline-graph">
            <header class="card-header graph-header">
                <div style="float: left; line-height: 42px;">
                    Timeline
                </div>
            </header>
            <div class="graph-content" id="graph-timeline">
            </div>
        </div>
    </section>
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