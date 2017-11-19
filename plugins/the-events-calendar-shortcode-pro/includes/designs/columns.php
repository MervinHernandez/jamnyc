<?php
/*
 * Change the default contentorder for a compressed, minimal view
 */
function ecs_default_contentorder_columns( $contentorder, $atts, $post ) {
	return 'thumbnail, title, venue, date, time, excerpt, button';

}

function ecs_register_scripts_columns() {
	wp_register_script( 'tecs-masonry', plugins_url( '/assets/js/masonry.pkgd.min.js', __FILE__ ), array( 'jquery' ), '4.2.0', true );
	wp_register_script( 'tecs-imagesloaded', plugins_url( '/assets/js/imagesloaded.pkgd.min.js', __FILE__ ), array( 'jquery' ), '4.2.0', true );
	wp_register_script( 'tecs-columns-init', plugins_url( '/assets/js/tecs-columns.min.js', __FILE__ ), array( 'tecs-masonry', 'tecs-imagesloaded' ), TECS_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'ecs_register_scripts_columns' );


/*
 * Change any other default attributes
 */
function ecs_shortcode_atts_columns( $default_atts, $atts, $post ) {
	$default_atts['thumb'] = 'true';
	$default_atts['eventdetails'] = 'true';
	$default_atts['venue'] = 'false';
	$default_atts['thumbwidth'] = '300';
	$default_atts['thumbheight'] = '200';
	$default_atts['groupby'] = 'day';
	$default_atts['timeonly'] = 'false';
	$default_atts['excerpt'] = 'true';
	$default_atts['limit'] = '30';
	$default_atts['button'] = 'false';
	return $default_atts;
}

/*
 * Global start/end tags
 */

function ecs_start_tag_columns( $output, $atts, $post ) {
	global $ecs_columns_design_count;
	if ( ! $ecs_columns_design_count )
		$ecs_columns_design_count = 0;
	$ecs_columns_design_count++;

	// Enqueue the necessary scripts if they're not already
	if ( ! wp_script_is( 'tecs-columns-init' ) ) {
		wp_enqueue_script( 'tecs-columns-init' );
	}

	$output = '<style>';
	$output .= '.ecs-events.ecs-grid.ecs-grid-' . intval( $ecs_columns_design_count ) . ' .ecs-event, .ecs-events.ecs-grid.ecs-grid-' . intval( $ecs_columns_design_count ) . ' .ecs-grid-sizer {width:30%;margin-bottom:20px;padding:0;}';
	$output .= '@media only screen and (max-width: 600px) { .ecs-events.ecs-grid.ecs-grid-' . intval( $ecs_columns_design_count ) . ' .ecs-event, .ecs-events.ecs-grid.ecs-grid-' . intval( $ecs_columns_design_count ) . ' .ecs-grid-sizer {width:97%;} }';
	$output .= '.ecs-events.ecs-grid.ecs-grid-' . intval( $ecs_columns_design_count ) . ' .ecs-event img {width:100%;}';
	$output .= '.ecs-events.ecs-grid.ecs-grid-' . intval( $ecs_columns_design_count ) . ' .ecs-event .ecs-venue {margin-bottom:10px;}';
	$output .= '.ecs-events.ecs-grid.ecs-grid-' . intval( $ecs_columns_design_count ) . ' .ecs-event .ecs-excerpt {margin-bottom:10px;}';
	$output .= '.ecs-events.ecs-grid.ecs-grid-' . intval( $ecs_columns_design_count ) . ' .ecs-event .ecs-wrap {padding:10px;}';
	$output .= '.ecs-events.ecs-grid.ecs-grid-' . intval( $ecs_columns_design_count ) . ' .ecs-event .ecs-date {margin-bottom:10px;font-weight:bold;}';
	$output .= '.ecs-events.ecs-grid.ecs-grid-' . intval( $ecs_columns_design_count ) . ' .ecs-button a {background-color:' . esc_html( $atts['buttonbg'] ) . ';background-image:none;border-radius:3px;border:0;box-shadow:none;color: ' . esc_html( $atts['buttonfg'] ) . ';cursor:pointer;display:inline-block;font-size:11px;font-weight:700;letter-spacing:1px;line-height:normal;padding:6px 9px;text-align:center;text-decoration:none;text-transform:uppercase;vertical-align:middle;zoom:1;}';
	$output .= '.ecs-events.ecs-clearfix {zoom:1;overflow:auto;}';
	if ( isset( $atts['titlesize'] ) and $atts['titlesize'] )
		$output .= '.ecs-events.ecs-grid.ecs-grid-' . intval( $ecs_columns_design_count ) . ' .ecs-event .summary a {font-size:' . esc_html( $atts['titlesize'] ) . ';}';
	$output = apply_filters( 'ecs_columns_styles', $output, $atts, $post );
	$output .= '</style>';
	$output .= '<div class="ecs-events ecs-clearfix ecs-grid ecs-grid-' . intval( $ecs_columns_design_count ) . '"' . ( ( isset( $atts['id'] ) and $atts['id'] ) ? ' id="' . esc_attr( $atts['id'] ) . '"' : '' ) . '><div class="ecs-grid-sizer"></div>';
	return $output;
}

function ecs_end_tag_columns( $output, $atts, $post ) {
	return '</div>';
}
