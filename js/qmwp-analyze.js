var AnalyzePage = function () {
    var timezone = jstz.determine().name();

    var quantimodoUnits = {};
    var quantimodoVariables = {};

    var dateRangeStart, dateRangeEnd;

    var dateSelectorVisible;
    var inputSelectorVisible;

    var selectedVariables = new Array();

    var initLoginDialog = function () {
        jQuery(document).on('lwa_login', function (event, data, form) {
            if (data.result === true) {
                refreshMeasurementsRange(function () {
                    refreshVariables([], function () {
                        categoryListUpdated();
                        restoreChart();
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

    var initVariableCard = function () {
        // Create the variable picker menu
        jQuery("#addVariableMenu").menu({
            select: function (event, ui) {
                var originalVariableName = ui.item.attr('variable');
                if (originalVariableName != null) {
                    newVariableSelected(originalVariableName, ui.item.attr('category'), ui.item.attr('source'), null, false);
                    //ui.item.addClass("ui-state-disabled");
                    var variableSelectElement = jQuery("#addVariableMenu li[variable='" + originalVariableName + "'][category='" + ui.item.attr('category') + "'][source='" + ui.item.attr('source') + "']");
                    if (!variableSelectElement.hasClass("ui-state-disabled")) {
                        variableSelectElement.addClass("ui-state-disabled");
                    }
                }
            }
        });

        // Handle clicking the close button
        jQuery('#selectedVariables').on('click', 'li > .closeButton', function (event) {
            var selectedVariableElement = jQuery(event.target).parent();
            var selectedCategoryName = selectedVariableElement.attr('category');
            var selectedOriginalName = selectedVariableElement.attr('variable');
            //var selectedSource = selectedVariableElement.attr('source');

            // Remove it from the selected variables so that we won't get the data when refreshing
            for (var i = 0; i < AnalyzePage.selectedVariables.length; i++) {
                var selVariable = AnalyzePage.selectedVariables[i];

                if (selVariable.originalName === selectedOriginalName) {
                    if (oceanFiveInUse.length > 0) {
                        var index = jQuery.inArray(selVariable.color, oceanFiveInUse);
                        if (index > -1) {
                            oceanFiveInUse.splice(index, 1);
                        }
                    }
                    break;
                }
            }
            AnalyzePage.selectedVariables.splice(i, 1);
            // Remove HTML
            selectedVariableElement.remove();

            // Reenable this item in the variable picker
            /*            var variableSelectElement = jQuery("#addVariableMenu li[variable='" + selectedOriginalName + "'][category='" + selectedCategoryName + "'][source='" + selectedSource + "']");
             if (variableSelectElement.hasClass("ui-state-disabled")) {
             variableSelectElement.removeClass("ui-state-disabled");
             }*/

            // Create a fake variable element for the graph handler
            var fakeVariable = {
                originalName: selectedOriginalName,
                category: selectedCategoryName,
                //source: selectedSource
            }
            // Remove the variable from our graph
            AnalyzeChart.removeData(fakeVariable);

            // Remove from localstorage
            removeSelectedVariable(selectedOriginalName, selectedCategoryName);
        });

        // Handle clicking the visibility toggler
        jQuery('#selectedVariables').on('click', 'li > .eyeballButton', function (event) {
            var selectedVariableElement = jQuery(event.target).parent();
            var selectedCategoryName = selectedVariableElement.attr('category');
            var selectedOriginalName = selectedVariableElement.attr('variable');
            //var selectedSource = selectedVariableElement.attr('source');

            // Create a fake variable element for the graph handler
            var fakeVariable = {
                originalName: selectedOriginalName,
                category: selectedCategoryName,
                //source: selectedSource
            }

            var visible = AnalyzeChart.toggleDataVisibility(fakeVariable);
            if (visible) {
                selectedVariableElement.find(".colorIndicator").css({opacity: "1"});
            }
            else {
                selectedVariableElement.find(".colorIndicator").css({opacity: "0.2"});
            }
        });

        jQuery("#selectedVariables").on('click', 'li > .settingsButton', function (event) {
            var selectedVariableElement = jQuery(event.target).parent();
            var selectedCategoryName = selectedVariableElement.attr('category');
            var selectedOriginalName = selectedVariableElement.attr('variable');

            currentCategory = AnalyzePage.quantimodoVariables[selectedCategoryName]
            for (var i = 0; i < currentCategory.length; i++) {
                var currentVariable = currentCategory[i];
                if (currentVariable.originalName == selectedOriginalName && currentVariable.category == selectedCategoryName) {
                    variableSettings.show(currentVariable);
                    break;
                }
            }
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
        }
        if (variable.source != null && variable.source.length != 0) {
            filters.source = variable.source;
        }
        if (variable.color == null) {
            variable.color = getRandomColor();
        }

        Quantimodo.getMeasurements(filters, function (measurements) {
            AnalyzeChart.addData(variable, measurements);

            //var source = variable.source == null ? '' : source;
            var variableIndicator = jQuery("#selectedVariables [variable='" + variable.originalName + "'][category='" + variable.category + "'] .colorIndicator");
            variableIndicator.css({'background-size': '0 14px'});
            setTimeout(function () {
                variableIndicator.removeClass("loading");
            }, 220);
        });

        return variable.color;
    };

    var newVariableSelected = function (originalVariableName, category, source, color, restoreFromLocalStorage) {
        var selectedVariable;

        var categories = Object.keys(AnalyzePage.quantimodoVariables);
        var currentCategory, currentVariable;

        currentCategory = AnalyzePage.quantimodoVariables[category];
        if (currentCategory == null || currentCategory.length == 0) {
            return;
        }
        for (var n = 0; n < currentCategory.length; n++) {
            currentVariable = currentCategory[n];
            if (currentVariable.originalName == originalVariableName) {
                selectedVariable = jQuery.extend({}, currentVariable);	// Create a copy so that modifications won't pollute the original set
                break;
            }
        }

        if (selectedVariable != null) {
            selectedVariable.source = source;
            selectedVariable.color = color;
            var variableColor = addData(selectedVariable);

            var variableEntryString = selectedVariable.name;

            if (source != null && source.length > 0) {
                variableEntryString += ' (' + source + ')';
            }

            var variableEntryStringTruncated = variableEntryString;
            if (variableEntryStringTruncated != null && variableEntryStringTruncated.length > 25) {
                //the indexOf will return the position of the first space starting from the 10th
                var tempHeader = variableEntryStringTruncated;
                for (var i = 23; i >= 10; i--) {
                    tempHeader = variableEntryStringTruncated.indexOf(' ', i) != -1 ? variableEntryStringTruncated.substring(0, variableEntryStringTruncated.indexOf(' ', i)) + "..." : variableEntryStringTruncated;
                    if (tempHeader.length < 26) {
                        break;
                    }
                }
                variableEntryStringTruncated = tempHeader;
            }
            var sourceAttr = source == undefined || source == null || source == '' ? 'source' : 'source="' + source + '"';
            jQuery('#selectedVariables').append('<li variable="' + selectedVariable.originalName + '" ' + sourceAttr + ' category="' + category + '"><div class="colorIndicator loading" style="background-color: ' + variableColor + '"></div><span title="' + variableEntryString + '">' + variableEntryStringTruncated + '</span><div class="closeButton icon-remove icon-large"></div><div class="eyeballButton icon-eye-open icon-large"></div><div class="settingsButton icon-cog icon-large" category="' + category + '"></div></li>');


            AnalyzePage.selectedVariables.push(selectedVariable);
            // we store only we select new variable from menu, during restoring no sence to store again which is already in local storage.
            if (!restoreFromLocalStorage) {
                storeSelectedVariable(selectedVariable, variableColor);
            }
        }
    }

    var storeSelectedVariable = function (variable, color) {
        if (typeof(Storage) !== "undefined") {
            newStoredVariable = {
                'name': variable.originalName,
                'cat': variable.category,
                'src': variable.source,
                'color': color
            }

            try {
                var storedVariables = JSON.parse(localStorage["selectedVariables"]);

                var contains = false;
                for (i = 0; i < storedVariables.length; i++) {
                    if (storedVariables[i].name == variable.originalName && storedVariables[i].cat == variable.category && storedVariables[i].src == variable.source) {
                        contains = true;
                        break;
                    }
                }
                if (!contains) {
                    storedVariables.push(newStoredVariable);
                    saveSetting('selectedVariables', JSON.stringify(storedVariables));
                }
            }
            catch (e) {
                console.log(e);
                var storedVariables = [newStoredVariable];
                saveSetting('selectedVariables', JSON.stringify(storedVariables));
            }
        }
    }

    var removeSelectedVariable = function (originalVariableName, category) {
        if (typeof(Storage) !== "undefined") {
            try {
                var storedVariables = JSON.parse(localStorage["selectedVariables"]);

                for (i = 0; i < storedVariables.length; i++) {
                    if (storedVariables[i].name == originalVariableName && storedVariables[i].cat == category) {
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
    }

    var lastPeriod = 1;
    var periodUpdated = function () {
        var newPeriod = AnalyzePage.getPeriod();
        if (newPeriod !== lastPeriod) {
            lastPeriod = newPeriod;
            refreshData();
        }
    };

    var categoryListUpdated = function () {
        jQuery('#selectInputCategory').empty();
        jQuery('#selectVariableCategorySetting').empty();
        jQuery.each(Object.keys(AnalyzePage.quantimodoVariables).sort(function (a, b) {
            return a.toLowerCase().localeCompare(b.toLowerCase());
        }), function (index, category) {
            jQuery('#selectInputCategory').append(jQuery('<option/>').attr('value', category).text(category));
            jQuery('#selectVariableCategorySetting').append(jQuery('<option/>').attr('value', category).text(category));
            jQuery('#addVariableMenuCategories').append(jQuery('<li><a>' + category + '</a><ul class="variableContainer">'));
            jQuery.each(AnalyzePage.quantimodoVariables[category], function (index, variable) {
                if (variable.parent != null) {
                    // this variable has parent do not display it on variables list, it will be displayed above sources of its parent
                    return true;
                }
                // Variable entry
                jQuery('#addVariableMenuCategories .variableContainer').last().append(jQuery('<li category="' + variable.category + '" variable="' + variable.originalName + '" source=""><a>' + variable.name + '</a><ul class="sourceContainer">'));

                // "Sub variables" if we have
                if (variable.subVariables != null && variable.subVariables.length > 0) {
                    for (var k = 0; k < variable.subVariables.length; k++) {
                        var subVariable = variable.subVariables[k];
                        jQuery('#addVariableMenuCategories .sourceContainer').last().append(jQuery('<li category="' + subVariable.category + '" variable="' + subVariable.originalName + '" source=""><a>' + subVariable.name + '</a><ul class="sourceContainerSubVars">'));

                        // "All sources" entry for subVariable, hope we do not go deep than one level, I do not know use case.
                        // also hope no deadlocks in the future
                        jQuery('#addVariableMenuCategories .sourceContainerSubVars').last().append(jQuery('<li category="' + subVariable.category + '" variable="' + subVariable.originalName + '" source=""><a>All sources</a>'));
                        var sourcesSubVariable = subVariable.sources.split(',');
                        for (var j = 0; j < sourcesSubVariable.length; j++) {
                            jQuery('#addVariableMenuCategories .sourceContainerSubVars').last().append(jQuery('<li category="' + subVariable.category + '" variable="' + subVariable.originalName + '" source="' + sourcesSubVariable[j] + '"><a>' + sourcesSubVariable[j] + '</a>'));
                        }

                        jQuery('#addVariableMenuCategories .sourceContainer').last().append(jQuery('</ul></li>'));
                    }
                    jQuery('#addVariableMenuCategories .sourceContainer').last().append('<hr/>');
                }

                // "All sources" entry
                jQuery('#addVariableMenuCategories .sourceContainer').last().append(jQuery('<li category="' + variable.category + '" variable="' + variable.originalName + '" source=""><a>All sources</a>'));
                var sources = variable.sources.split(',');
                for (var i = 0; i < sources.length; i++) {
                    jQuery('#addVariableMenuCategories .sourceContainer').last().append(jQuery('<li category="' + variable.category + '" variable="' + variable.originalName + '" source="' + sources[i] + '"><a>' + sources[i] + '</a>'));
                }

                jQuery('#addVariableMenuCategories .variableContainer').last().append(jQuery('</ul></li>'));
            });
            jQuery('#addVariableMenuCategories').append(jQuery('</ul></li>'));

        });
        jQuery("#addVariableMenu").menu("refresh");
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
            var storedVariables = JSON.parse(localStorage["selectedVariables"]);
            jQuery('#selectedVariables').empty();
            for (i = 0; i < storedVariables.length; i++) {
                var storedVariable = storedVariables[i];

                // restore colors
                if (oceanFiveInUse.length < 5 && jQuery.inArray(storedVariable.color, oceanFive) > -1 && jQuery.inArray(storedVariable.color, oceanFiveInUse) == -1) {
                    oceanFiveInUse.push(storedVariable.color);
                }

                // Add the variable
                newVariableSelected(storedVariable.name, storedVariable.cat, storedVariable.src, storedVariable.color, true);

                // Disable the entry in the variable picker
                var variableSelectElement = jQuery("#addVariableMenu li[variable='" + storedVariable.name + "'][category='" + storedVariable.cat + "'][source='" + storedVariable.src + "']");

                if (!variableSelectElement.hasClass('ui-state-disabled')) {
                    variableSelectElement.addClass('ui-state-disabled');
                }
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
            if (access_token) {

                refreshMeasurementsRange(function () {

                    //get variable input
                    var variableInput = jQuery('#variable-selector');
                    //set it disabled while variables are not loaded
                    variableInput.prop('disabled', true);
                    variableInput.val('Loading. Please wait...');

                    refreshVariables([], function () {

                        //once variables are ready - enable variable searcher
                        variableInput.val('');
                        variableInput.prop('disabled', false);
                        //setup autocomplete functionality
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
                                newVariableSelected(
                                    selectedVariable.originalName,
                                    selectedVariable.category,
                                    null,   //TODO source
                                    null, false
                                );
                                //blank variable searcher
                                jQuery("#variable-selector").val('');
                                return false;
                            }
                        });

                        categoryListUpdated();
                        restoreChart();

                    });
                });
                refreshUnits(function () {
                    unitListUpdated();
                });

                retrieveSettings();
                initVariableCard();

                variableSettings.init({
                    saveCallback: function () {
                        refreshVariables([], function () {
                            categoryListUpdated();
                            restoreChart();
                        });	//TODO replace this with something that updates the variables locally, since this triggers
                    }
                });

                initDateRangeSelector();
                initLoginDialog();
                initSharing();
                initDeleteMeasurements();

            } else {
                window.location.href = "?connect=quantimodo";
            }

        }
    };
}();

jQuery(AnalyzePage.init);
