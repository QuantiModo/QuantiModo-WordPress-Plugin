//-------------Mood alarms-------------//
function setMoodAlarm()
{
	//Alarms mood notifications
	if(getMoodNotificationInterval() == 0)
	{
		try
		{
			if(localStorage.getItem("moodAlarmId") != "null" && localStorage.getItem("moodAlarmId") != null && localStorage.getItem("moodAlarmId") != "")
			{
				//console.log("moodAlarmId in local storage: " + localStorage.getItem("moodAlarmId"));
				//tizen.alarm.remove(localStorage.getItem("moodAlarmId"));
				tizen.alarm.removeAll();
			}
			localStorage.setItem("moodAlarmId", "");
		}
		catch (e){
	        console.log("Exception: " + e);
	    }
	}
	
	else if(getMoodNotificationInterval() == 1)
	{
		try
		{
			//Every hour
			var alarmHourly = new tizen.AlarmRelative(tizen.alarm.PERIOD_HOUR, tizen.alarm.PERIOD_HOUR);
			tizen.alarm.add(alarmHourly, tizen.application.getCurrentApplication().appInfo.id, appControl);
			
			if(localStorage.getItem("moodAlarmId") != "null" && localStorage.getItem("moodAlarmId") != null && localStorage.getItem("moodAlarmId") != "")
			{
				//console.log("moodAlarmId in local storage: " + localStorage.getItem("moodAlarmId"));
				tizen.alarm.removeAll();
				//tizen.alarm.remove(localStorage.getItem("moodAlarmId"));
				localStorage.setItem("moodAlarmId", alarmHourly.id);
			}
			else
			{
				localStorage.setItem("moodAlarmId", alarmHourly.id);
			}
		}
		catch (eq){
	        console.log("Exception: " + eq);
	    }
	}
		
	else if(getMoodNotificationInterval() == 2)
	{
		try
		{
			//Every 3 hours
			var alarm3Hourly = new tizen.AlarmRelative(3 * tizen.alarm.PERIOD_HOUR, 3 * tizen.alarm.PERIOD_HOUR);
			tizen.alarm.add(alarm3Hourly, tizen.application.getCurrentApplication().appInfo.id, appControl);
			
			if(localStorage.getItem("moodAlarmId") != "null" && localStorage.getItem("moodAlarmId") != null && localStorage.getItem("moodAlarmId") != "")
			{
				//console.log("moodAlarmId in local storage: " + localStorage.getItem("moodAlarmId"));
				tizen.alarm.removeAll();
				//tizen.alarm.remove(localStorage.getItem("moodAlarmId"));
				localStorage.setItem("moodAlarmId", alarm3Hourly.id);
			}
			else
			{
				localStorage.setItem("moodAlarmId", alarm3Hourly.id);
			}
		
		}
		catch (e2){
	        console.log("Exception: " + e2);
	    }
		
	}
	
	else if(getMoodNotificationInterval() == 3)
	{
		try
		{
			//Twice daily
			var alarmTwiceDaily = new tizen.AlarmRelative(12 * tizen.alarm.PERIOD_HOUR, 12 * tizen.alarm.PERIOD_HOUR);
			tizen.alarm.add(alarmTwiceDaily, tizen.application.getCurrentApplication().appInfo.id, appControl);
			
			if(localStorage.getItem("moodAlarmId") != "null" && localStorage.getItem("moodAlarmId") != null && localStorage.getItem("moodAlarmId") != "")
			{
				//console.log("moodAlarmId in local storage: " + localStorage.getItem("moodAlarmId"));
				tizen.alarm.removeAll();
				//tizen.alarm.remove(localStorage.getItem("moodAlarmId"));
				localStorage.setItem("moodAlarmId", alarmTwiceDaily.id);
			}
			else
			{
				localStorage.setItem("moodAlarmId", alarmTwiceDaily.id);
			}
		
		}
		catch (e3){
	        console.log("Exception: " + e3);
	    }
		
	}
	
	else if(getMoodNotificationInterval() == 4)
	{
		try
		{
			//Every day
			var alarmDaily = new tizen.AlarmRelative(tizen.alarm.PERIOD_DAY, tizen.alarm.PERIOD_DAY);
			tizen.alarm.add(alarmDaily, tizen.application.getCurrentApplication().appInfo.id, appControl);
			
			//console.log("alarm added with id: " + alarmDaily.id);

			if(localStorage.getItem("moodAlarmId") != "null" && localStorage.getItem("moodAlarmId") != null && localStorage.getItem("moodAlarmId") != "")
			{
				//console.log("moodAlarmId in local storage: " + localStorage.getItem("moodAlarmId"));
				tizen.alarm.removeAll();
				//tizen.alarm.remove(localStorage.getItem("moodAlarmId"));
				localStorage.setItem("moodAlarmId", alarmDaily.id);
			}
			else
			{
				localStorage.setItem("moodAlarmId", alarmDaily.id);
			}
			
		}
		catch (e4){
	        console.log("Exception: " + e4);
	    }
			
	}
	
	/*else if(getMoodNotificationInterval() == 5)
	{
		try
		{
			//Every 30 seconds
			var alarmDebug = new tizen.AlarmRelative(30, 30);
			tizen.alarm.add(alarmDebug, tizen.application.getCurrentApplication().appInfo.id, appControl);
			console.log("alarm added with id: " + alarmDebug.id);

			if(localStorage.getItem("moodAlarmId") != "null" && localStorage.getItem("moodAlarmId") != null && localStorage.getItem("moodAlarmId") != "")
			{
				console.log("moodAlarmId in local storage: " + localStorage.getItem("moodAlarmId"));
				tizen.alarm.remove(localStorage.getItem("moodAlarmId"));
				localStorage.setItem("moodAlarmId", alarmDebug.id);
			}
			else
			{
				localStorage.setItem("moodAlarmId", alarmDebug.id);
			}
			console.log("alarm set: every 30 seconds");	
		}
		catch (e){
	        console.log("Exception: " + e);
	    }
			
	}*/
}
//-------MoodNotification interval-------//
function getMoodNotificationInterval()
{
	if(localStorage.getItem("moodNotificationInterval") != null)
	{
		return localStorage.getItem("moodNotificationInterval");
	}
	else
	{
		localStorage.setItem("moodNotificationInterval", 2);
		return localStorage.getItem("moodNotificationInterval");
	}
}

function setMoodNotificationInterval(value)
{
	localStorage.setItem("moodNotificationInterval", value);
	setMoodAlarm();
}

//
