<?php

wp_enqueue_style("jquery-ui-flick", plugins_url('../../', __FILE__) . "css/jquery-ui-flick.css");
wp_enqueue_style("bootstrap-with-icons", "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css");
wp_enqueue_style("qmwp-search-correlations", plugins_url('../../', __FILE__) . "css/qmwp-search-correlations.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-autocomplete");
wp_enqueue_script("angular", "https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.0-beta.1/angular.min.js");
wp_enqueue_script("ui-bootstrap", "https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.14.3/ui-bootstrap-tpls.min.js");
wp_enqueue_script("underscore", "https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js");


wp_enqueue_script("qmwp-search-correlations", plugins_url('../../', __FILE__) . "js/qmwp-search-correlations.js", array('angular'));

wp_enqueue_script("qm-sdk", plugins_url('../../', __FILE__) . "js/libs/quantimodo-api.js", "jquery", false, true);
wp_enqueue_script("quantimodo-intercom", plugins_url('../../', __FILE__) . "js/intercom.js", array('jquery', 'qm-sdk'));

?>

<div ng-app="quantimodoSearch" ng-controller="QuantimodoSearchController">

    <!-- biggest search area -->
    <div class="searchFormByDefault" ng-show='homeShown'>
        <div>
            <div class="fieldsContainer">
                <div>
                    <input auto-complete ui-items="names" ng-model="searchVariable" class="searchVariable" type="text"
                           placeholder="Enter a medication, food supplement or anything else..."/>
                </div>
                <div>
                    <input class="searchsubmit" type="button" value="Ask QuantiModo"
                           ng-click="showCorrelations(searchVariable)"/>
                </div>
            </div>
        </div>
    </div>

    <!-- search area with result -->
    <div id="searchResultRegion" ng-show="!homeShown" ng-cloak>
        <div class="fieldsContainer">
            <select class="selectOutputAsType" ng-model="selectOutputAsType">
                <option value="effect">Predictive of...</option>
                <option value="cause">Predicted by...</option>
            </select>
            <input auto-complete ui-items="names" class="searchVariable" ng-model="searchVariable" type="text"
                   placeholder="Enter a medication, food supplement or anything else...">
            <input class="searchsubmit" type="button" value="Ask QuantiModo"
                   ng-click="showCorrelations(searchVariable)">
        </div>

        <p class="predictor">{{resultTitle}}</p>

        <p ng-show="isNotEmpty(correlations)">{{countAndTime}}</p>

        <div id="searchResultList">

            <div class="row search-result" ng-repeat="c in correlations">
                <div class="col-md-10">

                    <h4>
                        <a href="http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_tl?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={{c.variable}}&linkCode=ur2&tag=quant08-20">
                            {{c.variable}}
                        </a>
                        <small>({{c.category}})</small>

                    </h4>

                    <p ng-if="c.originalCorrelation.predictorExplanation">
                        {{c.originalCorrelation.predictorExplanation}}</p>

                    <p ng-if="c.originalCorrelation.valuePredictingHighOutcomeExplanation">
                        {{c.originalCorrelation.valuePredictingHighOutcomeExplanation}}
                    </p>

                    <p ng-if="c.originalCorrelation.valuePredictingHighOutcomeExplanation">
                        {{c.originalCorrelation.valuePredictingLowOutcomeExplanation}}
                    </p>

                </div>

                <div class="col-md-2 controls">

                    <span class="glyphicon glyphicon-thumbs-up vote-thumb up"
                          ng-class="{voted: c.originalCorrelation.userVote==1}"
                          ng-click="vote(c, 1)"></span>

                    <span class="glyphicon glyphicon-thumbs-down vote-thumb down"
                          ng-class="{voted: c.originalCorrelation.userVote==0}"
                          ng-click="vote(c, 0)"></span>

                    <a href="http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_tl?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={{c.variable}}&linkCode=ur2&tag=quant08-20"
                       class="shop-cart" target="_blank">
                        <span class="glyphicon glyphicon-shopping-cart"></span></a>

                </div>
            </div>
        </div>
    </div>
    <div id="paginationSearchResultList" ng-show="hasMoreThanTen()">
        <pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm"
                    ng-change="pageChanged()"></pagination>
    </div>
</div>

</div>

