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
$class_name = 'acf-block lt-cta-box wd:mx-8';

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
$cb_heading = get_field('cb_heading');
$cb_content = get_field('cb_content');
$cb_bg_img = get_field('cb_bg_img');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="sm:container">
		<div
			class="lt-cta-box__inner relative py-16 px-4 sm:min-h-[434px] min-h-[459px] flex justify-center items-center sm:rounded-[20px] sm:border border-[#E6E6E6] overflow-hidden">
			<?php
			if ($cb_bg_img):
				?>
				<div class="abs-img absolute inset-0 w-full h-full">
					<?php
					echo wp_get_attachment_image($cb_bg_img, $size, false, array('class' => 'w-full h-full object-cover'));
					?>
				</div>
				<?php
			endif;
			?>
			<div class="overlay absolute inset-0 w-full h-full bg-lt-black opacity-55 pointer-events-none"></div>
			<div class="lt-cta-box__content text-center text-lt-white max-w-[576px] mx-auto relative z-2">
				<?php if ($cb_heading): ?>
					<h2
						class="sm:mb-4 mb-5 text-h3 smlr:leading-[1.30] leading-[1.26] capitalize sm:tracking-[-1px]">
						<?php echo esc_html($cb_heading); ?>
					</h2>
				<?php endif; ?>
				<?php if ($cb_content): ?>
					<div class="texts sm:text-body-lg text-caption-md max-sm:leading-[1.64]">
						<?php echo $cb_content; ?>
					</div>
				<?php endif; ?>
				<?php
				$link = get_field('cb_button');
				if ($link):
					$link_url = $link['url'];
					$link_title = $link['title'];
					$link_target = $link['target'] ? $link['target'] : '_self';
					?>
					<div class="btn-wrap sm:mt-5 mt-4">
						<a class="btn btn--brand md:min-w-[270px] transition-colors duration-360 hover:bg-lt-white! hover:border-white" href="<?php echo esc_url($link_url); ?>"
							target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>