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
$class_name = 'acf-block lt-google-review py-16';

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

$section_title = get_field('section_title');

?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="container flex flex-col sm:gap-15 gap-6">
		<!-- Section Title -->
		<?php if ($section_title): ?>
			<h2 class="font-semi-ext md:text-h3 text-[24px] leading-normal font-bold text-lt-onyx text-center">
				<?php echo esc_html($section_title); ?>
			</h2>
		<?php endif; ?>
		<!-- End of Section Title -->
		<!-- Review -->
		<div class="review-holder">
			<?php if ($google_review_shortcode = get_field('google_review_shortcode')): ?>
				<?php echo esc_html($google_review_shortcode); ?>
			<?php endif; ?>
		</div>
		<!-- End of Review -->
		<!-- Btn -->
		<?php
		$link = get_field('button');
		if ($link):
			$link_url = $link['url'];
			$link_title = $link['title'];
			$link_target = $link['target'] ? $link['target'] : '_self';
			?>
			<div class="btn-wrap text-center">
				<a class="btn btn--brand inline-flex md:min-w-[270px]" href="<?php echo esc_url($link_url); ?>"
					target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>