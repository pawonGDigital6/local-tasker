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
$class_name = 'acf-block lt-cta-wide py-[4.6875rem] sm:min-h-[580px] relative overflow-hidden flex items-center';

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
$is_bg_video = get_field('is_bg_video'); // True/False toggle field
$cta_wd_bg_image = get_field('cta_wd_bg_image'); // Image ID field
$video_field = get_field('cta_wd_video'); // File Array field

?>
<section <?php echo $anchor; ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<!-- OVERLAY -->
	<div class="overlay absolute inset-0 w-full h-full z-2 pointer-events-none"></div>
	<?php
	/**
	 * BACKGROUND LAYER LOGIC
	 * Prioritize Video only if the toggle is explicitly active and a valid file array is present.
	 */
	if ($is_bg_video && !empty($video_field) && is_array($video_field)):
		$video_url = esc_url($video_field['url']);
		$mime_type = esc_attr($video_field['mime_type']);
		$video_title = esc_attr($video_field['title']);
		?>
		<div class="abs-bg-video absolute inset-0 w-full h-full pointer-events-none">
			<video class="w-full h-full object-cover" autoplay loop playsinline muted>
				<source src="<?php echo $video_url; ?>" type="<?php echo $mime_type; ?>">
			</video>
		</div>
		<?php
		/**
		 * FALLBACK IMAGE LAYER
		 * Triggers if the Video Toggle is turned off, or if the field lacks a valid video array asset.
		 */
	elseif (!empty($cta_wd_bg_image)):
		?>
		<div class="abs-bg-video absolute inset-0 w-full h-full z-0">
			<?php
			// Senior Note: Passing Tailwind layout rules directly into WordPress native render context
			echo wp_get_attachment_image($cta_wd_bg_image, $size, false, [
				'class' => 'w-full h-full object-cover',
				'sizes' => '100vw'
			]);
			?>
		</div>
	<?php endif; ?>
	<!-- FOREGROUND CONTENT LAYER -->
	<div class="container">
		<div class="inner-holder max-w-[574px] max-sm:text-center text-lt-white  relative z-10 wd:ml-[2.2rem]">
			<?php if ($cta_wd_heading = get_field('cta_wd_heading')): ?>
				<h2 class="text-h2 sm:mb-3 mb-6 leading-[1.25] sm:tracking-[-1.2px] tracking-[0.07px]"><?php echo esc_html($cta_wd_heading); ?></h2>
			<?php endif; ?>
			<?php if ($cta_wd_content = get_field('cta_wd_content')): ?>
				<div class="lt-cta-wide__content sm:leading-[1.81] max-sm:text-caption-md max-sm:tracking-[-0.15px] text-[#DBEAFE]">
					<?php echo $cta_wd_content; ?>
				</div>
			<?php endif; ?>
			<?php
			$link = get_field('cta_wd_button');
			if ($link):
				$link_url = $link['url'];
				$link_title = $link['title'];
				$link_target = $link['target'] ? $link['target'] : '_self';
				?>
				<div class="btn-wrap sm:mt-[22px] mt-[30px]">
					<a class="btn btn--accent sm:min-w-[248px]" href="<?php echo esc_url($link_url); ?>"
						target="<?php echo esc_attr($link_target); ?>">
						<?php echo esc_html($link_title); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>