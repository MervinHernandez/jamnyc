<?php $contentorder = array_map( 'trim', $atts['contentorder'] ); ?>

<!-- Event Grid Item -->
<li class="x-block-grid-item topevents">
    <div class="ecs-event">
        <div class="month" id="topevents-month">
            <?php echo tribe_get_start_date( null, false, 'l' ) ?>
        </div>
		<div class="ecs-title">
            <a href="<?php echo tribe_get_event_link(); ?>" rel="bookmark"><?php echo apply_filters( 'ecs_event_list_title', get_the_title(), $atts, $post ) ?></a>
        </div>
        <div class="day" id="topevents-day">
            <?php echo tribe_get_start_date( null, false, 'M j' ).' @ '.tribe_get_start_date( null, false, 'g:i a' )?>
        </div>
    </div>
</li>
<!--CLOSE item-->