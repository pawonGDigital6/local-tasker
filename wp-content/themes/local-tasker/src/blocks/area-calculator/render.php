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

/**
 * Toggles that are ON by default.
 *
 * `get_field()` returns null when a block instance was saved before the field
 * existed, or while the field group is waiting to be synced. Falling straight
 * through to false would silently drop a whole column, so null means "use the
 * default" and only an explicit `false` turns the option off.
 *
 * @param mixed $value Raw field value.
 * @return bool
 */
$ac_enabled = static function ($value) {
	return (null === $value || '' === $value) ? true : (bool) $value;
};

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'acf-block lt-area-calculator-block sm:py-14 py-10';

if ($ac_enabled(get_field('ac_light_bg'))) {
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
$ac_show_hiw = $ac_enabled(get_field('ac_show_hiw'));
$ac_hiw_title = get_field('ac_hiw_title');
$ac_hiw_steps = get_field('ac_hiw_steps');
$ac_hiw_show_summary = $ac_enabled(get_field('ac_hiw_show_summary'));
$ac_hiw_summary_title = get_field('ac_hiw_summary_title');

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

// Wastage label reused by the summary box, so the two never drift apart.
$ac_wastage_label = rtrim(rtrim(number_format((float) $ac_args['wastage'], 2, '.', ''), '0'), '.');

/**
 * "How it works" args.
 *
 * The steps default to generic measuring copy (no boxes, cartons or checkout)
 * and the summary box mirrors this block's own calculator totals instead of the
 * product "Quick estimate" — so the card carries no WooCommerce dependency.
 */
$ac_hiw_args = array();
if ($ac_show_hiw) {
	$ac_steps = array();

	if (is_array($ac_hiw_steps) && !empty($ac_hiw_steps)) {
		foreach ($ac_hiw_steps as $ac_step) {
			$ac_steps[] = array(
				'title' => isset($ac_step['hiw_step_title']) ? $ac_step['hiw_step_title'] : '',
				'desc' => isset($ac_step['hiw_step_text']) ? $ac_step['hiw_step_text'] : '',
			);
		}
	} else {
		$ac_steps = array(
			array(
				'title' => __('Measure each room', 'local-tasker'),
				'desc' => __('Enter the length and width of every area you want to cover, in metres.', 'local-tasker'),
			),
			array(
				'title' => __('Add as many areas as you need', 'local-tasker'),
				'desc' => __('Use "Add another area" to build your total up room by room.', 'local-tasker'),
			),
			array(
				'title' => __('Get your total in sqm', 'local-tasker'),
				'desc' => __('We add everything up and include a wastage allowance for cuts and offcuts.', 'local-tasker'),
			),
		);
	}

	$ac_hiw_args = array(
		'title' => $ac_hiw_title ? $ac_hiw_title : __('How it works', 'local-tasker'),
		'steps' => $ac_steps,
	);

	if ($ac_hiw_show_summary) {
		$ac_hiw_args['mirror_of'] = $ac_uid;
		$ac_hiw_args['estimate'] = array(
			'title' => $ac_hiw_summary_title ? $ac_hiw_summary_title : __('Your total', 'local-tasker'),
			'rows' => array(
				array(
					'label' => __('Total area', 'local-tasker'),
					'value' => '0.00 sqm',
					'mirror' => 'total',
				),
				array(
					/* translators: %s: wastage percentage, e.g. "10". */
					'label' => sprintf(__('With %s%% wastage', 'local-tasker'), $ac_wastage_label),
					'value' => '0.00 sqm',
					'mirror' => 'wastage',
					'divider' => true,
					'emphasis' => true,
				),
			),
		);
	}
}
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
		<?php if ($ac_show_hiw): ?>
			<!-- Two-column row — same track as the single product explainer section -->
			<div class="flex flex-wrap lg:flex-nowrap gap-8 lg:gap-9 items-stretch">

				<!-- Area Calculator — card has its own built-in header/title -->
				<div class="w-full lg:flex-1 min-w-0">
					<?php get_template_part('template-parts/components/area-calculator', null, $ac_args); ?>
				</div>

				<!-- How it works — self-contained card with its own header -->
				<div class="w-full lg:flex-1 min-w-0">
					<?php get_template_part('template-parts/components/how-it-works', null, $ac_hiw_args); ?>
				</div>

			</div>
		<?php else: ?>
			<!-- Calculator only -->
			<div class="calc-wrap <?php echo $ac_narrow ? 'max-w-[720px] mx-auto' : ''; ?>">
				<?php get_template_part('template-parts/components/area-calculator', null, $ac_args); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
