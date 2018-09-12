<?php

// =============================================================================
// FUNCTIONS.PHP
// -----------------------------------------------------------------------------
// Overwrite or add your own custom functions to Pro in this file.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Parent Stylesheet
//   02. Additional Functions
// =============================================================================

// Enqueue Parent Stylesheet
// =============================================================================

add_filter( 'x_enqueue_parent_stylesheet', '__return_true' );

// Additional Functions
// =============================================================================


// WordPress - Remove Emoji load
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// WordPress - HIDE Link Manager
function remove_links_menu() {
	remove_menu_page('link-manager.php');
}
add_action( 'admin_menu', 'remove_links_menu' );

// WOOCOMMERCE - Account Page - Additional Tab
include 'functions-wc-account-tab.php';

// WOOCOMMERCE - Exclude Category from Shop Page
function custom_pre_get_posts_query( $q ) {

	$tax_query = (array) $q->get( 'tax_query' );

	$tax_query[] = array(
		'taxonomy' => 'product_cat',
		'field' => 'slug',
		'terms' => array( 'jam-membership' ), // Don't display products in the clothing category on the shop page.
		'operator' => 'NOT IN'
	);


	$q->set( 'tax_query', $tax_query );

}
add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );

// WOOCOMMERCE - Automatically Complete Orders
/**
 * Auto Complete all WooCommerce orders.
 */
add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );
function custom_woocommerce_auto_complete_order( $order_id ) {
	if ( ! $order_id ) {
		return;
	}

	$order = wc_get_order( $order_id );
	$order->update_status( 'completed' );
}

// GRAVITY - Custom Activation Page SUB FOLDER
/**
 * Gravity Forms Custom Activation Template
 * http://gravitywiz.com/customizing-gravity-forms-user-registration-activation-page
 */
//add_action('wp', 'custom_maybe_activate_user', 9);
//function custom_maybe_activate_user() {
//
//	$template_path = STYLESHEETPATH . '/gfur-activate-template/activate.php';
//	$is_activate_page = isset( $_GET['page'] ) && $_GET['page'] == 'gf_activation';
//
//	if( ! file_exists( $template_path ) || ! $is_activate_page  )
//		return;
//
//	require_once( $template_path );
//
//	exit();
//}