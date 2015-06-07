AnalyzeChart = function() {
    var inputColor = '#5DA5DA', outputColor = '#F15854', mixedColor = '#60BD68', linearRegressionColor = '#bf8f1f';

	var inputData = { variableName: 'Data loading...', unit: 'Data loading...', timeSeries: [], interpolant: function() { return 0; } };
	var outputData = { variableName: 'Data loading...', unit: 'Data loading...', timeSeries: [], interpolant: function() { return 0; } };
	var inputVariable, outputVariable;

	var timelineChart, scatterplotChart, correlationGauge;

	// Timeline settings
    var	tlSmoothGraph, tlGraphType; // Smoothgraph true = graphType spline
	var tlEnableMarkers;

	// Scatterplot settings
	var spShowLinearRegression

	var retrieveSettings = function()
	{
		if (typeof(Storage)!=="undefined")
		{
			tlEnableMarkers = (localStorage["tlEnableMarkers"] || "true") == "true" ? true : false;
			tlSmoothGraph = (localStorage["tlSmoothGraph"] || "true") == "true" ? true : false;
			tlGraphType = tlSmoothGraph == true ? "spline" : "line";
			spShowLinearRegression = (localStorage["spShowLinearRegression"] || "true") == "true" ? true : false;
		}
	}

	var setSettings = function(newSettings)
	{
	    if (typeof newSettings["tlSmoothGraph"] != "undefined")
		{
			tlSmoothGraph = newSettings["tlSmoothGraph"];
			tlGraphType = tlSmoothGraph == true ? "spline" : "line";
			timelineChart.series[0].update({
				type: tlGraphType
			}, false);
			timelineChart.series[1].update({
				type: tlGraphType
			}, false);
			saveSetting("tlSmoothGraph", tlSmoothGraph);
		}

		if (typeof newSettings["tlEnableMarkers"] != "undefined")
		{
			tlEnableMarkers = newSettings["tlEnableMarkers"];
			timelineChart.series[0].update({
				marker:
				{
					enabled: tlEnableMarkers
				}
			}, false);
			timelineChart.series[1].update({
				marker:
				{
					enabled: tlEnableMarkers
				}
			}, false);
			saveSetting("tlEnableMarkers", tlEnableMarkers);
		}

		if (typeof newSettings["spShowLinearRegression"] != "undefined")
		{
			spShowLinearRegression = newSettings["spShowLinearRegression"];
			scatterplotChart.series[0].update({
				visible:  spShowLinearRegression
			}, false);
			saveSetting("spShowLinearRegression", spShowLinearRegression);
		}

		timelineChart.redraw();
		scatterplotChart.redraw();
	};

	var saveSetting = function(setting, value)
	{
		if (typeof(Storage)!=="undefined")
		{
			localStorage[setting] = value;
		}
	}

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

		jQuery('input[name=sp-show-linear-regression]').attr('checked', spShowLinearRegression);
		jQuery('input[name=sp-show-linear-regression]').change(function()
		{
			var settings = {
				spShowLinearRegression : jQuery(this).is(":checked")
			};
			setSettings(settings);

		});
	}

	var updateGraphs = function()	//TODO split graphs, update series separately
	{
		var versus;
		if (inputData.variableName == outputData.variableName)
		{
			versus = inputData.variableName;
		}
		else
		{
			versus = inputData.variableName + ' vs ' + outputData.variableName;
		}

		timelineChart.setTitle({ text: versus });
		timelineChart.yAxis[0].update({ title: { text: inputData.variableName + ' (' + inputData.unit + ')'} }, false);
		timelineChart.yAxis[1].update({ title: { text: outputData.variableName  + ' (' + outputData.unit + ')'} }, false);

		timelineChart.series[0].update({ name: inputData.variableName, data: inputData.timeSeries}, false);
		timelineChart.series[1].update({ name: outputData.variableName, data: outputData.timeSeries}, false);

		var inputTimeSeries = inputData.timeSeries, inputInterpolant = inputData.interpolant;
		var outputTimeSeries = outputData.timeSeries, outputInterpolant = outputData.interpolant;
		var scatterplotDots = [];

		var hideScatterplot = (inputTimeSeries.length === 0) || (outputTimeSeries.length === 0);
		if (!hideScatterplot && (inputData.variableName == outputData.variableName) && (inputTimeSeries.length === outputTimeSeries.length)) {
			hideScatterplot = true;
			for (var i = 0; i < inputTimeSeries.length; i++) {
				var inputPoint = inputTimeSeries[i], outputPoint = outputTimeSeries[i];
				if ((inputPoint[0] !== outputPoint[0]) || (inputPoint[1] !== outputPoint[1])) {
					hideScatterplot = false;
					break;
				}
			}
		}

		if (hideScatterplot)
		{
			AnalyzePage.hideScatterplot();
			AnalyzePage.hideCorrelationGauge();
		}
		else
		{
			var i = 0, j = 0;
			var inputPoint = inputTimeSeries[0], inputTime = inputPoint[0], inputDatum = inputPoint[1];
			var outputPoint = outputTimeSeries[0], outputTime = outputPoint[0], outputDatum = outputPoint[1];
			while(true)
			{
				if (inputTime === outputTime)
				{
					if(inputDatum !== null && outputDatum !== null)
					{
						// Insert twice so that coincident points don't get counted only half as much as others
						var dot = { time: inputTime, x: outputDatum, y: inputDatum };
						scatterplotDots.push(dot);
						scatterplotDots.push(dot);
					}

					if (++i === inputTimeSeries.length) 
						{ break; }
					if (++j === outputTimeSeries.length) 
						{ break; }
					inputPoint = inputTimeSeries[i]; inputTime = inputPoint[0]; inputDatum = inputPoint[1];
					outputPoint = outputTimeSeries[j]; outputTime = outputPoint[0]; outputDatum = outputPoint[1];
				}
				else if (inputTime < outputTime)
				{
					if (inputDatum !== null && j > 0) 
					{ 
						scatterplotDots.push({ time: inputTime, x: outputInterpolant(inputTime), y: inputDatum }); 
					}
					if (++i === inputTimeSeries.length) 
						{ break; }
					inputPoint = inputTimeSeries[i]; inputTime = inputPoint[0]; inputDatum = inputPoint[1];
				}
				else
				{
					if (outputDatum !== null && i > 0) 
					{ 
						scatterplotDots.push({ time: outputTime, x: outputDatum, y: inputInterpolant(outputTime) }); 
					}
					if (++j === outputTimeSeries.length) 
						{ break; }
					outputPoint = outputTimeSeries[j]; outputTime = outputPoint[0]; outputDatum = outputPoint[1];
				}
			}

			scatterplotChart.setTitle({ text: versus });
			scatterplotChart.yAxis[0].update({ min: inputData.minimum, max: inputData.maximum, title: { text: inputData.variableName + ' (' + inputData.unit + ')'} }, false);
			scatterplotChart.xAxis[0].update({ min: outputData.minimum, max: outputData.maximum, title: { text: outputData.variableName  + ' (' + outputData.unit + ')'} }, false);
			scatterplotChart.series[0].setData(QuantimodoMath.linearRegressionEndpoints(scatterplotDots, outputData.minimum, outputData.maximum), false);
			scatterplotChart.series[1].update({ name: versus }, false);
			scatterplotChart.series[1].setData(scatterplotDots, false);

			var correlation = Math.min(1, Math.max(-1, QuantimodoMath.correlationCoefficient(scatterplotDots)));
			correlationGauge.series[0].points[0].update(correlation);

			var effectSizeText;
			var statisticalRelationshipText = "Not enough samples";
			if (correlation > 0.5)
			{
				effectSizeText = "Strong Positive";
				if (scatterplotDots.length > 17)
				{
					statisticalRelationshipText = "Significant";
				}
			}
			else if (correlation > 0.3)
			{
				effectSizeText = "Medium Positive";
				if (scatterplotDots.length > 45)
				{
					statisticalRelationshipText = "Significant";
				}
			}
			else if (correlation > 0.1)
			{
				effectSizeText = "Weak Positive";
				if (scatterplotDots.length > 400)
				{
					statisticalRelationshipText = "Significant";
				}
			}
			else if (correlation > -0.1)
			{
				effectSizeText = "None";
			}
			else if (correlation > -0.3)
			{
				effectSizeText = "Weak Negative";
				if (scatterplotDots.length > 400)
				{
					statisticalRelationshipText = "Significant";
				}
			}
			else if (correlation > -0.5)
			{
				effectSizeText = "Medium Negative";
				if (scatterplotDots.length > 45)
				{
					statisticalRelationshipText = "Significant";
				}
			}
			else
			{
				effectSizeText = "Strong Negative";
				if (scatterplotDots.length > 17)
				{
					statisticalRelationshipText = "Significant";
				}
			}

			jQuery('#statisticalRelationshipValue').text(statisticalRelationshipText);
			jQuery('#effectSizeValue').text(effectSizeText);

			//AnalyzePage.showScatterplot();
			//AnalyzePage.showCorrelationGauge();

			scatterplotChart.redraw();
		}

		timelineChart.redraw();
	};

	function fillBeforeWithValue(firstDate, fillingValue, dates, values, timeSeries)
	{
		var groupingWidth = AnalyzePage.getPeriod() * 1000;
		var startTime = AnalyzePage.getStartTime() * 1000;
		var timeDiff =  firstDate - startTime;

		var numInsertions = timeDiff / groupingWidth;
		if (numInsertions > 0)
		{
			fillWithValue(startTime, numInsertions, groupingWidth, fillingValue, dates, values, timeSeries);
		}
	}
	function fillBetweenWithValue(date, nextDate, fillingValue, dates, values, timeSeries)
	{
		var groupingWidth = AnalyzePage.getPeriod() * 1000;
		var timeDiff = nextDate - date;
		var numInsertions =  timeDiff / groupingWidth;
		if (numInsertions > 0)
		{
			fillWithValue(date, numInsertions, groupingWidth, fillingValue, dates, values, timeSeries);
		}
	}
	function fillAfterWithValue(lastDate, fillingValue, dates, values, timeSeries)
	{
		var groupingWidth = AnalyzePage.getPeriod() * 1000;
		var timeDiff = (AnalyzePage.getEndTime() * 1000) - lastDate;

		var numInsertions = timeDiff / groupingWidth;
		if (numInsertions > 0)
		{
			fillWithValue(lastDate, numInsertions, groupingWidth, fillingValue, dates, values, timeSeries);
		}
	}

	function fillWithValue(startDate, numInsertions, interval, fillingValue, dates, values, timeSeries)
	{
		for(n = 1; n < numInsertions; n++)
		{
			var newDate = parseInt(startDate + (interval * n));
			dates.push(newDate);
			values.push(fillingValue);
			timeSeries.push([newDate, fillingValue]);
		}
	}

	var prepDataForGraphing = function(variable, data)
	{
		data.sort(function(a, b) { return a.timestamp < b.timestamp ? -1 : 1; });
		var dates = [], values = [], timeSeries = [];

		var noData = data.length === 0;
		var minimum = noData ? null : variable.minimumValue;
		if (minimum == -Infinity) { minimum = null;}
		var maximum = noData ? null : variable.maximumValue;
		if (maximum == Infinity) { maximum = null; }

		for (var i = 0; i < data.length; i++)
		{
			var date = data[i].timestamp * 1000
			var value = data[i].value;

			dates.push(date);
			values.push(value);
			timeSeries.push([date, value]);

			if (data[i].repeat != null)
			{
				var numRepeats = data[i].repeat.times;
				var interval = data[i].repeat.interval * 1000;
				for(n = 0; n < numRepeats; n++)
				{
					date += interval
					dates.push(date);
					values.push(value);
					timeSeries.push([date, value]);
				}
			}
		}

		return {
			variableName: variable.name.valueOf(),
			unit: noData ? 'unknown' : data[0].unit.valueOf(),
			minimum: minimum,
			maximum: maximum,
			timeSeries: timeSeries,
			interpolant: QuantimodoMath.createInterpolant(dates, values)
		};
	};

	var setInputData = function(variable, data)
	{
		inputVariable = variable;
		inputData = prepDataForGraphing(variable, data);
		updateGraphs();
	};

	var setOutputData = function(variable, data)
	{
		outputVariable = variable;
		outputData = prepDataForGraphing(variable, data);
		updateGraphs();
	};

	var initTimelineChart = function()
	{

        Highcharts.setOptions({
            colors: ['#1851CE', '#C61800', '#31B639', '#FFCF00']
        });

		timelineChart = new Highcharts.Chart({
			chart: { renderTo: 'graph-timeline', zoomType: 'x'},
			title: { text: 'Data loading...' },
			subtitle: { text: 'from Quantimodo.com' },
			legend: { enabled: false },
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
			yAxis: [
				{
					title: { text: 'Data loading...', style: { color: inputColor } },
					labels: { formatter: function() { return this.value; }, style: { color: inputColor } }
				},
				{
					title: { text: 'Data loading...', style: { color: outputColor } },
					labels: { formatter: function() { return this.value; }, style: { color: outputColor } },
					opposite: true
				}
			],
			tooltip: {
				formatter: function() {
					var result = '<span style="color: ' + (this.points.length === 1 ? this.points[0].series.options.color : mixedColor) + ';"><i>' +
						Highcharts.dateFormat('%Y %b %d', this.points[0].x) + '</i></span><br>';
					for (var i = 0; i < this.points.length; i++) {
						var point = this.points[i];
						if (i !== 0) { result += '<br>'; }
						result += '<span style="color: ' + point.series.options.color + ';"><b>'+ point.series.name + '</b>: ' +
							Highcharts.numberFormat(point.y, 2) + '</span>';
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
			series: [
				{ yAxis: 0, name: 'Data loading...', type: tlGraphType, color: inputColor, data: [], marker: { enabled: tlEnableMarkers, radius: 3 }},
				{ yAxis: 1, name: 'Data loading...', type: tlGraphType, color: outputColor, data: [], marker: { enabled: tlEnableMarkers, radius: 3 }}
			],
			credits: {
				enabled: false
			}
		});
	};

	var initScatterplotChart = function()
	{
        Highcharts.setOptions({
            colors: ['#1851CE', '#C61800', '#31B639', '#FFCF00']
        });

		scatterplotChart = new Highcharts.Chart({
			chart: { renderTo: 'graph-scatterplot', type: 'scatter', zoomType: 'xy'},
			title: { text: 'Data loading...' },
			subtitle: { text: 'from Quantimodo.com' },
			xAxis: {
				title: { text: 'Data loading...', style: { color: outputColor } },
				labels: {formatter: function() { return outputData.unit === 'unknown' ? '' : this.value; }, style: { color: outputColor } }
			},
			yAxis: {
				title: { text: 'Data loading...', style: { color: inputColor } },
				labels: {useHTML: true, formatter: function() { return inputData.unit === 'unknown' ? '' : this.value + '&nbsp;'; }, style: { color: inputColor } }
			},
			legend: { enabled: false },
			plotOptions: {
				scatter: {
					marker: { radius: 5, states: { hover: { enabled: true, lineColor: mixedColor } } },
					states: { hover: { marker: { enabled: false } } }
				}
			},
			tooltip: {
				formatter: function() {
					return '<b>' + Highcharts.dateFormat('%Y %b %d', this.point.time) + '</b><br>' +
						'<span style="color: ' + inputColor + ';">' + Highcharts.numberFormat(this.point.y, 2) + inputData.unit + '</span> with ' +
						'<span style="color: ' + outputColor + ';">' + Highcharts.numberFormat(this.point.x, 2) + outputData.unit + '</span>';
				},
				useHTML: true
			},
			series: [
				{ type: 'line', color: linearRegressionColor, enableMouseTracking: false, visible: spShowLinearRegression, marker: { enabled: false }, data: [] },
				{ name: 'Data loading...', color: mixedColor, data: [] }
			],
			credits: {
				enabled: false
			}
		});
	};

	var initCorrelationGauge = function()
	{
        Highcharts.setOptions({
            colors: ['#1851CE', '#C61800', '#31B639', '#FFCF00']
        });

		correlationGauge = new Highcharts.Chart({
			chart: {
				renderTo: 'gauge-correlation',
				type: 'gauge',
				width: 255
			},
			tooltip: {
				enabled: false,
			},
			title: {
				text: ''
			},
			pane: {
				startAngle: -180,
				endAngle: 0,
				background: null
			},
			yAxis: [{
				min: -1,
				max: 1,
				minorTickPosition: 'outside',
				tickPosition: 'outside',
				tickPixelInterval: 40,
				labels: {
					rotation: '0',
					distance: 25
				},
				plotBands: [{
					from: -1,
					to: 0,
					color: '#e5394a',
					innerRadius: '100%',
					outerRadius: '105%'
				},{
					from: 0,
					to: 1,
					color: '#65af5d',
					innerRadius: '100%',
					outerRadius: '105%'
				}

				],
				pane: 0,
			}],
			plotOptions: {
				gauge: {
					dataLabels: {
						enabled: false
					},
					dial: {
						radius: '100%'
					}
				}
			},
			series: [{
				data: [0],
				yAxis: 0
			}],
			credits: {
				enabled: false
			}
		});
	}

	return {
		init: function()
		{
		    retrieveSettings();
			initChartSettings();

			initTimelineChart();
			initScatterplotChart();
			initCorrelationGauge();
		},
		setInputData: setInputData,
		setOutputData: setOutputData,
	};
}();

jQuery(AnalyzeChart.init);