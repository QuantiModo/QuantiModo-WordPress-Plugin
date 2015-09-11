var AnalyzePage = function () {
    var timezone = jstz.determine().name();

    var selectedInputVariableName;		// This holds the variable that was last selected in the bargraph

    var quantimodoUnits = {};
    var quantimodoVariables = {};
    var causeMeasurements;
    var effectMeasurements;

    var dateRangeStart, dateRangeEnd;

    var dateSelectorVisible;
    var inputSelectorVisible;
    var outputSelectorVisible;
    var correlationGaugeVisible = true;
    var scatterplotVisible = true;

    var initLoginDialog = function () {
        jQuery(document).on('lwa_login', function (event, data, form) {
            if (data.result === true) {
                refreshMeasurementsRange(function () {
                    refreshVariables([], function () {
                        categoryListUpdated();
                        outputCategoryUpdated();
                        getBargraph();
                        refreshInputData();
                    });
                });
                refreshUnits(function () {
                    unitListUpdated();
                });

                jQuery("#login-dialog-background").addClass('transitions').css({'opacity': 0});
                jQuery("#login-dialog").addClass('transitions').css({'opacity': 0});

                setTimeout(function () {
                    jQuery("#login-dialog-background").css({'display': 'none'});
                    jQuery("#login-dialog").css({'display': 'none'});
                }, 500);
            }
        });

    };

    /* Initialize left menubar */
    var initAccordion = function () {
        jQuery('.questionMark').tooltip();

        jQuery("#button-input-varsettings").on('click', function () {
            variableSettings.show(AnalyzePage.lastInputVariable);
        });
        jQuery("#button-output-varsettings").on('click', function () {
            variableSettings.show(AnalyzePage.lastOutputVariable);
        });
    };

    var initVariableSelectors = function () {
        jQuery('#selectOutputCategory').change(outputCategoryUpdated);
        jQuery('#selectOutputVariable').change(function () {
            outputVariableUpdated();
            getBargraph();
        });
        jQuery('#selectOutputAsType').change(function () {
            getBargraph(true);
        });
    };

    var lastStartTime = null;
    var startDateUpdated = function () {
        var newStartTime = AnalyzePage.getStartTime();
        if (newStartTime !== lastStartTime) {
            lastStartTime = newStartTime;
            refreshInputData();
            refreshOutputData();
        }
    };

    var lastEndTime = null;
    var endDateUpdated = function () {
        var newEndTime = AnalyzePage.getEndTime();
        if (newEndTime !== lastEndTime) {
            lastEndTime = newEndTime;
            refreshInputData();
            refreshOutputData();
        }
    };

    var bothDatesUpdated = function () {
        var newStartTime = AnalyzePage.getStartTime();
        var newEndTime = AnalyzePage.getEndTime();
        if ((newStartTime !== lastStartTime) || (newEndTime !== lastEndTime)) {
            lastStartTime = newStartTime;
            lastEndTime = newEndTime;
            refreshInputData();
            refreshOutputData();
        }
    };

    var initDateRangeSelector = function () {
        jQuery("#accordion-content-rangepickers").buttonset();
        jQuery("#accordion-content-rangepickers :radio").click(periodUpdated);
    };

    var lastPeriod = 1;
    var periodUpdated = function () {
        var newPeriod = AnalyzePage.getPeriod();
        if (newPeriod !== lastPeriod) {
            lastPeriod = newPeriod;
            refreshInputData();
            refreshOutputData();
        }
    };

    var categoryListUpdated = function () {

        jQuery('#selectOutputCategory').empty();
        jQuery('#selectVariableCategorySetting').empty();
        jQuery.each(Object.keys(AnalyzePage.quantimodoVariables).sort(function (a, b) {
            return a.toLowerCase().localeCompare(b.toLowerCase());
        }), function (_, category) {
            //output category set values
            if (AnalyzePage.lastOutputVariable != null && AnalyzePage.lastOutputVariable.category == category) {
                jQuery('#selectOutputCategory').append(jQuery('<option/>').attr('selected', 'selected').attr('value', category).text(category));
            }
            else {
                jQuery('#selectOutputCategory').append(jQuery('<option/>').attr('value', category).text(category));
            }

            //jQuery('#selectOutputCategory').append(jQuery('<option/>').attr('value', category).text(category));
            jQuery('#selectVariableCategorySetting').append(jQuery('<option/>').attr('value', category).text(category));
        });
    };

    var unitListUpdated = function () {
    };

    var lastInputVariable = null;
    var inputVariableUpdated = function () {
        var newInputVariable = AnalyzePage.getInputVariable();
        if (newInputVariable !== AnalyzePage.lastInputVariable) {
            refreshInputData();
            AnalyzePage.lastInputVariable = newInputVariable;
            saveSetting('lastInputVariableName', AnalyzePage.lastInputVariable.originalName);
            AnalyzePage.selectedInputVariableName = AnalyzePage.lastInputVariable.originalName;
        }
    };

    var lastOutputCategory = null;
    var outputCategoryUpdated = function () {
        var newOutputCategory = AnalyzePage.getOutputCategory();

        jQuery('#selectOutputVariable').empty();
        jQuery.each(AnalyzePage.quantimodoVariables[newOutputCategory], function (_, variable) {
            //	if (variable.name == variable.originalName)
            // 	{
            if (AnalyzePage.lastOutputVariable != null && AnalyzePage.lastOutputVariable.originalName == variable.originalName) {
                jQuery('#selectOutputVariable').append(jQuery('<option/>').attr('selected', 'selected').attr('value', variable.originalName).text(variable.name));
            }
            else {
                jQuery('#selectOutputVariable').append(jQuery('<option/>').attr('value', variable.originalName).text(variable.name));
            }
            //	}

            // if (variable.name == variable.originalName)
            // {
            // 	jQuery('#selectOutputVariable').append(jQuery('<option/>').attr('value', variable.name).text(variable.name));
            // }
            // else
            // {
            // 	jQuery('#selectOutputVariable').append(jQuery('<option/>').attr('value', variable.name).text(variable.name + " (" + variable.originalName + ")"));
            // }

        });
        lastOutputCategory = newOutputCategory;
        outputVariableUpdated();
        jQuery("#selectOutputVariable").change();
        refreshOutputData();

    };

    var lastOutputVariable = null;
    var outputVariableUpdated = function () {
        var newOutputVariable = AnalyzePage.getOutputVariable();
        if (newOutputVariable !== AnalyzePage.lastOutputVariable) {
            refreshOutputData();
            AnalyzePage.lastOutputVariable = newOutputVariable;
            saveSetting('lastOutputVariableName', AnalyzePage.lastOutputVariable.originalName);
        }
    };

    var retrieveSettings = function () {
        if (typeof(Storage) !== "undefined") {
            dateSelectorVisible = (window.localStorage["dateSelectorVisible"] || "true") == "true" ? true : false;
            inputSelectorVisible = (window.localStorage["inputSelectorVisible"] || "true") == "true" ? true : false;
            outputSelectorVisible = (window.localStorage["outputSelectorVisible"] || "true") == "true" ? true : false;
            AnalyzePage.selectedInputVariableName = window.localStorage["lastInputVariableName"];
        }
    };

    var saveSetting = function (setting, value) {
        if (typeof(Storage) !== "undefined") {
            window.localStorage[setting] = value;
        }
    };

    var initSharing = function () {
        jQuery("#share-dialog #button-doshare").click(function () {
            jQuery("#share-dialog").css({'display': 'block', 'opacity': 0.8});
            showLoadingOverlay("#share-dialog");

            share(function (id) {
                jQuery("#share-dialog").css({'display': 'block', 'opacity': 1});
                hideLoadingOverlay("#share-dialog");

                var url = location.href;						// Get current URL
                if (url.substr(-1) != '/')  url = url + '/';	// Append trailing slash if not exists
                jQuery("#share-dialog").append(url + 'shared/?data=' + id);	// Return share URL
            });
        });
        jQuery("#share-dialog #button-cancelshare").click(function () {
            jQuery("#share-dialog-background").css({'opacity': 0});
            jQuery("#share-dialog").css({'opacity': 0});

            setTimeout(function () {
                jQuery("#share-dialog-background").css({'display': 'none'});
                jQuery("#share-dialog").css({'display': 'none'});
            }, 500);
        });

        jQuery('#shareTimeline').click(function () {
            showShareDialog();
        });
        jQuery('#shareScatterplot').click(function () {
            showShareDialog();
        });
        jQuery('#shareCorrelationGauge').click(function () {
            showShareDialog();
        });
    }

    var showShareDialog = function () {
        jQuery("#share-dialog-background").css({'display': 'block', 'opacity': 0.5});
        jQuery("#share-dialog").css({'display': 'block', 'opacity': 1});
    }

    var selectVariableName = function (variableName) {
        var variable = AnalyzePage.getVariableFromName(variableName);
        if (!variableName || variableName == '') {
            jQuery("#addmeasurement-variable-name").val('');
            jQuery("#addmeasurement-variable-original-name").val('');
            jQuery("#addmeasurement-variable-unit").val('');
            jQuery("#addmeasurement-variable-value").val('');
            jQuery("#addmeasurement-variable-datetime").val('');
            return false;
        } else {
            if (variable)
                jQuery("#addmeasurement-variable-original-name").val(variable.originalName);
        }
        Quantimodo.getDailyMeasurements(
            {
                'variableName': variableName,
                'startTime': AnalyzePage.getStartTime(),
                'endTime': AnalyzePage.getEndTime(),
                'groupingWidth': AnalyzePage.getPeriod(),
                'groupingTimezone': AnalyzePage.getTimezone()
            }, function (measurements) {
                var meanValue = 0;
                var meanCount = 0;

                var meanByUnits = {};
                var recentUnitAbbr;
                jQuery.each(measurements, function (_, measurement) {
                    var meanValue = meanByUnits[measurement.unit];
                    recentUnitAbbr = measurement.unit;
                    if (meanValue === undefined) {
                        meanByUnits[measurement.unit] = {unit: measurement.unit, sum: measurement.value, count: 0};
                    }
                    else {
                        meanValue.sum += measurement.value;
                        meanValue.count++;
                    }
                });

                var varunits = [];
                Quantimodo.getUnitsForVariable({variable: variableName}, function (units) {
                    jQuery.each(units, function (_, unit) {
                        varunits.push({label: unit.name, category: "Used"});

                    });

                    jQuery.each(Object.keys(AnalyzePage.quantimodoUnits), function (_, category) {
                        var units = AnalyzePage.quantimodoUnits[category];
                        for (var n = 0; n < units.length; n++) {
                            var currentUnit = units[n];
                            if (currentUnit.abbreviatedName == variable.unit) {
                                selectedUnitToAdd = currentUnit;
                                jQuery('#addmeasurement-variable-unit').append(jQuery('<option/>').attr('selected', 'selected').attr('value', currentUnit.abbreviatedName).text(currentUnit.name));
                            }
                            else
                                jQuery('#addmeasurement-variable-unit').append(jQuery('<option/>').attr('value', currentUnit.abbreviatedName).text(currentUnit.name));
                        }
                    });

                    /*
                     jQuery("#addmeasurement-variable-unit").catcomplete({
                     source: varunits,
                     select: function (event, ui) {
                     //AnalyzePage.quantimodoUnits
                     jQuery("#addmeasurement-variable-value").val('');
                     }
                     });
                     */
                    if (selectedUnitToAdd && selectedUnitToAdd.name != '') {
                        //jQuery("#addmeasurement-variable-unit").val(selectedUnitToAdd.name);
                        var meanValue = meanByUnits[selectedUnitToAdd.abbreviatedName];
                        if (meanValue && meanValue.count > 0) {
                            jQuery("#addmeasurement-variable-value").val(Math.round(meanValue.sum / meanValue.count * 100) / 100);
                        }
                    }
                });
            });
    }


    var initAddMeasurement = function () {
        jQuery("#addmeasurement-dialog #button-add").on('click', function () {
            var name = jQuery("#addmeasurement-variable-name").val();
            var originalName = jQuery("#addmeasurement-variable-original-name").val();
            var unit = jQuery("#addmeasurement-variable-unit").val();
            var value = jQuery("#addmeasurement-variable-value").val();
            var datetime = jQuery("#addmeasurement-variable-datetime").val();
            var values = [];
            var variable = AnalyzePage.getVariableFromOriginalName(originalName);

            if (name == '' || unit == '' || value == '' || datetime == '') {
                alert('Please fill in the values.');
                return;
            }

            var measurements = [{
                timestamp: Math.floor(new Date(datetime).getTime() / 1000),
                value: value
            }];
            var measurementset = {
                measurements: measurements,
                name: name,
                source: 'QuantiMo.Do',
                category: variable.category,
                combinationOperation: variable.combinationOperation,
                unit: selectedUnitToAdd.abbreviatedName
            };

            Quantimodo.postMeasurementsV2(
                new Array(measurementset),
                function (response) {
                    if (response.success == true) {
                        alert('Measurement data is stored successfully.');
                        jQuery("#addmeasurement-variable-name").val('');
                        jQuery("#addmeasurement-variable-original-name").val('');
                        jQuery("#addmeasurement-variable-unit").val('');
                        jQuery("#addmeasurement-variable-value").val('');
                        jQuery("#addmeasurement-variable-datetime").val('');
                    }
                    else {
                        alert('Measurement data is not stored. error code: ' + response.error);
                    }
                }
            );
        });

        jQuery("#addmeasurement-dialog #button-close").on('click', function () {
            jQuery("#addmeasurement-dialog-background").css({'opacity': 0});
            jQuery("#addmeasurement-dialog").css({'opacity': 0});

            setTimeout(function () {
                jQuery("#addmeasurement-dialog-background").css({'display': 'none'});
                jQuery("#addmeasurement-dialog").css({'display': 'none'});
            }, 500);
        });

        jQuery('div.icon-plus-sign.icon-2x').on('click', function () {
            AnalyzePage.showAddMeasurementDialog();
        });
    }

    var share = function (onDoneListener) {
        var shareObject = {
            'type': 'correlate',
            'inputVariable': AnalyzePage.lastInputVariable.originalName,
            'outputVariable': AnalyzePage.lastOutputVariable.originalName,
            'startTime': AnalyzePage.getStartTime(),
            'endTime': AnalyzePage.getEndTime(),
            'groupingWidth': AnalyzePage.getPeriod(),
            'groupingTimezone': AnalyzePage.getTimezone()
        };
        Quantimodo.postCorrelateShare(shareObject, function (response) {
            onDoneListener(response['id']);
        });
    };

    return {
        lastInputVariable: lastInputVariable,
        lastOutputVariable: lastOutputVariable,
        quantimodoUnits: quantimodoUnits,
        quantimodoVariables: quantimodoVariables,
        causeMeasurements: causeMeasurements,
        effectMeasurements: effectMeasurements,

        selectedInputVariableName: selectedInputVariableName,

        dateRangeStart: dateRangeStart,
        dateRangeEnd: dateRangeEnd,

        getTimezone: function () {
            return timezone;
        },
        getStartTime: function () {
            return AnalyzePage.dateRangeStart;
        },
        getEndTime: function () {
            return AnalyzePage.dateRangeEnd;
        },
        getOutputCategory: function () {
            return jQuery('#selectOutputCategory :selected').val();
        },
        getVariableFromName: function (variableName) {
            var selectedVariable;
            var categories = Object.keys(AnalyzePage.quantimodoVariables);
            var currentCategory, currentVariable;
            for (var i = 0; i < categories.length; i++) {
                currentCategory = AnalyzePage.quantimodoVariables[categories[i]];
                for (var n = 0; n < currentCategory.length; n++) {
                    currentVariable = currentCategory[n];
                    if (currentVariable.name == variableName) {
                        selectedVariable = jQuery.extend({}, currentVariable);	// Create a copy so that modifications won't pollute the original set
                        return selectedVariable;
                    }
                }
            }
            return null;
        },
        getVariableFromOriginalName: function (originalVariableName) {
            var selectedVariable;
            var categories = Object.keys(AnalyzePage.quantimodoVariables);
            var currentCategory, currentVariable;
            for (var i = 0; i < categories.length; i++) {
                currentCategory = AnalyzePage.quantimodoVariables[categories[i]];
                for (var n = 0; n < currentCategory.length; n++) {
                    currentVariable = currentCategory[n];
                    if (currentVariable.originalName == originalVariableName) {
                        selectedVariable = jQuery.extend({}, currentVariable);	// Create a copy so that modifications won't pollute the original set
                        return selectedVariable;
                    }
                }
            }
            return null;
        },
        getInputVariable: function () {
            return AnalyzePage.getVariableFromOriginalName(AnalyzePage.selectedInputVariableName);
        },
        getOutputVariable: function () {
            var categoryName = jQuery('#selectOutputCategory :selected').val();
            var variableName = jQuery('#selectOutputVariable :selected').val();
            var wantedVariable;
            jQuery.each(AnalyzePage.quantimodoVariables[categoryName], function (_, variable) {
                if (variable.originalName == variableName) {
                    wantedVariable = variable;
                    return;
                }
            });
            return wantedVariable;
        },
        setInputVariable: function (originalVariableName) {
            AnalyzePage.selectedInputVariableName = originalVariableName;
            inputVariableUpdated();
        },
        getPeriod: function () {
            switch (jQuery('#accordion-content-rangepickers :radio:checked + label').text()) {
                case 'Second':
                    return 1;
                case 'Minute':
                    return 60;
                case 'Hour':
                    return 3600;
                case 'Day':
                    return 86400;
                case 'Week':
                    return 604800;
                case 'Month':
                    return 2628000;
            }
        },

        init: function () {
            retrieveSettings();
            if (typeof accessToken == "undefined" || !accessToken) {
                window.location.href = "?connect=quantimodo";
            } else {
                refreshMeasurementsRange(function () {
                    refreshVariables([], function () {
                        categoryListUpdated();
                        outputCategoryUpdated();
                        getBargraph();
                        refreshInputData();
                    });
                });
                refreshUnits(function () {
                    unitListUpdated();
                });
            }
            initDateRangeSelector();
            initVariableSelectors();
            variableSettings.init({
                saveCallback: function () {
                    refreshVariables([], function () {
                        categoryListUpdated();
                        outputCategoryUpdated();
                        getBargraph();
                        refreshInputData();
                    });	//TODO replace this with something that updates the variables locally, since this triggers
                }
            });
            initLoginDialog();
            initSharing();
            initAddMeasurement();
            initDeleteMeasurements();
            initAccordion();
        },
        hideScatterplot: function () {
            if (scatterplotVisible) {
                toggleElement('#scatterplot-graph');
                scatterplotVisible = false;
            }
        },
        showScatterplot: function () {
            if (!scatterplotVisible) {
                toggleElement('#scatterplot-graph');
                scatterplotVisible = true
            }
        },
        hideCorrelationGauge: function () {
            if (correlationGaugeVisible) {
                toggleElement('#correlation-gauge');
                correlationGaugeVisible = false;
            }
        },
        showCorrelationGauge: function () {
            if (!correlationGaugeVisible) {
                toggleElement('#correlation-gauge');
                correlationGaugeVisible = true
            }
        },
        showSettingsForVariableFromGraph: function (variableName) {
            variableSettings.show(AnalyzePage.getVariableFromOriginalName(variableName));
        },
        getVariableSelectedFromBarGraph: function (variableName) {
            return variableSelectedFromBarGraph(variableName);
        },
        showAddMeasurementDialog: function (variableName) {
            jQuery.widget("custom.catcomplete", jQuery.ui.autocomplete, {
                _renderMenu: function (ul, items) {
                    var that = this,
                        currentCategory = "";
                    jQuery.each(items, function (index, item) {
                        if (item.category != currentCategory) {
                            ul.append("<li class='ui-autocomplete-category'>" + item.category + "</li>");
                            currentCategory = item.category;
                        }
                        that._renderItemData(ul, item);
                    });
                }
            });

            var varnames = [];
            jQuery.each(Object.keys(AnalyzePage.quantimodoVariables).sort(function (a, b) {
                return a.toLowerCase().localeCompare(b.toLowerCase());
            }), function (_, category) {
                var variables = AnalyzePage.quantimodoVariables[category];
                for (var n = 0; n < variables.length; n++) {
                    var currentVariable = variables[n];
                    varnames.push({label: currentVariable.name, category: currentVariable.category});
                }
            });

            jQuery("#addmeasurement-variable-name").catcomplete({
                source: varnames,
                select: function (event, ui) {
                    jQuery("#addmeasurement-variable-unit").val('');
                    jQuery("#addmeasurement-variable-value").val('');

                    selectVariableName(ui.item.label);
                }
            });

            if (variableName != null && variableName !== undefined) {
                variableName = unescape(variableName);
                var variable = AnalyzePage.getVariableFromOriginalName(variableName);
                jQuery("#addmeasurement-variable-name").val(variable.name);
                jQuery("#addmeasurement-variable-original-name").val(variable.originalName);
                selectVariableName(variable.name);
            } else {
                selectVariableName('');
            }


            jQuery("#addmeasurement-variable-datetime").datetimepicker();
            var currentTime = new Date();
            jQuery("#addmeasurement-variable-datetime").val(currentTime.getFullYear() + '-' + (currentTime.getMonth() + 1) + '-' + currentTime.getDate() + ' ' + currentTime.getHours() + ':00');

            jQuery("#addmeasurement-dialog-background").css({'display': 'block', 'opacity': 0.5});
            jQuery("#addmeasurement-dialog").css({'display': 'block', 'opacity': 1});


        }

    };
}();

jQuery(AnalyzePage.init);

function getSettingsForm(variableName) {
    AnalyzePage.showSettingsForVariableFromGraph(unescape(variableName));
}

function setInputVariable(variableName) {
    var variable = AnalyzePage.getVariableFromOriginalName(unescape(variableName));
    if (variable) {
        AnalyzePage.setInputVariable(variable.originalName);
    }
}


var bargraph;
var bargraphData;
var sortedByCorrelation = new Array();
var sortedByCausality = new Array();
var dataArray = new Array();
var bargraphDataAsEffect;
var bargraphDataAsCause;
var selectedBargraphRowIndex = null;
var overBargraphRowIndex = null;
var leaveBargraphRowIndex = null;
var settingsIconsOnBargraphRow = null;
var selectedVarNameOnBargraphRow = null;
var overVarNameOnBargraphRow = null;
var leaveVarNameOnBargraphRow = null;

function resetHighlightStuff() {
    selectedBargraphRowIndex = null;
    selectedBargraphRowIndex = null;
    overBargraphRowIndex = null;
    leaveBargraphRowIndex = null;
    settingsIconsOnBargraphRow = null;
    selectedVarNameOnBargraphRow = null;
    overVarNameOnBargraphRow = null;
    leaveVarNameOnBargraphRow = null;
}

function jsonCallback(data) {

    if (data.length == 0) {
        jQuery('.no-data').show();
        jQuery('#graph-bar').hide();
        jQuery('.barloading').hide();
        bargraphData = null;
    }
    else {
        jQuery('.no-data').hide();
        jQuery('.barloading').hide();
        jQuery('#graph-bar').show();

        sortedByCorrelation = new Array();
        sortedByCausality = new Array();

        var valAs = jQuery('#selectOutputAsType').val();

        for (var i in data) {
            dataArray[i] = ((valAs === 'cause') ? {
                'originalName': data[i].originalEffect,
                'name': data[i].effect
            } : {'originalName': data[i].originalCause, 'name': data[i].cause});
            var value = data[i].correlationCoefficient;
            var label = data[i].cause;
            var category = data[i].causeCategory;
            var causalityFactor = data[i].causalityFactor;
            var durationOfAction = data[i].durationOfAction;
            var effect = data[i].effect;
            var effectSize = data[i].effectSize;
            var numberOfPairs = data[i].numberOfPairs;
            var onsetDelay = data[i].onsetDelay;
            var reverseCorrelation = data[i].reverseCorrelation;
            var statisticalSignificance = data[i].statisticalSignificance;


            var color = 'blue';

            if (value > 0) {
                color = 'red';
            }

            sortedByCorrelation[i] = {
                y: value,
                color: color,
                name: 'Correlation',
                label: label,
                category: category,
                composition: {
                    //'Causality Factor': causalityFactor,
                    'Duration Of Action': durationOfAction,
                    //'Effect': effect,
                    //'Effect Size': effectSize,
                    'Number Of Samples': numberOfPairs,
                    'Onset Delay': onsetDelay,
                    //'Reverse Correlation': reverseCorrelation,
                    //'Statistical Significance': statisticalSignificance,
                },
            };//end data

            sortedByCausality[i] = {
                y: causalityFactor,
                color: color,
                name: 'Causality Factor',
                label: label,
                category: category,
                composition: {
                    'Correlation Cofficient': value,
                    'Duration Of Action': durationOfAction,
                    //'Effect': effect,
                    //'Effect Size': effectSize,
                    'Number Of Samples': numberOfPairs,
                    'Onset Delay': onsetDelay,
                    //'Reverse Correlation': reverseCorrelation,
                    //'Statistical Significance': statisticalSignificance,
                },
            };//end data

        }//end for


        constructBarGraph(data.length, sortedByCorrelation, dataArray);

        bargraphData = data;

        jQuery("#minimumNumberOfSamples").keypress(function (event) {
            var code = (event.keyCode ? event.keyCode : event.which);
            if (code == 13) {
                event.stopImmediatePropagation();
                var numberOfPairs = parseInt(jQuery("#minimumNumberOfSamples").val());
                if (isNaN(numberOfPairs)) {
                    alert("Invalid filling value, must be a number.");
                    return;
                }
                filterByNumberOfPairs(numberOfPairs);
                if (jQuery("#gauge-timeline-settingsicon").hasClass("dropdown-open")) {
                    jQuery("#gauge-timeline-settingsicon").removeClass("dropdown-open");
                    jQuery("#dropdown-barchart-settings").hide();
                }
            }
        });
    }
}

function getBargraph(bUseCache) {
    jQuery('#graph-bar').bind('mouseenter', function (event) {
        jQuery('#graph-bar').css('cursor', 'pointer');
    });
    jQuery('#graph-bar').bind('mouseleave', function (event) {
        jQuery('#graph-bar').css('cursor', 'auto');
        if (overBargraphRowIndex != null && overBargraphRowIndex != selectedBargraphRowIndex) {
            overBargraphRowIndex.attr('fill', '#FFF');
            overVarNameOnBargraphRow.attr('style', '');
        }
        if (settingsIconsOnBargraphRow != null) {
            settingsIconsOnBargraphRow.attr('style', '');
        }
    });

    jQuery('#graph-bar').hide();
    jQuery('.barloading').show();
    var val = jQuery('#selectOutputVariable').val();
    var url = Quantimodo.url + 'correlations';

    var valAs = jQuery('#selectOutputAsType').val();
    var jsonParam = {effect: val};
    if (valAs == 'cause')
        jsonParam = {cause: val};

    /*	if (valAs == 'effect' &&  AnalyzePage.getVariableFromOriginalName(val).causeOnly == 1)
     {
     jQuery('.no-data').show();
     jQuery('#graph-bar').hide();
     jQuery('.barloading').hide();
     bargraphData = null;
     return;
     }*/

    if (bUseCache === undefined) {
        bUseCache = false;
        bargraphDataAsEffect = bargraphDataAsCause = undefined;
    }
    if (bUseCache == true) {
        if (valAs == 'effect' && typeof bargraphDataAsEffect !== 'undefined' && bargraphDataAsEffect.length > 0) {
            jsonCallback(bargraphDataAsEffect);
            return;
        }
        if (valAs == 'cause' && typeof bargraphDataAsCause !== 'undefined' && bargraphDataAsCause.length > 0) {
            jsonCallback(bargraphDataAsCause);
            return;
        }
    }

    /*    jQuery.get(url, jsonParam).done(function (data) {
     if (valAs == 'cause')
     bargraphDataAsCause = data;
     else
     bargraphDataAsEffect = data;
     jsonCallback(data);
     })*/
    console.debug('getBargraph API call')
    jQuery.ajax({
        data: jsonParam,
        type: 'GET',
        url: url,
        dataType: 'json',
        contentType: 'application/json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", "Bearer " + accessToken);
            if (mashapeKey) {
                xhr.setRequestHeader('X-Mashape-Key', mashapeKey);
            }
        }
    }).done(function (data) {
        if (valAs == 'cause')
            bargraphDataAsCause = data;
        else
            bargraphDataAsEffect = data;
        jsonCallback(data);
    })
}
function resetBarGraph() {
    jQuery(".no-data").hide();
    barchart = null;
    jQuery("#graph-bar").empty();
}

function constructBarGraph(count, dataOfSerie, dataSeries) {
    resetBarGraph();

    var GraphContainerHeight = (count * 40.5);
    var height = 50;
    var new_height = height + GraphContainerHeight;

    selectedVariableName = sortedByCorrelation[0].label;

    var varName = jQuery('#selectOutputVariable').val();
    var valAs = jQuery('#selectOutputAsType').val();
    var headerText = "Effect on ";
    if (valAs == 'cause')
        headerText = "Effect of ";

    var headerTextTruncated = headerText + varName;
    jQuery(jQuery("#bar-graph-header div")[0]).attr('title', headerTextTruncated);
    if (headerTextTruncated != null && headerTextTruncated.length > 32) {
        //the indexOf will return the position of the first space starting from the 10th
        var tempHeader = headerTextTruncated;
        for (var i = 30; i >= 20; i--) {
            tempHeader = headerTextTruncated.indexOf(' ', i) != -1 ? headerTextTruncated.substring(0, headerTextTruncated.indexOf(' ', i)) + "..." : headerTextTruncated;
            if (tempHeader.length < 32) {
                break;
            }
        }
        headerTextTruncated = tempHeader;
    }
    jQuery(jQuery("#bar-graph-header div")[0]).html(headerTextTruncated);

    var pBands = new Array();
    resetHighlightStuff();
    for (var i = 0; i < dataSeries.length; i++) {
        pBands[i] = {
            cursor: 'pointer',
            color: '#FFF',
            from: i - 0.5,
            to: i + 0.5,
            events: {
                click: function (e) {
                    setInputVariable(escape(this.axis.categories[this.options.from + 0.5].originalName));
                    if (selectedBargraphRowIndex != null) {
                        selectedBargraphRowIndex.attr('fill', this.options.color);
                        selectedVarNameOnBargraphRow.attr('style', '');
                    }
                    this.svgElem.attr('fill', '#29bdca');
                    selectedBargraphRowIndex = this.svgElem;
                    selectedVarNameOnBargraphRow = jQuery("div[data-row=\'" + escape(this.axis.categories[this.options.from + 0.5].originalName) + "\']");
                    selectedVarNameOnBargraphRow.attr('style', 'color:#FFF;');
                },
                mouseover: function (e) {
                    overBargraphRowIndex = this.svgElem;
                    overVarNameOnBargraphRow = jQuery("div[data-row=\'" + escape(this.axis.categories[this.options.from + 0.5].originalName) + "\']");
                    if (leaveBargraphRowIndex != null && leaveBargraphRowIndex != overBargraphRowIndex && leaveBargraphRowIndex != selectedBargraphRowIndex) {
                        leaveBargraphRowIndex.attr('fill', this.options.color);
                        leaveVarNameOnBargraphRow.attr('style', '');
                    }
                    if (leaveBargraphRowIndex != null && leaveBargraphRowIndex != overBargraphRowIndex && settingsIconsOnBargraphRow != null) {
                        settingsIconsOnBargraphRow.attr('style', '');
                    }
                    this.svgElem.attr('fill', '#29bdca');
                    overVarNameOnBargraphRow.attr('style', 'color:#FFF;');
                    settingsIconsOnBargraphRow = jQuery("div[data-row=\'" + escape(this.axis.categories[this.options.from + 0.5].originalName) + "\'] .setButton");
                    settingsIconsOnBargraphRow.attr('style', 'opacity:1;');
                    leaveBargraphRowIndex = overBargraphRowIndex;
                    leaveVarNameOnBargraphRow = overVarNameOnBargraphRow;
                }
            }
        }
    }

    Highcharts.setOptions({
        colors: ['#1851CE', '#C61800', '#31B639', '#FFCF00']
    });

    barchart = new Highcharts.Chart({
        chart: {renderTo: 'graph-bar', type: 'bar', marginLeft: 0, marginRight: 2, height: new_height},
        title: {text: name, align: 'left', x: i === 0 ? 90 : 0},
        credits: {enabled: false},
        xAxis: {
            categories: dataSeries,
            labels: {
                align: 'left',
                step: 1,
                x: 10,
                useHTML: true,
                formatter: function () {
                    return '<div class="variableInBarGraph" data-row="' + escape(this.value.originalName) + '">' +
                        '<div class="variableRowInBarGraph" onclick="highlightBargraphRow(); setInputVariable(\'' + escape(this.value.originalName) + '\');">' +
                        '<div class="variableName">' + this.value.originalName + ' </div>' +
                        '<div class="setButton icon-cog icon-large gear" onclick="event.stopPropagation(); getSettingsForm(\'' + escape(this.value.originalName) + '\');"></div>' +
                        '<div class="setButton icon-plus icon-large plus" onclick="event.stopPropagation(); AnalyzePage.showAddMeasurementDialog(\'' + escape(this.value.originalName) + '\');"></div>' +
                        '</div>' +
                        '</div>';
                },
            },
            lineColor: '#FFF',
            tickWidth: 0,
            plotBands: pBands
        },
        yAxis: {
            allowDecimals: false,
            title: {text: null},
            min: -2.50,
            max: 1.00,
            gridLineWidth: 0,
            labels: {enabled: false}
        },
        legend: {enabled: false},
        series: [{data: dataOfSerie}],
        tooltip: {
            shared: false,
            formatter: function () {
                var vax = this.series.options.data;
                //for (var i in vax) {
                var rex = vax[this.series.data.indexOf(this.point)];

                var serie = this.series;
                //	var s = '<b>' + Highcharts.dateFormat('%A, %b %e, %Y', this.x) + '</b><br>';
                var s = '<span style="color:' + serie.color + '">' + rex.name + '</span>: <b>' + this.y + '</b><br/>';
                jQuery.each(rex.composition, function (name, value) {
                    s += '<b>' + name + ':</b> ' + value + '<br>';
                });
                return s;
            }
        }
    });
}

function highlightBargraphRow() {
    if (selectedBargraphRowIndex != null) {
        selectedBargraphRowIndex.attr('fill', '#FFF');
        selectedVarNameOnBargraphRow.attr('style', '');
    }
    overBargraphRowIndex.attr('fill', '#29bdca');
    selectedBargraphRowIndex = overBargraphRowIndex;
    selectedVarNameOnBargraphRow = overVarNameOnBargraphRow;
}

function sortByCorrelation() {
    jQuery("#minimumNumberOfSamples").val('');
    constructBarGraph(bargraphData.length, sortedByCorrelation, dataArray);
}

function sortByCausality() {
    jQuery("#minimumNumberOfSamples").val('');
    constructBarGraph(bargraphData.length, sortedByCausality, dataArray);
}

function filterByNumberOfPairs(numberOfPairs) {

    if (bargraphData != null && bargraphData.length > 0) {
        var causesFiltered = new Array();
        var filteredByNumberOfPairs = new Array();
        var k = 0;
        var isEffect = (jQuery('#selectOutputAsType').val() === 'cause');
        for (var i in bargraphData) {
            // bargraphData.sort(compare);
            if (numberOfPairs < parseInt(bargraphData[i].numberOfPairs)) {
                causesFiltered.push((isEffect ? {
                    'originalName': bargraphData[i].originalEffect,
                    'name': bargraphData[i].effect
                } : {'originalName': bargraphData[i].originalCause, 'name': bargraphData[i].cause}));
                var color = 'blue';
                if (bargraphData[i].correlationCoefficient > 0) {
                    color = 'red';
                }
                filteredByNumberOfPairs[k] = {
                    y: bargraphData[i].correlationCoefficient,
                    color: color,
                    name: 'Correlation',
                    label: causesFiltered[k].name,
                    category: bargraphData[i].causeCategory,
                    composition: {
                        //'Causality Factor': bargraphData[i].causalityFactor,
                        'Duration Of Action': bargraphData[i].durationOfAction,
                        //'Effect': bargraphData[i].effect,
                        //'Effect Size': bargraphData[i].effectSize,
                        'Number Of Pairs': bargraphData[i].numberOfPairs,
                        'Onset Delay': bargraphData[i].onsetDelay,
                        //'Reverse Correlation': bargraphData[i].reverseCorrelation,
                        //'Statistical Significance': bargraphData[i].statisticalSignificance,
                    },
                };//end data
                k++;
            }
        }

        if (causesFiltered.length > 0) {
            constructBarGraph(causesFiltered.length, filteredByNumberOfPairs, causesFiltered);
            // if(causesFiltered.length < 4 && barchart != null) {
            // barchart.setSize(305, (causesFiltered.length * 40.5) + 150, false);
            // }
        } else {
            resetBarGraph();
            //jQuery(".no-data").show();
        }
    }
}

function compare(a, b) {
    if (a.correlationCoefficient > b.correlationCoefficient)
        return -1;
    if (a.correlationCoefficient < b.correlationCoefficient)
        return 1;
    return 0;
}

