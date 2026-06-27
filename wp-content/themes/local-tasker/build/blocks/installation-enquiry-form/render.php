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
$class_name = 'acf-block lt-insta-enquiry relative py-16 bg-text-secondary';

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

/** =========================================================================
 * DYNAMIC ACF FIELDS
 * ========================================================================= */
$size = 'full';
$ie_bg_image = get_field('ie_bg_image');
$ie_pre_title = get_field('ie_pre_title');
$ie_section_title = get_field('ie_section_title');
$ie_section_content = get_field('ie_section_content');
$ie_form_shortcode = get_field('ie_form_shortcode');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<?php
	if ($ie_bg_image):
		?>
		<div class="abs-bg-img absolute inset-0 w-full h-full z-0">
			<?php
			echo wp_get_attachment_image($ie_bg_image, $size, false, [
				'class' => 'w-full h-full object-cover',
			]);
			?>
		</div>
		<?php
	endif; ?>
	<div class="container">
		<div class="rows flex flex-wrap justify-between gap-10 relative z-1 wd:px-5">
			<!-- Content Col -->
			<div class="content-col md:w-[48%] w-full text-lt-white max-sm:text-center">
				<?php if ($ie_pre_title): ?>
					<span class="inline-block pre-title p-[11px_16px] sm:mb-6 mb-[10px] bg-[#FFFFFF1A] rounded-[60px] sm:text-caption-md text-caption-sm leading-none tracking-[0.02em]">
						<?php echo esc_html($ie_pre_title); ?></span>
				<?php endif; ?>
				<?php if ($ie_section_title): ?>
					<h2 class="text-h3 section-title sm:mb-6 mb-[10px]"><?php echo esc_html($ie_section_title); ?></h2>
				<?php endif; ?>
				<?php if ($ie_section_content): ?>
					<div class="section-content sm:tracking-[0.02em] max-sm:text-caption-md">
						<?php echo $ie_section_content; ?>
					</div>
				<?php endif; ?>
			</div><!-- End of Content Col -->
			<!-- Form Col -->
			<div class="form-col wd:w-[41.9%] md:w-[44%] w-full">
				<div class="form-holder sm:py-7 sm:px-8 px-4 py-9.5 bg-lt-white sm:rounded-[8px] rounded-[20px] th-form-style">
					<span class="form-heading block text-h5 font-medium tracking-[-0.01em] pb-4 mb-6 text-lt-text-primary border-b border-[#13284B1A]">Personal Details</span>
					<?php if ($ie_form_shortcode): ?>
						<?php echo do_shortcode($ie_form_shortcode); ?>
					<?php endif; ?>
				</div>
			</div><!-- End of Form Col -->
		</div><!-- End of Row -->
	</div>
</section>