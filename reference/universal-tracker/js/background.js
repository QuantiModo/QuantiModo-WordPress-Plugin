/***
****	HELPER FUNCTIONS FOR FOREGROUND PROCESS
***/

/*
**	Returns true in the result listener if the user is logged in, false if not
*/
function isUserLoggedIn(resultListener)
{
	chrome.cookies.get({ url: 'https://quantimo.do', name: 'wordpress_logged_in_df6e405f82a01fe45903695de91ec81d' },
	  function (cookie) {
		if (cookie) {
		  console.log(cookie.value);
		}
		else {
			var url = "https://quantimo.do/analyze";
			chrome.tabs.create({"url":url, "selected":true});
		}
	});
}


/***
****	EVENT HANDLERS
***/

/*
**	Called when the extension is installed
*/
chrome.runtime.onInstalled.addListener(function() 
{
	//var notificationInterval = parseInt(localStorage["notificationInterval"] || "180");
	var notificationInterval = -1;
	
	if(notificationInterval == -1)
	{
		chrome.alarms.clear("trackReportAlarm");
		console.log("Alarm cancelled");
	}
	else
	{
		var alarmInfo = {periodInMinutes: notificationInterval}
		chrome.alarms.create("trackReportAlarm", alarmInfo)
		console.log("Alarm set, every " + notificationInterval + " minutes");
	}
});

/*
**	Called when an alarm goes off (we only have one)
*/
chrome.alarms.onAlarm.addListener(function(alarm)
{
	var showNotification = (localStorage["showNotification"] || "true") == "true" ? true : false;
	if(showNotification)
	{
		var notificationParams = {
			type: "basic",
			title: "How are you?",
			message: "It's time to add a measurement!",
			iconUrl: "images/photo.png",
			priority: 2
		}
		chrome.notifications.create("trackReportNotification", notificationParams, function(id){});
	}
	
	var showBadge = (localStorage["showBadge"] || "true") == "true" ? true : false;
	if(showBadge)
	{
		var badgeParams = {text:"?"};
		chrome.browserAction.setBadgeText(badgeParams);
	}
});

/*
**	Called when the "add a measurement" notification is clicked
*/
chrome.notifications.onClicked.addListener(function(notificationId)
{
	if(notificationId == "trackReportNotification")
	{
		var windowParams = {url: "popup.html", 
							type: 'panel',
							width: 300,
							height: 290,
							top: screen.height,
							left: screen.width
						   };
		chrome.windows.create(windowParams);
	}
});

/*
**	Handles extension-specific requests that come in, such as a 
** 	request to upload a new measurement
*/
chrome.extension.onMessage.addListener(function(request, sender, sendResponse)
{
	console.log("Received request: " + request.message);
	if(request.message == "uploadMeasurements") 
	{
		uploadMeasurements(request.payload, function (responseText) {
			sendResponse(responseText);
        });
		return true;
	} 
	else if(request.message == "getVariables") 
	{
		getVariables(request.params, function (responseText) {
			sendResponse(responseText);
        });
		return true;
	} 
	else if(request.message == "getVariableUnits") 
	{
		
		getVariableUnits(request.params, function (responseText) {
			sendResponse(responseText);
        });
		return true;
	} else if ( request.message == "getVariableCategories" )
	{
		getVariableCategories(request.params, function (responseText) {
			sendResponse(responseText);
        });
		return true;
	}
	
});

chrome.tabs.getSelected(null, function(tab){
    //chrome.tabs.executeScript(tab.id, {code: "alert('test');"}, function(response) {
    //});
});

/***
****	HELPER FUNCTIONS
***/

function uploadMeasurements(measurements, onDoneListener)
{
	var xhr = new XMLHttpRequest();
	xhr.open("POST", "https://quantimo.do/api/measurements/v2", true);
	xhr.onreadystatechange = function() 
		{
			// If the request is completed
			if (xhr.readyState == 4) 
			{
				if(onDoneListener != null)
				{
					onDoneListener(xhr.responseText);
				}
			}
		};
	xhr.send(JSON.stringify(measurements));
}

function getVariables(params, onDoneListener)
{
	var xhr = new XMLHttpRequest();
	xhr.open("GET", "https://quantimo.do/api/variables", true);
	xhr.onreadystatechange = function() 
		{
			// If the request is completed
			if (xhr.readyState == 4) 
			{
				if(onDoneListener != null)
				{
					onDoneListener(xhr.responseText);
				}
			}
		};
	xhr.send(JSON.stringify(params));
}

// Categories Filled
function getVariableCategories(params, onDoneListener)
{
	var xhr = new XMLHttpRequest();
	xhr.open("GET", "https://quantimo.do/api/variableCategories", true);
	xhr.onreadystatechange = function() 
		{
			// If the request is completed
			if (xhr.readyState == 4) 
			{
				if(onDoneListener != null)
				{
					onDoneListener(xhr.responseText);
				}
			}
		};
	xhr.send(JSON.stringify(params));
}


function getVariableUnits(params, onDoneListener)
{
	var xhr = new XMLHttpRequest();
	xhr.open("GET", "https://quantimo.do/api/units", true);
	xhr.onreadystatechange = function() 
		{
			// If the request is completed
			if (xhr.readyState == 4) 
			{
				if(onDoneListener != null)
				{
					onDoneListener(xhr.responseText);
				}
			}
		};
	xhr.send(JSON.stringify(params));
}