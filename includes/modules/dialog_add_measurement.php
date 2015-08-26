<div class="dialog-background transitions" id="addmeasurement-dialog-background"></div>
<div class="dialog transitions" id="addmeasurement-dialog">
    <div class="loading-overlay" id="addmeasurement-loading"></div>
    <div class="addmeasurement-header" id="addmeasurement-dialog-header">
        <div class="dialog-header">
            Add a Measurement
        </div>
    </div>
    <div class="addmeasurement-content" id="addmeasurement-dialog-content">
        <input type="hidden" id="addmeasurement-variable-original-name">
        <table border="0" cellspacing="0">
            <tr>
                <td>Variable</td>
                <td><input type="text" placeholder="" id="addmeasurement-variable-name"></td>
            </tr>
        </table>
        <table border="0" cellspacing="0">
            <tr>
                <td>Value</td>
                <td><input id="addmeasurement-variable-value" type="text" placeholder=""></td>
            </tr>
            <tr>
                <td>Unit</td>
                <td><select id="addmeasurement-variable-unit"></select></td>
            </tr>
            <tr>
                <td>Date & Time</td>
                <td><input id="addmeasurement-variable-datetime" type="text" placeholder=""></td>
            </tr>
        </table>

        <button id="button-add" class="button-cancel buttonrow-2">Add</button>
        <button id="button-close" class="button-save buttonrow-2" style="margin-bottom: 12px">Cancel</button>

    </div>
</div>