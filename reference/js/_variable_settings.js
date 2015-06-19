var variableSettings = {
	current : null, // Variable currently "open" in settings
	params : {
		saveCallback : function() {}
	},
	init : function(options) {
		
		if (options) 
		{
			jQuery.extend(variableSettings.params, options);
		}

		// Activate cancel/save buttons on variable-settings
		jQuery(document).on('click', '#accordion-settings-content .inner .button-save', function() {
			variableSettings.hide(true);
		});
		jQuery(document).on('click', '#accordion-settings-content .inner .button-cancel', function() {
			variableSettings.hide(false);
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


				jQuery( '#joinedVariablesList' ).append('<li value="' + selectedOriginalName + '">' + selectedName + '<div></div></li>');


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
	},

	show : function(variable) {
		// Init and checks
	    if (variable == null)
	    {
	        alert("No variable selected");
	        return;
	    }
		variableSettings.current = variable;

		//jQuery('#variableOnsetDelayValueSetting').attr('placeholder', variableSettings.current.onsetDelay);
		//jQuery('#variableDurationOfActionValueSetting').attr('placeholder', variableSettings.current.durationOfAction);
		jQuery('#variableOnsetDelayValueSetting').val(Math.round(variableSettings.current.onsetDelay / 3600));
		jQuery('#variableDurationOfActionValueSetting').val(Math.round(variableSettings.current.durationOfAction / 3600));
		
		// Fill unit selector
		jQuery('#selectVariableUnitSetting').empty();
		var categories = Object.keys(AnalyzePage.quantimodoUnits);
		var currentCategory, currentUnit;
		var foundUnit = false;
		var count = categories.length, innerCount;
		for(var i = 0; i < count; i++)
		{
			currentCategory = AnalyzePage.quantimodoUnits[categories[i]];
			innerCount = currentCategory.length;
			for(var n = 0; n < innerCount; n++)
			{
				currentUnit = currentCategory[n];
				if (foundUnit)	// If foundUnit = true we're in the right category, so start adding values
				{
					jQuery('#selectVariableUnitSetting').append(jQuery('<option/>').attr('value', currentUnit.abbreviatedName).text(currentUnit.name));
				}
				else if (currentUnit.abbreviatedName == variableSettings.current.unit)
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
		var variablesCount = AnalyzePage.quantimodoVariables[variableSettings.current.category].length;
		if ('joinedVariables' in variableSettings.current)
		{
			var joinedVariablesCount = variableSettings.current['joinedVariables'].length;
		}
		else
		{
			var joinedVariablesCount = 0;
		}
		var foundVariable = false;
		for(var n = 0; n < variablesCount; n++)
		{
			currentVariable = AnalyzePage.quantimodoVariables[variableSettings.current.category][n];
			if (currentVariable.id == variableSettings.current.id)	// If this is the current variable skip it
			{
				continue;
			}
			var isJoinedVariable = false;
			for(var i = 0; i < joinedVariablesCount; i++)
			{
				if (currentVariable.id == variableSettings.current['joinedVariables'][i].id)
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
			currentVariable = variableSettings.current['joinedVariables'][i];
			jQuery('#joinedVariablesList').append('<li value="' + currentVariable.originalName + '">' + currentVariable.name + '<div></div></li>');
		}

		// Set current values
		jQuery("#input-variable-name").val(variableSettings.current.name);
	            jQuery("#input-variable-id").val(variableSettings.current.id);
	    //jQuery("#input-variable-name").attr('placeholder', variableSettings.current.name);
		jQuery("#selectVariableUnitSetting").val(variableSettings.current.unit);
	    jQuery("#selectVariableCategorySetting").val(variableSettings.current.category);
		
		jQuery('#unitForMinValue').text(variableSettings.current.unit);
		if (variableSettings.current.minimumValue == -Infinity)
		{
			jQuery("#variableMinimumValueSetting").val("-Infinity");
		}
		else
		{
			jQuery("#variableMinimumValueSetting").val(variableSettings.current.minimumValue);
		}
		
		jQuery('#unitForMaxValue').text(variableSettings.current.unit);
		if (variableSettings.current.maximumValue == Infinity)
		{
			jQuery("#variableMaximumValueSetting").val("Infinity");
		}
		else
		{
			jQuery("#variableMaximumValueSetting").val(variableSettings.current.maximumValue);
		}
                //reset filling value before setting anything
                jQuery("#variableFillingValueSetting").val('');
                jQuery("#assumeValue").prop("checked", false);
                jQuery("#assumeMissing").prop("checked", false);
		if (variableSettings.current.fillingValue != null)
		{
			jQuery("#variableFillingValueSetting").val(variableSettings.current.fillingValue);
			jQuery("#assumeValue").prop("checked", true);
		}
		else
		{
			jQuery("#assumeMissing").prop("checked", true);
		}

		var settingsBox = jQuery('#section-configure-settings');
		jQuery.fancybox(settingsBox, {
			closeBtn: true,
			helpers: {
				overlay: {
					closeClick : false
				}
			},
			keys : {
				close  : null
			}
		});

	},

	hide : function(saveSettings) {
		if (saveSettings)
		{
			var allNewSettings = []; // Holds settings for all variables that are changed this "session"
			var newSettings = {"variable":variableSettings.current.originalName};

			var newUnit = jQuery("#selectVariableUnitSetting").val();
			if (newUnit != variableSettings.current.unit)
			{
				newSettings['unit'] = newUnit;
			}

			var newName = jQuery("#input-variable-name").val();
			if (newName != null && newName.length > 0 && newName != variableSettings.current.name)
			{
				if (newName == variableSettings.current.originalName)
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
				if (variableSettings.current.fillingValue != null)
				{
					newSettings['fillingValue'] = null;
				}
			}
			else if (assumeValueChecked)
			{
				var newFillingValue = parseFloat(jQuery("#variableFillingValueSetting").val());
				if (newFillingValue != variableSettings.current.fillingValue)
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
			
			var newMinimumValue = parseFloat(newMinimumValueStr);
			if (newMinimumValueStr == "-Infinity" || newMinimumValueStr == "Infinity" || newMinimumValueStr == "") newMinimumValue = -Infinity;
			if (newMinimumValue != null && newMinimumValue != variableSettings.current.minimumValue)
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
			var newMaximumValue = parseFloat(newMaximumValueStr);
			if (newMaximumValueStr == "-Infinity" || newMaximumValueStr == "Infinity" || newMaximumValueStr == "") newMaximumValue = Infinity;
			if (newMaximumValue != null && newMaximumValue != variableSettings.current.maximumValue)
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
				}
				else
				{
					alert("Invalid maximum value, must be a number, \"-Infinity\" or \"Infinity\"");
					return;
				}
			}

			var newonsetDelayValueStr = jQuery("#variableOnsetDelayValueSetting").val()
			var newonsetDelayValue = parseFloat(newonsetDelayValueStr) * 3600;
			if (newonsetDelayValueStr != null && newonsetDelayValue != variableSettings.current.onsetDelay)
			{
				if (!isNaN(newonsetDelayValue))
				{
					if (newonsetDelayValue == Infinity || newonsetDelayValue == -Infinity)
					{
						newSettings['onsetDelay'] = "Infinity";
					}
					else
					{
						newSettings['onsetDelay'] = newonsetDelayValue;
					}
					newSettings['onsetDelay'] = newonsetDelayValue;
				}
				else
				{
					alert("Invalid onset delay value, must be a number. \nCan't be null");
					return;
				}
			}
			
			var newdurationOfActionValueStr = jQuery("#variableDurationOfActionValueSetting").val()
			var newdurationOfActionValue = parseFloat(newdurationOfActionValueStr)  * 3600;
			if (newdurationOfActionValueStr != null && newdurationOfActionValue != variableSettings.current.durationOfAction)
			{
				if (!isNaN(newdurationOfActionValue))
				{
					if (newdurationOfActionValue == Infinity || newdurationOfActionValue == -Infinity)
					{
						newSettings['durationOfAction'] = "Infinity";
					}
					else
					{
						newSettings['durationOfAction'] = newdurationOfActionValue;
					}
					newSettings['durationOfAction'] = newdurationOfActionValue;
				}
				else
				{
					alert("Invalid duration of action value, must be a number. \nCan't be null");
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
			if ('joinedVariables' in variableSettings.current)
			{
				// Get all previously joined variables
				var currentJoinedVariablesCount = variableSettings.current['joinedVariables'].length;
				for(var i = 0; i < currentJoinedVariablesCount; i++)
				{
					currentJoinedVariables.push(variableSettings.current['joinedVariables'][i].originalName);
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
						allNewSettings.push({"variable":newListedVariables[i], "joinWith":variableSettings.current.originalName});
					}
				}
			}
			else
			{
				// There were no joined variables before, so all listed variables are new
				for(var i = 0; i < newListedVariables.length; i++)
				{
					allNewSettings.push({"variable":newListedVariables[i], "joinWith":variableSettings.current.originalName});
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
				variableSettings.save(allNewSettings, function() {
					hideLoadingOverlay("#accordion-settings-content");
					jQuery("#accordion-settings-content").css('opacity', 1);
					jQuery.fancybox.close();

					variableSettings.current = null;
				});
				return;
			}
		}

		jQuery.fancybox.close();
	},

	save : function(newSettings, onDoneListener) {
		Quantimodo.postVariableUserSettings(newSettings, function()
		{
			variableSettings.params.saveCallback();
			onDoneListener();
		});
	}
};
