<?php

// =============================================================================
// VIEWS/INTEGRITY/WOOCOMMERCE.PHP
// -----------------------------------------------------------------------------
// WooCommerce page output for Integrity.
// =============================================================================

?>

<?php get_header(); ?>
  <div class="x-container max width offset">
    <div class="x-main full" role="main">
        <div class="entry-wrap">
              <?php woocommerce_content(); ?>
        </div>
    </div>
    <?php get_sidebar(); ?>
  </div>
<?php get_footer(); ?>