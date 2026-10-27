<?php
/**
 * Delivery cost note on the single product page.
 *
 * Tells the customer what delivery costs before they reach checkout: the
 * lowest price per delivery zone, read from WooCommerce → Shipping by
 * lt_get_delivery_zone_prices() (inc/lt-woocommerce-flooring.php), so it follows
 * any rate change. The exact price is still calculated at checkout from the
 * customer's postcode and order weight. Prints nothing when no zone is priced.
 *
 * Uses utility classes the theme already compiles, so no stylesheet rebuild.
 *
 * @package local-tasker
 */

defined('ABSPATH') || exit;

$lt_delivery_prices = lt_get_delivery_zone_prices();
if (!$lt_delivery_prices) {
	return;
}
?>
<div class="lt-delivery-note flex items-start gap-3 rounded-lg bg-lt-snow-drift px-4 py-3 mb-5" role="note" style="padding-inline: 16px">
	<svg class="shrink-0 mt-1 text-lt-brand" width="18" height="14" viewBox="0 0 18 14" fill="none"
		xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
		<path d="M1 1.5h9.5v8H1v-8ZM10.5 4.5h3.5l2.5 2.5v2.5h-6v-5Z" stroke="currentColor" stroke-width="1.2"
			stroke-linejoin="round" />
		<circle cx="4.5" cy="11" r="1.5" fill="#fff" stroke="currentColor" stroke-width="1.2" />
		<circle cx="13" cy="11" r="1.5" fill="#fff" stroke="currentColor" stroke-width="1.2" />
	</svg>
	<div class="text-caption-sm leading-[1.5] text-lt-text-secondary">
		<p class="m-0 font-semibold text-lt-text-primary">
			<?php
			$lt_delivery_parts = array();
			foreach ($lt_delivery_prices as $lt_row) {
				/* translators: 1: delivery zone name, 2: lowest delivery price. */
				$lt_delivery_parts[] = sprintf(__('%1$s from %2$s', 'local-tasker'), $lt_row['zone'], lt_format_delivery_price($lt_row['from']));
			}
			/* translators: %s: list of zones and prices, e.g. "Metro Melbourne from $160 · VIC Outer Metro from $250". */
			echo esc_html(sprintf(__('Delivery: %s', 'local-tasker'), implode(' · ', $lt_delivery_parts)));
			?>
		</p>
		<p class="m-0 mt-1">
			<?php esc_html_e('Price depends on your postcode and order weight. You will see the exact delivery cost at checkout before you pay.', 'local-tasker'); ?>
			<?php
			printf(
				/* translators: %s: "Call us" phone link. */
				esc_html__('Outside these areas? %s for a shipping quote.', 'local-tasker'),
				'<a href="tel:' . esc_attr(lt_get_store_call_number()) . '" class="text-lt-brand underline hover:no-underline">' . esc_html__('Call us', 'local-tasker') . '</a>'
			);
			?>
		</p>
	</div>
</div>
