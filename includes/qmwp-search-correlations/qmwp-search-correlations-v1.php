<?php

wp_enqueue_style("jquery-ui-flick", plugins_url('../../', __FILE__) . "css/jquery-ui-flick.css");
//wp_enqueue_style("bootstrap-with-icons", "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css");
wp_enqueue_style("font-awesome", "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css");
wp_enqueue_style("datetimepicker", plugins_url('../../', __FILE__) . "css/jquery.datetimepicker.css");
wp_enqueue_style("qmwp-search-correlations", plugins_url('../../', __FILE__) . "css/qmwp-search-correlations.css");
wp_enqueue_style("loading-bar", plugins_url('../../', __FILE__) . "js/libs/loading-bar/loading-bar.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-autocomplete");
wp_enqueue_script("momentjs", plugins_url('../../', __FILE__) . "js/libs/moment.min.js");
wp_enqueue_script("angular", "https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.0-beta.1/angular.min.js");
wp_enqueue_script("ui-bootstrap", "https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.14.3/ui-bootstrap-tpls.min.js");
wp_enqueue_script("loading-bar", plugins_url('../../', __FILE__) . "js/libs/loading-bar/loading-bar.js");
wp_enqueue_script("datetimepicker", plugins_url('../../', __FILE__) . "js/libs/jquery.datetimepicker.js");
wp_enqueue_script("underscore", "https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js");


wp_enqueue_script("qmwp-search-correlations", plugins_url('../../', __FILE__) . "js/qmwp-search-correlations.js", array('angular'));

wp_enqueue_script("qm-sdk", plugins_url('../../', __FILE__) . "js/libs/quantimodo-api.js", "jquery", false, true);
wp_enqueue_script("quantimodo-intercom", plugins_url('../../', __FILE__) . "js/intercom.js", array('jquery', 'qm-sdk'));

?>

<div ng-app="quantimodoSearch" ng-controller="QuantimodoSearchController" id="qmSearchCorrelationsApp">

    <div class="form-group">
        <label for="outcome-input">Outcome:</label>
        <input id="outcome-input" auto-complete
               class="form-control"
               ng-model="outcomeVariableName"
               type="text"
               placeholder="Enter a medication, food supplement or anything else...">
    </div>

    <div class="form-group">
        <label for="predictor-input">Predictor:</label>
        <input id="predictor-input" auto-complete
               class="form-control"
               ng-model="predictorVariableName"
               type="text"
               placeholder="Enter a medication, food supplement or anything else...">
    </div>

    <div id="searchResultRegion" ng-cloak ng-show="showResults">

        <div ng-if="totalCorrelations.length > 0">

            <p>Correlations where
                <span ng-show="predictorVariableName">Predictor: <strong>{{predictorVariableName}}</strong></span>
                <span ng-show="outcomeVariableName && predictorVariableName">&nbsp;and&nbsp;</span>
                <span ng-show="outcomeVariableName">Outcome: <strong>{{outcomeVariableName}}</strong></span>
            </p>

            <p>Search took: {{timeTakenForSearch}} second(s)</p>

            <div id="searchResultList">

                <div class="row search-result" ng-repeat="c in correlations">

                    <h4>
                        <a target="_blank"
                           href="http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_tl?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={{c.variableName}}&linkCode=ur2&tag=quant08-20">
                            {{c.variableName}}
                        </a>
                        <small>({{c.variableCategory}})</small>

                    </h4>

                    <div class="details-and-controls">

                        <div class="col-md-8">

                            <p ng-if="c.correlation.predictorExplanation">
                                {{c.correlation.predictorExplanation}}</p>

                            <p ng-if="c.correlation.valuePredictingHighOutcomeExplanation">
                                {{c.correlation.valuePredictingHighOutcomeExplanation}}
                            </p>

                            <p ng-if="c.correlation.valuePredictingHighOutcomeExplanation">
                                {{c.correlation.valuePredictingLowOutcomeExplanation}}
                            </p>

                        </div>

                        <div class="col-md-4 controls">

                            <span class="fa fa-thumbs-o-up vote-thumb up"
                                  uib-tooltip="{{getToolTipText('thumbUp', c.correlation)}}"
                                  tooltip-class="qmwp-tooltip"
                                  ng-class="{'voted fa-thumbs-up': c.correlation.userVote==1}"
                                  ng-click="vote(c, 1)"></span>

                            <span class="fa fa-thumbs-o-down vote-thumb down"
                                  uib-tooltip="{{getToolTipText('thumbDown', c.correlation)}}"
                                  tooltip-class="qmwp-tooltip"
                                  ng-class="{'voted fa-thumbs-down': c.correlation.userVote==0}"
                                  ng-click="vote(c, 0)"></span>

                            <a href="http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_tl?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={{c.variableName}}&linkCode=ur2&tag=quant08-20"
                               class="shop-cart" target="_blank">
                            <span class="fa fa-shopping-cart"
                                  tooltip-class="qmwp-tooltip"
                                  uib-tooltip="Buy it here">

                            </span>
                            </a>

                            <span class="fa fa-cog"
                                  uib-tooltip="Improve our algorithms by optimizing the variable settings"
                                  tooltip-class="qmwp-tooltip"
                                  ng-click="openVarSettingsModal(c)">

                        </span>
                            <span class="fa fa-plus"
                                  uib-tooltip="Add measurement for variable"
                                  tooltip-class="qmwp-tooltip"
                                  ng-click="addMeasurement(c)">

                        </span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div ng-if="totalCorrelations.length == 0">
            <p class="no-correlations-message">
                Hi! We don't have enough data yet to determine your top predictors.
                Please connect to some data sources on the Import Data page or
                start using one of the great tracking apps and devices at
                <a href="https://quantimo.do/data-sources">https://quantimo.do/data-sources</a>
            </p>
        </div>

        <uib-pagination ng-if="totalCorrelations.length > itemsPerPage"
                        total-items="totalCorrelations.length"
                        ng-model="currentPage"
                        max-size="maxSize"
                        class="pagination-sm"
                        ng-change="displayPage(currentPage)"
                        previous-text="<"
                        next-text=">">
        </uib-pagination>


    </div>

</div>

</div>

