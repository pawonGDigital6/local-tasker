/**
 * Delivery cost note on the cart and checkout pages.
 *
 * The Cart block on its own only says "Shipping will be calculated at checkout",
 * so customers had no idea of delivery cost until the last step. This adds a
 * short note under the order summary with the lowest price per delivery zone
 * and a "call us" fallback for areas outside them.
 *
 * Figures come from window.ltDeliveryNote, which PHP builds from the live
 * shipping setup (lt_get_delivery_zone_prices() in
 * inc/lt-woocommerce-flooring.php) — nothing is hardcoded here. Rendered through
 * WooCommerce's ExperimentalOrderMeta slot rather than by patching block markup.
 * Display only: the price charged is still calculated by the shipping methods.
 *
 * Markup uses utility classes the theme already compiles (see the product
 * page's woocommerce/partials/product-delivery-note.php).
 */
(function () {
	var data = window.ltDeliveryNote;
	if (!data || !data.zones || !data.zones.length) {
		return;
	}
	if (!window.wp || !window.wp.plugins || !window.wp.element || !window.wc || !window.wc.blocksCheckout) {
		return;
	}

	var el = window.wp.element.createElement;
	var OrderMeta = window.wc.blocksCheckout.ExperimentalOrderMeta;
	if (!OrderMeta) {
		return;
	}

	var summary = 'Delivery: ' + data.zones.map(function (z) {
		return z.zone + ' from ' + z.from;
	}).join(' · ');

	function DeliveryNote(props) {
		if (!props.cart || props.cart.cartNeedsShipping === false) {
			return null;
		}

		var detail = props.context === 'woocommerce/checkout'
			? 'Price depends on your postcode and order weight, and is shown under Delivery once your address is entered.'
			: 'Price depends on your postcode and order weight. You will see the exact delivery cost at checkout before you pay.';

		return el(
			'div',
			{ className: 'lt-delivery-note mt-4 rounded-lg bg-lt-snow-drift px-4 py-3 text-caption-sm leading-[1.5] text-lt-text-secondary', role: 'note' },
			el('p', { className: 'm-0 font-semibold text-lt-text-primary' }, summary),
			el(
				'p',
				{ className: 'm-0 mt-1' },
				detail + ' Outside these areas? ',
				el('a', { href: 'tel:' + data.phone, className: 'text-lt-brand underline hover:no-underline' }, 'Call us'),
				' to arrange delivery or collection.'
			)
		);
	}

	window.wp.plugins.registerPlugin('lt-cart-delivery-note', {
		// The slot clones its children with { cart, extensions, context }.
		render: function () {
			return el(OrderMeta, null, el(DeliveryNote));
		},
		scope: 'woocommerce-checkout',
	});
})();
