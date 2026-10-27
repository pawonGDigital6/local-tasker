<?php
/**
 * "Order Sample" button and its one-line explainer.
 *
 * Self-gating on the product's "Offer Samples" toggle (off by default). The
 * button adds a free sample through js/product-sample.js → lt_add_sample_to_cart
 * (inc/lt-samples.php); nothing here touches the flooring line or its price.
 *
 * Args:
 *   part — 'button', 'note' or 'both' (default). The box calculator prints the
 *          button inside its action row and the note under it; products using
 *          WooCommerce's own form print both together.
 *
 * Uses utility classes the theme already compiles, so no stylesheet rebuild.
 *
 * @package local-tasker
 */

defined('ABSPATH') || exit;

global $product;

if (!$product instanceof WC_Product || !lt_product_offers_samples($product->get_id())) {
	return;
}

$lt_part = isset($args['part']) ? (string) $args['part'] : 'both';
$lt_max  = lt_sample_max_per_order();
$lt_fee  = lt_format_delivery_price(lt_sample_delivery_fee());
?>
<?php if ('note' !== $lt_part): ?>
	<button type="button" id="lt-order-sample"
		class="btn bg-transparent flex-1 py-[14px]! border border-lt-accent text-lt-accent hover:opacity-80 cursor-pointer disabled:opacity-50 transition-opacity duration-75"
		aria-describedby="lt-sample-note">
		<?php esc_html_e('Order Sample', 'local-tasker'); ?>
	</button>
<?php endif; ?>
<?php if ('button' !== $lt_part): ?>
	<p id="lt-sample-note" class="m-0 mt-3 rounded-lg bg-lt-brand/5 border border-lt-brand/20 px-4 py-3 text-caption-sm leading-[1.5] text-lt-brand text-center">
		<?php
		printf(
			/* translators: 1: maximum samples per order, 2: sample delivery charge. */
			esc_html(_n('Order %1$d flooring sample for %2$s delivery, waived with a full flooring order.', 'Order up to %1$d flooring samples for %2$s delivery, waived with a full flooring order.', $lt_max, 'local-tasker')),
			(int) $lt_max,
			esc_html($lt_fee)
		);
		?>
	</p>
	<p id="lt-sample-status" class="m-0 mt-2 text-caption-sm text-center text-lt-accent hidden" role="status" aria-live="polite"></p>
<?php endif; ?>
