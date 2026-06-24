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
$class_name = 'acf-block lt-service-installation max-md:pb-14';

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

?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="md:partial-container-right flex flex-wrap">
		<!-- Left -->
		<div
			class="service-insta__media relative z-0 md:w-[51.5%] w-full img-full-cover">
			<div class="swiper lt-left-slide h-full">
				<div class="swiper-wrapper h-full">
					<?php if (have_rows('service_and_installation_lists')): ?>
						<?php while (have_rows('service_and_installation_lists')):
							the_row();
							$sai_image = get_sub_field('sai_image');
							$size = 'full';
							?>
							<div class="swiper-slide h-full img-holder xl:min-h-[960px] md:min-h-[720px] smlr:min-h-[560px] max-smlr:min-h-[390px]">
								<?php
								if ($sai_image):
									$url = wp_get_attachment_url($sai_image);
									echo wp_get_attachment_image($sai_image, $size);
								endif;
								?>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>
			</div>
			<div class="lt-slide-pagination absolute md:bottom-8! bottom-11! z-10 max-md:justify-center"></div>
		</div>
		<!-- Right -->
		<div
			class="service-insta__content relative bigLp:w-[48%] wd:w-[38.7%] md:w-[48%] w-full max-md:px-8 max-sm:px-4 z-10 max-md:mt-[-27px]">
			<div class="slider-holder relative md:ml-[-3.2rem] z-10 md:pt-[5.3rem]">
				<div class="swiper lt-right-slide">
					<div class="swiper-wrapper">
						<?php if (have_rows('service_and_installation_lists')): ?>
							<?php while (have_rows('service_and_installation_lists')):
								the_row();
								$sai_pre_title = get_sub_field('sai_pre_title');
								$sai_heading = get_sub_field('sai_heading');
								$sai_content = get_sub_field('sai_content');
								?>
								<div class="swiper-slide">
									<div
										class="service-insta__card relative bg-lt-brand sm:px-8 pl-8 pr-[30px] sm:py-[33px] py-[27px_32px] max-md:rounded-br-[8.75rem] md:rounded-br-[10rem] md:px-15 md:py-[5.55rem]">
										<div class="inline-flex flex-col gap-[1.875rem] md:gap-10">
											<div class="flex flex-col gap-4">
												<div class="flex flex-col gap-[11px] md:gap-5">
													<?php if ($sai_pre_title): ?>
														<div
															class="text-caption-sm uppercase tracking-[0.05em] text-lt-white md:text-caption-md md:tracking-normal">
															<?php echo esc_html($sai_pre_title); ?>
														</div>
													<?php endif; ?>
													<?php if ($sai_heading): ?>
														<h2
															class="font-semi-ext text-2xl font-bold leading-[normal] tracking-[-0.0333em] text-lt-white md:text-[2rem] md:tracking-[-0.025em]">
															<?php echo esc_html($sai_heading); ?>
														</h2>
													<?php endif; ?>
												</div>
												<?php if ($sai_content): ?>
													<div
														class="service-insta__text md:line-clamp-4 text-body sm:tracking-[0.02em] text-lt-white md:text-[#DBEAFE] max-sm:leading-[normal]">
														<?php echo $sai_content; ?>
													</div>
												<?php endif; ?>
											</div>
											<!-- Btn wrap -->
											<?php
											$link = get_sub_field('sai_button');
											if ($link):
												$link_url = $link['url'];
												$link_title = $link['title'];
												$link_target = $link['target'] ? $link['target'] : '_self';
												?>
												<div class="btn-wrap">
													<a class="btn btn--white font-medium inline-flex" href="<?php echo esc_url($link_url); ?>"
														target="<?php echo esc_attr($link_target); ?>">
														<?php echo esc_html($link_title); ?>
													</a>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endwhile; ?>
						<?php endif; ?>
					</div><!-- Swiper wrapper -->
				</div><!-- Swiper -->
			</div>
			<!-- Slide Arrow -->
			<div class="slide-arrow-holder md:flex hidden gap-[12px] pl-[34px] py-[0.875rem_1.125rem]">
				<!-- Slide Prev -->
				<div
					class="slide-arrow prev flex items-center justify-center p-2 w-[87px] h-[87px] rounded-full border border-transparent hover:bg-transparent hover:border-lt-border-white transition-colors duration-600 cursor-pointer bg-lt-snow-drift">
					<svg width="17" height="8" viewBox="0 0 17 8" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g clip-path="url(#clip0_192_6528)">
							<path
								d="M0.6464 4.03553C0.4512 3.84027 0.4512 3.52369 0.6464 3.32843L3.8284 0.146447C4.0237 -0.0488157 4.3403 -0.0488157 4.5355 0.146447C4.7308 0.341709 4.7308 0.658291 4.5355 0.853553L1.7071 3.68198L4.5355 6.51041C4.7308 6.70567 4.7308 7.02225 4.5355 7.21751C4.3403 7.41278 4.0237 7.41278 3.8284 7.21751L0.6464 4.03553ZM17 3.68198L17 4.18198H1V3.68198V3.18198H17L17 3.68198Z"
								fill="black" />
						</g>
						<defs>
							<clipPath id="clip0_192_6528">
								<rect width="17" height="8" fill="white" transform="matrix(-1 0 0 1 17 0)" />
							</clipPath>
						</defs>
					</svg>
				</div><!-- End of Slide Prev -->
				<!-- Slide Next -->
				<div
					class="slide-arrow next flex items-center justify-center p-2 w-[87px] h-[87px] rounded-full border border-transparent hover:bg-transparent hover:border-lt-border-white transition-colors duration-600 cursor-pointer bg-lt-snow-drift">
					<svg width="17" height="8" viewBox="0 0 17 8" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path
							d="M16.3536 4.03519C16.5488 3.83993 16.5488 3.52335 16.3536 3.32809L13.1716 0.146106C12.9763 -0.0491566 12.6597 -0.0491566 12.4645 0.146106C12.2692 0.341368 12.2692 0.65795 12.4645 0.853212L15.2929 3.68164L12.4645 6.51007C12.2692 6.70533 12.2692 7.02191 12.4645 7.21717C12.6597 7.41244 12.9763 7.41244 13.1716 7.21717L16.3536 4.03519ZM0 3.68164L4.37114e-08 4.18164L16 4.18164L16 3.68164L16 3.18164L-4.37114e-08 3.18164L0 3.68164Z"
							fill="black" />
					</svg>
				</div><!-- End of Slide Next -->
			</div>
			<!-- End of Slide Arrow -->
		</div><!-- End of Right -->
	</div>
</section>