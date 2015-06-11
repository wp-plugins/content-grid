<?php
/*
Plugin Name: Content-Grid
Plugin URI: http://wordpress.org/plugins/content-grid/
Description: Content-Grid.
Version: 1.0
Author: webvitaly
Author URI: http://web-profile.com.ua/wordpress/plugins/
License: GPLv3
*/

define('CONTENT_GRID_VERSION', '1.0');


$content_grid_settings = array(
	'content_rows' => 3,
	'content_areas' => 7 // starting from #2, because main content treated as #1
);


include('content-grid-admin.php');
include('content-grid-frontend.php');


function content_grid_row_meta($links, $file) { // add some links to plugin meta row
	if (strpos($file, 'content-grid.php') !== false) {
		$links = array_merge( $links, array( '<a href="http://web-profile.com.ua/wordpress/plugins/content-grid/" title="Plugin page">Content-grid</a>' ) );
		$links = array_merge( $links, array( '<a href="http://web-profile.com.ua/donate/" title="Support the development">Donate</a>' ) );
	}
	return $links;
}
add_filter('plugin_row_meta', 'content_grid_row_meta', 10, 2);
