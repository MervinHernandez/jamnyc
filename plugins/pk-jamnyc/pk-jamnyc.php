<?php
/**
* Plugin Name: Piklist - Jam NYC Customizations
* Plugin URI: https://mervinhernandez.com
* Description: These are custom functions created on Piklist
* Version: 1.0.0
* Author: Mervin Hernandez
* Author URI: https://mervinhernandez.com
* Plugin Type: Piklist
*/

add_filter('piklist_admin_pages', 'my_admin_pages');
function my_admin_pages($pages) {
	$pages[] = array(
		'page_title' => __('Jam NYC')
		,'menu_title' => __('The Jam NYC', 'jam-admin')
		,'capability' => 'manage_options'
		,'menu_slug' => 'jam_admin'
	);
	return $pages;
}
