var QuantimodoSearchConstants = {
    sourceURL: apiHost + '/api/',
    // sourceURL: 'https://dilshod-dev-wplms.quantimo.do/api/',
    vURL: 'public/variables/search/',
    cURL: 'public/correlations/search/',
    predOfURL: apiHost + '/api/v1/variables/_VARIABLE_/public/causes',
    predByURL: apiHost + '/api/v1/variables/_VARIABLE_/public/effects',
    method: 'JSONP'
};

// Define a new module for our search page app
var quantimodoSearch = angular.module('quantimodoSearch', ['ui.bootstrap']);

// Define config
quantimodoSearch.config(function ($httpProvider) {
    console.log($httpProvider.defaults.headers.common);

    if (typeof mashapeKey !== 'undefined' && mashapeKey) {
        $httpProvider.defaults.headers.common['X-Mashape-Key'] = mashapeKey;
    }

    if (typeof accessToken !== 'undefined' && accessToken) {
        $httpProvider.defaults.headers.common.Authorization = 'Bearer ' + accessToken;
    }

    // for CORS requests but we are going to use JSONP so no need to do these lines
    // $httpProvider.defaults.useXDomain = true;
    //  delete $httpProvider.defaults.headers.common['X-Requested-With'];
});

// The controller
quantimodoSearch.controller('QuantimodoSearchController', ['$scope', 'QuantimodoSearchService',
    function ($scope, QuantimodoSearchService) {
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

        $scope.loadCurrentPageData = function () {
            $scope.correlations =
                $scope.totalCorrelations.slice(($scope.bigCurrentPage - 1) *
                    $scope.itemsPerPage, $scope.itemsPerPage * $scope.bigCurrentPage);
        };

        $scope.loadData = function () {
            $scope.bigTotalItems = $scope.totalCorrelations.length;
            $scope.bigCurrentPage = 1;
            $scope.loadCurrentPageData();
        };

        $scope.setPage = function (pageNo) {
            $scope.currentPage = pageNo;
        };

        $scope.pageChanged = function () {
            $scope.loadCurrentPageData();
        };

        $scope.hasMoreThanTen = function () {
            return $scope.totalCorrelations.length > 10;
        };

        $scope.isNotEmpty = function () {
            return $scope.totalCorrelations.length > 0;
        };

        $scope.showCorrelations = function (variable) {
            var timeToSearch = (new Date()).getTime();
            var correlationURL = '';
            $scope.homeShown = false;
            $scope.autoLoad = true;

            var queryVariable = variable.replace(/ /g, '+');


            /*if ($scope.selectOutputAsType === 'effect') {
             correlationURL = QuantimodoSearchConstants.predOfURL;
             } else {
             correlationURL = QuantimodoSearchConstants.predByURL;
             } */
            correlationURL = QuantimodoSearchConstants.sourceURL + QuantimodoSearchConstants.cURL + variable;

            correlationURL = correlationURL.replace('_VARIABLE_', queryVariable);

            QuantimodoSearchService.getData(correlationURL, {'effectOrCause': $scope.selectOutputAsType},
                function (correlations) {
                    $scope.totalCorrelations = [];
                    if (jQuery.isArray(correlations)) {
                        jQuery.each(correlations, function (_, correlation) {
                            if ($scope.selectOutputAsType === 'effect') {
                                $scope.totalCorrelations.push({
                                    correlation: correlation.correlationCoefficient,
                                    variable: correlation.cause,
                                    category: correlation.causeCategory
                                });
                            } else {
                                $scope.totalCorrelations.push({
                                    correlation: correlation.correlationCoefficient,
                                    variable: correlation.effect,
                                    category: correlation.effectCategory
                                });
                            }
                        });
                    }
                    $scope.resultTitle = 'Strongly predicted by ' + variable;
                    if ($scope.selectOutputAsType === 'effect') {
                        $scope.resultTitle = 'Strongest predictors of ' + variable;
                    }
                    $scope.countAndTime = $scope.totalCorrelations.length +
                        ' results  (' + (((new Date()).getTime() - timeToSearch) / 1000) + ' seconds)';
                    if ($scope.totalCorrelations.length === 0) {
                        $scope.resultTitle = 'Your search for variable ' + variable + ' does not have any results';
                    }
                    $scope.loadData();
                }
            );

        };
    }]);


// The service
quantimodoSearch.service('QuantimodoSearchService', function ($http) {
    this.getData = function (url, p, f) {
        $http.get(url, {params: p}).then(function (response) {
            f(response.data);
        });
    };
});


// The autocomplete directive
quantimodoSearch.directive('autoComplete', ['QuantimodoSearchService',
    function (QuantimodoSearchService) {
        return {
            link: function (scope, element) {

                // init jqueryUi autocomplete
                element.autocomplete({
                    source: function (request, response) {
                        var searchURL = QuantimodoSearchConstants.sourceURL +
                            QuantimodoSearchConstants.vURL + request.term;
                        QuantimodoSearchService.getData(searchURL, {},
                            function (variables) {
                                response(jQuery.map(variables,
                                    function (item) {
                                        return {
                                            label: item.name,
                                            value: item.name
                                        };
                                    }
                                ));
                            }
                        );
                    },
                    select: function (event, ui) {
                        scope.searchVariable = ui.item.value;
                        if (scope.autoLoad) {
                            scope.showCorrelations(ui.item.value);
                        }
                    },
                    focus: function (event, ui) {
                        scope.searchVariable = ui.item.value;
                        if (scope.autoLoad) {
                            scope.showCorrelations(ui.item.value);
                        }
                    }
                });
            }
        };
    }]);



