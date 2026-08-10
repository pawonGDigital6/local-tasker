<?php
/**
 * ACF Block template — Area Calculator.
 *
 * Standalone, WooCommerce-free wrapper around the shared area calculator
 * component (`template-parts/components/area-calculator.php`). It needs no
 * product, product meta or cart context and can be dropped on any page.
 *
 * @param array $block The block settings and attributes.
 */

// Support custom "anchor" values.
$anchor = '';
if (!empty($block['anchor'])) {
	$anchor = 'id="' . esc_attr($block['anchor']) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'acf-block lt-area-calculator-block sm:py-14 py-10';

if (get_field('ac_light_bg')) {
	$class_name .= ' bg-[#F7F6F4]';
}

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

$ac_sec_title = get_field('ac_sec_title');
$ac_sec_content = get_field('ac_sec_content');
$ac_card_title = get_field('ac_card_title');
$ac_card_subtitle = get_field('ac_card_subtitle');
$ac_wastage = get_field('ac_wastage');
$ac_narrow = get_field('ac_narrow');
$ac_show_cta = get_field('ac_show_cta');
$ac_cta = get_field('ac_cta');

// `$block['id']` is unique per instance, so several blocks can share a page.
$ac_uid = empty($block['id'])
	? wp_unique_id('lt-area-calc-')
	: 'lt-area-calc-' . sanitize_html_class(str_replace('block_', '', (string) $block['id']));

// Component args — everything below is content, never product data.
$ac_args = array(
	'uid' => $ac_uid,
	'title' => $ac_card_title ? $ac_card_title : __('Area Calculator', 'local-tasker'),
	'subtitle' => null === $ac_card_subtitle ? __('Calculate your total area', 'local-tasker') : $ac_card_subtitle,
	'wastage' => '' === $ac_wastage || null === $ac_wastage ? 10 : (float) $ac_wastage,
	'show_cta' => (bool) $ac_show_cta,
	'cta_label' => $ac_show_cta && !empty($ac_cta['title']) ? $ac_cta['title'] : __('Use This Area', 'local-tasker'),
	'cta_url' => $ac_show_cta && !empty($ac_cta['url']) ? $ac_cta['url'] : '',
);
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="container">
		<?php if ($ac_sec_title || $ac_sec_content): ?>
			<!-- Section Head -->
			<div class="section-head max-w-[780px] mx-auto text-center sm:mb-10 mb-8">
				<?php if ($ac_sec_title): ?>
					<h2
						class="section-title sm:mb-4 mb-3 text-h4 leading-[1.30] font-bold font-semi-ext text-lt-text-primary sm:tracking-[-1px]">
						<?php echo esc_html($ac_sec_title); ?>
					</h2>
				<?php endif; ?>
				<?php if ($ac_sec_content): ?>
					<div class="section-content text-lt-text-secondary sm:text-body text-caption-md">
						<?php echo $ac_sec_content; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<!-- Calculator -->
		<div class="calc-wrap <?php echo $ac_narrow ? 'max-w-[720px] mx-auto' : ''; ?>">
			<?php get_template_part('template-parts/components/area-calculator', null, $ac_args); ?>
		</div>
	</div>
</section>
