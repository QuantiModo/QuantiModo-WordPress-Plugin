<?php
wp_enqueue_style("qm-rating-faces", plugins_url('../../', __FILE__) . "css/qm-rating-faces.css");
wp_enqueue_script("jquery", true);
wp_enqueue_script("qm-rating-faces", plugins_url('../../', __FILE__) . "js/qm-rating-faces.js", array('jquery'));
wp_enqueue_script("quantimodo-js-sdk", plugins_url('../../', __FILE__) . "js/libs/quantimodo-api.js", array('jquery'));
?>

<div id="track-variable-content">
<!--    <div id="track-variable-header">How do you feel about <span id="track-variable-name"></span>?</div>-->
    <section id="sectionRatingFaces">
        <ul>
            <li><img id="buttonFaceDepressed" class="track-icon" data-value="1"
                     src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_depressed.png" ?>">
                </img></li>

            <li><img id="buttonFaceSad" class="track-icon" data-value="2"
                     src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_sad.png" ?>">
                </img></li>

            <li><img id="buttonFaceOk" class="track-icon" data-value="3"
                     src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_ok.png" ?>">
                </img></li>

            <li><img id="buttonFaceHappy" class="track-icon" data-value="4"
                     src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_happy.png" ?>">
                </img></li>

            <li><img id="buttonFaceEcstatic" class="track-icon" data-value="5"
                     src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_ecstatic.png" ?>">
                </img></li>
        </ul>
    </section>
    <section id="sectionSendingRating">
    </section>
</div>

