<?php
wp_enqueue_style("qmwp-mood-tracker", plugins_url('../../', __FILE__) . "css/qmwp-mood-tracker.css");
wp_enqueue_script("jquery", true);
wp_enqueue_script("qmwp-mood-tracker", plugins_url('../../', __FILE__) . "js/qmwp-mood-tracker.js", array('jquery'));
?>

<section id="sectionRateMood">
    <ul>
        <li><img id="buttonMoodDepressed"
                 src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_depressed.png" ?>">
            </img></li>

        <li><img id="buttonMoodSad" src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_sad.png" ?>">
            </img></li>

        <li><img id="buttonMoodOk" src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_ok.png" ?>">
            </img></li>

        <li><img id="buttonMoodHappy" src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_happy.png" ?>">
            </img></li>

        <li><img id="buttonMoodEcstatic"
                 src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_ecstatic.png" ?>">
            </img></li>
    </ul>
</section>
<section id="sectionSendingMood">
</section>
