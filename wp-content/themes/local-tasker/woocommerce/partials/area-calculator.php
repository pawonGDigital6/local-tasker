<?php
/**
 * Area calculator — single product wrapper.
 *
 * The card itself is the theme-wide component in
 * `template-parts/components/area-calculator.php` (also used by the standalone
 * `acf-block/area-calculator` block). This wrapper only adds the WooCommerce
 * specific bit: the "Use This Area" CTA feeds the total into the product
 * calculator (`#lt-calc-area`) and scrolls back up to it (`#lt-calculator`).
 *
 * @package local-tasker
 */

defined('ABSPATH') || exit;

global $product;

get_template_part(
	'template-parts/components/area-calculator',
	null,
	array(
		// Keeps the historical `#lt-area-calculator` anchor on product pages.
		'uid' => 'lt-area-calculator',
		// Same backend-configured allowance the box calculator above uses, so the
		// two cards on one page never quote different wastage figures.
		'wastage' => lt_get_wastage_percent($product instanceof WC_Product ? $product : 0),
		'cta_target' => '#lt-calc-area',
		'cta_scroll_to' => '#lt-calculator',
	)
);
