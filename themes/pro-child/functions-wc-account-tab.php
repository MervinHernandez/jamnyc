<?php
/**
* @snippet       WooCommerce Add New Tab @ My Account
* @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
* @sourcecode    https://businessbloomer.com/?p=21253
* @credits       https://github.com/woothemes/woocommerce/wiki/2.6-Tabbed-My-Account-page
* @author        Rodolfo Melogli
* @testedwith    WooCommerce 2.6.7
*/

// ------------------
// 1. Register new endpoint to use for My Account page
// Note: Resave Permalinks or it will give 404 error

function add_jam_messages_endpoint() {
add_rewrite_endpoint( 'jam-messages', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'add_jam_messages_endpoint' );

// ------------------
// 2. Add new query var

function jam_messages_query_vars( $vars ) {
$vars[] = 'jam-messages';
return $vars;
}

add_filter( 'query_vars', 'jam_messages_query_vars', 0 );

// ------------------
// 3. Insert the new endpoint into the My Account menu

function add_jam_messages_link_my_account( $items ) {
$items['messages'] = 'Jam Messages';
return $items;
}

add_filter( 'woocommerce_account_menu_items', 'add_jam_messages_link_my_account' );

// ------------------
// 4. Add content to the new endpoint

function jam_messages_content() {
echo '<h3>Premium WooCommerce Support</h3>
<p>Welcome to the WooCommerce support area. As a premium customer, you can submit a ticket should you have any WooCommerce issues with your website, snippets or customization. <i>Please contact your theme/plugin developer for theme/plugin-related support.</i></p>';
echo do_shortcode( ' /* your shortcode here */ ' );
}
add_action( 'woocommerce_account_jam-messages_endpoint', 'jam_messages_content' );