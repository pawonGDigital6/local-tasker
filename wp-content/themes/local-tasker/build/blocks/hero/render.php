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
$class_name = 'acf-block lt-hero relative md:pt-[5.3125rem] md:pb-[5.8rem] py-16';

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
$hero_image = get_field('hero_image');
$pre_title_image = get_field('pre_title_image');
$rating_image = get_field('rating_image');
$pre_title = get_field('pre_title');
$hero_title = get_field('hero_title');
$hero_video_bg = get_field('hero_video_bg');
$is_bg_video = get_field('is_bg_video');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<?php
	/**
	 * BACKGROUND LAYER LOGIC
	 * Prioritize Video only if the toggle is explicitly active and a valid file array is present.
	 */
	if ($is_bg_video && !empty($hero_video_bg) && is_array($hero_video_bg)):
		$video_url = esc_url($hero_video_bg['url']);
		$mime_type = esc_attr($hero_video_bg['mime_type']);
		$video_title = esc_attr($hero_video_bg['title']);
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
	elseif (!empty($hero_image)):
		?>
		<div class="abs-bg-video absolute inset-0 w-full h-full z-0">
			<?php
			// Senior Note: Passing Tailwind layout rules directly into WordPress native render context
			echo wp_get_attachment_image($hero_image, $size, false, [
				'class' => 'w-full h-full object-cover',
				'sizes' => '100vw'
			]);
			?>
		</div>
	<?php endif; ?>
	<!-- Abs BG -->
	<div class="abs-bg absolute inset-0" style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.42) 1%, rgba(0, 0, 0, 0) 37%, rgba(0, 0, 0, 0) 42%),
radial-gradient(52.86% 52.86% at 50% 47.14%, rgba(0, 0, 0, 0.65) 0%, rgba(5, 52, 131, 0.396875) 79.62%, rgba(10, 101, 252, 0.1625) 100%);
"></div>
	<div class="container relative">
		<!-- Pre Heading -->
		<div
			class="pre-header flex max-sm:flex-col sm:gap-4 gap-1 items-center text-center justify-center sm:mb-[1.85rem] mb-[1.25rem]">
			<div class="imgs flex  gap-2 items-center">
				<?php
				if ($pre_title_image):
					$url = wp_get_attachment_url($pre_title_image);
					echo wp_get_attachment_image($pre_title_image, $size);
				endif;
				if ($rating_image):
					$url = wp_get_attachment_url($rating_image);
					echo wp_get_attachment_image($rating_image, $size);
				endif;
				?>
			</div>
			<!-- Pre Title -->
			<?php if ($pre_title): ?>
				<span
					class="text-white text-caption-md [&_*:last-child]:mb-0 [&_strong]:font-semibold"><?php echo $pre_title; ?></span>
			<?php endif; ?>
		</div>
		<!-- Hero Title -->
		<?php if ($hero_title): ?>
			<h1
				class="hero-title text-white text-center font-bold max-w-[892px] mx-auto leading-[1.3] font-semi-ext max-md:leading-[1.23]">
				<?php echo esc_html($hero_title); ?>
			</h1>
		<?php endif; ?>
		<!-- Feature Lists -->
		<div class="feature-list grid sm:grid-cols-3 sm:gap-6 gap-[21px] sm:mt-[48px] mt-10 wd:px-5">
			<?php if (have_rows('features_item')): ?>
				<?php while (have_rows('features_item')):
					the_row();
					$feature_item_icon = get_sub_field('feature_item_icon');
					$feature_title = get_sub_field('feature_title');
					$feature_text = get_sub_field('feature_text');
					?>
					<!-- Item -->
					<div class="feature-list__item text-center text-lt-white">
						<?php
						if ($feature_item_icon):
							?>
							<div class="icon sm:mb-[18px] mb-4 flex justify-center">
								<?php
								$url = wp_get_attachment_url($feature_item_icon);
								echo wp_get_attachment_image($feature_item_icon, $size);
								; ?>
							</div>
							<?php
						endif;
						?>
						<?php if ($feature_title): ?>
							<h2
								class="feature-heading text-body-xl leading-[1.4] font-bold tracking-[-0.8px] font-semi-ext sm:mb-[11px] mb-2">
								<?php echo esc_html($feature_title); ?></h2>
						<?php endif; ?>
						<?php if ($feature_text): ?>
							<div class="feature-text max-w-[302px] mx-auto tracking-[0.02em]"><?php echo $feature_text; ?></div>
						<?php endif; ?>
					</div><!-- End of Item -->
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
		<!-- End of Feature Lists -->
	</div>
</section>