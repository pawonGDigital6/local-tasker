<?php
/**
 * ACF Block template.
 *
 * @param array $block The block settings and attributes.
 * 
 * @package acf-block-demo
 */

// Support custom "anchor" values.
$anchor = '';
if (!empty($block['anchor'])) {
	$anchor = 'id="' . esc_attr($block['anchor']) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'acf-block lt-testimonials sm:py-[5.4375rem_7.625rem] py-10';

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

$size = 'full';
$testi_sec_pre_title = get_field('testi_sec_pre_title');
$testi_sec_title = get_field('testi_sec_title');

?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="md:partial-container-left pl-4 wd:ml-[2.3rem]">
		<?php if ($testi_sec_pre_title): ?>
			<div class="pre-title sm:mb-8 mb-[10px] text-caption-md uppercase font-semibold tracking-[0.08em] opacity-50">
				<?php echo esc_html($testi_sec_pre_title); ?>
			</div>
		<?php endif; ?>
		<?php if ($testi_sec_title): ?>
			<h2
				class="sec-title capitalize smlr:text-h3 text-[1.5rem] smlr:leading-[1.30] leading-[1.26] font-bold font-semi-ext text-lt-text-primary sm:tracking-[-1px]">
				<?php echo esc_html($testi_sec_title); ?>
			</h2>
		<?php endif; ?>
		<div class="testimonial-slider swiper md:mt-[2.6rem] mt-[3.225rem]">
			<div class="swiper-wrapper py-2 pl-2">
				<?php if (have_rows('testimonial_lists')): ?>
					<?php while (have_rows('testimonial_lists')):
						the_row();
						$testimonial_content = get_sub_field('testimonial_content');
						$testimonial_author = get_sub_field('testimonial_author');
						$testimonial_img = get_sub_field('testimonial_img');
						$testimonial_video = get_sub_field('testimonial_video');
						?>
						<div class="swiper-slide md:w-[834px]! sm:w-[434px]! smlr:w-[395px]! w-[295px]! h-[initial]!">
							<!-- Card -->
							<div
								class="testimonial-card flex md:flex-wrap max-md:flex-col max-md:justify-between md:gap-[38px] h-full">
								<!-- Card Content -->
								<div
									class="testimonial-card__content md:w-[calc(50%-19px)] w-full sm:p-[2.5625rem_1.9375rem_2.5625rem_2.375rem] p-[20px_21px_32px_12px] flex flex-col justify-between max-md:min-h-[calc(100%-287px)] max-sm:min-h-[calc(100%-187px)] max-sm:rounded-l-[12px] max-sm:rounded-r-2xl">
									<div class="upper">
										<img class="max-sm:w-[146px]"
											src="<?php echo site_url(); ?>/wp-content/uploads/2026/06/Logo.png" alt="">
										<!-- Star -->
										<div class="starts flex sm:mt-10 mt-5">
											<?php for ($i = 1; $i <= 5; $i++): ?>
												<svg class="sm:w-[25px] w-[18px]" width="15" height="15" viewBox="0 0 15 15" fill="none"
													xmlns="http://www.w3.org/2000/svg">
													<mask id="mask0_50598_25418" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
														y="0" width="15" height="15">
														<g clip-path="url(#clip0_50598_25418)">
															<g clip-path="url(#clip1_50598_25418)">
																<path
																	d="M14.467 5.4257C14.4191 5.27759 14.3288 5.14682 14.2072 5.04963C14.0856 4.95245 13.9381 4.89313 13.783 4.87905L9.46552 4.48769L7.76005 0.484324C7.6989 0.340926 7.59696 0.218661 7.4669 0.132726C7.33683 0.0467915 7.18438 0.000976563 7.02849 0.000976562C6.8726 0.000976562 6.72015 0.0467915 6.59008 0.132726C6.46002 0.218661 6.35808 0.340926 6.29693 0.484324L4.59146 4.48769L0.273931 4.87995C0.118221 4.89352 -0.0300511 4.95262 -0.152401 5.04988C-0.27475 5.14715 -0.365765 5.27828 -0.4141 5.42691C-0.462434 5.57555 -0.465949 5.73513 -0.424207 5.88576C-0.382465 6.03638 -0.297312 6.17139 -0.179365 6.27394L3.08437 9.14093L2.12212 13.3884C2.08801 13.5401 2.099 13.6985 2.15375 13.844C2.2085 13.9895 2.30462 14.1159 2.43026 14.2075C2.55591 14.299 2.70559 14.3519 2.86089 14.3595C3.01618 14.3671 3.17031 14.3291 3.30428 14.2502L7.02759 12.0187L10.7491 14.2493C10.8831 14.3287 11.0375 14.3672 11.1931 14.3599C11.3487 14.3526 11.4988 14.2998 11.6247 14.2081C11.7507 14.1164 11.847 13.9899 11.9018 13.844C11.9566 13.6982 11.9675 13.5395 11.9331 13.3875L10.9708 9.14183L14.2345 6.27394C14.3517 6.17078 14.4361 6.03555 14.4773 5.88496C14.5184 5.73436 14.5145 5.57501 14.4661 5.4266L14.467 5.4257Z"
																	fill="#EDEDED" />
															</g>
														</g>
													</mask>
													<g mask="url(#mask0_50598_25418)">
														<rect width="14.3619" height="14.3619" fill="#F89122" />
													</g>
													<defs>
														<clipPath id="clip0_50598_25418">
															<rect width="14.3619" height="14.3619" fill="white" />
														</clipPath>
														<clipPath id="clip1_50598_25418">
															<rect width="15.2595" height="14.3619" fill="white"
																transform="translate(-0.449219)" />
														</clipPath>
													</defs>
												</svg>
											<?php endfor; ?>
										</div><!-- End of Star -->
										<?php if ($testimonial_content): ?>
											<!-- Testimonial Content -->
											<div
												class="text sm:mt-5 mt-[15px] text-lt-text-muted sm:leading-relaxed max-sm:text-caption-md">
												<?php echo $testimonial_content; ?>
											</div>
										<?php endif; ?>
									</div>
									<?php if ($testimonial_author): ?>
										<!-- Testimonial Author -->
										<div class="author sm:mt-5 mt-4 text-lt-accent sm:text-body-xl text-caption-md">
											<?php echo esc_html($testimonial_author); ?>
										</div>
									<?php endif; ?>
								</div>
								<!-- Card Img -->
								<div
									class="testimonial-card__img relative md:w-[calc(50%-19px)] w-full max-md:h-[287px] max-sm:h-[187px]">
									<div class="abs-img absolute inset-0 w-full h-full">
										<?php
										echo wp_get_attachment_image($testimonial_img, $size, false, array('class' => 'w-full h-full object-cover'));
										?>
									</div>
									<?php if ($testimonial_video):
										$video_url = esc_url($testimonial_video['url']);
										$mime_type = esc_attr($testimonial_video['mime_type']);
										$video_title = esc_attr($testimonial_video['title']);
										?>
										<a href="<?php echo $video_url; ?>" data-fancybox
											class="icon-play md:w-[77px] md:h-[77px] w-[58px] h-[58px] rounded-full bg-lt-accent flex items-center justify-center absolute md:left-[38px] md:bottom-[42px] max-md:left-1/2 max-md:top-1/2 max-md:translate-x-[-50%] max-md:translate-y-[-50%] z-1 cursor-pointer focus:outline-none">
											<svg class="ml-1" width="16" height="18" viewBox="0 0 16 18" fill="none"
												xmlns="http://www.w3.org/2000/svg">
												<path d="M15.4629 8.92788L-0.000225908 17.8555L-0.000225128 0.00025014L15.4629 8.92788Z"
													fill="white" />
											</svg>
										</a>
									<?php endif; ?>
								</div><!-- End of img -->
							</div><!-- End of Card -->
						</div>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>