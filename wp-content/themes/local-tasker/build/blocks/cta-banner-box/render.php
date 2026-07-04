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
$has_no_bg_img = get_field('cta_bx_no_bg_img');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name) . ($has_no_bg_img ? ' no-bg-img' : ''); ?>" <?php echo $style_attr; ?>>
	<div class="wd:container-bx sm:container <?php echo $has_no_bg_img ? 'container' : 'sm:container' ?>">
		<div
			class="lt-cta-box__inner relative py-10 px-4 flex justify-center items-center sm:border border-[#E6E6E6] overflow-hidden <?php echo $has_no_bg_img ? 'bg-lt-brand sm:min-h-[278px] min-h-[321px] sm:rounded-[10px] rounded-[16px]' : 'sm:min-h-[434px] min-h-[459px] sm:rounded-[20px]' ?>">
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
			<?php if (!$has_no_bg_img): ?>
				<div class="overlay absolute inset-0 w-full h-full bg-lt-black opacity-55 pointer-events-none"></div>
			<?php endif; ?>
			<div class="lt-cta-box__content text-center text-lt-white mx-auto relative z-2 <?php echo $has_no_bg_img ? 'max-w-[672px] ' : 'max-w-[576px] ' ?>">
				<?php if ($cb_heading): ?>
					<h2 class="sm:mb-4 mb-5 smlr:leading-[1.30] leading-[1.26] capitalize sm:tracking-[-1px] <?php echo $has_no_bg_img ? 'text-h4' : 'text-h3' ?>">
						<?php echo esc_html($cb_heading); ?>
					</h2>
				<?php endif; ?>
				<?php if ($cb_content): ?>
					<div class="texts max-sm:leading-[1.64] <?php echo $has_no_bg_img ? 'sm:tracking-[0.02em]' : 'sm:text-body-lg text-caption-md' ?>">
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
					<div class="btn-wrap <?php echo $has_no_bg_img ? 'sm:mt-10 mt-8' : 'sm:mt-5 mt-4' ?>">
						<a class="btn  <?php echo $has_no_bg_img ? 'btn--white' : 'btn--brand hover:bg-lt-white! hover:border-white md:min-w-[270px]' ?>  transition-colors duration-360"
							href="<?php echo esc_url($link_url); ?>"
							target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>