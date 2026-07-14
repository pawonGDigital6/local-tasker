<?php
/**
 * Product single page — layout shell.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main lt-product-single">
	<?php while ( have_posts() ) : the_post(); ?>

		<?php wc_get_template_part( 'content', 'single-product' ); ?>

	<?php endwhile; ?>
</main>

<?php
get_footer();
