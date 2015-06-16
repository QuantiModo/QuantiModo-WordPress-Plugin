var refreshMeasurementsRange = function(callback)
{
	Quantimodo.getMeasurementsRange([], function(range) {
		AnalyzePage.dateRangeStart = range['lowerLimit'];
		AnalyzePage.dateRangeEnd = range['upperLimit'];

		if (callback) {
			callback();
		}
	});
};

var refreshUnits = function(callback)
{   
	Quantimodo.getUnits({}, function(units)
	{
		jQuery.each(units, function(_, unit)
		{
			var category = AnalyzePage.quantimodoUnits[unit.category];
			if (category === undefined)
			{
				AnalyzePage.quantimodoUnits[unit.category] = [unit];
			}
			else
			{
				category.push(unit);
			}
		});
		jQuery.each(Object.keys(AnalyzePage.quantimodoUnits), function(_, category)
		{
			AnalyzePage.quantimodoUnits[category] = AnalyzePage.quantimodoUnits[category].sort();
		});

		if (callback) 
		{
			callback();
		}
	});
};

var refreshVariables = function(variables, callback)
{
	Quantimodo.getVariables({}, function(variables)
	{
		var storedLastCauseVariableName = window.localStorage['lastCauseVariableName'],
			storedLastEffectVariableName = window.localStorage['lastEffectVariableName'];

		AnalyzePage.quantimodoVariables = {};
		jQuery.each(variables, function(_, variable)
		{
			if(variable.originalName == storedLastCauseVariableName)
			{
				AnalyzePage.lastCauseVariable = variable;
			}
			else if(variable.originalName == storedLastEffectVariableName)
			{
				AnalyzePage.lastEffectVariable = variable;
			}

			var category = AnalyzePage.quantimodoVariables[variable.category];
			if (category === undefined)
			{
				AnalyzePage.quantimodoVariables[variable.category] = [variable];
			}
			else
			{
				category.push(variable);
			}
		});
		jQuery.each(Object.keys(AnalyzePage.quantimodoVariables), function(_, category)
		{
			AnalyzePage.quantimodoVariables[category] = AnalyzePage.quantimodoVariables[category].sort(function(a, b)
			{
				return a.name.toLowerCase().localeCompare(b.name.toLowerCase());
			});
		});

		if (callback) 
		{
			callback();
		}
	});
};

var refreshInputData = function()
{
	var variable = AnalyzePage.getCauseVariable();
	if (variable == null)
	{
		return;
	}
	Quantimodo.getMeasurements({
		'variableName': variable.originalName,
		'startTime': AnalyzePage.getStartTime(),
		'endTime': AnalyzePage.getEndTime(),
		'groupingWidth': AnalyzePage.getPeriod(),
		'groupingTimezone': AnalyzePage.getTimezone()
	}, function(measurements) {
		AnalyzePage.causeMeasurements = measurements;
		AnalyzeChart.setInputData(variable, measurements);
	});
};

var refreshOutputData = function()
{
	var variable = AnalyzePage.getEffectVariable();
	Quantimodo.getMeasurements({
		'variableName': variable.originalName,
		'startTime': AnalyzePage.getStartTime(),
		'endTime': AnalyzePage.getEndTime(),
		'groupingWidth': AnalyzePage.getPeriod(),
		'groupingTimezone': AnalyzePage.getTimezone()
	}, function(measurements) {
		AnalyzePage.effectMeasurements = measurements;
		AnalyzeChart.setOutputData(variable, measurements); 
	});
};


var refreshData = function()
{             
	for (var i = 0; i < AnalyzePage.selectedVariables.length; i++)
	{
		var variable = AnalyzePage.selectedVariables[i];
                var filters = {
			'variableName': variable.originalName,
			'startTime': AnalyzePage.dateRangeStart,
			'endTime': AnalyzePage.dateRangeEnd,
			'groupingWidth': AnalyzePage.getPeriod(),
			'groupingTimezone': AnalyzePage.getTimezone()
		}
		if(variable.source != null && variable.source.length != 0)
		{
			filters.source = variable.source;
		}	
		if(variable.color == null)
		{
			variable.color = AnalyzePage.getRandomColor();
		}
		Quantimodo.getMeasurements(filters,
		function(vari)
		{
			return function(measurements)
			{
				AnalyzeChart.addData(vari, measurements);
			}
		}(variable));
	}
};