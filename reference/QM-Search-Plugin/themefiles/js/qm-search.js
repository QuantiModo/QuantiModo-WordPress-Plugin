var QuantimodoSearchConstants = {
    //sourceURL: 'https://quantimo.do/api/'
    sourceURL: 'https://dilshod-dev-wplms.quantimo.do/api/',
    vURL: 'public/variables/search/',
    cURL: 'public/correlations/search/',
    method: 'JSONP'
};

// Define a new module for our search page app
var quantimodoSearch = angular.module("quantimodoSearch", ['ui.bootstrap']);

// Define config
quantimodoSearch.config(function ($httpProvider) {  
    // for CORS requests but we are going to use JSONP so no need to do these lines
   // $httpProvider.defaults.useXDomain = true;
  //  delete $httpProvider.defaults.headers.common['X-Requested-With'];
});

// The controller
quantimodoSearch.controller('QuantimodoSearchController', ['$scope', 'QuantimodoSearchService', function($scope, QuantimodoSearchService) {
        $scope.correlations = [];
        $scope.totalCorrelations = [];
        $scope.maxSize = 10;
        $scope.itemsPerPage = 10;
        $scope.autoLoad = false;
        $scope.homeShown = true;
        $scope.selectOutputAsType = 0;
        $scope.searchVariable = '';
        $scope.resultTitle = '';
        $scope.countAndTime = '';

        $scope.loadCurrentPageData = function() {
            $scope.correlations = $scope.totalCorrelations.slice(($scope.bigCurrentPage - 1) * $scope.itemsPerPage, $scope.itemsPerPage * $scope.bigCurrentPage);
        };

        $scope.loadData = function() {
            $scope.bigTotalItems = $scope.totalCorrelations.length;
            $scope.bigCurrentPage = 1;
            $scope.loadCurrentPageData();
        };

        $scope.setPage = function(pageNo) {
            $scope.currentPage = pageNo;
        };

        $scope.pageChanged = function() {
            $scope.loadCurrentPageData();
        };

        $scope.hasMoreThanTen = function() {
            return $scope.totalCorrelations.length > 10;
        };

        $scope.isNotEmpty = function() {
            return $scope.totalCorrelations.length > 0;
        };

        $scope.showCorrelations = function(variable) {
            var timeToSearch = (new Date()).getTime();
            $scope.homeShown = false;
            $scope.autoLoad = true;
            QuantimodoSearchService.getData(QuantimodoSearchConstants.method, QuantimodoSearchConstants.sourceURL + QuantimodoSearchConstants.cURL + variable, 
                                            { 'effectOrCause' : $scope.selectOutputAsType, 'callback' : 'JSON_CALLBACK'}, function(correlations) {
                $scope.totalCorrelations = [];
                if(jQuery.isArray(correlations)) {
                    jQuery.each(correlations, function(_, correlation) {
                        if ($scope.selectOutputAsType === "effect") {
                            $scope.totalCorrelations.push({correlation: correlation.correlationCoefficient, variable: correlation.cause, category: correlation.causeCategory});
                        } else {
                            $scope.totalCorrelations.push({correlation: correlation.correlationCoefficient, variable: correlation.effect, category: correlation.effectCategory});
                        }
                    });
                }
                $scope.resultTitle = "Strongly predicted by " + variable;
                if ($scope.selectOutputAsType === "effect") {
                    $scope.resultTitle = "Strongest predictors of " + variable;
                }
                $scope.countAndTime = $scope.totalCorrelations.length + " results  (" + (((new Date()).getTime() - timeToSearch) / 1000) + " seconds)";
                if ($scope.totalCorrelations.length === 0) {
                    $scope.resultTitle = "Your search for variable " + variable + " does not have any results";
                }
                $scope.loadData();
            });

        };
    }]);


// The service
quantimodoSearch.service('QuantimodoSearchService', function($http) {
    this.getData = function(method, url, params, f) {
        $http({method: method, url: url, params: params}).then(function(response) {
            f(response.data);
        });
    };    
});


// The autocomplete directive
quantimodoSearch.directive('autoComplete', ['QuantimodoSearchService', function(QuantimodoSearchService) {
        return {
            link: function(scope, element, attrs) {

                // init jqueryUi autocomplete
                element.autocomplete({
                    source: function(request, response) {
                        QuantimodoSearchService.getData(QuantimodoSearchConstants.method, QuantimodoSearchConstants.sourceURL + QuantimodoSearchConstants.vURL + request.term, {'callback' : 'JSON_CALLBACK'}, function(variables) {
                            response(jQuery.map(variables, function(item) {
                                return {
                                    label: item.name,
                                    value: item.name
                                };
                            }))
                        });
                    },
                    select: function(event, ui) {
                        scope.searchVariable = ui.item.value;
                        /*if (scope.autoLoad) {
                            scope.showCorrelations(ui.item.value);
                        }*/
                    },
                    focus: function(event, ui) {
                        scope.searchVariable = ui.item.value;
                        if (scope.autoLoad) {
                            scope.showCorrelations(ui.item.value);
                        }
                    }
                });
            }
        };
    }]);



