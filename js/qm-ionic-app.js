jQuery(document).ready(function () {

    var appHolder = jQuery('#ionic-app-holder');
    var showHideButton = jQuery('#qm-ionic-app-show-hide');

    setTimeout(function () {
        showHideButton.show();
    }, 4444);

    showHideButton.click(function () {

        appHolder.toggle();

        if (appHolder.is(':visible')) {
            showHideButton.css('right', '396px');
        } else {
            showHideButton.css('right', '96px');
        }

    });

});
