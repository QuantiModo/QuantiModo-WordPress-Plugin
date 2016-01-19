<div class="closed" id="section-configure-settings" style="display: none;">
    <div class="inner">
        <div class="accordion-header" id="accordion-settings-header">
            <div class="dialog-header">
                Settings
            </div>
            <div style="float: right; margin-top:3px; margin-right:10px;">
                <img id="deleteVariableMeasurements" style="cursor:pointer;"
                     src=" <?php echo plugins_url('quantimodo'); ?>/css/images/trash.png"
                     title="Delete the Measurements for this variable">
                <input id="input-variable-id" type="hidden">
            </div>
        </div>
        <div class="accordion-content closed" id="accordion-settings-content">
            <div class="inner">
                <div class="loading-overlay" id="settings-loading"></div>
                <b style="margin-top: 12px;">Properties</b>
                <table border="0" cellspacing="0">
                    <tr>
                        <td>Variable name:</td>
                        <td><span id="input-variable-name"></span></td>
                    </tr>
                    <tr>
                        <td>Unit:</td>
                        <td>
                            <span id="selectVariableUnitSetting"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Category:</td>
                        <td>
                            <span id="selectVariableCategorySetting">
                            </span>
                        </td>
                    </tr>
                </table>

                <b style="margin-top: 8px;">Data Optimization</b>
                <table border="0" style="border-collapse:collapse;" cellspacing="0">
                    <tr>
                        <td>Minimum value</td>
                        <td><input type="text" id="variableMinimumValueSetting" placeholder="" style="width: 60%"><label
                                id="unitForMinValue" class="unitlabel"></label></td>
                    </tr>
                    <tr>
                        <td>Maximum value</td>
                        <td><input type="text" id="variableMaximumValueSetting" placeholder="" style="width: 60%"><label
                                id="unitForMaxValue" class="unitlabel"></label></td>
                    </tr>
                    <tr>
                        <td>Delay Before <br/>Onset of Action</td>
                        <td><input type="text" id="variableOnsetDelayValueSetting" placeholder=""
                                   style="width: 60%"><label id="unitForOnsetDelay" class="unitlabel">hrs</label></td>
                    </tr>
                    <tr>
                        <td>Duration of Action</td>
                        <td><input type="text" id="variableDurationOfActionValueSetting" placeholder=""
                                   style="width: 60%"><label id="unitForDurationAction" class="unitlabel">hrs</label>
                        </td>
                    </tr>
                </table>
                <div>
                    When there's no data:
                    <div class="assume-option-holder">
                        <input type="radio" name="missingAssumptionGroup" id="assumeMissing" checked="true">
                        <label for="assumeMissing">Assume data is missing</label>
                    </div>
                    <div class="assume-option-holder">
                        <input type="radio" name="missingAssumptionGroup" id="assumeValue">
                        <label for="assumeValue" class="assume-value-label">
                            <span>Assume&nbsp;</span>
                            <input id="variableFillingValueSetting"
                                   style="text-align: center; width: 50px; height: 26px;" type="text"
                                   id="inputVariableMaximumValueSetting" placeholder="">
                            <span>&nbsp;for that time</span>
                        </label>
                    </div>
                </div>

                <b style="margin-top: 8px;">Joined variables</b>

                <div style="margin-bottom: 8px;">
                    <ul id="joinedVariablesList">
                    </ul>
                    <select id="joinedVariablePicker"></select>
                    <button id="addJoinedVariableButton"></button>
                </div>
                <!--<b style="margin-top: 4px;">Sources</b>
                <div>
                    <ul id="sourcesSortable">
                      <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><label>Medhelper</label></li>
                      <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><label>My Pillbox</label></li>
                      <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><label>MediGuard</label></li>
                    </ul>
                </div>-->
                <button class="button-cancel buttonrow-2">Cancel</button>
                <button class="button-save buttonrow-2" style="margin-bottom: 12px">Save</button>
            </div>
        </div>
    </div>
</div>
