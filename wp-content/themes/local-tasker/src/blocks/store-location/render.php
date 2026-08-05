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
$class_name = 'acf-block lt-store-location py-16 bg-lt-white';

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

?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="container flex flex-col gap-[27px] max-md:gap-5">
		<?php if ($section_title): ?>
			<h2
				class="font-semi-ext text-[2rem] font-bold leading-[1.3] tracking-[0.0022em] text-lt-onyx md:text-[2.5rem] md:leading-[3.375rem] md:tracking-[-0.0225em]">
				<?php echo esc_html($section_title); ?>
			</h2>
		<?php endif; ?>
		<?php
		$args = array(
			'post_type' => 'lt_store_location',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => 'date',
			'order' => 'DESC',
		);

		$locations_query = new WP_Query($args);


		if ($locations_query->have_posts()): ?>
			<div class="store-location__grid grid wd:grid-cols-4 md:grid-cols-3 smlr:grid-cols-2 grid-cols-1 bigLp:gap-5 wd:gap-4 sm:gap-6 gap-4">
				<?php while ($locations_query->have_posts()):
					$locations_query->the_post();

					$image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium_large') ?: 'https://via.placeholder.com/430x180';
					$location = get_field('location', get_the_ID());
					$time = get_field('time', get_the_ID());
					$address = get_field('address', get_the_ID());
					?>
					<article id="location-<?php the_ID(); ?>" <?php post_class('group showroom-card flex flex-col overflow-hidden rounded-[12px] border border-[#E5E7EB] bg-lt-white shadow-[0px_1px_2px_0px_rgba(0,0,0,0.1)] md:rounded-[15px] relative'); ?>>
						<div class="showroom-card__media h-[150px] shrink-0 overflow-hidden md:h-[180px] img-full-cover">
							<img
								class="will-change-transform transition-transform duration-600 group-hover:scale-105 origin-center"" src="
								<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
						</div>
						<div class="showroom-card__body flex flex-1 flex-col justify-between sm:gap-[27px] gap-5 sm:px-6 sm:py-[22px_18px] p-5">
							<div class="flex flex-col gap-5 sm:gap-[25px]">
								<h3 class="sm:text-body text-caption-md font-base font-bold leading-[1.37] text-lt-onyx sm:tracking-[0.02em]">
									<?php the_title(); ?>
								</h3>
								<ul class="flex flex-col gap-2.5 sm:gap-3 m-0">
									<?php if ($location): ?>
										<li class="flex items-start gap-3">
											<span class="sm:mt-0.5 shrink-0 text-lt-text-muted max-sm:w-[16px]" aria-hidden="true">
												<svg width="16" height="20" viewBox="0 0 16 20" fill="none"
													xmlns="http://www.w3.org/2000/svg">
													<g clip-path="url(#clip0_173_6541)">
														<path
															d="M14.6654 8.33366C14.6654 12.4945 10.0495 16.8278 8.49953 18.1662C8.35513 18.2747 8.17936 18.3335 7.9987 18.3335C7.81803 18.3335 7.64226 18.2747 7.49786 18.1662C5.94786 16.8278 1.33203 12.4945 1.33203 8.33366C1.33203 6.56555 2.03441 4.86986 3.28465 3.61961C4.5349 2.36937 6.23059 1.66699 7.9987 1.66699C9.76681 1.66699 11.4625 2.36937 12.7127 3.61961C13.963 4.86986 14.6654 6.56555 14.6654 8.33366Z"
															stroke="#0A65FC" stroke-width="1.66667" stroke-linecap="round"
															stroke-linejoin="round" />
														<path
															d="M8 10.833C9.38071 10.833 10.5 9.71372 10.5 8.33301C10.5 6.9523 9.38071 5.83301 8 5.83301C6.61929 5.83301 5.5 6.9523 5.5 8.33301C5.5 9.71372 6.61929 10.833 8 10.833Z"
															stroke="#0A65FC" stroke-width="1.66667" stroke-linecap="round"
															stroke-linejoin="round" />
													</g>
													<defs>
														<clipPath id="clip0_173_6541">
															<rect width="16" height="20" fill="white" />
														</clipPath>
													</defs>
												</svg>
											</span>
											<span class="sm:text-body text-caption-sm font-medium leading-[1.4] text-lt-text-secondary">
												<?php echo esc_html($location); ?>
											</span>
										</li>
									<?php endif; ?>
									<?php if ($time): ?>
										<li class="flex items-start gap-3">
											<span class="sm:mt-0.5 shrink-0 text-lt-text-muted max-sm:w-[16px]" aria-hidden="true">
												<svg width="20" height="20" viewBox="0 0 20 20" fill="none"
													xmlns="http://www.w3.org/2000/svg">
													<g clip-path="url(#clip0_170_6645)">
														<path
															d="M9.99935 18.3337C14.6017 18.3337 18.3327 14.6027 18.3327 10.0003C18.3327 5.39795 14.6017 1.66699 9.99935 1.66699C5.39698 1.66699 1.66602 5.39795 1.66602 10.0003C1.66602 14.6027 5.39698 18.3337 9.99935 18.3337Z"
															stroke="#0A65FC" stroke-width="1.66667" stroke-linecap="round"
															stroke-linejoin="round" />
														<path d="M10 5V10L13.3333 11.6667" stroke="#0A65FC" stroke-width="1.66667"
															stroke-linecap="round" stroke-linejoin="round" />
													</g>
													<defs>
														<clipPath id="clip0_170_6645">
															<rect width="20" height="20" fill="white" />
														</clipPath>
													</defs>
												</svg>
											</span>
											<span class="sm:text-body text-caption-sm font-medium leading-[1.4] text-lt-text-secondary">
												<?php echo $time; ?>
											</span>
										</li>
									<?php endif; ?>
									<?php if ($address): ?>
										<li class="flex items-start gap-3">
											<span class="sm:mt-0.5 shrink-0 text-lt-text-muted max-sm:w-[16px]" aria-hidden="true">
												<svg class="w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0A65FC" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round">
														<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
													</svg>
											</span>
											<span class="sm:text-body text-caption-sm font-medium leading-[1.4] text-lt-text-secondary">
												<?php echo esc_html($address); ?>
											</span>
										</li>
									<?php endif; ?>
								</ul>
							</div>
							<a href="<?php the_permalink(); ?>"
								class="showroom-card__link inline-flex w-fit items-center gap-2 sm:text-caption-md text-caption-sm font-bold leading-[1.34] text-lt-accent md:text-[0.875rem]">
								View Location </a>
						</div>
						<a href="<?php the_permalink(); ?>" class="stretched-link"></a>
					</article>
				<?php endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</section>