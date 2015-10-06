<?php

wp_enqueue_style("jquery-datetimepicker", plugins_url('../../', __FILE__) . "css/jquery.datetimepicker.css");
wp_enqueue_style("qmwp-add-measurement", plugins_url('../../', __FILE__) . "css/qmwp-add-measurement.css");
wp_enqueue_style("jquery-ui-custom", plugins_url('../../', __FILE__) . "css/jquery-ui-1.10.4.custom.css");


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
        <div style="float:left;">
            <h3>What do you want to track?</h3>
        </div>
        <div style="float:right; margin-right:8px; margin-top:14px;">
            <a href="#" title="QuantiModo" id="logo-correlate">
                <img
                    src="<?php echo plugins_url('../../', __FILE__) . "images/logo-full.png" ?>"
                    alt="Better living through data." width=100>
            </a>
        </div>
        <div style="display:block;">

            <div class="validation-holder">
                <span></span>
            </div>

            <input type="text" placeholder=""
                   id="addmeasurement-variable-name" style="font-weight:bold;">

            <center>
                <div style="margin-top: 25px">
                    <button id="button-record-a-measurement" class="button login">Record a Measurement</button>
                </div>
            </center>

            <div style="height:8px;">&nbsp;</div>
        </div>

    </div>

    <div id="edt_record_a_measurement_block">
        <div style="float:left;"><h3>What do you want to track?</h3></div>
        <div style="float:right; margin-right:8px; margin-top:14px;"><a href="#" title="QuantiModo"
                                                                        id="logo-correlate-1">
                <img
                    src="<?php echo plugins_url('../../', __FILE__) . "images/logo-full.png" ?>"
                    alt="Better living through data." width=100></a></div>

        <input type="text" placeholder=""
               id="edt-addmeasurement-variable-name" style="font-weight:bold;">

        <div class="sectionTitle">
            <h4 style="height:4px; margin-left:9px;">Enter Your Measurement</h4>
        </div>

        <table class="inputContainer" cellpadding=6>
            <tr>
                <td width="15%">
                    <input id="addmeasurement-variable-value" type="text" style="font-weight:bold;">
                </td>
                <td width="35%">
                    <input id="addmeasurement-variable-unitCategory" type="hidden">
                    <select id="addmeasurement-variable-unit" style="margin-left: 2%; font-weight:bold"></select></td>
                <td>
                    <input id="addmeasurement-variable-date" type="text" placeholder="Date"
                           style="margin-left:2%;width:80%;font-weight:bold"> <span style="float:right;margin-top:-1px">
                        <img
                            src="<?php echo plugins_url('../../', __FILE__) . "images/calendar.png" ?>"
                            width="16px" id="pickDate"> </span>
                </td>
            </tr>
        </table>
        <div style="height:12px;">&nbsp;</div>
        <center>
            <div>
                <button id="button-edit-record-a-measurement" class="button login">Submit</button>
            </div>
        </center>
        <div style="height:20px;">&nbsp;</div>

    </div>

    <div id="add_record_a_measurement_block">
        <div style="float:left;"><h3>Create a New Variable</h3></div>
        <div style="float:right; margin-right:8px; margin-top:14px;"><a href="#" title="QuantiModo"
                                                                        id="logo-correlate-2">
                <img
                    src="<?php echo plugins_url('../../', __FILE__) . "images/logo-full.png" ?>"
                    alt="Better living through data." width=100></a></div>


        <input type="text" placeholder=""
               id="add-addmeasurement-variable-name">

        <div class="sectionTitle">
            <h4 style="height:4px;">Enter Your Measurement</h4>
        </div>

        <table class="inputContainer" cellpadding=6>
            <tr>
                <td width="15%">
                    <input id="add-addmeasurement-variable-value" type="text">

                </td>
                <td width="35%">
                    <input id="add-addmeasurement-variable-unitCategory" type="hidden">
                    <select id="add-addmeasurement-variable-unit" style="margin-left: 2%;"></select>
                </td>
                <td>
                    <input id="add-addmeasurement-variable-date" type="text" placeholder="Date"
                           style="margin-left:2%;width:80%;"> <span style="float:right;margin-top:-1px">
                        <img
                            src="<?php echo plugins_url('../../', __FILE__) . "images/calendar.png" ?>"
                            width="16px" id="add-pickDate"> </span>
                </td>
            </tr>
            <tr>

            </tr>
        </table>
        <div class="sectionTitle" style="margin-top:-8px;">
            <h4 style="height:4px;">Variable Category</h4>

        </div>

        <table class="inputContainer">

            <tr>
                <td>
                    <select id="addmeasurement-variable-category" style="font-weight:bold;"></select>

                    <input type="hidden" name="combineOperation" value="MEAN" id="combineOperation">
                </td>
            </tr>
        </table>
        <div style="height:12px;">&nbsp;</div>


        <center>
            <div>
                <button id="button-add-record-a-measurement" class="button login">Create Variable & Submit Measurement
                </button>
            </div>
        </center>

        <div style="height:20px;">&nbsp;</div>

    </div>


</div>
