(function($){
   
    $(function() {
        var builder_width = $('#layout').width(),
            $builder_add_links = $( '#qm_editor_controls a.add_element' ),
            $main_save_button = $( '#v_main_save' ),
            module_settings_clicked = false,
            hidden_editor_object = tinyMCEPreInit.mceInit['v_hidden_editor'],
            page_builder_original_width = 742,
            main_module_width = 0;
        
                
               
                         
        $( 'body' ).delegate( 'span.settings_arrow', 'click', function(){
            var $this_setting_link = $(this),
                $settings_window = $('#active_module_settings'),
                $active_module = $this_setting_link.closest('.module');
                
                                
            if ( module_settings_clicked ) return false;
            else module_settings_clicked = true;
            
            $('#layout .module').css( 'z-index', '1' );
            
            if ( $('#modules').is(':hidden') ) $builder_add_links.eq(0).trigger('click');
            
            $.ajax({
                type: "POST",
                url: v_options.ajaxurl,
                data:
                {
                    action : 'show_module_options',
                    load_nonce : v_options.load_nonce,
                    module_class : $(this).closest('.module').attr('class'),
                    modal_window : 0,
                    module_exact_name : $(this).closest('.module').attr('data-placeholder')
                },
                error: function( xhr, ajaxOptions, thrownError ){
                    module_settings_clicked = false;
                    console.log('error occurred' +ajaxOptions);
                },
                success: function( data ){
                    $main_save_button.hide();
                                        
                                        
                                        
                    $active_module.addClass('active');
                    $settings_window.hide().append(data).slideDown();
                    $settings_window.find('.html-active').removeClass('html-active').addClass('tmce-active');
                    $('#module_separator').show();
                    
                    $('#layout .module:not(.active,.m_column)').css('opacity',0.5);
                    $('html:not(:animated),body:not(:animated)').animate({ scrollTop: $('#page_builder').offset().top - 82 }, 500);
                    
                                        $active_module.find( 'span.module_name').append('<span class="unsaved"> ( Unsaved )</span>');
                    
                                        deactivate_ui_actions();
                    module_settings_clicked = false;
                    
                    $( '#module_settings .v_option' ).each( function(){
                        var $this_option = $(this),
                            this_option_id = $this_option.attr('id'),
                            $found_element = $active_module.find('.module_settings .module_setting.' + this_option_id);
                        
                        if ( $found_element.length ){
                            if ( $this_option.is('select') ){
                                if($this_option.attr('multiple') == 'multiple'){
                                                                    var myString =$found_element.html();
                                                                    var myArray = myString.split(',');
                                                                    // display the result in myDiv
                                                                    for(var i=0;i<myArray.length;i++){
                                                                        $this_option.find("option[value='" + myArray[i] + "']").attr('selected','selected');
                                                                        }
                                                                }else
                                                                $this_option.find("option[value='" + $found_element.html() + "']").attr('selected','selected');
                                                                
                            } else if ( $this_option.is('input') ){
                                $this_option.val( $found_element.html() );
                                                        }else if ( $this_option.is('textarea') ){ 
                                $this_option.val( $found_element.html() );
                            } else { 
                                $this_option.html( $found_element.html() );
                            }
                        }
                        
                        if ( $this_option.hasClass('v_wp_editor') ) {
                            tinyMCE.execCommand( "mceAddControl", true, this_option_id );
                            quicktags( { id : this_option_id } );
                            init_new_editor( this_option_id );
                        }
                        if($(this).hasClass('image_value')){
                                                    var value=$(this).val();
                                                    $(this).parent().find('.radio_images').each(function(){
                                                        if($(this).attr('data-value') == value){
                                                            $(this).addClass('clicked');
                                                            $(this).append('<span></span>');
                                                        }
                                                    });
                                                }
                                                
                                                
                        init_sortable_attachments();
                    } );
                    
            
                         jQuery(".chzn-select").chosen();   
                         
                        jQuery('.csstext').each(function(){
                            var $string=jQuery(this).val();
                            //str.indexOf("Yes") >= 0
                            var buttons=jQuery(this).parent().find('.addclasstocsstext');
                            buttons.each(function(){
                                if($string.indexOf(jQuery(this).attr('data-class')) >= 0){
                                    jQuery(this).addClass('fadeoutbutton');
                                };
                            });
                        });               
                        
                        jQuery('.select_yesno_val').each(function(){
                            var yesno_val=jQuery(this).val();
                            var button =jQuery(this).parent().find('.select_yesno_button');
                        if(yesno_val !=0){
                            button.addClass('enable');
                        }else{
                            button.removeClass('enable');
                        }
                        });
                        jQuery('.divider').each(function(){
                            var hider=parseInt(jQuery(this).attr('rel-hide'));
                            var parent=jQuery(this).parent();
                            var next = parent;
                            for(var i=0;i<hider;i++){
                                next=next.next();
                                next.hide();
                            }
                            jQuery(this).addClass('closed');
                        });
                        
                        jQuery('.divider').parent().click(function(){
                            var hider=parseInt(jQuery(this).find('.divider').attr('rel-hide'));
                            var next = jQuery(this);
                            if(jQuery(this).find('.divider').hasClass('closed')){
                                for(var i=0;i<hider;i++){
                                next=next.next();
                                next.show();
                                }
                                jQuery(this).find('.divider').removeClass('closed');
                                jQuery(this).find('.toggle').removeClass('closed');
                            }else{
                                for(var i=0;i<hider;i++){
                                next=next.next();
                                next.hide();
                                }
                                jQuery(this).find('.divider').addClass('closed');
                                jQuery(this).find('.toggle').addClass('closed');
                            }
                            
                            
                        });
                        
                        jQuery('.uploaded_image').each(function(){
                           var src=jQuery(this).parent().find('.v_upload_field').val();
                           if(src){
                               jQuery(this).parent().find('.remove_uploaded').fadeIn();
                           }
                           jQuery(this).attr('src',src);
                        });
                        jQuery('.select_yesno_button').click(function(){
                           var select = jQuery(this).parent().find('select.select_yesno_val');
                           select.trigger('change');
                        if(jQuery(this).hasClass('enable')){
                            jQuery(this).removeClass('enable');
                            select.val(0);
                        }else{
                            select.val(1);
                            jQuery(this).addClass('enable');
                        } 
                        });
                        
                        jQuery('#masonry').each(function(){
                            var value= parseInt(jQuery(this).val());
                            var parent =  jQuery(this).parent();
                            
                            if(value == 1){
                                parent.next().hide();
                                parent.next().next().show();
                                parent.next().next().next().show();
                            }else{
                                parent.next().show();
                                parent.next().next().hide();
                                parent.next().next().next().hide();
                            }
                        });
                        
                        jQuery('#masonry').change(function(){
                            var value= parseInt(jQuery(this).val())
                            var parent =  jQuery(this).parent();
                            
                            if(value == 0){
                                parent.next().hide();
                                parent.next().next().show();
                                parent.next().next().next().show();
                            }else{
                                parent.next().show();
                                parent.next().next().hide();
                                parent.next().next().next().hide();
                            }
                        });
                            
                        jQuery('.addclasstocsstext').click(function(event){ 
                                event.preventDefault();
                                 if(jQuery(this).hasClass('fadeoutbutton')){ 
                                 var val= '';
                                 val= val+jQuery(this).parent().find('.csstext').val();
                                 var dataclass=jQuery(this).attr('data-class');
                                 val=val.replace('  ', ' ');
                                 if(val.indexOf(dataclass) >= 0){
                                     val=val.replace(dataclass, '');
                                    jQuery(this).removeClass('fadeoutbutton');
                                    jQuery(this).parent().find('.csstext').val(val);
                                };
                                    
                             }else{
                                    var $id=jQuery(this).attr('data-class');
                                    jQuery(this).addClass('fadeoutbutton');
                                    var val= '';
                                    val= val+jQuery(this).parent().find('.csstext').val();
                                    val= val+' '+$id;
                                    jQuery(this).parent().find('.csstext').val(val);
                                 }
                                });
                                
                }
            });
   
        } );
           
               $( 'body' ).delegate( '.radio_images', 'click', function(){
                    var value = $(this).attr('data-value');
                    $(this).parent().find('.clicked').removeClass('clicked');
                    $(this).parent().find('.image_value').val(value); 
                    $(this).addClass('clicked');
                    $(this).append('<span></span>');
               });
               $( 'body' ).delegate( '#new_sample_layout', 'click', function(){
                   $('.overlay').fadeIn(200);
                   $('.overlay').find('h3.success').remove();
                   
               });
               $( 'body' ).delegate( '#save_new_sample_layout', 'click', function(){
                   var layout_name= $('#new_sample_layout_name').val();
                   var postid= $('#new_sample_layout_name').attr('data-id');
                   
                   if(layout_name.length >0){
                       $.ajax({
                type: "POST",
                url: v_options.ajaxurl,
                data:
                {
                    action : 'save_new_layout',
                    load_nonce : v_options.load_nonce,
                    name : layout_name,
                                        id: postid
                },
                                error: function( xhr, ajaxOptions, thrownError ){
                    console.log('error occurred' +ajaxOptions);
                },
                success: function( data ){
                                    $('.overlay').append('<h3 class="success"> Layout Saved </h3>');
                                    setTimeout(function(){$('.overlay').fadeOut(200);},1000);
                                }
                   });
                   }else{
                       $('#new_sample_layout_name').after('<span class="error">Enter a name.</span>');
                        setTimeout(function(){$('span.error').hide(200);},2000);
                   }
                   
               });
               
               $( 'body' ).delegate( '.remove', 'click', function(){
                   $(this).parent().fadeOut(200);
                   
               });
        $( 'body' ).delegate( 'span.delete, span.delete_column', 'click', function(){
            var $this_delete_button = $(this);
            
            if ( $this_delete_button.hasClass('delete') ){
                if ( $this_delete_button.find('.v_delete_confirmation').length ){ 
                    $this_delete_button.find('.v_delete_confirmation').remove();
                } else { 
                    $this_delete_button.append( '<span class="v_delete_confirmation">' + '<span>' + v_options.confirm_message + '</span>' + '<a href="#" class="v_delete_confirm_yes">' + v_options.confirm_message_yes + '</a><a href="#" class="v_delete_confirm_no">' + v_options.confirm_message_no + '</a></span>' );
                }
                return false;
            }
            
            v_delete_module( $this_delete_button.closest('.module') );
        } );
        
        $( 'body' ).delegate( '.v_delete_confirm_yes', 'click', function(){
            v_delete_module( $(this).closest('.module') );
            return false;
        } );
        
        $( 'body' ).delegate( '#close_dialog_settings', 'click', function(){
            var $dialog_form = $('form#dialog_settings');
            
            $dialog_form.find('.v_wp_editor').each( function(){
                tinyMCE.execCommand("mceRemoveControl", false, $(this).attr('id'));
            } );
            
            close_modal_window();
            
            return false;
        });
        
        $( 'body' ).delegate( 'form#module_settings input#submit, #close_module_settings', 'click', function(){
            var $active_module_settings = $('.active .module_settings');
            
            $active_module_settings.empty();
            $main_save_button.show();
            
            $('form#module_settings .v_option').each( function(){
                var option_value, option_class,
                    this_option_id = $(this).attr('id');
                
                option_class = this_option_id + ' module_setting';
                
                               
                                        
                 if ( $(this).hasClass('v_wp_editor') ){
                    option_value = $(this).is(':hidden') ? tinyMCE.get( this_option_id ).getContent() : switchEditors.wpautop( tinymce.DOM.get( this_option_id ).value );
                    tinyMCE.execCommand("mceRemoveControl", false, this_option_id);
                }
                else if ( $(this).is('select, input,textarea') ) {
                    option_value = $(this).val();
                                        
                }else if ( $(this).is('.slides') ){
                    $(this).find('input, textarea').each(function(){
                        var this_value = $(this).val();
                        
                        if ( $(this).is('input') ) $(this).attr('value', this_value);
                        else $(this).html( this_value );
                    });
                    option_value = $(this).html();
                }
                
                if ( $(this).hasClass('v_module_content') ) option_class += ' v_module_content';
                
                $active_module_settings.append( '<div data-option_name="' + this_option_id + '" class="' + option_class + '">' + option_value + '</div>' );
            } );
            
            $( '#layout .module' ).removeClass('active').css('opacity',1);
            
            $(this).closest('#active_module_settings').slideUp().find('form#module_settings').remove();
            $('#module_separator').hide();
            
            $('#layout').css( 'height', 'auto' );
            
            reactivate_ui_actions();
            
            $('#v_main_save').trigger('click');
            
                        $('#layout_container').find('.module').each(function(){
                            if($(this).hasClass('m_column')){
                                
                            }else{
                                var name =$(this).find('.module_settings .title').text();
                                
                            if(name){
                                name=name.substring(0,15); 
                                $(this).find('.module_name').text(name);
                            }else{
                                name=$(this).find('.module_settings .v_module_content').text();
                                if(name){
                                    if(name.substring(0,1) == '['){
                                       word=name.substring(1, name.indexOf(' '));
                                       if(word.length<6){
                                           name=name.substring(1,16)+'...'; 
                                       }else{
                                           name=word;
                                       }
                                    }else{
                                        word=name.substring(0, name.indexOf(' '));
                                       if(word.length<6){
                                           name=name.substring(0,15)+'...'; 
                                       }else{
                                           name=word;
                                       }
                                    }
                                    
                                    $(this).find('.module_name').text(name);
                                }else{ 
                                            name=$(this).find('.module_settings .id').text();
                                            if(name){
                                                 name='LayerSlider : '+name;
                                                  $(this).find('.module_name').text(name);
                                            }else{
                                                name=$(this).find('.module_settings .alias').text();
                                                if(name){
                                                    name='Revolution Slider : '+name;
                                                    $(this).find('.module_name').text(name);
                                                 }else{
                                                
                                                name=$(this).find('.module_settings .area').text();
                                                if(name){
                                                 name='Sidebar: '+name.substring(0,15);
                                                $(this).find('.module_name').text(name);
                                                }
                                            }
                                         
                                        }
                                
                                    }
                            }
                           }
                            
                        });
                        
            return false;
        } );
        
        $( 'body' ).delegate( 'form#dialog_settings input#submit', 'click', function(){
            var $dialog_form = $('form#dialog_settings'),
                current_module_name = 'v_' + $dialog_form.find('input#saved_module_name').val(),
                shortcode_text, shortcode_content = '',
                advanced_option = false,
                editor_id = $dialog_form.find('input#paste_to_editor_id').val();
            
            shortcode_text = '[' + current_module_name;
            
            $dialog_form.find('.v_option').each( function(){
                var option_value,
                    this_option_id = $(this).attr('id'),
                    shortcode_option_id = this_option_id.replace('dialog_','');
                
                     if ( $(this).hasClass('v_wp_editor') ){
                        option_value = $(this).is(':hidden') ? tinyMCE.get( this_option_id ).getContent() : switchEditors.wpautop( tinymce.DOM.get( this_option_id ).value );
                        tinyMCE.execCommand("mceRemoveControl", false, this_option_id);
                    }
                    else if ( $(this).is(':checkbox') ){
                        option_value = ( $(this).is(':checked') ) ? 1 : 0;
                    }
                    else if ( $(this).is('select, input') ) {
                        option_value = $(this).val();
                    }
                    
                    if ( $(this).hasClass('v_module_content') ) {
                        shortcode_content = option_value;
                    } else {
                        shortcode_text += ' ' + shortcode_option_id + '="' + option_value + '"';
                    }
                    
            } );
            
            if ( ! advanced_option ) shortcode_text += ']' + shortcode_content + '[/' + current_module_name + ']';
            else shortcode_text += '[/' + current_module_name + ']';
            
            switchEditors.go(editor_id,'tmce');
            tinyMCE.getInstanceById( editor_id ).execCommand("mceInsertContent", false, shortcode_text);
            
            close_modal_window();
            
            return false;
        } );
        
        $( 'body' ).delegate( 'a.delete_attachment', 'click', function(){
            $(this).closest('.attachment').remove();
            return false;
        } );
        
        $builder_add_links.click( function(){
            var $clicked_link = $(this),
                $modules_container = $('#modules'),
                open_modules_window = false;
            
            if ( $clicked_link.hasClass('active') ) return false;
            
            $modules_container.find('.module').css( { 'opacity' : 0, 'display' : 'none' } );
            
            if ( $clicked_link.hasClass('add_module') )
                $modules_container.find('.module:not(.m_column, .sample_layout)').css({'display':'inline-block', 'opacity' : 0}).animate( { 'opacity' : 1 }, 500 );
            else if ( $clicked_link.hasClass('add_sample_layout') )
                $modules_container.find('.module.sample_layout').css({'display':'inline-block', 'opacity' : 0}).animate( { 'opacity' : 1 }, 500 );
            else
                $modules_container.find('.module.m_column').css({'display':'inline-block', 'opacity' : 0}).animate( { 'opacity' : 1 }, 500 );
                
            if ( $modules_container.is(':hidden') || open_modules_window ) {
                $modules_container.slideDown(700);
            }
                
            $builder_add_links.removeClass('active');
            $clicked_link.addClass('active');
            
            return false;
        } );
        
        (function integrate_media_uploader(){
            var fileInput = false,
                change_image = false,
                upload_field = false,
                $upload_field_input = null,
                upload_field_name = '',
                tb_interval;
                
            $( 'body' ).delegate( 'a#v_add_slider_images', 'click', function(){
                fileInput = true;
                
                tb_interval = setInterval( function() { 
                    $('#TB_iframeContent').contents().find('.savesend .button').val('Insert Into Slider');
                }, 2000 );
                
                tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');
                return false;
            });
            
            $( 'body' ).delegate( 'a.change_attachment_image', 'click', function(){
                fileInput = true;
                change_image = true;
                
                $(this).closest('.attachment').addClass('active');
                
                tb_interval = setInterval( function() { 
                    $('#TB_iframeContent').contents().find('.savesend .button').val('Use This Image');
                }, 2000 );
                
                tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');
                return false;
            });
            
            $( 'body' ).delegate( 'a.v_upload_button', 'click', function(){
                fileInput = true;
                upload_field = true;
                
                $upload_field_input = $(this).siblings('.v_upload_field');
                $uploaded_image = $(this).siblings('.uploaded_image');
                                $uploaded_cancel = $(this).siblings('.remove_uploaded');
                tb_interval = setInterval( function() { 
                    $('#TB_iframeContent').contents().find('.savesend .button').val('Use This Image');
                }, 2000 );
                
                tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');
                return false;
            });
            
            window.original_send_to_editor = window.send_to_editor;
            window.send_to_editor = function(html){
                var attachment_class;
                
                if ( fileInput ) {
                    clearInterval(tb_interval);
                    attachment_class = $( 'img', html ).attr('class');
                    change_image = ( change_image ) ? 1 : 0;
                    data_type = ( change_image ) ? 'json' : 'html';
                    
                                        
                    tb_remove();
                    init_sortable_attachments();
                    
                    $.ajax({
                        type: "POST",
                        url: v_options.ajaxurl,
                        dataType: data_type,
                        data:
                        {
                            action : 'add_slider_item',
                            load_nonce : v_options.load_nonce,
                            attachment_class : attachment_class,
                            change_image : change_image
                        },
                        success: function( data ){
                            if ( change_image ) {
                                var $active_attachment = $('.attachment.active').removeClass('active');
                                    
                                attachment_settings = data;
                                console.log(data);
                                $active_attachment.attr( 'data-attachment', attachment_settings['attachment_id'] ).find('img').remove();
                                $active_attachment.prepend( attachment_settings['attachment_image'] );
                                
                                change_image = false;
                            }
                            else if ( upload_field ){
                                $upload_field_input.val( $(html).find('img').attr('src') );
                                $uploaded_image.attr('src', $(html).find('img').attr('src')); 
                                upload_field = false;
                                $uploaded_cancel.fadeIn();
                            }
                            else {
                                $('.slides:visible').append( data );
                            }
                        }
                    });
                    
                    fileInput = false;
                } else {
                    window.original_send_to_editor( html );
                }
            }
        })();
        
        
        
        $('#v_main_save').click(function(){
            layout_save( true );
            $('#new_sample_layout').fadeIn(100);
            return false;
        });
                
        $( 'body' ).delegate( '.remove_uploaded', 'click', function(){
            var reldefault = $(this).attr('rel-default'); 
            $(this).parent().find('.uploaded_image').attr('src',reldefault);
            $(this).parent().find('.v_upload_field').val('');
            $(this).fadeOut();
            return false;
        });
             
        
        $( 'body' ).delegate( '.module', 'click', function(){
            $('#new_sample_layout').fadeOut(100);
        });
        $('#publish').click(function(){ 
            layout_save( true );
            return true;
        });
                
        function layout_save( show_save_message ){
            var layout_html = $('#layout').html(),
                layout_shortcode = v_generate_layout_shortcode( $('#layout') ),
                $save_message = jQuery("#v_ajax_save");
            $.ajax({
                type: "POST",
                url: v_options.ajaxurl,
                data:
                {
                    action : 'save_layout',
                    load_nonce : v_options.load_nonce,
                    layout_html : layout_html,
                    layout_shortcode : layout_shortcode,
                    post_id : $('input#post_ID').val()
                },
                beforeSend: function ( xhr ){
                    if ( show_save_message ){
                        $save_message.children("img").css("display","block");
                        $save_message.children("span").css("margin","6px 0px 0px 40px").html( v_options.saving_text );
                        $save_message.fadeIn('fast');
                    }
                },
                success: function( data ){
                    $save_message.children("img").css("display","none");
                    $save_message.children("span").css("margin","0px").html( v_options.saved_text );
                    
                    setTimeout(function(){
                        $save_message.fadeOut("slow");
                    },500);
                }
            });
        }
        
        //make sure the hidden WordPress Editor is in Visual mode
        //switchEditors.go('v_hidden_editor','tmce');
        
        (function init_ui(){
            $( '#layout' ).droppable({
                accept: ":not(.ui-sortable-helper)",
                greedy: true,
                drop: function( event, ui ) {
                    if ( ui.draggable.hasClass('sample_layout') ){
                        v_append_sample_layout( ui.draggable );
                        return;
                    }
                    ui.draggable.clone().appendTo( this );
                    init_modules_js();
                }
            }).sortable({
                forcePlaceholderSize: true,
                placeholder: 'module_placeholder',
                cursor: 'move',
                distance: 2,
                start: function(event, ui) {
                    ui.placeholder.text( ui.item.attr('data-placeholder') );
                    ui.placeholder.css( 'width', ui.item.width() );
                },
                update: function(event, ui){
                    init_modules_js();
                },
                stop: function(event, ui) {
                    layout_save( false );
                }
            });
            
            $( '#modules .module' ).draggable({
                revert: 'invalid',
                zIndex: 100,
                distance: 2,
                cursor: 'move',
                helper: 'clone'
            });
        })();
        
        $( '#layout .module .ui-resizable-handle' ).remove();
        init_modules_js();
        
        // resizable and sortable init
        function init_modules_js(){
            var $helper_text = $('#v_helper');
            
            // remove 'resizable' handler from 'full width' modules
            $( '#layout > .module.full_width .move' ).remove();
            
            $( '#layout > .m_column' ).each( function(){
                $(this).removeClass('m_column_no_modules');
                if ( ! $(this).find('.module').length ) $(this).addClass('m_column_no_modules');
            } );
            
            $( '#layout > .module:not(.full_width)' ).resizable({
                handles: 'e',
                containment: 'parent',
                start: function(event, ui) {
                    ui.helper.css({position: ""}); // firefox fix
                    
                    ui.helper.css({
                        position: "relative !important",
                        top: "0 !important",
                        left: "0 !important"
                    });
                },
                stop: function(event, ui) {        
                    ui.helper.css({
                        position: "",
                        top: "",
                        left: ""
                    });
                    calculate_modules();
                },
                resize: function(event, ui) {
                    var module_width = ui.helper.hasClass('m_column_resizable') ? ( ui.size.width+26 ) : (ui.size.width+2),
                        new_width = Math.floor( ( module_width / builder_width ) * 100 ),
                        $module_width = ui.helper.find('> span.module_name > span.module_width');
                    
                    ui.helper.css({
                        top: "",
                        left: ""
                    });
                    
                    if ( new_width >= 100 ) new_width = '';
                    else new_width = ' (' + new_width + '%)';
                    
                    if ( $module_width.length ){
                        $module_width.html( new_width );
                    } else {
                        ui.helper.find('> span.module_name').append('<span class="module_width">' + new_width + '</span>')
                    }
                    
                    if ( ui.helper.hasClass('m_column_resizable') ) ui.helper.css('height','auto');
                }
            });
            
            $( '#layout .m_column' ).droppable({
                accept: ".module:not(.m_column,.full_width,.sample_layout)",
                hoverClass: 'column_active',
                greedy: true,
                drop: function( event, ui ) {
                    // return if we're moving modules inside the column
                    if ( ui.draggable.parents('.m_column').length && $(this).find('.ui-sortable-helper').length ) return;
                    
                    ui.draggable.clone().appendTo( this ).css( { 'width' : '100%', 'marginRight' : '0' } ).find('span.module_width').remove();
                    
                    if ( ui.draggable.parents('#layout').length ){
                        ui.draggable.remove();
                    }
                    
                    init_modules_js();
                }
            }).sortable({
                forcePlaceholderSize: true,
                cancel: 'span.column_name',
                placeholder: 'module_placeholder',
                cursor: 'move',
                distance: 2,
                connectWith: '#layout',
                zIndex: 10,
                start: function(event, ui) {
                    ui.placeholder.text( ui.item.attr('data-placeholder') );
                    ui.placeholder.css( 'width', ui.item.width() );
                    ui.item.closest('.m_column').css( 'z-index', '10' );
                },
                stop: function(event, ui) {
                    $( '#layout .m_column' ).css( 'z-index', '1' );
                    
                    layout_save( false );
                }
            });
            
            if ( $( '#layout > .module' ).length ) $helper_text.hide();
            else $helper_text.show();
            //Changed
            // columns and modules within columns can't be resized
            //$( '#layout .m_column:not(.m_column_resizable)' ).resizable( "destroy" );
            //$( '#layout .m_column .module' ).resizable( "destroy" ).find('.ui-resizable-handle' ).remove();
            $( '#layout .m_column > span.move' ).remove();
            
            $( '#layout .module' ).css( { 'position' : '', 'top' : '', 'left' : '', 'height' : 'auto !important', 'z-index' : '1' } ).removeClass('ui-sortable-helper').removeClass('column_active');
            
            calculate_modules();
        }
        
        function calculate_modules(){
            var row_width = 0;
            
            $( '#layout > .module' ).each( function(){
                var $module_width_span = $(this).find('> span.module_name > span.module_width'),
                    modifier = $(this).hasClass('m_column_resizable') ? 26 : 2;
                
                if ( ! $(this).hasClass('m_column') || $(this).hasClass('m_column_resizable') ){
                    if ( $module_width_span.length && $module_width_span.text() !== '' ) $(this).css( 'width', builder_width * parseInt( $module_width_span.text().substring(2) ) / 100 - modifier );
                    else {
                        if ( $(this).hasClass('m_column_resizable') ) $(this).css( 'width', main_module_width - modifier );
                        else $(this).css( 'width', main_module_width );
                    }
                }
            } );
            
            $( '#layout > .module' ).removeClass('first').each( function(index){
                if ( index === 0 || row_width === 0 ) $(this).addClass('first');
                
                row_width += $(this).outerWidth(true);
                
                if ( row_width === builder_width ){
                    $(this).next('.module').addClass('first');
                    row_width = 0;
                } else if ( row_width > builder_width ){
                    $(this).addClass('first');
                    row_width = $(this).outerWidth(true);
                }
            } );
            
            $( '#layout > .module.first' ).each( function(){
                var modifier = $(this).hasClass('m_column_resizable') ? 26 : 2,
                    module_width = $(this).width(),
                    $module_width_span = $(this).find('> span.module_name > span.module_width');
                
                if ( $module_width_span.length && $module_width_span.text() !== '' ) {
                    $module_width_span.text( ' (' + Math.round( ( ( module_width + modifier ) / builder_width ) * 100 ) + '%)' );
                }
            } );    
        }
        
        function v_append_sample_layout( $layout_module ){
            $.ajax({
                type: "POST",
                url: v_options.ajaxurl,
                data:
                {
                    action : 'append_layout',
                    load_nonce : v_options.load_nonce,
                    layout_name : $layout_module.attr('data-name')
                },
                success: function( data ){
                    $('#layout').append( data );
                    init_modules_js();
                }
            });
        }
        
        function deactivate_ui_actions(){
            $( '#layout' ).droppable( "disable" ).sortable( "disable" );
            $( '#layout > .module' ).resizable( "disable" );
            $( '#layout .m_column' ).droppable( "disable" ).sortable( "disable" );
            
            $( '#layout > .module span.move, #layout > .module span.delete' ).css( 'display', 'none' );
            
            make_editor_droppable();
        }
        
        function reactivate_ui_actions(){
            $( '#layout' ).droppable( "enable" ).sortable( "enable" );
            $( '#layout > .module' ).resizable( "enable" );
            $( '#layout .m_column' ).droppable( "enable" ).sortable( "enable" );
            
            $( '#layout > .module span.move, #layout > .module span.delete' ).css( 'display', 'block' );
        }
        
        function make_editor_droppable(){
            $( '.wp-editor-container' ).droppable({
                accept: ".module",
                hoverClass: 'editor_hover',
                greedy: true,
                drop: function( event, ui ) {
                    var paste_to_editor_id = $(this).find('.v_wp_editor').attr('id'),
                        action = 'show_module_options';
                    
                    // don't allow inserting module into the same module 
                    if ( $('#layout .active').attr('data-placeholder') == ui.draggable.attr('data-placeholder') ) return;
                    if ( ui.draggable.hasClass('sample_layout') ) return;
                    
                    if ( ui.draggable.hasClass('m_column') ) action = 'show_column_options';
                    
                    $.ajax({
                        type: "POST",
                        url: v_options.ajaxurl,
                        data:
                        {
                            action : action,
                            load_nonce : v_options.load_nonce,
                            module_class : ui.draggable.attr('class'),
                            modal_window : 1,
                            paste_to_editor_id : paste_to_editor_id,
                            module_exact_name : ui.draggable.attr('data-placeholder')
                        },
                        success: function( data ){
                            $('body').append( '<div id="dialog_modal">' + '<div class="dialog_handle">Insert Shortcode</div>' + data + '</div> <div class="modal_blocker"></div>' );
                            
                            $('#dialog_modal').draggable( { 'handle' : 'div.dialog_handle' } );
                            
                            $( '#dialog_settings .v_option' ).each( function(){
                                var $this_option = $(this),
                                    this_option_id = $this_option.attr('id');
                                
                                if ( $this_option.hasClass('v_wp_editor') ) {
                                    tinyMCE.execCommand( "mceAddControl", true, this_option_id );
                                    quicktags( { id : this_option_id } );
                                    init_new_editor( this_option_id );
                                }
                            } );
                            
                            $('html:not(:animated),body:not(:animated)').animate({ scrollTop: 0 }, 500);
                        }
                    });
                }
            });
        }
        
        function close_modal_window(){
            $( 'div#dialog_modal, div.modal_blocker' ).remove();
            $('html:not(:animated),body:not(:animated)').animate({ scrollTop: $('#page_builder').offset().top - 82 }, 500);
        }
        
        function init_sortable_attachments(){
            $('.slides').sortable({
                forcePlaceholderSize: true,
                cursor: 'move',
                distance: 2,
                zIndex: 10
            });
        }
        
        function init_sortable_tabs(){
            $('.slides').sortable({
                forcePlaceholderSize: true,
                cursor: 'move',
                distance: 2,
                zIndex: 10,
                start: function(e, ui){
                    $(this).find('.v_wp_editor').each(function(){
                        tinyMCE.execCommand( 'mceRemoveControl', false, $(this).attr('id') );
                    });
                },
                stop: function(e,ui) {
                    $(this).find('.v_wp_editor').each(function(){
                        tinyMCE.execCommand( 'mceAddControl', false, $(this).attr('id') );
                        tinyMCE.execCommand( 'mceSetContent', false, switchEditors.wpautop( $(this).val() ) );
                        $(this).sortable("refresh");
                    });
                }
            });
        }
        
        function init_new_editor(editor_id){
            if ( typeof tinyMCEPreInit.mceInit[editor_id] !== "undefined" ) return;
            var new_editor_object = hidden_editor_object;
            
            new_editor_object['elements'] = editor_id;
            tinyMCEPreInit.mceInit[editor_id] = new_editor_object;
        }
        
        function v_delete_module( $module ){
            $module.remove();
            init_modules_js();
            
            // save changes after the element is removed
            layout_save( false );
        }
        
        function v_generate_layout_shortcode( html_element ){
            var shortcode_output = '';
            
            html_element.find( '> .module' ).each( function(){
                var $this_module = $(this),
                    $this_module_width = $this_module.find('> .module_name > .module_width'),
                    module_name = 'v_' + $this_module.attr('data-name'),
                    module_content = '';
                
                shortcode_output += '[' + module_name;
                
                if ( $this_module_width.length && $this_module_width.text() !== '' ) shortcode_output += ' width="' + parseInt( $this_module_width.text().replace(/[()]/,'') ) + '"';
                if ( $this_module.hasClass('first') ) shortcode_output += ' first_class="1"';
                
                if ( $this_module.hasClass('m_column') ){
                    shortcode_output += ']' + '\n';
                    shortcode_output += v_generate_layout_shortcode( $this_module );
                } else {
                    $this_module.find('> .module_settings .module_setting').each( function(){
                        var $this_option = $(this),
                            option_name = $this_option.attr('data-option_name'),
                            option_value = $this_option.html();
                        
                 //console.log(option_name);        
                if ( option_name == 'slides' || option_name == 'nivo' || option_name == 'camera'){  
                            shortcode_output += ']';
                            $this_option.find('.attachment').each( function(){
                                var $this_attachment = $(this),
                                    attachment_id = $this_attachment.attr('data-attachment'),
                                    attachment_link = $this_attachment.find('.attachment_link').val(),
                                    attachment_description = $this_attachment.find('.attachment_description').html();
                                
                                shortcode_output += '[v_'+option_name+' attachment_id="' + attachment_id + '" link="' + attachment_link + '"]' + attachment_description + '[/v_'+option_name+']';
                            } );
                                                        
                        } else if ( $this_option.hasClass('v_module_content') ){
                                module_content = option_value;
                            } else {
                                shortcode_output += ' ' + option_name + '="' + option_value + '"';
                            }
                        
                    } );
                    
                    if ( ! ( shortcode_output.charAt(shortcode_output.length-1) === ']' ) ) shortcode_output += ']';
                }
                
                shortcode_output += module_content + '[/' + module_name + ']' + '\n';
            } );
            
                        console.log(shortcode_output);
            return shortcode_output;
        }
        
        
        layout_window_resize();
        
        function layout_window_resize(){
            var $_page_builder = $('#page_builder')
                _window_width = $(window).width(),
                _new_page_builder_width = 0,
                _block_width_difference = 0;
            
            if ( _window_width > 1260 ) _new_page_builder_width = page_builder_original_width;
            else if ( _window_width <= 1260 && _window_width > 900 ) _new_page_builder_width = page_builder_original_width - ( 1260 - _window_width );
            else if ( _window_width <= 900 && _window_width > 850 ) _new_page_builder_width = page_builder_original_width - ( 1260 - _window_width ) + 113;
            else _new_page_builder_width = page_builder_original_width - ( 1260 - _window_width ) + 113 + 305;
            
            $_page_builder.width( _new_page_builder_width );
            
            builder_width = _new_page_builder_width - 42;
            
            main_module_width = builder_width - 2;
            
            if ( _window_width < 1260 ){
                _block_width_difference = _new_page_builder_width - _window_width;
            }
            
            calculate_modules();
        }
        
        $(window).resize( function(){
            layout_window_resize();
        } );
    });
        
})(jQuery)