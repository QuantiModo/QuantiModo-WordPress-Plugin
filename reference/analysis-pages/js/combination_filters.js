var CombinationFilters = function() {
    var filters = {};
    var container = null;
    var initEvents = function()
    {
        // filter buttons
        jQuery('.isotope-filter-none li').click(function() {  
            // don't proceed if already selected
            if (jQuery(this).hasClass('selected')) {
                return;
            }

            var optionSet = jQuery(this).parent();
            // change selected class
            optionSet.find('.selected').removeClass('selected');
            jQuery(this).addClass('selected');
            
            var group = optionSet.attr('data-filter-group');             
            filters[ group ] = jQuery(this).children("a").attr('data-filter-value');
            // convert object into array
            var isoFilters = [];
            for (var prop in filters) {
                isoFilters.push(filters[ prop ]);
            }
            var selector = isoFilters.join('');            
            container.isotope({filter: selector});

            return false;
        });
        
        // remove selected category
        jQuery('.remove-selected-category').click(function() {  
            jQuery(".isotope-filter-drop-down li[menu-top-index='"+jQuery(this).attr('menu-group')+"']").trigger('click');
        });
                
        // filter buttons from drop down
        jQuery('.isotope-filter-drop-down li').click(function() {  
            // don't proceed if already selected 
            if (jQuery(this).hasClass('choosen')) {
                return false;
            }
            
            var choosenCategoryName = jQuery(this).attr('choosen-category-name');
            var menuGroupIndex = jQuery(this).attr('menu-group-index');
            if(choosenCategoryName == "") {
                jQuery(".choosen-category[menu-group='"+menuGroupIndex+"']").hide();
            } else {
                jQuery(".choosen-category[menu-group='"+menuGroupIndex+"'] > div").html(choosenCategoryName);
                jQuery(".choosen-category[menu-group='"+menuGroupIndex+"']").show();
            }
            
            var optionSet = jQuery(this).parent().closest("div");
            // change selected class
            optionSet.find('.choosen').removeClass('choosen');
            jQuery(this).addClass('choosen');
            
            var group = optionSet.attr('data-filter-group');  
            
            filters[ group ] = jQuery(this).children("a").attr('data-filter-value');
            // convert object into array
            var isoFilters = [];
            for (var prop in filters) {
                isoFilters.push(filters[ prop ]);
            }
            var selector = isoFilters.join('');            
            container.isotope({filter: selector});            
            return false;
        });

    };

    var initContainer = function() {
        container = jQuery('#container').isotope({
            itemSelector: '.isotope-item' /*,            
            masonry: {
                columnWidth: 80
            } */
        });
    };

    return {
        init: function()
        {              
            initContainer();
            initEvents();
        }
    };
}();

jQuery(CombinationFilters.init);


