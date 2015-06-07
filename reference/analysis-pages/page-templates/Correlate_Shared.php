<?php
/**
 *	Template Name: Correlate (Shared) Page
 *	Description: Page for displaying shared correlation data
 */
	$stylesheet_dir = get_stylesheet_directory_uri();
	
 	wp_enqueue_style("correlate", $stylesheet_dir . "/css/correlate.css");
 	wp_enqueue_style("correlate-shared", $stylesheet_dir . "/css/correlate_shared.css");
	wp_enqueue_style("jquery-ui-flick", $stylesheet_dir . "/css/jquery-ui-flick.css");
	wp_enqueue_style("jquery-dropdown", $stylesheet_dir . "/css/jquery.dropdown.css");
	wp_enqueue_style("select2", $stylesheet_dir . "/css/metrolium.select2.css");
	
	wp_enqueue_script("jquery", true);
	wp_enqueue_script("jquery-ui-core");
	wp_enqueue_script("jquery-ui-dialog");
	wp_enqueue_script("jquery-ui-datepicker");
	wp_enqueue_script("jquery-ui-button");
	wp_enqueue_script("jquery-ui-sortable");
	wp_enqueue_script("jquery-dropdown", $stylesheet_dir ."/js/libs/jquery.dropdown.min.js", "jquery");
	wp_enqueue_script("jquery-touch",$stylesheet_dir . "/js/libs/jquery.ui.touch-punch.min.js", "jquery");
	wp_enqueue_script("qm-math", $stylesheet_dir . "/js/math.js", "jquery", false, true);
	wp_enqueue_script("timezone", $stylesheet_dir . "/js/jstz.min.js", "jquery", false, true);
	wp_enqueue_script("qm-sdk", $stylesheet_dir . "/js/libs/quantimodo-api.js", "jquery", false, true);
	wp_enqueue_script("highcharts", "https://code.highcharts.com/highcharts.js", "jquery", false, true);
	wp_enqueue_script("highcharts-more", "https://code.highcharts.com/highcharts-more.js", "highcharts", false, true);
	wp_enqueue_script("correlate-charts", $stylesheet_dir . "/js/correlate_charts.js", array("highcharts-more", "qm-sdk", "qm-math"), false, true);
	wp_enqueue_script("correlate-shared", $stylesheet_dir . "/js/correlate_shared.js", array("correlate-charts", "jquery-ui-datepicker", "jquery-ui-button"), false, true);
	wp_enqueue_script('select2', $stylesheet_dir."/js/libs/select2.js");
	wp_enqueue_script('dom', $stylesheet_dir."/js/dom.js");

	get_header();
?>

<div id="content">
	<div class="open" id="correlation-gauge">
			<div class="inner">
				<header class="graph-header">
					<div style="float: left; line-height: 42px;">
						Correlation
					</div>
					<div id="gauge-correlation-settingsicon" data-dropdown="#dropdown-gauge-settings" class="gear-icon"></div>
				</header>
				<div class="graph-content" style="width: 100%; overflow: hidden;">
					<div style="float: right; width: 155px; padding-left: 12px; position:relative; height:100%; border-left: solid thin #F5FBFB;" id="gauge-correlation"></div>
					<div style="overflow: hidden; height: 100%;">
						<table style="height: 100%;">
							<tr>
								<td>
									Statistical Relationship
								</td>
								<td>
									Significant
								</td>
							</tr>
							<tr>
								<td>
									Effect Size
								</td>
								<td>
									Large
								</td>
							</tr>
								<td> </td>
								<td> </td>
							<tr>
								<td> </td>		
								<td> </td>
							</tr>
								<td> </td>
								<td> </td>
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
		</div><!---
		
	---><div class="open" id="scatterplot-graph">
			<div class="inner">
				<header class="graph-header">
					<div style="float: left; line-height: 42px;">
						Correlation Scatterplot
					</div>
					<div id="graph-scatterplot-settingsicon" data-dropdown="#dropdown-scatterplot-settings"class="gear-icon"></div>
				</header>
				<div class="graph-content" id="graph-scatterplot"></div>
			</div>
		</div><!---		

	---><div id="timeline-graph">
			<header class="graph-header">
				<div style="float: left; line-height: 42px;">
					Timeline
				</div>
				<div id="gauge-timeline-settingsicon" data-dropdown="#dropdown-timeline-settings" class="gear-icon"></div>
			</header>
			<div class="graph-content" id="graph-timeline"></div>
		</div>
</div>

<?php
 get_footer();
?>

<!-- Menu for correlation gauge settings -->
<div id="dropdown-gauge-settings" class="dropdown dropdown-tip">
	<ul class="dropdown-menu">
	</ul>
</div>
<!-- Menu for timeline settings -->
<div id="dropdown-timeline-settings" class="dropdown dropdown-tip dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><label><input name="tl-enable-markers" type="checkbox" /> Show markers</label></li>
		<li><label><input name="tl-smooth-graph" type="checkbox" /> Smoothen graph</label></li>
	</ul>
</div>

<!-- Menu for timeline settings -->
<div id="dropdown-scatterplot-settings" class="dropdown dropdown-tip dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><label><input name="sp-show-linear-regression" type="checkbox" /> Show linear regression</label></li>
	</ul>
</div>