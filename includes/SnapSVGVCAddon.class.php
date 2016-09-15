<?php
// don't load directly
if (!defined('ABSPATH')) die('-1');

class SnapSVGVCAddon {
    function __construct() {
        // We safely integrate with VC with this hook
        add_action( 'init', array( $this, 'integrateWithVC' ) );
 
        // Use this when creating a shortcode addon
        add_shortcode( 'vc-snap-svg', array( 'SnapSVG', 'SnapSVGShortCodeFunc' ) );

        // Register CSS and JS
        add_action( 'wp_enqueue_scripts', array( $this, 'loadCssAndJs' ) );
        
    }

    public function loadCssAndJs() {
		//if( is_admin() ) {
			wp_enqueue_style( 'snap_svg_admin_style', plugin_dir_url(__FILE__).'/css/admin.css', array(), false, 'all' );
		//}    	
    }
    
    public function generateDropdown($array) {    	
    	foreach( $array as $index=>$value ) {
    		$dropdown[$value] = $index;
    	}
    	
    	return $dropdown;
    }
    
	function SnapSVGPostSelector( $settings, $value ) {
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'category'         => '',
			'category_name'    => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'snap_svg',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'author'	   => '',
			'author_name'	   => '',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$svg_animations_array = get_posts( $args );
		
		foreach( $svg_animations_array as $post ) {
			$options[$post->ID] = '<option value="'.$post->ID.'" '.( $post->ID == $value ? 'selected' : '' ).'>'.$post->post_title.'</option>';
		}
	   return '<div class="my_param_block">
				 <select name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput ' .
             esc_attr( $settings['param_name'] ) . ' ' .
             esc_attr( $settings['type'] ) . '_field">
				 	'.implode( '', $options ).'
				 </select>
		</div>'; // This is html markup that will be outputted in content elements edit form
	}	

 
    public function integrateWithVC() {
        // Check if Visual Composer is installed
        if ( ! defined( 'WPB_VC_VERSION' ) ) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array( $this, 'showVcVersionNotice' ));
            return;
        }
        vc_add_shortcode_param( 'snap_svg_post_selector', array( $this, 'SnapSVGPostSelector' )  );
        wp_enqueue_style( 'snap_svg_admin_style', plugin_dir_url(__FILE__).'../css/admin.css', array(), false, 'all' );
        
        $devices = array(
                  		'' => __("Select a device", 'wp-snap-svg'),
                  		'iphone6' => __("iPhone 6", 'wp-snap-svg'),
                  		'ipadair' => __("iPad Air", 'wp-snap-svg')
                  );
        
        vc_map( array(
            "name" => __("SVG Animation", 'wp-snap-svg'),
            "description" => __("A Snap.SVG Animated element", 'wp-snap-svg'),
            "base" => 'vc-snap-svg',
            "class" => "",
            "controls" => "full",
            "category" => __('Animation', 'wp-snap-svg'),
            "icon" => 'vc_extend_snap_svg',
            /*'admin_enqueue_js' => preg_replace( '/\s/', '%20', plugins_url( '../js/vc_wp-snap-svg.view.js', __FILE__ ) ),*/
            'js_view' => 'VCResponsiveWebsitePreviewView',
            'devices' => $devices,
           	/* 'custom_markup' => '<div class="vc_custom-element-container device-<%- params.device %>"><h4 class="wpb_element_title"> <i class="vc_general vc_element-icon vc_extend_rwp"></i> <%- vc_user_mapper.vc_wp-snap-svg.name %> </h4><strong>Device:</strong> <%- vc_user_mapper.vc_wp-snap-svg.devices[params.device] %><br /><strong>Scale:</strong> <%- Math.round(params.scale*100) %>%<br /><strong>URL:</strong> <%- params.url %></div>', */
            "params" => array(
                array(
                  "type" => "snap_svg_post_selector",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Select an SVG", 'wp-snap-svg'),
                  "param_name" => "id",
                  "value" => '',
                  "admin_label" => true,
                  "description" => __("Specify an URL to load on device", 'wp-snap-svg'),
              	),
              	/*
                array(
                  "type" => "dropdown",
                  "holder" => "",
                  "class" => "",
                  "heading" => __("Device type", 'wp-snap-svg'),
                  "param_name" => "device",
                  "value" => $this->generateDropdown($devices),
                  "devices" => $devices,
                  "admin_label" => true,
                  "description" => __("Please select a device to show", 'wp-snap-svg'),
              	),
              	*/
                array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Extras", 'wp-snap-svg'),
                  "param_name" => "extras",
                  "value" => '',
                  "description" => __("Animation extra parameters (if required)", 'wp-snap-svg'),
              	),
                array(
                  "type" => "checkbox",
                  "holder" => "",
                  "class" => "",
                  "heading" => __("Start when in Viewport", 'wp-snap-svg'),
                  "param_name" => "start_when_in_viewport",
                  "value" => false,
                  "description" => __("Check if you want to start Animation when it's in Viewport.", 'wp-snap-svg'),
              	),
                array(
                  "type" => "textfield",
                  "holder" => "",
                  "class" => "",
                  "heading" => __("Offset", 'wp-snap-svg'),
                  "param_name" => "start_when_in_viewport_offset",
                  "admin_label" => false,
                  "value" => '',
                  "description" => __("An offset in Pixels for \"Start when in Viewport\"", 'wp-snap-svg'),
              	), 
                array(
                  "type" => "textfield",
                  "holder" => "",
                  "class" => "",
                  "heading" => __("Delay", 'wp-snap-svg'),
                  "param_name" => "start_when_in_viewport_delay",
                  "admin_label" => false,
                  "value" => '',
                  "description" => __("Animation start delay in seconds", 'wp-snap-svg'),
              	),             	
                array(
                  "type" => "textfield",
                  "holder" => "",
                  "class" => "",
                  "heading" => __("Element ID", 'wp-snap-svg'),
                  "param_name" => "html_id",
                  "admin_label" => true,
                  "value" => '',
                  "description" => __("Enter row ID (Note: make sure it is unique and valid according to <a href=\"http://www.w3schools.com/tags/att_global_id.asp\" target=\"_blank\">w3c specification</a>", 'wp-snap-svg'),
              	),
                array(
                  "type" => "textfield",
                  "holder" => "",
                  "class" => "",
                  "heading" => __("Extra class name", 'wp-snap-svg'),
                  "param_name" => "html_class",
                  "value" => '',
                  "description" => __("Style particular content element differently - add a class name and refer to it in custom CSS.", 'wp-snap-svg'),
              	),
            )
        ) );
    }

    /*
    Show notice if your plugin is activated but Visual Composer is not
    */
    public function showVcVersionNotice() {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="updated">
          <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']).'</p>
        </div>';
    }
}

// Finally initialize code
new SnapSVGVCAddon();