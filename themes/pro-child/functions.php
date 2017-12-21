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

// ACF - Custom Gear List Page
add_action('acf/init', 'my_acf_init');
function my_acf_init() {

	if( function_exists('acf_add_options_page') ) {

		$option_page = acf_add_options_page(array(
			'page_title' 	=> __('Jam NYC', 'acf_jamnyc'),
			'menu_title' 	=> __('Jam NYC ACF', 'acf_jamnyc'),
			'menu_slug' 	=> 'acf-jamnyc',
		));

	}

}

// ACF - DISPLAY Sponsors - Shortcode Registration
function jamspons() {
	$jamspons = '';
	if( have_rows('sponsor_item','option') ):
		while ( have_rows('sponsor_item','option') ) : the_row();
			$jamspons .= get_sub_field('sponsor_name').'</br>';
			$jamspons .= '<img style="width:80px;" src="'.get_sub_field('sponsor_logo').'"/> </br>';
			$jamspons .= get_sub_field('sponsor_link').'</br>';
		endwhile;
	else :
		// no rows found
	endif;
	// Done
	return $jamspons;
}
add_shortcode( 'acf-jamnyc-sponsors', 'jamspons' );


// Additional Functions
// =============================================================================

