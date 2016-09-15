/*
jQuery.fn.isInViewport = function(atts, callback) {
	var element = this;
	var top_of_element = jQuery(element).offset().top;
	var bottom_of_element = jQuery(element).offset().top + jQuery(element).outerHeight();
	var bottom_of_screen = jQuery(window).scrollTop() + jQuery(window).height();
	if( (bottom_of_screen > top_of_element + atts.offset && bottom_of_screen < bottom_of_element + atts.offset) ) {
		element.was_in_viewport = true;
		callback(element);
	}
	return element;
}

jQuery.fn.startWhenInViewport = function(atts, callback) {
	var atts = atts ? atts : {},
		element = this;
	atts.offset = atts.offset ? atts.offset : 0;
	jQuery(this).each(function() {
		var element = this;
		jQuery(window).scroll(function() {
			if( !element.was_in_viewport ) {
				jQuery(element).isInViewport(atts, function(e) {
					callback();	
				});
			}
		});
		jQuery(document).ready(function() {
			jQuery(element).isInViewport(atts, function(e) {
				callback();	
			});
		});
	});	
}
*/
jQuery.fn.isInViewport = function(atts) {
	var element = this;
	var top_of_element = jQuery(element).offset().top;
	var bottom_of_element = jQuery(element).offset().top + jQuery(element).outerHeight();
	var bottom_of_screen = jQuery(window).scrollTop() + jQuery(window).height();
	var top_of_screen = jQuery(window).scrollTop();
	
	if( (bottom_of_screen > top_of_element + atts.offset && top_of_screen < bottom_of_element + atts.offset) ) {
		return true
	}
	return false;
}

jQuery.fn.startWhenInViewport = function(atts, callback) {
	var atts = atts ? atts : {},
		element = this;
	atts.offset = atts.offset ? atts.offset : 0;
	jQuery(this).each(function() {
		var element = this;
		jQuery(window).scroll(function() {
			if( !element.was_in_viewport && jQuery(element).isInViewport(atts) ) {
				callback();
			}
		});
		jQuery(document).ready(function() {
			if(jQuery(element).isInViewport(atts)) {
				callback();
			}
		});
	});	
}