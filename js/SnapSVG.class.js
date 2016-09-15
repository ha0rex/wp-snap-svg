function SnapSVG(atts) {
	var Constructor = SnapSVG;
	var SnapSVGObject = this;
	
	SnapSVGObject.token = atts.token;
	SnapSVGObject.animation = atts.animation;
	
	this.remapSVG = function() {
		SnapSVGObject.jQuerySVG = jQuery('.snap-svg-token-'+SnapSVGObject.token+' svg'),
		SnapSVGObject.SVG = SnapSVGObject.jQuerySVG[0],
		SnapSVGObject.Snap = new Snap(SnapSVGObject.SVG);		
	}
	
	this.remapSVG();
	var getType = {};
	SnapSVGObject.animation(SnapSVGObject);
	
	this.setAnimation = function(animation) {
		SnapSVGObject.animation = animation;
		return new SnapSVG({
			animation: animation,
		});
	}
	
	this.resetAnimation = function() {
		SnapSVGObject.animation(SnapSVGObject);		
	}
}