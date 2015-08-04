var questionAnswers = new Array(NUM_RESULT_TYPES);


var NUM_RESULT_TYPES = 22;

var RATING_VALUE_NULL = -1;
var RATING_VALUE_1 = 0;
var RATING_VALUE_2 = 1;
var RATING_VALUE_3 = 2;
var RATING_VALUE_4 = 3;
var RATING_VALUE_5 = 4;

var RATING_MOOD = 0;
var RATING_ACCURATE_MOOD = 1;
var RATING_GUILTY = 2;
var RATING_ALERT = 3;
var RATING_AFRAID = 4;
var RATING_EXCITED = 5;
var RATING_IRRITABLE = 6;
var RATING_ASHAMED = 7;
var RATING_ATTENTIVE = 8;
var RATING_HOSTILE = 9;
var RATING_ACTIVE = 10;
var RATING_NERVOUS = 11;
var RATING_INTERESTED = 12;
var RATING_ENTHUSIASTIC = 13;
var RATING_JITTERY = 14;
var RATING_STRONG = 15;
var RATING_DISTRESSED = 16;
var RATING_DETERMINED = 17;
var RATING_UPSET = 18;
var RATING_PROUD = 19;
var RATING_SCARED = 20;
var RATING_INSPIRED = 21;

var averageMood;

function moodEntry(answers) 
{
	this.moodId = answers[RATING_MOOD];
    this.accurateMood = answers[RATING_ACCURATE_MOOD];
    this.guilty = ((answers[RATING_GUILTY] != -1) ? answers[RATING_GUILTY] : -1);
    this.alert = ((answers[RATING_ALERT] != -1) ? answers[RATING_ALERT] : -1);
    this.afraid = ((answers[RATING_AFRAID] != -1) ? answers[RATING_AFRAID] : -1);
    this.excited = ((answers[RATING_EXCITED] != -1) ? answers[RATING_EXCITED] : -1);
    this.irritable = ((answers[RATING_IRRITABLE] != -1) ? answers[RATING_IRRITABLE] : -1);
    this.ashamed = ((answers[RATING_ASHAMED] != -1) ? answers[RATING_ASHAMED] : -1);
    this.attentive = ((answers[RATING_ATTENTIVE] != -1) ? answers[RATING_ATTENTIVE] : -1);
    this.hostile = ((answers[RATING_HOSTILE] != -1) ? answers[RATING_HOSTILE] : -1);
    this.active = ((answers[RATING_ACTIVE] != -1) ? answers[RATING_ACTIVE] : -1);
    this.nervous = ((answers[RATING_NERVOUS] != -1) ? answers[RATING_NERVOUS] : -1);
    this.interested = ((answers[RATING_INTERESTED] != -1) ? answers[RATING_INTERESTED] : -1);
    this.enthusiastic = ((answers[RATING_ENTHUSIASTIC] != -1) ? answers[RATING_ENTHUSIASTIC] : -1);
    this.jittery = ((answers[RATING_JITTERY] != -1) ? answers[RATING_JITTERY] : -1);
    this.strong = ((answers[RATING_STRONG] != -1) ? answers[RATING_STRONG] : -1);
    this.distressed = ((answers[RATING_DISTRESSED] != -1) ? answers[RATING_DISTRESSED] : -1);
    this.determined = ((answers[RATING_DETERMINED] != -1) ? answers[RATING_DETERMINED] : -1);
    this.upset = ((answers[RATING_UPSET] != -1) ? answers[RATING_UPSET] : -1);
    this.proud = ((answers[RATING_PROUD] != -1) ? answers[RATING_PROUD] : -1);
    this.scared = ((answers[RATING_SCARED] != -1) ? answers[RATING_SCARED] : -1);
    this.inspired = ((answers[RATING_INSPIRED] != -1) ? answers[RATING_INSPIRED] : -1);
    this.timestamp = new Date().getTime() / 1000;
    
};

function resetQuestionAnswers()
{
	questionAnswers = new Array(NUM_RESULT_TYPES);
	
	for(var i = 0; i < questionAnswers.length; i++)
	{
		questionAnswers[i] = RATING_VALUE_NULL;
	}
}

function setMoodId(moodId)
{
	questionAnswers[RATING_MOOD] = parseInt(moodId, 10);
	questionAnswers[RATING_ACCURATE_MOOD] = parseInt(((moodId / 5) * 100), 10);
}
function setAccurateMood(accurateMood)
{
	questionAnswers[RATING_ACCURATE_MOOD] = accurateMood;
}
function setGuilty(guilty)
{
	questionAnswers[RATING_GUILTY] = guilty;
}
function setAlert(alert)
{
	questionAnswers[RATING_ALERT] = alert;
}
function setAfraid(afraid)
{
	questionAnswers[RATING_AFRAID] = afraid;
}
function setExcited(excited)
{
	questionAnswers[RATING_EXCITED] = excited;
}
function setIrritable(irritable)
{
	questionAnswers[RATING_IRRITABLE] = irritable;
}
function setAshamed(ashamed)
{
	questionAnswers[RATING_ASHAMED] = ashamed;
}
function setAttentive(attentive)
{
	questionAnswers[RATING_ATTENTIVE] = attentive;
}
function setHostile(hostile)
{
	questionAnswers[RATING_HOSTILE] = hostile;
}
function setActive(active)
{
	questionAnswers[RATING_ACTIVE] = active;
}
function setNervous(nervous)
{
	questionAnswers[RATING_NERVOUS] = nervous;
}
function setInterested(interested)
{
	questionAnswers[RATING_INTERESTED] = interested;
}
function setEnthusiastic(enthusiastic)
{
	questionAnswers[RATING_ENTHUSIASTIC] = enthusiastic;
}
function setJittery(jittery)
{
	questionAnswers[RATING_JITTERY] = jittery;
}
function setStrong(strong)
{
	questionAnswers[RATING_STRONG] = strong;
}
function setDistressed(distressed)
{
	questionAnswers[RATING_DISTRESSED] = distressed;
}
function setDetermined(determined)
{
	questionAnswers[RATING_DETERMINED] = determined;
}
function setUpset(upset)
{
	questionAnswers[RATING_UPSET] = upset;
}
function setProud(proud)
{
	questionAnswers[RATING_PROUD] = proud;
}
function setScared(scared)
{
	questionAnswers[RATING_SCARED] = scared;
}
function setInspired(inspired)
{
	questionAnswers[RATING_INSPIRED] = inspired;
}

function showMoodDetails(moodEntry)
{
	//$('#moodDetails').append("<h4 id='mood'>" + moodEntry.moodId + "</h4>");
	$.mobile.changePage("mood_details.html");
	
}