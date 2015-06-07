//Initialize function
var moodLabels = [ "Depressed", "Sad", "OK", "Happy", "Ecstatic" ];
var colors = [ "#55000000", "#5D83FF", "#68B107", "#ffbd40", "#CB0000" ];
var lineChartData = [];
var barChartData = [];
// = [ 3, 3, 3, 3, 4, 1, 2, 1, 4, 3, 3, 3, 3, 4, 2, 1, 3, 2, 0, 0, 1, 2, 0, 3, 4 ];

//------------init linechart-------------//
var initLineChart = function() {
	lineChart = new Highcharts.Chart({
	//$('#LineContainer').highcharts({
		chart : {
			borderWidth : 2,
			backgroundColor : '#EFEDE5',
			borderColor : '#DEDCD5',
			type : 'spline',
			height : 300,
			margin : [ 0, 0, 0, 0 ],
			spacingBottom : 0,
			spacingLeft : 0,
			spacingRight : 0,
			renderTo: 'LineContainer'
		},
		title : {
			text : ''
		},
		legend : {
			enabled : false
		},
		lang: {
			loading: ''
		},
		loading: {
			style: {
				background: 'url(/res/loading3.gif) no-repeat center'
			},
            hideDuration: 10,
            showDuration: 10
		},
		plotOptions : {
			spline : {
				lineWidth : 2,
				allowPointSelect : false,
				marker : {
					enabled : false
				},
				enableMouseTracking : false,
				size : '100%',
				dataLabels : {
					enabled : false
				}
			}
		},
		credits: {
			enabled: false
		},
		series : [ {
			name : 'Your mood',
			data : lineChartData
		} ]
	});
	
	
};


//-------------init barchart-------------//
var initBarChart = function() {
	
	/*console.log("barChartData after setting: " + localStorage.getItem("barChartData"));
	var barData = localStorage.getItem("barChartData");
	var data = [barData[0], barData[2], barData[4], barData[6], barData[8]];
	barChartData[0] = barData[0];
	barChartData[1] = barData[2];
	barChartData[2] = barData[4];
	barChartData[3] = barData[6];
	barChartData[4] = barData[8];
	console.log("barChartData after setting 2: " + data);*/

	barChart = new Highcharts.Chart({
	//$('#BarContainer').highcharts({
		chart : {
			borderWidth : 2,
			backgroundColor : '#EFEDE5',
			borderColor : '#DEDCD5',
			height : 400,
			type : 'column',
			renderTo : 'BarContainer',
			animation: {
                duration: 1000
            }
		},
		title : {
			text : ''
		},
		xAxis : {
			categories : [ 'Depressed', 'Sad', 'OK', 'Happy', 'Ecstatic' ]
		},
		yAxis : {
			title : {
				text : ''
			},
			min : 0
		},
		lang: {
			loading: ''
		},
		loading: {
			style: {
				background: 'url(/res/loading3.gif) no-repeat center'
			},
            hideDuration: 10,
            showDuration: 10
		},
		legend : {
			enabled : false
		},
		plotOptions : {
			column : {
				pointPadding : 0.2,
				borderWidth : 0,
				pointWidth : 40,
				enableMouseTracking : false,
				colorByPoint : true
			}
		},
		credits: {
			enabled: false
		},
		colors : colors,
		series : [ {
			data : barChartData
		} ]
	});
};


//--------------init charts--------------//

var initCharts = function() {
	initLineChart();
	initBarChart();
	//console.log("charts loaded");
};

$(document).bind('pagebeforeshow', '#home', function() {
	//getBarChartData();
	//console.log("db manager barchart data to chart data");
	//initLineChartData();
	//initMoodData2();
	openDatabase();
	//getMoodData();
	//initBarChartData();
	initCharts();
});
