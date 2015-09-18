var AnalyzePage = function () {
    var timezone = jstz.determine().name();

    var quantimodoUnits = {};
    var quantimodoVariables = {};

    var dateRangeStart, dateRangeEnd;

    var dateSelectorVisible;
    var inputSelectorVisible;

    var selectedVariables = new Array();

    /* Initialize left menubar */

    var initVariableCard = function () {

        // Handle clicking the close button
        jQuery('#selectedVariables').on('click', 'li > .closeButton', function (event) {
            var clickedVariableElement = jQuery(event.target).parent();
            var variableId = clickedVariableElement.attr('variable');

            // Remove it from the selected variables so that we won't get the data when refreshing
            for (var i = 0; i < AnalyzePage.selectedVariables.length; i++) {

                if (AnalyzePage.selectedVariables[i].id == variableId) {
                    var selectedVariable = AnalyzePage.selectedVariables[i];
                    if (oceanFiveInUse.length > 0) {
                        var index = jQuery.inArray(selectedVariable.color, oceanFiveInUse);
                        if (index > -1) {
                            oceanFiveInUse.splice(index, 1);
                        }
                    }
                    // Remove from localStorage
                    removeSelectedVariable(selectedVariable);
                    // Remove the variable from our graph
                    AnalyzeChart.removeData({
                        id: selectedVariable.id,
                        originalName: selectedVariable.name,
                        category: selectedVariable.category
                    });

                    break;
                }
            }

            AnalyzePage.selectedVariables.splice(i, 1);

            // Remove HTML
            clickedVariableElement.remove();
        });

        // Handle clicking the visibility toggler
        jQuery('#selectedVariables').on('click', 'li > .eyeballButton', function (event) {
            var clickedVariableElement = jQuery(event.target).parent();
            var variableId = clickedVariableElement.attr('variable');

            var selectedVariable = null;
            for (var i = 0; i < AnalyzePage.selectedVariables.length; i++) {
                if (AnalyzePage.selectedVariables[i].id == variableId) {
                    selectedVariable = AnalyzePage.selectedVariables[i];
                    break;
                }
            }

            var visible = AnalyzeChart.toggleDataVisibility({
                id: selectedVariable.id,
                originalName: selectedVariable.name,
                category: selectedVariable.category
            });

            if (visible) {
                clickedVariableElement.find(".colorIndicator").css({opacity: "1"});
            }
            else {
                clickedVariableElement.find(".colorIndicator").css({opacity: "0.2"});
            }

        });

        jQuery("#selectedVariables").on('click', 'li > .settingsButton', function (event) {
            var clickedVariableElement = jQuery(event.target).parent();

            var variableId = clickedVariableElement.attr('variable');

            var selectedVariable = null;
            for (var i = 0; i < AnalyzePage.selectedVariables.length; i++) {
                if (AnalyzePage.selectedVariables[i].id == variableId) {
                    selectedVariable = AnalyzePage.selectedVariables[i];
                    break;
                }
            }

            variableSettings.show(selectedVariable);

        });

    };


    var initDateRangeSelector = function () {
        jQuery("#accordion-content-rangepickers").buttonset();
        jQuery("#accordion-content-rangepickers :radio").click(periodUpdated);
    };


    // Very crude, TODO use hsv
    function getRandomColor() {
        if (oceanFiveInUse.length < 5) {
            for (var i = 0; i < oceanFive.length; i++) {
                if (jQuery.inArray(oceanFive[i], oceanFiveInUse) == -1) {
                    oceanFiveInUse.push(oceanFive[i]);
                    return oceanFive[i];
                }
            }
        }
        var letters = '0123456789ABCDEF'.split('');
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.round(Math.random() * 15)];
        }
        return color;
    }

    var addData = function (variable) {
        var filters = {
            'variableName': variable.originalName,
            'startTime': AnalyzePage.dateRangeStart,
            'endTime': AnalyzePage.dateRangeEnd,
            //'groupingWidth': AnalyzePage.getPeriod(),
            'groupingTimezone': AnalyzePage.getTimezone()
        };

        if (variable.color == null) {
            variable.color = getRandomColor();
        }

        Quantimodo.getDailyMeasurements(filters, function (measurements) {
            AnalyzeChart.addData(variable, measurements);

            //var source = variable.source == null ? '' : source;
            var variableIndicator = jQuery("#selectedVariables [variable='" + variable.id + "'][category='" + variable.category + "'] .colorIndicator");
            variableIndicator.css({'background-size': '0 14px'});
            setTimeout(function () {
                variableIndicator.removeClass("loading");
            }, 220);
        });
    };

    var newVariableSelected = function (selectedVariable) {

        if (!isVariableAlreadySelected(selectedVariable.id)) {
            AnalyzePage.selectedVariables.push(selectedVariable);
            Quantimodo.getVariableCategories(null, function (variableCategories) {

                var currentCategory = null;
                for (var i = 0; i < variableCategories.length; i++) {
                    if (variableCategories[i].name == selectedVariable.category) {
                        currentCategory = variableCategories[i];
                        break;
                    }
                }
                if (currentCategory) {

                    if (!selectedVariable.color) {
                        selectedVariable.color = getRandomColor();
                    }

                    addData(selectedVariable);

                    var variableEntryString = selectedVariable.name;

                    var variableEntryStringTruncated = variableEntryString;

                    if (variableEntryStringTruncated.length >= 25) {
                        variableEntryStringTruncated = variableEntryStringTruncated.substr(0, 22) + '...';
                    }

                    jQuery('#selectedVariables').append(
                        '<li variable="' + selectedVariable.id + '" category="' + currentCategory.name + '">' +
                        '<div class="colorIndicator loading" style="background-color: ' + selectedVariable.color + '">' +
                        '</div>' +
                        '<span title="' + variableEntryString + '">' + variableEntryStringTruncated + '</span>' +
                        '<div class="closeButton fa fa-times">' +
                        '</div>' +
                        '<div class="eyeballButton fa fa-eye"></div>' +
                        '<div class="settingsButton fa fa-cog" category="' + currentCategory.name + '">' +
                        '</div></li>');

                    storeSelectedVariable(selectedVariable);

                }

            });
        }

    };

    var storeSelectedVariable = function (variable) {
        if (typeof(Storage) !== "undefined") {
            try {
                var storedVariables = JSON.parse(localStorage["selectedVariables"]);

                var contains = false;
                for (var i = 0; i < storedVariables.length; i++) {
                    if (storedVariables[i].id == variable.id) {
                        contains = true;
                        break;
                    }
                }
                if (!contains) {
                    storedVariables.push(variable);
                    saveSetting('selectedVariables', JSON.stringify(storedVariables));
                }
            }
            catch (e) {
                console.log(e);
                var storedVariables = [variable];
                saveSetting('selectedVariables', JSON.stringify(storedVariables));
            }
        }
    };

    var removeSelectedVariable = function (variable) {
        if (typeof(Storage) !== "undefined") {
            try {
                var storedVariables = JSON.parse(localStorage["selectedVariables"]);

                for (var i = 0; i < storedVariables.length; i++) {
                    if (storedVariables[i].id == variable.id) {
                        storedVariables.splice(i, 1);
                        break;
                    }
                }

                saveSetting('selectedVariables', JSON.stringify(storedVariables));
            }
            catch (e) {
                console.log(e);
                localStorage.removeItem('selectedVariables');
            }
        }
    };

    var lastPeriod = 1;
    var periodUpdated = function () {
        var newPeriod = AnalyzePage.getPeriod();
        if (newPeriod !== lastPeriod) {
            lastPeriod = newPeriod;
            refreshData();
        }
    };

    var unitListUpdated = function () {
        jQuery('#selectVariableUnitSetting').empty();
        jQuery.each(AnalyzePage.quantimodoUnits, function (index, category) {
            jQuery('#selectVariableUnitSetting').append(jQuery('<option disabled/>').attr("style", "color:#29bdca;font-styl;").text(category[0].category));
            jQuery.each(category, function (index, unit) {
                jQuery('#selectVariableUnitSetting').append(jQuery('<option/>').attr('value', unit.abbreviatedName).text(unit.name));
            });
        });
    };

    var retrieveSettings = function () {
        if (typeof(Storage) !== "undefined") {
            dateSelectorVisible = (localStorage["dateSelectorVisible"] || "true") == "true" ? true : false;
            inputSelectorVisible = (localStorage["inputSelectorVisible"] || "true") == "true" ? true : false;
            outputSelectorVisible = (localStorage["outputSelectorVisible"] || "true") == "true" ? true : false;
        }
    };

    var saveSetting = function (setting, value) {
        if (typeof(Storage) !== "undefined") {
            localStorage[setting] = value;
        }
    };

    var restoreChart = function () {
        if (typeof(Storage) !== "undefined") {

            var storedVariables = [];
            if (localStorage.getItem("selectedVariables")) {
                storedVariables = JSON.parse(localStorage["selectedVariables"]);
            }
            jQuery('#selectedVariables').empty();
            for (i = 0; i < storedVariables.length; i++) {
                var storedVariable = storedVariables[i];

                // restore colors
                if (oceanFiveInUse.length < 5 && jQuery.inArray(storedVariable.color, oceanFive) > -1 && jQuery.inArray(storedVariable.color, oceanFiveInUse) == -1) {
                    oceanFiveInUse.push(storedVariable.color);
                }

                // Add the variable
                newVariableSelected(storedVariable);

            }
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
    };

    var showShareDialog = function () {
        jQuery("#share-dialog-background").css({'display': 'block', 'opacity': 0.5});
        jQuery("#share-dialog").css({'display': 'block', 'opacity': 1});
    };

    var share = function (onDoneListener) {
        var shareObject = {
            "type": "analyze",
            "causeMeasurements": causeMeasurements,
            "effectMeasurements": effectMeasurements,
            "inputVariable": lastInputVariable,
            "outputVariable": lastOutputVariable
        };
        Quantimodo.postAnalyzeShare(shareObject, function (response) {
            onDoneListener(response['id']);
        });
    };

    var initPreselectedVariables = function () {

        if (typeof qmwpShortCodeDefinedVariables !== 'undefined' && qmwpShortCodeDefinedVariables) {

            var variables = qmwpShortCodeDefinedVariables.split(';');

            for (var i = 0; i < variables.length; i++) {
                if (variables[i]) {
                    Quantimodo.getVariableByName(variables[i], function (variable) {
                        if (typeof variable.id !== 'undefined') {
                            newVariableSelected(variable);
                        }
                    });
                }
            }
        }

    };

    var isVariableAlreadySelected = function (variableId) {

        var isSelected = false;

        for (var i = 0; i < AnalyzePage.selectedVariables.length; i++) {
            if (AnalyzePage.selectedVariables[i].id == variableId) {
                isSelected = true;
                break;
            }
        }

        return isSelected;
    };

    return {
        quantimodoUnits: quantimodoUnits,
        quantimodoVariables: quantimodoVariables,

        dateRangeStart: dateRangeStart,
        dateRangeEnd: dateRangeEnd,

        selectedVariables: selectedVariables,

        getRandomColor: function () {
            return getRandomColor();
        },

        getTimezone: function () {
            return timezone;
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
            if (accessToken) {

                refreshMeasurementsRange(function () {

                    //setup autocomplete functionality
                    var variableInput = jQuery('#variable-selector');
                    variableInput.autocomplete({

                        source: function (request, response) {
                            //fetch variables using quantimodo-api
                            Quantimodo.searchVariables(jQuery("#variable-selector").val(), function (data) {

                                var results = [];

                                filterFoundVariables:
                                    for (var i = 0; i < data.length; i++) {
                                        for (var j = 0; j < AnalyzePage.selectedVariables.length; j++) {
                                            if (data[i].id == AnalyzePage.selectedVariables[j].id) {
                                                //if variable with such ID is already selected
                                                //we are skipping it
                                                continue filterFoundVariables;
                                            }
                                        }
                                        results.push({
                                            label: data[i].name,
                                            value: data[i].name,
                                            variable: data[i]
                                        })
                                    }
                                //passing filtered variables to the autocomplete for displaying
                                response(results);

                            });
                        },
                        minLength: 2,
                        select: function (event, ui) {
                            //get selected item
                            var selectedVariable = ui.item.variable;
                            console.debug('Variable Selected:');
                            console.debug(selectedVariable);
                            //pass it for processing
                            newVariableSelected(ui.item.variable);
                            //blank variable searcher
                            jQuery("#variable-selector").val('');
                            return false;
                        }
                    });

                    initPreselectedVariables();
                    restoreChart();

                });

                refreshUnits(function () {
                    unitListUpdated();
                });

                retrieveSettings();
                initVariableCard();

                variableSettings.init({
                    saveCallback: function () {
                        refreshVariables([], function () {
                            /*categoryListUpdated();*/
                            restoreChart();
                        });	//TODO replace this with something that updates the variables locally, since this triggers
                    }
                });

                initDateRangeSelector();
                initSharing();
                initDeleteMeasurements();

            } else {
                window.location.href = "?connect=quantimodo";
            }

        }
    };
}();

jQuery(AnalyzePage.init);
