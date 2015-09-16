var variables = [];
var units = [];

var setBlockHideShow = function () {

    jQuery('#pickDate').click(function () {
        jQuery('#addmeasurement-variable-date').datetimepicker('show'); //support hide,show and destroy command
    });

    jQuery('#add-pickDate').click(function () {
        jQuery('#add-addmeasurement-variable-date').datetimepicker('show'); //support hide,show and destroy command
    });

    jQuery("#signup_block").hide();
    jQuery("#record_a_measurement_block").hide();
    jQuery("#edt_record_a_measurement_block").hide();
    jQuery("#add_record_a_measurement_block").hide();


    if (accessToken) {
        jQuery("#record_a_measurement_block").show();
    } else {
        jQuery("#signup_block").show();
    }


}

var setButtonListeners = function () {

    document.getElementById('button-record-a-measurement').onclick = onQmRcdMstButtonClicked;

    document.getElementById('button-edit-record-a-measurement').onclick = onEdtButtonClicked;
    document.getElementById('button-add-record-a-measurement').onclick = onAddButtonClicked;

};

/**
 * Record a measurement is clicked
 */
var onQmRcdMstButtonClicked = function () {
    var name = jQuery('#addmeasurement-variable-name').val();

    if (name === '') {

        jQuery('.validation-holder span').text('Please enter the variable name');
        jQuery('#addmeasurement-variable-name').addClass('error');

        return;
    } else {

        jQuery('.validation-holder').text('');
        jQuery('#addmeasurement-variable-name').removeClass('error');

    }

    var n_value = getVariableWithName(name);

    if (n_value == null) {
        jQuery('#record_a_measurement_block').hide();
        jQuery('#add_record_a_measurement_block').show();
        jQuery('#add-addmeasurement-variable-name').val(name);

    }
    else {
        jQuery('#record_a_measurement_block').hide();
        jQuery('#edt_record_a_measurement_block').show();
        jQuery('#edt-addmeasurement-variable-name').val(name);

    }

}

// Simple Sign In button clicked
var onQmSignButtonClicked = function () {
    window.location.href = '?connect=quantimodo';
};

var onCloseButtonClicked = function () {
    window.close();
};


/**
 * Desc:
 */

var onEdtButtonClicked = function () {
    // Create an array of measurements
    var name = jQuery('#edt-addmeasurement-variable-name').val();
    var unit = jQuery('#addmeasurement-variable-unit').val();
    var value = jQuery('#addmeasurement-variable-value').val();
    var valueCategory = jQuery('#addmeasurement-variable-category').val();
    var combineOp = jQuery('#combineOperation').val();
    var datetimeString = jQuery('#addmeasurement-variable-date').val();

    datetimeString = datetimeString.replace('AM', '');
    datetimeString = datetimeString.replace('PM', '');

    var hour = jQuery('#addmeasurement-variable-timeh').val();
    var min = jQuery('#addmeasurement-variable-timem').val();
    var ap = jQuery('#addmeasurement-variable-timeap').val();
    var datetime = new Date(datetimeString);

    /*
     alert (datetime) ;
     alert (Math.floor(datetime.getTime()  / 1000));
     /*datetime.setHours(parseInt(hour) + (ap * 12));
     datetime.setMinutes(min);
     datetime.setSeconds(0);
     */

    if (name === '') {
        alert('Please enter the variable name.');
        return;
    }
    if (value == '') {
        alert('Please enter the value.');
        return;
    }

    //var variable = getVariableWithName(name);
    var measurements = [
        {
            timestamp: Math.floor(datetime.getTime() / 1000),
            value: value
        }
    ];
    //alert ( measurements ) ;
    // Add it to a request, payload is what we'll send to QuantiModo
    var request = {
        message: 'uploadMeasurements',
        payload: [
            {
                measurements: measurements,
                name: name,
                source: 'QuantiModo',
                category: valueCategory,
                combinationOperation: combineOp,
                unit: unit
            }
        ]

    };

    Quantimodo.postMeasurementsV2(request.payload, function (responseText) {
        var response = responseText;
        //alert (response) ;
        if (response.success === true) {
            //save measurement to pre-populate this values next time
            localCache.setSubmittedMeasurement(name, value, unit);
            jQuery('#addmeasurement-variable-name').val('');
            setBlockHideShow();
            alert('Measurement have been posted successfully');
        }
        else {
            alert('Adding a measurement failed.');
            console.log(responseText);
        }
    });

};

var onAddButtonClicked = function () {
    // Create an array of measurements
    var name = jQuery('#add-addmeasurement-variable-name').val();
    var unit = jQuery('#add-addmeasurement-variable-unit').val();
    var value = jQuery('#add-addmeasurement-variable-value').val();
    var valueCategory = jQuery('#addmeasurement-variable-category').val();
    var combineOp = jQuery('#combineOperation').val();
    var datetimeString = jQuery('#add-addmeasurement-variable-date').val();

    //alert (Date.parse(datetimeString)) ;
    datetimeString = datetimeString.replace('AM', '');
    datetimeString = datetimeString.replace('PM', '');
    //


    //year, month, day, hours, minutes, seconds, milliseconds
    var hour = jQuery('#add-addmeasurement-variable-timeh').val();
    var min = jQuery('#add-addmeasurement-variable-timem').val();
    var ap = jQuery('#add-addmeasurement-variable-timeap').val();
    var datetime = new Date(datetimeString);

    /*
     datetime.setHours(parseInt(hour) + (ap * 12));
     datetime.setMinutes(min);
     datetime.setSeconds(0);
     */

    if (name === '') {
        alert('Please enter the variable name.');
        return;
    }
    if (value == '') {
        alert('Please enter the value.');
        return;
    }
    //var variable = getVariableWithName(name);
    var measurements = [
        {
            timestamp: Math.floor(datetime.getTime() / 1000),
            value: value
        }
    ];
    // Add it to a request, payload is what we'll send to QuantiModo
    var request = {
        message: 'uploadMeasurements',
        payload: [
            {
                measurements: measurements,
                name: name,
                source: 'QuantiModo',
                category: valueCategory,
                combinationOperation: combineOp,
                unit: unit
            }
        ]

    };

    Quantimodo.postMeasurementsV2(request.payload, function (responseText) {
        var response = responseText;
        //alert (response) ;
        if (response.success === true) {
            //save measurement to pre-populate this values next time
            localCache.setSubmittedMeasurement(name, value, unit);
            jQuery('#addmeasurement-variable-name').val('');
            setBlockHideShow();
            alert('Measurement have been posted successfully');
        }
        else {
            alert('Adding a measurement failed.');
            console.log(responseText);
        }
    });

};

var onVariableNameInputFocussed = function () {
    jQuery('#snd_gap').height('100px');
    //document.getElementById('sectionMeasurementInput').style.opacity='0.2';
};

var onVariableNameInputUnfocussed = function () {
    //jQuery('#snd_gap').height('10px');
    //document.getElementById('sectionMeasurementInput').style.opacity='1';
};

var getVariableWithName = function (variableName) {
    var filteredVars = jQuery.grep(variables, function (variable, i) {
        return variable.name == variableName;
    });
    if (filteredVars.length > 0) return filteredVars[0];
    return null;
};

var getUnitWithAbbriatedName = function (unitAbbr) {
    var filteredUnits = jQuery.grep(units, function (unit, i) {
        return unit.abbreviatedName == unitAbbr;
    });

    if (filteredUnits.length > 0) return filteredUnits[0];
    return null;
};

var loadVariableCategories = function () {

    Quantimodo.getVariableCategories(null, function (variableCategories) {
        variables = variableCategories;
        var varnames = [];
        var categories = [];
        variableCategorySelect = document.getElementById('addmeasurement-variable-category');

        if (variables.length) {
            jQuery.each(variables.sort(function (a, b) {
                return a.name.localeCompare(b.name);
            }), function (_, variable) {
                varnames.push(variable.name);
                categories.push(variable.name);
            });
        }

        if (categories.length) {
            categories.sort();
            for (var i = 0; i < categories.length; i++)
                variableCategorySelect.options[variableCategorySelect.options.length] = new Option(categories[i], categories[i]);
        }
    })
};

var loadVariables = function () {
    jQuery.widget('custom.catcomplete', jQuery.ui.autocomplete, {
        _renderMenu: function (ul, items) {
            var that = this,
                currentCategory = '';
            jQuery.each(items, function (index, item) {
                if (item.category != currentCategory) {
                    ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');
                    currentCategory = item.category;
                }
                that._renderItemData(ul, item);
            });

        }
    });

    var request = {message: 'getVariables', params: {}};

    Quantimodo.getVariables(null, function (vars) {

        variables = vars;
        var varnames = [];
        var categories = [];
        variableCategorySelect = document.getElementById('addmeasurement-variable-category');

        if (variables.length) {
            jQuery.each(variables.sort(function (a, b) {
                return a.name.localeCompare(b.name);
            }), function (_, variable) {
                varnames.push(variable.name);
            });
        }

    });

};

var loadVariableUnits = function () {

    Quantimodo.getUnits(null, function (variableUnits) {
        units = variableUnits;
        unitSelect = document.getElementById('addmeasurement-variable-unit');

        if (units.length) {
            jQuery.each(units.sort(function (a, b) {
                return a.name.localeCompare(b.name);
            }), function (_, unit) {
                unitSelect.options[unitSelect.options.length] = new Option(unit.name, unit.abbreviatedName);
            });
        }
    });

};

// Load option for the Distance
var loadAddVariableUnits = function () {

    Quantimodo.getUnits(null, function (variableUnits) {
        units = variableUnits;
        unitSelect = document.getElementById('add-addmeasurement-variable-unit');

        if (units.length) {
            jQuery.each(units.sort(function (a, b) {
                return a.name.localeCompare(b.name);
            }), function (_, unit) {
                unitSelect.options[unitSelect.options.length] = new Option(unit.name, unit.abbreviatedName);
            });
        }
    });

};

function addZero(i) {
    if (i < 10) {
        i = '0' + i;
    }
    return i;
}

var loadDateTime = function () {

    jQuery('#addmeasurement-variable-date').datetimepicker({
        dayOfWeekStart: 1,
        lang: 'en',
        startDate: '1986/01/05',
        format: 'h:i A m/d/Y'
    });


    var currentTime = new Date();

    var jYears = currentTime.getFullYear();
    var jMonths = currentTime.getMonth();
    var jDate = currentTime.getDate();

    var jHours = addZero(currentTime.getHours());
    var jMinutes = addZero(currentTime.getMinutes());

    var jJjMinutes = ((currentTime.getHours() % 12) ? currentTime.getHours() % 12 : 12) + ':' + currentTime.getMinutes() + (currentTime.getHours() < 12 ? 'AM' : 'PM');

    var cDate = jMonths + '/' + jDate + '/' + jYears;

    var cDateTime = jJjMinutes + " " + cDate;

    jQuery('#addmeasurement-variable-date').datetimepicker({value: cDateTime, step: 10});

};

// Load Date Time
var loadAddDateTime = function () {
    jQuery('#add-addmeasurement-variable-date').datetimepicker({
        dayOfWeekStart: 1,
        lang: 'en',
        startDate: '1986/01/05',
        format: 'h:i A m/d/Y',
        todayButton: true,
        inverseButton: true
    });

    var currentTime = new Date();

    var jYears = currentTime.getFullYear();
    var jMonths = currentTime.getMonth();
    var jDate = currentTime.getDate();

    var jHours = addZero(currentTime.getHours());
    var jMinutes = addZero(currentTime.getMinutes());

    var cDate = jMonths + '/' + jDate + '/' + jYears;

    var jJjMinutes = ((currentTime.getHours() % 12) ? currentTime.getHours() % 12 : 12) + ':' + currentTime.getMinutes() + (currentTime.getHours() < 12 ? 'AM' : 'PM');

    var cDateTime = jJjMinutes + ' ' + cDate;

    jQuery('#add-addmeasurement-variable-date').datetimepicker({value: cDateTime, step: 10});

};

var handleResponse = function (response, callback) {
    if (response.status == 401) {
        //go to login screen

        jQuery('body').css('width', '270px');
        jQuery('#record_a_measurement_block').hide();
        jQuery('#edt_record_a_measurement_block').hide();
        jQuery('#add_record_a_measurement_block').hide();

        jQuery('#signup_block').show();

    } else {
        var parsedResponse = JSON.parse(response.responseText);
        callback(parsedResponse);
    }

};

var localCache = {

    setSubmittedMeasurement: function (name, value, unit) {

        var storageEntry = {
            variable: name,
            value: value,
            unit: unit
        };

        var lastSubmittedMeasurements = localStorage.getItem('lastSubmittedMeasurements');

        if (!lastSubmittedMeasurements) {
            lastSubmittedMeasurements = [];
            lastSubmittedMeasurements.push(storageEntry);
        } else {
            lastSubmittedMeasurements = JSON.parse(lastSubmittedMeasurements);

            for (var i = 0; i < lastSubmittedMeasurements.length; i++) {
                if (lastSubmittedMeasurements[i].variable == name) {
                    lastSubmittedMeasurements[i].value = value;
                    lastSubmittedMeasurements[i].unit = unit;
                    break;
                }
                if (i == lastSubmittedMeasurements.length - 1) {
                    lastSubmittedMeasurements.push(storageEntry);
                }
            }
        }

        localStorage.setItem('lastSubmittedMeasurements', JSON.stringify(lastSubmittedMeasurements));

    },

    getSubmittedMeasurement: function (name) {

        var lastSubmittedMeasurements = localStorage.getItem('lastSubmittedMeasurements');

        if (!lastSubmittedMeasurements) {
            return null;
        } else {
            lastSubmittedMeasurements = JSON.parse(lastSubmittedMeasurements);

            for (var i = 0; i < lastSubmittedMeasurements.length; i++) {
                if (lastSubmittedMeasurements[i].variable == name) {
                    return lastSubmittedMeasurements[i];
                }
            }
        }

    }

};

jQuery(document).ready(function () {

    setBlockHideShow();
    setButtonListeners();
    loadVariables();
    loadVariableCategories();
    loadVariableUnits();
    loadAddVariableUnits();
    loadAddDateTime();
    loadDateTime();

    var inputField = document.getElementById('addmeasurement-variable-name');
    inputField.onfocus = onVariableNameInputFocussed;
    inputField.onblur = onVariableNameInputUnfocussed;

    setInterval(function () {
        inputField.focus();
    }, 50);

    jQuery('#addmeasurement-variable-name').keypress(function () {
        if (jQuery(this).val().length > 0) {
            jQuery('#addmeasurement-variable-name').removeClass('error');
            jQuery('.validation-holder span').text('');
        }
    });

    jQuery('#addmeasurement-variable-name').autocomplete({
        source: function (req, resp) {

            Quantimodo.searchVariables(jQuery('#addmeasurement-variable-name').val(), function (data) {

                variables = data;
                resp(jQuery.map(data, function (variable) {
                    return {
                        label: variable.name,
                        value: variable.name,
                        variable: variable
                    }
                }));

            });
        },
        minLength: 2,
        select: function (event, ui) {

            document.getElementById('addmeasurement-variable-value').focus();
            //var variable = getVariableWithName(ui.item.label);
            var variable = ui.item.variable;
            jQuery('input[name="combineOperation"][value="' + variable.combinationOperation + '"]').prop('checked', true);
            if (variable == null) return;
            jQuery('#addmeasurement-variable-category').val(variable.category);

            var variableUnit = null;
            var variableValue = '';
            var lastMeasurementForVariable = localCache.getSubmittedMeasurement(variable.name);

            if (lastMeasurementForVariable) {

                variableUnit = getUnitWithAbbriatedName(lastMeasurementForVariable.unit);
                variableValue = lastMeasurementForVariable.value;

            } else {

                if (variable.mostCommonUnit) {
                    variableUnit = getUnitWithAbbriatedName(variable.mostCommonUnit);
                } else {
                    variableUnit = getUnitWithAbbriatedName(variable.abbreviatedUnitName);
                }

                if (variable.mostCommonValue) {
                    variableValue = variable.mostCommonValue;
                }

            }

            if (variableUnit == null) return;
            jQuery('#addmeasurement-variable-unitCategory').val(variableUnit.category).trigger('change');
            jQuery('#addmeasurement-variable-unit').val(variableUnit.abbreviatedName);
            jQuery('#addmeasurement-variable-value').val(variableValue);

        }
    });

})

