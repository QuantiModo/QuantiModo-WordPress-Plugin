<?php
wp_enqueue_style("qm-rating-faces", plugins_url('../../', __FILE__) . "css/qm-rating-faces.css");
wp_enqueue_script("jquery", true);
wp_enqueue_script("qm-rating-faces", plugins_url('../../', __FILE__) . "js/qm-rating-faces.js", array('jquery'));
wp_enqueue_script("quantimodo-js-sdk", plugins_url('../../', __FILE__) . "js/libs/quantimodo-api.js", array('jquery'));
wp_enqueue_script("quantimodo-intercom", plugins_url('../../', __FILE__) . "js/intercom.js", array('jquery', 'quantimodo-js-sdk'));
?>


<div id="track-variable-content" class="shortcode-content rating-faces">

    <span>Please rate: <span id="tracked-variable-name"></span></span>

    <div class="rating-buttons">

        <div class="rating-button-wrap" data-value="1">
            <div class="rating-button">
                    <img id="buttonFaceDepressed" class="track-icon" data-value="1"
                         src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_depressed.png" ?>">
                    </img>
            </div>
        </div>

        <div class="rating-button-wrap" data-value="2">
            <div class="rating-button">
                    <img id="buttonFaceSad" class="track-icon" data-value="2"
                         src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_sad.png" ?>">
                    </img>
            </div>
        </div>

        <div class="rating-button-wrap" data-value="3">
            <div class="rating-button">
                    <img id="buttonFaceOk" class="track-icon" data-value="3"
                         src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_ok.png" ?>">
                    </img>
            </div>
        </div>

        <div class="rating-button-wrap" data-value="4">
            <div class="rating-button">
                    <img id="buttonFaceHappy" class="track-icon" data-value="4"
                         src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_happy.png" ?>">
                    </img>
            </div>
        </div>

        <div class="rating-button-wrap" data-value="5">
            <div class="rating-button">
                <img id="buttonFaceEcstatic" class="track-icon" data-value="5"
                     src="<?php echo plugins_url('../../', __FILE__) . "images/ic_mood_ecstatic.png" ?>">
                </img>
            </div>
        </div>

        <div class="clearfix"></div>

    </div>

    <span id="result-string"></span>

</div>

