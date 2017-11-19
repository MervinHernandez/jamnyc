<?php
/**
 * Searches for the blog language in the locale folder
 * Defaults to en (the default fullcalendar language)
 *
 * Sets $GLOBALS['ecs_calendar_language']
 */
function ecs_set_fullcalendar_language() {
	$GLOBALS['ecs_calendar_language'] = 'en';
	$language = str_replace( '_', '-', get_locale() );
	if ( file_exists( trailingslashit( dirname( __FILE__ ) ) . 'assets/js/locale/' . strtolower( basename( $language ) ) . '.js' ) )
		$GLOBALS['ecs_calendar_language'] = strtolower( basename( $language ) );
	else {
		$roots = explode( '-', $language );
		$language = $roots[0];
		if ( file_exists( trailingslashit( dirname( __FILE__ ) ) . 'assets/js/locale/' . strtolower( basename( $language ) ) . '.js' ) ) {
			$GLOBALS['ecs_calendar_language'] = strtolower( basename( $language ) );
		}
	}
}

function ecs_register_scripts_calendar() {
	wp_register_script( 'tecs-full-calendar-moment', plugins_url( '/assets/js/moment.min.js', __FILE__ ), array(), '2.18.1', true );
	wp_register_script( 'tecs-full-calendar', plugins_url( '/assets/js/fullcalendar.min.js', __FILE__ ), array( 'jquery', 'tecs-full-calendar-moment' ), '3.4.0', true );

	// Load the exact locale for the calendar if available, otherwise try for the root language
	ecs_set_fullcalendar_language();
	if ( $GLOBALS['ecs_calendar_language'] and 'en' !== $GLOBALS['ecs_calendar_language'] ) {
		wp_register_script( 'tecs-full-calendar-language', plugins_url( '/assets/js/locale/' . basename( $GLOBALS['ecs_calendar_language'] ) . '.js', __FILE__ ), array( 'tecs-full-calendar' ), '3.4.0', true );
		wp_register_script( 'tecs-calendar-init', plugins_url( '/assets/js/tecs-calendar.min.js', __FILE__ ), array( 'tecs-full-calendar-language' ), TECS_VERSION, true );
	} else {
		wp_register_script( 'tecs-calendar-init', plugins_url( '/assets/js/tecs-calendar.min.js', __FILE__ ), array( 'tecs-full-calendar' ), TECS_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'ecs_register_scripts_calendar' );

/*
 * Remove default content order elements as they won't be rendered anyway
 */
function ecs_default_contentorder_calendar( $contentorder, $atts, $post ) {
	return 'title';
}

function ecs_always_show_calendar( $always_show, $atts ) {
	if ( isset( $atts['design'] ) and 'calendar' == $atts['design'] )
		$always_show = true;
	return $always_show;
}
add_action( 'ecs_always_show', 'ecs_always_show_calendar', 10, 2 );

/*
 * Change any other default attributes
 */
function ecs_shortcode_atts_calendar( $default_atts, $atts, $post ) {
	$default_atts['thumb'] = 'false';
	$default_atts['venue'] = 'false';
	$default_atts['eventbg'] = '';
	$default_atts['eventfg'] = '#fff';
	$default_atts['eventborder'] = '';
	$default_atts['limit'] = 100;
	$default_atts['fromdate'] = date( 'Y-m-d', strtotime( date( 'Y-m' ) . '-01' ) - DAY_IN_SECONDS * 7 );
	$default_atts['todate'] = date( 'Y-m-d', strtotime( date( 'Y-m' ) . '-01' ) + DAY_IN_SECONDS * 45 );

	return $default_atts;
}

/*
 * Global start/end tags
 */
function ecs_start_tag_calendar( $output, $atts, $post ) {
	$output = '';

	// Set an indicator so we don't get a trailing ,
	$GLOBALS['tecs_calendar_events_first'] = true;

	if ( ! defined( 'DOING_AJAX' ) or ! DOING_AJAX ) {
		// Create a unique ID in case there are multiple calendars on the page
		if ( ! isset( $GLOBALS['ecs-calendar-id'] ) )
			$GLOBALS['ecs-calendar-id'] = 0;
		$GLOBALS['ecs-calendar-id']++;
		$calendar_id = 'ecs-calendar-' . intval( $GLOBALS['ecs-calendar-id'] );

		// Add the CSS styling.  Don't want to enqueue on all pages nor have a regex for every
		// single page request to detect if the plugin with design="calendar" is loading.
		$output .= '<style>';
		ob_start();
		include( trailingslashit( dirname( __FILE__) ) . 'assets/css/fullcalendar.min.css' );
		$output .= ob_get_contents();
		ob_end_clean();

		$output .= '#' . $calendar_id . ' table {margin: 0;}
	#' . $calendar_id . ' .fc-widget-header table {margin: 0;}
	#' . $calendar_id . ' th {font-weight: normal;}
	#' . $calendar_id . ' a.fc-event {box-shadow:none;}
	#' . $calendar_id . '-container {position:relative;}
	#' . $calendar_id . '-loading {position:absolute;width:100%;height:100%;z-index:100;top:0;left:0;background:#fff;opacity:0.7;text-align:center;line-height:90px;display:none;}"';

		if ( isset( $atts['eventbg'] ) and $atts['eventbg'] )
			$output .= '#' . $calendar_id . ' a.fc-event {background-color:' . esc_html( $atts['eventbg'] ) . ';border:1px solid ' . esc_html( $atts['eventbg'] ) . ';}';
		if ( isset( $atts['eventborder'] ) and $atts['eventborder'] )
			$output .= '#' . $calendar_id . ' a.fc-event {border:1px solid ' . esc_html( $atts['eventborder'] ) . ';}';
		if ( isset( $atts['eventfg'] ) and $atts['eventfg'] )
			$output .= '#' . $calendar_id . ' a.fc-event {color:' . esc_html( $atts['eventfg'] ) . ';}';

		$output .= '#tecs-tooltipevent.tooltip-' . $calendar_id . ' h4 {font-size:18px;letter-spacing:0;margin:0;color:#0a0a0a;}';
		$output .= '#tecs-tooltipevent.tooltip-' . $calendar_id . ' .ecs-calendar-event-body {font-size:11px;color:#0a0a0a;}';
		$output = apply_filters( 'ecs_calendar_styles', $output, $atts, $post );
		$output .= '</style>';

		// Enqueue the necessary scripts if they're not already
		if ( ! wp_script_is( 'tecs-calendar-init' ) ) {
			wp_enqueue_script( 'tecs-calendar-init' );
		}
		$output .= '<script type="text/javascript">var tecsEvents = tecsEvents || {}; var tecEventCalendarSettings = tecEventCalendarSettings || {};</script>';

		// Create the container element for the calendar and the "loading" overlay
		$output .= '<div id="' . $calendar_id . '-container">';
		$output .= '<div id="' . $calendar_id . '" class="ecs-events calendar"></div>';
		$output .= '<div id="' . $calendar_id . '-loading">Loading...</div>';
		$output .= '</div>';
		$output .= "<script type='text/javascript'>";

		// Set any options via the attributes, to pass into the calendar
		$atts['height'] = is_numeric( $atts['height'] ) ? intval( $atts['height'] ) : $atts['height'];
		$atts['ajaxurl'] = admin_url( 'admin-ajax.php' );
		$atts['action'] = 'ecs_calendar_events';
		$atts['first_load'] = true;
		$output .= "tecEventCalendarSettings['" . $calendar_id . "'] = " . json_encode( $atts ) . ";";

		// Start the JS array for the events in this calendar.  Create a unique ID for each.
		$output .= "tecsEvents['" . $calendar_id . "'] = [";
	} else {
		$output .= '[';
	}
	return $output;
}

function ecs_end_tag_calendar( $output, $atts, $post ) {
	$output = '';
	if ( ! defined( 'DOING_AJAX' ) or ! DOING_AJAX ) {
		// End the array from the start tag
		$output = '];</script>';
	} else {
		$output .= ']';
	}
	return $output;
}
