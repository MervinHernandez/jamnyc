<div class="x-recent-posts cf vertical">
	<a class ="x-recent-post3 no-image" href="<?php echo tribe_get_event_link(); ?>" rel="bookmark">
		<article class="post-775 post type-post status-publish format-standard has-post-thumbnail hentry category-newsletters even">
			<div class="entry-wrap">
				<div class="x-recent-posts-content">
					<h3 class="h-recent-posts"><?php echo apply_filters( 'ecs_event_list_title', get_the_title(), $atts, $post ) ?></h3>
					<span class="x-recent-posts-date">
						<?php echo tribe_get_start_date( null, false, 'M j' ).' @ '.tribe_get_start_date( null, false, 'g:i a' )?>
					</span>
				</div>
			</div>
		</article>
	</a>
</div>
