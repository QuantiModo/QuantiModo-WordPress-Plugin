var VariablePicker = function() 
{
	var filterQuery = "";

	var lastSelectedVariable;

	/*
	**	Initialize onclicks, options, etc
	*/
	var init = function(options, selectedCallback)
	{
		if (options) 
		{
			jQuery.extend(VariablePicker.params, options);
		}

		VariablePicker.variableSelectedCallback = selectedCallback;
		jQuery("#variablePickerFilter").on('keyup', onFilterQueryChanged);
	};

	var refresh = function()
	{
		var variablePickerListElement = jQuery("#variablePickerList");
		variablePickerListElement.empty();

		jQuery('#selectOutputCategory').empty();
		jQuery('#selectVariableCategorySetting').empty();

		jQuery.each(Object.keys(AnalyzePage.quantimodoVariables).sort(function(a, b)
		{
			return a.toLowerCase().localeCompare(b.toLowerCase());
		}), function(_, category)
		{
			// Add the category header
			variablePickerListElement.append(
				jQuery('<li/>')
					.addClass('variablePickerCategory')
					.text(category
			));

			// Loop through all variables in this category
			jQuery.each(AnalyzePage.quantimodoVariables[category], function(_, variable)
			{
				// If the variable "matches"
				if (filterQuery == "" || variable.name.toLowerCase().indexOf(filterQuery) != -1)
				{
					// Add the variable element
					variablePickerListElement.append(
						jQuery('<li/>')
							.on('click', onVariablePicked)
							.attr('category', category)
							.attr('originalName', variable.originalName)
							.text(variable.name)

					);
				}
			});
		});
	};

	/**********************
	**	LISTENERS BELOW  **
	***********************/
	var onFilterQueryChanged = function()
	{
		filterQuery = jQuery(this).val().toLowerCase();
		VariablePicker.refresh();
	};

	var onVariablePicked = function()
	{
		var category = jQuery(this).attr('category');
		var originalName = jQuery(this).attr('originalName');
		jQuery.each(AnalyzePage.quantimodoVariables[category], function(_, variable)
		{
			if(variable.originalName == originalName)
			{
				VariablePicker.params.variablePickedCallback(variable);
				return;
			}
		});
	}

	// Return public stuffs
	return{
			params : {
				variablePickedCallback : function() {}
			},

			init : init,
			refresh : refresh
		}
}();