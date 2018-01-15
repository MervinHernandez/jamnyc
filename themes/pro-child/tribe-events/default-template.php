<?php
/**
 * Default Events Template
 * This file is the basic wrapper template for all the views if 'Default Events Template'
 * is selected in Events -> Settings -> Template -> Events Template.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/default-template.php
 *
 * @package TribeEventsCalendar
 *
 */
/*
    =========================
    THE JAM NYC | 2018
    Custom CSS for Events Calendar
    v1.0
    =========================
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

get_header();
?>
<div class="x-container max width offset">
    <div id="tribe-events-pg-template" class="tribe-events-pg-template entry-wrap" >
        <?php tribe_events_before_html(); ?>
        <?php tribe_get_view(); ?>
        <?php tribe_events_after_html(); ?>
    </div> <!-- #tribe-events-pg-template -->
</div>
<?php
get_footer();
