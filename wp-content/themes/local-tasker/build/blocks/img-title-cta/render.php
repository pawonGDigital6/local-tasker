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
$class_name = 'acf-block';

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
	<div class="xl:container">
		<div class="rows grid sm:grid-cols-2">
			<?php if (have_rows('cta_item')): ?>
				<?php while (have_rows('cta_item')):
					the_row();
					$cta_heading = get_sub_field('cta_heading');
					$cta_image = get_sub_field('cta_image');
					$cta_link = get_sub_field('cta_link');
					$size = 'full';
					?>
					<!-- CTA Item -->
					<div class="cta-item group relative p-5 smlr:min-h-[210px] min-h-[113px] flex justify-center items-center overflow-hidden">
						<div
							class="overlay absolute inset-0 bg-lt-black opacity-38 z-10 mix-blend-multiply">
						</div>
						<?php
						if ($cta_image):
							?>
							<div class="abs-bg-img absolute inset-0 img-full-cover will-change-transform transition-transform duration-800 group-hover:scale-105 origin-center">
								<?php
								$url = wp_get_attachment_url($cta_image);
								echo wp_get_attachment_image($cta_image, $size);
								?>
							</div>
							<?php
						endif;
						?>
						<!-- Heading -->
						<?php if ($cta_heading): ?>
							<h2 class="title smlr:text-h4 text-body font-bold font-semi-ext smlr:tracking-[-0.8px] tracking-[-0.043px] text-lt-white text-center z-10">
								<?php echo esc_html($cta_heading); ?>
							</h2>
						<?php endif; ?>
						<?php if ($cta_link): ?>
							<a href="<?php echo esc_html($cta_link); ?>" class="stretched-link"></a>
						<?php endif; ?>
					</div>
					<!-- End of CTA Item -->
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
	</div>
</section>