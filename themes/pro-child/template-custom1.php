<?php

// =============================================================================
// TEMPLATE NAME: My Blank - Container | Header, Footer
// MERVIN Special
// =============================================================================

// original instructions for this template file
//x_get_view( x_get_stack(), 'template', 'blank-1' );

?>


 <?php get_header(); ?>

<div class="x-container max width offset">
	<div class="x-main full" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-wrap">
					<?php x_get_view( 'global', '_content', 'the-content' ); ?>

					### ACF </br>
						<?php

						// check if the repeater field has rows of data
						if( have_rows('gear_item', 'option') ):

							// loop through the rows of data
							while ( have_rows('gear_item', 'option') ) : the_row();
								echo 'Name: '; the_sub_field('gear_name'); echo '</br>';
								echo 'Category: '; the_sub_field('gear_category'); echo '</br>';
								echo 'Description: '; the_sub_field( 'gear_descrip' ); echo '</br>';
								echo '*** </br>';
							endwhile;

						else :

							// no rows found

						endif;

						?>
					### ACF
				</div>
			</article>

		<?php endwhile; ?>

	</div>
</div>

<?php get_footer(); ?>
