var AnalyzePage = function() {
	var timezone = jstz.determine().name();

	var sharedData;

	var correlationGaugeVisible = true;
	var scatterplotVisible = true;

	var getInputData = function()
	{
		var queryString = window.location.search.substring(1, window.location.search.length);

		var parts = queryString.split('=', 2);
		if (parts[0] == "data")
		{
			Quantimodo.getCorrelateShare(
			{
				'id': parts[1],
			},
			function(result) {
				sharedData = result;
				AnalyzeChart.setInputData(sharedData['causeVariable'], sharedData['causeMeasurements']);
				AnalyzeChart.setOutputData(sharedData['effectVariable'], sharedData['effectMeasurements']);
			});
		}
		else
		{
			alert("Not a valid share");
		}
	}

	return {
		init: function()
		{
			getInputData();
		},
		hideScatterplot: function() { if (scatterplotVisible) {toggleElement('#scatterplot-graph'); scatterplotVisible = false;} },
		showScatterplot: function() { if (!scatterplotVisible) {toggleElement('#scatterplot-graph'); scatterplotVisible = true} },
		hideCorrelationGauge: function() { if (correlationGaugeVisible) {toggleElement('#correlation-gauge'); correlationGaugeVisible = false;} },
		showCorrelationGauge: function() { if (!correlationGaugeVisible) {toggleElement('#correlation-gauge'); correlationGaugeVisible = true} }
	};
}();

jQuery(AnalyzePage.init);

function toggleElement(element)
{
	var content = jQuery(element);
	content.inner = jQuery(element + ' .inner');

	content.on('transitionEnd webkitTransitionEnd transitionend oTransitionEnd msTransitionEnd', function(e)
	{
		if (content.hasClass('open'))
		{
			content.css('max-height', 9999);
		}
	});

	content.toggleClass('open closed');
    content.contentHeight = content.outerHeight();

	if (content.hasClass('closed'))
	{
        content.removeClass('transitions').css('max-height', content.contentHeight);
        setTimeout(function()
        {
            content.addClass('transitions').css(
            {
                'max-height': 0
            });
        }, 10);
    }
	else if (content.hasClass('open'))
	{
        content.contentHeight += content.inner.outerHeight();
        content.addClass('transitions').css(
		{
            'max-height': content.contentHeight
        });
    }
}