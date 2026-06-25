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
$class_name = 'acf-block lt-inner-two-col-hero sm:py-[2.075rem] py-16';

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
$rating_image_one = get_field('rating_image_one');
$rating_image_two = get_field('rating_image_two');
$in_two_hero_pre_title = get_field('in_two_hero_pre_title');
$in_two_hero_content = get_field('in_two_hero_content');

?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="container">
		<div class="rows flex justify-between flex-wrap md:gap-5 gap-14">
			<!-- Left Col -->
			<div
				class="left-col relative overflow-hidden wd:w-[57.6%] w-full rounded-[20px] sm:p-[3.8125rem_3.1875rem_3.8125rem_2.375rem] max-lg:text-center flex flex-col justify-between wd:min-h-[690px]">
				<div class="upper">
					<!-- breadcrumbs -->
					<div class="breadcrumb md:mb-10.5 mb-13.5">
						<?php if ( function_exists('rank_math_the_breadcrumbs') ) : ?>
							<ul class="flex items-center max-wd:justify-center gap-2.75 text-white text-caption-sm font-semibold tracking-[0.48px] mb-0">
									<?php 
									// Fetch the raw breadcrumb array from Rank Math
									$crumbs = RankMath\Frontend\Breadcrumbs::get()->get_crumbs();
									
									if ( ! empty( $crumbs ) && is_array( $crumbs ) ) {
										$count = count( $crumbs );
										foreach ( $crumbs as $key => $crumb ) {
											$is_last = ( $key + 1 === $count );
											
											// Render Breadcrumb Link
											if ( $is_last || empty( $crumb[1] ) ) {
													echo '<li class="active">' . esc_html( $crumb[0] ) . '</li>';
											} else {
													echo '<li><a class="text-inherit hover:text-lt-accent transition-colors duration-320" href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a></li>';
											}

											// Render Custom SVG Separator (if not the last element)
											if ( ! $is_last ) {
													echo '<li class="sep mt-[2px]">';
													echo '<svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">';
													echo '<path d="M0.46405 0.463867L4.83905 4.83887L0.46405 9.21387" stroke="white" stroke-width="1.3125" />';
													echo '</svg>';
													echo '</li>';
											}
										}
									}
									?>
							</ul>
						<?php endif; ?>
					</div><!-- End of breadcrumbs -->
					<!-- Pre Heading -->
					<div class="pre-header flex  max-wd:justify-center sm:gap-4 mb-5.25 max-sm:flex-col max-sm:items-center max-md:justify-center">
						<div class="imgs flex gap-2 items-center">
							<?php
							if ($rating_image_one):
								$url = wp_get_attachment_url($rating_image_one);
								echo wp_get_attachment_image($rating_image_one, $size);
							endif;
							if ($rating_image_two):
								$url = wp_get_attachment_url($rating_image_two);
								echo wp_get_attachment_image($rating_image_two, $size);
							endif;
							?>
						</div>
						<!-- Pre Title -->
						<?php if ($in_two_hero_pre_title): ?>
							<span class="text-white text-caption-md [&_*:last-child]:mb-0 [&_strong]:font-semibold">
								<?php echo $in_two_hero_pre_title; ?>
							</span>
						<?php endif; ?>
					</div>
					<!-- Title -->
					<?php if ($in_two_hero_main_heading = get_field('in_two_hero_main_heading')): ?>
						<h1
							class="text-h2 leading-[1.20] font-bold font-semi-ext sm:tracking-[-0.02em] tracking-[0.4px] md:mb-3 mb-3 text-lt-white">
							<?php echo esc_html($in_two_hero_main_heading); ?>
						</h1>
					<?php endif; ?>
					<!-- Content -->
					<?php if ($in_two_hero_content): ?>
						<div class="content md:mb-7.5 mb-11.25 lp:pr-10 text-white sm:text-body-xl">
							<?php echo $in_two_hero_content; ?>
						</div>
					<?php endif; ?>
					<!-- Btn wrap -->
					<div class="btn-wrap flex max-wd:justify-center md:flex-wrap max-md:flex-col gap-4 md:mb-10 mb-12.5">
						<?php
						$link = get_field('in_two_hero_button_one');
						if ($link):
							$link_url = $link['url'];
							$link_title = $link['title'];
							$link_target = $link['target'] ? $link['target'] : '_self';
							?>
							<a class="btn btn--brand md:min-w-[270px] hover:text-lt-white!"
								href="<?php echo esc_url($link_url); ?>"
								target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
						<?php endif; ?>
						<?php
						$link = get_field('in_two_hero_button_two');
						if ($link):
							$link_url = $link['url'];
							$link_title = $link['title'];
							$link_target = $link['target'] ? $link['target'] : '_self';
							?>
							<a class="btn btn--white md:min-w-[270px]" href="<?php echo esc_url($link_url); ?>"
								target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
						<?php endif; ?>
					</div><!-- End of Btn wrap -->
				</div>
				<!-- Icon Title -->
				<div class="icon-title-list flex max-wd:justify-center md:flex-wrap items-center max-sm:flex-col gap-x-9 sm:gap-y-4 gap-y-7.5">
					<?php if (have_rows('in_two_hero_ic_lists')): ?>
						<?php while (have_rows('in_two_hero_ic_lists')):
							the_row();
							$size = 'full';
							$in_two_hero_ic_icon = get_sub_field('in_two_hero_ic_icon');
							$in_two_hero_ic_title = get_sub_field('in_two_hero_ic_title');
							?>
							<!-- List -->
							<div class="icon-title flex items-center gap-3">
								<?php
								if ($in_two_hero_ic_icon):
									?>
									<div class="icon shrink-0">
										<?php
										$url = wp_get_attachment_url($in_two_hero_ic_icon);
										echo wp_get_attachment_image($in_two_hero_ic_icon, $size);
										?>
									</div>
									<?php
								endif;
								?>
								<?php if ($in_two_hero_ic_title): ?>
									<div class="title text-lt-white md:tracking-[0.02em] tracking-[0.28px]"><?php echo esc_html($in_two_hero_ic_title); ?></div>
								<?php endif; ?>
							</div><!-- End of List -->
						<?php endwhile; ?>
					<?php endif; ?>
				</div>
				<!-- End of Icon Title -->
				<div class="abs-imgs absolute right-[-20px] bottom-0 pointer-events-none">
					<svg width="427" height="382" viewBox="0 0 427 382" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path
							d="M303.725 296.628L340.467 259.872C348.883 251.453 360.29 246.71 372.194 246.678C384.097 246.647 395.529 251.329 403.989 259.703L659.442 495.454C668.744 504.221 674.851 515.395 675.414 527.905C675.686 534.11 674.675 540.304 672.445 546.1C670.215 551.897 666.814 557.171 662.454 561.594L607.572 617.081C607.543 617.117 607.51 617.149 607.474 617.179C598.992 625.631 587.518 630.395 575.544 630.435H573.883C561.373 629.971 550.171 624.103 541.278 614.716L303.88 360.432C299.67 356.258 296.326 351.294 294.039 345.825C291.752 340.357 290.567 334.49 290.553 328.562C290.538 322.634 291.695 316.762 293.955 311.282C296.215 305.802 299.535 300.822 303.725 296.628ZM8.43359 222.341L8.86981 221.905L57.0953 174.172C59.9201 171.356 63.2747 169.128 66.9653 167.616C70.6559 166.104 74.6094 165.338 78.5976 165.363C85.0594 165.37 91.3557 167.408 96.5959 171.188C96.5959 170.583 96.5115 169.992 96.4693 169.472C95.8361 160.325 94.851 146.506 103.787 133.982C114.411 119.662 126.076 106.144 138.686 93.538C138.72 93.497 138.758 93.4593 138.799 93.4254C157.543 74.9627 198.338 40.1903 260.833 14.7055C284.534 4.98 309.909 -0.0156071 335.528 3.66275e-05C379.94 3.66275e-05 413.713 20.0389 426.646 31.9158C433.968 38.9105 440.646 46.5497 446.6 54.7409C449.444 58.6667 450.945 63.4055 450.88 68.2528C450.815 73.1001 449.187 77.7969 446.239 81.6449C443.291 85.4929 439.179 88.2865 434.515 89.6104C429.852 90.9342 424.886 90.7176 420.355 88.9927C416.408 87.5191 412.376 86.2833 408.281 85.2917C399.752 83.4887 391.005 82.9435 382.318 83.6734C363.757 85.2073 341.804 94.4246 332.094 102.094C315.63 115.336 307.707 133.179 306.399 159.917C306.145 165.292 317.262 185.444 334.543 208.438C337.835 212.777 339.442 218.163 339.067 223.596C338.692 229.03 336.359 234.143 332.503 237.989L284.08 286.412C280.011 290.49 274.533 292.854 268.774 293.016C263.015 293.179 257.413 291.127 253.121 287.284C239.429 275.055 218.574 256.621 211.284 252.104C200.463 245.448 192.723 244.167 190.598 243.942C185.084 243.403 179.538 244.572 174.711 247.291C174.687 247.532 174.713 247.775 174.788 248.005C174.863 248.235 174.986 248.447 175.147 248.628L177.708 251.062L178.13 251.456C180.96 254.276 183.204 257.627 184.733 261.318C186.263 265.008 187.046 268.965 187.04 272.959C187.033 276.954 186.237 280.908 184.695 284.593C183.154 288.279 180.899 291.623 178.06 294.433L129.849 342.152C127.023 344.965 123.668 347.191 119.977 348.701C116.287 350.21 112.334 350.974 108.346 350.947C100.339 350.959 92.6477 347.826 86.9283 342.222L8.82758 265.078C8.56024 264.811 8.29285 264.529 8.03955 264.248C2.80005 258.505 -0.0717163 250.991 0.00134277 243.217C0.0744629 235.443 3.08698 227.985 8.43359 222.341Z"
							fill="white" fill-opacity="0.05" />
					</svg>
				</div>
			</div><!-- End of Left Col -->
			<!-- Right Col -->
			<div class="right-col lp:w-[40.8%] wd:w-[40%] w-full relative max-wd:min-h-[451px]">
				<!-- Testimonial -->
				<div class="swiper in-testimonial h-full">
					<div class="swiper-wrapper">
						<?php if (have_rows('inner_hero_testimonial')): ?>
							<?php while (have_rows('inner_hero_testimonial')):
								the_row();
								$size = 'full';
								$in_hero_testimonial_image = get_sub_field('in_hero_testimonial_image');
								$in_hero_testimonial_content = get_sub_field('in_hero_testimonial_content');
								$In_hero_testimonial_author = get_sub_field('In_hero_testimonial_author');
								?>
								<div class="swiper-slide">
									<div
										class="in-testimonial__item flex items-end h-full relative sm:p-[2.375rem_2.125rem_5.7375rem_2.375rem] p-[1.5rem_1.5rem_2.6rem_1.5rem] p-4 sm:rounded-[20px] rounded-[13px] overflow-hidden">
										<div class="overlay absolute inset-0 w-full h-full pointer-events-none z-2" style="background: linear-gradient(179.35deg, rgba(0, 0, 0, 0) 13.54%, rgba(0, 0, 0, 0.85) 94.79%);
					"></div>
										<?php
										if ($in_hero_testimonial_image):
											?>
											<div class="abs-bg-img absolute inset-0 w-full h-full z-1">
												<?php
												$url = wp_get_attachment_url($in_hero_testimonial_image);
												echo wp_get_attachment_image($in_hero_testimonial_image, $size);
												?>
											</div>
											<?php
										endif;
										?>
										<div class="in-testimonial__content relative z-10">
											<!-- Starts -->
											<div
												class="in-testimonial__stars-lists flex flex-wrap sm:gap-[5px] gap-[3px] sm:mb-[14px] mb-[10px]">
												<?php if ($number_of_stars = get_sub_field('number_of_stars')): ?>
													<?php
													for ($i = 0; $i < intval($number_of_stars); $i++):
														?>
														<span class="stars max-sm:w-[10px]">
															<svg width="15" height="15" viewBox="0 0 15 15" fill="none"
																xmlns="http://www.w3.org/2000/svg">
																<mask id="mask0_50598_25418" style="mask-type:alpha" maskUnits="userSpaceOnUse"
																	x="0" y="0" width="15" height="15">
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
														</span>
													<?php endfor; ?>
												<?php endif; ?>
											</div>
											<!-- Content -->
											<?php if ($in_hero_testimonial_content): ?>
												<div
													class="in-testimonial__text sm:text-body-xl sm:leading-normal leading-[1.31] max-md:tracking-[-0.02em] sm:mb-4 mb-[11px] text-lt-white">
													<?php echo $in_hero_testimonial_content; ?>
												</div>
											<?php endif; ?>
											<!-- Author -->
											<?php if ($In_hero_testimonial_author): ?>
												<span class="in-testimonial__author block text-lt-white font-medium max-sm:text-caption-sm">
													<?php echo esc_html($In_hero_testimonial_author); ?>
												</span>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endwhile; ?>
						<?php endif; ?>
					</div><!-- End of Swipper wrapper -->
				</div><!-- End of Testimonial -->
				<div class="lt-slide-pagination mt-4.5 absolute left-0! sm:bottom-16! bottom-6! z-10 sm:gap-2 gap-1 sm:px-9.5 px-6"></div>
			</div><!-- End of Right Col -->
		</div><!-- End of Row -->
	</div>
</section>