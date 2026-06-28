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
$class_name = 'acf-block lt-ms-content py-16 max-sm:pt-10 bg-lt-white-lilac';

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

$ms_heading = get_field('ms_heading');
$ms_content = get_field('ms_content');
$is_img_right = get_field('ms_img_position');
$button_orange = get_field( 'btn_color_brand_orange') ;
 
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="container max-sm:px-0">
		<div class="rows flex flex-wrap justify-between md:gap-8 gap-5 wd:px-8 <?php echo $is_img_right ? 'md:flex-row-reverse flex-wrap-reverse max-md:gap-12.5' : ''; ?>">
			<!-- Media Col -->
			<div class="media-col md:w-[51.2%] w-full relative">
				<div class="media-slider swiper">
					<div class="swiper-wrapper">
						<?php if (have_rows('media_slider_lists')): ?>
							<?php while (have_rows('media_slider_lists')):
								the_row();
								$size = 'full';
								$media_image = get_sub_field('media_image');
								?>
								<div class="swiper-slide">
									<?php
									if ($media_image):
										?>
										<div
											class="lt-ms-content__img-holder relative overflow-hidden sm:border border-[#F1F5F9] sm:rounded-[20px] sm:min-h-[490px] min-h-[390px]">
											<?php
											$url = wp_get_attachment_url($media_image);
											echo wp_get_attachment_image($media_image, $size);
											?>
										</div>
										<?php
									endif;
									?>
								</div>
							<?php endwhile; ?>
						<?php endif; ?>
					</div><!-- Media Swiper wrapper -->
				</div><!-- Media Slider -->
				<!-- Slide Arrow -->
				<div class="slide-arrow-holder px-4 md:pb-8 pb-4 absolute bottom-0 z-10 flex max-md:justify-center sm:gap-[12px] gap-2 w-full">
					<!-- Slide Prev -->
					<div
						class="slide-arrow prev flex items-center justify-center p-2 sm:w-[87px] sm:h-[87px] w-[52px] h-[52px] rounded-full border border-transparent hover:bg-transparent hover:border-lt-border-white transition-colors duration-600 cursor-pointer bg-lt-snow-drift">
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
						class="slide-arrow next flex items-center justify-center p-2 sm:w-[87px] sm:h-[87px] w-[52px] h-[52px] rounded-full border border-transparent hover:bg-transparent hover:border-lt-border-white transition-colors duration-600 cursor-pointer bg-lt-snow-drift">
						<svg width="17" height="8" viewBox="0 0 17 8" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M16.3536 4.03519C16.5488 3.83993 16.5488 3.52335 16.3536 3.32809L13.1716 0.146106C12.9763 -0.0491566 12.6597 -0.0491566 12.4645 0.146106C12.2692 0.341368 12.2692 0.65795 12.4645 0.853212L15.2929 3.68164L12.4645 6.51007C12.2692 6.70533 12.2692 7.02191 12.4645 7.21717C12.6597 7.41244 12.9763 7.41244 13.1716 7.21717L16.3536 4.03519ZM0 3.68164L4.37114e-08 4.18164L16 4.18164L16 3.68164L16 3.18164L-4.37114e-08 3.18164L0 3.68164Z"
								fill="black" />
						</svg>
					</div><!-- End of Slide Next -->
				</div>
				<!-- End of Slide Arrow -->
			</div><!-- End of Media Col -->
			<!-- Content Col -->
			<div class="content-col md:w-[41.5%] w-full self-center max-md:text-center wd:pr-[42px] max-sm:px-4">
				<?php if ($ms_heading): ?>
					<h2
						class="lt-ms-content__title sm:text-h3 text-[2rem] sm:leading-[1.30] leading-[normal] font-bold font-semi-ext sm:tracking-[-0.1px] tracking-[0.07px] text-lt-text-primary mb-2">
						<?php echo esc_html($ms_heading); ?>
					</h2>
				<?php endif; ?>
				<?php if ($ms_content): ?>
					<div class="lt-ms-content__cont mb-6 max-sm:text-caption-md sm:tracking-[0.02em] tracking-[-0.15px] text-lt-text-secondary tick-ul"><?php echo $ms_content; ?></div>
				<?php endif; ?>
				<?php
				$link = get_field('ms_button');
				if ($link):
					$link_url = $link['url'];
					$link_title = $link['title'];
					$link_target = $link['target'] ? $link['target'] : '_self';
					?>
					<a class="btn md:min-w-[270px] <?php echo $button_orange ? 'btn--accent' : 'btn--brand' ?>" href="<?php echo esc_url($link_url); ?>"
						target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>