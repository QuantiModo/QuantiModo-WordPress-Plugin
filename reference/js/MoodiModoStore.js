var moodData = new Array();

var storeParams = {
		storeName: 'MoodStore',
		storePrefix: 'QM-',
		dbVersion: 1,
		keyPath: 'timestamp',
		onStoreReady: function() { 
			initMoodData2();
		}
};

var myStore = new IDBStore(storeParams);

function openDatabase(){
	myStore = new IDBStore(storeParams);
};

function addMood(moodResult) {
		moodData[moodData.length] = moodResult;
		
		var data = {
				"moodId": parseInt(moodResult.moodId, 10),
				"guilty": parseInt(moodResult.guilty, 10),
				"alert": parseInt(moodResult.alert, 10),
				"afraid": parseInt(moodResult.afraid, 10),
				"excited": parseInt(moodResult.excited, 10),
				"irritable": parseInt(moodResult.irritable, 10),
				"ashamed": parseInt(moodResult.ashamed, 10),
				"attentive": parseInt(moodResult.attentive, 10),
				"hostile": parseInt(moodResult.hostile, 10),
				"active": parseInt(moodResult.active, 10),
				"nervous": parseInt(moodResult.nervous, 10),
				"interested": parseInt(moodResult.interested, 10),
				"enthusiastic": parseInt(moodResult.enthusiastic, 10),
				"jittery": parseInt(moodResult.jittery, 10),
				"strong": parseInt(moodResult.strong, 10),
				"distressed": parseInt(moodResult.distressed, 10),
				"determined": parseInt(moodResult.determined, 10),
				"upset": parseInt(moodResult.upset, 10),
				"proud": parseInt(moodResult.proud, 10),
				"scared": parseInt(moodResult.scared, 10),
				"inspired": parseInt(moodResult.inspired, 10),
				"timestamp": parseInt(moodResult.timestamp, 10)
		};
	
		var onsuccess = function(e) {
			//console.log("item added");
			//initMoodData2();
		};
	
		var onerror = function(e) {
			console.error("Error while adding item");
			moodData.pop(moodResult);
			//TODO: Show message to user
		};
		myStore.put(data, onsuccess, onerror);
};

function getAllMoods() {
	var onsuccess2 = function(data){
	  var i = 0;
	  data.forEach(function(item){
		  moodData[i] = item;
		  i++;
	  });

	  initLineChartData();
	  initBarChartData();
	}
	
	var onerror2 = function(error){
	  console.log('Error while loading moods', error);
	}
	
	myStore.getAll(onsuccess2, onerror2);
};

function initMoodData2()
{
	if(moodData.length == 0)
	{
		getAllMoods();
	}
	else
	{
		initLineChartData();
		initBarChartData();
	}
}


function initBarChartData()
{
	var sui=0;
	var sad=0;
	var ok=0;
	var hap=0;
	var ecs=0;
	
	var mood;
    var moodsNumber = moodData.length;
    for (i = 0; i < moodsNumber; i++) 
    {
       mood = moodData[i];
       if(mood.moodId == 0)
       {
    	   sui=sui+1;
       }
       else if(mood.moodId == 1)
       {
    	   sad=sad+1;
       }
       else if(mood.moodId == 2)
       {
    	   ok=ok+1;
       }
       else if(mood.moodId == 3)
       {
    	   hap=hap+1;
       }
       else if(mood.moodId == 4)
       {
    	   ecs=ecs+1;
       }
    }
    
    barChartData[0] = parseInt(sui, 10);
    barChartData[1] = parseInt(sad, 10);
    barChartData[2] = parseInt(ok, 10);
    barChartData[3] = parseInt(hap, 10);
    barChartData[4] = parseInt(ecs, 10);
}

function initLineChartData()
{
	var mood;
    var moodsNumber = moodData.length;
    for (i = 0; i < moodsNumber; i++) 
    {
       mood = moodData[i];
       if(mood.moodId == 0)
       {
    	   lineChartData[i] = 0;
       }
       else if(mood.moodId == 1)
       {
    	   lineChartData[i] = 1;
       }
       else if(mood.moodId == 2)
       {
    	   lineChartData[i] = 2;
       }
       else if(mood.moodId == 3)
       {
    	   lineChartData[i] = 3;
       }
       else if(mood.moodId == 4)
       {
    	   lineChartData[i] = 4;
       }
    }
}

function saveMood(moodId, manually)
{
	setMoodId(moodId);
	var moodEntry1 = new moodEntry(questionAnswers);
	addMood(moodEntry1);

	if(manually == "true" || manually == true)
	{
		parent.history.back();
	}
	else if(manually == "false" || manually == false)
	{
		tizen.application.getCurrentApplication().exit();
	}
}

