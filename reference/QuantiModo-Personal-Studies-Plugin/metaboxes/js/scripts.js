jQuery(document).ready(function($) {
	
    function recalculate_index(repeatable){
        repeatable.find('.count').each(function(){
            var i= jQuery(this).parent().index();
            jQuery(this).html(i);
        });
    }
	// A hackish way to change the Button text to be more UX friendly
	jQuery('#media-items').bind('DOMNodeInserted',function(){
		jQuery('input[value="Insert into Post"]').each(function(){
				jQuery(this).attr('value','Use This Image');
		});
	});
	
	// the upload image button, saves the id and outputs a preview of the image
	jQuery('.meta_box_upload_image_button').click(function() { 
		formID = jQuery(this).attr('rel');
		formfield = jQuery(this).siblings('.meta_box_upload_image');
		preview = jQuery(this).siblings('.meta_box_preview_image');
		tb_show('Choose Image', 'media-upload.php?post_id='+formID+'&type=image&TB_iframe=1');
                window.original_base_send_to_editor = window.send_to_editor; 
		window.send_to_editor = function(html) {
			img = jQuery('img',html);
			imgurl = img.attr('src');
			classes = img.attr('class');
			id = classes.replace(/(.*?)wp-image-/, '');
			formfield.val(id);
			preview.attr('src', imgurl);
			tb_remove();
                        window.send_to_editor = window.original_base_send_to_editor ; 
		}
		return false;
	});
	
	// the remove image link, removes the image id from the hidden field and replaces the image preview
	jQuery('.meta_box_clear_image_button').click(function() {
		var defaultImage = jQuery(this).parent().siblings('.meta_box_default_image').text();
		jQuery(this).parent().siblings('.meta_box_upload_image').val('');
		jQuery(this).parent().siblings('.meta_box_preview_image').attr('src', defaultImage);
		return false;
	});

	// repeatable fields
	jQuery('.meta_box_repeatable_add').live('click', function(event) {
        event.preventDefault();
		// clone
        var repeatable = jQuery(this).siblings('.meta_box_repeatable');
		var row = repeatable.find('li.hide');
        var lastrow = repeatable.find('li:last-child');
		var clone = row.clone();
        clone.removeClass('hide');

		clone.find('input').val('');
        clone.find('select').val('');
		
		// increment name and id
		
        var inputname=clone.find('input').attr('rel-name');
        clone.find('input').attr('name',inputname);

        var inputname=clone.find('input[type="number"]').attr('rel-name');
        clone.find('input').attr('name',inputname);
       

        var selectname=clone.find('select').attr('rel-name');
            clone.find('select').attr('name',selectname);

            clone.find('.chosen-container').remove();
            lastrow.after(clone);
            recalculate_index(repeatable);

            jQuery('.chzn-select,.chosen').chosen({
              allow_single_deselect: true,
              disable_search_threshold: 8
            });  
        
		//
		return false;
	});
	
    jQuery('body').delegate('#qm_quiz_questions-repeatable input[type="number"]', 'change', function(){
        var total = parseInt(0);
        console.log(total);
        jQuery('#qm_quiz_questions-repeatable input[type="number"]').each(function(){
            if(!$(this).parent().hasClass('hide')){
                var ival=jQuery(this).val();
                if(ival == 'NAN' || ival ==''){
                    ival=parseInt(0);
                }
                total = parseInt(total) + parseInt(ival);
            }
        });
        jQuery('#total_quiz_marks').text(total);
    });

    jQuery('.meta_box_add_section').live('click', function(event) {
        event.preventDefault();
        var row = jQuery(this).siblings('.meta_box_repeatable').find('li.section.hide');
        var clone = row.clone();
        clone.removeClass('hide');
        clone.find('input').val('');
        var name=clone.find('input').attr('rel-name');
        clone.find('input').attr('name',name);
        row.after(clone);
    });

    jQuery('.meta_box_add_posttype1').live('click', function(event) {
        event.preventDefault();
        var row = jQuery(this).siblings('.meta_box_repeatable').find('li.posttype1.hide');
        var clone = row.clone();
        clone.removeClass('hide');
        clone.find('select').val('');
        var name=clone.find('select').attr('rel-name');
        clone.find('select').attr('name',name);
        row.after(clone);
        clone.find('.chz-select').chosen({
          allow_single_deselect: true,
          disable_search_threshold: 8});
    });

    jQuery('.meta_box_add_posttype2').live('click', function(event) {
        event.preventDefault();
        var row = jQuery(this).siblings('.meta_box_repeatable').find('li.posttype2.hide');
        var clone = row.clone();
        clone.removeClass('hide');
        clone.find('select').val('');
        var name=clone.find('select').attr('rel-name');
        clone.find('select').attr('name',name);
        row.after(clone);
        clone.find('.chz-select').chosen({
          allow_single_deselect: true,
          disable_search_threshold: 8});
    });
    

	jQuery('.meta_box_repeatable_remove').live('click', function(){
		
        var repeatable=jQuery(this).closest('.meta_box_repeatable');
        jQuery(this).closest('li').remove();
        recalculate_index(repeatable);
		return false;
	});
        
	jQuery('.meta_box_repeatable').sortable({
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.handle',
        update: function( event, ui ) {
            recalculate_index(jQuery(this));
        }
	});
        
        
        // repeatable fields
	jQuery('.meta_box_sliders_add').live('click', function() {
		// clone
		var row = jQuery(this).siblings('.meta_box_sliders').find('li:last');
		var clone = row.clone( true, true );
		clone.find('input[type="hidden"]').val('');
                clone.find('input[type="text"]').val('');
                clone.find('textarea').val('');
                var default_src=jQuery('.meta_box_default_image').html();
                if(!default_src){
                    default_src=clone.find('img').attr('rel-default');
                }
                clone.find('img').attr('src',default_src);
		row.after(clone);
		// increment name and id
		clone.find('input.meta_box_upload_image')
			.attr('name', function(index, name) {
				return name.replace(/(\d+)/, function(fullMatch, n) {
					return Number(n) + 1;
				});
			}).attr('id', function(index, name) {
				return name.replace(/(\d+)/, function(fullMatch, n) {
					return Number(n) + 1;
				});
			});
                clone.find('.slide_caption input[type="text"]')
			.attr('name', function(index, name) {
				return name.replace(/(\d+)/, function(fullMatch, n) {
					return Number(n) + 1;
				});
                             });
                clone.find('.slide_caption select')
			.attr('name', function(index, name) {
				return name.replace(/(\d+)/, function(fullMatch, n) {
					return Number(n) + 1;
				});
                             });             
                clone.find('.slide_caption textarea')
			.attr('name', function(index, name) {
				return name.replace(/(\d+)/, function(fullMatch, n) {
					return Number(n) + 1;
				});                
			});
                 clone.find('.step_fields input[type="text"]')
			.attr('name', function(index, name) {
				return name.replace(/(\d+)/, function(fullMatch, n) {
					return Number(n) + 1;
				});
                             });        
                         
		//
		return false;
	});
	
        	
        jQuery('.meta_box_clear_slider_image_button').click(function() {
		var defaultImage = jQuery(this).parent().siblings('.meta_box_preview_image').attr('rel-default');
		jQuery(this).parent().siblings('.meta_box_upload_image').val('');
		jQuery(this).parent().siblings('.meta_box_preview_image').attr('src', defaultImage);
		return false;
	});
        
	jQuery('.meta_box_sliders_remove').live('click', function(){ 
            if(jQuery(this).closest('ul').children().length > 1){
                jQuery(this).closest('li').remove();
            }else{ 
                var answer=confirm('Deleting first slide would diable featured sliders for this post. Are you sure you want to delete this slide?');
                if(answer)
                {jQuery(this).closest('li').remove();}
            }
	   return false;
	});
		
	jQuery('.meta_box_sliders').sortable({
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.handle'
	});
     
        
        jQuery('.radio-image-wrapper').click(function(){
            jQuery(this).find('input[type="radio"]').attr("checked","checked");
            
            jQuery(this).parent().find('.select').removeClass("selected");
            jQuery(this).find('.select').addClass("selected");
        });
        
        
       
        var parenttr = jQuery('#qm_select_featured').parent().parent();
         
                parenttr.siblings().eq(0).show('fast');
                parenttr.siblings().eq(1).hide('fast');
                parenttr.siblings().eq(2).hide('fast');
                parenttr.siblings().eq(3).hide('fast');
                parenttr.siblings().eq(4).hide('fast');
        
        var selectvalue = jQuery('#qm_select_featured').val();
            if(selectvalue == 'disable'){
                parenttr.siblings().eq(0).hide('fast');
                parenttr.siblings().eq(1).hide('fast');
                parenttr.siblings().eq(2).hide('fast');
                 parenttr.siblings().eq(3).hide('fast');
                  parenttr.siblings().eq(4).hide('fast');
            }  
            if(selectvalue == 'video'){
                parenttr.siblings().eq(0).hide('fast');
                parenttr.siblings().eq(1).show('fast');
                parenttr.siblings().eq(2).hide('fast');
                parenttr.siblings().eq(3).hide('fast');
                parenttr.siblings().eq(4).hide('fast');
            }
             if(selectvalue == 'iframevideo'){
                parenttr.siblings().eq(0).hide('fast');
                parenttr.siblings().eq(2).show('fast');
                parenttr.siblings().eq(1).hide('fast');
                parenttr.siblings().eq(3).hide('fast');
                parenttr.siblings().eq(4).hide('fast');
            }
            if(selectvalue == 'audio'){
                parenttr.siblings().eq(0).hide('fast');
                parenttr.siblings().eq(3).show('fast');
                parenttr.siblings().eq(1).hide('fast');
                parenttr.siblings().eq(2).hide('fast');
                parenttr.siblings().eq(4).hide('fast');
            }
            if(selectvalue == 'gallery'){
                parenttr.siblings().eq(0).show('fast');
                parenttr.siblings().eq(1).hide('fast');
                parenttr.siblings().eq(2).hide('fast');
                 parenttr.siblings().eq(3).hide('fast');
                 parenttr.siblings().eq(4).hide('fast');
            }
             if(selectvalue == 'other'){
                parenttr.siblings().eq(0).hide('fast');
                parenttr.siblings().eq(1).hide('fast');
                parenttr.siblings().eq(2).hide('fast');
                 parenttr.siblings().eq(4).show('fast');
                 parenttr.siblings().eq(3).hide('fast');
            }
            
        jQuery('#qm_select_featured').change(function(){
            var selectvalue = jQuery('#qm_select_featured').val();
            if(selectvalue == 'disable'){
                parenttr.siblings().eq(0).hide('fast');
                parenttr.siblings().eq(1).hide('fast');
                parenttr.siblings().eq(2).hide('fast');
                 parenttr.siblings().eq(3).hide('fast');
                  parenttr.siblings().eq(4).hide('fast');
            }  
            if(selectvalue == 'video'){
                parenttr.siblings().eq(0).hide('fast');
                parenttr.siblings().eq(1).show('fast');
                parenttr.siblings().eq(2).hide('fast');
                parenttr.siblings().eq(3).hide('fast');
                parenttr.siblings().eq(4).hide('fast');
            }
             if(selectvalue == 'iframevideo'){
                parenttr.siblings().eq(0).hide('fast');
                parenttr.siblings().eq(2).show('fast');
                parenttr.siblings().eq(1).hide('fast');
                parenttr.siblings().eq(3).hide('fast');
                parenttr.siblings().eq(4).hide('fast');
            }
            if(selectvalue == 'audio'){
                parenttr.siblings().eq(0).hide('fast');
                parenttr.siblings().eq(3).show('fast');
                parenttr.siblings().eq(1).hide('fast');
                parenttr.siblings().eq(2).hide('fast');
                parenttr.siblings().eq(4).hide('fast');
            }
            if(selectvalue == 'gallery'){
                parenttr.siblings().eq(0).show('fast');
                parenttr.siblings().eq(1).hide('fast');
                parenttr.siblings().eq(2).hide('fast');
                 parenttr.siblings().eq(3).hide('fast');
                 parenttr.siblings().eq(4).hide('fast');
            }
             if(selectvalue == 'other'){
                parenttr.siblings().eq(0).hide('fast');
                parenttr.siblings().eq(1).hide('fast');
                parenttr.siblings().eq(2).hide('fast');
                 parenttr.siblings().eq(4).show('fast');
                 parenttr.siblings().eq(3).hide('fast');
            }
        });
        
        var slidertype=jQuery('.select_slider_type').val();
            if(slidertype == 'qmcom' || slidertype == 'qmcomthumbnail' || slidertype == 'stepqmcom'){ 
                jQuery('.plus_more').show('fast');}
            else{
                jQuery('.plus_more').hide('fast');
            }
            
            if(slidertype == 'stepqmcom' || slidertype == 'stepsimple'){ 
                jQuery('.step_more').show('fast'); jQuery('.stepinfo h4').show('fast');}
            else{
                jQuery('.step_more').hide('fast'); jQuery('.stepinfo h4').hide('fast');
            }
        jQuery('.select_slider_type').change(function(){
            var slidertype=jQuery(this).val();
            if(slidertype == 'qmcom' || slidertype == 'qmcomthumbnail' || slidertype == 'stepqmcom'){ 
                jQuery('.plus_more').show('fast');}
            else{
                jQuery('.plus_more').hide('fast');
            }
            
            if(slidertype == 'stepqmcom' || slidertype == 'stepsimple'){ 
                jQuery('.step_more').show('fast'); jQuery('.stepinfo h4').show('fast');}
            else{
                jQuery('.step_more').hide('fast'); jQuery('.stepinfo h4').hide('fast');
            }
        });
        jQuery('.plus_more').click(function(){ 
           jQuery(this).next().next().slideToggle('fast');
        });
        jQuery('.step_more').click(function(){ 
           jQuery(this).next().slideToggle('fast');
        });
        
        jQuery('.more_settings').click(function(){ console.log('clcok');
           jQuery(this).parent().next().fadeToggle('fast');
        });
	
});

jQuery(document).ready(function(){
             var builder_enable=jQuery('.builder_enable').find('#builder_enable');
             
                if(builder_enable.is(':checked')){ 
                    jQuery('.builder_enable').addClass('_enable');
                }
                
                 jQuery('.builder_enable').click(function(){ 
                     
                    var checkbox = jQuery(this).find('input');
                    
                        if(jQuery(this).hasClass('_enable')){
                            
                            jQuery(this).removeClass('_enable');
                            checkbox.removeAttr('checked');
                        }else{
                            checkbox.attr('checked','checked');   
                            jQuery(this).addClass('_enable');
                        }
                 });
                 
                 
             jQuery('.checkbox_val').each(function(){
                     if(jQuery(this).is(':checked')){ 
                    jQuery(this).parent().find('.checkbox_button').addClass('enable');
                    }
                 });
             
                
                
                 jQuery('.checkbox_button').click(function(){ 
                     
                    var checkbox = jQuery(this).parent().find('input');
                    
                        if(jQuery(this).hasClass('enable')){
                            jQuery(this).removeClass('enable');
                            checkbox.removeAttr('checked');
                        }else{
                            checkbox.attr('checked','checked');   
                            jQuery(this).addClass('enable');
                        }
                 });
                 
                 jQuery('.select_val').each(function(){
                     if(jQuery(this).val() == 'S'){ 
                    jQuery(this).parent().find('.select_button').addClass('enable');
                    }else{
                        jQuery(this).parent().find('.select_button').removeClass('enable');
                    }
                 });
             
                
                
                 jQuery('.select_button').click(function(){ 
                     
                    var select = jQuery(this).parent().find('select.select_val');
                        if(jQuery(this).hasClass('enable')){
                            jQuery(this).removeClass('enable');
                            select.val('H');
                        }else{
                            select.val('S');
                            jQuery(this).addClass('enable');
                        }
                 });
                 jQuery('.color').iris({palettes: ['#125', '#459', '#78b', '#ab0', '#de3', '#f0f']});
                 jQuery('.color').click(function(){
                    jQuery(this).iris('toggle');
                 });
                 jQuery('.chzn-select').each(function(){
                    jQuery(this).chosen({
                        allow_single_deselect: true,
                        disable_search_threshold: 8
                    });
                 });

                 jQuery( ".date-picker-field" ).datepicker({
                    dateFormat: "yy-mm-dd",
                    numberOfMonths: 1,
                    showButtonPanel: true,
                });
                 jQuery( ".timepicker" ).each(function(){
                 jQuery(this).timePicker({
                      show24Hours: false,
                      separator:':',
                      step: 15
                  });
                }) 
    });

