var AnalyzePage = function() {
	var timezone = jstz.determine().name();

	var settingsCurrentVariable;	// Variable currently "open" in settings

	var quantimodoUnits = {};
	var quantimodoVariables = {};
	var inputMeasurements;
	var outputMeasurements;

	var dateRangeStart, dateRangeEnd;

	var dateSelectorVisible;
	var inputSelectorVisible;
	var outputSelectorVisible;
	var correlationGaugeVisible = true;
	var scatterplotVisible = true;


	var initLoginDialog = function()
	{
        if (!isLoggedIn)
        {
            jQuery(document).on('lwa_login', function(event, data, form)
            {
                if (data.result === true)
                {
                    Quantimodo.getMeasurementsRange([], function(range)
                    {
                        dateRangeStart = new Date(0);
                        dateRangeStart.setUTCSeconds(range['lowerLimit']);
                        jQuery('#datepicker-start').datepicker('setDate', dateRangeStart);
                        dateRangeEnd = new Date(0);
                        dateRangeEnd.setUTCSeconds(range['upperLimit']);
                        jQuery('#datepicker-end').datepicker('setDate', dateRangeEnd);

                        refreshVariables();
                    });
					refreshUnits();

                    jQuery("#login-dialog-background")  .addClass('transitions').css( {'opacity':0} );
                    jQuery("#login-dialog")             .addClass('transitions').css( {'opacity':0} );

                    setTimeout(function()
                    {
                        jQuery( "#login-dialog-background" ).css({'display':'none'});
                        jQuery( "#login-dialog" ).css({'display':'none'});
                    }, 500);
				}
			});
		}
	};

	/* Initialize left menubar */
	var initAccordion = function()
	{
		jQuery('#accordion-date-header').click(function()
		{
			dateSelectorVisible = !dateSelectorVisible;
			saveSetting("dateSelectorVisible", dateSelectorVisible);
			toggleElement("#accordion-date-content");
		});
		jQuery('#accordion-input-header').click(function()
		{
			inputSelectorVisible = !inputSelectorVisible
			saveSetting("inputSelectorVisible", inputSelectorVisible);
			toggleElement("#accordion-input-content");
		});
		jQuery('#accordion-output-header').click(function()
		{
			outputSelectorVisible = !outputSelectorVisible
			saveSetting("outputSelectorVisible", outputSelectorVisible);
			toggleElement("#accordion-output-content");
		});

		var delay = 300;
		if (dateSelectorVisible)
		{
			setTimeout(function() { toggleElement("#accordion-date-content"); dateSelectorVisible = true;}, delay);
			delay += 250;
		}
		if (inputSelectorVisible)
		{
			setTimeout(function() {	toggleElement("#accordion-input-content"); inputSelectorVisible = true;}, delay);
			delay += 250;
		}
		if (outputSelectorVisible)
		{
			setTimeout(function() {	toggleElement("#accordion-output-content"); outputSelectorVisible = true; }, delay);
		}

		jQuery("#button-input-varsettings").click(function()
		{
			showVariableSettings(lastInputVariable);
		});
		jQuery("#button-output-varsettings").click(function()
		{
			showVariableSettings(lastOutputVariable);
		});
	};
	
  	var initVariableSettings = function()
	{
		// Activate cancel/save buttons on varsettings
		jQuery("#accordion-settings-content .inner .button-save").click(function()
		{
			hideVariableSettings(true);
		});
		jQuery("#accordion-settings-content .inner .button-cancel").click(function()
		{
			hideVariableSettings(false);
		});
		// Plus button next to the joined variable selector
		jQuery("#addJoinedVariableButton").click(function()
		{
			var button = jQuery("#addJoinedVariableButton");
			var picker = jQuery("#joinedVariablePicker");
			if (button.hasClass('active'))
			{
				var selectedOptionElement = jQuery("#joinedVariablePicker option:selected")

				var selectedOriginalName = selectedOptionElement.val();
				var selectedName = selectedOptionElement.text();

				jQuery('#joinedVariablesList').append('<li value="' + selectedOriginalName + '">' + selectedName + '<div></div></li>');

				setTimeout(function()
				{
					selectedOptionElement.remove();
				}, 500);
			}
			button.toggleClass('active');
			picker.toggleClass('active');
		});
		// Remove button next to joined variables
		jQuery('#joinedVariablesList').on('click', 'li > *', function(event)
		{
			var selectedVariableElement = jQuery(event.target).parent();
			var selectedOriginalName = selectedVariableElement.attr('value');
			var selectedName = selectedVariableElement.text();

			jQuery('#joinedVariablePicker').append(jQuery('<option/>').attr('value', selectedOriginalName).text(selectedName));

			selectedVariableElement.remove();
		});
	}

	var initDateRangeSelector = function()
	{
		dateRangeEnd = new Date();    // Date for the "To" textbox
		dateRangeStart = new Date();  // Date for the "From" textbox
		var fromMaxDate = new Date();
		fromMaxDate.setDate(dateRangeEnd.getDate() - 1); // Max date for the "From" datepicker, which is one day before today

		dateRangeStart.setDate(dateRangeStart.getDate() - 7);
		jQuery("#datepicker-start").val(jQuery.datepicker.formatDate("'From' MM d',' yy", dateRangeStart));
		jQuery("#datepicker-start").datepicker(
        {
			dateFormat: "'From' MM d',' yy",
			defaultDate: dateRangeStart,
			maxDate: fromMaxDate,
			changeMonth: true,
			changeYear: true,
			beforeShow: function(textbox, instance) { instance.dpDiv.css({ marginTop: '-3px' }); },
			onSelect: function(dateText, inst)
			{
				dateRangeStart = jQuery("#datepicker-start").datepicker("getDate");
				// If the start date is equal or greater than end date, decrease it
				if (dateRangeStart >= dateRangeEnd)
				{
					dateRangeEnd.setDate(dateRangeStart.getDate() + 1);
					jQuery("#datepicker-end").val(jQuery.datepicker.formatDate("'To' MM d',' yy", dateRangeEnd));
					jQuery("#datepicker-end").datepicker( "option", "defaultDate", dateRangeStart);
					bothDatesUpdated();
				}
				else
				{
					startDateUpdated();
				}
			}
		});

		jQuery("#datepicker-end").val(jQuery.datepicker.formatDate("'To' MM d',' yy", dateRangeEnd));
		jQuery("#datepicker-end").datepicker(
        {
			dateFormat: "'To' MM d',' yy",
			defaultDate: dateRangeEnd,
			maxDate: dateRangeEnd,
			changeMonth: true,
			changeYear: true,
			beforeShow: function(textbox, instance) { instance.dpDiv.css({ marginTop: '-3px' }); },
			onSelect: function(dateText, inst) {
				dateRangeEnd = jQuery("#datepicker-end").datepicker("getDate");
				// If the start date is equal or greater than end date, decrease it
				if (dateRangeStart >= dateRangeEnd)
				{
					dateRangeStart.setDate(dateRangeEnd.getDate() - 1);
					jQuery("#datepicker-start").val(jQuery.datepicker.formatDate("'From' MM d',' yy", dateRangeStart));
					jQuery("#datepicker-start").datepicker( "option", "defaultDate", dateRangeStart);
					bothDatesUpdated();
				}
				else
				{
					endDateUpdated();
				}
			}
		});

		jQuery("#accordion-content-rangepickers").buttonset();
		jQuery("#accordion-content-rangepickers :radio").click(periodUpdated);

		if (isLoggedIn)
		{
			Quantimodo.getMeasurementsRange([], function(range) {
				dateRangeStart = new Date(0);
				dateRangeStart.setUTCSeconds(range['lowerLimit']);
				jQuery('#datepicker-start').datepicker('setDate', dateRangeStart);
				dateRangeEnd = new Date(0);
				dateRangeEnd.setUTCSeconds(range['upperLimit']);
				jQuery('#datepicker-end').datepicker('setDate', dateRangeEnd);

				refreshVariables();
			});
			refreshUnits();
		}
	};

	var showVariableSettings = function(variable)
	{
		// Init and checks
	    if (variable == null)
	    {
	        alert("No variable selected");
	        return;
	    }
		settingsCurrentVariable = variable;

		// Fill unit selector
		jQuery('#selectVariableUnitSetting').empty();
		var categories = Object.keys(quantimodoUnits)
		var currentCategory, currentUnit;
		var foundUnit = false;
		var count = categories.length, innerCount;
		for(var i = 0; i < count; i++)
		{
			currentCategory = quantimodoUnits[categories[i]];
			innerCount = currentCategory.length;
			for(var n = 0; n < innerCount; n++)
			{
				currentUnit = currentCategory[n];
				if (foundUnit)	// If foundUnit = true we're in the right category, so start adding values
				{
					jQuery('#selectVariableUnitSetting').append(jQuery('<option/>').attr('value', currentUnit.abbreviatedName).text(currentUnit.name));
				}
				else if (currentUnit.abbreviatedName == settingsCurrentVariable.unit)
				{
					foundUnit = true;
					n = -1;	// Reset the position in this category and continue, values will be added next loop;
				}
			}
			if (foundUnit)
			{
				break;
			}
		}

		// Fill variable selector, TODO: Suggestions at the top?
		jQuery('#joinedVariablePicker').empty();
		var currentVariable;
		var variablesCount = quantimodoVariables[settingsCurrentVariable.category].length;
		if ('joinedVariables' in settingsCurrentVariable)
		{
			var joinedVariablesCount = settingsCurrentVariable['joinedVariables'].length;
		}
		else
		{
			var joinedVariablesCount = 0;
		}
		var foundVariable = false;
		for(var n = 0; n < variablesCount; n++)
		{
			currentVariable = quantimodoVariables[settingsCurrentVariable.category][n];
			if (currentVariable.id == settingsCurrentVariable.id)	// If this is the current variable skip it
			{
				continue;
			}
			var isJoinedVariable = false;
			for(var i = 0; i < joinedVariablesCount; i++)
			{
				if (currentVariable.id == settingsCurrentVariable['joinedVariables'][i].id)
				{
					isJoinedVariable = true;
				}
			}
			if (!isJoinedVariable)	// If this variable is joined with the one currently being edited, skip it
			{
				jQuery('#joinedVariablePicker').append(jQuery('<option/>').attr('value', currentVariable.originalName).text(currentVariable.name));
			}
		}

		// Generate list of joined variables
		jQuery('#joinedVariablesList').empty();
		for(var i = 0; i < joinedVariablesCount; i++)
		{
			currentVariable = settingsCurrentVariable['joinedVariables'][i];
			jQuery('#joinedVariablesList').append('<li value="' + currentVariable.originalName + '">' + currentVariable.name + '<div></div></li>');
		}

		// Set current values
		jQuery("#input-variable-name").val("");
        jQuery("#input-variable-name").attr('placeholder', settingsCurrentVariable.name);
        jQuery("#selectVariableUnitSetting").val(settingsCurrentVariable.unit);
        jQuery("#selectVariableCategorySetting").val(settingsCurrentVariable.category);
		jQuery("#variableMinimumValueSetting").val("");
		if (settingsCurrentVariable.minimumValue == -Infinity)
		{
			jQuery("#variableMinimumValueSetting").attr('placeholder', "-Infinity");
		}
		else
		{
			jQuery("#variableMinimumValueSetting").attr('placeholder', settingsCurrentVariable.minimumValue);
		}
		jQuery("#variableMaximumValueSetting").val("");
		if (settingsCurrentVariable.maximumValue == Infinity)
		{
			jQuery("#variableMaximumValueSetting").attr('placeholder', "Infinity");
		}
		else
		{
			jQuery("#variableMaximumValueSetting").attr('placeholder', settingsCurrentVariable.maximumValue);
		}
		if (settingsCurrentVariable.fillingValue != null)
		{
			jQuery("#variableFillingValueSetting").val(settingsCurrentVariable.fillingValue);
			jQuery("#assumeValue").prop("checked", true)
		}
		else
		{
			jQuery("#assumeMissing").prop("checked", true)
		}

		// Open div
		toggleElement("#section-configure-input");
		toggleElement("#section-configure-settings");
		setTimeout(function()
		{
			toggleElement("#accordion-settings-content");
		}, 300);

	};
	var hideVariableSettings = function(saveSettings)
	{
		if (saveSettings)
		{
			var allNewSettings = []; // Holds settings for all variables that are changed this "session"
			var newSettings = {"variable":settingsCurrentVariable.originalName};

			var newUnit = jQuery("#selectVariableUnitSetting").val();
			if (newUnit != settingsCurrentVariable.unit)
			{
				newSettings['unit'] = newUnit;
			}

			var newName = jQuery("#input-variable-name").val();
			if (newName != null && newName.length > 0 && newName != settingsCurrentVariable.name)
			{
				if (newName == settingsCurrentVariable.originalName)
				{
					newSettings['name'] = newName;
				}
				else
				{
					newSettings['name'] = newName;
				}
			}

			var assumeMissingChecked = jQuery("#assumeMissing").prop("checked");
			var assumeValueChecked = jQuery("#assumeValue").prop("checked");
			if (assumeMissingChecked)
			{
				if (settingsCurrentVariable.fillingValue != null)
				{
					newSettings['fillingValue'] = null;
				}
			}
			else if (assumeValueChecked)
			{
				var newFillingValue = parseInt(jQuery("#variableFillingValueSetting").val());
				if (newFillingValue != settingsCurrentVariable.fillingValue)
				{
					if (newFillingValue == null || newFillingValue.length == 0)
					{
						newSettings['fillingValue'] = null;
					}
					else if (!isNaN(newFillingValue))
					{
						newSettings['fillingValue'] = newFillingValue;
					}
					else
					{
						alert("Invalid filling value, must be a number.");
						return;
					}
				}
			}

			var newMinimumValueStr = jQuery("#variableMinimumValueSetting").val()
			var newMinimumValue = parseInt(newMinimumValueStr);
			if (newMinimumValueStr != null && newMinimumValueStr.length > 0 && newMinimumValue != settingsCurrentVariable.minimumValue)
			{
				if (!isNaN(newMinimumValue))
				{
					if (newMinimumValue == Infinity || newMinimumValue == -Infinity)
					{
						newSettings['minimumValue'] = "-Infinity";
					}
					else
					{
						newSettings['minimumValue'] = newMinimumValue;
					}
				}
				else
				{
					alert("Invalid minimum value, must be a number, \"-Infinity\" or \"Infinity\"");
					return;
				}
			}

			var newMaximumValueStr = jQuery("#variableMaximumValueSetting").val()
			var newMaximumValue = parseInt(newMaximumValueStr);
			if (newMaximumValueStr != null && newMaximumValueStr.length > 0 && newMaximumValue != settingsCurrentVariable.maximumValue)
			{
				if (!isNaN(newMaximumValue))
				{
					if (newMaximumValue == Infinity || newMaximumValue == -Infinity)
					{
						newSettings['maximumValue'] = "Infinity";
					}
					else
					{
						newSettings['maximumValue'] = newMaximumValue;
					}
					newSettings['maximumValue'] = newMaximumValue;
				}
				else
				{
					alert("Invalid maximum value, must be a number, \"-Infinity\" or \"Infinity\"");
					return;
				}
			}

			// Bunch of arrays to make the code below a bit more intuitive
			var newListedVariables = [];
			var currentJoinedVariables = [];
			// Get all listed variables
			jQuery('#joinedVariablesList > li').each(function ()
			{
				newListedVariables.push(this.getAttribute("value"));
			});
			if ('joinedVariables' in settingsCurrentVariable)
			{
				// Get all previously joined variables
				var currentJoinedVariablesCount = settingsCurrentVariable['joinedVariables'].length;
				for(var i = 0; i < currentJoinedVariablesCount; i++)
				{
					currentJoinedVariables.push(settingsCurrentVariable['joinedVariables'][i].originalName);
				}

				// Loop through previously joined variables
				for(var i = 0; i < currentJoinedVariables.length; i++)
				{
					var hasMatch = false;
					// Loop through new listed variables
					for(var n = 0; n < newListedVariables.length; n++)
					{
						if (currentJoinedVariables[i] == newListedVariables[n])	// If we have a match the variable is still joined
						{
							hasMatch = true;
							newListedVariables.splice(n, 1);	// Remove the listed variable from the array, so that we can loop through the remaining ones later
							break;
						}
					}
					if (!hasMatch)	// No match, the variable was joined, but is no longer listed
					{
						allNewSettings.push({"variable":currentJoinedVariables[i], "joinWith":null});	// Create a settings object to undo the joining
					}
				}
				// Loop through the remaining listed variables, those we matched before are removed from the array, so those unprocessed remain
				for(var i = 0; i < newListedVariables.length; i++)
				{
					var hasMatch = false;
					for(var n = 0; n < currentJoinedVariables.length; n++)
					{
						if (currentJoinedVariables[i] == newListedVariables[n])	// If we have a match the variable is still joined
						{
							hasMatch = true;
							break;
						}
					}
					if (!hasMatch)	// No match, the variable is listed, but wasn't joined before
					{
						allNewSettings.push({"variable":newListedVariables[i], "joinWith":settingsCurrentVariable.originalName});
					}
				}
			}
			else
			{
				// There were no joined variables before, so all listed variables are new
				for(var i = 0; i < newListedVariables.length; i++)
				{
					allNewSettings.push({"variable":newListedVariables[i], "joinWith":settingsCurrentVariable.originalName});
				}
			}

			// If attributes of this variable have changed, add the settings object to our array
			if (Object.keys(newSettings).length > 1)
			{
				allNewSettings.push(newSettings);
			}

			// If there are new settings
			if (allNewSettings.length > 0)
			{
				// Show loading overlay
				jQuery("#accordion-settings-content").css('opacity', 0.5);
				showLoadingOverlay("#accordion-settings-content");

				// Start saving variable settings (with a callback once it's done)
				saveVariableSettings(allNewSettings, function()
				{
					// Hide settings div, show input settings
					toggleElement("#accordion-settings-content");
					setTimeout(function()
					{
						toggleElement("#section-configure-input");
						toggleElement("#section-configure-settings");
						hideLoadingOverlay("#accordion-settings-content");
						setTimeout(function()
						{
							jQuery("#accordion-settings-content").css('opacity', 1);
						}, 500);
					}, 300);
					settingsCurrentVariable = null
				});
				return;
			}
		}

		toggleElement("#accordion-settings-content");
		setTimeout(function()
		{
			toggleElement("#section-configure-input");
			toggleElement("#section-configure-settings");
		}, 300);
	};

	var saveVariableSettings = function(newSettings, onDoneListener)
	{
		Quantimodo.postVariableUserSettings(newSettings, function()
		{
			refreshVariables();	//TODO replace this with something that updates the variables locally, since this triggers
			onDoneListener();
		});
	}

	// Requires div with .loading-overlay in element
	var showLoadingOverlay = function(element)
	{
		jQuery(element + " .loading-overlay").css('display', 'table-cell');
		jQuery(element + " .loading-overlay").css('opacity', 0.5);
	}
	var hideLoadingOverlay = function(element)
	{
		jQuery(element + " .loading-overlay").css('opacity', 0);
		setTimeout(function()
		{
			jQuery(element + " .loading-overlay").css('display', 'none');
		}, 500);
	}

	var initVariableSelectors = function()
	{
		jQuery('#selectInputCategory').change(inputCategoryUpdated);
		jQuery('#selectOutputCategory').change(outputCategoryUpdated);
	 	jQuery('#selectInputVariable').change(inputVariableUpdated);
		jQuery('#selectOutputVariable').change(outputVariableUpdated);
	};

	var refreshUnits = function()
	{
		Quantimodo.getUnits({}, function(units)
		{
			jQuery.each(units, function(_, unit)
			{
				var category = quantimodoUnits[unit.category];
				if (category === undefined)
				{
					quantimodoUnits[unit.category] = [unit];
				}
				else
				{
					category.push(unit);
				}
			});
			jQuery.each(Object.keys(quantimodoUnits), function(_, category)
			{
				quantimodoUnits[category] = quantimodoUnits[category].sort();
			});

			unitListUpdated();
		});
	};

	var refreshVariables = function(variables)
	{

 		Quantimodo.getVariables({}, function(variables)
		{
			storedLastInputVariableName = window.localStorage['lastInputVariableName'];
	 		storedLastOutputVariableName = window.localStorage['lastOutputVariableName'];
			quantimodoVariables = {};
			jQuery.each(variables, function(_, variable)
			{
				if(variable.originalName == storedLastInputVariableName)
				{
					lastInputVariable = variable;
				}
				else if(variable.originalName == storedLastOutputVariableName)
				{
					lastOutputVariable = variable;
				}
				var category = quantimodoVariables[variable.category];
				if (category === undefined)
				{
					quantimodoVariables[variable.category] = [variable];
				}
				else
				{
					category.push(variable);
				}
			});
			jQuery.each(Object.keys(quantimodoVariables), function(_, category)
			{
				quantimodoVariables[category] = quantimodoVariables[category].sort(function(a, b)
				{
					return a.name.toLowerCase().localeCompare(b.name.toLowerCase());
				});
			});

			categoryListUpdated();
		  	inputCategoryUpdated();
			outputCategoryUpdated();
		});
	};

	var refreshInputData = function()
	{
		var variable = AnalyzePage.getInputVariable();
		Quantimodo.getMeasurements(
		{
			'variableName': variable.originalName,
			'startTime': AnalyzePage.getStartTime(),
			'endTime': AnalyzePage.getEndTime(),
			'groupingWidth': AnalyzePage.getPeriod(),
			'groupingTimezone': AnalyzePage.getTimezone()
		},
		function(measurements) { inputMeasurements = measurements; AnalyzeChart.setInputData(variable, measurements); });
	};

	var refreshOutputData = function()
	{
		var variable = AnalyzePage.getOutputVariable();
		Quantimodo.getMeasurements(
		{
			'variableName': variable.originalName,
			'startTime': AnalyzePage.getStartTime(),
			'endTime': AnalyzePage.getEndTime(),
			'groupingWidth': AnalyzePage.getPeriod(),
			'groupingTimezone': AnalyzePage.getTimezone()
		},
		function(measurements) { outputMeasurements = measurements; AnalyzeChart.setOutputData(variable, measurements); });
	};

	var lastStartTime = null;
	var startDateUpdated = function()
	{
		var newStartTime = AnalyzePage.getStartTime();
		if (newStartTime !== lastStartTime)
		{
			lastStartTime = newStartTime;
			refreshInputData();
			refreshOutputData();
		}
	};

	var lastEndTime = null;
	var endDateUpdated = function()
	{
		var newEndTime = AnalyzePage.getEndTime();
		if (newEndTime !== lastEndTime)
		{
			lastEndTime = newEndTime;
			refreshInputData();
			refreshOutputData();
		}
	};

	var bothDatesUpdated = function()
	{
		var newStartTime = AnalyzePage.getStartTime();
		var newEndTime = AnalyzePage.getEndTime();
		if ((newStartTime !== lastStartTime) || (newEndTime !== lastEndTime))
		{
			lastStartTime = newStartTime;
			lastEndTime = newEndTime;
			refreshInputData();
			refreshOutputData();
		}
	};

	var lastPeriod = 1;
	var periodUpdated = function()
	{
		var newPeriod = AnalyzePage.getPeriod();
		if (newPeriod !== lastPeriod)
		{
			lastPeriod = newPeriod;
			refreshInputData();
			refreshOutputData();
		}
	};

	var categoryListUpdated = function()
	{
		jQuery('#selectOutputCategory').empty();
		jQuery('#selectVariableCategorySetting').empty();
		jQuery.each(Object.keys(quantimodoVariables).sort(function(a, b)
		{
			return a.toLowerCase().localeCompare(b.toLowerCase());
		}), function(_, category)
		{

			if(lastInputVariable != null  && lastInputVariable.category == category)
			{
				jQuery('#selectInputCategory').append(jQuery('<option/>').attr('selected', 'selected').attr('value', category).text(category));
 			}
			else
			{
				jQuery('#selectInputCategory').append(jQuery('<option/>').attr('value', category).text(category));
			}
			//output category set values
			if(lastOutputVariable != null  && lastOutputVariable.category == category)
			{
				jQuery('#selectOutputCategory').append(jQuery('<option/>').attr('selected', 'selected').attr('value', category).text(category));
 			}
			else
			{
				jQuery('#selectOutputCategory').append(jQuery('<option/>').attr('value', category).text(category));
			}


			//jQuery('#selectOutputCategory').append(jQuery('<option/>').attr('value', category).text(category));
			jQuery('#selectVariableCategorySetting').append(jQuery('<option/>').attr('value', category).text(category));
		});
 			 
	};

	var unitListUpdated = function()
	{
	};

	var lastInputCategory = null;
	var inputCategoryUpdated = function()
	{
		var newInputCategory = AnalyzePage.getInputCategory();

		jQuery('#selectInputVariable').empty();
		jQuery.each(quantimodoVariables[newInputCategory], function(_, variable)
		{
			if (variable.name == variable.originalName)
 			{
 				if(lastInputVariable != null && lastInputVariable.originalName == variable.originalName)
 				{
 
					jQuery('#selectInputVariable').append(jQuery('<option/>').attr('selected', 'selected').attr('value', variable.originalName).text(variable.name));
 					jQuery("#selectInputVariable").change();

 				}
 				else
					jQuery('#selectInputVariable').append(jQuery('<option/>').attr('value', variable.originalName).text(variable.name));
			}
		});

		lastInputCategory = newInputCategory;
	 	inputVariableUpdated();
		//keep default state refreshed
		refreshInputData(); // added

	};

	var lastInputVariable = null;
	var inputVariableUpdated = function()
	{

		var newInputVariable = AnalyzePage.getInputVariable();
		if (newInputVariable !== lastInputVariable)
		{
			refreshInputData();
			lastInputVariable = newInputVariable;
			saveSetting('lastInputVariableName', lastInputVariable.originalName);
		}
	};

	var lastOutputCategory = null;
	var outputCategoryUpdated = function()
	{
		var newOutputCategory = AnalyzePage.getOutputCategory();

		jQuery('#selectOutputVariable').empty();
		jQuery.each(quantimodoVariables[newOutputCategory], function(_, variable)
		{
			if (variable.name == variable.originalName)
 			{
 				if(lastOutputVariable != null && lastOutputVariable.originalName == variable.originalName)
 				{
 
					jQuery('#selectOutputVariable').append(jQuery('<option/>').attr('selected', 'selected').attr('value', variable.originalName).text(variable.name));

 				}
 				else
					jQuery('#selectOutputVariable').append(jQuery('<option/>').attr('value', variable.originalName).text(variable.name));
			}


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
	var outputVariableUpdated = function() {
		var newOutputVariable = AnalyzePage.getOutputVariable();
		if (newOutputVariable !== lastOutputVariable) {
			refreshOutputData();
			lastOutputVariable = newOutputVariable;
			saveSetting('lastOutputVariableName', lastOutputVariable.originalName);

		}
	};

	var retrieveSettings = function()
	{
		if (typeof(Storage)!=="undefined")
		{
			dateSelectorVisible = (localStorage["dateSelectorVisible"] || "true") == "true" ? true : false;
			inputSelectorVisible = (localStorage["inputSelectorVisible"] || "true") == "true" ? true : false;
			outputSelectorVisible = (localStorage["outputSelectorVisible"] || "true") == "true" ? true : false;
		}
	};
	
	var saveSetting = function(setting, value)
	{
		if (typeof(Storage)!=="undefined")
		{
			localStorage[setting] = value;
		}
	};

	var initSharing = function()
	{
		jQuery("#share-dialog #button-doshare").click(function()
		{
			showLoadingOverlay("#share-dialog");

			share(function(id)
			{
				hideLoadingOverlay("#share-dialog");

				var url = location.href;						// Get current URL
				if (url.substr(-1) != '/')  url = url + '/';	// Append trailing slash if not exists
				jQuery("#share-dialog").append(url + 'shared/?data=' + id);	// Return share URL
			});
		});
		jQuery("#share-dialog #button-cancelshare").click(function()
		{
			jQuery("#share-dialog-background")  .toggleClass("active");
			jQuery("#share-dialog")             .toggleClass("active");
		});

		jQuery('#shareTimeline').click(function(){
			showShareDialog();
		});
		jQuery('#shareScatterplot').click(function(){
			showShareDialog();
		});
		jQuery('#shareCorrelationGauge').click(function(){
			showShareDialog();
		});
	}

	var showShareDialog = function()
	{
		jQuery("#share-dialog-background")  .toggleClass("active");
		jQuery("#share-dialog")             .toggleClass("active");
		
		share(function(id)
			{
				hideLoadingOverlay("#share-dialog");

				var url = location.href;						// Get current URL
				if (url.substr(-1) != '/')  url = url + '/';	// Append trailing slash if not exists

				jQuery("#share-linkcontainer").val(url + 'shared/?data=' + id)
				jQuery("#share-linkcontainer").select();
			});
	}

	var share = function(onDoneListener)
	{
		var shareObject = {	'type':'correlate',
							'inputVariable': 	lastInputVariable.originalName,
							'outputVariable': 	lastOutputVariable.originalName,
							'startTime': 		AnalyzePage.getStartTime(),
							'endTime': 			AnalyzePage.getEndTime(),
							'groupingWidth': 	AnalyzePage.getPeriod(),
							'groupingTimezone': AnalyzePage.getTimezone()};
		Quantimodo.postCorrelateShare(shareObject, function(response)
		{
			onDoneListener(response['id']);
		});
	};

	return {
		getTimezone:       function() { return timezone; },
		getStartTime:      function() { return Math.floor(dateRangeStart.getTime() / 1000); },
		getEndTime:        function() { return Math.floor(dateRangeEnd.getTime() / 1000); },
		getInputCategory:  function() { return jQuery('#selectInputCategory :selected').text(); },
		getOutputCategory: function() { return jQuery('#selectOutputCategory :selected').text(); },
		getInputVariable:  function() {
											var categoryName = jQuery('#selectInputCategory :selected').val();
											var variableName = jQuery('#selectInputVariable :selected').val();
											var wantedVariable;
											jQuery.each(quantimodoVariables[categoryName], function(_, variable)
											{
												if (variable.name == variableName)
												{
													wantedVariable = variable;
													return;
												}
											});
											return wantedVariable;
									  },
		getOutputVariable: function() {
											var categoryName = jQuery('#selectOutputCategory :selected').val();
											var variableName = jQuery('#selectOutputVariable :selected').val();
											var wantedVariable;
											jQuery.each(quantimodoVariables[categoryName], function(_, variable)
											{
												if (variable.name == variableName)
												{
													wantedVariable = variable;
													return;
												}
											});
											return wantedVariable;
									  },
		getPeriod:         function() {
			switch (jQuery('#accordion-content-rangepickers :radio:checked + label').text())
			{
				case 'Second': return 1;
				case 'Minute': return 60;
				case 'Hour':   return 3600;
				case 'Day':    return 86400;
				case 'Week':   return 604800;
				case 'Month':  return 2628000;
			}
		},

		init: function()
		{
			retrieveSettings();
			initAccordion();
			initDateRangeSelector();
			initVariableSelectors();
			initVariableSettings();
			initLoginDialog();
			initSharing();

		},
		hideScatterplot: function() { if (scatterplotVisible) {toggleElement('#scatterplot-graph'); scatterplotVisible = false;} },
		showScatterplot: function() { if (!scatterplotVisible) {toggleElement('#scatterplot-graph'); scatterplotVisible = true} },
		hideCorrelationGauge: function() { if (correlationGaugeVisible) {toggleElement('#correlation-gauge'); correlationGaugeVisible = false;} },
		showCorrelationGauge: function() { if (!correlationGaugeVisible) {toggleElement('#correlation-gauge'); correlationGaugeVisible = true} },
	};
}();

jQuery(AnalyzePage.init);

function toggleElement(element)
{
	var content = jQuery(element);
	content.inner = jQuery(element + ' .inner');

	content.on('transitionEnd webkitTransitionEnd transitionend oTransitionEnd msTransitionEnd', function(e)
	{
		if (content.hasClass('open'))
		{
			content.css('max-height', 9999);
		}
	});

	content.toggleClass('open closed');
    content.contentHeight = content.outerHeight();

	if (content.hasClass('closed'))
	{
        content.removeClass('transitions').css('max-height', content.contentHeight);
        setTimeout(function()
        {
            content.addClass('transitions').css(
            {
                'max-height': 0
            });
        }, 10);
    }
	else if (content.hasClass('open'))
	{
        content.contentHeight += content.inner.outerHeight();
        content.addClass('transitions').css(
		{
            'max-height': content.contentHeight
        });
    }
}