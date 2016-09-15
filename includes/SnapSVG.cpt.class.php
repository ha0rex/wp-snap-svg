<?php
class SnapSVGCPT {
	public function __construct() {
		return $this->init();
	}
	
	private function init() {
		$this->pluginVersion = WP_SNAP_SVG_PLUGIN_VER;
		$this->SnapSVGVersion = SNAP_SVG_VER;
		
		// Register snap_svg custom post type
		add_action( 'init', array( $this, 'registerPostType') );
		
		// Set up meta boxes
		$this->metaBoxesSetup();
		
		return $this;
	}
	
	public function getPluginVersion() {
		return $this->pluginVersion;
	}
	
	public function getSnapSVGVersion() {
		return $this->SnapSVGVersion;
	}
	
	public function registerPostType() {
		register_post_type( 'snap_svg',
		  array(
			'labels' => array(
			  'name' => __( 'Snap.SVG Animations', 'wp-snap-svg' ),
			  'singular_name' => __( 'Animation', 'wp-snap-svg' ),
			  'add_new' => __( 'Add new', 'wp-snap-svg' ),
			  'add_new_item' => __( 'Add new Animation', 'wp-snap-svg' ),
			  'edit_item' => __( 'Edit Animation', 'wp-snap-svg' ),
			  'new_item' => __( 'New Animation', 'wp-snap-svg' ),
			  'view_item' => __( 'View Animation', 'wp-snap-svg' ),
			  'not_found' => __( 'No Animations found', 'wp-snap-svg' ),
			),
			'public' => true,
			'has_archive' => false,
			'rewrite' => array('slug' => 'animations'),
			'menu_icon' => 'dashicons-portfolio',
			'taxonomies' => array('post_tag'),
			'supports' => array('title', 'slug', 'author')
		  )
		);
		
		return $this;
	}
	
	private function metaBoxesSetup() {
		add_action( 'load-post.php', array( $this, 'addMetaBoxes') );
		add_action( 'load-post-new.php', array( $this, 'addMetaBoxes') );
	
		/* Add meta boxes on the 'add_meta_boxes' hook. */
	  	add_action( 'add_meta_boxes', array($this, 'addMetaBoxes') );
  
	  	/* Save post meta on the 'save_post' hook. */
	  	add_action( 'save_post', array($this, 'savePostMeta'), 10, 2 );
	  	
	  	return $this;
	}
	
	public function addMetaBoxes() {
		add_meta_box(
			'snap_svg_meta_boxes',      // Unique ID
			esc_html__( 'Animation', 'Mimox' ),    // Title
		  	array( $this, 'addMetaBoxesCallback'),   // Callback function
		  	'snap_svg',         // Admin page (or post type)
		  	'normal',         // Context
		  	'default'         // Priority
		);
		
		return $this;
	}
	
	private function enqueueAceEditorJS() {
		wp_enqueue_script( 'ace-editor', plugin_dir_url(__FILE__).'../assets/ace-editor/src-noconflict/ace.js', false, '1.2.5', true );
		
		return $this;
	}
	
	private function enqueueAdminJS() {
		wp_enqueue_script( 'wp-snap-svg-svg-layer-class', plugin_dir_url(__FILE__).'../js/SVGLayer.class.js', false, $this->getPluginVersion(), true );
		wp_enqueue_script( 'wp-snap-svg-backend-editor', plugin_dir_url(__FILE__).'../js/SnapSVGBackendEditor.jquery.js', false, $this->getPluginVersion(), true );
		wp_enqueue_script( 'wp-snap-svg-admin-functions', plugin_dir_url(__FILE__).'../js/admin-functions.js', false, $this->getPluginVersion(), true );
		
		return $this;
	}
	
	public function savePostMeta( $post_id, $post ) {
		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST['snap_svg_animation_nonce'] ) || !wp_verify_nonce( $_POST['snap_svg_animation_nonce'], basename( __FILE__ ) ) )
			return $post_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;


		/* Get the posted data and sanitize it for use as an HTML class. */
		//$new_meta_value = ( isset( $_POST['smashing-post-class'] ) ? sanitize_html_class( $_POST['smashing-post-class'] ) : '' );
		update_post_meta( $post_id, 'animation', $_POST['animation'] );	
		
		return $this;
	}
	
	public function addMetaBoxesCallback( $object, $box ) { 
		$animation = get_post_meta( $object->ID, 'animation', true );	
		wp_nonce_field( basename( __FILE__ ), 'snap_svg_animation_nonce' );
		
		$this->enqueueAceEditorJS()->enqueueAdminJS();
		
		$SnapSVG = new SnapSVG( $object->ID );
		
		$SnapSVG->loadSnapSVGJS()->enqueueFrontEndJS();
		
		$animation_html = $animation ? $animation['html'] : '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg id="" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <!-- Generator: WP Snap.SVG '.$SnapSVG->pluginVersion.' - http://levi.racz.nl/wp-snap-svg -->
	
	<!-- '.__('Your SVG Code goes here...', 'wp-snap-svg').' -->
</svg>';
		
		$animation_js = $animation ? $animation['js'] : 'Animation.start = function($scope) {
	/**
	 * Your Animation JS code
	 * $scope is your SVG\'s Snap.SVG Object
	 * You can simply call $scope.select(\'#element\')
	 */		
}';
		
		?>
		<div id="wp-snap-svg-editor">
			<div class="wp-snap-svg-editors-container">
				<div class="html-code-container"><h3>SVG & JS code</h3><pre class="editor-html" id="editor-html"></pre><textarea name="animation[html]"><?php echo $animation_html ?></textarea></div>		
				<div class="js-code-container"><pre class="editor-js" id="editor-js"></pre><textarea name="animation[js]"><?php echo $animation_js ?></textarea></div>	
			</div>
			<div class="wp-snap-svg-preview-container">
				<h3>Preview</h3>
				<div class="svg-wrapper snap-svg-token-<?php echo $SnapSVG->token ?>">
					<div class="svg-inner-wrapper">
					</div>
					<div class="js-wrapper">			
					</div>				
				</div>
			</div>
			<div class="wp-snap-svg-layers-container">
				<ul></ul>
			</div>			
			<div class="wp-snap-svg-console-container">
				<pre class="console" id="console"></pre>
			</div>
		</div>
		<script type="text/javascript">
			var snap_svg_animation_<?php echo $SnapSVG->token ?>;
			jQuery(document).ready(function() {
				jQuery('#wp-snap-svg-editor').SnapSVGBackendEditor({
					token: "<?php echo $SnapSVG->token ?>", 
					animation: function(SnapSVG) {
						var WPSnapSVG = SnapSVG,
							SnapSVG = SnapSVG.Snap;
							
						var Animation = {};
						var shortcode_atts = {};
				
						<?php echo $SnapSVG->animation['js'] ?>		
						
						if(Animation.init) {
							Animation.init(SnapSVG, shortcode_atts);
						}
						
						if(Animation.start) {
							Animation.start(SnapSVG, shortcode_atts);
						}								
					}
				});				
			});
		</script>		
		<?php
		
		return $this;
	}		
}
?>