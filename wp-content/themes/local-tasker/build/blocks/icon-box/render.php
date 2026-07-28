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
$class_name = 'acf-block lt-icon-box sm:py-12 py-16 bg-[#F6F7F9]';

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
	<div class="container">
		<?php
		$ib_lists = get_field('ib_lists');
		$count = $ib_lists ? count($ib_lists) : 0;
		?>
		<div
			class="grid item-count-<?php echo $count; ?> <?php echo get_field('four_column') ? 'md:grid-cols-4 has-four-col' : 'md:grid-cols-3'; ?> sm:grid-cols-2 grid-cols-1 gap-x-8 gap-y-[50px] wd:px-6">
			<?php if (have_rows('ib_lists')): ?>
				<?php while (have_rows('ib_lists')):
					the_row();
					$size = 'full';
					$ib_icon = get_sub_field('ib_icon');
					$ib_heading = get_sub_field('ib_heading');
					$ib_text = get_sub_field('ib_text');
					?>
					<!-- Item -->
					<div class="grid-item">
						<div class="icon-box sm:max-w-[334px] max-w-[302px] mx-auto text-center text-lt-brand">
							<?php
							if ($ib_icon):
								?>
								<div class="icon-box__icon flex justify-center sm:mb-4 mb-4">
									<?php
									$url = wp_get_attachment_url($ib_icon);
									echo wp_get_attachment_image($ib_icon, $size);
									?>
								</div>
								<?php
							endif;
							?>
							<?php if ($ib_heading): ?>
								<h2
									class="icon-box__title sm:text-body-xl text-body-lg leading-[normal] font-bold font-semi-ext tracking-[-0.8px] sm:mb-2.2 mb-2">
									<?php echo esc_html($ib_heading); ?>
								</h2>
							<?php endif; ?>
							<?php if ($ib_text): ?>
								<div class="icon-box__content max-sm:text-caption-md">
									<?php echo $ib_text; ?>
								</div>
							<?php endif; ?>
						</div><!-- Icon Box -->
					</div>
				<?php endwhile; ?>
			<?php endif; ?>
		</div><!-- Grid -->
	</div>
</section>