var QuantimodoSearchConstants = {
    sourceURL: apiHost + '/api/',
    vURL: 'public/variables/search/',
    publicURL: 'public/correlations/search/',
    privateURL: 'v1/correlations?',
    voteURL: 'v1/votes',
    predOfURL: apiHost + '/api/v1/variables/_VARIABLE_/public/causes',
    predByURL: apiHost + '/api/v1/variables/_VARIABLE_/public/effects',
    method: 'JSONP',
    predefinedVariable: (typeof qmwpShortCodeDefinedVariable !== 'undefined') ? qmwpShortCodeDefinedVariable : null,
    predefinedVariableAs: (typeof qmwpShortCodeDefinedVariableAs !== 'undefined') ? qmwpShortCodeDefinedVariableAs : null,
    commonOrUser: (typeof qmwpCommonOrUser !== 'undefined') ? qmwpCommonOrUser : null,
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
});

// The controller
quantimodoSearch.controller('QuantimodoSearchController', ['$scope', 'QuantimodoSearchService', '$uibModal', 'correlationsVoteHelper',
    function ($scope, QuantimodoSearchService, $uibModal, correlationsVoteHelper) {
        $scope.correlations = [];
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
            if ($scope.totalCorrelations) {
                return $scope.totalCorrelations.length > 10;
            } else {
                return false;
            }

        };

        $scope.isNotEmpty = function (correlations) {
            return correlations.length > 0;
        };

        $scope.showCorrelations = function (variable) {
            var timeToSearch = (new Date()).getTime();
            var correlationURL = '';
            $scope.homeShown = false;
            $scope.autoLoad = true;

            var queryVariable = variable.replace(/ /g, '+');


            if (QuantimodoSearchConstants.commonOrUser === 'common') {
                correlationURL = QuantimodoSearchConstants.sourceURL +
                    QuantimodoSearchConstants.publicURL + queryVariable +
                    '?effectOrCause=' + $scope.selectOutputAsType;
            } else if (QuantimodoSearchConstants.commonOrUser === 'user') {
                correlationURL = QuantimodoSearchConstants.sourceURL +
                    QuantimodoSearchConstants.privateURL + $scope.selectOutputAsType + '=' + queryVariable;
            }


            correlationURL = correlationURL.replace('_VARIABLE_', queryVariable);

            QuantimodoSearchService.getData(correlationURL, null,
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

                    correlationSet.originalCorrelation.userVote = likeValue;

                })

            }, function () {
                console.debug('dismissed');
            });

        };

        $scope.addMeasurement = function (correlation) {

            var variable = correlation.effect;

            if (QuantimodoSearchConstants.predefinedVariableAs === 'effect') {
                variable = correlation.cause;
            }

            console.log('Going to add measurement for variable: ' + variable);
            QuantimodoSearchService.getVariableByName(variable, function (varDetails) {
                console.log(varDetails);

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

        $scope.openVarSettingsModal = function (correlation) {

            var variable = correlation.effect;

            if (QuantimodoSearchConstants.predefinedVariableAs === 'effect') {
                variable = correlation.cause;
            }

            console.log('Going change setting for: ' + variable);
            QuantimodoSearchService.getVariableByName(variable, function (varDetails) {
                QuantimodoSearchService.getUnitsForVariableByName(varDetails.name, function (units) {

                    console.log('Variable details:' + varDetails);

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

        if (QuantimodoSearchConstants.predefinedVariable && QuantimodoSearchConstants.predefinedVariableAs) {
            console.log('Variable: ' + QuantimodoSearchConstants.predefinedVariable);
            console.log('Variable as: ' + QuantimodoSearchConstants.predefinedVariableAs);

            $scope.selectOutputAsType = QuantimodoSearchConstants.predefinedVariableAs;
            $scope.searchVariable = QuantimodoSearchConstants.predefinedVariable;
            $scope.showCorrelations($scope.searchVariable);
        }

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

    this.getVariableByName = function (varName, callback) {
        $http.get(QuantimodoSearchConstants.sourceURL + 'v1/variables/' + varName).then(function (response) {
            callback(response.data);
        });
    };

    this.addMeasurement = function (measurement, callback) {
        console.log('Going to post this measurement:');
        console.log(measurement);

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




