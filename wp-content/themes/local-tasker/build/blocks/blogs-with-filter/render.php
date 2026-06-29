<?php
/**
 * ACF Block Template: Blog With Filter
 *
 * Renders a filterable grid of WordPress posts with AJAX-powered
 * category filtering and "Load More" pagination.
 *
 * @package  Local Tasker
 * @since    1.0.0
 *
 * @param array $block ACF block settings and attributes.
 */

if (!defined('ABSPATH')) {
	exit;
}

// ─── Anchor ──────────────────────────────────────────────────────────────────

$anchor = '';
if (!empty($block['anchor'])) {
	// Note: $anchor is used via `echo $anchor` (no extra esc_attr) because the
	// anchor value is already escaped here. Double-escaping would corrupt the quotes.
	$anchor = 'id="' . esc_attr($block['anchor']) . '" ';
}

// ─── Class Name ──────────────────────────────────────────────────────────────

$class_name = 'acf-block lt-blogs-filter py-16';

if (!empty($block['className'])) {
	$class_name .= ' ' . $block['className'];
}

if (!empty($block['align'])) {
	$class_name .= ' align' . $block['align'];
}

// Cache result — avoid calling the helper twice.
$block_settings = generate_block_settings_classnames();
$class_name .= $block_settings['class_name'];

// ─── Inline Style ────────────────────────────────────────────────────────────

$style_attr = '';
$block_style = $block_settings['block_style'];

if ('' !== $block_style) {
	$style_attr = 'style="' . esc_attr($block_style) . '"';
}

// ─── Unique Block Instance ID ─────────────────────────────────────────────────
//
// ACF provides a unique `$block['id']` for every placed block. We expose this
// as a data attribute so the JS can scope all selectors to THIS element,
// allowing multiple instances of the block on the same page without conflicts.

$block_id = !empty($block['id']) ? $block['id'] : 'bwf-' . wp_unique_id();

// ─── Configuration ────────────────────────────────────────────────────────────

$posts_per_page = 3;
$nonce = wp_create_nonce('bwf_ajax_nonce');

// ─── ACF Fields ───────────────────────────────────────────────────────────────

$bwf_section_title = get_field('bwf_section_title');

// ─── Initial Query ────────────────────────────────────────────────────────────

$initial_query = new WP_Query(
	[
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => $posts_per_page,
		'paged' => 1,
	]
);

// max_num_pages is WP's ceil( found_posts / posts_per_page ) — cleanest has_more check.
$has_more = $initial_query->max_num_pages > 1;

// ─── Category Tabs ────────────────────────────────────────────────────────────
//
// Pull every non-empty category sorted alphabetically.

$categories = get_categories(
	[
		'hide_empty' => true,
		'orderby' => 'name',
		'order' => 'ASC',
	]
);
?>
<section <?php echo $anchor; ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>
	data-block-id="<?php echo esc_attr($block_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"
	data-posts-per-page="<?php echo esc_attr($posts_per_page); ?>">
	<div class="wd:container-bx container">
		<!-- Filter Header -->
		<div class="fiter-head flex flex-wrap justify-between sm:gap-8 gap-5 sm:mb-12 mb-10">
			<?php if ($bwf_section_title): ?>
				<h2
					class="sec-title sm:text-[1.875rem] text-body-xl sm:leading-[1.2] leading-[1.4] md:w-[40%] w-full text-lt-text-primary sm:tracking-[0.4px] tracking-[-0.95px]">
					<?php echo esc_html($bwf_section_title); ?>
				</h2>
			<?php endif; ?>
			<!-- Category Filter -->
			<div class="filter-holder md:w-[54%] w-full overflow-auto scrollbar-thin scrollbar-thumb-[#0a65fc78] scrollbar-track-lt-snow-drift max-md:mr-[-32px] max-md:w-[calc(100%+32px)] max-sm:mr-[-16px] max-sm:w-[calc(100%+16px)]">
				<ul class="th-filter filter-blog flex gap-4.5 m-0 whitespace-nowrap pb-2" role="list">
					<li>
						<button
							class="active filter-btn font-medium sm:tracking-[0.02em] leading-none cursor-pointer p-[11px_14px] bg-transparent text-lt-text-placeholder text-center rounded-[50px] transition-colors duration-360 hover:bg-lt-brand hover:text-lt-white"
							data-category="0" aria-pressed="true">
							<?php esc_html_e('All Posts', 'lt-theme'); ?>
						</button>
					</li>
					<?php foreach ($categories as $cat): ?>
						<li>
							<button
								class="filter-btn font-medium sm:tracking-[0.02em] leading-none cursor-pointer p-[11px_14px] bg-transparent text-lt-text-placeholder text-center rounded-[50px] transition-colors duration-360 hover:bg-lt-brand hover:text-lt-white"
								data-category="<?php echo esc_attr($cat->term_id); ?>" aria-pressed="false">
								<?php echo esc_html($cat->name); ?>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			</div><!-- End Category Filter -->
		</div><!-- End Filter Header -->
		<!-- Blog Grid -->
		<div class="blog-lists grid wd:grid-cols-3 sm:grid-cols-2 grid-cols-1 sm:gap-7 gap-10">
			<?php
			if ($initial_query->have_posts()):
				while ($initial_query->have_posts()):
					$initial_query->the_post();
					get_template_part('template-parts/blocks/blogs-filter/card');
				endwhile;
			else:
				echo '<p class="col-span-full text-center text-lt-text-placeholder py-8">'
					. esc_html__('No posts found.', 'lt-theme')
					. '</p>';
			endif;

			// Always reset after a custom WP_Query to restore the main query's
			// global $post and prevent corruption in subsequent template code.
			wp_reset_postdata();
			?>
		</div><!-- End Blog Grid -->
		<!-- Load More -->
		<div class="ld-btn-wrap flex justify-center mt-12<?php echo !$has_more ? ' hidden' : ''; ?>">
			<button class="blog-load-more-btn btn btn--brand md:min-w-[256px] cursor-pointer max-smlr:w-full"
				aria-label="<?php esc_attr_e('Load more blog articles', 'lt-theme'); ?>">
				<?php esc_html_e('Load More Articles', 'lt-theme'); ?>
			</button>
		</div><!-- End Load More -->
	</div>
</section>