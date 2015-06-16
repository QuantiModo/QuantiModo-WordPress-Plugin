<?php
/**
 * 	Template Name: Bargraph page
 * 	Description: Page based on original PHP website
 */
function quantimodo_personal_studies_scripts_css()
{
    // Register Scripts and css 
    wp_register_script( 'jquery-dropdown', plugins_url( '/js/libs/jquery.dropdown.min.js', __FILE__ ), array( 'jquery' ) );
	wp_register_script( 'jquery-datetimepicker', plugins_url( '/js/libs/jquery.datetimepicker.js', __FILE__ ), array( 'jquery' ) );
	wp_register_script( 'jquery-touch', plugins_url( '/js/libs/jquery.ui.touch-punch.min.js', __FILE__ ), array( 'jquery' ) );
	wp_register_script( 'jquery-fancybox', plugins_url( '/js/libs/jquery.fancybox.pack.js', __FILE__ ), array( 'jquery' ) );
	wp_register_script( 'qm-math', plugins_url( '/js/math.js', __FILE__ ), array( 'jquery' ) );
	wp_register_script( 'timezone', plugins_url( '/js/jstz.min.js', __FILE__ ), array( 'jquery' ));
    wp_register_script( 'qm-sdk', plugins_url( '/js/libs/quantimodo-api.js', __FILE__ ), array( 'jquery' ) );
    wp_register_script( 'jquery-simpletip', plugins_url( '/js/libs/jquery.simpletip-1.3.1.js', __FILE__ ), array( 'jquery' ) );		
	wp_register_script( 'highcharts', plugins_url( '/js/libs/highstock.js', __FILE__  ), array( 'jquery' ) );		
    wp_register_script( 'highcharts-more', plugins_url( '/js/libs/highcharts-more.js', __FILE__  ), array( 'highcharts' ) );
    wp_register_script( 'correlate-charts', plugins_url( '/js/bargraph_charts.js', __FILE__ ) );	
    wp_register_script( 'other-shared', plugins_url( '/js/_other_shared.js', __FILE__ ),array( 'jquery' ) );	
	wp_register_script( 'variable-settings', plugins_url( '/js/_variable_settings.js', __FILE__ ),array( 'jquery' ) );	
	wp_register_script( 'refresh-shared', plugins_url( '/js/_data_refresh.js', __FILE__ ),array( 'jquery' ) );	
	wp_register_script( 'variable-picker', plugins_url( '/js/_variable_picker.js', __FILE__ ),array( 'jquery' ) );	
	wp_register_script( 'correlate', plugins_url( '/js/bargraph.js', __FILE__ ), array("correlate-charts", "jquery-ui-datepicker", "jquery-ui-button") );	
	
	wp_register_style( 'correlate', plugins_url( '/css/bargraph.css', __FILE__  ));
	wp_register_style( 'shared-styles', plugins_url( '/css/_shared_styles.css', __FILE__  ));
	wp_register_style( 'jquery-ui-flick', plugins_url( '/css/jquery-ui-flick.css', __FILE__  ));
	wp_register_style( 'jquery-dropdown', plugins_url( '/css/jquery.dropdown.css', __FILE__  ));
	wp_register_style( 'jquery-fancybox', plugins_url( '/css/jquery.fancybox.css', __FILE__  ));
	wp_register_style( 'jquery-tip', plugins_url( '/css/simpletip.css', __FILE__  ));
	wp_register_style( 'seperator', plugins_url( '/css/seperator.css', __FILE__  ));
	wp_register_style( 'fullwidth', plugins_url( '/css/fullwidth.css', __FILE__  ));
	
	
    //Execute Scripts and css
    wp_enqueue_style("fullwidth");
    wp_enqueue_style("seperator");
    wp_enqueue_style("correlate");
    wp_enqueue_style("shared-styles");
    wp_enqueue_style("jquery-ui-flick");
    wp_enqueue_style("jquery-fancybox");
    wp_enqueue_style("jquery-dropdown");
    wp_enqueue_style("jquery-tip");
    wp_enqueue_style("jquery-datetimepicker");

    wp_enqueue_script("jquery", true);
    wp_enqueue_script("jquery-ui-core");
    wp_enqueue_script("jquery-ui-dialog");
    wp_enqueue_script("jquery-ui-datepicker");
    wp_enqueue_script("jquery-ui-button");
    wp_enqueue_script("jquery-ui-sortable");
    wp_enqueue_script("jquery-ui-autocomplete");
    wp_enqueue_script("jquery-dropdown", "jquery");
    wp_enqueue_script("jquery-datetimepicker","jquery");
    wp_enqueue_script("jquery-touch", "jquery");

    wp_enqueue_script("jquery-fancybox","jquery");
    wp_enqueue_script("qm-math", false, true);
    wp_enqueue_script("timezone", "jquery", false, true);
    wp_enqueue_script("qm-sdk", false, true);
    wp_enqueue_script("jquery-simpletip", "jquery", false, true);
    wp_enqueue_script("highcharts", "jquery", false, true);
    wp_enqueue_script("highcharts-more", "highcharts", false, true);
    wp_enqueue_script("correlate-charts", array("highcharts-more", "qm-sdk", "qm-math"), false, true);
    wp_enqueue_script("other-shared","jquery", false, true);
    wp_enqueue_script("variable-settings","jquery", false, true);
    wp_enqueue_script("refresh-shared","jquery", false, true);
    wp_enqueue_script("variable-picker","jquery", false, true);
    wp_enqueue_script("correlate", false, true);
}
add_action( 'wp_enqueue_scripts', 'quantimodo_personal_studies_scripts_css' );

get_header();
?>

<?php if (!is_user_logged_in()): ?>
    <div class="dialog-background" id="login-dialog-background"></div>
    <div class="dialog" id="login-dialog">
        <?php login_with_ajax(); ?>
    </div>
<?php endif; ?>


<?php require "modules/dialog_add_measurement.php"; ?>
<?php require "modules/dialog_delete_measurements.php"; ?>
<?php require "modules/dialog_share.php"; ?>
<?php require "modules/variable_settings.php"; ?>
<?php require "modules/import_variables.php"; ?>


<div id="content">
     <div class="container">
             <!-- Fetch Post data and title -->
			 <div id="content" class="widecolumn">
               <?php if (have_posts()) : while (have_posts()) : the_post();?>
               <div class="post">
               <h1 id="post-<?php the_ID(); ?>" style="text-align:center;"><?php the_title();?></h1>
               <div class="entrytext">
                 <?php the_content('<p class="serif">Read the rest of this page »</p>'); ?>
               </div>
             </div>
        <?php endwhile; endif; ?>
		<div class="separator"></div>
        </div>
		 <section id="section-configure">
        <div class="outstate">
            <div class="accordion-header" id="accordion-output-header">
                <div class="generalHeader resolutionHeader">
                    Examined Variable
                </div>
                <div class="icon-question-sign icon-large questionMark questionMarkAlone" title="This is the variable to be examined. It can be considered to be a hypothetical cause for the variables in the bar graph or hypothetical effect of the variables in the bar graph by changing the setting below."></div>
            </div>
            <div class="accordion-content closed" id="accordion-output-content">
                <div class="inner">
                    <select id="selectOutputCategory"></select>
                    <select id="selectEffectVariable"></select>
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
                    Correlations
                </div>
                <div id="gauge-timeline-settingsicon" data-dropdown="#dropdown-barchart-settings" class="gear-icon"></div>
                <div class="icon-question-sign icon-large questionMark" title="This is the list of variables in order of their correlation with your examined variable."></div>
            </header>
            <div class="graph-content" style="height: 596px; overflow-y: scroll;">
                <img src=" <?php echo get_template_directory_uri(); ?>/brainloading.gif" class="barloading" style="margin-left: 4%; margin-top: 20%; display:none" />
                <span class="no-data" style="display:none"> <br />  <center> No data found </center> <br /><br /></span>
                <div id="graph-bar" class="graph-content" >
                </div>
                <input type="hidden"  id="selectBargraphCauseVariable" value="" />
                <input type="hidden"  id="selectBargraphInputCategory" value="" />

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
        <div class="open" id="correlation-gauge" style="float:left;">
            <div class="inner">
                <header class="graph-header" id="correlation-gauge-header">
                    <div class="generalHeader correlationHeader">
                        Correlation Scatterplot
                    </div>
                    <!--  <div id="gauge-correlation-settingsicon" data-dropdown="#dropdown-gauge-settings" class="gear-icon"></div>
                      <div class="icon-question-sign icon-large questionMark"></div> -->
                </header>
                <div class="graph-content" style="width: 100%; overflow: hidden;">
                    <div style="float: right; width: 155px; padding-left: 12px; position:relative; height:100%; border-left: solid thin #F5FBFB;" id="gauge-correlation"></div>
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
                                <td> </td>		
                                <td> </td>
                            </tr>
                            <tr>
                                <td> </td>
                                <td> </td>
                            </tr>
                            <tr>
                                <td> </td>
                                <td> </td>
                            </tr>
                            <tr>
                                <td> </td>
                                <td> </td>
                            </tr>		
                            <tr>
                                <td> </td>
                                <td> </td>
                            </tr>								
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="open" id="scatterplot-graph" >
            <div class="inner">
                <header class="graph-header"  id="scatterplot-graph-header" >
                    <!-- <div class="generalHeader scatterplotHeader">
                         Correlation Scatterplot
                     </div>-->
                    <div class="keepInline">
                        <div id="graph-scatterplot-settingsicon" data-dropdown="#dropdown-scatterplot-settings" class="gear-icon"></div>
                        <div class="icon-plus-sign icon-2x plusMark" title="Click to add a measurement."></div>
                        <div class="icon-question-sign icon-large questionMark questionMarkPlus" title="Displays the collection of measurement points, each having the value of examined variable on the horizontal axis and the value of the other variable on the vertical axis."></div>
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
                <div id="gauge-timeline-settingsicon" data-dropdown="#dropdown-timeline-settings" class="gear-icon"></div>				
                <div class="icon-plus-sign icon-2x plusMark" title="Click to add a measurement." ></div>
                <div class="icon-question-sign icon-large questionMark questionMarkPlus" title="Shows the measurement data in the order of measurement dates."></div>
            </header>
            <div class="graph-content" id="graph-timeline"></div>
        </div>

    </section>
</div>

<?php
get_footer();
?>

<!-- Menu for correlation gauge settings -->
<div id="dropdown-gauge-settings" class="dropdown dropdown-tip">
    <ul class="dropdown-menu">
        <li><a id="shareCorrelationGauge">Share graph</a></li>
    </ul>
</div>
<!-- Menu for timeline settings -->
<div id="dropdown-timeline-settings" class="dropdown dropdown-tip dropdown-anchor-right">
    <ul class="dropdown-menu">
        <li><label><input name="tl-enable-markers" type="checkbox" /> Show markers</label></li>
        <li><label><input name="tl-smooth-graph" type="checkbox" /> Smoothen graph</label></li>
        <li><label><input name="tl-enable-horizontal-guides" type="checkbox" /> Show horizontal guides</label></li>
        <li class="dropdown-divider"></li>
        <li><a id="shareTimeline">Share graph</a></li>
    </ul>
</div>

<!-- Menu for timeline settings -->
<div id="dropdown-scatterplot-settings" class="dropdown dropdown-tip dropdown-anchor-right">
    <ul class="dropdown-menu">
        <li><label><input name="sp-show-linear-regression" type="checkbox" /> Show linear regression</label></li>
        <li class="dropdown-divider"></li>
        <li><a id="shareScatterplot" >Share graph</a></li>
    </ul>
</div>

<!-- Menu for barchart settings -->
<div id="dropdown-barchart-settings" class="dropdown dropdown-tip dropdown-anchor-right">
    <ul class="dropdown-menu">
        <li><a id="" onclick="sortByCorrelation()">Sort By Correlation</a></li>		
        <li><a id="shareScatterplot"  onclick="sortByCausality()">Sort By Causality Factor</a></li>
        <li  style="padding:3px 15px;"><input type="text" id="minimumNumberOfSamples" placeholder="Min. Number of Samples"></li>
    </ul>
</div>
</div>


<?php
get_footer('buddypress');
?>