<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package local-tasker
 */

get_header();
?>
<main id="primary" class="site-main">
	<section class="error-404 not-found sm:py-16 py-14 text-center min-h-[480px] flex items-center">
		<div class="container">
			<header class="page-header">
				<h1 class="page-title mb-4"><?php esc_html_e('404 ERROR', 'local-tasker'); ?></h1>
			</header><!-- .page-header -->
			<div class="page-content">
				<div class="text">
					<p>
						Sorry, we can’t find the page you’re looking for.<br> Click the button below to go back to the homepage.
					</p>
				</div>
				<div class="btn-wrap mt-8">
					<a href="<?php echo home_url(); ?>" class="btn btn--brand">Back Home</a>
				</div>
			</div><!-- .page-content -->
		</div>
	</section><!-- .error-404 -->
</main><!-- #main -->
<?php
get_footer();
