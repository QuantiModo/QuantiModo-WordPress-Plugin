(function connectjs() {
    "use strict";
    window.QuantiModoIntegration = {options: {}};
    function getDefaultOptions() {
        return {
            clientUserId: encodeURIComponent('UNIQUE_ID_FOR_YOUR_USER'),
            clientUser: null,
            baseIonicPath: getApiOrigin()+'/app/public/#/app/',
            clientId: 'CLIENT_ID',
            logout: false,
            publicToken: '',
            qmAccessToken: null,
            fullscreen: true,
            showButton: false,
            defaultState: 'import',
            hideMenu: true,
            sideBarWidth: "600px",
            floatingActionButtonRightOffset: "15px",
            clientServerFinishUrl: "https://yourserver.com/api/v1/quantimodo/finish",
            finish: function(sessionTokenObject) {
                logError('You have not defined window.QuantiModoIntegration.options.finish!');
                console.warn("window.QuantiModoIntegration.options.finish is called after user finishes connecting their health data.");
                console.warn("You should set this to POST sessionTokenObject as-is to your server for step 2");
                console.warn("Also, include code here to refresh the page.");
                var req = new XMLHttpRequest();   // new HttpRequest instance

                req.open("POST", getApiOrigin()+"/api/v1/quantimodo/connect/finish");
                req.setRequestHeader("Content-Type", "application/json");
                req.send(sessionTokenObject);
            },
            close: function() {
                /* (optional) Called when a user closes the popup without connecting any data sources */
            },
            error: function(err) {
                console.error(err);
                /* (optional) Called if an error occurs when loading the popup. */
            }
        }
    }
    var qmMain;
    var connectorList;
    var qmPageElements = {
        qmPopup: {},
        qmPopupInner: {},
        singleFloatingActionButton: {},
        qmIonicAppSidebar: {},
        iFramePopup: {},
        connectorListPopup: {},
        connectorBlock: {},
        tripleFloatingActionButtons: {
            right: 200
        }
    };
    for (var key in qmPageElements) {if (qmPageElements.hasOwnProperty(key)) {qmPageElements[key].id = camelCaseToDash(key);}}
    var callbackInterval = null;
    var callbackCancelTimeout = null;
    var useConnectionWindow = true;
    var methodsDelegated = false;
    function getOption(optionName) {
        var optionValue;
        if(window.QuantiModoIntegration.options[optionName]){
            optionValue = window.QuantiModoIntegration.options[optionName];
        } else {
            optionValue = getDefaultOptions()[optionName];
        }
        return optionValue;
    }
    function logError(errorMessage){
        console.error(errorMessage);
        if(window.QuantiModoIntegration.error){window.QuantiModoIntegration.error(errorMessage);}
        if(getDefaultOptions().error){getDefaultOptions().error(errorMessage);}
    }
    function dashesToCamelCase(myString) {return myString.replace(/-([a-z])/g, function (g) { return g[1].toUpperCase(); });}
    function camelCaseToDash( myStr ) {return myStr.replace( /([a-z])([A-Z])/g, '$1-$2' ).toLowerCase();}
    function getClientId () {
        var clientId;
        if(typeof options !== "undefined" && options.clientId){clientId = options.clientId;}
        if (!clientId) { clientId = window.QuantiModoIntegration.options.clientId }
        if (!clientId) { clientId = 'quantimodo' }
        console.log('clientId is ' + clientId);
        return clientId
    }
    function getApiHost() {
        var apiUrl = getOption('apiUrl');
        if (!apiUrl) {apiUrl = 'app.quantimo.do'}
        if (window.location.href.indexOf('staging.quantimo.do') !== -1) {apiUrl = 'staging.quantimo.do'}
        if (window.location.href.indexOf('qm-staging.quantimo.do') !== -1) {apiUrl = 'staging.quantimo.do'}
        if (window.location.href.indexOf('local.quantimo.do') !== -1) {apiUrl = 'local.quantimo.do'}
        console.log('apiUrl is ' + apiUrl);
        return apiUrl
    }
    function getApiOrigin() {
        return 'https://' + getApiHost()
    }
    function getAppSettings (successHandler) {
        var appSettings = localStorage.getItem('appSettings');
        if(appSettings){appSettings = JSON.parse(appSettings);}
        if(appSettings && appSettings.clientId === getClientId()){
            console.info("Got appSettings from local storage");
            successHandler(appSettings);
            return;
        }
        function getAppSettingsUrl () {return 'https://' + getApiHost() + '/api/v1/appSettings?clientId=' + getClientId()}
        var xhr = new XMLHttpRequest();
        xhr.open('GET', getAppSettingsUrl(), true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                var response = JSON.parse(xhr.responseText)
                window.QuantiModoIntegration.appSettings = response.appSettings
                if (window.QuantiModoIntegration.appSettings && typeof window.QuantiModoIntegration.appSettings !== 'undefined') {
                    console.log('got appSettings', window.QuantiModoIntegration.appSettings)
                    successHandler()
                } else {
                    logError('Could not get your app settings!');
                    window.alert('Could not get your app settings!')
                }
            }
        };
        xhr.send()
    }
    // http://ejohn.org/blog/javascript-micro-templating/
    function template(str, data) {
        function timeDiff(date, suffix) {
            function numberEnding(number) { return (number > 1) ? 's' : ''; }
            var diff = (new Date().getTime() - date.getTime()) / 1000;
            var years = Math.floor(diff / 31536000);
            if (years) { return years + ' year' + numberEnding(years) + suffix; }
            var days = Math.floor((diff %= 31536000) / 86400);
            if (days) { return days + ' day' + numberEnding(days) + suffix; }
            var hours = Math.floor((diff %= 86400) / 3600);
            if (hours) { return hours + ' hour' + numberEnding(hours) + suffix; }
            var minutes = Math.floor((diff %= 3600) / 60);
            if (minutes) { return minutes + ' minute' + numberEnding(minutes) + suffix; }
            var seconds = Math.floor(diff % 60);
            if (seconds) { return seconds + ' second' + numberEnding(seconds) + suffix; }
            return 'just now';
        }
        data.timeDiff = timeDiff;
        var fn = new Function("obj", "var p=[],print=function(){p.push.apply(p,arguments);};" + "with(obj){p.push('" + str .replace(/[\r\t\n]/g, " ") .split("<%").join("\t") .replace(/((^|%>)[^\t]*)'/g, "$1\r") .replace(/\t=(.*?)%>/g, "',$1,'") .split("\t").join("');") .split("%>").join("p.push('") .split("\r").join("\\'") + "');}return p.join('');");
        return fn(data);
    }
    function createNewDiv(qmPageElement) {
        console.log("QMIntegration creating: ", qmPageElement);
        if (document.getElementById(qmPageElement.id)) {return;}
        var newDiv = document.createElement('div');
        newDiv.innerHTML = qmPageElement.template;
        document.body.appendChild(newDiv);
        qmPageElement.element = document.getElementById(qmPageElement.id);
        if(qmPageElement.onClickListener){qmPageElement.element.addEventListener('click', qmPageElement.onClickListener);}
    }
    function showElement(element, delayInMilliseconds) {
        if(delayInMilliseconds){
            setTimeout(function(){element.style.display = 'block';}, delayInMilliseconds);
        } else{
            element.style.display = 'block';
        }
    }
    function applyCssStyles(cssStyles) {
        var head = document.head || document.getElementsByTagName('head')[0];
        var style = document.createElement('style');
        style.type = 'text/css';
        if (style.styleSheet) { style.styleSheet.cssText = cssStyles; } else { style.appendChild(document.createTextNode(cssStyles)); }
        head.appendChild(style);
    }
    function getAPIQueryString() {
        var queryString;
        var moreInfo = '  See https://builder.quantimo.do for more information.';
        if(getClientId() && getClientId() !== ''){
            queryString = '?clientId=' + getClientId();
        } else {
            throw ('Please set window.QuantiModoIntegration.options.clientId! ' + moreInfo);
        }
        var qmAccessToken = window.QuantiModoIntegration.options.qmAccessToken;
        if(qmAccessToken && qmAccessToken !== ''){
            queryString += '&accessToken=' + qmAccessToken;
        } else if(window.QuantiModoIntegration.options.publicToken && window.QuantiModoIntegration.options.publicToken !== ''){
            queryString += '&publicToken=' + window.QuantiModoIntegration.options.publicToken;
        } else {
            console.warn('No window.QuantiModoIntegration.options.publicToken or window.QuantiModoIntegration.options.qmAccessToken provided so assuming this a new user. ' + moreInfo);
        }
        if(window.QuantiModoIntegration.options.clientUserId && window.QuantiModoIntegration.options.clientUserId !== ''){
            queryString += '&clientUserId=' + window.QuantiModoIntegration.options.clientUserId;
        } else {
            console.warn('Please set window.QuantiModoIntegration.options.clientUserId! ' + moreInfo);
        }
        return queryString;
    }
    function postJsonToAPI(jsonObject, url, successHandler, errorHandler) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", url, true);
        xhr.setRequestHeader("Content-type", "application/json");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status >= 200 && xhr.status < 400) {
                console.log(url + " response: " + xhr.responseText);
                if(xhr.responseText === ""){
                    logError("No xhr.responseText from POST to "+url);
                    errorHandler("No xhr.responseText from POST to "+url);
                    return;
                }
                if(successHandler){successHandler(xhr.responseText);}
            } else {
                console.error(xhr.responseText);
                if(errorHandler){errorHandler(url + " response: " + xhr.responseText);}
            }
        };
        xhr.onerror = logError;
        if(typeof jsonObject !== "string"){
            jsonObject = JSON.stringify(jsonObject);
        }
        xhr.send(jsonObject);
    }
    function connectorsApiCall(method, requestPath, success, errorHandler) {
        if (!errorHandler) { errorHandler = function (err) { logError(err); }; }
        var request = new XMLHttpRequest();
        request.open(method, 'https://' + getApiHost() + requestPath + getAPIQueryString(), true);
        request.onload = function () {
            if (request.status >= 200 && request.status < 400) {
                var data = JSON.parse(request.responseText);
                success(data);
                if(window.QuantiModoIntegration.options.finish && data.sessionTokenObject){
                    window.QuantiModoIntegration.options.finish(data.sessionTokenObject);
                }
            } else {
                logError(request.responseText);
            }
        };
        request.onerror = errorHandler;
        request.send();
    }
    function showPopup() {
        if(window.QuantiModoIntegration.options.fullscreen){
            qmPageElements.qmPopup.style.width = '100%';
            qmPageElements.qmPopup.style.left = '0';
            qmPageElements.qmPopup.style.top = '0';
            qmPageElements.qmPopup.style.border = 'none';
        } else {
            var width = window.innerWidth * 0.8;
            if (width > 1100) { width = 1100; }
            var left = (window.innerWidth - width) / 2;
            qmPageElements.qmPopup.style.width = width + 'px';
            qmPageElements.qmPopup.style.left = left + 'px';
        }
        qmPageElements.qmPopup.style.display = 'block';
    }
    function loadConnectors(callback) {
        connectorsApiCall('GET', '/api/v3/connectors/list', function (data) {
            connectorList = {};
            data.connectors.forEach(function (connector) { connectorList[connector.name + connector.id] = connector; });
            callback();
        });
    }
    function renderConnectorList() {
        function matches(el, selector) {
            return (el.matches || el.matchesSelector || el.msMatchesSelector || el.mozMatchesSelector ||
                el.webkitMatchesSelector || el.oMatchesSelector) .call(el, selector);
        }
        // https://jsfiddle.net/1a6j4es1/28/
        function delegateSelector(selector, event, childSelector, handler) {
            var elements = document.querySelectorAll(selector);
            [].forEach.call(elements, function (el) {
                el.addEventListener(event, function (e) {
                    if (matches(e.target, childSelector)) {
                        handler(e);
                    }
                });
            });
        }
        function showAuthWindow(url) {
            var authWindow;
            var windowSize = { width: Math.floor(window.outerWidth * 0.8), height: Math.floor(window.outerHeight * 0.7) };
            if (windowSize.height < 500) { windowSize.height = Math.min(500, window.outerHeight); }
            if (windowSize.width < 800) { windowSize.width = Math.min(800, window.outerWidth); }
            windowSize.left = window.screenX + (window.outerWidth - windowSize.width) / 2;
            windowSize.top = window.screenY + (window.outerHeight - windowSize.height) / 8;
            var windowOptions = "width=" + windowSize.width + ",height=" + windowSize.height;
            windowOptions += ",toolbar=0,scrollbars=1,status=1,resizable=1,location=1,menuBar=0";
            windowOptions += ",left=" + windowSize.left + ",top=" + windowSize.top;
            authWindow = window.open(url, "Authorization", windowOptions);
            if (authWindow) { authWindow.focus(); }
            return authWindow;
        }
        function countObjectAttributes(o) {
            if (typeof o != "object") { return null; }
            var count = 0;
            for (var k in o) { if (o.hasOwnProperty(k)) { ++count; } }
            return count;
        }
        function waitForAccount(callback) {
            // clear old intervals
            clearIntervals();
            // set a new interval to check connection
            callbackInterval = setInterval(function () { loadConnectors(callback); }, 3000);
            // stop refreshing the API after 2 minutes
            callbackCancelTimeout = setTimeout(function () {
                clearIntervals();
                // TODO error message
            }, 120000);
        }
        function addClass(el, className) {
            if (el.classList) { el.classList.add(className); } else { el.className += ' ' + className; }
        }
        function closest(el, selector) {
            while (el) { if (matches(el, selector)) { return el; } else { el = el.parentElement; } }
            return false;
        }
        function addFieldsFromConnectButton(button, block, instructions) {
            for (var i in instructions.parameters) {
                if (instructions.parameters.hasOwnProperty(i)) {
                    var parameter = instructions.parameters[i];
                    var fields = block.querySelector('.qm-account-block-fields');
                    var span = document.createElement('span');
                    span.setAttribute('class', 'qm-account-block-field-text');
                    span.innerHTML = parameter['displayName'];
                    var input = document.createElement('input');
                    input.setAttribute('type', parameter['type']);
                    input.value = parameter['defaultValue'];
                    input.setAttribute('name', parameter['key']);
                    input.setAttribute('placeholder', parameter['placeholder']);
                    input.setAttribute('class', 'qm-account-block-field-input');
                    fields.appendChild(span);
                    fields.appendChild(input);
                    fields.appendChild(document.createElement('br'));
                    addClass(button, 'qm-account-connect-button-with-params');
                    input.addEventListener('keypress', function (event) {
                        if (event.keyCode === 13) {
                            var block = closest(event.target, '.qm-account-block');
                            var connectButton = block.getElementsByClassName('qm-account-connect-button')[0];
                            if (connectButton) {
                                connectButton.click();
                            }
                        }
                    });
                }
            }
        }
        function hasClass(el, className) {
            if (el.classList) { return el.classList.contains(className); } else { return new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className); }
        }
        function redirectFromConnectButton(button, block, targetUrl) {
            if (hasClass(button, 'qm-account-connect-button-with-params')) {
                var queryParams = [];
                var fields = block.querySelectorAll('.qm-account-block-field-input');
                for (var i in fields) {
                    if (fields.hasOwnProperty(i)) {
                        var field = fields[i];
                        queryParams.push(encodeURIComponent(field.name) + '=' + encodeURIComponent(field.value));
                    }
                }
                targetUrl += "?" + queryParams.join('&');
                console.debug('targetUrl is ', targetUrl);
            }
            var ref;
            if(typeof ionic !== "undefined" && ionic){
                if(ionic.Platform.platforms[0] === "browser"){
                    // browser
                    ref = window.open(targetUrl,'', "width=600,height=800");
                    var pollTimer = window.setInterval(function() {
                        if (ref.closed !== false) { // !== is required for compatibility with Opera
                            window.clearInterval(pollTimer);
                            showLoader(false);
                            window.QuantiModoIntegration.qmSetupIonicPopupIframe();
                        }
                    }, 200);
                } else {
                    // mobile
                    ref = window.open(targetUrl,'_blank', 'location=no,toolbar=yes');
                    ref.addEventListener('exit', function(){
                        showLoader(false);
                        window.QuantiModoIntegration.qmSetupIonicPopupIframe();
                    });
                }
            } else {
                window.location = targetUrl;
            }
        }
        var connectorBlock = {
            template: '<div class="qm-account-block" data-name="<%= name %><%= id %>">' +
            '    <div class="qm-account-block-left">' +
            '        <img class="qm-connect-image" src="<%= image %>" alt="<%= displayName %> logo">' +
            '    </div>' +
            '    <div class="qm-account-block-right">' +
            '        <div class="qm-first-two">' +
            '           <h4 class="qm-account-name"><%= displayName %></h4>' +
            '           <% if (!qmClient && connected && errorMessage) { %>' +
            '               <img class="qm-sync-image" src="https://static.quantimo.do/public/img/sync-btn-red.png"' +
                ' alt="qm-sync-image">' +
            '           <% } else if (!qmClient && connected) { %>' +
            '               <img class="qm-sync-image" src="https://static.quantimo.do/public/img/sync-btn.png">' +
            '           <% } %>' +
            '           <% if (!qmClient && errorMessage) { %><small class="qm-error"><%= errorMessage %></small><% } %>' +
            '           <div class="clear"></div>' +
            '           <% if (!qmClient && connected && lastUpdate) { %>' +
            '               <div class="qm-account-last-updated">Last updated <%= timeDiff(new Date(lastUpdate * 1000), " ago") %>: <%= totalMeasurementsInLastUpdate %> new measurements</div>' +
            '           <% } %>' +
            '        </div>' +
            '        <span><%= shortDescription %></span>' +
            '    <div class="qm-account-block-fields"></div>' +
            '    <div class="qm-button-container">' +
            '        <% if (qmClient) { %>' +
            '            <a class="qm-button qm-account-get-button" target="_blank" href="<%= getItUrl %>">GET IT HERE</a>' +
            '        <% } else if (connected && updateStatus == "WAITING") { %>' +
            '           <a class="qm-button qm-account-scheduled-button hovered" href="#">Update Scheduled</a>' +
            '           <a class="qm-button qm-account-disconnect-button pull-right" href="#">Disconnect</a>' +
            '        <% } else if (connected && updateStatus == "UPDATING") { %>' +
            '           <a class="qm-button qm-account-sync-button" href="#">Updating</a>' +
            '           <a class="qm-button qm-account-disconnect-button pull-right" href="#">Disconnect</a>' +
            '        <% } else if (connected) { %>' +
            '           <a class="qm-button qm-account-sync-button" href="#">Sync</a>' +
            '           <a class="qm-button qm-account-disconnect-button pull-right" href="#">Disconnect</a>' +
            '        <% } else { %>' +
            '            <a class="qm-button qm-account-get-button" target="_blank" href="<%= getItUrl %>">GET IT HERE</a>' +
            '            <a class="qm-button qm-account-connect-button pull-right" href="#">Connect</a>' +
            '        <% } %>' +
            '    </div>' +
            '    </div>' +
            '<div class="clear"></div>' +
            '</div>'
        };
        var renderedBlock;
        qmMain.innerHTML = '';
        for (var key in connectorList) {
            if (connectorList.hasOwnProperty(key)) {
                renderedBlock = template(connectorBlock.template, connectorList[key]);
                qmMain.innerHTML += renderedBlock;
            }
        }
        if (methodsDelegated) { return; }
        delegateSelector('#qm-main', 'click', '.qm-account-connect-button', function (event) {
            event.preventDefault();
            var block = closest(event.target, '.qm-account-block');
            var name = block.getAttribute('data-name');
            var instructions = connectorList[name].connectInstructions;
            console.debug('instructions are ', instructions);
            var hasParameters = countObjectAttributes(instructions.parameters);
            if (useConnectionWindow && !hasParameters) {
                showLoader(true);
                showAuthWindow(instructions.url);
                waitForAccount(function () {
                    if (connectorList[name] && connectorList[name].connected) {
                        showLoader(false);
                        renderConnectorList();
                        clearIntervals();
                    }
                });
            } else {
                if (hasParameters && !hasClass(event.target, 'qm-account-connect-button-with-params')) {
                    addFieldsFromConnectButton(event.target, block, instructions);
                } else {
                    showLoader(true);
                    redirectFromConnectButton(event.target, block, instructions.url);
                }
            }
        });
        delegateSelector('#qm-main', 'click', '.qm-account-disconnect-button', function (event) {
            event.preventDefault();
            showLoader(true);
            var block = closest(event.target, '.qm-account-block');
            var name = block.getAttribute('data-name');
            connectorsApiCall('GET', '/api/v1/connectors/' + name + '/disconnect', function () {
                loadConnectors(function () {
                    if (connectorList[name] && !connectorList[name].connected) {
                        showLoader(false);
                        renderConnectorList();
                        clearIntervals();
                    }
                });
            });
        });
        delegateSelector('#qm-main', 'click', '.qm-account-sync-button', function (event) {
            event.preventDefault();
            var block = closest(event.target, '.qm-account-block');
            var name = block.getAttribute('data-name');
            (function (target) {
                target.innerHTML = 'Scheduling...';
                connectorsApiCall('GET', '/api/v1/connectors/' + name + '/update', function () {
                    loadConnectors(function () {
                        if (connectorList[name] && connectorList[name].connected) {
                            target.innerHTML = 'Update Scheduled';
                            renderConnectorList();
                            clearIntervals();
                        }
                    });
                });
            })(event.target);
        });
        delegateSelector('#qm-main', 'click', '.qm-account-scheduled-button', function (event) {
            event.preventDefault();
            return false;
        });
        methodsDelegated = true;
    }
    function showLoader(show) {
        var qmLoaderWrapper = document.getElementById('qm-loader-wrapper');
        if (!qmLoaderWrapper) {
            var loaderWrapper = document.createElement('div');
            loaderWrapper.style.display = 'flex';
            loaderWrapper.style['align-items'] = 'center';
            loaderWrapper.style['justify-content'] = 'center';
            loaderWrapper.style.position = 'absolute';
            loaderWrapper.style.top = '0';
            loaderWrapper.style.left = '0';
            loaderWrapper.style.height = '100%';
            loaderWrapper.style.width = '100%';
            //loaderWrapper.style['background-color'] = 'rgba(255, 255, 255, 0.75)';
            loaderWrapper.style['z-index'] = '30';
            loaderWrapper.setAttribute('id', 'qm-loader-wrapper');
            var img = document.createElement('img');
            img.src = 'https://static.quantimo.do/qm-connect/loader_gears.gif';
            img.setAttribute('id', 'qm-loader');
            img.style['z-index'] = '31';
            loaderWrapper.appendChild(img);
            document.body.appendChild(loaderWrapper);
            qmLoaderWrapper = document.getElementById('qm-loader-wrapper');
        }
        if (show) {
            qmLoaderWrapper.style.display = 'flex';
        } else {
            qmLoaderWrapper.style.display = 'none';
        }
    }
    function clearIntervals() {
        clearInterval(callbackInterval);
        callbackInterval = null;
        clearTimeout(callbackCancelTimeout);
    }

    function getIonicUrl(statePath) {
        return getDefaultOptions().baseIonicPath + statePath + window.QuantiModoIntegration.getIframeQueryString();
    }

    window.QuantiModoIntegration.qmSetupIonicPopupIframe = function (state) {
        function createQmShowHideButtonBlock() {
            createNewDiv(qmPageElements.singleFloatingActionButton);
            showElement(qmPageElements.singleFloatingActionButton.element, 5000);
        }
        function createHiddenIframePopupBlock() {
            if (document.getElementById('qm-main')) { return; }
            var iFramePopupTemplate =
                '<div id="qm-popup">' +
                '    <div id="qm-popup-inner">' +
                '        <img id="qm-close" alt="x" src="https://static.quantimo.do/qm-connect/close.png">' +
                '        <div id="qm-main"></div>' +
                '    </div>' +
                '</div>';
            var newDiv = document.createElement('div');
            newDiv.innerHTML = template(iFramePopupTemplate, {});
            document.body.appendChild(newDiv.children[0]);
            qmPageElements.qmPopup = document.getElementById('qm-popup');
            qmPageElements.qmPopupInner = document.getElementById('qm-popup-inner');
            qmPageElements.singleFloatingActionButton = document.getElementById('qm-close');
            qmPageElements.singleFloatingActionButton.addEventListener('click', function () {
                qmPageElements.qmPopup.style.display = 'none';
            });
            qmMain = document.getElementById('qm-main');
        }
        function createIframePopupStyles() {
            var iframeCss =
                '#show-ionic-app { display: none; }' +
                '#qm-ionic-app-show-hide-button { height: 60px; width: 60px; position: fixed; bottom: 15px; right: 80px; cursor: pointer; }' +
                '#ionic-app-frame { z-index: 999999; }' +
                ';';
            var head = document.head || document.getElementsByTagName('head')[0];
            var style = document.createElement('style');
            style.type = 'text/css';
            if (style.styleSheet) { style.styleSheet.cssText = iframeCss; } else { style.appendChild(document.createTextNode(iframeCss)); }
            head.appendChild(style);
        }
        window.onload = function() {
            createQmShowHideButtonBlock();
            createHiddenIframePopupBlock();
            showLoader(true);
            createIframePopupStyles();
            var statePath = '';
            if(state){statePath = '/app/' + state;}
            var url = getIonicUrl(statePath)
            qmMain.innerHTML = '<iframe style="height:100%;width:100%;" id="ionic-app-frame" src="' + url + '" frameborder="0"></iframe>';
            showPopup();
            showLoader(false);
        };
    };
    window.QuantiModoIntegration.openConnectorsListPopup = function () {
        function createHiddenConnectorListPopupBlock() {
            window.onload = function() {
                if (document.getElementById('qm-main')) {
                    return;
                }
                var connectorListPopupTemplate =
                    '<div id="qm-popup">' +
                    '    <div id="qm-popup-inner">' +
                    '        <img id="qm-close" alt="x" src="https://static.quantimo.do/qm-connect/close.png">' +
                    '        <div id="qm-main"></div>' +
                    '    </div>' +
                    '</div>';
                var temp = document.createElement('div');
                temp.innerHTML = template(connectorListPopupTemplate, {});
                document.body.appendChild(temp.children[0]);
                qmPageElements.qmPopup = document.getElementById('qm-popup');
                qmPageElements.qmPopupInner = document.getElementById('qm-popup-inner');
                qmPageElements.singleFloatingActionButton = document.getElementById('qm-close');
                qmPageElements.singleFloatingActionButton.addEventListener('click', function () {
                    qmPageElements.qmPopup.style.display = 'none';
                });
                qmMain = document.getElementById('qm-main');
            }
        }
        var connectorListCssStyles =
            'body {'+ 'margin: 0;'+ '}'+
            '#qm-popup {'+ 'display: none;'+ 'position:absolute;'+ 'left:50%;'+ 'top:15%;'+ '}'+
            '* {'+ "font-family: 'Open Sans', sans-serif, verdana"+ '}'+
            '.clear {'+ 'clear: both;'+ '}'+
            '@media (max-width: 640px) {'+
            '.qm-account-last-updated {'+ 'position: static;'+ '}'+
            '.qm-button-container {'+ 'padding:0 !important;'+ '}'+
            '.qm-button {'+ 'font-size: 14px !important;'+ '}'+
            '}'+
            '@media (max-width: 880px) {'+
            '.qm-button-container {'+ 'padding:0 !important;'+ '}'+
            '.qm-account-block-right {'+ 'width: 100% !important;'+ '}'+
            '}'+
            '.qm-connector-container {'+ 'background: #c7dde4;'+ 'padding: 35px 20px;'+ '}'+
            'div.qm-account-block {'+ 'background-color: #FFFFFF;'+ 'border-radius: 5px;'+ 'padding: 27px 40px 20px 40px;'+ 'margin-bottom: 35px;'+ '}'+
            '.qm-account-block-left {'+ 'float: left;'+ 'position: relative;'+ 'width: 132px;'+ 'margin-top:20px;'+ '}'+
            '.qm-account-block-right {'+ 'float: left;'+ 'position:relative;'+ 'width:84%;'+ '}'+
            '.qm-button-container {'+ 'padding: 0 10%;'+ '}'+
            '.qm-error {'+ 'line-height: 43px;'+ 'margin-left: 10px;'+ '}'+
            '.qm-account-block-right span {'+ 'font-size: 16px;'+ 'color: #888;'+ 'font-weight: 100;'+ '}'+
            '.pull-left {'+ 'float:left;'+ '}'+
            '.pull-right {'+ 'float: right;'+ '}'+
            '.qm-first-two {'+ 'margin-bottom: 13px;'+ '}'+
            'img.qm-connect-image {'+ 'width: 110px;'+ 'height: 110px;'+ '}'+
            'img.qm-connector-status {'+ 'position: absolute;'+ 'top: -10px;'+ 'right: 0px;'+ 'height: 50px;'+ 'width: 50px;'+ '}'+
            '.qm-account-name {'+ 'font-size: 32px;'+ 'font-family: sans-serif;'+ 'font-weight: bold;'+ 'margin: 0;'+ 'float: left;'+ '}'+
            '.qm-sync-image {'+ 'float: left;'+ 'margin-top: 7.5px;'+ 'margin-left: 15px;'+ '}'+
            '.qm-account-last-updated {'+ 'color: gray;'+ 'font-size: 0.8em;'+ '}'+
            'a.qm-button {'+ 'display: inline-block;'+ 'margin: 0px;'+ 'color: #36869c;'+ 'font-size: 21px;'+ 'font-weight: bolder;'+ 'text-decoration: none;'+ 'text-transform: uppercase;'+ 'border-radius: 5px;'+ 'padding:10px 18px;'+ 'margin-top: 5px;'+ 'text-align: center;'+ '}'+
            'a.qm-button:hover, a.qm-button.hovered {'+ 'background: #c6dce2;'+ '}'+
            '.qm-account-block-field-text {'+ 'display: inline-block;'+ 'width: 80px;'+ 'margin-right: 10px;'+ 'margin-top: 10px;'+ '}'+
            'div.clear {'+ 'clear: both;'+ '}'+
            '.qm-account-block:last-child hr {'+ 'display: none;'+ '}'+
            '@media (min-width: 640px){'+ '#qm-loader {'+ 'left:calc(50% - 250px);'+ 'top: calc(50% - 125px);'+ '}'+ '}'+
            '@media (max-width: 639px){'+ '#qm-loader {'+ 'width: 125px;'+ 'left:calc(50% - 60px);'+ 'top: calc(50% - 125px);'+ '}'+ '}';
        if(!window.QuantiModoIntegration.options.fullscreen) {
            connectorListCssStyles += '#qm-popup-inner {max-width:900px; min-width:600px; padding:10px 50px border:2px solid gray; border-radius:10px; position: relative;}';
            connectorListCssStyles += '#qm-close {position:absolute; right:-14px; top:-14px; cursor:pointer;}';
        } else{
            connectorListCssStyles += '#qm-popup-inner {padding: none; width: 100%;}';
            connectorListCssStyles += '#qm-close {position:absolute; right:40px; top:40px; cursor:pointer;}';
        }
        connectorListCssStyles += ';';
        createHiddenConnectorListPopupBlock();
        showLoader(true);
        loadConnectors(function () {
            applyCssStyles(connectorListCssStyles);
            renderConnectorList();
            showPopup();
            showLoader(false);
        });
    };
    window.QuantiModoIntegration.renderConnectorListAtProvidedSelector = function (connectOptions) {
        window.onload = function() {
            var selector = (connectOptions.selector) ? connectOptions.selector : connectOptions;
            // Handles older clients that only provide selector instead of the option object
            var parent = document.querySelector(selector);
            qmMain = document.createElement('div');
            qmMain.setAttribute('id', 'qm-main');
            qmMain.setAttribute('class', 'qm-connector-container');
            parent.appendChild(qmMain);
            showLoader(true);
            loadConnectors(function () {
                renderConnectorList();
                showLoader(false);
            });
        }
    };
    window.QuantiModoIntegration.renderConnectorListInIframe = function() {
        window.onload = function() {
            connectjs();
            //access_token = localStorage[config.appSettings.storage_identifier + 'accessToken'];
            useConnectionWindow = false;
            var importIframe = document.getElementById('import_iframe');
            importIframe.innerHTML = '<div id="qm-main"></div>';
            qmMain = document.getElementById('qm-main');
            showLoader(true);
            loadConnectors(function () {
                renderConnectorList();
                showLoader(false);
            });
        }
    };
    window.QuantiModoIntegration.renderConnectorPageForMobileEmbed = function () {
        window.onload = function() {
            useConnectionWindow = false;
            document.body.innerHTML = '<div id="qm-main"></div>';
            qmMain = document.getElementById('qm-main');
            showLoader(true);
            loadConnectors(function () {
                renderConnectorList();
                showLoader(false);
            });
        }
    };
    function toggleQmIonicAppSidebar() {
        function openAppSidebar() {
            qmPageElements.qmIonicAppSidebar.element.style.display = 'block';
            qmPageElements.singleFloatingActionButton.element.setAttribute('style', qmPageElements.singleFloatingActionButton.css.open);
        }
        function closeAppSidebar() {
            qmPageElements.qmIonicAppSidebar.element.style.display = 'none';
            qmPageElements.singleFloatingActionButton.element.setAttribute('style', qmPageElements.singleFloatingActionButton.css.closed);
        }
        console.debug('Clicked QM button');
        if(qmPageElements.qmIonicAppSidebar.element.style.display === 'none'){openAppSidebar();} else {closeAppSidebar();}
    }
    window.QuantiModoIntegration.getIframeQueryString = function(){
        var str = '?apiUrl=' + getApiHost();
        if(window.QuantiModoIntegration.options.qmAccessToken){str += '&accessToken=' + window.QuantiModoIntegration.options.qmAccessToken;}
        if(window.QuantiModoIntegration.options.hideMenu){str += '&hideMenu=' + window.QuantiModoIntegration.options.hideMenu;}
        if(window.QuantiModoIntegration.options.logout){str += '&logout=' + window.QuantiModoIntegration.options.logout;}
        if(window.QuantiModoIntegration.options.clientId){str += '&clientId=' + window.QuantiModoIntegration.options.clientId;}
        return str;
    };
    window.QuantiModoIntegration.createSingleFloatingActionButton = function() {
        getAppSettings(function() {
            var sharedButtonCss = "position:fixed; z-index:999998; height:60px; width:60px; cursor:pointer;";
            var rotatedCss =  'transform: rotate(125deg); -ms-transform: rotate(125deg); -moz-transform: rotate(125deg); -webkit-transform: rotate(125deg); -o-transform: rotate(125deg)';
            var notRotatedCss = 'transform: rotate(0deg); -ms-transform: rotate(0deg); -moz-transform: rotate(0deg); -webkit-transform: rotate(0deg); -o-transform: rotate(0deg);';
            var statePath = window.QuantiModoIntegration.options.defaultState || 'intro';
            qmPageElements.qmIonicAppSidebar.template =
                '<div id="' + qmPageElements.qmIonicAppSidebar.id + '" style="display: none; z-index: 999997; height: 100%; position: fixed;right: 0; top: 0; border: 1px solid #eee; background: white; ">' +
                '<iframe style="height:100%;width:' + getOption('sideBarWidth') + ';" id="ionic-app-frame" frameborder="0" ' +
                'src="' + getIonicUrl(statePath)+ '">' +
                '</iframe>' +
                '</div>';
            createNewDiv(qmPageElements.qmIonicAppSidebar);
            qmPageElements.singleFloatingActionButton.css = {
                open: sharedButtonCss + "top:15px; right:" + getOption('sideBarWidth') + ';' + rotatedCss,
                closed: sharedButtonCss + "bottom:15px; right:" + getOption('floatingActionButtonRightOffset') +";" + notRotatedCss
            };
            function getIconUrl () {
                console.log('window.QuantiModoIntegration.appSettings', window.QuantiModoIntegration.appSettings);
                console.log('window.QuantiModoIntegration.appSettings.additionalSettings', window.QuantiModoIntegration.appSettings.additionalSettings);
                console.log('window.QuantiModoIntegration.appSettings.additionalSettings.appImages', window.QuantiModoIntegration.appSettings.additionalSettings.appImages);
                if (window.QuantiModoIntegration.appSettings) {
                    return window.QuantiModoIntegration.appSettings.additionalSettings.appImages.appIcon
                }
                return 'https://static.quantimo.do/app_uploads/' + getClientId() + '/app_images_appIcon.png'
            }
            qmPageElements.singleFloatingActionButton.template =
                '<img style="' + qmPageElements.singleFloatingActionButton.css.closed + 'display:none;" id="' +
                qmPageElements.singleFloatingActionButton.id + '" src="' + getIconUrl() + '"/>'
            qmPageElements.singleFloatingActionButton.onClickListener = function () {
                console.debug('Clicked QM button');
                toggleQmIonicAppSidebar();
            };
            createNewDiv(qmPageElements.singleFloatingActionButton);
            showElement(qmPageElements.singleFloatingActionButton.element, 5000);
        });
    };
    window.QuantiModoIntegration.createTripleFloatingActionButton = function() {
        var tripleFloatingActionButtonsCssStyles = ".qm-fab-container { bottom: 0; position: fixed; margin: 1em; right: " + qmPageElements.tripleFloatingActionButtons.right + "px;}" +
            ".qm-fab-buttons { box-shadow: 0px 5px 11px -2px rgba(0, 0, 0, 0.18), 0px 4px 12px -7px rgba(0, 0, 0, 0.15); border-radius: 50%; display: block; width: 56px; height: 56px; margin: 20px auto 0; position: relative; -webkit-transition: all .1s ease-out; transition: all .1s ease-out;}" +
            ".qm-fab-buttons:active, .qm-fab-buttons:focus, .qm-fab-buttons:hover { box-shadow: 0 0 4px rgba(0,0,0,.14), 0 4px 8px rgba(0,0,0,.28);}" +
            ".qm-fab-buttons:not(:last-child) { width: 40px; height: 40px; margin: 20px auto 0; opacity: 0; -webkit-transform: translateY(50px); -ms-transform: translateY(50px); transform: translateY(50px);}" +
            ".qm-fab-container:hover .qm-fab-buttons:not(:last-child) { opacity: 1; -webkit-transform: none; -ms-transform: none; transform: none; margin: 15px auto 0;}" +
            ".qm-fab-buttons:nth-last-child(1) { -webkit-transition-delay: 25ms; transition-delay: 25ms; background-image: url('https://cbwconline.com/IMG/Share.svg'); background-size: contain;}" +
            ".qm-fab-buttons:not(:last-child):nth-last-child(2) { -webkit-transition-delay: 50ms; transition-delay: 20ms; background-image: url('https://cbwconline.com/IMG/Facebook-Flat.png'); background-size: contain;}" +
            ".qm-fab-buttons:not(:last-child):nth-last-child(3) { -webkit-transition-delay: 75ms; transition-delay: 40ms; background-image: url('https://cbwconline.com/IMG/Twitter-Flat.png'); background-size: contain;}" +
            ".qm-fab-buttons:not(:last-child):nth-last-child(4) { -webkit-transition-delay: 100ms; transition-delay: 60ms; background-image: url('https://cbwconline.com/IMG/Google%20Plus.svg'); background-size: contain;}" +
            "[tooltip]:before { bottom: 25%; font-family: arial; font-weight: 600; border-radius: 2px; background: #585858; color: #fff; content: attr(tooltip); font-size: 12px; visibility: hidden; opacity: 0; padding: 5px 7px; margin-right: 12px; position: absolute; right: 100%; white-space: nowrap;}" +
            "[tooltip]:hover:before,[tooltip]:hover:after { visibility: visible; opacity: 1;}";
        qmPageElements.tripleFloatingActionButtons.template =
            '<nav id="' + qmPageElements.tripleFloatingActionButtons.id + '" class="qm-fab-container"> ' +
            '<a href="#" class="qm-fab-buttons" tooltip="Google+"></a>' +
            '<a href="#" class="qm-fab-buttons" tooltip="Twitter"></a>' +
            '<a href="#" class="qm-fab-buttons" tooltip="Facebook"></a>' +
            '<a class="qm-fab-buttons" tooltip="Share" href="#"></a>' +
            '</nav>';
        window.onload = function() {
            createNewDiv(qmPageElements.tripleFloatingActionButtons);
            applyCssStyles(tripleFloatingActionButtonsCssStyles);
        }
    };
    function addEventListenerToQmIntegrationButton() {
        window.onload = function() {
            var qmIntegrationButtonElement = document.getElementById('qm-integration-button');
            if (qmIntegrationButtonElement) {
                createNewDiv(qmPageElements.qmIonicAppSidebar);
                qmIntegrationButtonElement.addEventListener('click', toggleQmIonicAppSidebar);
            }
        }
    }
    addEventListenerToQmIntegrationButton();
})();
