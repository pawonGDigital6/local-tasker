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
$class_name = 'acf-block lt-inner-two-col-hero sm:py-[2.075rem] py-8 overflow-hidden';

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
$testimoial_hero_off = get_field('turn_off_hero_testimonial');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="container">
		<div class="rows flex justify-between flex-wrap md:gap-5 gap-12">
			<!-- Left Col -->
			<div
				class="left-col relative rounded-[20px] w-full sm:p-[3.8125rem_3.1875rem_3.8125rem_2.375rem] max-lg:text-center flex flex-col wd:min-h-[650px] <?php echo $testimoial_hero_off ? 'no-testimonial justify-center items-center text-center' : 'wd:w-[57.6%] justify-center' ?>">
				<div class="upper relative z-10 <?php echo $testimoial_hero_off ? 'max-w-[652px]' : '' ?>">
					<!-- BreadCrumb -->
					<?php get_template_part('template-parts/components/breadcrumb'); ?>
					<!-- End of BreadCrumb -->
					<!-- Pre Heading -->
					<div
						class="pre-header flex  max-wd:justify-center sm:gap-4 max-sm:flex-col max-sm:items-center max-md:justify-center <?php echo $testimoial_hero_off ? 'justify-center sm:mb-10.5 mb-5.5' : 'sm:mb-5.25 mb-6' ?>">
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
							class="text-h2 leading-[1.20] font-bold font-semi-ext sm:tracking-[-0.02em] tracking-[0.4px] md:mb-3 mb-6 text-lt-white">
							<?php echo esc_html($in_two_hero_main_heading); ?>
						</h1>
					<?php endif; ?>
					<!-- Btn wrap -->
					<div
						class="btn-wrap flex max-wd:justify-center sm:flex-wrap max-sm:flex-col gap-4 md:mb-10 mb-6 <?php echo $testimoial_hero_off ? 'justify-center' : '' ?>">
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
				<div
					class="icon-title-list flex max-wd:justify-center md:flex-wrap items-center max-sm:flex-col gap-x-9 gap-y-4.5 w-full <?php echo $testimoial_hero_off ? ' justify-center' : '' ?>">
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
									<div class="title text-lt-white md:tracking-[0.02em] tracking-[0.28px]">
										<?php echo esc_html($in_two_hero_ic_title); ?>
									</div>
								<?php endif; ?>
							</div><!-- End of List -->
						<?php endwhile; ?>
					<?php endif; ?>
				</div>
				<!-- End of Icon Title -->
				<div class="abs-imgs absolute sm:bottom-0 pointer-events-none <?php echo $testimoial_hero_off ? 'sm:right-0 smlr:right-[-16px] max-smlr:left-[58%] max-smlr:translate-x-[-50%] max-sm:bottom-[-222px] max-smlr:w-[110%]' : 'right-[-20px] max-sm:bottom-[-90px]' ?>">
					<?php if ($testimoial_hero_off): ?>
						<!-- Desktop: No Testimonial Graphic -->
						<div class="graph-dk max-smlr:hidden">
							<svg width="534" height="577" viewBox="0 0 534 577" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path
									d="M303.724 296.628L340.467 259.872C348.883 251.453 360.29 246.71 372.193 246.678C384.097 246.647 395.528 251.329 403.989 259.703L659.442 495.454C668.744 504.221 674.851 515.395 675.414 527.905C675.685 534.11 674.675 540.304 672.445 546.1C670.215 551.897 666.813 557.171 662.453 561.594L607.572 617.081C607.542 617.117 607.509 617.149 607.473 617.179C598.991 625.631 587.517 630.395 575.543 630.435H573.883C561.373 629.971 550.171 624.103 541.277 614.716L303.879 360.432C299.67 356.258 296.325 351.294 294.038 345.825C291.752 340.357 290.567 334.49 290.552 328.562C290.538 322.634 291.694 316.762 293.955 311.282C296.215 305.802 299.535 300.822 303.724 296.628ZM8.43323 222.341L8.86945 221.905L57.0949 174.172C59.9197 171.356 63.2743 169.128 66.9649 167.616C70.6555 166.104 74.609 165.338 78.5972 165.363C85.0591 165.37 91.3553 167.408 96.5956 171.188C96.5956 170.583 96.5112 169.992 96.4689 169.472C95.8357 160.325 94.8506 146.506 103.786 133.982C114.411 119.662 126.075 106.144 138.686 93.538C138.719 93.497 138.757 93.4593 138.798 93.4254C157.542 74.9627 198.338 40.1903 260.832 14.7055C284.534 4.98 309.909 -0.0156071 335.528 3.66275e-05C379.94 3.66275e-05 413.713 20.0389 426.645 31.9158C433.968 38.9105 440.646 46.5497 446.6 54.7409C449.444 58.6667 450.945 63.4055 450.88 68.2528C450.815 73.1001 449.187 77.7969 446.239 81.6449C443.29 85.4929 439.179 88.2865 434.515 89.6104C429.852 90.9342 424.885 90.7176 420.355 88.9927C416.408 87.5191 412.376 86.2833 408.281 85.2917C399.752 83.4887 391.005 82.9435 382.318 83.6734C363.757 85.2073 341.804 94.4246 332.094 102.094C315.63 115.336 307.707 133.179 306.398 159.917C306.145 165.292 317.262 185.444 334.543 208.438C337.834 212.777 339.442 218.163 339.066 223.596C338.691 229.03 336.359 234.143 332.502 237.989L284.08 286.412C280.011 290.49 274.533 292.854 268.774 293.016C263.015 293.179 257.413 291.127 253.121 287.284C239.429 275.055 218.574 256.621 211.284 252.104C200.463 245.448 192.723 244.167 190.598 243.942C185.084 243.403 179.538 244.572 174.71 247.291C174.686 247.532 174.713 247.775 174.788 248.005C174.863 248.235 174.985 248.447 175.147 248.628L177.708 251.062L178.13 251.456C180.96 254.276 183.204 257.627 184.733 261.318C186.262 265.008 187.046 268.965 187.04 272.959C187.033 276.954 186.236 280.908 184.695 284.593C183.154 288.279 180.899 291.623 178.06 294.433L129.848 342.152C127.023 344.965 123.668 347.191 119.977 348.701C116.286 350.21 112.333 350.974 108.346 350.947C100.339 350.959 92.6473 347.826 86.928 342.222L8.82721 265.078C8.55988 264.811 8.29248 264.529 8.03918 264.248C2.79968 258.505 -0.0720825 250.991 0.000976562 243.217C0.0740967 235.443 3.08661 227.985 8.43323 222.341Z"
									fill="white" fill-opacity="0.05" />
							</svg>
						</div>
					<?php else: ?>
						<!-- Desktop: Has Testimonial Graphic -->
						<div class="graph-dk max-smlr:hidden">
							<svg width="427" height="382" viewBox="0 0 427 382" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path
									d="M303.725 296.628L340.467 259.872C348.883 251.453 360.29 246.71 372.194 246.678C384.097 246.647 395.529 251.329 403.989 259.703L659.442 495.454C668.744 504.221 674.851 515.395 675.414 527.905C675.686 534.11 674.675 540.304 672.445 546.1C670.215 551.897 666.814 557.171 662.454 561.594L607.572 617.081C607.543 617.117 607.51 617.149 607.474 617.179C598.992 625.631 587.518 630.395 575.544 630.435H573.883C561.373 629.971 550.171 624.103 541.278 614.716L303.88 360.432C299.67 356.258 296.326 351.294 294.039 345.825C291.752 340.357 290.567 334.49 290.553 328.562C290.538 322.634 291.695 316.762 293.955 311.282C296.215 305.802 299.535 300.822 303.725 296.628ZM8.43359 222.341L8.86981 221.905L57.0953 174.172C59.9201 171.356 63.2747 169.128 66.9653 167.616C70.6559 166.104 74.6094 165.338 78.5976 165.363C85.0594 165.37 91.3557 167.408 96.5959 171.188C96.5959 170.583 96.5115 169.992 96.4693 169.472C95.8361 160.325 94.851 146.506 103.787 133.982C114.411 119.662 126.076 106.144 138.686 93.538C138.72 93.497 138.758 93.4593 138.799 93.4254C157.543 74.9627 198.338 40.1903 260.833 14.7055C284.534 4.98 309.909 -0.0156071 335.528 3.66275e-05C379.94 3.66275e-05 413.713 20.0389 426.646 31.9158C433.968 38.9105 440.646 46.5497 446.6 54.7409C449.444 58.6667 450.945 63.4055 450.88 68.2528C450.815 73.1001 449.187 77.7969 446.239 81.6449C443.291 85.4929 439.179 88.2865 434.515 89.6104C429.852 90.9342 424.886 90.7176 420.355 88.9927C416.408 87.5191 412.376 86.2833 408.281 85.2917C399.752 83.4887 391.005 82.9435 382.318 83.6734C363.757 85.2073 341.804 94.4246 332.094 102.094C315.63 115.336 307.707 133.179 306.399 159.917C306.145 165.292 317.262 185.444 334.543 208.438C337.835 212.777 339.442 218.163 339.067 223.596C338.692 229.03 336.359 234.143 332.503 237.989L284.08 286.412C280.011 290.49 274.533 292.854 268.774 293.016C263.015 293.179 257.413 291.127 253.121 287.284C239.429 275.055 218.574 256.621 211.284 252.104C200.463 245.448 192.723 244.167 190.598 243.942C185.084 243.403 179.538 244.572 174.711 247.291C174.687 247.532 174.713 247.775 174.788 248.005C174.863 248.235 174.986 248.447 175.147 248.628L177.708 251.062L178.13 251.456C180.96 254.276 183.204 257.627 184.733 261.318C186.263 265.008 187.046 268.965 187.04 272.959C187.033 276.954 186.237 280.908 184.695 284.593C183.154 288.279 180.899 291.623 178.06 294.433L129.849 342.152C127.023 344.965 123.668 347.191 119.977 348.701C116.287 350.21 112.334 350.974 108.346 350.947C100.339 350.959 92.6477 347.826 86.9283 342.222L8.82758 265.078C8.56024 264.811 8.29285 264.529 8.03955 264.248C2.80005 258.505 -0.0717163 250.991 0.00134277 243.217C0.0744629 235.443 3.08698 227.985 8.43359 222.341Z"
									fill="white" fill-opacity="0.05" />
							</svg>
						</div>
					<?php endif; ?>
					<div class="graph-mb smlr:hidden">
						<svg width="345" height="389" viewBox="0 0 345 389" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M187.168 182.795L209.81 160.144C214.996 154.956 222.026 152.033 229.361 152.013C236.696 151.994 243.741 154.88 248.955 160.04L406.376 305.319C412.108 310.722 415.871 317.608 416.218 325.317C416.386 329.14 415.763 332.957 414.388 336.53C413.014 340.102 410.918 343.352 408.231 346.077L374.411 380.271C374.393 380.293 374.372 380.313 374.35 380.331C369.123 385.54 362.053 388.475 354.674 388.5H353.651C345.941 388.214 339.038 384.598 333.558 378.814L187.263 222.113C184.669 219.541 182.608 216.482 181.199 213.112C179.79 209.742 179.06 206.126 179.051 202.473C179.042 198.821 179.754 195.202 181.147 191.825C182.54 188.448 184.586 185.379 187.168 182.795ZM5.19711 137.016L5.46594 136.747L35.1845 107.332C36.9252 105.597 38.9925 104.224 41.2668 103.292C43.5411 102.36 45.9774 101.888 48.4351 101.903C52.4172 101.908 56.2971 103.163 59.5264 105.493C59.5264 105.121 59.4744 104.756 59.4484 104.435C59.0582 98.7987 58.4511 90.2829 63.9578 82.565C70.5048 73.7405 77.693 65.4103 85.464 57.642C85.4849 57.6167 85.5081 57.5935 85.5334 57.5726C97.0844 46.1951 122.224 24.7669 160.736 9.06213C175.342 3.06888 190.979 -0.00961773 206.766 2.25714e-05C234.135 2.25714e-05 254.947 12.3488 262.917 19.6678C267.429 23.9783 271.544 28.6858 275.213 33.7336C276.966 36.1528 277.891 39.0731 277.851 42.0602C277.811 45.0473 276.808 47.9417 274.991 50.313C273.174 52.6842 270.64 54.4058 267.766 55.2216C264.893 56.0374 261.832 55.9039 259.04 54.841C256.608 53.9329 254.123 53.1713 251.6 52.5603C246.344 51.4492 240.954 51.1132 235.6 51.563C224.162 52.5082 210.634 58.1883 204.65 62.9145C194.504 71.0747 189.622 82.0707 188.815 98.5472C188.659 101.86 195.51 114.278 206.159 128.448C208.188 131.122 209.178 134.441 208.947 137.789C208.716 141.138 207.279 144.289 204.902 146.659L175.062 176.499C172.554 179.012 169.179 180.469 165.63 180.569C162.081 180.669 158.629 179.405 155.984 177.036C147.546 169.501 134.694 158.14 130.202 155.357C123.534 151.255 118.764 150.466 117.455 150.327C114.057 149.995 110.639 150.715 107.664 152.391C107.649 152.539 107.665 152.689 107.712 152.831C107.758 152.973 107.833 153.104 107.933 153.215L109.511 154.715L109.771 154.958C111.515 156.695 112.898 158.761 113.84 161.035C114.783 163.309 115.266 165.747 115.262 168.209C115.258 170.67 114.767 173.107 113.817 175.378C112.867 177.649 111.478 179.71 109.728 181.442L80.0181 210.848C78.2769 212.582 76.2094 213.954 73.9351 214.884C71.6607 215.814 69.2246 216.285 66.7675 216.268C61.8332 216.276 57.0934 214.345 53.5688 210.891L5.43994 163.352C5.27518 163.187 5.11041 163.014 4.95432 162.841C1.72549 159.301 -0.0441895 154.671 0.000854492 149.88C0.0458984 145.09 1.90234 140.494 5.19711 137.016Z"
								fill="white" fill-opacity="0.05" />
						</svg>
					</div>
				</div>
			</div><!-- End of Left Col -->
			<?php if (!$testimoial_hero_off): ?>
				<!-- Right Col -->
				<div class="right-col lp:w-[40.8%] wd:w-[40%] w-full relative max-wd:min-h-[380px]">
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
					<div
						class="lt-slide-pagination mt-4.5 absolute left-0! sm:bottom-16! bottom-6! z-10 sm:gap-2 gap-1 sm:px-9.5 px-6">
					</div>
				</div><!-- End of Right Col -->
			<?php endif; ?>
		</div><!-- End of Row -->
	</div>
</section>