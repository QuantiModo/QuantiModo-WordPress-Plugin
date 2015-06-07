AnalyzeChart = function() 
{
	// Timeline data
	var timelineChart;

	// Timeline settings
	var	tlSmoothGraph, tlGraphType; // Smoothgraph true = graphType spline
	var tlEnableMarkers;
	var tlEnableHorizontalGuides;

	var retrieveSettings = function()
	{
		if (typeof(Storage) !== "undefined")
		{
			tlEnableMarkers = (localStorage["tlEnableMarkers"] || "true") == "true" ? true : false;
			tlSmoothGraph = (localStorage["tlSmoothGraph"] || "true") == "true" ? true : false;
			tlEnableHorizontalGuides = 	(localStorage["tlEnableHorizontalGuides"] || "false") == "true" ? true : false;
			tlGraphType = tlSmoothGraph == true ? "spline" : "line";
		}
	}

	var setSettings = function(newSettings)
	{
		if (typeof newSettings["tlSmoothGraph"] != "undefined")
		{
			tlSmoothGraph = newSettings["tlSmoothGraph"];
			tlGraphType = tlSmoothGraph == true ? "spline" : "line";
			for(i = 0; i < timelineChart.series.length; i++)
			{			
				if(timelineChart.series[i].name == "Navigator")
				{
					continue;
				}
				timelineChart.series[i].update({
					type: tlGraphType
				}, false);
			}
			saveSetting("tlSmoothGraph", tlSmoothGraph);
		}

		if (typeof newSettings["tlEnableMarkers"] != "undefined")
		{
			tlEnableMarkers = newSettings["tlEnableMarkers"];
			for(i = 0; i < timelineChart.series.length; i++)
			{		
				if(timelineChart.series[i].name == "Navigator")
				{
					continue;
				}
				timelineChart.series[i].update({
					marker:
					{
						enabled: tlEnableMarkers
					}
				}, false);
			}
			saveSetting("tlEnableMarkers", tlEnableMarkers);
		}

		if (typeof newSettings["tlEnableHorizontalGuides"] != "undefined")
		{
			tlEnableHorizontalGuides = newSettings["tlEnableHorizontalGuides"];
			for(i = 1; i < timelineChart.yAxis.length; i++)
			{			
				timelineChart.yAxis[i].update({
					gridLineWidth:tlEnableHorizontalGuides
				}, false)
			}

			saveSetting("tlEnableHorizontalGuides", tlEnableHorizontalGuides);
		}

		timelineChart.redraw();
	};

	var saveSetting = function(setting, value)
	{
		if (typeof(Storage) !== "undefined")
		{
			localStorage[setting] = value;
		}
	};

	var initChartSettings = function()
	{
		jQuery('input[name=tl-enable-markers]').attr('checked', tlEnableMarkers);
		jQuery('input[name=tl-enable-markers]').change(function()
		{
			var settings = {
				tlEnableMarkers : jQuery(this).is(":checked")
			};
			setSettings(settings);
		});

		jQuery('input[name=tl-smooth-graph]').attr('checked', tlSmoothGraph);
		jQuery('input[name=tl-smooth-graph]').change(function()
		{
			var settings = {
				tlSmoothGraph : jQuery(this).is(":checked")
			};
			setSettings(settings);

		});

		jQuery('input[name=tl-enable-horizontal-guides]').attr('checked', tlEnableHorizontalGuides);
		jQuery('input[name=tl-enable-horizontal-guides]').change(function()
		{
			var settings = {
				tlEnableHorizontalGuides : jQuery(this).is(":checked")
			};
			setSettings(settings);
		});
	};

	var addGraph = function(variable, timeSeries)
	{
		var title = variable.name + ' (' + variable.unit + ')';
		if (variable.source != null && variable.source.length > 0)
		{
			title += ' from ' + variable.source;
		}

		timelineChart.addAxis({
							gridLineWidth:tlEnableHorizontalGuides,
							title:	{
										text: title, style: { color: variable.color }
									},
							labels: {
										formatter: function() { return this.value; },
										style: { color: variable.color }
									},
							opposite: (timelineChart.yAxis.length % 2 == 1),
							variable: variable
						  },
						  false, false);	// isX, redraw
		timelineChart.addSeries({
							yAxis:	timelineChart.yAxis.length - 1,
							name: 	title,
							type: 	tlGraphType,
							color: 	variable.color,
							data: 	timeSeries,
							marker: {
										enabled: tlEnableMarkers,
										radius: 2.5
									},
							variable: variable
						},
						false);	// redraw

		timelineChart.redraw();
	};
        
        function initDatePickerForHighChartsRangeSelecter(minimum, maximum) {           
            if(timelineChart != null) {
                if(!jQuery("input.highcharts-range-selector[name='min']", jQuery('#' + timelineChart.options.chart.renderTo)).hasClass('hasDatepicker')) {
                    jQuery("input.highcharts-range-selector[name='min']", jQuery('#' + timelineChart.options.chart.renderTo)).datepicker( {
                        dateFormat: "'From' MM d',' yy",
			defaultDate: new Date(minimum),
                        minDate: new Date(minimum),
			maxDate: new Date(maximum),                    
			changeMonth: true,
			changeYear: true,                        
                        onSelect: function(dateText) {
                            var currentMin = jQuery(this).datepicker("getDate");
                            var currentMax = new Date(jQuery("input.highcharts-range-selector[name='max']", jQuery('#' + timelineChart.options.chart.renderTo)).val());
                            if (currentMax.getTime() - currentMin.getTime() < ((5 * 24 * 60 * 60 * 1000))) { 
                                currentMin.setTime(currentMax.getTime() - ((5 * 24 * 60 * 60 * 1000)));				
                            } else {
                                currentMin.setTime(currentMin.getTime());
                            }                           
                            jQuery("input.highcharts-range-selector[name='min']", jQuery('#' + timelineChart.options.chart.renderTo)).datepicker( "option", "defaultDate", currentMin);
                            jQuery("input.highcharts-range-selector[name='min']", jQuery('#' + timelineChart.options.chart.renderTo)).val(jQuery.datepicker.formatDate("'To' MM d',' yy", new Date(currentMin.getTime() + (24 * 60 * 60 * 1000))));  
                            this.onchange();
                            this.onblur();
                             
                        }
                    });
                 } else {
                    jQuery("input.highcharts-range-selector[name='min']", jQuery('#' + timelineChart.options.chart.renderTo)).datepicker( "option", "defaultDate", new Date(minimum));
                    jQuery("input.highcharts-range-selector[name='min']", jQuery('#' + timelineChart.options.chart.renderTo)).datepicker( "option", "minDate", new Date(minimum));
                    jQuery("input.highcharts-range-selector[name='min']", jQuery('#' + timelineChart.options.chart.renderTo)).datepicker( "option", "maxDate", new Date(maximum));
                            
                }
                if(!jQuery("input.highcharts-range-selector[name='max']", jQuery('#' + timelineChart.options.chart.renderTo)).hasClass('hasDatepicker')) {
                    jQuery("input.highcharts-range-selector[name='max']", jQuery('#' + timelineChart.options.chart.renderTo)).datepicker({
                        dateFormat: "'From' MM d',' yy",
			defaultDate: new Date(maximum),
                        minDate: new Date(minimum),
			maxDate: new Date(maximum),                       
			changeMonth: true,
			changeYear: true,
                        onSelect: function(dateText) {
                            var currentMax = jQuery(this).datepicker("getDate");
                            var currentMin = new Date(jQuery("input.highcharts-range-selector[name='min']", jQuery('#' + timelineChart.options.chart.renderTo)).val());
                            if (currentMax.getTime() - currentMin.getTime() < ((5 * 24 * 60 * 60 * 1000))) { 
                                currentMax.setTime(currentMin.getTime() + ((5 * 24 * 60 * 60 * 1000)));				
                            } else {
                                currentMax.setTime(currentMax.getTime());
                            }                           
                            jQuery("input.highcharts-range-selector[name='max']", jQuery('#' + timelineChart.options.chart.renderTo)).datepicker( "option", "defaultDate", currentMax);
                            jQuery("input.highcharts-range-selector[name='max']", jQuery('#' + timelineChart.options.chart.renderTo)).val(jQuery.datepicker.formatDate("'To' MM d',' yy", new Date(currentMax.getTime() + (24 * 60 * 60 * 1000))));  
                            this.onchange();
                            this.onblur();
                        }
                    });   
                } else {
                    jQuery("input.highcharts-range-selector[name='max']", jQuery('#' + timelineChart.options.chart.renderTo)).datepicker( "option", "defaultDate", new Date(maximum));
                    jQuery("input.highcharts-range-selector[name='max']", jQuery('#' + timelineChart.options.chart.renderTo)).datepicker( "option", "minDate", new Date(minimum));
                    jQuery("input.highcharts-range-selector[name='max']", jQuery('#' + timelineChart.options.chart.renderTo)).datepicker( "option", "maxDate", new Date(maximum));
                            
                }
            }
        }
        
        var updateExrtemes = function(timeSeries) {
            var minimum, maximum;
            if(timeSeries.length > 0) {
                minimum = timeSeries[0][0];
                maximum = timeSeries[0][0];
            }
            for(var i=0; i<timeSeries.length; i++) {
		if (minimum > timeSeries[i][0]) minimum = timeSeries[i][0];
		if (maximum < timeSeries[i][0]) maximum = timeSeries[i][0];		
            }
           
            timelineChart.xAxis[0].setExtremes(minimum, maximum);
            initDatePickerForHighChartsRangeSelecter(minimum, maximum);
        }  

	var updateGraph = function(seriesPosition, timeSeries)
	{
		timelineChart.series[seriesPosition].setData(timeSeries, true);
	};

	var createTimeSeries = function(data) 
	{
		timeSeries = [];

		var dataLength = data.length;
		for (var i = 0; i < dataLength; i++)
		{
			var date = data[i].timestamp * 1000
			var value = data[i].value;

			timeSeries.push([date, value]);

			if (data[i].repeat != null)
			{
				var numRepeats = data[i].repeat.times;
				var interval = data[i].repeat.interval * 1000;
				for(n = 0; n < numRepeats; n++)
				{
					date += interval
					timeSeries.push([date, value]);
				}
			}
		}

		return timeSeries;
	};

	var addData = function(variable, data) 
	{
		var timeSeries = createTimeSeries(data);

		var seriesPosition = getSeriesPosition(variable);
		if(seriesPosition >= 0)
		{
			updateGraph(i, timeSeries);
		}
		else
		{
			addGraph(variable, timeSeries);
		}
                updateExrtemes(timeSeries);
	};

	var removeData = function(variable)
	{
		var seriesPosition = getSeriesPosition(variable);
		var axisPosition = getAxisPosition(variable);

		if(seriesPosition >= 0 && axisPosition >= 0)
		{
			timelineChart.series[seriesPosition].remove(false);
			timelineChart.yAxis[axisPosition].remove(false);
			timelineChart.redraw();
		}
	}

	var toggleDataVisibility = function(variable)
	{
		var seriesPosition = getSeriesPosition(variable);

		if(seriesPosition >= 0)
		{
			var series = timelineChart.series[seriesPosition];
			if(series.visible)
			{
				series.hide();
				return false;
			} 
			else 
			{
				series.show();
				return true;
			}
		}
		else
		{
			return false;
		}
	}

	var getSeriesPosition = function(variable)
	{
		for(i = 0; i < timelineChart.series.length; i++)
		{
			var currentSeries = timelineChart.series[i];
			if(currentSeries.options.variable != null)
			{
				var seriesVar = currentSeries.options.variable;
				if(seriesVar.originalName == variable.originalName && seriesVar.category == variable.category && seriesVar.source == variable.source)
				{
					return i;
				}
			}
		}

		return -1;
	}	

	var getAxisPosition = function(variable)
	{
		for(i = 0; i < timelineChart.yAxis.length; i++)
		{
			var currentAxis = timelineChart.yAxis[i];
			if(currentAxis.options.variable != null)
			{
				var axisVar = currentAxis.options.variable;
				if(axisVar.originalName == variable.originalName && axisVar.category == variable.category && axisVar.source == variable.source)
				{
					return i;
				}
			}
		}

		return -1;
	}

	var initTimelineChart = function()
	{
        Highcharts.setOptions({
            colors: ['#1851CE', '#C61800', '#31B639', '#FFCF00']
        });

		timelineChart = new Highcharts.StockChart({
			chart: { renderTo: 'graph-timeline', zoomType: 'x', backgroundColor:'rgba(255, 255, 255, 0.1)'},
			title: { text: '' },
			subtitle: { text: '' },
			legend: { enabled: false },
			scrollbar: {
				barBackgroundColor: '#eeeeee',
				barBorderRadius: 0,
				barBorderWidth: 0,
				buttonBackgroundColor: '#eeeeee',
				buttonBorderWidth: 0,
				buttonBorderRadius: 0,
				trackBackgroundColor: 'none',
				trackBorderWidth: 0.5,
				trackBorderRadius: 0,
				trackBorderColor: '#CCC'
			},
			navigator : {
				adaptToUpdatedData: false,
				margin: 10,
				height: 50,
				handles: {
					backgroundColor: '#eeeeee'
				}
			},
			xAxis: {
				type: 'datetime',
				dateTimeLabelFormats: {
					millisecond: '%H:%M:%S.%L',
					second: '%H:%M:%S',
					minute: '%H:%M',
					hour: '%H:%M',
					day: '%e. %b',
					week: '%e. %b',
					month: '%b \'%y',
					year: '%Y'
				}
			},
			tooltip: {
				formatter: function() {
					var color = (this.points.length === 1 ? this.points[0].series.options.color : '#31B639');
					var date = Highcharts.dateFormat('%Y %b %d', this.points[0].x);
					var result = '<span style="color: ' + color + ';"><i>' + date + '</i></span><br>';
					for (var i = 0; i < this.points.length; i++) {
						var point = this.points[i];
						if (i !== 0) { result += '<br>'; }
						var pointColor = point.series.options.color;
						var pointName = point.series.name;
						var pointLocation = Highcharts.numberFormat(point.y, 2);
						result += '<span style="color: ' + pointColor + ';"><b>' + pointName + '</b>: ' + pointLocation+ '</span>';
					}
					return result;
				},
				shared: true, useHTML: true
			},
			plotOptions: {
				series: {
					lineWidth: 1,
					states: {
						hover: {
							enabled: true,
							lineWidth: 1.5
						}
					}
				}
			},
			credits: {
				enabled: false
			}
		});
	};

	return {
		init: function()
		{
			retrieveSettings();
			initChartSettings();
			initTimelineChart();
		},
		addData: addData,
		removeData: removeData,
		toggleDataVisibility: toggleDataVisibility
	};
}();

jQuery(AnalyzeChart.init);