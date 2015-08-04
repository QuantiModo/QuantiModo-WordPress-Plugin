var SearchPage = function() {

    var selectOutputAsType = 'effect';

    var correlations = [];

    var searchForCorrelations = function() {
        if (jQuery("#qms").val() == null || jQuery("#qms").val() == "") {
            return;
        }
        var timeToSearch = (new Date()).getTime();
        jQuery("#predictorName").html(jQuery("#qms").val());
        
        jQuery(".searchFormByDefault").hide();
        jQuery(".searchFormForResult").show();

        var variableName = jQuery('#qms').val();
        Quantimodo.getPublicCorrelations('public/correlations/search/' + variableName, {'effectOrCause': SearchPage.selectOutputAsType}, function(correlations) {
                SearchPage.correlations = [];
                jQuery.each(correlations, function(_, correlation) {
                    if(SearchPage.selectOutputAsType === 'effect') {
                        SearchPage.correlations.push({correlation: correlation.correlationCoefficient, variable: correlation.cause, category: correlation.causeCategory});
                    } else {
                        SearchPage.correlations.push({correlation: correlation.correlationCoefficient, variable: correlation.effect, category: correlation.effectCategory});                     
                    }
                });
                jQuery("#loadSearchResult").click();
                jQuery("#searchResultRegion").show();
                jQuery("#resultCount").html(SearchPage.correlations.length + " results");
                jQuery("#timePeriod").html("  (" + (((new Date()).getTime() - timeToSearch) / 1000) + " seconds)");
         });        
    };

    var initEvents = function()
    {
        jQuery("#askqm").on('click', function() {
            searchForCorrelations();
        });
        
        jQuery("#askqmr").on('click', function() {
            searchForCorrelations();
        });

        jQuery("input:radio[name=selectOutputAsType]").click(function() {
            SearchPage.selectOutputAsType = $(this).val();
        });
        
        jQuery( "#searchVariable" ).keypress(function( event ) {
            if ( event.which == 13 ) {
                event.preventDefault();
                searchForCorrelations();
            }            
        });
        
        jQuery( "#qms" ).keypress(function( event ) {
            if ( event.which == 13 ) {
                event.preventDefault();
                searchForCorrelations();
            }            
        });        
    };

    var initAutoComplete = function() {
        jQuery("#searchVariable").autocomplete({
            source: function(request, response) {
                jQuery("#qms").val(request.term);
                Quantimodo.getPublicVariablesWithHighestCorrelationNumber('public/variables/search/' + request.term, {'effectOrCause': SearchPage.selectOutputAsType}, function(variables) {
                    response(jQuery.map(variables, function(item) {
                        return {
                            label: item.name,
                            value: item.name
                        };
                    }));
                });
            },
            select: function(event, ui) {
                jQuery("#qms").val(ui.item.value);
                searchForCorrelations();
            },
            focus: function(event, ui) {
            }
        });

        jQuery("#qms").autocomplete({
            source: function(request, response) {
                jQuery("#qms").val(request.term);
                Quantimodo.getPublicVariablesWithHighestCorrelationNumber('public/variables/search/' + request.term, {'effectOrCause': SearchPage.selectOutputAsType}, function(variables) {
                    response(jQuery.map(variables, function(item) {
                        return {
                            label: item.name,
                            value: item.name
                        };
                    }));
                });
            },
            select: function(event, ui) {
                jQuery("#qms").val(ui.item.value);
                searchForCorrelations();
            },
            focus: function(event, ui) {
            }
        });
    };

    return {
        correlations: correlations,
        selectOutputAsType: selectOutputAsType,
        init: function()
        {
            initAutoComplete();
            initEvents();
        }
    };
}();

jQuery(SearchPage.init);

