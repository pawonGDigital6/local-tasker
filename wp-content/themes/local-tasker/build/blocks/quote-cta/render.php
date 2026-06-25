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
$class_name = 'acf-block lt-quote-cta';

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

$qc_heading = get_field('qc_heading');
$qc_content = get_field('qc_content');
$qc_cta_image = get_field('qc_cta_image');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="flex flex-wrap-reverse justify-between">
		<!-- Content -->
		<div
			class="lt-quote-cta__content-col relative md:w-[calc(100%-38.54%)] md:partial-container-left w-full bg-lt-brand text-lt-white flex items-center max-md:text-center md:pr-10 sm:px-8 sm:py-10 px-4 py-9.5">
			<div class="inner md:max-w-[660px] z-2 wd:pl-24.5">
				<!-- Title -->
				<?php if ($qc_heading): ?>
					<h2 class="sm:mb-4 mb-6 text-h2 leading-[1.2] font-bold font-semi-ext sm:tracking-[-1.2px] tracking-[0.07px]"><?php echo esc_html($qc_heading); ?></h2>
				<?php endif; ?>
				<!-- Content -->
				<?php if ($qc_content): ?>
					<div class="content sm:mb-9.75 mb-7.5 max-sm:text-caption-md sm:tracking-[0.02em] tracking-[-0.15px]"><?php echo $qc_content; ?></div>
				<?php endif; ?>
				<!-- Button -->
				<?php
				$link = get_field('quote_button');
				if ($link):
					$link_url = $link['url'];
					$link_title = $link['title'];
					$link_target = $link['target'] ? $link['target'] : '_self';
					?>
					<div class="btn-wrap mb-5.5">
						<a class="btn btn--accent md:min-w-[270px]" href="<?php echo esc_url($link_url); ?>"
							target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
					</div>
				<?php endif; ?>
				<!-- Features -->
				<div class="lt-quote-cta__feat inline-flex flex-wrap max-sm:justify-center gap-4 sm:p-4 sm:bg-[#1C398E33] sm:rounded-[16px]">
					<?php if (have_rows('qc_icon_title')): ?>
						<?php while (have_rows('qc_icon_title')):
							the_row();
							$size = 'full';
							$qc_list_icon = get_sub_field('qc_list_icon');
							$qc_list_title = get_sub_field('qc_list_title');
							?>
							<div class="lt-quote-cta__feat-item flex items-center sm:gap-3 gap-2 sm:pr-4 sm:border-r">
								<?php
								if ($qc_list_icon):
									?>
									<div class="icon flex shrink-0 items-center justify-center rounded-full sm:w-[40px] sm:h-[40px] w-[24px] h-[24px] sm:p-2 p-1 bg-lt-white">
										<?php
										$url = wp_get_attachment_url($qc_list_icon);
										echo wp_get_attachment_image($qc_list_icon, $size);
										?>
									</div>
									<?php
								endif;
								?>
								<?php if ($qc_list_title): ?>
									<span class="lt-quote-cta__feat-item-title sm:max-w-[80px] sm:text-caption-md text-[8px] font-semibold text-left"><?php echo esc_html($qc_list_title); ?></span>
								<?php endif; ?>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>
			</div><!-- Inner -->
			<div class="bg-graphic absolute left-0 top-0 pointer-events-none z-1">
				<?php
				$qc_bg_graphic_img = get_field('qc_bg_graphic_img');
				$size = 'full';
				if ($qc_bg_graphic_img) {
					$url = wp_get_attachment_url($qc_bg_graphic_img);
					echo wp_get_attachment_image($qc_bg_graphic_img, $size);
				}
				; ?>
			</div>
		</div>
		<!-- Img Col -->
		<div class="img-col md:w-[38.54%] w-full sm:min-h-[571px] min-h-[390px]">
			<div class="lt-quote-cta__img-holder h-full relative">
				<?php
				

				if ($qc_cta_image) {
					// 'full' ensures WordPress includes the largest version in the srcset pool
					echo wp_get_attachment_image($qc_cta_image, 'full', false, [
						// Tells browser: use 100vw on desktop (min-width: 768px), and standard medium size width on mobile
						'sizes' => '(max-width: 767px) 390px',
					]);
				}
				?>
			</div>
		</div>
	</div>
</section>