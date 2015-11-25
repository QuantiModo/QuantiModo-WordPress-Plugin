var QuantimodoSearchConstants = {
    sourceURL: apiHost + '/api/',
    vURL: 'public/variables/search/',
    publicURL: 'public/correlations/search/',
    privateURL: 'v1/correlations?',
    voteURL: 'v1/votes',
    predOfURL: apiHost + '/api/v1/variables/_VARIABLE_/public/causes',
    predByURL: apiHost + '/api/v1/variables/_VARIABLE_/public/effects',
    method: 'JSONP',
    commonOrUser: (typeof qmwpCommonOrUser !== 'undefined') ? qmwpCommonOrUser : null,
};

// Define a new module for our search page app
var quantimodoSearch = angular.module('quantimodoSearch', ['ui.bootstrap', 'angular-loading-bar']);

// Define config
quantimodoSearch.config(function ($httpProvider) {
    if (typeof accessToken !== 'undefined' && accessToken) {
        $httpProvider.defaults.headers.common.Authorization = 'Bearer ' + accessToken;
    }
});

// The controller
quantimodoSearch.controller('QuantimodoSearchController', ['$scope', 'QuantimodoSearchService', '$uibModal', 'correlationsVoteHelper',
    function ($scope, QuantimodoSearchService, $uibModal, correlationsVoteHelper) {

        $scope.showResults = false;

        $scope.correlations = [];
        $scope.totalCorrelations = [];

        $scope.maxSize = 10;
        $scope.itemsPerPage = 10;

        $scope.countAndTime = '';

        $scope.predictorVariableName = null || qmwpPredictor;
        $scope.outcomeVariableName = null || qmwpOutcome;

        $scope.displayPage = function (pageNumber) {
            $scope.correlations =
                $scope.totalCorrelations.slice(
                    (pageNumber - 1) * $scope.itemsPerPage, $scope.itemsPerPage * pageNumber
                );
        };

        $scope.showCorrelations = function () {

            if ($scope.outcomeVariableName || $scope.predictorVariableName) {

                var timeSearchStarted = new Date();
                QuantimodoSearchService.searchCorrelations($scope.predictorVariableName, $scope.outcomeVariableName)
                    .then(function (correlations) {
                        var timeSearchEnded = new Date();
                        $scope.timeTakenForSearch = Math.ceil((timeSearchEnded - timeSearchStarted) / 1000);

                        console.debug('Correlations fetching response:', correlations);

                        if ($scope.outcomeVariableName && !$scope.predictorVariableName) {
                            //only outcome is set

                            $scope.totalCorrelations = jQuery.map(correlations.data, function (correlation) {

                                return {
                                    variableName: correlation.causeName,
                                    variableCategory: correlation.causeCategory,
                                    explanation: correlation.predictorExplanation,  //TODO do predictor always here?
                                    correlation: correlation
                                }

                            });

                        } else if ($scope.predictorVariableName && !$scope.outcomeVariableName) {
                            //only predictor is set

                            $scope.totalCorrelations = jQuery.map(correlations.data, function (correlation) {

                                return {
                                    variableName: correlation.effectName,
                                    variableCategory: correlation.effectCategory,
                                    explanation: correlation.predictorExplanation,  //TODO do predictor always here?
                                    correlation: correlation
                                }

                            });

                        } else if ($scope.outcomeVariableName && $scope.predictorVariableName) {
                            //both: predictor and outcome  are set

                            $scope.totalCorrelations = jQuery.map(correlations.data, function (correlation) {

                                return {
                                    variableName: correlation.causeName,
                                    variableCategory: correlation.causeCategory,
                                    explanation: correlation.causeExplanation,  //TODO do predictor always here?
                                    correlation: correlation
                                }

                            });

                        }

                        $scope.displayPage(1);

                        $scope.showResults = true;

                    });

            } else {
                //both fields are empty. we will hide results
                $scope.showResults = false;
            }
        };

        $scope.vote = function (correlationSet, likeValue) {

            var modalInstance = $uibModal.open({
                templateUrl: qmwpPluginUrl + '/templates/search-correlations/vote-confirm-modal.html',
                controller: 'voteModalInstanceController',
                resolve: {
                    confirmationOptions: function () {
                        return {
                            correlation: correlationSet.correlation,
                            likeValue: likeValue
                        }
                    }
                }
            });

            modalInstance.result.then(function () {
                //confirmed
                var prevVoted = correlationsVoteHelper.getPreviouslyVoted(correlationSet.correlation);

                if (prevVoted === likeValue) {
                    likeValue = 'null';
                }

                if (likeValue !== 'null') {
                    QuantimodoSearchService.vote(correlationSet.correlation, likeValue, function (resp) {

                        correlationsVoteHelper.saveVotedCorrelation(correlationSet.correlation, likeValue);

                        correlationSet.correlation.userVote = likeValue;

                    });
                } else {
                    QuantimodoSearchService.deleteVote(correlationSet.correlation, function (resp) {

                        correlationsVoteHelper.saveVotedCorrelation(correlationSet.correlation, likeValue);

                        correlationSet.correlation.userVote = likeValue;

                    });
                }

            }, function () {
                console.debug('dismissed');
            });

        };

        $scope.addMeasurement = function (correlationSet) {

            var variable = correlationSet.variableName;
            console.log('Going to add measurement for variable: ', variable);
            QuantimodoSearchService.getVariableByName(variable, function (varDetails) {

                QuantimodoSearchService.getUnits(function (units) {

                    var modalInstance = $uibModal.open({
                        templateUrl: qmwpPluginUrl + '/templates/search-correlations/add-measurement-modal.html',
                        controller: 'addMeasurementModalInstanceController',
                        resolve: {
                            variable: function () {
                                return varDetails;
                            },
                            units: function () {
                                return units;
                            }
                        }
                    });

                    modalInstance.result.then(function (measurement) {
                        //confirmed
                        QuantimodoSearchService.addMeasurement(
                            [{
                                measurements: [{
                                    value: measurement.value,
                                    timestamp: moment(new Date(measurement.date)).unix()
                                }],
                                name: measurement.variable.name,
                                source: 'QuantiModo',
                                category: measurement.variable.category,
                                combinationOperation: measurement.variable.combinationOperation,
                                unit: measurement.variable.abbreviatedUnitName
                            }],
                            function (result) {
                                console.log(result);
                            });
                    }, function () {
                        console.debug('dismissed');
                    });

                });
            });

        };

        $scope.openVarSettingsModal = function (correlationSet) {

            var variable = correlationSet.variableName;
            console.log('Going change setting for variable: ', variable);
            QuantimodoSearchService.getVariableByName(variable, function (varDetails) {
                QuantimodoSearchService.getUnitsForVariableByName(varDetails.name, function (units) {

                    console.log('Variable details:', varDetails);

                    var modalInstance = $uibModal.open({
                        templateUrl: qmwpPluginUrl + '/templates/search-correlations/variable-settings-modal.html',
                        controller: 'varSettingsModalInstanceController',
                        resolve: {
                            variable: function () {
                                return varDetails;
                            },
                            varUnits: function () {
                                return units.data;
                            }
                        }
                    });

                    modalInstance.result.then(function () {
                        console.log('confirmed');
                    }, function () {
                        console.debug('dismissed');
                    });

                });
            });

        };

        $scope.getToolTipText = function (toolTipFor, correlation) {

            var message = 'Help us improve our algorithms! ';

            if (toolTipFor === 'thumbUp') {

                message += "Give this a thumbs up if you think it's plausible " +
                    "that " + correlation.cause +
                    " could affect " + correlation.effect + ".";

            } else if (toolTipFor === 'thumbDown') {

                message += "Give this a thumbs down if you don't think it's plausible " +
                    "that " + correlation.cause + " could affect " + correlation.effect + ".";

            }

            return message;

        };

        if ($scope.predictorVariableName || $scope.outcomeVariableName) {
            $scope.showCorrelations();
        }

    }

]);

quantimodoSearch.controller('voteModalInstanceController', function ($scope, $uibModalInstance, confirmationOptions) {

    $scope.opts = confirmationOptions;

    $scope.ok = function () {
        $uibModalInstance.close('confirm');
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };

});

quantimodoSearch.controller('addMeasurementModalInstanceController', function ($scope, $uibModalInstance, variable, units) {

    $scope.variable = variable;
    $scope.units = units;

    $scope.mgmtVal = variable.mostCommonValue;

    $scope.mgmtDate = moment().format('lll');

    $scope.ok = function () {
        $uibModalInstance.close({
            variable: variable,
            value: $scope.mgmtVal,
            date: $scope.mgmtDate
        });
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };

});

quantimodoSearch.controller('varSettingsModalInstanceController',
    function ($scope, $uibModalInstance, QuantimodoSearchService, variable, varUnits) {

        $scope.varUnits = varUnits;
        $scope.variable = angular.copy(variable);
        $scope.assumeMissing = 'true';

        $scope.ok = function () {

            if ($scope.assumeMissing === 'true') {
                $scope.variable.fillingValue = null;
            }

            QuantimodoSearchService.getCurrentUserData(function (userData) {

                var variableSettings = [{
                    user: userData.id,
                    variable: variable.name,
                    name: $scope.variable.name,
                    durationOfAction: $scope.variable.durationOfAction,
                    fillingValue: $scope.variable.fillingValue,
                    maximumAllowedValue: $scope.variable.maximumAllowedValue,
                    minimumAllowedValue: $scope.variable.minimumAllowedValue,
                    onsetDelay: $scope.variable.onsetDelay,
                    unit: $scope.variable.abbreviatedUnitName
                }];

                QuantimodoSearchService.setVariableSettings(variableSettings, function (response) {
                    console.log(response);
                });

            });

            $uibModalInstance.close({
                variable: $scope.variable,
            });
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

    this.searchVariablesByName = function (query) {
        return $http.get(QuantimodoSearchConstants.sourceURL + 'public/variables/search/' + query);
    };

    this.searchCorrelations = function (cause, effect) {

        var requestUrl = QuantimodoSearchConstants.sourceURL + 'v1/correlations?';

        if (cause) {
            requestUrl += 'cause=' + cause + '&';
        }

        if (effect) {
            requestUrl += 'effect=' + effect;
        }

        return $http.get(requestUrl);

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
    };

    this.deleteVote = function (correlation, callback) {
        $http.post(QuantimodoSearchConstants.sourceURL + 'v1/votes/delete', {
            cause: correlation.cause,
            effect: correlation.effect
        }).then(function (response) {
            callback(response.data);
        })
    };

    this.getVariableByName = function (varName, callback) {
        $http.get(QuantimodoSearchConstants.sourceURL + 'v1/variables/' + varName).then(function (response) {
            callback(response.data);
        });
    };

    this.addMeasurement = function (measurement, callback) {
        console.debug('Going to post this measurement:', measurement);

        $http.post(QuantimodoSearchConstants.sourceURL + 'measurements/v2', measurement, function (result) {
            callback(result);
        });
    };

    this.getUnitsForVariableByName = function (variableName, callback) {
        $http.get(QuantimodoSearchConstants.sourceURL + 'v1/unitsVariable?variable=' + variableName)
            .then(function (response) {
                callback(response);
            });
    };

    this.getUnits = function (callback) {
        $http.get(QuantimodoSearchConstants.sourceURL + 'v1/units').then(function (response) {
            callback(response.data);
        })
    };

    this.setVariableSettings = function (variableSettings, callback) {
        $http.post(QuantimodoSearchConstants.sourceURL + 'v1/userVariables', variableSettings, function (response) {
            callback(response.data);
        });
    };

    this.getCurrentUserData = function (callback) {
        $http.get(QuantimodoSearchConstants.sourceURL + 'v1/user/me').then(function (response) {
            callback(response.data);
        });
    };

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

    };

});

// The autocomplete directive
quantimodoSearch.directive('autoComplete', ['QuantimodoSearchService',
    function (QuantimodoSearchService) {
        return {

            require: 'ngModel',

            link: function (scope, element, attrs, ngModel) {

                // init jqueryUi autocomplete
                element.autocomplete({

                    minLength: 0,

                    source: function (request, response) {

                        scope.showResults = false;

                        if (request.term.length >= 2) {

                            QuantimodoSearchService.searchVariablesByName(request.term)
                                .then(function (searchResponse) {
                                    response(
                                        jQuery.map(searchResponse.data, function (result) {
                                            console.debug(result);
                                            return {
                                                label: result.name,
                                                value: result.name
                                            }
                                        }));
                                });

                        } else if (request.term.length == 0) {
                            scope.showCorrelations();
                        }


                    },

                    select: function (event, ui) {
                        ngModel.$setViewValue(ui.item.value);
                        scope.$apply();
                        scope.showCorrelations();
                    }

                });
            }
        };
    }]);

quantimodoSearch.directive('dateTimePicker', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModelCtrl) {
            jQuery(function () {
                element.datetimepicker({
                    dayOfWeekStart: 1,
                    lang: 'en',
                    startDate: '1986/01/05',
                    format: 'M j, Y h:i A',
                    step: 10,
                    onChangeDateTime: function (date) {
                        ngModelCtrl.$setViewValue(moment(date).format('lll'));
                        scope.$apply();
                    }
                });
            });
        }
    };
});




