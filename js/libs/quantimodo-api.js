// Quantimodo.com API. Requires JQuery.
Quantimodo = function () {

    var hostUrl = api_host + "/api/";

    var GET = function (baseURL, allowedParams, params, successHandler) {
        if (access_token) {
            var urlParams = [];
            for (var key in params) {
                if (jQuery.inArray(key, allowedParams) == -1) {
                    throw 'invalid parameter; allowed parameters: ' + allowedParams.toString();
                }
                urlParams.push(encodeURIComponent(key) + '=' + encodeURIComponent(params[key]));
            }

            var results = [];

            fetchAPI(0);

        } else {
            window.location.href = "?connect=quantimodo";
        }

        function fetchAPI(offset) {

            var url = hostUrl;

            if (urlParams.length == 0) {
                url += baseURL + "?offset=" + offset + "&limit=200";
            } else {
                url += baseURL + '?' + urlParams.join('&') + "&offset=" + offset + "&limit=200";
            }
            console.debug('Fecthing: ' + url);
            jQuery.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                contentType: 'application/json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + access_token);
                },
                success: function (data, status, xhr) {

                    if (data.constructor === Array) {
                        console.debug('Fetched: ' + data.length + ' items');
                        if (data.length > 0) {
                            results = results.concat(data);
                            fetchAPI(results.length);
                        } else {
                            successHandler(results);
                        }
                    } else {
                        successHandler(data)
                    }
                }
            });
        }
    };

    var POST = function (baseURL, requiredFields, items, successHandler) {
        console.debug('POST API Call');
        if (access_token) {
            for (var i = 0; i < items.length; i++) {
                var item = items[i];
                for (var j = 0; j < requiredFields.length; j++) {
                    if (!(requiredFields[j] in item)) {
                        throw 'missing required field in POST data; required fields: ' + requiredFields.toString();
                    }
                }
            }
            jQuery.ajax({
                type: 'POST',
                url: hostUrl + baseURL,
                contentType: 'application/json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Authorization", "Bearer " + access_token);
                },
                data: JSON.stringify(items),
                dataType: 'json',
                success: successHandler
            });
        } else {
            window.location.href = "?connect=quantimodo";
        }
    };

    var localCache = {

        timeOut: 1000 * 420,    //cache valid for 7 minutes

        exist: function (key) {
            if (localStorage.getItem(key)) {

                var cachedData = JSON.parse(localStorage.getItem(key));

                return new Date().getTime() - cachedData.cachedAt < localCache.timeOut;

            }
        },

        get: function (key) {

            var cachedData = JSON.parse(localStorage.getItem(key));

            return cachedData.payload;

        },

        set: function (key, data) {

            localStorage.removeItem(key);

            var dataToCache = JSON.stringify({
                cachedAt: new Date().getTime(),
                payload: data
            });

            localStorage.setItem(key, dataToCache)

        }

    };

    return {
        getMeasurements: function (params, f) {
            GET('measurements', [
                'variableName',
                'startTime',
                'endTime',
                'groupingWidth',
                'groupingTimezone',
                'source'], params, f);
        },
        getDailyMeasurements: function (params, f) {
            GET('v1/measurements/daily', [
                'variableName',
                'startTime',
                'endTime',
                'groupingWidth',
                'groupingTimezone'], params, f);
        },
        postMeasurements: function (measurements, f) {
            POST('measurements', [
                'source',
                'variable',
                'combinationOperation',
                'timestamp',
                'value',
                'unit'], measurements, f);
        },
        postMeasurementsV2: function (measurementset, f) {
            POST('measurements/v2', [
                'measurements',
                'name',
                'source',
                'category',
                'combinationOperation',
                'unit'], measurementset, f);
        },
        deleteVariableMeasurements: function (variables, f) {
            POST('measurements/delete', [
                'variableId',
                'variableName'], variables, f);
        },

        getMeasurementsRange: function (params, f) {
            GET('measurementsRange', [], params, f);
        },

        getMeasurementSources: function (params, f) {
            GET('measurementSources', [], params, f);
        },
        postMeasurementSources: function (measurements, f) {
            POST('measurementSources', ['name'], measurements, f);
        },

        getUnits: function (params, f) {
            GET('units', [
                'unitName',
                'abbreviatedUnitName',
                'categoryName'], params, f);
        },
        getUnitsForVariable: function (params, f) {
            GET('unitsVariable', [
                'variable',
                'unitName',
                'abbreviatedUnitName',
                'categoryName'], params, f);
        },
        postUnits: function (measurements, f) {
            POST('units', [
                'name',
                'abbreviatedName',
                'category',
                'conversionSteps'], measurements, f);
        },

        getUnitCategories: function (params, f) {
            GET('unitCategories', [], params, f);
        },
        postUnitCategories: function (measurements, f) {
            POST('unitCategories', ['name'], measurements, f);
        },

        getVariables: function (params, f) {

            if (localCache.exist('qmVariables')) {
                f(localCache.get('qmVariables'));
            } else {
                GET('variables', ['categoryName'], params, function (variables) {
                    localCache.set('qmVariables', variables);
                    f(variables);
                });
            }

        },
        postVariables: function (measurements, f) {
            POST('variables', ['name', 'category', 'unit', 'combinationOperation'], measurements, f);
        },

        searchVariables: function (query, f) {
            GET('variables/search/' + query, ['categoryName'], null, f);
        },

        getVariableCategories: function (params, f) {
            GET('variableCategories', [], params, f);
        },
        postVariableCategories: function (measurements, f) {
            POST('variableCategories', ['name'], measurements, f);
        },

        getPairs: function (params, f) {
            GET('pairs', [
                'cause',
                'effect',
                'duration',
                'delay', 
                'startTime',
                'endTime',
                'causeSource',
                'effectSource',
                'causeUnit',
                'effectUnit'], params, f);
        },

        getUserVariables: function (params, f) {
            GET('userVariables', ['variableName'], params, f);
        },
        postUserVariables: function (measurements, f) {
            POST('userVariables', ['variable'], measurements, f);
        },

        getCorrelations: function (params, f) {
            GET('correlations', ['effect'], params, f);
        },

        getCorrelateShare: function (params, f) {
            GET('share', ['id'], params, f);
        },
        postCorrelateShare: function (measurements, f) {
            POST('share', ['type', 'inputVariable', 'outputVariable'], measurements, f);
        },

        connectorsInterface: function (baseURL, defaultConnector) {
            this.params = {
                baseURL: typeof baseURL == 'undefined' ? hostUrl : hostUrl + baseURL,
                connector: typeof defaultConnector == 'undefined' ? null : defaultConnector
            };
            this.connector = function (name) {
                this.params.connector = name;
                return this;
            };
            this.do = function () {
                var action = arguments[0],
                    params = {},
                    f = undefined;

                if (typeof arguments[1] == 'object') {
                    params = arguments[1];
                    f = arguments[2];
                } else if (typeof arguments[1] == 'function') {
                    f = arguments[1];
                }
                switch (action) {
                    case 'connect':
                        this.sendRequest('connectors/' + this.params.connector + '/connect', params, f);
                        break;
                    case 'disconnect':
                        this.sendRequest('connectors/' + this.params.connector + '/disconnect', params, f);
                        break;
                    case 'update':
                        this.sendRequest('connectors/' + this.params.connector + '/update', params, f);
                        break;
                    case 'info':
                        this.sendRequest('connectors/' + this.params.connector + '/info', params, f);
                        break;
                }
            };
            this.listConnectors = function (f) {
                this.sendRequest('connectors/list', {}, f);
            };
            this.sendRequest = function (url, params, f) {
                console.debug('API Call via QM JS SDK ');
                if (access_token) {
                    var that = this;
                    jQuery.ajax(this.params.baseURL + url, {
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader("Authorization", "Bearer " + access_token);
                        },
                        data: params,
                        dataType: 'json'
                    }).done(function (data) {
                        if (typeof f != 'undefined') {
                            f(data, that.params.connector);
                        }
                    });
                } else {
                    window.location.href = '?connect=quantimodo';
                }

            };
        },

        url: hostUrl
    };
}();

