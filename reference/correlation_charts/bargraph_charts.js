AnalyzeChart = function() {
    var inputColor = '#5DA5DA', outputColor = '#F15854';

	var inputData = { variableName: 'Data loading...', unit: 'Data loading...', timeSeries: [], interpolant: function() { return 0; } };
	var outputData = { variableName: 'Data loading...', unit: 'Data loading...', timeSeries: [], interpolant: function() { return 0; } };
	var inputVariable, outputVariable;

	var tlEnableMarkers;
	var tlEnableHorizontalGuides;


	var retrieveSettings = function()
	{
		if (typeof(Storage)!=="undefined")
		{
			tlEnableMarkers = 			(localStorage["tlEnableMarkers"] || "true") == "true" ? true : false;					// On by default
			tlEnableHorizontalGuides = 	(localStorage["tlEnableHorizontalGuides"] || "false") == "true" ? true : false;			// Off by default

		}
	}

	var setSettings = function(newSettings)
	{



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


		jQuery('input[name=tl-enable-horizontal-guides]').attr('checked', tlEnableHorizontalGuides);
		jQuery('input[name=tl-enable-horizontal-guides]').change(function()
		{
			var settings = {
				tlEnableHorizontalGuides : jQuery(this).is(":checked")
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


		var cause, effect;
		var inputIsCause = (jQuery('#selectOutputAsType').val() == 'effect');

		if (inputIsCause) {
			cause = inputData;
                        causeColor = inputColor;
			effect = outputData;
                        effectColor = outputColor;
		} else {
			cause = outputData;
                        causeColor = outputColor;
			effect = inputData;
                        effectColor = inputColor;
		}

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






	return {
		init: function()
		{
			retrieveSettings();
			initChartSettings();

		},
		setInputData: setInputData,
		setOutputData: setOutputData,
	};
}();

jQuery(AnalyzeChart.init);