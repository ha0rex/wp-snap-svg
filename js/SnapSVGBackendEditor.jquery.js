jQuery.fn.SnapSVGBackendEditor = function(params) {
	/**
	 * @(private)params: {
	 * 		token: Generated token for animation
	 *		animation: The animation JS function inside the editor
	 * }
	 */
	var params = params ? params : {};
	var Editor = this;
	var AnimationFunction = params.animation;
	var PreviewSVG;
	var AnimationRunner;
	
	this.token = params.token;
	
	/**
	 * @(public)Editor.layersContainer: Layers container element JS object
	 */
	this.layersContainer = jQuery('.wp-snap-svg-layers-container', this)[0];

	/**
	 * CONSOLE "EDITOR" variables & functions
	 * @(private)consoleElement: Console element JS object
	 *
	 * @(public)Editor.theme_console: Theme of Console (default: dawn)
	 * @(public)Editor.console: Console ACE Editor JS object
	 */	
	var consoleElement = jQuery('.console', this)[0];	
	
	this.theme_console = params.theme_console ? params.theme_console : 'dawn';
	this.console = ace.edit(consoleElement);
	this.console.setTheme("ace/theme/"+this.theme_console);
	this.console.setReadOnly(true);
	this.console.renderer.setShowGutter(false);

	/**
	 * HTML EDITOR variables & functions
	 *
	 * @(private)html_editor: HTML Editor element JS object
	 * @(private)html_editor: HTML Editor element JS object
	 *
	 * @(public)Editor.theme_html: Theme of HTML Editor (default: dawn)
	 * @(public)Editor.editor_html: HTML Editor ACE Editor JS object
	 * @(public)Editor.value_html: HTML Editor textarea value
	 */
	var html_editor = jQuery('.editor-html', this)[0],
		html_textarea = jQuery('textarea[name="animation[html]"]', this).hide();
				
	this.theme_html = params.theme_html ? params.theme_html : 'dawn';		
	this.value_html = html_textarea.val();
	this.editor_html = ace.edit(html_editor);
	
	this.editor_html.session.setValue(this.value_html);
	this.editor_html.setTheme("ace/theme/"+this.theme_html);
	this.editor_html.session.setMode("ace/mode/xml");

	/**
	 * On change of HTML editor, let's update it's Textarea value & update the Preview
	 */	
	this.editor_html.session.on('change', function() {
		Editor.updateHTMLValue();
		Editor.updatePreview();
	});
	
	/**
	 * Editor.updateHTMLValue(): Updates Editor.value_html & @(private)html_textarea value by current value of ACE Editor
	 */		
	this.updateHTMLValue = function() {
		this.value_html = this.editor_html.session.getValue();
		html_textarea.val(this.value_html);
	}

	/**
	 * JS EDITOR variables & functions
	 *
	 * @(private)html_editor: HTML Editor element JS object
	 * @(private)html_editor: HTML Editor element JS object
	 *
	 * @(public)Editor.theme_html: Theme of HTML Editor (default: dawn)
	 * @(public)Editor.editor_html: HTML Editor ACE Editor JS object
	 * @(public)Editor.value_html: HTML Editor textarea value
	 */
	var js_editor = jQuery('.editor-js', this)[0],
		js_textarea = jQuery('textarea[name="animation[js]"]', this).hide();
			
	this.theme_js = params.theme_js ? params.theme_js : 'dawn'		
	this.value_js = js_textarea.val();
	this.editor_js = ace.edit(js_editor);
	this.editor_js.resize();
	
	this.editor_js.session.setValue(this.value_js);
	this.editor_js.setTheme("ace/theme/"+this.theme_js);
	this.editor_js.session.setMode("ace/mode/javascript");

	/**
	 * On change of JS editor, let's update it's Textarea value & update the Preview
	 */		
	this.editor_js.session.on('change', function() {
		Editor.updateJSValue();
		Editor.updatePreview();
	});

	/**
	 * Editor.updateJSValue(): Updates Editor.value_js & @(private)js_textarea value by current value of ACE Editor
	 */		
	this.updateJSValue = function() {
		this.value_js = this.editor_js.session.getValue();
		js_textarea.val(this.value_js);
		AnimationFunction = this.value_js;
	}

	/**
	 * PREVIEW variables & functions
	 *
	 * @(public)Editor.preview_container: Preview container element jQuery object
	 * @(public)Editor.svg_inner_wrapper: SVG Inner Wrapper element jQuery object
	 * @(public)Editor.svg: SVG element jQuery object
	 */	
	var start_stop_button = jQuery('<button class="start-stop">Start</button>');
	start_stop_button.click(function() {
		Editor.run(Editor.SnapSVG);
		return false;
	});
	
	this.preview_container = jQuery('.wp-snap-svg-preview-container', this);
	this.preview_container.prepend(start_stop_button);
	this.svg_inner_wrapper = jQuery('.svg-inner-wrapper', this);
	this.svg = jQuery('svg', this.svg_inner_wrapper);

	/**
	 * Editor.fetchLayers(elem): Fetches all layers & builds structure from @elem
	 * Returns @Layers: Recursive SVGLayer object
	 */		
	this.fetchLayers = function(elem) {
		var elements = jQuery(elem).children('*');
		var Layers = [];
		elements.each(function() {
			var Parent = new SVGLayer({
				'layer': this,
				'editor': Editor,
				'svg': Editor.jQuerySVG,
			});
			
			Layers.push(Parent);
		});
		
		return Layers;
	}

	/**
	 * Editor.prependLayers(elem): Builds layers list by previously generated layers structure (by Editor.fetchLayers(elem))
	 * Returns @Layers: Recursive layers list (ul > li > ul > li ...)
	 */		
	this.prependLayers = function(elem) {
		var Layers = jQuery('<ul></ul>');
		var a = [];
		var li = [];
		
		if(elem.length) {
			for(var i in elem) {
				Layers.append(elem[i].getLayerBox());
			}
			
			return Layers;
		}
		
		return false;
	}

	/**
	 * Editor.createImageFromSVG(svg): svg is svg[text (svg code)]. Default value is html value of ACE Editor
	 */		
	this.createImageFromSVG = function(svg) {
		var svg = svg ? svg : Editor.value_html;
		Editor.svg_inner_wrapper.html(svg);
	}
	
	/**
	 * Editor.updatePreview(): Updates preview by current values of HTML & JS editors
	 */	
	this.updatePreview = function() {
		//Editor.run();
		this.svg_inner_wrapper.html(this.value_html);
		try {
			PreviewSVG = new SnapSVG({
				token: Editor.token,
				animation: function(SnapSVG) { 
					var WPSnapSVG = SnapSVG,
						SnapSVG = SnapSVG.Snap;
					
					Editor.SnapSVG = SnapSVG;
						
					Editor.jQuerySVG = jQuery(SnapSVG.node);
					
					var elements = jQuery(Editor.jQuerySVG).children('g');
					
					if( !Editor.Layers ) {
						Editor.Layers = Editor.fetchLayers(Editor.jQuerySVG.parent());
						var Layers = Editor.prependLayers(Editor.Layers);
						jQuery('.wp-snap-svg-layers-container ul').replaceWith(Layers);
					}	
					
					Editor.run(SnapSVG);
					
					/*
					for(var i in $runner)	{
						$runner[i] = function() {
							return;
						};
					}
					*/				
				}
			});
		}
		catch(err) {
			Editor.consoleLog(err);
		}
		
	}
	
	this.run = function($svg) {
		/* Editor.createImageFromSVG(); */
		/* Editor.updatePreview(); */
		/* Editor.clearPreview(); */
		var Animation = {};
		var $atts = {};
		var SnapSVG = $svg;
		
		var $runner = {};
		
		eval(AnimationFunction);
	
		if(Animation.init) {
			Animation.init($svg, $atts, $runner);
		}
	
		if(Animation.start) {
			Animation.start($svg, $atts, $runner);
		}	
	}
	
	this.clearPreview = function() {
		Editor.svg_inner_wrapper.html(Editor.value_html);	
	}

	/**
	 * Editor.consoleLog(data): Writes data to custom console (Editor.console)
	 */	
	this.consoleLog = function(data) {
		var value = Editor.console.session.getValue();
		Editor.console.session.setValue( value + data.message + "\n" );
		Editor.console.gotoLine(Editor.console.session.getLength());
	};
	
	this.updateJSValue();
	this.updatePreview();
}