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
$class_name = 'acf-block lt-why-us';

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

$pre_title = get_field('pre_title');
$title = get_field('title');
$section_content = get_field('section_content');
$image_big = get_field('image_big');
$image_small = get_field('image_small');
$size = 'full';
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="md:partial-container-left flex flex-wrap-reverse md:justify-end">
		<div class="why-us__content relative bigLp:w-[48%] wd:w-[42.3%] md:w-[49%] self-end md:pt-[6.938rem] pb-[2.875rem] md:mr-[-4.3rem] z-10 max-md:px-[32px] max-sm:px-4 md:my-12">
			<?php
			if ($image_small):
				?>
				<div
					class="why-us__accent absolute md:left-[-58px] top-0 z-2 max-md:hidden w-45 h-45 overflow-hidden rounded-full md:block">
					<?php
					$url = wp_get_attachment_url($image_small);
					echo wp_get_attachment_image($image_small, $size);
					?>
				</div>
			<?php endif; ?>
			<div class="why-us__card relative bg-lt-brand sm:px-8 pl-8 pr-[30px] sm:py-[33px] py-[27px_32px] max-md:rounded-br-[8.75rem] md:rounded-bl-[10rem] md:px-[3.75rem] md:py-[5.55rem] max-md:mt-[-27px]">
				<div class="inline-flex flex-col gap-[1.875rem] md:gap-10">
					<div class="flex flex-col gap-4">
						<div class="flex flex-col gap-[11px] md:gap-5">
							<?php if ($pre_title): ?>
								<div
									class="text-caption-sm uppercase tracking-[0.05em] text-lt-white md:text-caption-md md:tracking-normal">
									<?php echo esc_html($pre_title); ?>
								</div>
							<?php endif; ?>
							<?php if ($title): ?>
								<h2
									class="font-semi-ext text-2xl font-bold leading-[normal] tracking-[-0.0333em] text-lt-white md:text-[2rem] md:tracking-[-0.025em]">
									<?php echo esc_html($title); ?>
								</h2>
							<?php endif; ?>
						</div>
						<?php if ($section_content): ?>
							<div class="text-body sm:tracking-[0.02em] text-lt-white md:text-[#DBEAFE] max-sm:leading-[normal]">
								<?php echo $section_content; ?>
							</div>
						<?php endif; ?>
					</div>
					<?php
					$link = get_field('button');
					if ($link):
						$link_url = $link['url'];
						$link_title = $link['title'];
						$link_target = $link['target'] ? $link['target'] : '_self';
						?>
						<div class="btn-wrap">
							<a class="btn btn--white font-medium inline-flex" href="<?php echo esc_url($link_url); ?>"
								target="<?php echo esc_attr($link_target); ?>">
								<?php echo esc_html($link_title); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php if ($image_big): ?>
			<div
				class="why-us__media relative z-0 w-full md:aspect-square md:w-[51.8%] max-smlr:h-[390px] img-full-cover">
				<?php
				$url = wp_get_attachment_url($image_big);
				echo wp_get_attachment_image($image_big, $size);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>