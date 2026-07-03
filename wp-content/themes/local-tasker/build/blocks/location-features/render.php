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
$class_name = 'acf-block lt-location-feat bg-lt-white py-16';

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

// ======================================= ACF Dyamic Fields
$lf_sec_title = get_field('lf_sec_title');
$lf_sec_text = get_field('lf_sec_text');
$block_uid = !empty($block['id']) ? $block['id'] : uniqid('loc-feat-');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="wd:container-bx container">
		<!-- Section Header -->
		<div class="section-header sm:mb-12 mb-[50px] text-center">
			<?php if ($lf_sec_title): ?>
				<h2
					class="sec-title font-semi-ext text-[2rem] font-bold sm:leading-[1.2] leading-[normal] tracking-[-0.02em] text-lt-text-primary">
					<?php echo esc_html($lf_sec_title); ?>
				</h2>
			<?php endif; ?>
			<?php if ($lf_sec_text): ?>
				<div
					class="sec-text sm:mt-6 mt-2 text-lg leading-7 tracking-[-0.024em] text-lt-text-muted max-md:text-sm max-md:leading-5 max-md:tracking-[-0.011em]">
					<?php echo $lf_sec_text; ?>
				</div>
			<?php endif; ?>
		</div><!-- End of Section Header -->
		<!-- Feature Lists -->
		<div class="lt-location-feat__grid grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8" role="list">
			<?php if (have_rows('lf_lists')): ?>
				<?php while (have_rows('lf_lists')):
					the_row();
					$lf_list_icon = get_sub_field('lf_list_icon');
					$lf_list_heading = get_sub_field('lf_list_heading');
					$lf_list_content = get_sub_field('lf_list_content');
					$card_title_id = $lf_list_heading ? 'loc-feat-title-' . $block_uid . '-' . get_row_index() : '';
					?>
					<!-- Card -->
					<article class="loc-feat-card group flex flex-col items-center gap-6 text-center lg:max-w-[280px] lg:justify-self-center"
						role="listitem" <?php echo $card_title_id ? 'aria-labelledby="' . esc_attr($card_title_id) . '"' : ''; ?>>
						<?php if ($lf_list_icon): ?>
							<div class="loc-feat-card__icon flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-[#DBEAFE]"
								aria-hidden="true">
								<?php
								echo wp_get_attachment_image(
									$lf_list_icon,
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
						<div class="loc-feat-card__body flex flex-col items-stretch sm:gap-4 gap-3">
							<?php if ($lf_list_heading): ?>
								<h3 id="<?php echo esc_attr($card_title_id); ?>"
									class="loc-feat-card__title sm:text-body-xl text-body-lg font-bold leading-normal font-base sm:tracking-[0.02em] text-lt-text-primary">
									<?php echo esc_html($lf_list_heading); ?>
								</h3>
							<?php endif; ?>
							<?php if ($lf_list_content): ?>
								<div
									class="loc-feat-card__text sm:text-base text-caption-md leading-normal sm:tracking-[0.02em] [&_p]:mb-0 [&_p:last-child]:mb-0">
									<?php echo $lf_list_content; ?>
								</div>
							<?php endif; ?>
						</div>
					</article>
				<?php endwhile; ?>
			<?php endif; ?>
		</div><!-- End of Feature Lists -->
	</div>
</section>