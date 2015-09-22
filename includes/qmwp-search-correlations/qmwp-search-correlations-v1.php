<?php

wp_enqueue_style("qmwp-search-correlations", plugins_url('../../', __FILE__) . "css/qmwp-search-correlations.css");
wp_enqueue_style("jquery-ui-flick", plugins_url('../../', __FILE__) . "css/jquery-ui-flick.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-autocomplete");
wp_enqueue_script("angular", plugins_url('../../', __FILE__) . "js/libs/angular.min.js");
wp_enqueue_script("ui-bootstrap", plugins_url('../../', __FILE__) . "js/libs/ui-bootstrap-tpls-0.11.0.min.js");


wp_enqueue_script("qmwp-search-correlations", plugins_url('../../', __FILE__) . "js/qmwp-search-correlations.js", array('angular'));

?>

<div ng-app="quantimodoSearch" ng-controller="QuantimodoSearchController">

    <!-- biggest search area -->
    <div class="searchFormByDefault" ng-show='homeShown'>
        <div class="qmSearchLogo"></div>
        <div>
            <div class="fieldsContainer">
                <div>

                </div>
                <div>
                    <input auto-complete ui-items="names"  ng-model="searchVariable" class="searchVariable" type="text" placeholder="Enter a medication, food supplement or anything else..." />
                </div>
                <div>
                    <input class="searchsubmit" type="button" value="Ask QuantiModo" ng-click="showCorrelations(searchVariable)" />
                </div>
            </div>
        </div>
    </div>

    <!-- search area with result -->
    <div id="searchResultRegion" ng-show="!homeShown" ng-cloak>
        <div class="qmSearchMiniLogo"></div>
        <div class="fieldsContainer">
            <select class="selectOutputAsType" ng-model="selectOutputAsType">
                <option value="effect">Predictive of...</option>
                <option value="cause">Predicted by...</option>
            </select>
            <input auto-complete ui-items="names" class="searchVariable" ng-model="searchVariable" type="text" placeholder="Enter a medication, food supplement or anything else...">
            <input class="searchsubmit" type="button" value="Ask QuantiModo" ng-click="showCorrelations(searchVariable)">
        </div>

        <p class="predictor">{{resultTitle}}</p>
        <p ng-show="isNotEmpty(correlations)">{{countAndTime}}</p>

        <div id="searchResultList">
            <ul>
                <li ng-repeat="c in correlations">
                    <div class="resultVariableName"><a href="http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_tl?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={{c.variable}}&linkCode=ur2&tag=quant08-20" class="result-title" target="_blank">{{c.variable}}</a>
                        <?php /*<span class="correlationValue">{{c.correlation}}</span> */ ?>
                        <a href="http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_tl?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={{c.variable}}&linkCode=ur2&tag=quant08-20" class="shop-cart" target="_blank">
                            <img src="<?php echo plugins_url('../../','/css/images/shop-cart.png', __FILE__ );?>" />
                        </a>
                    </div>
                    <div class="resultCategoryName">{{c.category}}</div>
                </li>
            </ul>
        </div>
        <div id="paginationSearchResultList" ng-show="hasMoreThanTen()">
            <pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm" ng-change="pageChanged()"></pagination>
        </div>
    </div>

</div>

