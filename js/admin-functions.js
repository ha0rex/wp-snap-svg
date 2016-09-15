jQuery.fn.single_double_click = function(single_click_callback, double_click_callback, timeout) {
  return this.each(function(){
    var clicks = 0, self = this;
    jQuery(this).click(function(event){
      clicks++;
      if (clicks == 1) {
        setTimeout(function(){
          if(clicks == 1) {
            single_click_callback.call(self, event);
          } else {
            double_click_callback.call(self, event);
          }
          clicks = 0;
        }, timeout || 300);
      }
    });
  });
}

function WPAnimate(animation, svg) {
	for(var i in animation) {
		animation[i] = new WPSnapSVGAnimation(animation[i], svg);
	}
}

function WPSnapSVGAnimation(animation, svg) {
	if(typeof animation !== 'function') {
		animation.count = animation.count ? animation.count : 1;
		animation.current = animation.current ? animation.current : 1;
		setTimeout(function() {
			svg.select(animation.element).animate(animation.atts, animation.timing, animation.type, function() {
				console.log('Animation "'+animation.element+'"['+animation.current+'/'+animation.count+'] finished in '+animation.timing+'ms with a '+animation.delay+'ms delay. '+JSON.stringify(animation.atts));
				//console.log(animation);
				if(animation.finish) {
					if(!animation.finish.element) { animation.finish.element = animation.element }
					if(!animation.finish.type) { animation.finish.type = animation.type }
					if(!animation.finish.timing) { animation.finish.timing = animation.timing }
					if(!animation.finish.delay) { animation.finish.delay = animation.delay }
					/* if(!animation.finish.atts) { animation.finish.atts = animation.atts } */
			
					animation.finish = new WPSnapSVGAnimation(animation.finish, svg);
				}
				if(animation.repeat && animation.count > animation.current) {
					animation.delayBetween = animation.delayBetween ? animation.delayBetween : 0;
					setTimeout(function() {
						animation.current++;
						console.log(animation); 
						var repeat = new WPSnapSVGAnimation(animation, svg);
					}, animation.delayBetween);
				}
					
							
			});
			if(animation.start) {
				if(!animation.start.element) { animation.start.element = animation.element }
				if(!animation.start.type) { animation.start.type = animation.type }
				if(!animation.start.timing) { animation.start.timing = animation.timing }
				if(!animation.start.delay) { animation.start.timing = animation.delay }
		
				animation.start = new WPSnapSVGAnimation(animation.start, svg);
			}			

		}, animation.delay);
	}
	else {
		animation();
	}	
	
	this.getFullLength = function() {
		
	}
	
	return this;
}