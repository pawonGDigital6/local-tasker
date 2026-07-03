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
$class_name = 'acf-block lt-service-areas bg-[#F9FAFB] py-16';

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

$sa_sec_head_icon = get_field('sa_sec_head_icon');
$sa_sec_title = get_field('sa_sec_title');
$sa_sec_text = get_field('sa_sec_text');
$block_uid = !empty($block['id']) ? $block['id'] : uniqid('service-area-');

?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="wd:container-bx container">
		<!-- Section Header -->
		<div class="section-header sm:mb-12 mb-[50px] text-center max-w-[672px] mx-auto">
			<?php if ($sa_sec_head_icon): ?>
				<div class="loc-feat-card__icon flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-[#DBEAFE] mx-auto mb-8"
					aria-hidden="true">
					<?php
					echo wp_get_attachment_image(
						$sa_sec_head_icon,
						'thumbnail',
						false,
						array(
							'class' => 'h-8 w-8 object-contain',
							'alt' => '',
							'loading' => 'lazy',
							'decoding' => 'async',
						)
					);
					?>
				</div>
			<?php endif; ?>
			<?php if ($sa_sec_title): ?>
				<h2
					class="sec-title font-semi-ext text-[2rem] font-bold sm:leading-[1.2] leading-[normal] tracking-[-0.02em] text-lt-text-primary">
					<?php echo esc_html($sa_sec_title); ?>
				</h2>
			<?php endif; ?>
			<?php if ($sa_sec_text): ?>
				<div
					class="sec-text sm:mt-3 mt-2 text-lg leading-7 tracking-[-0.024em] text-lt-text-muted max-md:text-sm max-md:leading-5 max-md:tracking-[-0.011em]">
					<?php echo $sa_sec_text; ?>
				</div>
			<?php endif; ?>
		</div><!-- End of Section Header -->
		<!-- Service areas lists -->
		<?php if (have_rows('area_lists')): ?>
			<div class="lt-service-areas__grid grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-7" role="list">
				<?php while (have_rows('area_lists')):
					the_row();
					$al_heading = get_sub_field('al_heading');
					$al_content = get_sub_field('al_content');
					$card_title_id = $al_heading ? 'service-area-title-' . $block_uid . '-' . get_row_index() : '';
					?>
					<article
						class="service-area-card group flex flex-col gap-4 rounded-[10px] bg-lt-white px-6 py-6 theme-shadow transition-shadow duration-300 motion-safe:hover:shadow-[0px_4px_12px_rgba(0,0,0,0.08)]"
						role="listitem" <?php echo $card_title_id ? 'aria-labelledby="' . esc_attr($card_title_id) . '"' : ''; ?>>
						<?php if ($al_heading): ?>
							<h3 id="<?php echo esc_attr($card_title_id); ?>"
								class="service-area-card__title font-base text-xl font-medium leading-7 tracking-[-0.022em] text-lt-text-primary">
								<?php echo esc_html($al_heading); ?>
							</h3>
						<?php endif; ?>
						<?php if ($al_content): ?>
							<div
								class="service-area-card__content tick-ul-two text-base leading-6 tracking-[-0.02em] text-[#364153] [&_ul]:mb-0">
								<?php echo $al_content; ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
		<!-- End of Service areas lists -->
	</div>
</section>