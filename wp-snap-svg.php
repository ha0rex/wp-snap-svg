<?php
/**
 * Plugin Name: WP Snap.SVG
 * Plugin URI: http://upd8.hu
 * Description: Snap.SVG Plugin for WordPress to animate SVGs
 * Version: 1.0.0
 * Author: Levi Racz
 * Author URI: http://levi.racz.nl
 * License: GPL2
 * Text Domain: vc_snap_svg
 */
 
// don't load directly
if (!defined('ABSPATH')) die('-1');

/*
add_action( 'admin_init', 'define_plugin_version_func' );
function define_plugin_version_func() {
	if( function_exists( 'get_plugin_data' ) ) {
		define('WP_SNAP_SVG_PLUGIN_VER', get_plugin_data( __FILE__ )['Version']);
	}
	define('SNAP_SVG_VER', '0.4.1');
}
*/

if( function_exists( 'get_plugin_data' ) ) {
	define('WP_SNAP_SVG_PLUGIN_VER', get_plugin_data( __FILE__ )['Version']);
}
define('SNAP_SVG_VER', '0.4.1');

require_once 'includes/SnapSVG.class.php';
require_once 'includes/SnapSVG.cpt.class.php';

$SnapSVG = new SnapSVG();