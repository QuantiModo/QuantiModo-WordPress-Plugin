<?php

wp_enqueue_style("jquery-datetimepicker", plugins_url('../../', __FILE__) . "css/jquery.datetimepicker.css");
wp_enqueue_style("qmwp-add-measurement", plugins_url('../../', __FILE__) . "css/qmwp-add-measurement.css");
wp_enqueue_style("jquery-ui-custom", plugins_url('../../', __FILE__) . "css/jquery-ui-1.10.4.custom.css");
wp_enqueue_style("bootstrap", plugins_url('../../', __FILE__) . "css/bootstrap.min.css");


wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-autocomplete");
wp_enqueue_script("jquery-datetimepicker",
    plugins_url('../../', __FILE__) . "js/libs/jquery.datetimepicker.js", "jquery");
wp_enqueue_script("qm-sdk",
    plugins_url('../../', __FILE__) . "js/libs/quantimodo-api.js", "jquery", false, true);
wp_enqueue_script("qmwp-add-measurement",
    plugins_url('../../', __FILE__) . "js/qmwp-add-measurement.js", "jquery", false, true);
wp_enqueue_script("quantimodo-intercom", plugins_url('../../', __FILE__) . "js/intercom.js", array('jquery', 'qm-sdk'));

?>

<div id="addmeasurement-dialog-content" class="addmeasurement-content">

    <div id="signup_block">

        <p>Please wait...</p>

    </div>

    <div id="record_a_measurement_block">

        <form>
            <div class="form-group">
<!--                <label for="addmeasurement-variable-name">
                    Variable Name
                </label>-->
                <input id="addmeasurement-variable-name"
                       type="text"
                       class="form-control"
                       placeholder="Enter variable name">
                <span class="help-block"></span>
            </div>
            <a id="button-record-a-measurement" class="btn btn-default">Record a Measurement</a>
        </form>

    </div>

    <div id="edt_record_a_measurement_block">

        <form>

            <div class="form-group">
<!--                <label for="edt-addmeasurement-variable-name">
                    Variable Name
                </label>-->
                <input id="edt-addmeasurement-variable-name"
                       type="text"
                       class="form-control"
                       placeholder="Enter variable name">
                <span class="help-block"></span>
            </div>

            <h4>Enter Your Measurement</h4>

            <div class="form-group">
                <label for="addmeasurement-variable-value">
                    Value
                </label>
                <input id="addmeasurement-variable-value"
                       type="text"
                       class="form-control"
                       placeholder="Enter measurement value">
                <span class="help-block"></span>
            </div>

            <div class="form-group">
                <label for="addmeasurement-variable-unit">
                    Unit
                </label>
                <input id="addmeasurement-variable-unitCategory" type="hidden">
                <select id="addmeasurement-variable-unit" class="form-control"></select>
                <span class="help-block"></span>
            </div>

            <div class="form-group">
                <label for="addmeasurement-variable-date">
                    Date
                </label>
                <input id="addmeasurement-variable-date" type="text">
                <span class="help-block"></span>
            </div>

            <a id="button-edit-record-a-measurement" class="btn btn-default">Submit</a>

        </form>

    </div>

    <div id="add_record_a_measurement_block">

        <form>

            <h4>Create a New Variable</h4>

            <div class="form-group">
                <label for="add-addmeasurement-variable-name">
                    New Variable Name
                </label>
                <input id="add-addmeasurement-variable-name"
                       type="text"
                       class="form-control"
                       placeholder="Enter new variable name">
                <span class="help-block"></span>
            </div>

            <h5>Enter Your Measurement</h5>

            <div class="form-group">
                <label for="add-addmeasurement-variable-value">
                    Value
                </label>
                <input id="add-addmeasurement-variable-value"
                       type="text"
                       class="form-control"
                       placeholder="Enter measurement value">
                <span class="help-block"></span>
            </div>

            <div class="form-group">
                <label for="add-addmeasurement-variable-unit">
                    Unit
                </label>
                <input id="add-addmeasurement-variable-unitCategory" type="hidden">
                <select id="add-addmeasurement-variable-unit" class="form-control"></select>
                <span class="help-block"></span>
            </div>

            <div class="form-group">
                <label for="add-addmeasurement-variable-date">
                    Date
                </label>
                <input id="add-addmeasurement-variable-date" type="text">
                <span class="help-block"></span>
            </div>

            <div class="form-group">
                <label for="add-addmeasurement-variable-date">
                    Category
                </label>
                <select id="addmeasurement-variable-category" class="form-control"></select>
                <input type="hidden" name="combineOperation" value="MEAN" id="combineOperation">
                <span class="help-block"></span>
            </div>

            <a id="button-add-record-a-measurement" class="btn btn-default">Create Variable & Submit Measurement</a>

        </form>

    </div>

</div>
