jQuery(document).ready(function () {

    var appHolder = jQuery('#ionic-app-holder');
    var showHideButton = jQuery('#qm-ionic-app-show-hide');
    var iFrame = jQuery('#ionic-app-holder iframe');

    iFrame.load(function () {
        showHideButton.show();
    });

    showHideButton.click(function () {

        appHolder.toggle();

        if (appHolder.is(':visible')) {
            showHideButton.css('right', '396px');
        } else {
            showHideButton.css('right', '96px');
        }

    });

});
