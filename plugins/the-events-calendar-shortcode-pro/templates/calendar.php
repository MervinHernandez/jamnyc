<?php
global $post;
if ( $GLOBALS['tecs_calendar_events_first'] )
	$GLOBALS['tecs_calendar_events_first'] = false;
else
	echo ',';
?><?php echo json_encode( array(
        'title' => get_the_title(),
        'start' => tribe_get_start_date( null, false, 'Y-m-d' ) . ( ( ! tribe_event_is_all_day() ) ? 'T' : '' ) . tribe_get_start_time( null, 'H:i:s' ),
        'end' => tribe_get_end_date( null, false, 'Y-m-d' ) . ( ( ! tribe_event_is_all_day() ) ? 'T' : '' ) . tribe_get_end_time( null, 'H:i:s' ),
        'url' => tribe_get_event_link(),
		'excerpt' => tribe_events_get_the_excerpt(),
		'details' => tribe_events_template_data( $post )
) ); ?>