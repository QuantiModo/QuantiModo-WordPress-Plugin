ManageAccountsPage = function () {
    var api = new Quantimodo.connectorsInterface();

    var reloadConnectorData = function () {
        api.listConnectors(function (connectors) {
            for (var i in connectors) {
                connectors[i].lastUpdate = new Date(connectors[i].lastUpdate * 1000).toUTCString();
                connectors[i].showGetItButton = (connectors[i].getItUrl != '');
            }
            renderConnectors(connectors);
        });
    };

    var renderConnectors = function (connectors) {
        // Render the connector template
        jQuery('#connectorInfoTable').html(
            Mustache.render(
                jQuery("#connectorsTemplate").html(),
                {"connectors": connectors}
            )
        );

        jQuery('.connectorDialog').each(function () {
            var connectorName = jQuery(this).attr('id').replace(/^showDialog-/, ''),
                curDialog = jQuery(this);

            curDialog.dialog({
                dialogClass: 'wp-dialog',
                close: function () {
                    jQuery('#connectNotificationContainer-' + connectorName).css({height: '0px'});
                },
                modal: true,
                autoOpen: false,
                maxWidth: 600,
                width: 600,
                closeOnEscape: true
            });
            curDialog.dialog('option', 'dialogClass', 'noTitleStuff');
            curDialog.dialog('option', 'title', connectorName);
        });

        jQuery('.disconnect-button').on('click', function () {
            var el = jQuery(this),
                id = el.attr('id'),
                connectorName = id.split('-')[1],
                buttonType = id.split('-')[0];

            if (buttonType == 'update') {
                el.html('Syncing');
            } else if (buttonType == 'disconnect') {
                el.html('Disconnecting');
            }

            ManageAccountsPage.api.connector(connectorName).do(buttonType, function () {
                location.reload();
            });
        });

        jQuery('.view-updates-button').on('click', function (e) {
            e.preventDefault();

            var el = jQuery(this),
                id = el.attr('id'),
                connectorName = id.split('-')[1];

            ManageAccountsPage.api.connector(connectorName).do('info', function (data) {
                showUpdates(data.history);
            });
        });

        jQuery('.connectorBlock').on('click', function () {
            var el = jQuery(this),
                id = el.attr('id'),
                connectorName = id.replace(/^connector-/, '');

            jQuery("#showDialog-" + connectorName).dialog("open");
        });

        var numConnectors = connectors.length;
        for (var i = 0; i < numConnectors; i++) {
            setOnConnectResponseHandler(connectors[i]);
        }
    };

    var setOnConnectResponseHandler = function (connector) {
        var element = jQuery('#connectNotificationContainer-' + connector.name),
            beforeSubmit = function (arr) {
                var params = {};
                for (var i in arr) {
                    params[arr[i].name] = arr[i].value;
                }

                if (connector.connectInstructions.usePopup && isExternal(connector.connectInstructions.url)) {
                    authPopup(connector.connectInstructions.url, function (r) {
                        processPopupResponse(element, r);
                    });
                }
                else {
                    if (connector.connectInstructions.usePopup) {
                        // open popup before ajax call, to pass the popup blocker
                        var blankPopup = authPopup('about:blank', function (r) {
                            processPopupResponse(element, r);
                        });
                    }

                    ManageAccountsPage.api.connector(connector.name).do('connect', params, function (response) {
                        if (response.error != null) {
                            setMessage(element, 'Negative', response.error.errorMessage);
                        }
                        else if (response.redirect != null) {
                            blankPopup.location.replace(response.redirect.location);
                        }
                        else {
                            setMessage(element, 'Positive', 'Connected!');
                            location.reload();
                        }
                    });
                }

                element.css({height: '0'});
                return false;
            };

        jQuery('#connectform-' + connector.name).on('submit', function () {
            var arr = jQuery(this).serializeArray();
            beforeSubmit(arr);
            return false;
        });

        function processPopupResponse(element, response) {
            if (typeof response.error != 'undefined') {
                setMessage(element, 'Negative', response.error);
            } else {
                setMessage(element, 'Positive', 'Connected!');
                location.reload();
            }
        }

        function setMessage(el, type, message) {
            el.removeClass('connectNotificationNegative connectNotificationPositive').addClass('connectNotification' + type);
            element.text(message);
            element.css({height: '30px'});
        }
    }

    var initLoginDialog = function () {
        jQuery(document).on('lwa_login', function (event, data, form) {
            if (data.result === true) {
                ManageAccountsPage.reloadConnectorData();

                jQuery("#login-dialog-background").addClass('transitions')
                    .css({'opacity': 0});
                jQuery("#login-dialog").addClass('transitions')
                    .css({
                        'opacity': 0
                    });

                setTimeout(function () {
                    jQuery("#login-dialog-background").css({
                        'display': 'none'
                    });
                    jQuery("#login-dialog").css({
                        'display': 'none'
                    });
                }, 500);
            }
        });
    };

    return {
        reloadConnectorData: reloadConnectorData,
        api: api,
        init: function () {
            initLoginDialog();
            if (access_token) {
                reloadConnectorData();
            } else {
                window.location.href = "?connect=quantimodo";
            }
        },
    };
}();

jQuery(ManageAccountsPage.init);

jQuery(window).resize(function () {
    jQuery('.connectorDialog').each(function () {
        jQuery(this).dialog('option', 'position', 'center');
    });
});

function showUpdates(data) {
    var table = jQuery('.updates-table').clone(),
        tbody = jQuery('<tbody>');
    for (var i in data) {
        var tr = jQuery('<tr>');
        for (var j in data[i]) {
            var td = jQuery('<td>'),
                value = data[i][j];
            switch (j) {
                case 'error':
                    var statusImg = jQuery('<img>');
                    if (value == null) {
                        statusImg.attr({
                            src: 'https://i.imgur.com/Rvv8Ujo.png',
                            title: 'Success'
                        });
                    } else {
                        statusImg.attr({
                            src: 'https://i.imgur.com/tvNH2wA.png',
                            title: value
                        });
                    }
                    value = statusImg;
                    break;
                case 'timestamp':
                    value = new Date(value * 1000).toUTCString();
                    break;
            }
            td.html(value);
            tr.append(td);
        }
        tbody.append(tr);
    }
    table.append(tbody);
    jQuery.fancybox(table);
}

function authPopup(url, callback) {
    var wnd;

    // create popup
    var wnd_settings = {
        width: Math.floor(window.outerWidth * 0.8),
        height: Math.floor(window.outerHeight * 0.5)
    };
    if (wnd_settings.height < 350)
        wnd_settings.height = 350;
    if (wnd_settings.width < 800)
        wnd_settings.width = 800;
    wnd_settings.left = window.screenX + (window.outerWidth - wnd_settings.width) / 2;
    wnd_settings.top = window.screenY + (window.outerHeight - wnd_settings.height) / 8;
    var wnd_options = "width=" + wnd_settings.width + ",height=" + wnd_settings.height;
    wnd_options += ",toolbar=0,scrollbars=1,status=1,resizable=1,location=1,menuBar=0";
    wnd_options += ",left=" + wnd_settings.left + ",top=" + wnd_settings.top;

    var titleCheckTimer,
        sendCallback = function (response) {
            if (titleCheckTimer) clearInterval(titleCheckTimer);
            callback(response);
        };

    if (titleCheckTimer) clearInterval(titleCheckTimer);

    titleCheckTimer = setInterval(function () {
        if (!wnd) return;
        try {
            var response = jQuery.parseJSON(wnd.document.documentElement.innerText);
            if (typeof response.success != "undefined") {
                wnd.close();
                sendCallback(response);
            }
        }
        catch (e) {
        }
    }, 0.3 * 1000);

    setTimeout(function () {
        sendCallback({error: 'Authorization timed out.'});
    }, 600 * 1000);

    wnd = window.open(url, "Authorization", wnd_options);
    if (wnd) wnd.focus();
    return wnd;
}

function isExternal(url) {
    var match = url.match(/^([^:\/?#]+:)?(?:\/\/([^\/?#]*))?([^?#]+)?(\?[^#]*)?(#.*)?/);
    if (typeof match[1] === "string" && match[1].length > 0 && match[1].toLowerCase() !== location.protocol) return true;
    if (typeof match[2] === "string" && match[2].length > 0 && match[2].replace(new RegExp(":(" + {
                "http:": 80,
                "https:": 443
            }[location.protocol] + ")?$"), "") !== location.host) return true;
    return false;
}