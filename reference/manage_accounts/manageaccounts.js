Connectors = function() {
	var handleReplacement = function(formId, url, data) {
		jQuery.ajax({
			type : 'POST',
			url : url,
			data : data,
			success : function(html) {
				jQuery(formId).html(html);
			}
		});
	};

	return {
		handleReplacement : handleReplacement,
		submitLumosityCredentials : function() {
			handleReplacement('#auth-lumosity',
					'/api/xtream/lumosity/submitCredentials', {
						username : jQuery('#lumosity-username').val(),
						password : jQuery('#lumosity-password').val()
					});
		},
		submitMyFitnessPalCredentials : function() {
			handleReplacement('#auth-myfitnesspal',
					'/api/xtream/myfitnesspal/submitCredentials', {
						username : jQuery('#myfitnesspal-username').val(),
						password : jQuery('#myfitnesspal-password').val()
					});
		},
		submitMoodscopeCredentials : function() {
			handleReplacement('#auth-moodscope',
					'/api/xtream/moodscope/submitCredentials', {
						username : jQuery('#moodscope-username').val(),
						password : jQuery('#moodscope-password').val()
					});
		},
		chooseWithingsUser : function() {
			handleReplacement('#auth-withings',
					'/api/xtream/withings/chooseWithingsUser', {
						chosenUser : jQuery('#withings-chosenUser:checked')
								.val()
					});
		},
		submitWithingsUsernameAndPassword : function() {
			handleReplacement('#auth-withings',
					'/api/xtream/withings/setupWithings', {
						username : jQuery('#withings-username').val(),
						password : jQuery('#withings-password').val()
					});
		}
	};
}();

ManageAccountsPage = function() {
	
	// If renderConnectorsTemplate is called early, wait for half a second and
	// check if it's been replaced with its final version
	var renderConnectorsTemplate = function() {
		setTimeout(renderConnectorsTemplate, 500);
	};

	
	/* View Update code */
	
	var viewTemplate = jQuery("#viewUpdates").html();

	function viewUpdates(connectorName) {
		var connectorName = connectorName.charAt(0).toUpperCase()
				+ connectorName.slice(1);
		jQuery.ajax({
			url : "/api/xtream/api/updates/" + connectorName
					+ "?page=0&pageSize=50",
			success : function(updates) {
				for (var i = 0; i < updates.length; i++)
					updates[i].time = formatDate(updates[i].ts, true);

				var html = Mustache.render(viewTemplate, {
					"updates" : updates
				});
				jQuery('#viewUpdatesDialog').html(html);

				jQuery('#update-dialog-background').css({
					'display' : 'block',
					'opacity' : 0.2
				});

				jQuery('#viewUpdatesModal').css({
					'display' : 'block',
					'opacity' : 1
				});
			}
		});
	}

	
	/* End view Update */
	
	function setToSyncing(connectorName) {
		var div = jQuery("#synStatus-" + connectorName);
		if (div.hasClass("nowSynchro"))
			return;
		div.addClass("nowSynchro");
		var syncLED = jQuery("#syncLED-" + connectorName);
		syncLED.removeClass("syncLED-yes");
		syncLED.removeClass("syncLED-no");
		syncLED.addClass("syncLED-waiting");
		syncLED.html("<span class=\"syncLED-waiting\">"
				+ "<img src=\"https://i.imgur.com/PlIolkh.gif\" alt=\"load\">"
				+ "</span>");
		var lastSync = jQuery("#connectorDialog-lastSync-" + connectorName);
		lastSync.html("Now synchronizing");
		var syncNowBtn = jQuery("#syncNow-" + connectorName);
		syncNowBtn.hide();
	}

	var reloadConnectorData = function() {
		var cleanArray = function(array) {
			var newArray = [];
			for (var i = 0; i < array.length; i++) {
				var el = array[i];
				switch (el.name) {
				// Connectors to be hidden
				case 'api/xtream Capture':
				case 'Google Latitude':
				case 'Zeo':
					break;
				default:
					newArray.push(el);
				}
			}
			newArray.sort(function(a, b) {
				return (a.name < b.name) ? -1 : (a.name == b.name) ? 0 : 1;
			});
			return newArray;
		};

		function getConnectorParams(data) {
			var params = {};
			for ( var member in data) {
				switch (member) {
				default:
					params[member] = data[member];
					break;
				case "latestData":
				case "lastSync":
					var formatted = formatDate(data[member], true);
					if (formatted == "Present")
						formatted = member == "lastSync" ? "Never" : "No Data";
					params[member] = formatted;
					break;
				}
			}
			return params;
		}

		var handleFinish = function(installedConnectors, uninstalledConnectors) {
			var data = installedConnectors.concat(uninstalledConnectors);

			var params = [];
			for (var i = 0; i < data.length; i++) {
				if (!data[i].manageable)
					continue;
				params[i] = getConnectorParams(data[i]);
			}

			jQuery('#connectorInfoTable').html(
					Mustache.render(jQuery("#connectorsTemplate").html(), {
						"connectors" : params
					}));

			// Handle clicking delete
			jQuery('.remove-button').bind('click', function() {
				var el = jQuery(this);
				var id = el.attr('id');
				var connectorName = id.replace(/^remove-/, '');
				el.html('Deleting...');
				jQuery.ajax({
					type : 'DELETE',
					url : '/api/xtream/api/connectors/' + connectorName,
					async : false,
					success : reloadConnectorData
				});
				location.reload();
			});

			
			//Handle Clicking on connector block
			jQuery('.connectorBlock').bind('click', function() {
				var el = jQuery(this);
				var id = el.attr('id');
				
				var connectorName = id.replace(/^connector-/, '');
				
				jQuery("#showDialog-" + connectorName).css({
					'display' : 'block',
					'opacity' : 1
				});

				
				jQuery("#login-dialog-background").css({
					'display' : 'block',
					'opacity' : 0.5
				});
				
			});

			jQuery('.connectorBlock').each(function() {
				var el = jQuery(this);
				var id = el.attr('id');
				var connectorName = id.replace(/^connector-/, '');
				jQuery('#showDialog-' + connectorName).css({
					'display' : 'none',
					'opacity' : 0
				});

			});

			jQuery('.closeDialog').bind('click', function() {
				var el = jQuery(this);
				var id = el.attr('id');
				var connectorName = id.replace(/^closeDialog-/, '');
				jQuery('#showDialog-' + connectorName).css({
					'display' : 'none',
					'opacity' : 0
				});
				jQuery("#login-dialog-background").css({
					'display' : 'none',
					'opacity' : 0
				});
				

				if(jQuery("#showDialog-"+ connectorName).find('input.successClose-button').length){
					location.reload();
				}

			});
			
			jQuery('#updatedialog-close').bind('click', function() {
				jQuery('#viewUpdatesModal').css({
					'display' : 'none',
					'opacity' : 0
				});

				jQuery('#update-dialog-background').css({
					'display' : 'none',
					'opacity' : 0
				});
			});

			jQuery('.remove-button').each(function() {
				var el = jQuery(this);
				var id = el.attr('id');
				var connectorName = id.replace(/^remove-/, '');
				var viewDataBtn = jQuery("#viewUpdates-" + connectorName);
				viewDataBtn.off("click");
				viewDataBtn.click(function(event) {
					event.preventDefault();
					viewUpdates(connectorName);
				});
				var syncNowBtn = jQuery("#syncNow-" + connectorName);
				syncNowBtn.click(function(event) {
					event.preventDefault();
					setToSyncing(connectorName);
					
					jQuery.ajax("/api/xtream/api/sync/" + connectorName, {
						type : "POST",
						success: function(data){
							jQuery("#connectorDialog-lastSync-" + connectorName).html(formatDate(data[0].when,true));
							var div = jQuery("#synStatus-" + connectorName);
							div.removeClass("nowSynchro");
							var syncLED = jQuery("#syncLED-" + connectorName);
							syncLED.removeClass("syncLED-no");
							syncLED.removeClass("syncLED-waiting");
							jQuery(".syncLED-waiting").remove();
							syncLED.addClass("syncLED-yes");
							jQuery("#syncNow-" + connectorName).show();
		                },
		                error: function(XMLHttpRequest, textStatus, errorThrown) { 
		                	jQuery("#connectorDialog-lastSync-" + connectorName).html("Failed");
		                	var div = jQuery("#synStatus-" + connectorName);
							div.removeClass("nowSynchro");
		                    var syncLED = jQuery("#syncLED-" + connectorName);
							jQuery(".syncLED-waiting").remove();
							syncLED.addClass("syncLED-no");
							jQuery("#syncNow-" + connectorName).show();
		                }    
					});
				});

			});

			var setupFormReplacement = function(el, connectorName, formId,
					submitUrl) {
				el
						.html('<form name="'
								+ connectorName
								+ '-auth-form">'
								+ '<label for="'
								+ connectorName
								+ '-username"> <input type="text" id="'
								+ connectorName
								+ '-username" name="username"  placeholder="Username" value="" size="15"></label>'
								+ '<label for="'
								+ connectorName
								+ '-password"> <input type="password" id="'
								+ connectorName
								+ '-password" name="password"   placeholder="Password" value="" size="15"></label>'
								+ '<input type="submit" value="Connect">'
								+ '</form>');
				// Precompute the strings so that urlName and connectorName and
				// so on can be garbage collected now and things are more
				// efficient later
				var usernameSelector = '#' + connectorName + '-username';
				var passwordSelector = '#' + connectorName + '-password';
				jQuery('form[name="' + connectorName + '-auth-form"]').submit(
						function(event) {
							Connectors.handleReplacement(formId, submitUrl, {
								username : jQuery(usernameSelector).val(),
								password : jQuery(passwordSelector).val()
							});
							event.preventDefault();
						});
			};

			// Create authentication forms
			jQuery('.auth')
					.each(
							function() {
								var el = jQuery(this);
								var id = el.attr('id');
								var connectorName = id.replace(/^auth-/, '');
								var urlName = connectorName;
								switch (urlName) {
								case 'google_calendar':
									urlName = 'calendar';
									break;
								}
								switch (connectorName) {
								case 'bodymedia':
								case 'flickr':
								case 'fitbit':
								case 'google_calendar':
								case 'lastfm':
								case 'runkeeper':
								case 'twitter':
									el
											.html('<form name="'
													+ connectorName
													+ '-auth-form" action="/api/xtream/'
													+ urlName
													+ '/token" method="get">'
													+ '<input type="submit" style="padding: 8px 15px;" value="Connect">'
													+ '</form>');
									break;

								case 'moodscope':
									setupFormReplacement(el, connectorName, '#'
											+ id, '/api/xtream/' + urlName
											+ '/submitCredentials');
									break;
								case 'myfitnesspal':
									setupFormReplacement(el, connectorName, '#'
											+ id, '/api/xtream/' + urlName
											+ '/submitCredentials');
									break;
								case 'withings':
									setupFormReplacement(el, connectorName, '#'
											+ id, '/api/xtream/' + urlName
											+ '/setup'
											+ urlName.charAt(0).toUpperCase()
											+ urlName.slice(1));
									break;
								case 'quantifiedmind':
									el
											.html('<form name="'
													+ connectorName
													+ '-auth-form" action="/api/xtream/'
													+ urlName
													+ '/setToken" method="post">'
													+ '<label for="'
													+ connectorName
													+ '-username"><a href="https://quantified-mind.com/authenticate/get_token" target="_blank">Username</a>: <input type="text" name="username" value="" size="15"></label>'
													+ '<label for="'
													+ connectorName
													+ '-token"><a href="https://quantified-mind.com/authenticate/get_token" target="_blank">Token</a>: <input type="text" name="token" value="" size="15"></label>'
													+ '<input type="submit" value="Connect">'
													+ '</form>');
								case 'mymee':
									el
											.html('<form name="'
													+ connectorName
													+ '-auth-form" action="/api/xtream/'
													+ urlName
													+ '/setAuthInfo" method="post">'
													+ '<label for="'
													+ connectorName
													+ '-username"><input type="text" name="username" value="" size="15" placeholder="Username"></label>'
													+ '<label for="'
													+ connectorName
													+ '-password"> <input type="password" name="password" value="" size="15" placeholder="Password">'
													+ '<input type="text" name="activationCode" value="" size="15" placeholder="Activation Code"></label>'
													+ '<input type="submit" value="Connect">'
													+ '</form>');
									break;
								case 'github':
									el
											.html('<form name="'
													+ connectorName
													+ '-auth-form" action="https://api.singly.com/authorize" method="GET">'
													+ '<input type="hidden" name="client_id" value="4537f968e8d5298aba2c4fc3bfde2cf5">'
													+ '<input type="hidden" name="redirect_uri" value="'
													+ window.location.origin
													+ '/api/xtream/singly/github/callback">'
													+ '<input type="hidden" name="service" value="github">'
													+ '<input type="submit" style="padding: 8px 15px;" value="Connect">'
													+ '</form>');
									break;

								default:
									console
											.log('Connector '
													+ connectorName
													+ ' isn\'t handled by the authentication form creator.');
								}
							});
		};

		var installedConnectors = null;
		var uninstalledConnectors = null;
		// Start both AJAX requests at the same time for speed. Whichever gets
		// data back last handles it properly.
		jQuery.ajax("/api/xtream/api/connectors/installed")
				.done(
						function(connectors) {
							installedConnectors = cleanArray(connectors);
							if (uninstalledConnectors !== null) {
								handleFinish(installedConnectors,
										uninstalledConnectors);
							}
						});
		jQuery.ajax("/api/xtream/api/connectors/uninstalled")
				.done(
						function(connectors) {
							uninstalledConnectors = cleanArray(connectors);
							if (installedConnectors !== null) {
								handleFinish(installedConnectors,
										uninstalledConnectors);
							}
						});

	};

	var initLoginDialog = function() {
		if (!isLoggedIn) {
			jQuery(document).on(
					'lwa_login',
					function(event, data, form) {
						if (data.result === true) {
							Quantimodo.getMeasurementsRange([],
									function(range) {
										dateRangeStart = new Date(0);
										dateRangeStart.setUTCSeconds(range[0]);
										jQuery('#datepicker-start').datepicker(
												'setDate', dateRangeStart);
										dateRangeEnd = new Date(0);
										dateRangeEnd.setUTCSeconds(range[1]);
										jQuery('#datepicker-end').datepicker(
												'setDate', dateRangeEnd);

										refreshVariables();
									});
							refreshUnits();

							jQuery("#login-dialog-background").addClass(
									'transitions').css({
								'opacity' : 0
							});
							jQuery("#login-dialog").addClass('transitions')
									.css({
										'opacity' : 0
									});

							setTimeout(function() {
								jQuery("#login-dialog-background").css({
									'display' : 'none'
								});
								jQuery("#login-dialog").css({
									'display' : 'none'
								});
							}, 500);
						}
					});
		}
	};

	// Requires div with .loading-overlay in element
	var showLoadingOverlay = function(element) {
		jQuery(element + " .loading-overlay").css('display', 'table-cell');
		jQuery(element + " .loading-overlay").css('opacity', 0.5);
	};

	var hideLoadingOverlay = function(element) {
		jQuery(element + " .loading-overlay").css('opacity', 0);
		setTimeout(function() {
			jQuery(element + " .loading-overlay").css('display', 'none');
		}, 500);
	};

	var retrieveSettings = function() {
		if (typeof Storage !== "undefined") {
			// dateSelectorVisible = (localStorage["dateSelectorVisible"] ||
			// "true") == "true";
		}
	};

	var saveSetting = function(setting, value) {
		if (typeof Storage !== "undefined") {
			localStorage[setting] = value;
		}
	};

	var formatDate = function(date, includeTime, UTC) {
		if (includeTime == null)
			includeTime = false;
		if (UTC == null)
			UTC = false;
		if (typeof (date) == "number") {
			if (!UTC)
				date = new Date(date);
			else {
				var ms = date;
				date = new Date(0);
				date.setUTCMilliseconds(ms);
			}
		}else{
			date = new Date(date);
		}
		if (isNaN(date.getFullYear()))
			return "Present";
		var value = "";
		var year, month, day, hour, minute, second;
		if (UTC) {
			year = date.getUTCFullYear();
			month = date.getUTCMonth();
			day = date.getUTCDate();
			hour = date.getUTCHours();
			minute = date.getUTCMinutes();
			second = date.getUTCSeconds();
		} else {
			year = date.getFullYear();
			month = date.getMonth();
			day = date.getDate();
			hour = date.getHours();
			minute = date.getMinutes();
			second = date.getSeconds();

		}

		switch (month) {
		case 0:
			value += "January";
			break;
		case 1:
			value += "February";
			break;
		case 2:
			value += "March";
			break;
		case 3:
			value += "April";
			break;
		case 4:
			value += "May";
			break;
		case 5:
			value += "June";
			break;
		case 6:
			value += "July";
			break;
		case 7:
			value += "August";
			break;
		case 8:
			value += "September";
			break;
		case 9:
			value += "October";
			break;
		case 10:
			value += "November";
			break;
		case 11:
			value += "December";
			break;
		}
		value += " " + day;
		value += ", " + year;
		if (includeTime) {
			value += " " + hour;
			value += ":";
			if (minute < 10)
				value += "0";
			value += minute;
			value += ":";
			if (second < 10)
				value += "0";
			value += second;
		}
		return value;
	}

	var toggleElement = function(element) {
		var content = jQuery(element);
		content.inner = jQuery(element + ' .inner');

		content
				.on(
						'transitionEnd webkitTransitionEnd transitionend oTransitionEnd msTransitionEnd',
						function(e) {
							if (content.hasClass('open')) {
								content.css('max-height', 9999);
							}
						});

		content.toggleClass('open closed');
		content.contentHeight = content.outerHeight();

		if (content.hasClass('closed')) {
			content.removeClass('transitions').css('max-height',
					content.contentHeight);
			setTimeout(function() {
				content.addClass('transitions').css({
					'max-height' : 0
				});
			}, 10);
		} else if (content.hasClass('open')) {
			content.contentHeight += content.inner.outerHeight();
			content.addClass('transitions').css({
				'max-height' : content.contentHeight
			});
		}
	};

	return {
		init : function() {
			retrieveSettings();
			reloadConnectorData();
			initLoginDialog();
		},
	};
}();
jQuery(ManageAccountsPage.init);
