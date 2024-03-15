<?php
wp_enqueue_style("qm-rating-faces", plugins_url('../../', __FILE__) . "css/qm-rating-faces.css");
wp_enqueue_script("jquery", true);
wp_enqueue_script("qm-rating-faces", plugins_url('../../', __FILE__) . "js/qm-rating-faces.js", array('jquery'));
wp_enqueue_script("quantimodo-js-sdk", plugins_url('../../', __FILE__) . "js/libs/quantimodo-sdk-javascript/quantimodo-api.js", array('jquery'));
wp_enqueue_script("quantimodo-intercom", plugins_url('../../', __FILE__) . "js/intercom.js", array('jquery', 'quantimodo-js-sdk'));
?>


<div id="track-variable-content" class="shortcode-content rating-faces">

    <div class="rating-buttons">

        <div class="rating-button-wrap" data-value="1">
            <div class="rating-button"></div>
        </div>

        <div class="rating-button-wrap" data-value="2">
            <div class="rating-button"></div>
        </div>

        <div class="rating-button-wrap" data-value="3">
            <div class="rating-button"></div>
        </div>

        <div class="rating-button-wrap" data-value="4">
            <div class="rating-button"></div>
        </div>

        <div class="rating-button-wrap" data-value="5">
            <div class="rating-button"></div>
        </div>

        <div class="clearfix"></div>

    </div>

    <span id="result-string"></span>

</div>

