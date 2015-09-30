<?php
wp_enqueue_style("qmwp-mood-tracker", plugins_url('../../', __FILE__) . "css/qmwp-mood-tracker.css");
wp_enqueue_script("jquery", true);
wp_enqueue_script("qmwp-mood-tracker", plugins_url('../../', __FILE__) . "js/qmwp-mood-tracker.js", array('jquery'));
wp_enqueue_script("quantimodo-js-sdk", plugins_url('../../', __FILE__) . "js/libs/quantimodo-api.js", array('jquery'));
?>

<div id="track-variable-content">
<!--    <div id="track-variable-header">How do you feel about <span id="track-variable-name"></span>?</div>-->
    <section id="sectionRateMood">
        <ul>
            <li><img id="buttonMoodDepressed" class="track-icon" data-value="1"
                     src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_depressed.png" ?>">
                </img></li>

            <li><img id="buttonMoodSad" class="track-icon" data-value="2"
                     src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_sad.png" ?>">
                </img></li>

            <li><img id="buttonMoodOk" class="track-icon" data-value="3"
                     src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_ok.png" ?>">
                </img></li>

            <li><img id="buttonMoodHappy" class="track-icon" data-value="4"
                     src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_happy.png" ?>">
                </img></li>

            <li><img id="buttonMoodEcstatic" class="track-icon" data-value="5"
                     src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_ecstatic.png" ?>">
                </img></li>
        </ul>
    </section>
    <section id="sectionSendingMood">
    </section>
</div>

