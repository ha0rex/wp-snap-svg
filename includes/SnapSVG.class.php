<?php
ini_set('display_errors', 1);
class SnapSVG {
	protected $SnapSVGVersion;
	
	public function __construct($id = false) {
		return $this->init($id);
	}

	private function init($id) {
		$this->id = $id;
		
		$this->pluginVersion = WP_SNAP_SVG_PLUGIN_VER;
		$this->SnapSVGVersion = SNAP_SVG_VER;

		$this->getAnimationDetails();
		$this->addShortCodes();
		$this->registerPostType();
		
		if ( defined( 'WPB_VC_VERSION' ) ) {
			$this->initVisualComposerAddOn();
		}
		
		return $this;
	}
	
	public function registerPostType() {
		$this->postType = new SnapSVGCPT();
		
		return $this;
	} 
	
	public function loadSnapSVGJS() {
		wp_enqueue_script( 'snap-svg', plugin_dir_url(__FILE__).'../assets/snap-svg/snap.svg-min.js', false, $this->SnapSVGVersion, true );
		
		return $this;
	}
	
	public function enqueueFrontEndJS() {
		wp_enqueue_script( 'wp-snap-svg', plugin_dir_url(__FILE__).'../js/SnapSVG.class.js', false, $this->pluginVersion, true );
		wp_enqueue_script( 'wp-snap-svg-inviewport', plugin_dir_url(__FILE__).'../js/inViewport.jquery.js', false, $this->pluginVersion, true );
		
		return $this;		
	}
	
	private function getAnimationDetails( $id = false ) {
		$this->id = $id = $id ? $id : $this->id;
		// generate token for element
		$this->generateToken();
		
		$animation = get_post_meta( $id, 'animation', true );
		$animation['post'] = get_post( $id );
		$this->animation = $animation;

		return $this; 
	}
	
	public function addShortCodes() {
		add_shortcode( 'snap-svg-animation', array( $this, 'SnapSVGShortCodeFunc' ) );
	}
	
	public function SnapSVGShortCodeFunc( $atts, $content = "" ) {
		$atts = shortcode_atts( array(
			'id' => '',
			'extras' => '',
			'start_when_in_viewport' => false,
			'start_when_in_viewport_offset' => 0,
			'start_when_in_viewport_delay' => '0',
			'html_id' => '',
			'html_class' => '',
		), $atts, 'snap-svg-animation' );
		
		if( !(isset($this) && get_class($this) == __SnapSVG__) ) {
			$animation = new SnapSVG( $atts['id'] );
		}
		else {
			$animation = $this;
		}
		
		$animation->loadSnapSVGJS()->enqueueFrontEndJS();
		
		return '<div id="'.$atts['html_id'].'" class="snap-svg-wrapper snap-svg-token-'.$animation->token.' snap-svg-'.$animation->animation['post']->post_name.' '.$atts['html_class'].'">
			<div class="svg-inner-wrapper">
				'.$animation->animation['html'].'
			</div>
	
			<div class="js-wrapper">
				<textarea class="tmp-js-container">'.$animation->animation['js'].'</textarea>
				<script type="text/javascript">
					var snap_svg_animation_'.$animation->token.';
					jQuery(document).ready(function() {
						snap_svg_animation_'.$animation->token.' = new SnapSVG({ 
							token: "'.$animation->token.'", 
							animation: function(SnapSVG) {
								var WPSnapSVG = SnapSVG,
									SnapSVG = SnapSVG.Snap;
									
								
								var Animation = {};
								var $atts = '.json_encode($atts).';
								
								var $runner = eval(jQuery(".snap-svg-token-'.$animation->token.' .tmp-js-container").val());
								jQuery(".snap-svg-token-'.$animation->token.' .tmp-js-container").remove();
																			
								if(Animation.init) {
									Animation.init(SnapSVG, $atts, $runner);
								}
								
								if($atts.start_when_in_viewport && Animation.start) {
									jQuery(SnapSVG.node).startWhenInViewport({
										offset: jQuery(window).height()/2+$atts.start_when_in_viewport_offset
									}, function() {
										if(!Animation.started) {
											Animation.started = true;
											setTimeout(function() {
												Animation.start(SnapSVG, $atts, $runner);
											}, $atts.start_when_in_viewport_delay*1000);
										}
									});
								}
								else if(Animation.start) {
									Animation.start(SnapSVG, $atts, $runner);
									setTimeout(function() {
										Animation.started = true;
									}, $atts.start_when_in_viewport_delay*1000);
								}						
							} 
						});						
					});
				</script>			
			</div>
		</div>';
	}
	
	private function initVisualComposerAddOn() {
		require_once 'SnapSVGVCAddon.class.php';
	}
	
	private function generateToken() {
		$this->token = md5(uniqid(rand(), true));
		
		return $this;
	}
}
?>