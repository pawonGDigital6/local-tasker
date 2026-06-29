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
$class_name = 'acf-block lt-inner-hero-wd relative flex items-end md:py-14 py-8 md:min-h-[500px] min-h-[378px]';

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
$ihw_image = get_field('ihw_image');
$ihw_heading = get_field('ihw_heading');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<!-- Overlay -->
	<div class="overlay absolute inset-0 w-full h-full pointer-events-none z-1">
	</div>
	<!-- BG Img -->
	<?php
	if ($ihw_image):
		?>
		<div class="abs-img absolute inset-0 w-full h-full z-0 pointer-events-none">
			<?php
			echo wp_get_attachment_image($ihw_image, $size, false, array('class' => 'w-full h-full object-cover'));
			?>
		</div>
		<?php
	endif;
	?>
	<div class="wd:container-bx container">
		<div class="content md:max-w-[700px] text-lt-white max-md:text-center relative z-1">
			<!-- BreadCrumb -->
			<?php
			get_template_part('template-parts/components/breadcrumb');
			?><!-- End of BreadCrumb -->
			<!-- Title -->
			<?php if ($ihw_heading): ?>
				<h1 class="sm:text-h2 text-[1.875rem] leading-[1.20] sm:tracking-[-0.02em] tracking-[0.4px] sm:mb-6 mb-2">
					<?php echo esc_html($ihw_heading); ?>
				</h1>
			<?php endif; ?>
			<!-- Content -->
			<?php if ($content = get_field('content')): ?>
				<div class="content sm:text-body-lg last-ele-0">
					<?php echo $content; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>