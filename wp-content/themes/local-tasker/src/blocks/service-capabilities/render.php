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
$class_name = 'acf-block lt-service-capa py-16 bg-lt-white-lilac';

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
$sc_heading = get_field('sc_heading');
$sc_content = get_field('sc_content');
$link = get_field('sc_button');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="bigLp:container max-bigLp:partial-container-right max-md:px-8 max-sm:px-4 max-sm:pl-0">
		<div class="rows flex flex-wrap-reverse justify-between md:gap-6 gap-13">
			<!-- Img Col -->
			<div class="img-col md:w-[51%] w-full md:-mt-6.5 max-wd:self-center">
				<?php
				$sc_image = get_field('sc_image');
				$size = 'full';
				if ($sc_image) {
					$url = wp_get_attachment_url($sc_image);
					echo wp_get_attachment_image($sc_image, $size);
				}
				; ?>
			</div>
			<!-- Content Col -->
			<div class="content-col md:w-[44.7%] w-full md:pt-12.5 max-sm:pl-4">
				<div class="content-inner md:max-w-[541px]">
					<?php if ($sc_heading): ?>
						<h2 class="sm:text-h3 text-[1.875rem] max-smlr:pr-6 leading-[1.25] sm:tracking-[-1px] mb-6 text-lt-text-primary">
							<?php echo esc_html($sc_heading); ?>
						</h2>
					<?php endif; ?>
					<?php if ($sc_content): ?>
						<div class="content max-sm:text-caption-sm max-sm:leading-[1.66]">
							<?php echo $sc_content; ?>
						</div>
					<?php endif; ?>
					<?php
					if ($link):
						$link_url = $link['url'];
						$link_title = $link['title'];
						$link_target = $link['target'] ? $link['target'] : '_self';
						?>
						<div class="btn-wrap sm:mt-7.5 mt-6.75">
							<a class="btn btn--accent sm:min-w-[270px]" href="<?php echo esc_url($link_url); ?>"
								target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>