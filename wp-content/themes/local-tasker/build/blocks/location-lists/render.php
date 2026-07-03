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

$class_name = 'acf-block lt-locations-list bg-lt-white py-16';

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



// ---------------------- ACF Dyamic Fields
$ll_sec_title = get_field('ll_sec_title');
$ll_sec_content = get_field('ll_sec_content');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="wd:container-bx container">
		<!-- Section Header -->
		<div class="section-header sm:mb-12 mb-[50px] text-center">
			<?php if ($ll_sec_title = get_field('ll_sec_title')): ?>
				<h2
					class="sec-title font-semi-ext text-[2rem] font-bold sm:leading-[1.2] leading-[normal] tracking-[-0.02em] text-lt-text-primary">
					<?php echo esc_html($ll_sec_title); ?>
				</h2>
			<?php endif; ?>
			<?php if ($ll_sec_content): ?>
				<div
					class="sec-text sm:mt-3 mt-2 text-lg leading-7 tracking-[-0.024em] text-lt-text-muted max-md:text-sm max-md:leading-5 max-md:tracking-[-0.011em]">
					<?php echo $ll_sec_content; ?>
				</div>
			<?php endif; ?>
		</div><!-- End of Section Header -->
		<!-- List Locations -->
		<?php
		$args = array(
			'post_type' => 'lt_location',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => 'date',
			'order' => 'DESC',
		);

		$location_query = new WP_Query($args);

		if ($location_query->have_posts()):

			?>
			<div class="lt-locations-list__grid grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-6 lg:gap-7">
				<?php while ($location_query->have_posts()):
					$location_query->the_post();
					$current_id = get_the_ID();
					$image_url = get_the_post_thumbnail_url($current_id, 'medium_large') ?: 'https://via.placeholder.com/430x180';
					$lp_number = get_field('lp_number', $current_id);
					$lp_address = get_field('lp_address', $current_id);
					$lp_phone_number = get_field('lp_phone_number', $current_id);
					$lp_opening_hours = get_field('lp_opening_hours', $current_id);
					?>
					<!-- Location Card -->
					<div id="location-<?php echo esc_attr($current_id); ?>"
						class="location-card flex flex-col overflow-hidden rounded-[10px] border border-[#E5E7EB] bg-lt-white shadow-[0px_1px_2px_-1px_rgba(0,0,0,0.1),0px_1px_3px_0px_rgba(0,0,0,0.1)]">
						<!-- Img Holder -->
						<div class="img-holder relative flex min-h-[162px] items-end overflow-hidden p-4 smlr:min-h-[192px]">
							<img class="absolute inset-0 h-full w-full object-cover" src="<?php echo esc_url($image_url); ?>"
								alt="<?php echo esc_attr(get_the_title()); ?>" />
							<div class="overlay pointer-events-none absolute inset-0 h-full w-full"
								style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 100%);"></div>
							<?php if ($lp_number): ?>
								<span
									class="project-numb max-sm:text-caption-sm absolute sm:right-[42px] right-[34px] top-[17px] z-10 inline-flex items-center rounded-full bg-[#155DFC] px-3 py-1 text-sm leading-5 tracking-[-0.011em] text-lt-white empty:hidden">
									<?php echo esc_html($lp_number); ?>
								</span>
							<?php endif; ?>
							<div class="text relative z-10 flex flex-col gap-0.5">
								<span
									class="pre-text font-base text-base font-normal leading-6 tracking-[-0.02em] text-[#BEDBFF] max-md:text-[13.5px] max-md:leading-[20px]">VIC</span>
								<h3
									class="title font-base text-2xl font-medium leading-8 tracking-[0.003em] text-lt-white max-md:text-xl max-md:leading-[27px]">
									<?php the_title(); ?>
								</h3>
							</div>
						</div>
						<!-- Text holder -->
						<div class="text-holder flex flex-col gap-6 px-6 pt-6 max-md:gap-5 max-md:px-5 max-md:pt-5">
							<div class="meta-group flex flex-col gap-3">
								<?php if ($lp_address): ?>
									<!-- Address -->
									<div class="meta-lists address flex gap-3">
										<span class="icon sm:mt-0.5 mt-[-2px] inline-flex h-5 sm:w-5 w-4 shrink-0 items-center justify-center"
											aria-hidden="true">
											<svg width="20" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
												<g clip-path="url(#clip-location-<?php echo esc_attr($current_id); ?>-addr)">
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
													<clipPath id="clip-location-<?php echo esc_attr($current_id); ?>-addr">
														<rect width="16" height="20" fill="white" />
													</clipPath>
												</defs>
											</svg>
										</span>
										<span
											class="text text-sm max-sm:text-caption-sm sm:leading-5 leading-[1.41] sm:tracking-[-0.011em] tracking-[-0.13px] text-lt-text-muted"><?php echo esc_html($lp_address); ?></span>
									</div>
								<?php endif; ?>
								<?php if ($lp_phone_number): ?>
									<!-- Tel -->
									<div class="meta-lists tel flex gap-3">
										<span class="icon sm:mt-0.5 mt-[-2px] inline-flex h-5 sm:w-5 w-4 shrink-0 items-center justify-center"
											aria-hidden="true">
											<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
												<g clip-path="url(#clip-location-<?php echo esc_attr($current_id); ?>-tel)">
													<path
														d="M18.3327 14.0753V16.5753C18.3334 16.8325 18.2802 17.0873 18.1762 17.3226C18.0721 17.5579 17.9198 17.7683 17.7294 17.94C17.539 18.1116 17.3147 18.2407 17.0711 18.3183C16.8275 18.3959 16.5699 18.4202 16.316 18.3894C13.9279 18.131 11.6309 17.3398 9.58268 16.0728C7.66562 14.9083 5.99299 13.2357 4.82851 11.3186C3.55624 9.26185 2.76488 6.95744 2.51018 4.56283C2.47943 4.30955 2.50357 4.0527 2.58075 3.8097C2.65793 3.5667 2.7863 3.34293 2.95712 3.15281C3.12794 2.96269 3.33724 2.81035 3.57154 2.70575C3.80584 2.60115 4.05977 2.54713 4.31602 2.54716H6.81602C7.25374 2.54299 7.67848 2.69141 8.01705 2.96753C8.35562 3.24365 8.58657 3.63057 8.66935 4.06283C8.82655 4.92651 9.08126 5.76947 9.42768 6.57216C9.55106 6.86151 9.58432 7.18128 9.52324 7.49045C9.46216 7.79962 9.30948 8.0835 9.08518 8.30533L7.91018 9.48033C9.04156 11.5748 10.7569 13.2901 12.8514 14.4215L14.0264 13.2465C14.2482 13.0222 14.5321 12.8695 14.8413 12.8084C15.1504 12.7474 15.4702 12.7806 15.7595 12.904C16.5622 13.2504 17.4052 13.5051 18.2688 13.6623C18.7043 13.7454 19.0937 13.9792 19.3703 14.3211C19.6469 14.663 19.7932 15.0913 19.7827 15.5303L18.3327 14.0753Z"
														stroke="#0A65FC" stroke-width="1.66667" stroke-linecap="round"
														stroke-linejoin="round" />
												</g>
												<defs>
													<clipPath id="clip-location-<?php echo esc_attr($current_id); ?>-tel">
														<rect width="20" height="20" fill="white" />
													</clipPath>
												</defs>
											</svg>
										</span>
										<span
											class="text text-sm max-sm:text-caption-sm sm:leading-5 leading-[1.41] sm:tracking-[-0.011em] tracking-[-0.13px] text-lt-text-muted"><?php echo esc_html($lp_phone_number); ?></span>
									</div>
								<?php endif; ?>
								<?php if ($lp_opening_hours): ?>
									<!-- Opening Hours -->
									<div class="meta-lists location flex gap-3">
										<span class="icon max-sm:mt-[-2px] inline-flex h-5 sm:w-5 w-4 shrink-0 items-center justify-center"
											aria-hidden="true">
											<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
												<g clip-path="url(#clip-location-<?php echo esc_attr($current_id); ?>-hours)">
													<path
														d="M9.99935 18.3337C14.6017 18.3337 18.3327 14.6027 18.3327 10.0003C18.3327 5.39795 14.6017 1.66699 9.99935 1.66699C5.39698 1.66699 1.66602 5.39795 1.66602 10.0003C1.66602 14.6027 5.39698 18.3337 9.99935 18.3337Z"
														stroke="#0A65FC" stroke-width="1.66667" stroke-linecap="round"
														stroke-linejoin="round" />
													<path d="M10 5V10L13.3333 11.6667" stroke="#0A65FC" stroke-width="1.66667"
														stroke-linecap="round" stroke-linejoin="round" />
												</g>
												<defs>
													<clipPath id="clip-location-<?php echo esc_attr($current_id); ?>-hours">
														<rect width="20" height="20" fill="white" />
													</clipPath>
												</defs>
											</svg>
										</span>
										<span
											class="text text-sm max-sm:text-caption-sm sm:leading-5 leading-[1.41] sm:tracking-[-0.011em] tracking-[-0.13px] text-lt-text-muted"><?php echo esc_html($lp_opening_hours); ?></span>
									</div>
								<?php endif; ?>
							</div>
							<!-- Speiclists -->
							<div class="spec flex flex-col gap-[10px] max-md:gap-[6.75px]">
								<span
									class="spec-label sm:text-sm text-caption-sm font-medium leading-5 tracking-[-0.011em] text-lt-text-primary">Specialties:</span>
								<?php if (have_rows('lp_specialties_lists', $current_id)): ?>
									<div class="tag flex flex-wrap gap-2">
										<?php while (have_rows('lp_specialties_lists', $current_id)):
											the_row();
											$specialties = get_sub_field('specialties', $current_id);
											?>
											<?php if ($specialties): ?>
												<span
													class="p-[5px_12px] bg-[#F3F4F6] rounded-full sm:text-caption-sm text-caption-xs leading-none text-[#364153]"><?php echo esc_html($specialties); ?></span>
											<?php endif; ?>
										<?php endwhile; ?>
									</div>
								<?php endif; ?>
							</div>
							<!-- Btn Wrap  -->
							<div class="btn-wrap pb-6 max-md:pb-5">
								<a class="group font-medium flex items-center rounded-[10px] gap-3 btn btn--brand py-[15px] hover:bg-lt-brand! hover:text-lt-white! px-2! max-sm:py-[13px] sm:tracking-[-0.31px] max-sm:text-caption-md"
									href="<?php the_permalink(); ?>">
									<span
										class="text"><?php echo esc_html(sprintf(__('Contact %s', 'local-tasker'), get_the_title())); ?></span>
									<span class="icon transition-transform duration-400 shrink-0 group-hover:translate-x-0.5 sm:w-[12px] w-[10px] mt-[1px]">
										<svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
											<g clip-path="url(#clip0_168_6617)">
												<path d="M1.16602 7.5H12.8327" stroke="#ffffff" stroke-width="1.66667"
													stroke-linecap="round" stroke-linejoin="round" />
												<path d="M7 1.66699L12.8333 7.50033L7 13.3337" stroke="#ffffff" stroke-width="1.66667"
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
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
			<!-- End of List Locations -->
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</section>