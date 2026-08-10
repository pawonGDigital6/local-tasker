<?php
/**
 * "How it works" — single product wrapper.
 *
 * The card itself is the theme-wide component in
 * `template-parts/components/how-it-works.php` (also used by the standalone
 * `acf-block/area-calculator` block). This wrapper supplies the product-specific
 * bits: the box-buying copy and the "Quick estimate" rows, whose ids
 * (#lt-qe-area / #lt-qe-boxes / #lt-qe-total) are populated by
 * src/global/js/product-single.js from the product price + carton size.
 *
 * @package local-tasker
 */

defined('ABSPATH') || exit;

get_template_part(
	'template-parts/components/how-it-works',
	null,
	array(
		'steps' => array(
			array(
				'title' => __('Enter your area', 'local-tasker'),
				'desc' => __('Use the calculator below so you always know the final unit price before adding to cart.', 'local-tasker'),
			),
			array(
				'title' => __('Enter your total area in sqm', 'local-tasker'),
				'desc' => __('Type in the number of square metres you need to cover.', 'local-tasker'),
			),
			array(
				'title' => __('We calculate your boxes', 'local-tasker'),
				'desc' => __('We round up to the nearest full box and show the total coverage and price upfront — no surprises at checkout.', 'local-tasker'),
			),
		),
		'estimate' => array(
			'title' => __('Quick estimate', 'local-tasker'),
			'rows' => array(
				array(
					'label' => __('Your area', 'local-tasker'),
					'value' => '0 sqm',
					'id' => 'lt-qe-area',
				),
				array(
					'label' => __('Boxes needed', 'local-tasker'),
					'value' => '0 boxes',
					'id' => 'lt-qe-boxes',
				),
				array(
					'label' => __('Total price', 'local-tasker'),
					'value' => '$0.00',
					'id' => 'lt-qe-total',
					'divider' => true,
					'emphasis' => true,
				),
			),
		),
	)
);
