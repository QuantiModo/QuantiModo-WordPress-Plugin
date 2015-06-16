<?php
/**
 *  Template Name: Search
 *  Description: Search for correlations.  Planned for landing page.
 */
add_action('admin_enqueue_scripts', 'queue_my_admin_scripts');

function queue_my_admin_scripts() {
    wp_enqueue_script('my-spiffy-miodal', // handle
                       URL_TO_THE_JS_FILE, // source
                       array('jquery-ui-dialog')); // dependencies
    // A style available in WP
    wp_enqueue_style('wp-jquery-ui-dialog');
}
function execute_stylescript()
{
    // Register Scripts and css
	wp_register_style( 'qm-search', plugins_url( '/css/qm-search.css', __FILE__  ));
	wp_register_style( 'jquery-ui-flick', plugins_url( '/css/jquery-ui-flick.css', __FILE__  ));

	wp_register_script( 'angular', plugins_url( '/js/libs/angular.min.js', __FILE__  ) );
    wp_register_script( 'ui-bootstrap', plugins_url( '/js/libs/ui-bootstrap-tpls-0.11.0.min.js', __FILE__  ) );
    wp_register_script( 'qm-search', plugins_url( '/js/qm-search.js', __FILE__ ), array( 'angular' ) );

    // Execute Scripts and css
    wp_enqueue_script("jquery", true);
    wp_enqueue_script("jquery-ui-core");
    wp_enqueue_script("jquery-ui-autocomplete");

    wp_enqueue_style("jquery-ui-flick");
    wp_enqueue_style("qm-search");

    wp_enqueue_script("angular");
    wp_enqueue_script("ui-bootstrap");
    wp_enqueue_script("qm-search", "angular", false, true);

	}
add_action( 'wp_enqueue_scripts', 'execute_stylescript' );

get_header();
?>

<div ng-app="quantimodoSearch" ng-controller="QuantimodoSearchController">

    <!-- biggest search area -->
    <div class="searchFormByDefault" ng-show='homeShown'>
        <div class="qmSearchLogo"></div>
        <div>
            <p>
            <div class="fieldsContainer">
                <select class="selectOutputAsType" ng-model="selectOutputAsType" ng-init="selectOutputAsType = 'effect'">
                    <option value="effect">Predictive of...</option>
                    <option value="cause">Predicted by...</option>
                </select>
                <input auto-complete ui-items="names"  ng-model="searchVariable" class="searchVariable" type="text" placeholder="Enter a medication, food supplement or anything else...">
                <input class="searchsubmit" type="button" value="Ask QuantiModo" ng-click="showCorrelations(searchVariable)">
            </div>
            </p>
        </div>
    </div>

    <!-- search area with result -->
    <div id="searchResultRegion" ng-show="!homeShown" ng-cloak>
        <div class="qmSearchMiniLogo"></div>
        <div class="fieldsContainer">
            <select class="selectOutputAsType" ng-model="selectOutputAsType" ng-init="selectOutputAsType = 'effect'">
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
                    <div class="resultVariableName"><a href="http://www.amazon.com/gp/search/ref=as_li_qf_sp_sr_tl?ie=UTF8&camp=1789&creative=9325&index=aps&keywords={{c.variable}}&linkCode=ur2&tag=quant08-20" target="_blank">{{c.variable}}</a><span class="correlationValue">{{c.correlation}}</span></div>
                    <div class="resultCategoryName">{{c.category}}</div>
                </li>
            </ul>
        </div>
        <div id="paginationSearchResultList" ng-show="hasMoreThanTen()">
            <pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm" ng-change="pageChanged()"></pagination>
        </div>
    </div>

</div>

<?php
get_footer();
?>
