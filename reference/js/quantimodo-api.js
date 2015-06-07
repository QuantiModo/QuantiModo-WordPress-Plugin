// Quantimodo.com API. Requires JQuery.
Quantimodo = function() 
{

	var url = document.location.origin + "/api/";
		
	var GET = function (baseURL, allowedParams, params, successHandler) 
	{
		var urlParams = [];
		for (var key in params) 
		{
			if (jQuery.inArray(key, allowedParams) == -1) 
			{ 
				throw 'invalid parameter; allowed parameters: ' + allowedParams.toString(); 
			}
			urlParams.push(encodeURIComponent(key) + '=' + encodeURIComponent(params[key]));
		}
		jQuery.ajax({	type: 'GET', 
						url: (url + ((urlParams.length == 0) ? baseURL : baseURL + '?' + urlParams.join('&'))), 
						dataType: 'json', 
						contentType: 'application/json', 
						xhrFields: {withCredentials: true},
						success: successHandler});
	};
	
	var POST = function (baseURL, requiredFields, items, successHandler) 
	{
		for (var i = 0; i < items.length; i++) 
		{
			var item = items[i];
			for (var j = 0; j < requiredFields.length; j++) { if (!(requiredFields[j] in item)) { throw 'missing required field in POST data; required fields: ' + requiredFields.toString(); } }
		}
		jQuery.ajax({	type: 'POST', 
						url: url + baseURL, 
						contentType: 'application/json', 
						xhrFields: {withCredentials: true},
						data: JSON.stringify(items), 
						dataType: 'json', 
						success: successHandler});
	};

	return {
		getMeasurements: function(params, f) { GET('measurements', ['variableName', 'startTime', 'endTime', 'groupingWidth', 'groupingTimezone', 'source'], params, f); },
		postMeasurements: function(measurements, f) { POST('measurements', ['source', 'variable', 'combinationOperation', 'timestamp', 'value', 'unit'], measurements, f); },
		postMeasurementsV2: function(measurementset, f) { POST('measurements/v2', ['measurements', 'name', 'source', 'category', 'combinationOperation', 'unit'], measurementset, f); },
		deleteVariableMeasurements: function(variables, f) { POST('measurements/delete', ['variableId', 'variableName'], variables, f); },
		                
		getMeasurementsRange: function(params, f) { GET('measurementsRange', [], params, f); },
		
		getMeasurementSources: function(params, f) { GET('measurementSources', [], params, f); },
		postMeasurementSources: function(measurements, f) { POST('measurementSources', ['name'], measurements, f); },
		
		getUnits: function(params, f) { GET('units', ['unitName', 'abbreviatedUnitName', 'categoryName'], params, f); },
		getUnitsForVariable: function(params, f) { GET('unitsVariable', ['variable', 'unitName', 'abbreviatedUnitName', 'categoryName'], params, f); },
		postUnits: function(measurements, f) { POST('units', ['name', 'abbreviatedName', 'category', 'conversionSteps'], measurements, f); },
		
		getUnitCategories: function(params, f) { GET('unitCategories', [], params, f); },
		postUnitCategories: function(measurements, f) { POST('unitCategories', ['name'], measurements, f); },
		
		getVariables: function(params, f) { GET('variables', ['categoryName'], params, f); },
		postVariables: function(measurements, f) { POST('variables', ['name', 'category', 'unit', 'combinationOperation'], measurements, f); },
		
		getVariableCategories: function(params, f) { GET('variableCategories', [], params, f); },
		postVariableCategories: function(measurements, f) { POST('variableCategories', ['name'], measurements, f); },

		getPairs: function(params, f) { GET('pairs', ["cause", "effect", "duration", "delay", "startTime", "endTime", "causeSource", "effectSource", "causeUnit", "effectUnit"], params, f); },
		
		getVariableUserSettings: function(params, f) { GET('variableUserSettings', ['variableName'], params, f); },
		postVariableUserSettings: function(measurements, f) { POST('variableUserSettings', ['variable'], measurements, f); },

		getCorrelations: function(params, f) { GET('correlations', ['effect'], params, f); },
		
		getCorrelateShare: function(params, f) { GET('share', ['id'], params, f); },
		postCorrelateShare: function(measurements, f) { POST('share', ['type', 'inputVariable', 'outputVariable'], measurements, f); },

		connectorsInterface: function(baseURL, defaultConnector) {
			this.params = {
				baseURL: typeof baseURL == 'undefined' ? '/' : baseURL,
				connector: typeof defaultConnector == 'undefined' ? null : defaultConnector
			};
			this.connector = function(name) {
				this.params.connector = name;
				return this;
			};
			this.do = function() {
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
						this.sendRequest('api/connectors/' + this.params.connector + '/connect', params, f);
						break;
					case 'disconnect':
						this.sendRequest('api/connectors/' + this.params.connector + '/disconnect', params, f);
						break;
					case 'update':
						this.sendRequest('api/connectors/' + this.params.connector + '/update', params, f);
						break;
					case 'info':
						this.sendRequest('api/connectors/' + this.params.connector + '/info', params, f);
						break;
				}
			};
			this.listConnectors = function(f) {
				this.sendRequest('api/connectors/list', {}, f);
			};
			this.sendRequest = function(url, params, f) {
				var that = this;
				jQuery.ajax(this.params.baseURL + url, {
					xhrFields: {withCredentials: true},
					data: params,
					dataType: 'json'
				}).done(function(data) {
					if (typeof f != 'undefined') {
						f(data, that.params.connector);
					}
				});
			};
		},
		
		url:url	
	};
}();

