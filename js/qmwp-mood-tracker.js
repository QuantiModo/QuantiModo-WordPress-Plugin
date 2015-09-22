var onMoodButtonClicked = function (value, variable) {

    jQuery("#sectionSendingMood").html("");
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
        source: "MoodiModo",
        category: variable.category,
        combinationOperation: variable.combinationOperation,
        unit: variable.abbreviatedUnitName
    }];

    Quantimodo.postMeasurementsV2(payload, function (response) {
        if (response.status == 201) {
            jQuery("#sectionSendingMood").html("Measurement has been posted successfully");
            window.measurementPostingResult = true;
        } else {
            jQuery("#sectionSendingMood").html("Error while posting measurement");
            window.measurementPostingResult = false;
            console.error(response);
        }
    });
};


jQuery(document).ready(function () {
    Quantimodo.getVariableByName(qmwpShortCodeDefinedVariable, function (variable) {
        if (variable && variable.abbreviatedUnitName == "/5") {
            console.debug('Tracker is set to post measurements for variable:');
            console.debug(variable);
            jQuery('#track-variable-content').toggle();
            jQuery('#track-variable-name').html(variable.name);

            jQuery('.track-icon').click(function (event) {

                onMoodButtonClicked(jQuery(event.currentTarget).data('value'), variable);
            });

        } else {
            console.error(variable);
            alert('Variable: ' + qmwpShortCodeDefinedVariable + '\n can not be tracked with this shortcode.')
        }
    });

});
