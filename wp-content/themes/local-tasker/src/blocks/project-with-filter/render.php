<?php
/**
 * ACF Block Template: Project With Filter
 *
 * Renders a filterable grid of `lt_projects` with AJAX-powered taxonomy
 * (`lt_project_type`) filtering and a "View All Projects" action.
 *
 * @param array $block The block settings and attributes.
 */

if (!defined('ABSPATH')) {
	exit;
}

// Support custom "anchor" values.
$anchor = '';
if (!empty($block['anchor'])) {
	$anchor = 'id="' . esc_attr($block['anchor']) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'acf-block lt-projects py-16';

if (!empty($block['className'])) {
	$class_name .= ' ' . $block['className'];
}

if (!empty($block['align'])) {
	$class_name .= ' align' . $block['align'];
}

$class_name .= generate_block_settings_classnames()['class_name'];

// Block Style.
$style_attr = '';
$block_style = generate_block_settings_classnames()['block_style'];

if ($block_style !== '') {
	$style_attr = 'style="' . $block_style . '"';
}

// ─── Unique Block Instance ID ─────────────────────────────────────────────────
// Lets the JS scope every selector to THIS block, so multiple instances on one
// page never conflict.
$block_id = !empty($block['id']) ? $block['id'] : 'pwf-' . wp_unique_id();

// ─── Post Data ────────────────────────────────────────────────────────────────
$post_id  = get_the_ID();

// ─── Configuration ────────────────────────────────────────────────────────────
// Temporary: 3. Final: 9. Change this single value — the SSR query, the AJAX
// handler (via data-posts-per-page) and the "has more" logic all key off it.
$posts_per_page = 6;
$nonce = wp_create_nonce('pwf_ajax_nonce');

// ─── ACF Fields ───────────────────────────────────────────────────────────────
$pwf_sec_title = get_field('pwf_sec_title');
$pwf_sec_text  = get_field('pwf_sec_text');



// ─── Initial Query ────────────────────────────────────────────────────────────
$initial_query = new WP_Query(
	[
		'post_type'      => 'lt_projects',
		'post_status'    => 'publish',
		'posts_per_page' => $posts_per_page,
		'paged'          => 1,
	]
);

// More projects exist than the first batch → show the "View All" button.
$has_more = (int) $initial_query->found_posts > $posts_per_page;

// ─── Category Tabs ────────────────────────────────────────────────────────────
// Every non-empty `lt_project_type` term, alphabetically.
$project_types = get_terms(
	[
		'taxonomy'   => 'lt_project_type',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	]
);
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>
	data-block-id="<?php echo esc_attr($block_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"
	data-posts-per-page="<?php echo esc_attr($posts_per_page); ?>">
	<div class="wd:container-bx container">
		<!-- Filter Header -->
		<div class="fiter-head flex flex-wrap items-center justify-between sm:gap-8 gap-14 mb-12">
			<div class="text-left md:w-[40%] w-full flex flex-col gap-2 max-sm:gap-[18px] max-sm:text-center">
				<?php if ($pwf_sec_title): ?>
					<h2
						class="sec-title sm:text-[1.875rem] text-body-xl sm:leading-[1.2] leading-[normal] text-lt-text-primary sm:tracking-[0.4px] tracking-[-0.95px] max-sm:text-h2">
						<?php echo esc_html($pwf_sec_title); ?>
					</h2>
				<?php endif; ?>
				<?php if ($pwf_sec_text): ?>
					<div class="text-lt-text-muted sm:tracking-[-0.31px] max-sm:text-caption-md"><?php echo esc_html($pwf_sec_text); ?></div>
				<?php endif; ?>
			</div>
			<!-- Category Filter -->
			<div
				class="filter-holder md:w-[54%] w-full overflow-auto scrollbar-thin scrollbar-thumb-[#0a65fc78] scrollbar-track-lt-snow-drift max-md:mr-[-32px] max-md:w-[calc(100%+32px)] max-sm:mr-[-16px] max-sm:w-[calc(100%+16px)]">
				<ul class="th-filter filter-project flex md:justify-end gap-2 m-0 whitespace-nowrap pb-2" role="list">
					<li>
						<button
							class="filter-btn active font-medium text-base leading-6 cursor-pointer px-4 py-2 rounded-[10px] bg-transparent text-lt-text-muted text-center transition-colors duration-300 hover:bg-lt-brand/10 hover:text-lt-brand"
							data-category="0" aria-pressed="true"><?php esc_html_e('All', 'lt-theme'); ?></button>
					</li>
					<?php if (!empty($project_types) && !is_wp_error($project_types)): ?>
						<?php foreach ($project_types as $term): ?>
							<li>
								<button
									class="filter-btn font-medium text-base leading-6 cursor-pointer px-4 py-2 rounded-[10px] bg-transparent text-lt-text-muted text-center transition-colors duration-300 hover:bg-lt-brand/10 hover:text-lt-brand"
									data-category="<?php echo esc_attr($term->term_id); ?>" aria-pressed="false">
									<?php echo esc_html($term->name); ?>
								</button>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div><!-- End Category Filter -->
		</div><!-- End Filter Header -->
		<!-- Project Grid -->
		<div class="project-lists grid wd:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-8">
			<?php
			if ($initial_query->have_posts()):
				while ($initial_query->have_posts()):
					$initial_query->the_post();
					get_template_part('template-parts/blocks/project-filter/card');
				endwhile;
			else:
				echo '<p class="col-span-full text-center text-lt-text-placeholder py-8">'
					. esc_html__('No projects found.', 'lt-theme')
					. '</p>';
			endif;

			// Restore the main query globals after our custom loop.
			wp_reset_postdata();
			?>
		</div><!-- End Project Grid -->
		<!-- View All -->
		<div class="ld-btn-wrap flex justify-center mt-12<?php echo !$has_more ? ' hidden' : ''; ?>">
			<button class="project-load-more-btn btn btn--brand md:min-w-[256px] cursor-pointer max-smlr:w-full"
				aria-label="<?php esc_attr_e('View all projects', 'lt-theme'); ?>">
				<?php esc_html_e('View All Projects', 'lt-theme'); ?>
			</button>
		</div><!-- End View All -->
	</div>
</section>
