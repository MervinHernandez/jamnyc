<?php
function ecsp_full_description( $output, $atts, $post, $excerptLength ) {
	if ( Events_Calendar_Shortcode::isValid( $atts['description'] ) ) {
		if ( Events_Calendar_Shortcode::isValid( $atts['raw_description'] ) )
			return get_the_content();
		else
			return Events_Calendar_Shortcode::get_excerpt( $excerptLength, 'content' );
	}
	return $output;
}
add_filter( 'ecs_event_excerpt', 'ecsp_full_description', 5, 4 );
add_filter( 'ecs_excerpt_compact', 'ecsp_full_description', 5, 4 );

function escp_raw_excerpt( $output, $atts, $post, $excerptLength ) {
	if ( Events_Calendar_Shortcode::isValid( $atts['raw_excerpt'] ) ) {
		if ( trim( $post->post_excerpt ) )
			return $post->post_excerpt;
		else
			return get_the_excerpt();
	}
	return $output;
}
add_filter( 'ecs_event_excerpt', 'escp_raw_excerpt', 5, 4 );
add_filter( 'ecs_excerpt_compact', 'escp_raw_excerpt', 5, 4 );

/**
 * TIME option
 */

function ecs_add_timeonly_option( $event_output, $atts, $post ) {
	if ( Events_Calendar_Shortcode::isValid( $atts['timeonly'] ) ) {
		$event_output .= apply_filters( 'ecs_event_time_tag_start', '<div class="duration time ecs_start_time">', $atts, $post ) .
		apply_filters( 'ecs_event_list_time', tribe_get_start_time(), $atts, $post ) .
		apply_filters( 'ecs_event_time_tag_end', '</div>', $atts, $post );
	}
	return $event_output;
}
add_filter( 'ecs_event_list_output_custom_time', 'ecs_add_timeonly_option', 5, 3 );

/**
 * Ability to override the event output entirely with a template file matching
 * the design name
 */
function ecs_override_event_output( $event_output, $atts, $post ) {
	$filename = false;
	if ( file_exists( trailingslashit( dirname( TECS_PLUGIN_FILE ) ) . 'templates/' .  stripslashes( $atts['design'] ) . '.php' ) )
		$filename = trailingslashit( dirname( TECS_PLUGIN_FILE ) ) . 'templates/' .  stripslashes( $atts['design'] ) . '.php';

	if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'tecshortcode/' . stripslashes( $atts['design'] ) . '.php' ) )
		$filename = trailingslashit( get_stylesheet_directory() ) . 'tecshortcode/' . stripslashes( $atts['design'] ) . '.php';

	if ( false !== $filename ) {
		ob_start();
		include( $filename );
		$event_output = ob_get_clean();
	}

	return $event_output;
}
add_filter( 'ecs_single_event_output', 'ecs_override_event_output', 10, 3 );

/**
 * Adding an option to filter for a specific event
 */
function ecs_get_specific_event_id( $args, $atts ) {
	if ( isset( $atts['id'] ) and is_numeric( $atts['id'] ) ) {
		unset( $args['meta_query'] );
		unset( $args['tax_query'] );
		unset( $args['meta_key'] );
		unset( $args['author'] );
		unset( $args['order'] );
		$args['post__in'] = array( intval( $atts['id'] ) );
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecs_get_specific_event_id', 10, 2 );

/**
 * Option to exclude specific IDs or 'current' to exclude the current event
 */
function ecs_exclude_specific_event_id( $args, $atts ) {
	if ( isset( $atts['exclude_id'] ) and trim( $atts['exclude_id'] ) ) {
		if ( 'current' == $atts['exclude_id'] )
			$args['post__not_in'] = array( get_the_ID() );
		elseif ( is_numeric( trim( $atts['exclude_id'] ) ) )
			$args['post__not_in'] = array( intval( trim( $atts['exclude_id'] ) ) );
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecs_exclude_specific_event_id', 10, 2 );

/**
 * Adding a "button" option
 */
function ecs_event_list_output_button( $output, $atts, $post ) {
	$output = '';
	if ( Events_Calendar_Shortcode::isValid( $atts['button'] ) ) {
		$output = '<div class="ecs-button"><a href="' . ( ( 'website' == $atts['buttonlink'] && tribe_get_event_website_url() ) ? tribe_get_event_website_url() : tribe_get_event_link() ) . '" rel="bookmark">' . esc_html( $atts['button'] ) . '</a></div>';
	}
	return $output;
}
add_filter( 'ecs_event_list_output_custom_button', 'ecs_event_list_output_button', 10, 3 );

/**
 * Option to restrict to past only
 * Tack onto any existing meta query
 */
function ecsp_past_only_filter( $args, $atts, $meta_date_date, $meta_date_compare ) {
	if ( ! empty( $atts['past'] ) ) {
		if ( ! isset( $args['meta_query']['relation'] ) )
			$args['meta_query']['relation'] = 'AND';
		$args['meta_query'][] = array(
			'key' => $atts['key'],
			'value' => $meta_date_date,
			'compare' => '<',
			'type' => 'DATETIME'
		);
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_past_only_filter', 99, 4 );


/**
 * Option to restrict to future only
 * Do this last and tack onto any existing meta query
 */
function ecsp_future_only_filter( $args, $atts, $meta_date_date, $meta_date_compare ) {
	if ( Events_Calendar_Shortcode::isValid( $atts['futureonly'] ) ) {
		if ( ! isset( $args['meta_query']['relation'] ) )
			$args['meta_query']['relation'] = 'AND';
		$args['meta_query'][] = array(
			'key' => $atts['key'],
			'value' => $meta_date_date,
			'compare' => '>=',
			'type' => 'DATETIME'
		);
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_future_only_filter', 99, 4 );

/**
 * Option to restrict to a certain year
 */
function ecsp_year_filter( $args, $atts, $meta_date_date, $meta_date_compare ) {
	if ( $atts['year'] == 'current' ) {
		$atts['year'] = current_time( 'Y' );
	}
	if ( is_numeric( $atts['year'] ) ) {
		$year_startdate = intval( $atts['year'] ) . '-01-01';
		$year_enddate = ( intval( $atts['year'] ) + 1 ) . '-01-01';

		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key' => $atts['key'],
				'value' => $year_startdate,
				'compare' => '>=',
				'type' => 'DATETIME'
			),
			array(
				'key' => $atts['key'],
				'value' => $year_enddate,
				'compare' => '<',
				'type' => 'DATETIME'
			),

		);
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_year_filter', 10, 4 );

/**
 * Option to restrict to a certain date range
 *
 * @param $args
 * @param $atts
 * @param $meta_date_date
 * @param $meta_date_compare
 *
 * @return mixed
 */
function ecsp_date_range_filter( $args, $atts, $meta_date_date, $meta_date_compare ) {
	if ( ( trim( $atts['fromdate'] ) and false !== strtotime( $atts['fromdate'], current_time( 'timestamp' ) ) ) or ( trim( $atts['todate'] ) and false !== strtotime( $atts['todate'], current_time( 'timestamp' ) ) ) ) {
		$startdate = ( false !== strtotime( $atts['fromdate'], current_time( 'timestamp' ) ) ) ? date( 'Y-m-d', strtotime( $atts['fromdate'], current_time( 'timestamp' ) ) ) : false;
		$enddate = ( false !== strtotime( $atts['todate'], current_time( 'timestamp' ) ) ) ? date( 'Y-m-d', strtotime( $atts['todate'], current_time( 'timestamp' ) ) ) . ' 23:59:59' : false;

		if ( false !== $enddate and false !== $startdate ) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => $atts['key'],
					'value' => array( $startdate, $enddate ),
					'compare' => 'BETWEEN',
					'type' => 'DATETIME'
				)
			);
		} elseif ( false !== $startdate ) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => $atts['key'],
					'value' => $startdate,
					'compare' => '>=',
					'type' => 'DATETIME'
				)
			);
		} elseif ( false !== $enddate ) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => $atts['key'],
					'value' => $enddate,
					'compare' => '<=',
					'type' => 'DATETIME'
				)
			);
		}
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_date_range_filter', 10, 4 );


/**
 * Option to restrict by number of days in the future
 */
function ecsp_days_in_future( $args, $atts, $meta_date_date, $meta_date_compare ) {
	if ( isset( $atts['days'] ) and is_numeric( $atts['days'] ) ) {
		if ( $meta_date_compare == '<' )
			$end_date = date( 'Y-m-d H:i:s', strtotime( $meta_date_date ) - ( 86400 * intval( $atts['days'] ) ) );
		else
			$end_date = date( 'Y-m-d H:i:s', strtotime( $meta_date_date ) + ( 86400 * intval( $atts['days'] ) ) );

		//$args['start_date'] = $meta_date_date;
		//$args['end_date'] = date( 'Y-m-d H:i:s', strtotime( $meta_date_date ) + ( 86400 * intval( $atts['days'] ) ) );
		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key' => $atts['key'],
				'value' => $meta_date_date,
				'compare' => $meta_date_compare,
				'type' => 'DATETIME'
			),
			array(
				'key' => $atts['key'],
				'value' => $end_date,
				'compare' => ( $meta_date_compare == '<' ? '>=' : '<' ),
				'type' => 'DATETIME'
			),
		);
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_days_in_future', 10, 4 );

/**
 * Restrict to one day
 *
 * @param $args
 * @param $atts
 * @param $meta_date_date
 * @param $meta_date_compare
 *
 * @return mixed
 */
function ecsp_specific_day( $args, $atts, $meta_date_date, $meta_date_compare ) {
	if ( isset( $atts['day'] ) and
	     ( 'current' == trim( $atts['day'] ) or false !== strtotime( trim( $atts['day'] ) ) ) ) {
		if ( 'current' == $atts['day'] ) {
			$start_date = current_time( 'Y-m-d' ) . ' 00:00:00';
			$end_date = current_time( 'Y-m-d' ) . ' 23:59:59';
		} else {
			$start_date = date( 'Y-m-d', strtotime( trim( $atts['day'] ), current_time( 'timestamp' ) ) ) . ' 00:00:00';
			$end_date = date( 'Y-m-d', strtotime( trim( $atts['day'] ), current_time( 'timestamp' ) ) ) . ' 23:59:59';
		}

		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key' => $atts['key'],
				'value' => $start_date,
				'compare' => '>=',
				'type' => 'DATETIME'
			),
			array(
				'key' => $atts['key'],
				'value' => $end_date,
				'compare' => '<=',
				'type' => 'DATETIME'
			),
		);
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_specific_day', 10, 4 );



/**
 * Option to only show the first recurring instance
 *
 * @param $args
 * @param $atts
 */
function ecsp_hide_recurring_after_first( $args, $atts ) {
	global $events_calendar_shortcode;
	if ( isset( $atts['hiderecurring'] ) and $events_calendar_shortcode::isValid( $atts['hiderecurring'] ) )
		$args['tribeHideRecurrence'] = '1';
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_hide_recurring_after_first', 10, 2 );

/**
 * Add any pro-only shortcode attributes
 *
 * @param $atts
 *
 * @return mixed
 */
function ecsp_add_pro_shortcode_attributes( $atts ) {
	$atts['hiderecurring'] = 'false';
	$atts['tag'] = '';
	$atts['exclude_cat'] = '';
	$atts['venue_id'] = '';
	$atts['organizer_id'] = '';
	$atts['timeonly'] = 'false';
	$atts['day'] = '';
	$atts['days'] = '';
	$atts['button'] = 'false';
	$atts['buttonlink'] = '';
	$atts['description'] = 'false';
	$atts['raw_excerpt'] = 'false';
	$atts['raw_description'] = 'false';
	$atts['cost'] = 'false';
	$atts['buttonbg'] = '#666';
	$atts['buttonfg'] = '#fff';
	$atts['id'] = '';
	$atts['exclude_id'] = '';
	$atts['tag_cat_operator'] = '';
	$atts['year'] = '';
	$atts['futureonly'] = 'false';
	$atts['offset'] = 'false';
	$atts['city'] = '';
	$atts['state'] = '';
	$atts['country'] = '';
	$atts['fromdate'] = '';
	$atts['todate'] = '';
	$atts['featured_only'] = 'false';
	return $atts;
}
add_filter( 'ecs_shortcode_atts', 'ecsp_add_pro_shortcode_attributes' );

/**
 * Allows offset
 */
function ecsp_add_offset_arg( $args, $atts ) {
	if ( isset( $atts['offset'] ) and is_numeric( $atts['offset'] ) ) {
		$args['offset'] = intval( $atts['offset'] );
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_add_offset_arg', 10, 2 );

/**
 * Adds a description of the design/styling options
 */
function ecsp_add_design_options_text() {
	include dirname( __FILE__ ) . '/templates/admin-page-styling.php';
}
add_action( 'ecs_admin_page_styling_before', 'ecsp_add_design_options_text' );

/**
 * Adds descriptions of pro-only options
 */
function ecsp_add_pro_attribute_descriptions() {
	include dirname( __FILE__ ) . '/templates/admin-page-pro-attributes.php';
}
add_action( 'ecs_admin_page_options_after_cat', 'ecsp_add_pro_attribute_descriptions' );

/**
 * Add tag query values if specified
 */
function ecsp_add_tag_query_args( $args, $atts ) {
	if ( isset( $atts['tag'] ) and $atts['tag'] ) {
		if ( strpos( $atts['tag'], "," ) !== false ) {
			$atts['tags'] = explode( ",", $atts['tag'] );
			$atts['tags'] = array_map( 'trim', $atts['tags'] );
		} else {
			$atts['tags'] = array( trim( $atts['tag'] ) );
		}

		if ( ! isset( $args['tax_query'] ) or ! $args['tax_query'] ) {
			$args['tax_query'] = array(
				'relation' => 'OR',
			);
		}
		foreach ( $atts['tags'] as $tag ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'post_tag',
				'field' => 'slug',
				'terms' => $tag,
			);
		}
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_add_tag_query_args', 10, 2 );

/**
 * Option to exclude certain categories
 *
 * @param $args
 * @param $atts
 *
 * @return mixed
 */
function ecsp_add_exclude_category_query_args( $args, $atts ) {
	if ( isset( $atts['exclude_cat'] ) and $atts['exclude_cat'] ) {
		if ( strpos( $atts['exclude_cat'], "," ) !== false ) {
			$atts['exclude_cats'] = explode( ",", $atts['exclude_cat'] );
			$atts['exclude_cats'] = array_map( 'trim', $atts['exclude_cats'] );
		} else {
			$atts['exclude_cats'] = array( trim( $atts['exclude_cat'] ) );
		}
		if ( ! isset( $args['tax_query'] ) or ! $args['tax_query'] ) {
			$args['tax_query'] = array(
				'relation' => 'OR',
			);
		}
		$args['tax_query'][] = array(
			'taxonomy' => 'tribe_events_cat',
			'field' => 'slug',
			'terms' => $atts['exclude_cats'],
			'operator' => 'NOT IN',
		);
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_add_exclude_category_query_args', 10, 2 );


/**
 * Ability to specify the operator AND or OR for the taxnomoy query (cat, tag)
 * (Default is OR)
 */
function ecsp_change_tax_query_operator( $args, $atts ) {
	if ( isset( $args['tax_query'], $args['tax_query']['relation'] ) and isset( $atts['tag_cat_operator'] ) and in_array( trim( strtoupper( $atts['tag_cat_operator'] ) ), array( 'AND', 'OR' ) ) ) {
		$args['tax_query']['relation'] = trim( strtoupper( $atts['tag_cat_operator'] ) );
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_change_tax_query_operator', 10, 5 );

/**
 * Location (city, country, state/province) meta filters
 *
 * First we need to find the venues with the given city/state/country,
 * then add those venue IDs to the query.
 */
function ecsp_add_location_meta_query( $args, $atts ) {
	$venue_meta_query = array();
	$venue_ids = array();
	$location_key_mapping = array(
		'city' => '_VenueCity',
		'state' => '_VenueStateProvince',
		'country' => '_VenueCountry',
	);

	foreach ( $location_key_mapping as $key => $meta_key ) {
		if ( isset( $atts[$key] ) and trim( $atts[$key] ) ) {
			$values = explode( ',', $atts[$key] );
			$values = array_map( 'trim', $values );
			$venue_meta_query[] = array(
				'key' => $meta_key,
				'value' => $values,
				'compare' => 'IN',
			);
		}
	}

	if ( count( $venue_meta_query ) ) {
		$venue_meta_query['relation'] = 'AND';
		$venue_query = new WP_Query(
			array(
				'post_type' => 'tribe_venue',
				'nopaging' => true,
				'meta_query' => $venue_meta_query,
			)
		);
		if ( $venue_query->have_posts() ) {
			while ( $venue_query->have_posts() ) {
				$venue_query->the_post();
				$venue_ids[] = get_the_ID();
			}
			wp_reset_postdata();
		}

		if ( count( $venue_ids ) ) {
			if ( ! isset( $args['meta_query']['relation'] ) )
				$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][] = array(
				'key' => '_EventVenueID',
				'value' => $venue_ids,
				'compare' => 'IN',
			);
		} else {
			// If we have venue queries but nothing found, add a meta query
			// so we get nothing back.
			if ( ! isset( $args['meta_query']['relation'] ) )
				$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][] = array(
				'key' => '_EventVenueID',
				'value' => array( -1 ),
				'compare' => 'IN',
			);
		}
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_add_location_meta_query', 10, 2 );

/**
 * Ability to filter by one or more venue IDs
 * @param $args
 * @param $atts
 *
 * @return array
 */
function ecsp_add_venue_id_query( $args, $atts ) {
	if ( isset( $atts['venue_id'] ) and trim( $atts['venue_id'] ) ) {
		if ( strpos( $atts['venue_id'], "," ) !== false ) {
			$atts['venue_ids'] = explode( ",", $atts['venue_id'] );
			$atts['venue_ids'] = array_map( 'trim', $atts['venue_ids'] );
		} else {
			$atts['venue_ids'] = array( trim( $atts['venue_id'] ) );
		}
		if ( ! isset( $args['meta_query']['relation'] ) )
			$args['meta_query']['relation'] = 'AND';
		$args['meta_query'][] = array(
			'key' => '_EventVenueID',
			'value' => $atts['venue_ids'],
			'compare' => 'IN',
		);
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_add_venue_id_query', 10, 2 );

/**
 * Ability to filter by one or more organizer IDs
 * @param $args
 * @param $atts
 *
 * @return array
 */
function ecsp_add_organizer_id_query( $args, $atts ) {
	if ( isset( $atts['organizer_id'] ) and trim( $atts['organizer_id'] ) ) {
		if ( strpos( $atts['organizer_id'], "," ) !== false ) {
			$atts['organizer_ids'] = explode( ",", $atts['organizer_id'] );
			$atts['organizer_ids'] = array_map( 'trim', $atts['organizer_ids'] );
		} else {
			$atts['organizer_ids'] = array( trim( $atts['organizer_id'] ) );
		}
		if ( ! isset( $args['meta_query']['relation'] ) )
			$args['meta_query']['relation'] = 'AND';
		$args['meta_query'][] = array(
			'key' => '_EventOrganizerID',
			'value' => $atts['organizer_ids'],
			'compare' => 'IN',
		);
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_add_organizer_id_query', 10, 2 );


/**
 * Ability to filter by featured events only
 * @param $args
 * @param $atts
 *
 * @return array
 */
function ecsp_add_featured_events_only_query( $args, $atts ) {
	if ( isset( $atts['featured_only'] ) and Events_Calendar_Shortcode::isValid( $atts['featured_only'] ) ) {
		if ( ! isset( $args['meta_query']['relation'] ) )
			$args['meta_query']['relation'] = 'AND';
		$args['meta_query'][] = array(
			'key' => '_tribe_featured',
			'value' => 1,
			'type' => 'numeric',
		);
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecsp_add_featured_events_only_query', 10, 2 );
