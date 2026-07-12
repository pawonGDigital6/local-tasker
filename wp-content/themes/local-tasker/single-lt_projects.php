<?php
/**
 * The template for displaying all single project
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package local-tasker
 */

get_header();
?>
<main id="primary" class="site-main">
	<?php
	while (have_posts()):
		the_post();

		// ----------------------------------------- Post Data
		$post_id = get_the_ID();
		$post_url = get_permalink();
		$post_title = get_the_title();
		$post_thumb = get_the_post_thumbnail_url($post_id, 'full');
		// $all_cats = get_the_category();
	
		// ----------------------------------------- ACF Fields Data
		$po_heading = get_field('po_heading');
		$po_content = get_field('po_content');

		$cb_sec_title = get_field('cb_sec_title');
		$cb_content = get_field('cb_content');
		$pr_heading = get_field('pr_heading');

		$os_sec_title = get_field('os_sec_title');
		$os_sec_text = get_field('os_sec_text');

		$key_result_head = get_field('key_result_head');

		$pg_sec_title = get_field('pg_sec_title');
		$pg_images = get_field('pg_images');
		$size = 'full';

		$test_sec_title = get_field('test_sec_title');
		$author_short = get_field('author_short');
		$proj_testi_content = get_field('proj_testi_content');
		$proj_testi_author = get_field('proj_testi_author');
		$proj_testi_designation = get_field('proj_testi_designation');
		?>
		<!-- Hero Section -->
		<section class="lt-inner-hero-wd relative flex items-end md:py-14 py-8 md:min-h-[441px] min-h-[450px]">
			<!-- Overlay -->
			<div class="overlay absolute inset-0 w-full h-full pointer-events-none z-1"
				style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.5) 50%, rgba(0, 0, 0, 0) 100%);">
			</div>
			<!-- BG Img -->
			<?php if ($post_thumb): ?>
				<div class="abs-img absolute inset-0 w-full h-full z-0 pointer-events-none">
					<img class="absolute w-full h-full object-cover" src="<?php echo esc_url($post_thumb); ?>"
						alt="<?php echo esc_attr($post_title); ?>" loading="lazy" decoding="async">
				</div>
			<?php endif; ?>
			<div class="wd:container-bx container">
				<div class="content md:max-w-[896px] text-lt-white relative z-1">
					<div class="flex flex-wrap gap-2 sm:mb-6 mb-5">
						<div
							class="cat p-[7px_13px] bg-lt-brand rounded-[50px] inline-flex sm:text-caption-md text-caption-sm leading-none tracking-[-0.15px]">
							Residential Project </div>
					</div>
					<!-- Title -->
					<?php if ($post_title): ?>
						<h1
							class="sm:text-h2 text-[1.875rem] leading-[1.20] sm:tracking-[-0.02em] tracking-[0.4px] sm:mb-6 mb-[13px]">
							<?php echo esc_html($post_title); ?>
						</h1>
					<?php endif; ?>
					<!-- Content -->
					<!-- Meta -->
					<div
						class="meta flex flex-wrap items-center gap-x-[27px] max-sm:gap-x-[34px] gap-y-2 max-sm:text-caption-sm text-lt-white tracking-[0.02em] mb-2">
						<span class="locations flex items-center sm:gap-2 gap-1.5">
							<span class="icon">
								<svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_50552_22836)">
										<path
											d="M13.3346 6.66634C13.3346 9.99501 9.64197 13.4617 8.40197 14.5323C8.28645 14.6192 8.14583 14.6662 8.0013 14.6662C7.85677 14.6662 7.71615 14.6192 7.60064 14.5323C6.36064 13.4617 2.66797 9.99501 2.66797 6.66634C2.66797 5.25185 3.22987 3.8953 4.23007 2.89511C5.23026 1.89491 6.58681 1.33301 8.0013 1.33301C9.41579 1.33301 10.7723 1.89491 11.7725 2.89511C12.7727 3.8953 13.3346 5.25185 13.3346 6.66634Z"
											stroke="#ffffff" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
										<path
											d="M8 8.66699C9.10457 8.66699 10 7.77156 10 6.66699C10 5.56242 9.10457 4.66699 8 4.66699C6.89543 4.66699 6 5.56242 6 6.66699C6 7.77156 6.89543 8.66699 8 8.66699Z"
											stroke="#ffffff" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
									</g>
									<defs>
										<clipPath id="clip0_50552_22836">
											<rect width="16" height="16" fill="white" />
										</clipPath>
									</defs>
								</svg>
							</span>
							<span class="text">Seattle, WA</span>
						</span>
						<!-- Posted On -->
						<time class="meta__compl-by flex items-center sm:gap-2 gap-1.5">
							<span class="icon">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M6.66797 1.66699V5.00033" stroke="#E5E7EB" stroke-width="1.66667"
										stroke-linecap="round" stroke-linejoin="round" />
									<path d="M13.332 1.66699V5.00033" stroke="#E5E7EB" stroke-width="1.66667" stroke-linecap="round"
										stroke-linejoin="round" />
									<path
										d="M15.8333 3.33301H4.16667C3.24619 3.33301 2.5 4.0792 2.5 4.99967V16.6663C2.5 17.5868 3.24619 18.333 4.16667 18.333H15.8333C16.7538 18.333 17.5 17.5868 17.5 16.6663V4.99967C17.5 4.0792 16.7538 3.33301 15.8333 3.33301Z"
										stroke="#E5E7EB" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
									<path d="M2.5 8.33301H17.5" stroke="#E5E7EB" stroke-width="1.66667" stroke-linecap="round"
										stroke-linejoin="round" />
								</svg>
							</span>
							<span class="text">Completed March 2026</span>
						</time>
						<!-- Read -->
						<span class="meta__sq-ft flex items-center sm:gap-2 gap-1.5">
							<span class="icon shrink-0" aria-hidden="true">
								<svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
									stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
									<path
										d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z" />
									<path d="m14.5 12.5 2-2" />
									<path d="m11.5 9.5 2-2" />
									<path d="m8.5 6.5 2-2" />
									<path d="m17.5 15.5 2-2" />
								</svg>
							</span>
							<span class="text"> 2,400 sq ft </span>
						</span><!-- End of Read -->
					</div><!-- End Meta -->
				</div>
		</section><!-- End of Hero -->
		<?php
		// Reusable checklist icon (Our Solution). Static for now — dynamic ACF later.
		$icon_check = '<svg class="w-5 h-5 shrink-0 text-lt-brand mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>';
		?><!-- Project Overview -->
		<section class="lt-project-overview bg-lt-white sm:py-16 py-12">
			<div class="wd:container-bx container">
				<div class="flex flex-col lg:flex-row lg:items-center gap-10 lg:gap-12">
					<!-- Intro -->
					<div class="flex-1 flex flex-col gap-6">
						<?php if ($po_heading): ?>
							<h2
								class="font-semi-ext text-[24px] sm:text-[2rem] font-bold leading-[1.2] tracking-[-0.02em] text-lt-onyx">
								<?php echo esc_html($po_heading); ?>
							</h2>
						<?php endif; ?>
						<?php if ($po_content): ?>
							<div class="text leading-[1.5] tracking-[0.02em] [&_*:last-child]:mb-0">
								<?php echo $po_content; ?>
							</div>
						<?php endif; ?>
						<div class="flex flex-wrap gap-4">
							<span
								class="inline-flex items-center gap-2 rounded-[10px] bg-[#eff6ff] max-sm:text-caption-md px-4 py-2 text-base leading-6 text-[#1447e6]">
								<svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
									stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
									<path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6" />
									<path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18" />
									<path d="M4 22h16" />
									<path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22" />
									<path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22" />
									<path d="M18 2H6v7a6 6 0 0 0 12 0V2Z" />
								</svg> Award Winning </span>
							<span
								class="inline-flex items-center gap-2 rounded-[10px] bg-[#f0fdf4] max-sm:text-caption-md px-4 py-2 text-base leading-6 text-[#008236]">
								<svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
									stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
									<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" />
									<path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12" />
								</svg> Eco-Friendly </span>
						</div>
					</div>
					<!-- Stat Row -->
					<div class="grid grid-cols-1 xs:grid-cols-2 sm:gap-6 gap-4 lg:w-[584px] lg:shrink-0">
						<?php if (have_rows('po_icon_head_list')): ?>
							<?php while (have_rows('po_icon_head_list')):
								the_row();
								$po_list_icon = get_sub_field('po_list_icon');
								$po_list_pre_head = get_sub_field('po_list_pre_head');
								$po_list_heading = get_sub_field('po_list_heading');
								$size = 'full';
								?>
								<!-- List -->
								<div
									class="flex flex-col gap-4 sm:rounded-[10px] rounded-[18px] bg-[#f9fafb] p-6 max-sm:border border-[#F3F4F6]">
									<div class="icon w-8 h-8">
										<?php
										if ($po_list_icon) {
											echo wp_get_attachment_image($po_list_icon, $size);
										}
										; ?>
									</div>
									<div class="flex flex-col gap-2">
										<?php if ($po_list_pre_head): ?>
											<span
												class="sm:text-base text-caption-sm leading-[1.5] tracking-[0.02em] text-lt-onyx"><?php echo esc_html($po_list_pre_head); ?></span>
										<?php endif; ?>
										<?php if ($po_list_heading): ?>
											<span
												class="text-body-xl font-semi-ext text-2xl font-bold leading-[1.2] tracking-[-0.02em] text-lt-onyx"><?php echo esc_html($po_list_heading); ?></span>
										<?php endif; ?>
									</div>
								</div><!-- Ebd of List -->
							<?php endwhile; ?>
						<?php endif; ?>
					</div><!-- End of Stat Row -->
				</div>
			</div>
		</section><!-- End Project Overview -->
		<?php
		// Only render Client Brief when at least one of its fields has content.
		if ($cb_sec_title || $cb_content || $pr_heading || have_rows('pr_lists')):
			?>
		<!-- Client Brief -->
		<section class="lt-client-brief bg-[#f9fafb] sm:py-16 py-12">
			<div class="wd:container-bx container">
				<div class="mx-auto max-w-[920px] flex flex-col gap-6">
					<!-- Heading -->
					<div class="flex items-center gap-3">
						<svg class="w-8 h-8 shrink-0 text-lt-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor"
							stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
						</svg>
						<?php if ($cb_sec_title): ?>
							<h2
								class="font-semi-ext text-[24px] sm:text-[2rem] font-bold leading-[1.2] tracking-[-0.02em] text-lt-onyx">
								<?php echo esc_html($cb_sec_title); ?>
							</h2>
						<?php endif; ?>
					</div>
					<!-- Card -->
					<div class="flex flex-col gap-8 rounded-[10px] bg-lt-white theme-shadow sm:p-8 p-6">
						<div class="flex flex-col gap-4 sm:mb-4">
							<?php if ($cb_content): ?>
								<div
									class="cb__content-area wd:max-w-[832px] cs-dot-list text-base leading-[1.5] tracking-[0.02em] [_&>mb-4]">
									<?php echo $cb_content; ?>
								</div>
							<?php endif; ?>
						</div>
						<hr class="border-0 h-px w-full bg-[#e5e7eb] m-0" />
						<!-- Lists protires -->
						<div class="flex flex-col gap-6">
							<?php if ($pr_heading): ?>
								<h3 class="font-base text-xl font-bold leading-[1.5] text-lt-onyx">
									<?php echo esc_html($pr_heading); ?>
								</h3>
							<?php endif; ?>
							<!-- Lists -->
							<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
								<?php if (have_rows('pr_lists')): ?>
									<?php while (have_rows('pr_lists')):
										the_row();
										$row_index = get_row_index();
										$formatted_index = sprintf('%02d', $row_index);
										$pr_list_heading = get_sub_field('pr_list_heading');
										$pr_list_text = get_sub_field('pr_list_text');
										?>
										<!-- List Item -->
										<div class="flex flex-col gap-6">
											<span
												class="font-semi-ext text-[2rem] font-bold leading-[1.2] tracking-[-0.02em] text-lt-brand"><?php echo $formatted_index; ?></span>
											<div class="flex flex-col gap-4">
												<?php if ($pr_list_heading): ?>
													<h4 class="font-base text-lg font-semibold leading-[1.5] text-lt-onyx">
														<?php echo esc_html($pr_list_heading); ?>
													</h4>
												<?php endif; ?>
												<?php if ($pr_list_text): ?>
													<div class="text-base leading-[1.5] tracking-[0.02em] mb-0">
														<?php echo esc_html($pr_list_text); ?>
													</div>
												<?php endif; ?>
											</div>
										</div>
									<?php endwhile; ?>
								<?php endif; ?>
							</div><!-- End of Lists -->
						</div>
					</div>
				</div>
			</div>
		</section><!-- End Client Brief -->
		<?php endif; ?>
		<?php
		// Only render Our Solution when at least one of its fields has content.
		if ($os_sec_title || $os_sec_text || have_rows('solution_block_lists') || $key_result_head || have_rows('key_result_list')):
			?>
		<!-- Our Solution -->
		<section class="lt-our-solution bg-lt-white sm:py-16 py-12">
			<div class="wd:container-bx container">
				<div class="mx-auto max-w-[896px] flex flex-col gap-10">
					<!-- Intro -->
					<div class="flex flex-col gap-6">
						<div class="flex gap-3">
							<svg class="w-8 h-8 shrink-0 text-lt-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor"
								stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
								<path
									d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5" />
								<path d="M9 18h6" />
								<path d="M10 22h4" />
							</svg>
							<?php if ($os_sec_title): ?>
								<h2
									class="font-semi-ext text-[24px] sm:text-[2rem] font-bold leading-[1.2] tracking-[-0.02em] text-lt-onyx">
									<?php echo esc_html($os_sec_title); ?>
								</h2>
							<?php endif; ?>
						</div>
						<?php if ($os_sec_text): ?>
							<div class="text-base leading-[1.5] tracking-[0.02em] "><?php echo $os_sec_text; ?></div>
						<?php endif; ?>
					</div>
					<!-- Two columns -->
					<div class="grid grid-cols-1 md:grid-cols-2 sm:gap-8 gap-4">
						<?php if (have_rows('solution_block_lists')): ?>
							<?php while (have_rows('solution_block_lists')):
								the_row();
								$sb_heading = get_sub_field('sb_heading');
								$sb_content = get_sub_field('sb_content');
								?>
								<div class="flex flex-col sm:gap-6 gap-4 sm:rounded-[10px] rounded-[16px] bg-[#eff6ff] p-6 even:bg-[#f9fafb]">
									<?php if ($sb_heading): ?>
										<h3 class="font-base sm:text-xl text-body font-bold leading-[1.5] text-lt-onyx">
											<?php echo esc_html($sb_heading); ?>
										</h3>
									<?php endif; ?>
									<?php if ($sb_content): ?>
										<div class="sb-text tick-ul-two max-sm:text-caption-md"><?php echo $sb_content; ?></div>
									<?php endif; ?>
								</div>
							<?php endwhile; ?>
						<?php endif; ?>
					</div>
					<!-- Key Results -->
					<div class="flex flex-col sm:gap-8 gap-4 sm:rounded-[10px] rounded-[24px] bg-gradient-to-r from-[#155dfc] to-[#1447e6] sm:p-8 p-6">
						<?php if ($key_result_head): ?>
							<h3 class="font-base text-xl font-medium leading-[1.4] tracking-[-0.02em] text-lt-white max-xs:text-center">
								<?php echo esc_html($key_result_head); ?>
							</h3>
						<?php endif; ?>
						<div class="grid grid-cols-1 sm:grid-cols-3 xs:grid-cols-2 gap-6">
							<?php if (have_rows('key_result_list')): ?>
								<?php while (have_rows('key_result_list')):
									the_row();
									$krl_head = get_sub_field('krl_head');
									$krl_text = get_sub_field('krl_text');
									?>
									<div class="flex flex-col sm:gap-4 gap-1 max-xs:text-center">
										<?php if ($krl_head): ?>
											<span
												class="font-semi-ext text-[2rem] font-bold leading-[1.2] tracking-[-0.02em] text-lt-white"><?php echo esc_html($krl_head); ?></span>
										<?php endif; ?>
										<?php if ($krl_text): ?>
											<span
												class="sm:text-base text-caption-sm leading-[1.5] tracking-[0.02em] text-[#dbeafe]"><?php echo esc_html($krl_text); ?></span>
										<?php endif; ?>
									</div>
								<?php endwhile; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</section><!-- End Our Solution -->
		<?php endif; ?>
		<?php
		// Only render the gallery when there is at least one image.
		if ($pg_images):
			?>
		<!-- Project Gallery -->
		<section class="lt-project-gallery bg-[#f9fafb] sm:py-16 py-12">
			<div class="wd:container-bx sm:container">
				<div class="flex flex-col sm:gap-8 gap-5">
					<?php if ($pg_sec_title): ?>
						<h2
							class="font-semi-ext text-[24px] sm:text-[2rem] font-bold leading-[1.2] tracking-[-0.02em] text-lt-onyx max-sm:px-4">
							<?php echo esc_html($pg_sec_title); ?>
						</h2>
					<?php endif; ?>
					<!-- Project Gallery -->
					<div class="main-slide-holder relative">
						<div class="gallery-main-slide swiper w-full">
							<div class="swiper-wrapper">
								<?php
								if ($pg_images): ?>
									<?php foreach ($pg_images as $image_id):
										$url = wp_get_attachment_url($image_id);
										$caption = wp_get_attachment_caption($image_id);
										?>
										<div class="swiper-slide">
											<!-- Featured -->
											<figure
												class="relative m-0 smlr:aspect-[1280/684] aspect-[3/3.09] w-full overflow-hidden sm:rounded-[10px] bg-lt-white-lilac mb-0!">
												<a href="<?php echo $url; ?>" data-fancybox="gallery">
													<?php echo wp_get_attachment_image($image_id, $size, false, array('class' => 'absolute inset-0 h-full w-full object-cover')); ?>
													<span
														class="pointer-events-none absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/70 to-transparent">
													</span>
												</a>
												<?php if ($caption): ?>
													<figcaption
														class="absolute inset-x-0 bottom-0 sm:p-6 p-4 text-lt-white font-base text-lg font-medium">
														<?php echo esc_html($caption); ?>
													</figcaption>
												<?php endif; ?>
												</a>
											</figure>
										</div>
									<?php endforeach; ?>
								<?php endif; ?>
							</div><!-- End of Swiper Wrapper -->
						</div><!-- End of main Slide -->
						<div
							class="sm:hidden main-slide-nav flex justify-between absolute top-1/2 px-4 -translate-y-1/2 w-full z-4">
							<!-- prev -->
							<div
								class="slide-nav prev w-8 h-8 opacity-80 rounded-[50%] bg-white flex items-center justify-center p-2"
								style="box-shadow: 0px 1px 2px -1px #0000001A, 0px 1px 3px 0px #0000001A;">
								<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_436_6095)">
										<path d="M6.16797 10.828L1.17049 5.83048L6.16797 0.833008" stroke="#1E2939"
											stroke-width="1.66583" stroke-linecap="round" stroke-linejoin="round" />
									</g>
									<defs>
										<clipPath id="clip0_436_6095">
											<rect width="7" height="12" fill="white" transform="matrix(-1 0 0 1 7 0)" />
										</clipPath>
									</defs>
								</svg>
							</div>
							<!-- Next -->
							<div
								class="slide-nav next w-8 h-8 opacity-80 rounded-[50%] bg-white flex items-center justify-center p-2"
								style="box-shadow: 0px 1px 2px -1px #0000001A, 0px 1px 3px 0px #0000001A;">
								<svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M0.832031 10.828L5.82951 5.83048L0.832031 0.833008" stroke="#1E2939"
										stroke-width="1.66583" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</div>
						</div>
					</div>
					<!-- Thumbnails -->
					<div class="holder max-sm:pl-4">
						<div class="gallery-thumbnail swiper w-full">
							<div class="swiper-wrapper">
								<?php
								if ($pg_images): ?>
									<?php foreach ($pg_images as $image_id): ?>
										<div class="swiper-slide">
											<div
												class="gallery-thumbnail__card cursor-pointer group relative block aspect-square w-full overflow-hidden rounded-[10px] bg-lt-white-lilac focus-visible:outline-2 focus-visible:outline-lt-brand opacity-70"
												aria-label="View gallery image">
												<?php echo wp_get_attachment_image($image_id, $size, false, array('class' => 'absolute inset-0 h-full w-full object-cover transition-transform duration-400 group-hover:scale-105 will-change-transform')); ?>
											</div>
										</div>
									<?php endforeach; ?>
								<?php endif; ?>
							</div><!-- End of Swiper Wrapper -->
						</div><!-- End of Thumbnails -->
					</div>
				</div>
			</div>
		</section><!-- End Project Gallery -->
		<?php endif; ?>
		<?php
		// Only render the testimonial when the quote or stats have content.
		// (The star rating and avatar are decorative, so they alone don't count.)
		if ($test_sec_title || $proj_testi_content || $proj_testi_author || $proj_testi_designation || have_rows('counts_lists')):
			?>
		<!-- Client Testimonial -->
		<section class="lt-project-testimonials sm:pt-16 py-12 sm:pb-23">
			<div class="wd:container-bx container">
				<div class="mx-auto max-w-[920px] flex flex-col gap-12">
					<?php if ($test_sec_title): ?>
						<h2
							class="font-semi-ext text-[24px] sm:text-[2rem] font-bold leading-[1.2] tracking-[-0.02em] text-lt-onyx text-center">
							<?php echo esc_html($test_sec_title); ?>
						</h2>
					<?php endif; ?>
					<!-- Quote card -->
					<div class="quote-card relative rounded-[10px] sm:p-10 p-6"
						style="background: linear-gradient(135deg, #EFF6FF 0%, #FFFFFF 100%);">
						<svg class="absolute sm:left-6 sm:top-6 left-3 top-3 w-12 h-12" width="48" height="48" viewBox="0 0 48 48" fill="none"
							xmlns="http://www.w3.org/2000/svg">
							<path
								d="M32 6C30.9391 6 29.9217 6.42143 29.1716 7.17157C28.4214 7.92172 28 8.93913 28 10V22C28 23.0609 28.4214 24.0783 29.1716 24.8284C29.9217 25.5786 30.9391 26 32 26C32.5304 26 33.0391 26.2107 33.4142 26.5858C33.7893 26.9609 34 27.4696 34 28V30C34 31.0609 33.5786 32.0783 32.8284 32.8284C32.0783 33.5786 31.0609 34 30 34C29.4696 34 28.9609 34.2107 28.5858 34.5858C28.2107 34.9609 28 35.4696 28 36V40C28 40.5304 28.2107 41.0391 28.5858 41.4142C28.9609 41.7893 29.4696 42 30 42C33.1826 42 36.2348 40.7357 38.4853 38.4853C40.7357 36.2348 42 33.1826 42 30V10C42 8.93913 41.5786 7.92172 40.8284 7.17157C40.0783 6.42143 39.0609 6 38 6H32Z"
								stroke="#BEDBFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
							<path
								d="M10 6C8.93913 6 7.92172 6.42143 7.17157 7.17157C6.42143 7.92172 6 8.93913 6 10V22C6 23.0609 6.42143 24.0783 7.17157 24.8284C7.92172 25.5786 8.93913 26 10 26C10.5304 26 11.0391 26.2107 11.4142 26.5858C11.7893 26.9609 12 27.4696 12 28V30C12 31.0609 11.5786 32.0783 10.8284 32.8284C10.0783 33.5786 9.06087 34 8 34C7.46957 34 6.96086 34.2107 6.58579 34.5858C6.21071 34.9609 6 35.4696 6 36V40C6 40.5304 6.21071 41.0391 6.58579 41.4142C6.96086 41.7893 7.46957 42 8 42C11.1826 42 14.2348 40.7357 16.4853 38.4853C18.7357 36.2348 20 33.1826 20 30V10C20 8.93913 19.5786 7.92172 18.8284 7.17157C18.0783 6.42143 17.0609 6 16 6H10Z"
								stroke="#BEDBFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
						<div class="relative flex flex-col items-center gap-6">
							<div class="flex flex-col items-center gap-6">
								<div class="flex items-center gap-1" aria-label="Rated 5 out of 5">
									<?php for ($s = 0; $s < 5; $s++): ?>
										<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path
												d="M10.5268 1.29489C10.5706 1.20635 10.6383 1.13183 10.7223 1.07972C10.8062 1.02761 10.903 1 11.0018 1C11.1006 1 11.1974 1.02761 11.2813 1.07972C11.3653 1.13183 11.433 1.20635 11.4768 1.29489L13.7868 5.97389C13.939 6.28186 14.1636 6.5483 14.4414 6.75035C14.7192 6.95239 15.0419 7.08401 15.3818 7.13389L20.5478 7.88989C20.6457 7.90408 20.7376 7.94537 20.8133 8.00909C20.8889 8.07282 20.9452 8.15644 20.9758 8.2505C21.0064 8.34456 21.0101 8.4453 20.9864 8.54133C20.9627 8.63736 20.9126 8.72485 20.8418 8.79389L17.1058 12.4319C16.8594 12.672 16.6751 12.9684 16.5686 13.2955C16.4622 13.6227 16.4369 13.9708 16.4948 14.3099L17.3768 19.4499C17.3941 19.5477 17.3835 19.6485 17.3463 19.7406C17.3091 19.8327 17.2467 19.9125 17.1663 19.9709C17.086 20.0293 16.9908 20.0639 16.8917 20.0708C16.7926 20.0777 16.6935 20.0566 16.6058 20.0099L11.9878 17.5819C11.6835 17.4221 11.345 17.3386 11.0013 17.3386C10.6576 17.3386 10.3191 17.4221 10.0148 17.5819L5.3978 20.0099C5.31013 20.0563 5.2112 20.0772 5.11225 20.0701C5.0133 20.0631 4.91832 20.0285 4.83809 19.9701C4.75787 19.9118 4.69563 19.8321 4.65846 19.7401C4.62128 19.6481 4.61066 19.5476 4.6278 19.4499L5.5088 14.3109C5.567 13.9716 5.54178 13.6233 5.43534 13.2959C5.32889 12.9686 5.14441 12.672 4.8978 12.4319L1.1618 8.79489C1.09039 8.72593 1.03979 8.63829 1.01576 8.54197C0.991731 8.44565 0.995237 8.34451 1.02588 8.25008C1.05652 8.15566 1.11307 8.07174 1.18908 8.00788C1.26509 7.94402 1.3575 7.90279 1.4558 7.88889L6.6208 7.13389C6.96106 7.08439 7.28419 6.95295 7.56238 6.75088C7.84058 6.54881 8.0655 6.28216 8.2178 5.97389L10.5268 1.29489Z"
												fill="#155DFC" stroke="#155DFC" stroke-width="2" stroke-linecap="round"
												stroke-linejoin="round" />
										</svg>
									<?php endfor; ?>
								</div>
								<?php if ($proj_testi_content): ?>
									<div
										class="quote-card__content m-0 max-w-[800px] text-center font-base font-normal sm:text-[22px] leading-[1.50] text-lt-text-primary tracking-normal">
										<?php echo $proj_testi_content; ?>
									</div>
								<?php endif; ?>
							</div>
							<figure class="flex items-center gap-4 m-0!">
								<?php if ($author_short): ?>
									<span
										class="flex sm:h-16 sm:w-16 w-12 h-12  shrink-0 items-center justify-center rounded-full bg-lt-brand font-base sm:text-2xl text-body-lg font-bold text-lt-white"><?php echo esc_html($author_short); ?></span>
								<?php endif; ?>
								<div class="text-left flex flex-col">
									<?php if ($proj_testi_author): ?>
										<span
											class="sm:font-base text-caption-md text-lg font-semibold leading-[1.5] text-lt-onyx"><?php echo esc_html($proj_testi_author); ?></span>
									<?php endif; ?>
									<?php if ($proj_testi_designation): ?>
										<span
											class="sm:text-base text-caption-sm leading-[1.5] text-lt-text-muted"><?php echo esc_html($proj_testi_designation); ?></span>
									<?php endif; ?>
								</div>
							</figure>
						</div>
					</div>
					<!-- Stats -->
					<div class="grid grid-cols-3 gap-6">
						<?php if (have_rows('counts_lists')): ?>
							<?php while (have_rows('counts_lists')):
								the_row();
								$proj_cnt_number = get_sub_field('proj_cnt_number');
								$proj_cnt_title = get_sub_field('proj_cnt_title');
								?>
								<div class="flex flex-col items-center gap-2 text-center">
									<?php if ($proj_cnt_number): ?>
										<span
										class="font-semi-ext text-[2rem] font-bold leading-[1.2] tracking-[-0.02em] text-lt-brand"><?php echo esc_html($proj_cnt_number); ?></span>
									<?php endif; ?>
									<?php if ($proj_cnt_title): ?>
										<span class="text-caption-md text-lt-text-muted"><?php echo esc_html($proj_cnt_title); ?></span>
									<?php endif; ?>
								</div>
							<?php endwhile; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section><!-- End Client Testimonial -->
		<?php endif; ?>
		<?php
		// ─── Related Projects ─────────────────────────────────────────────────
		// Prefer projects that share an `lt_project_type` term with the current
		// one; if none exist, fall back to the most recent projects. The section
		// is skipped entirely when there is nothing to show.
		$rp_terms = wp_get_post_terms($post_id, 'lt_project_type', ['fields' => 'ids']);

		$rp_args = [
			'post_type'           => 'lt_projects',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'post__not_in'        => [$post_id],
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
		];

		if (!empty($rp_terms) && !is_wp_error($rp_terms)) {
			$rp_args['tax_query'] = [
				[
					'taxonomy' => 'lt_project_type',
					'field'    => 'term_id',
					'terms'    => $rp_terms,
				],
			];
		}

		$related_query = new WP_Query($rp_args);

		// Fallback: no same-type projects → show recent projects instead.
		if (!$related_query->have_posts()) {
			unset($rp_args['tax_query']);
			$related_query = new WP_Query($rp_args);
		}

		if ($related_query->have_posts()):
			$projects_archive = get_post_type_archive_link('lt_projects');
			?>
			<!-- Related Projects -->
			<section class="lt-related-projects bg-[#f9fafb] sm:py-16 py-12">
				<div class="wd:container-bx container">
					<div class="flex flex-col gap-8">
						<div class="flex items-center justify-between gap-4">
							<h2
								class="font-semi-ext text-[24px] sm:text-[2rem] font-bold leading-[1.2] tracking-[-0.02em] text-lt-onyx">
								Related Projects</h2>
							<?php if ($projects_archive): ?>
								<a href="<?php echo esc_url($projects_archive); ?>"
									class="group inline-flex shrink-0 items-center gap-2 text-base font-medium text-lt-brand">
									<span>View All</span>
									<svg class="w-5 h-5 shrink-0 transition-transform duration-400 group-hover:translate-x-0.5"
										viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
										stroke-linejoin="round" aria-hidden="true">
										<path d="M5 12h14" />
										<path d="m12 5 7 7-7 7" />
									</svg>
								</a>
							<?php endif; ?>
						</div>
						<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
							<?php
							while ($related_query->have_posts()):
								$related_query->the_post();
								get_template_part('template-parts/blocks/project-filter/card');
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					</div>
				</div>
			</section><!-- End Related Projects -->
		<?php endif; ?>
		<!-- Main Content -->
		<?php the_content(); ?>
		<!-- End of Main Content -->
		<?php
	endwhile; // End of the loop.
	?>
</main>
<?php
get_footer();