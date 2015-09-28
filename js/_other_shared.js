// ocean five
var oceanFive = ['#3284FF', '#FFBB00', '#26B14C', '#FF3424', '#00A0B0'];

// ocean five in Use
var oceanFiveInUse = [];

// Requires div with .loading-overlay in element
var showLoadingOverlay = function (element) {
    jQuery(element + " .loading-overlay").css('display', 'table-cell');
    jQuery(element + " .loading-overlay").css('opacity', 0.5);
}
var hideLoadingOverlay = function (element) {
    jQuery(element + " .loading-overlay").css('opacity', 0);
    setTimeout(function () {
        jQuery(element + " .loading-overlay").css('display', 'none');
    }, 500);
}


function toggleElement(element) {
    var content = jQuery(element);
    content.inner = jQuery(element + ' .inner');

    content.on('transitionEnd webkitTransitionEnd transitionend oTransitionEnd msTransitionEnd', function (e) {
        if (content.hasClass('open')) {
            //content.css('max-height', 9999);
        }
    });

    content.toggleClass('open closed');
    content.contentHeight = content.outerHeight();

    if (content.hasClass('closed')) {
        content.removeClass('transitions').css('max-height', content.contentHeight);
        setTimeout(function () {
            content.addClass('transitions').css(
                {
                    //'max-height': 0,
                    'display': 'none'
                });
        }, 77);
    }
    else if (content.hasClass('open')) {
        content.contentHeight += content.inner.outerHeight();
        setTimeout(function () {
            content.addClass('transitions').css(
                {
                    //'max-height': content.contentHeight,
                    'display': 'block'
                });
        }, 77);
    }
}


var initDeleteMeasurements = function () {
    jQuery("#deletemeasurement-dialog #button-dodelete").click(function () {
        jQuery("#deletemeasurement-dialog").css({'display': 'block', 'opacity': 0.8});
        showLoadingOverlay("#deletemeasurement-dialog");

        var variables = [];
        variables.push({
            variableId: jQuery("#input-variable-id").val(),
            variableName: jQuery("#input-variable-name").val()
        });
        Quantimodo.deleteVariableMeasurements(variables, function (response) {
            if (response.success == true) {
                alert('Measurements for variable ' + jQuery("#input-variable-name").val() + ' were deleted successfully.');
            }
            else {
                alert('Measurements for variable ' + jQuery("#input-variable-name").val() + ' were not deleted. error code: ' + response.error);
            }
            ;

            hideLoadingOverlay("#deletemeasurement-dialog");
            setTimeout(function () {
                jQuery("#deletemeasurement-dialog-background").css({'display': 'none'});
                jQuery("#deletemeasurement-dialog").css({'display': 'none'});
                location.reload();
            }, 500);
        });

    });
    jQuery("#deletemeasurement-dialog #button-canceldelete").click(function () {
        jQuery("#deletemeasurement-dialog-background").css({'opacity': 0});
        jQuery("#deletemeasurement-dialog").css({'opacity': 0});

        setTimeout(function () {
            jQuery("#deletemeasurement-dialog-background").css({'display': 'none'});
            jQuery("#deletemeasurement-dialog").css({'display': 'none'});
        }, 500);
    });

    jQuery('#deleteVariableMeasurements').click(function () {
        showDeleteMeasurementsDialog();
    });
}

var showDeleteMeasurementsDialog = function () {
    jQuery("#deletemeasurement-dialog-background").css({'display': 'block', 'opacity': 0.5});
    jQuery("#deletemeasurement-dialog").css({'display': 'block', 'opacity': 1});
}