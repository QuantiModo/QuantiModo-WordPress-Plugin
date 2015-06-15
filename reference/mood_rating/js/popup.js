function clearNotifications()
{
	var badgeParams = {text:""};
	chrome.browserAction.setBadgeText(badgeParams);
	chrome.notifications.clear("moodReportNotification", function(){});
}

function setMoodButtonListeners()
{
	document.getElementById('buttonMoodDepressed').onclick=onMoodButtonClicked;
	document.getElementById('buttonMoodSad').onclick=onMoodButtonClicked;
	document.getElementById('buttonMoodOk').onclick=onMoodButtonClicked;
	document.getElementById('buttonMoodHappy').onclick=onMoodButtonClicked;
	document.getElementById('buttonMoodEcstatic').onclick=onMoodButtonClicked;
}

var onMoodButtonClicked = function()
{
	// Figure out what rating was selected
	var buttonId = this.id;
	if(buttonId == "buttonMoodDepressed")
	{
		var moodValue = 1;
	}
	else if(buttonId == "buttonMoodSad")
	{
		var moodValue = 2;
	}
	else if(buttonId == "buttonMoodOk")
	{
		var moodValue = 3;
	}
	else if(buttonId == "buttonMoodHappy")
	{
		var moodValue = 4;
	}
	else if(buttonId == "buttonMoodEcstatic")
	{
		var moodValue = 5;
	}
	else
	{
		console.log("How did I get here...");
		return;
	}
	
	// Create an array of measurements
	var measurements = 	[
							{
								timestamp: 	Math.floor(Date.now() / 1000), 
								value: 		moodValue
							}
						];
	// Add it to a request, payload is what we'll send to QuantiModo
	var request =		{
							message: "uploadMeasurements",
							payload:[
										{
											measurements:			measurements,
											name: 					"Overall Mood",
											source: 				"MoodiModo",
											category: 				"Mood",
											combinationOperation: 	"MEAN",
											unit:					"/5"
										}
									]
									
						};
	// Request our background script to upload it for us
	chrome.extension.sendMessage(request);
	
	clearNotifications();
	window.close();
	
	/*var sectionRateMood = document.getElementById("sectionRateMood");
	var sectionSendingMood = document.getElementById("sectionSendingMood");
	
	sectionRateMood.className = "invisible";
	setTimeout(function()
	{
			sectionRateMood.style.display = "none";

			sectionSendingMood.innerText = "Sending mood";
			sectionSendingMood.style.display = "block";
			sectionSendingMood.className = "visible";
			pushMeasurement(measurement, function(response) 
				{
					sectionSendingMood.className = "invisible";
					setTimeout(function()
					{
						window.close();
					}, 300);
				});
				
			clearNotifications();
		}, 400 );*/
}

document.addEventListener('DOMContentLoaded', function () 
{
	//console.log('pankajkmar');
	var wDiff = (346 - window.innerWidth);
	var hDiff = (60 - window.innerHeight);
	
	window.resizeBy(wDiff, hDiff);

	// cookie function call here to remove the error.
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

	/*
	var backgroundPage = chrome.extension.getBackgroundPage();
	backgroundPage.isUserLoggedIn(function(isLoggedIn)
	{
		if(!isLoggedIn)
		{

		}
	});
	*/

	setMoodButtonListeners();

});


