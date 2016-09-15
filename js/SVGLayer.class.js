function SVGLayer(atts) {
	var Layer = this;
	
	var atts = atts;
	var id = jQuery(atts.layer).attr('id');
	var type = jQuery(atts.layer).prop('tagName');
	var Editor = atts.editor;
	var selected = false;
	var open = false;
	
	this.parent = atts.parent;
	this.selected = false;
	
	var getID = function() {
		return id;
	}
	
	var getType = function() {
		return type;
	}
	
	this.setSelected = function(value) {
		if( value == true ) {
			selected = true;
			Layer.select();
		}
		else {
			selected = false;
			Layer.select();
		}
	}
	
	this.select = function() {
		if(selected) {	
			Layer.css({ 
				'strokeWidth': 1,
				'stroke': 'red',
			});
			Layer.layerListBox.addClass('selected');
			if(Editor.selectedLayer) {
				jQuery(Editor.selectedLayer.layerListBox).removeClass('selected');
				Editor.selectedLayer.css({ 
					'strokeWidth': 0,
				});
			}
			
			Editor.selectedLayer = Layer;
		}
		else {
			Layer.css({ 
				'strokeWidth': 0,
			});
			Layer.layerListBox.removeClass('selected');	
			Editor.selectedLayer = false;
		}
	}
	
	this.getLayerBox = function() {
		Layer.layerListBox = jQuery('<li class="layer"></li>');
		Layer.layerListBox.addClass('layer-type-'+Layer.type);
		
		var classNames = Layer.attr('class') ? ' | class: '+Layer.attr('class') : '';
		var idName = Layer.attr('id') ? ' # '+Layer.attr('id') : '';
		
		var a = jQuery('<a>[ '+Layer.type+idName+classNames+' ]</a>');
		Layer.layerListBox.append(a);
		var fill = jQuery('<span class="fill"></span>');
		fill.css('background-color', Layer.css('fill'));
		Layer.layerListBox.prepend(fill);
					
		if(Layer.children.length) {
			var children = Editor.prependLayers(Layer.children);
			Layer.layerListBox.append(children);
		}
					
		a.single_double_click(function() {
			Layer.setSelected(true);
		}, function() {
			if(open) {
				Layer.close();
				open = false;
			}
			else {
				Layer.open();
				open = true;				
			}
		});
		
	
		return 	Layer.layerListBox;
	}
	
	this.open = function() {
		Layer.openParent();
		jQuery(' > ul', Layer.layerListBox).slideDown(200);
	}
	
	this.openParent = function() {
		jQuery(Layer.layerListBox).parents('ul').slideDown(1);
	}
	
	this.close = function() {
		jQuery(' > ul', Layer.layerListBox).slideUp(200);
	}
	
	this.id = id;
	this.layer = atts.layer;
	this.type = type;
	this.attr = function(attr) { return jQuery(atts.layer).attr(attr) };
	this.children = Editor.fetchLayers(atts.layer);
	this.css = function(css) { return jQuery(atts.layer).css(css) };
	this.svg = atts.svg;
	
	
	Snap(this.layer).click(function(e) {
		if( !Editor.selectedLayer || Editor.selectedLayer.layer != Layer.layer ) {
			Layer.openParent();
			Layer.setSelected(true);
			

			setTimeout(function() {
				var scrollTop = jQuery(Layer.layerListBox).offset().top;
										
				jQuery(Editor.layersContainer).animate({
					scrollTop: jQuery(Layer.layerListBox).offset().top + jQuery(Editor.layersContainer).scrollTop() - jQuery(Editor.layersContainer).offset().top,
				}, 500);
				e.stopPropagation();
			}, 2);
		}
		
		e.stopPropagation();
	});
	
	return this;
}