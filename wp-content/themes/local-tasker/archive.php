<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package local-tasker
 */

get_header();
?>
<main id="primary" class="site-main">
	<section class="default-listing py-16">
		<div class="container">
			<?php if (have_posts()): ?>
				<header class="page-header md:mb-16 mb-10">
					<?php
					the_archive_title('<h1 class="page-title text-h2">', '</h1>');
					the_archive_description('<div class="archive-description">', '</div>');
					?>
				</header><!-- .page-header -->
				<div class="grid wd:grid-cols-3 sm:grid-cols-2 grid-cols-1 sm:gap-7 gap-10">
					<?php
					/* Start the Loop */
					while (have_posts()):
						the_post();

						/*
						 * Include the Post-Type-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
						 */
						get_template_part('template-parts/blocks/blogs-filter/card');


					endwhile;
					?>
				</div>
				<?php
				the_posts_navigation();

			else:

				get_template_part('template-parts/content', 'none');

			endif;
			?>
		</div>
	</section>
</main><!-- #main -->
<?php
get_footer();
