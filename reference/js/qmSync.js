var quantimodo = Quantimodo();

var toQuantimodoRecord = function(moodEntry)
{
	var records = new Array(NUM_RESULT_TYPES);
	for(var i = 0; i < NUM_RESULT_TYPES; i++)
	{
		if(moodEntry[i] != RATING_VALUE_NULL)
		{
			Log.i("RATING: " + i + " val: " + moodEntry[i]);
			switch(i)
			{
			case RATING_MOOD:
				records[RATING_MOOD] = ["MoodiModo", "Overall Mood", "Mood", "MEAN", this.timestamp, moodEntry[RATING_MOOD], "/5"];
				break;
			case RATING_GUILTY:
				records[RATING_GUILTY] = ["MoodiModo", "Guiltiness", "Mood", "MEAN", this.timestamp, moodEntry[RATING_GUILTY], "/5"];
				break;
			case RATING_ALERT:
				records[RATING_ALERT] = ["MoodiModo", "Alertness", "Mood", "MEAN", this.timestamp, moodEntry[RATING_ALERT], "/5"];
				break;
			case RATING_AFRAID:
				records[RATING_AFRAID] = ["MoodiModo", "Fear", "Mood", "MEAN", this.timestamp, moodEntry[RATING_AFRAID], "/5"];
				break;
			case RATING_EXCITED:
				records[RATING_EXCITED] = ["MoodiModo", "Excitability", "Mood", "MEAN", this.timestamp, moodEntry[RATING_EXCITED], "/5"];
				break;
			case RATING_IRRITABLE:
				records[RATING_IRRITABLE] = ["MoodiModo", "Irritability", "Mood", "MEAN", this.timestamp, moodEntry[RATING_IRRITABLE], "/5"];
				break;
			case RATING_ASHAMED:
				records[RATING_ASHAMED] = ["MoodiModo", "Shame", "Mood", "MEAN", this.timestamp, moodEntry[RATING_ASHAMED], "/5"];
				break;
			case RATING_ATTENTIVE:
				records[RATING_ATTENTIVE] = ["MoodiModo", "Attentiveness", "Mood", "MEAN", this.timestamp, moodEntry[RATING_ATTENTIVE], "/5"];
				break;
			case RATING_HOSTILE:
				records[RATING_HOSTILE] = ["MoodiModo", "Hostility", "Mood", "MEAN", this.timestamp, moodEntry[RATING_HOSTILE], "/5"];
				break;
			case RATING_ACTIVE:
				records[RATING_ACTIVE] = ["MoodiModo", "Activeness", "Mood", "MEAN", this.timestamp, moodEntry[RATING_ACTIVE], "/5"];
				break;
			case RATING_NERVOUS:
				records[RATING_NERVOUS] = ["MoodiModo", "Nervousness", "Mood", "MEAN", this.timestamp, moodEntry[RATING_NERVOUS], "/5"];
				break;
			case RATING_INTERESTED:
				records[RATING_INTERESTED] = ["MoodiModo", "Interest", "Mood", "MEAN", this.timestamp, moodEntry[RATING_INTERESTED], "/5"];
				break;
			case RATING_ENTHUSIASTIC:
				records[RATING_ENTHUSIASTIC] = ["MoodiModo", "Enthusiasm", "Mood", "MEAN", this.timestamp, moodEntry[RATING_ENTHUSIASTIC], "/5"];
				break;
			case RATING_JITTERY:
				records[RATING_JITTERY] = ["MoodiModo", "Jitteriness", "Mood", "MEAN", this.timestamp, moodEntry[RATING_JITTERY], "/5"];
				break;
			case RATING_STRONG:
				records[RATING_STRONG] = ["MoodiModo", "Resilience", "Mood", "MEAN", this.timestamp, moodEntry[RATING_STRONG], "/5"];
				break;
			case RATING_DISTRESSED:
				records[RATING_DISTRESSED] = ["MoodiModo", "Distress", "Mood", "MEAN", this.timestamp, moodEntry[RATING_DISTRESSED], "/5"];
				break;
			case RATING_DETERMINED:
				records[RATING_DETERMINED] = ["MoodiModo", "Determination", "Mood", "MEAN", this.timestamp, moodEntry[RATING_DETERMINED], "/5"];
				break;
			case RATING_UPSET:
				records[RATING_UPSET] = ["MoodiModo", "Upsettedness", "Mood", "MEAN", this.timestamp, moodEntry[RATING_UPSET], "/5"];
				break;
			case RATING_PROUD:
				records[RATING_PROUD] = ["MoodiModo", "Pride", "Mood", "MEAN", this.timestamp, moodEntry[RATING_PROUD], "/5"];
				break;
			case RATING_SCARED:
				records[RATING_SCARED] = ["MoodiModo", "Scaredness", "Mood", "MEAN", this.timestamp, moodEntry[RATING_SCARED], "/5"];
				break;
			case RATING_INSPIRED:
				records[RATING_INSPIRED] = ["MoodiModo", "Inspiration", "Mood", "MEAN", this.timestamp, moodEntry[RATING_INSPIRED], "/5"];
				break;
			}
		}
	}
	return records;
};

var putMeasurementsSynchronous = function(token, records)
{
	console.log("QMSDK", "Start putMeasurementsSynchronous with " + records.size() + " records");

	//final GsonBuilder gsonBuilder = new GsonBuilder();
	//gsonBuilder.registerTypeAdapter(QuantimodoRecord.class, new QuantimodoRecordSerializer());

	//Ion.getDefault(context).setLogging("QMSDK", Log.DEBUG);

	var maxTries = 5;
	var recordSetSize = 50;
	var numRecordSets = Math.ceil(records.size() / recordSetSize);
	console.log("QMSDK", "Will be sending " + numRecordSets + " sets of records");

	var startTime = System.currentTimeMillis();	
	
	var recordSets = new Array(numRecordSets);
	//List<List<QuantimodoRecord>> recordSets = new ArrayList<List<QuantimodoRecord>>(numRecordSets);

	//Gson gson = new Gson();

	for (var i = 0; i < numRecordSets; i++)
	{
		Log.i("QMSDK", "Creating set: " + (recordSetSize * i) + "," + Math.min(recordSetSize * (i + 1) - 1, records.size() - 1));

		//var record = records[i];
		recordSets.add(records[i]);
		//recordSets.add(records[i]
		//subList(recordSetSize * i, Math.min(recordSetSize * (i + 1), records.size())));
		var response = null;
		var currentFailWait = 500;
		while (true)
		{
			try
			{
				/*Log.i("QMSDK", "Sending set: " + i + " of " + recordSets.get(i).size() + " records");
				 */
				response = quantimodo.postMeasurements(recordSets[i], f);
				/*response = Ion.with(context, "https://quantimodo.com/api/measurements")
						.setTimeout(10000 + (20 * recordSetSize))
						.setHeader("Cookie", token)
						.setJsonObjectBody(recordSets.get(i))
						.asString()
						.get();*/
				Log.i("QMSDK", "API Response: " + response);
				break;
			}
			catch (InterruptedException)
			{
				printStackTrace();
			}
			try
			{
				currentFailWait *= 2;
				Thread.sleep(currentFailWait);
			}
			catch (InterruptedException)
			{
			}
		}
	}

	long elapsedTime = (System.currentTimeMillis() - startTime);
	Log.i("QMSDK", "Done in : " + elapsedTime + ", millis per record: " + (elapsedTime / records.size()));

	return null;
};

