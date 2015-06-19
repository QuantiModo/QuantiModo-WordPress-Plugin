<?php
/**
 *	Template Name: Dashboard Page
 *	Description: Page based on original PHP website
 */
	$stylesheet_dir = get_stylesheet_directory_uri();
		
 	wp_enqueue_style("dashboard", $stylesheet_dir . "/css/dashboard.css");
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
	wp_enqueue_script("correlate", $stylesheet_dir . "/js/correlate.js", array("correlate-charts", "jquery-ui-datepicker", "jquery-ui-button"), false, true);
	wp_enqueue_script('select2', $stylesheet_dir."/js/libs/select2.js");
	wp_enqueue_script('dom', $stylesheet_dir."/js/dom.js");
	
	get_header();
?>

<?php if(!is_user_logged_in()): ?>
<div class="dialog-background" id="login-dialog-background"></div>
<div class="dialog" id="login-dialog">
	<?php login_with_ajax(); ?>
</div>
<?php endif; ?>

<div class="dialog-background transitions" id="share-dialog-background"></div>
<div class="dialog transitions" id="share-dialog">
	<div class="loading-overlay" id="settings-loading"></div>
	<button id="button-doshare">Share</button>
	<button id="button-cancelshare">Close</button>
</div>

<div id="content">
	<section id="section-configure">
		<div id="section-configure-input" class="open">
			<div class="inner">
				<div class="accordion-header" id="accordion-date-header">
					<div style="float: left; line-height: 42px;">
						Date range
					</div>
				</div>
				<div class="accordion-content closed" id="accordion-date-content">
					<div class="inner">
						<div id="accordion-content-datepickers">
							<input type="text" id="datepicker-start" />
							<input type="text" id="datepicker-end" />
						</div>
						<div id="accordion-content-rangepickers">
							<input type="radio" value="Second" id="radio1" name="radio" /><label for="radio1">Second</label>
							<input type="radio" value="Minute" id="radio2" name="radio" /><label for="radio2">Minute</label>
							<input type="radio" value="Hour" id="radio3" name="radio" /><label for="radio3">Hour</label>
							<input type="radio" value="Day" id="radio4" name="radio" checked='checked' /><label for="radio4">Day</label>
							<input type="radio" value="Week" id="radio5" name="radio" /><label for="radio5">Week</label>
							<input type="radio" value="Month" id="radio6" name="radio" /><label for="radio6">Month</label>
						</div>
					</div>
				</div>
				
				<div class="accordion-header" id="accordion-examined-variable-header">
					<div style="float: left; line-height: 42px;">
						Input Behaviour
					</div>
				</div>
				<div class="accordion-content closed" id="accordion-examined-variable-content">
					<div class="inner">
						Category<br>
						<select id="selectInputCategory">
						</select>
						
						<br>Variable<br>
						<select id="selectExaminedVariable">
						</select>
						
						<button id="button-examined-variable-variable-settings">Settings</button>
					</div>
				</div>
				
				<div class="accordion-header" id="accordion-secondary-variable-header">
					<div style="float: left; line-height: 42px;">
						Output State
					</div>
				</div>
				<div class="accordion-content closed" id="accordion-secondary-variable-content">
					<div class="inner">
						Category<br>
						<select id="selectOutputCategory">
						</select>
						
						<br>Variable<br>
						<select id="selectSecondaryVariable">
						</select>
						<button id="button-secondary-variable-variable-settings">Settings</button>
					</div>
				</div>		
			</div>
		</div>
		
		<div class="closed" id="section-configure-settings">
			<div class="inner">
				<div class="accordion-header" id="accordion-settings-header">
					<div style="float: left; line-height: 42px;">
						Settings
					</div>
				</div>
				<div class="accordion-content closed" id="accordion-settings-content">
					<div class="inner">
						<div class="loading-overlay" id="settings-loading"></div>
						<b style="margin-top: 12px;">Properties</b>
						<table border="0" cellspacing="0">
							<tr>
						    	<td>Variable name</td>
								<td><input id="input-variable-name" type="text" placeholder=""></td>
							</tr>
							<tr>
								<td>Unit</td>
								<td>
									<select id="selectVariableUnitSetting">
									</select>
								</td>
							</tr>
							<tr>
								<td>Category</td>
								<td>
									<select id="selectVariableCategorySetting">
									</select>
								</td>
							</tr>
						</table>
						
						<b style="margin-top: 8px;">Data Optimization</b>
						<table border="0" style="border-collapse:collapse;" cellspacing="0">
						    <tr>
						    	<td>Minimum value</td>
								<td><input type="text" id="variableMinimumValueSetting" placeholder=""></td>
							</tr>
							<tr>
								<td>Maximum value</td>
								<td><input type="text" id="variableMaximumValueSetting" placeholder=""></td>
							</tr>
						</table>
						<div>
							When there's no data:
							<div>
								<input type="radio" name="missingAssumptionGroup" id="assumeMissing" checked="true">
								<label>Assume data is missing</label>
							</div>
							<div>
								<input type="radio" name="missingAssumptionGroup" id="assumeValue">
								<label>Assume <input id="variableFillingValueSetting" style="text-align: center; width: 50px; height: 26px;" type="text" id="inputVariableMaximumValueSetting" placeholder=""> for that time</label>
							</div>
						</div>
						
						<b style="margin-top: 8px;">Joined variables</b>
						<div style="margin-bottom: 8px;">
							<ul id="joinedVariablesList">
							</ul>
							<select id="joinedVariablePicker"></select>
							<button id="addJoinedVariableButton"></button>
						</div>
						<!--<b style="margin-top: 4px;">Sources</b>
						<div>
							<ul id="sourcesSortable">
							  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><label>Medhelper</label></li>
							  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><label>My Pillbox</label></li>
							  <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><label>MediGuard</label></li>
							</ul>
						</div>-->
						<button class="button-cancel buttonrow-2">Cancel</button>
						<button class="button-save buttonrow-2" style="margin-bottom: 12px">Save</button>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="section-analyze">
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
								<td id="statisticalRelationshipValue">
									Significant
								</td>
							</tr>
							<tr>
								<td>
									Effect Size
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