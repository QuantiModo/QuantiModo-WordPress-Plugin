jQuery(document).ready(function () {

    var appHolder = jQuery('#ionic-app-holder');
    var appFrame = jQuery('#ionic-app-frame');
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

    appFrame.on('load', function() {
        jQuery(this).css('height', 600);
        // try after 2 seconds to find correct height
        setTimeout(fixHeight, 2000);

        // try after 5 seconds to be perfectly sure all the xhr content loaded
        setTimeout(fixHeight, 5000);
    });

});

function fixHeight() {
    var height = 0;
    var appFrame = jQuery('#ionic-app-frame');
    appFrame.contents().find(".card").each(function(index, element) {
        height += element.scrollHeight;
    });
    appFrame.css('height', height + 100);
    appFrame.contents().find('.overflow-scroll').css('overflow-y', 'hidden');
}
