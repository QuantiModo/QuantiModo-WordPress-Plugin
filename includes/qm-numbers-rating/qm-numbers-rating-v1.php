<?php

wp_enqueue_style("qm-rating-buttons", plugins_url('../../', __FILE__) . "css/qm-rating-buttons.css");
wp_enqueue_script("jquery", true);

wp_enqueue_script("qm-sdk",
    plugins_url('../../', __FILE__) . "js/libs/quantimodo-api.js", "jquery", false, true);
wp_enqueue_script("qmwp-add-measurement",
    plugins_url('../../', __FILE__) . "js/qm-numbers-rating.js", "jquery", false, true);
?>


<div class="shortcode-content">

    <span>Please rate: <span id="tracked-variable-name"></span></span>

    <div class="rating-buttons">

        <div class="rating-button-wrap" data-value="1">

            <div class="rating-button">
                <span class="rating-button-value">1</span>
            </div>
            <div class="rating-button-label">
                None
            </div>
        </div>

        <div class="rating-button-wrap" data-value="2">

            <div class="rating-button">
                <span class="rating-button-value">2</span>
            </div>
            <div class="rating-button-label">
                Mild
            </div>
        </div>

        <div class="rating-button-wrap" data-value="3">

            <div class="rating-button">
                <span class="rating-button-value">3</span>
            </div>
            <div class="rating-button-label">
                Moderate
            </div>
        </div>

        <div class="rating-button-wrap" data-value="4">

            <div class="rating-button">
                <span class="rating-button-value">4</span>
            </div>
            <div class="rating-button-label">
                Difficult
            </div>
        </div>

        <div class="rating-button-wrap" data-value="5">

            <div class="rating-button">
                <span class="rating-button-value">5</span>
            </div>
            <div class="rating-button-label">
                Severe
            </div>

        </div>

        <div class="clearfix"></div>

    </div>

    <span id="result-string"></span>

</div>