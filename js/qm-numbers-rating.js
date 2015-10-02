jQuery(document).ready(function () {

    console.debug({
        variableName: qmShortCodeDefinedVariable,
        isNegative: qmShortCodeDefinedNegative,
        showLabels: qmShortCodeDefinedShowLabels
    });

    if (qmShortCodeDefinedShowLabels === 'false') {
        jQuery('.rating-button-label').hide();
    }

    var ratingButtons = jQuery('.rating-button');

    var colorClasses = ['qm-black', 'qm-blue', 'qm-green', 'qm-yellow', 'qm-red'];

    for (var i = 0; i < ratingButtons.length; i++) {
        var button = ratingButtons[i];

        if (qmShortCodeDefinedNegative === 'true') {
            jQuery(button).addClass(colorClasses[colorClasses.length - i - 1]);
        } else {
            jQuery(button).addClass(colorClasses[i]);
        }

    }

    Quantimodo.getVariableByName(qmShortCodeDefinedVariable, function (variable) {
        if (variable && variable.abbreviatedUnitName == '/5') {
            console.debug('Tracker is set to post measurements for variable:');
            console.debug(variable);
            jQuery('.shortcode-content').show();
            jQuery('#tracked-variable-name').html(variable.name);

            jQuery('.rating-button-wrap').click(function (event) {
                ratingButtonClicked(jQuery(event.currentTarget).data('value'), variable);
            });

        } else {
            console.error(variable);
            jQuery('.shortcode-content').hide();
            alert('Variable: ' + qmwpShortCodeDefinedVariable + '\n can not be tracked with this shortcode.');
        }
    });

    var ratingButtonClicked = function (value, variable) {
        console.log('value: ' + value);
        console.log('for variable: ' + variable.name);

        var timestamp = Math.floor(Date.now() / 1000);
        var measurements = [
            {
                timestamp: timestamp,
                value: value
            }
        ];
        var payload = [{
            measurements: measurements,
            name: variable.name,
            source: 'QuantiPress',
            category: variable.category,
            combinationOperation: variable.combinationOperation,
            unit: variable.abbreviatedUnitName
        }];

        Quantimodo.postMeasurementsV2(payload, function (response) {

            var resultHolder = jQuery('#result-string');
            resultHolder.html('');

            if (response.status == 201) {
                resultHolder.html('Thanks!  We love you!');
                window.measurementPostingResult = true;
            } else {
                resultHolder.html('Error! Please contact help@quantimo.do');
                window.measurementPostingResult = false;
                console.error(response);
            };

        });
    }

});
