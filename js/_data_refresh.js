var refreshMeasurementsRange = function (callback) {
    jQuery('#please-wait').show();
    Quantimodo.getMeasurementsRange([], function (range) {
        AnalyzePage.dateRangeStart = range['lowerLimit'];
        AnalyzePage.dateRangeEnd = range['upperLimit'];
        jQuery('#please-wait').hide();
        if (callback) {
            callback();
        }
    });
};

var refreshUnits = function (callback) {
    jQuery('#please-wait').show();
    Quantimodo.getUnits({}, function (units) {
        jQuery.each(units, function (_, unit) {
            var category = AnalyzePage.quantimodoUnits[unit.category];
            if (category === undefined) {
                AnalyzePage.quantimodoUnits[unit.category] = [unit];
            }
            else {
                category.push(unit);
            }
        });
        jQuery.each(Object.keys(AnalyzePage.quantimodoUnits), function (_, category) {
            AnalyzePage.quantimodoUnits[category] = AnalyzePage.quantimodoUnits[category].sort();
        });
        jQuery('#please-wait').hide();
        if (callback) {
            callback();
        }
    });
};

var refreshVariables = function (variables, callback) {
    jQuery('#please-wait').show();
    Quantimodo.getVariables({}, function (variables) {
        var storedLastInputVariableName = window.localStorage['lastInputVariableName'],
            storedLastOutputVariableName = window.localStorage['lastOutputVariableName'];

        AnalyzePage.quantimodoVariables = {};
        jQuery.each(variables, function (_, variable) {
            if (variable.originalName == storedLastInputVariableName) {
                AnalyzePage.lastInputVariable = variable;
            }
            else if (variable.originalName == storedLastOutputVariableName) {
                AnalyzePage.lastOutputVariable = variable;
            }

            var category = AnalyzePage.quantimodoVariables[variable.category];
            if (category === undefined) {
                AnalyzePage.quantimodoVariables[variable.category] = [variable];
            }
            else {
                category.push(variable);
            }
        });
        jQuery.each(Object.keys(AnalyzePage.quantimodoVariables), function (_, category) {
            AnalyzePage.quantimodoVariables[category] = AnalyzePage.quantimodoVariables[category].sort(function (a, b) {
                return a.name.toLowerCase().localeCompare(b.name.toLowerCase());
            });
        });
        jQuery('#please-wait').hide();
        if (callback) {
            callback();
        }
    });
};

var refreshInputData = function () {

    if (AnalyzePage.selectedInputVariableName) {
        Quantimodo.getVariableByName(AnalyzePage.selectedInputVariableName, function (variable) {

            jQuery('#please-wait').show();
            Quantimodo.getDailyMeasurements({
                'variableName': variable.originalName,
                'startTime': AnalyzePage.getStartTime(),
                'endTime': AnalyzePage.getEndTime(),
                //'groupingWidth': AnalyzePage.getPeriod(),
                'groupingTimezone': AnalyzePage.getTimezone()
            }, function (measurements) {
                jQuery('#please-wait').hide();
                AnalyzePage.inputMeasurements = measurements;
                AnalyzeChart.setInputData(variable, measurements);
            });
        });
    }

};

var refreshOutputData = function () {
    AnalyzePage.getOutputVariable(function (variable) {
        jQuery('#please-wait').show();
        Quantimodo.getDailyMeasurements({
            'variableName': variable.originalName,
            'startTime': AnalyzePage.getStartTime(),
            'endTime': AnalyzePage.getEndTime(),
            //'groupingWidth': AnalyzePage.getPeriod(),
            'groupingTimezone': AnalyzePage.getTimezone()
        }, function (measurements) {
            jQuery('#please-wait').hide();
            AnalyzePage.outputMeasurements = measurements;
            AnalyzeChart.setOutputData(variable, measurements);
        });
    });

};

var refreshData = function () {
    for (var i = 0; i < AnalyzePage.selectedVariables.length; i++) {
        var variable = AnalyzePage.selectedVariables[i];
        var filters = {
            'variableName': variable.originalName,
            'startTime': AnalyzePage.dateRangeStart,
            'endTime': AnalyzePage.dateRangeEnd,
            //'groupingWidth': AnalyzePage.getPeriod(),
            'groupingTimezone': AnalyzePage.getTimezone()
        }
        if (variable.source != null && variable.source.length != 0) {
            filters.source = variable.source;
        }
        if (variable.color == null) {
            variable.color = AnalyzePage.getRandomColor();
        }
        Quantimodo.getDailyMeasurements(filters,
            function (vari) {
                return function (measurements) {
                    AnalyzeChart.addData(vari, measurements);
                }
            }(variable));
    }
};

var refreshVariableCategories = function (callback) {
    jQuery('#please-wait').show();
    Quantimodo.getVariableCategories(null, function (categories) {
        jQuery('#please-wait').hide();
        AnalyzePage.variableCategories = categories;

        if (callback) {
            callback();
        }
    });
}