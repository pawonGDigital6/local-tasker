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
$class_name = 'acf-block lt-client-logo py-9';

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

$clients_logo_list = get_field('clients_logo_list');
$size = 'full';
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="xl:container">
		<div class="swiper lt-client-logo__carousel">
			<div class="swiper-wrapper">
				<?php
				if ($clients_logo_list): ?>
					<?php foreach ($clients_logo_list as $image_id): ?>
						<div class="swiper-slide">
							<div class="logo-holder">
								<?php echo wp_get_attachment_image($image_id, $size); ?>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>