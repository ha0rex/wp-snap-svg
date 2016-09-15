# wp-snap-svg

An SVG Animator Plugin for WordPress, based on Adobe's Web Framework Snap.SVG.

<h3>How to use it?</h3>
<ol>
	<li>Upload plugin files to /wp-content/plugins/wp-snap-svg directory, or use WordPress plugin installer</li>
	<li>Use Snap.SVG Animations > Add new menu on your WP Adminbar to create your first animation</li>
	<li>Place it anywhere on your website by the shortcode or the Visual Composer addon</li>
</ol>

<h3>How to write your Animation JS script?</h3>
<pre>
Animation.init = function($svg, $atts) {
	/** 
	 * This function runs immediately after pageload.
	 * You can simply call $svg.select('#element').
	 * $atts contains your WP shortcode atts. Useful to pass data from WP shortcode to your animation ($atts.extras).
	 */
}
</pre>
<pre>
Animation.start = function($svg, $atts) {
	/** 
	 * This function runs when the element appears on screen. If start_when_in_viewport is false, it starts right after Animation.init().
	 */
}
</pre>
<h3>How to pass data from WordPress to Animation?</h3>
Shortcode atts are available in the $atts JS object. By default "extras" shortcode attribute is available for this, also in the Visual Composer plugin. You can access it in $atts.extras in your script.

<h3>How to embed your animation?</h3>
<strong>[snap-svg-animation id="ID-OF-ANIMATION" start_when_in_viewport="true/false" start_when_in_viewport_offset="pixels" start_when_in_viewport_delay="seconds" html_id="HTML ID" html_class="HTML CLASSNAMES" extras="EXTRAS FIELD (optional)"]</strong><br />

<ul>
	<li>Set "start_when_in_viewport" attribute to true, if you want your animations to start only when it appears on screen</li>
	<li>You can set up an offset in pixels by the "start_when_in_viewport_offset", and a delay by the "start_when_in_viewport_delay" attributes.</li>
	<li>"extras" field is useful if you want to pass data from your shortcode to your animation JS script</li>
</ul>


