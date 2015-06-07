var ProductRating = function() {
    var initEvents = function()
    {
        jQuery("#email").bind( "focus", function() {
            jQuery("label[for='email']").css({'opacity':'0'});
            if(jQuery("label[for='email']").siblings('div').hasClass('error')) {
                jQuery("label[for='email']").siblings('div').removeClass('error');
            };
        });
        
        jQuery("#email").bind( "blur", function() {
            if(jQuery(this).val() == null || jQuery(this).val() == '') {
                jQuery("label[for='email']").css({'opacity':'1'});
            }
        });
        
        jQuery("#author").bind( "focus", function() {
            jQuery("label[for='author']").css({'opacity':'0'});
            if(jQuery("label[for='author']").siblings('div').hasClass('error')) {
                jQuery("label[for='author']").siblings('div').removeClass('error');
            };
        });
        
        jQuery("#author").bind( "blur", function() {
            if(jQuery(this).val() == null || jQuery(this).val() == '') {
                jQuery("label[for='author']").css({'opacity':'1'});
            }
        });
        
        jQuery("#url").bind( "focus", function() {
            jQuery("label[for='url']").css({'opacity':'0'});
        });
        
        jQuery("#url").bind( "blur", function() {
            if(jQuery(this).val() == null || jQuery(this).val() == '') {
                jQuery("label[for='url']").css({'opacity':'1'});
            }
        });
        
        jQuery(".star-common").bind( "click", function() {
            var ratingValue = (20 * parseInt(jQuery(this).attr('data-rating-value')));
            jQuery(".actual-rating").css( {'width':''+ ratingValue + '%'} );
            jQuery(".actual-rating").attr('data-rating-value', jQuery(this).attr('data-rating-value'));
            if( jQuery('.alert-message-container').is(':visible')) {
                jQuery('.alert-message-container').hide();
            }
        });
        
        jQuery('textarea#commentShown').focus(function() {  
            if(!jQuery('#product-rating-area').is(":visible")) {
                jQuery(this).animate({height:70},500);
                jQuery('#product-rating-area').fadeIn(50);
                jQuery('#comment-form-identity').fadeIn(50);
            }
            
        });
        
        jQuery(".comment-form-submit-button").bind( "click", function() {
            var ratingValue = parseInt(jQuery(".actual-rating").attr('data-rating-value'));
            var result = validate();
            if(ratingValue < 1) {                
                jQuery('.alert-message-container').show();
                result = false;
            } else {                
                var comment = jQuery('#commentShown').val();
                if(comment != '') {
                    comment = comment + "<br>";
                }
                comment = comment + '<div unique-token="' + getUniqueToken() + '" class="small-star star-rating-non-editable-container"> <div class="current-rating" style="width: ' + (ratingValue * 20) + '%;"></div> </div>';
                jQuery('textarea#comment').val(comment);              
                jQuery('#commentShown').val('');                
            }            
            return result;
            
        });

    }
    
    var getUniqueToken = function() {       
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for( var i=0; i < 20; i++ ) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));            
        }
        return text;
    }
    
    var validate = function() {       
        var result = true;
        if(jQuery('.anonymous_post_comment').length && jQuery('.anonymous_post_comment') != undefined && jQuery('.anonymous_post_comment') != null) {
                if(jQuery("#email").val() == null || jQuery("#email").val() == '') {
                    jQuery("label[for='email']").html("Please enter your email address here");
                    jQuery("label[for='email']").siblings('div').addClass('error');
                    jQuery("label[for='email']").addClass('error');                     
                    result = false;
                } else if(!validateEmail(jQuery("#email").val())) {
                    jQuery("label[for='email']").html('<span class="nopublish">Invalid email address</span>');
                    jQuery("label[for='email']").siblings('div').addClass('error');
                    jQuery("label[for='email']").addClass('error');
                    jQuery("label[for='email']").css({'opacity':'1'});                    
                    result = false;
                }
                
                if(jQuery("#author").length && (jQuery("#author").val() == null || jQuery("#author").val() == '')) {
                    jQuery("label[for='author']").html("Please enter your your name here");
                    jQuery("label[for='author']").siblings('div').addClass('error');
                    jQuery("label[for='author']").addClass('error');                    
                    result = false;
                } 
        }
        
        return result;
    }
    
    var validateEmail = function(email) {
        var regexStr = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return regexStr.test(email);
    }

    return {
        init: function()
        {
            initEvents();
        }
    }
}();

jQuery(ProductRating.init);

