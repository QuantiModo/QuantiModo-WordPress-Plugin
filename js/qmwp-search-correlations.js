var QuantimodoSearchConstants = {
    sourceURL: apiHost + '/api/',
    // sourceURL: 'https://dilshod-dev-wplms.quantimo.do/api/',
    vURL: 'public/variables/search/',
    cURL: 'public/correlations/search/',
    voteURL: 'v1/votes',
    predOfURL: apiHost + '/api/v1/variables/_VARIABLE_/public/causes',
    predByURL: apiHost + '/api/v1/variables/_VARIABLE_/public/effects',
    method: 'JSONP',
    predefinedVariable: (typeof qmwpShortCodeDefinedVariable !== 'undefined') ? qmwpShortCodeDefinedVariable : null,
    predefinedVariableAs: (typeof qmwpShortCodeDefinedVariableAs !== 'undefined') ? qmwpShortCodeDefinedVariableAs : null,
};

// Define a new module for our search page app
var quantimodoSearch = angular.module('quantimodoSearch', ['ui.bootstrap']);

// Define config
quantimodoSearch.config(function ($httpProvider) {

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
quantimodoSearch.controller('QuantimodoSearchController',
    ['$scope', 'QuantimodoSearchService', '$uibModal', 'correlationsVoteHelper',
        function ($scope, QuantimodoSearchService, $uibModal, correlationsVoteHelper) {
            $scope.correlations = [];
            $scope.totalCorrelations = [];
            $scope.maxSize = 10;
            $scope.itemsPerPage = 10;
            $scope.autoLoad = false;
            $scope.homeShown = true;
            $scope.selectOutputAsType = qmwpShortCodeDefinedVariableAs;
            $scope.searchVariable = '';
            $scope.resultTitle = '';
            $scope.countAndTime = '';

            $scope.loadCurrentPageData = function () {
                $scope.correlations =
                    $scope.totalCorrelations.slice(($scope.bigCurrentPage - 1) *
                        $scope.itemsPerPage, $scope.itemsPerPage * $scope.bigCurrentPage);
                refreshCorrelationsConsiderVotes();
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
                                        category: correlation.causeCategory,
                                        explanation: correlation.correlationExplanation,
                                        originalCorrelation: correlation
                                    });
                                } else {
                                    $scope.totalCorrelations.push({
                                        correlation: correlation.correlationCoefficient,
                                        variable: correlation.effect,
                                        category: correlation.effectCategory,
                                        explanation: correlation.correlationExplanation,
                                        originalCorrelation: correlation
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

            $scope.vote = function (correlationSet, likeValue) {
                console.log('Liked: ' + likeValue);
                console.log(correlationSet);

                var modalInstance = $uibModal.open({
                    templateUrl: qmwpPluginUrl + '/templates/search-correlations/vote-confirm-modal.html',
                    controller: 'voteModalInstanceController',
                    resolve: {
                        confirmationOptions: function () {
                            return {
                                correlation: correlationSet.originalCorrelation,
                                likeValue: likeValue
                            }
                        }
                    }
                });

                modalInstance.result.then(function () {
                    //confirmed
                    var prevVoted = correlationsVoteHelper.getPreviouslyVoted(correlationSet.originalCorrelation);

                    if (prevVoted === likeValue) {
                        likeValue = 'null';
                    }

                    QuantimodoSearchService.vote(correlationSet.originalCorrelation, likeValue, function (resp) {
                        console.log(resp);

                        correlationsVoteHelper.saveVotedCorrelation(correlationSet.originalCorrelation, likeValue);

                        refreshCorrelationsConsiderVotes();

                    })

                }, function () {
                    console.debug('dismissed');
                });

            };

            if (QuantimodoSearchConstants.predefinedVariable && QuantimodoSearchConstants.predefinedVariableAs) {
                console.log('Variable: ' + QuantimodoSearchConstants.predefinedVariable);
                console.log('Variable as: ' + QuantimodoSearchConstants.predefinedVariableAs);

                $scope.selectOutputAsType = QuantimodoSearchConstants.predefinedVariableAs;
                $scope.searchVariable = QuantimodoSearchConstants.predefinedVariable;
                $scope.showCorrelations($scope.searchVariable);
            }

            function refreshCorrelationsConsiderVotes() {
                var votedCorrelations = JSON.parse(localStorage.getItem('votedCorrelations'));
                if (votedCorrelations && $scope.correlations) {
                    for (var i = 0; i < votedCorrelations.length; i++) {
                        var votedCorrelation = votedCorrelations[i];
                        for (var j = 0; j < $scope.correlations.length; j++) {
                            if (votedCorrelation.effect === $scope.correlations[j].originalCorrelation.effect) {
                                if (votedCorrelation.cause === $scope.correlations[j].originalCorrelation.cause) {
                                    $scope.correlations[j].originalCorrelation.userVote = votedCorrelation.like;
                                }
                            }
                        }
                    }
                }
            };

        }

    ]);


quantimodoSearch.controller('voteModalInstanceController', function ($scope, $uibModalInstance, confirmationOptions) {

    console.log(confirmationOptions);

    $scope.opts = confirmationOptions;

    $scope.ok = function () {
        $uibModalInstance.close('confirm');
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };

});


// The service
quantimodoSearch.service('QuantimodoSearchService', function ($http) {
    this.getData = function (url, p, f) {
        $http.get(url, {params: p}).then(function (response) {
            f(response.data);
        });
    };

    this.vote = function (correlation, vote, callback) {
        $http.post(QuantimodoSearchConstants.sourceURL + QuantimodoSearchConstants.voteURL, {
            cause: correlation.cause,
            correlation: correlation.correlationCoefficient,
            effect: correlation.effect,
            vote: vote
        }).then(function (response) {
            callback(response.data);
        });
    }
});

quantimodoSearch.service('correlationsVoteHelper', function () {

    this.getPreviouslyVoted = function (correlation) {
        var votedCorrelations = JSON.parse(localStorage.getItem('votedCorrelations'));
        if (votedCorrelations) {
            for (var i = 0; i < votedCorrelations.length; i++) {
                if (correlation.effect === votedCorrelations[i].effect) {
                    if (correlation.cause === votedCorrelations[i].cause) {
                        return votedCorrelations[i].like;
                    }
                }
            }
        }
    };

    this.saveVotedCorrelation = function (correlation, vote) {

        var votedCorrelations = JSON.parse(localStorage.getItem('votedCorrelations'));

        var correlationToSave = {
            cause: correlation.cause,
            effect: correlation.effect,
            like: vote
        };

        if (votedCorrelations) {
            var found = false;
            for (var i = 0; i < votedCorrelations.length; i++) {
                if (correlation.effect === votedCorrelations[i].effect) {
                    if (correlation.cause === votedCorrelations[i].cause) {
                        votedCorrelations[i] = correlationToSave;
                        found = true;
                        break;
                    }
                }
            }
            if (!found) {
                votedCorrelations.push(correlationToSave);
            }
        } else {
            votedCorrelations = [];
            votedCorrelations.push(correlationToSave);
        }

        localStorage.setItem('votedCorrelations', JSON.stringify(votedCorrelations));

    }

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


//quantimodo factoty to interact with API server
/*quantimodoSearch.factory('QuantiModo', function($http, $q, authService){
 var QuantiModo = {};

 // POST method with the added token
 QuantiModo.post = function(baseURL, requiredFields, items, successHandler, errorHandler){
 authService.getAccessToken().then(function(token){

 console.log("TOKKEN : ", token.accessToken);
 // configure params
 for (var i = 0; i < items.length; i++)
 {
 var item = items[i];
 for (var j = 0; j < requiredFields.length; j++) {
 if (!(requiredFields[j] in item)) {
 throw 'missing required field in POST data; required fields: ' + requiredFields.toString();
 }
 }
 }

 // configure request
 var request = {
 method : 'POST',
 url: config.getURL(baseURL),
 responseType: 'json',
 headers : {
 "Authorization" : "Bearer " + token.accessToken,
 'Content-Type': "application/json"
 },
 data : JSON.stringify(items)
 };

 // mashape headers
 if(config.get('use_mashape') && config.getMashapeKey()){
 request.headers['X-Mashape-Key'] = config.getMashapeKey();
 console.log('added mashape_key', request.headers);
 }

 $http(request).success(successHandler).error(function(data,status,headers,config){
 Bugsnag.notify("API Request to "+request.url+" Failed",data.error.message,{},"error");
 errorHandler(data,status,headers,config);
 });

 }, errorHandler);
 };

 // post a vote
 QuantiModo.postVote = function(correlationSet, successHandler , errorHandler){
 QuantiModo.post('api/v1/votes',
 ['cause', 'effect', 'correlation', 'vote'],
 correlationSet,
 successHandler,
 errorHandler);
 };

 return QuantiModo;
 });*/



