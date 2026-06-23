<?php
/**
 * ACF Block template.
 *
 * @param array $block The block settings and attributes.
 */

// Support custom "anchor" values.
$anchor = '';
if (!empty($block['anchor'])) {
	$anchor = 'id="' . esc_attr($block['anchor']) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'acf-block lt-projects py-16 bg-lt-white';

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
$section_title = get_field('section_title');
$section_text = get_field('section_text');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="container flex flex-col sm:gap-10 gap-[3.125rem]">
		<div class="projects-header flex flex-col gap-[15px] md:flex-row md:items-end md:justify-between md:gap-8">
			<div class="projects-header__content flex max-w-[717px] flex-col gap-2.5">
				<?php if ($section_title): ?>
					<h2
						class="font-semi-ext text-[2rem] font-bold leading-8 tracking-[0.0022em] text-lt-onyx md:text-[2.5rem] md:leading-[3.375rem] md:tracking-[-0.0225em]">
						<?php echo esc_html($section_title); ?>
					</h2>
				<?php endif; ?>
				<?php if ($section_text): ?>
					<div class="text-caption-md leading-5 tracking-[0.02em] text-lt-secondary md:text-body md:leading-normal">
						<?php echo $section_text; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php
			$link = get_field('section_button_link');
			if ($link):
				$link_url = $link['url'];
				$link_title = $link['title'];
				$link_target = $link['target'] ? $link['target'] : '_self';
				?>
				<a class="projects-header__link group inline-flex w-fit items-center gap-2 text-caption-md font-bold leading-6 text-lt-accent md:text-body md:tracking-[0.02em]"
					href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
					<span class="text"><?php echo esc_html($link_title); ?></span>
					<span class="icon transition-transform duration-400 shrink-0 group-hover:translate-x-0.5">
						<svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="url(#clip0_168_6617)">
								<path d="M1.16602 7.5H12.8327" stroke="#F69222" stroke-width="1.66667" stroke-linecap="round"
									stroke-linejoin="round" />
								<path d="M7 1.66699L12.8333 7.50033L7 13.3337" stroke="#F69222" stroke-width="1.66667"
									stroke-linecap="round" stroke-linejoin="round" />
							</g>
							<defs>
								<clipPath id="clip0_168_6617">
									<rect width="14" height="15" fill="white" />
								</clipPath>
							</defs>
						</svg>
					</span>
				</a>
			<?php endif; ?>
		</div>
		<?php
		$args = array(
			'post_type' => 'lt_projects',
			'posts_per_page' => 4,
			'post_status' => 'publish',
		);

		$projects_query = new WP_Query($args);

		if ($projects_query->have_posts()): ?>
			<div class="projects-gallery flex flex-wrap justify-between gap-5">
				<?php
				$current_index = 0;

				while ($projects_query->have_posts()):
					$projects_query->the_post();
					$current_index++;

					// Native Featured Image with hardcoded placeholder fallback
					$image_url = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: 'https://via.placeholder.com/759x540';

					// Native Taxonomy Term
					$terms = get_the_terms(get_the_ID(), 'lt_project_type');
					$category_name = (!empty($terms) && !is_wp_error($terms)) ? $terms[0]->name : 'Project';
					?>
					<?php if (1 === $current_index): ?>
						<!-- Left Layout (First Post) -->
						<div class="left-layout wd:w-[56%] md:w-[49%] w-full">
							<article
								class="project-card relative w-full overflow-hidden rounded-[11px] md:rounded-3xl flex items-end group">
								<div class="project-card__media absolute inset-0 img-full-cover">
									<img class="will-change-transform transition-transform duration-700 group-hover:scale-105 origin-center"
										src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
								</div>
								<div
									class="project-card__overlay absolute inset-0 bg-[linear-gradient(0deg,rgba(9,30,66,0.8)_0%,rgba(9,30,66,0.2)_50%,rgba(0,0,0,0)_100%)] opacity-100">
								</div>
								<div
									class="project-card__content flex flex-col gap-[10px] px-3.5 md:px-[29px] md:py-[34px] py-3.5 md:gap-[4.8px] z-10">
									<span
										class="block text-caption-xs font-bold uppercase leading-[11px] text-lt-accent md:text-body md:normal-case md:leading-6">
										<?php echo esc_html($category_name); ?>
									</span>
									<h3
										class="text-body font-bold leading-[18px] text-lt-white md:text-[1.75rem] md:font-semibold md:leading-8 font-base">
										<?php the_title(); ?>
									</h3>
								</div>
								<a href="<?php the_permalink(); ?>" class="stretched-link"></a>
							</article>
						</div>
					<?php endif; ?>
					<?php if (2 === $current_index): ?>
						<!-- Right Layout Wrapper (Opens contextually at Post #2) -->
						<div class="right-layout flex flex-wrap gap-5 wd:w-[42%] md:w-[48.8%] w-full">
						<?php endif; ?>
						<?php if ($current_index > 1): ?>
							<!-- Right Layout Item (Appends dynamically for all remaining posts) -->
							<article
								class="project-card group relative w-full overflow-hidden rounded-[11px] md:rounded-3xl flex items-end">
								<div class="project-card__media absolute inset-0 img-full-cover">
									<img class="will-change-transform transition-transform duration-700 group-hover:scale-105 origin-center"
										src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
								</div>
								<div
									class="project-card__overlay absolute inset-0 bg-[linear-gradient(0deg,rgba(9,30,66,0.8)_0%,rgba(0,0,0,0)_100%)] opacity-100">
								</div>
								<div class="project-card__content w-full flex flex-col gap-[10px] p-3.5 md:gap-[4.8px] md:p-5 z-10">
									<span
										class="text-caption-xs font-bold uppercase leading-[11px] text-lt-accent md:text-caption-md md:normal-case md:leading-5">
										<?php echo esc_html($category_name); ?>
									</span>
									<h3
										class="text-body font-bold leading-[18px] text-lt-white md:text-xl md:font-semibold md:leading-8 font-base">
										<?php the_title(); ?>
									</h3>
								</div>
								<a href="<?php the_permalink(); ?>" class="stretched-link"></a>
							</article>
						<?php endif; ?>
					<?php endwhile; ?>
					<?php if ($projects_query->post_count >= 2): ?>
					</div><!-- End Right Layout Wrapper -->
				<?php endif; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</section>