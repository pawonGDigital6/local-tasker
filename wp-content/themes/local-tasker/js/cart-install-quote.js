/**
 * "I would also like an installation quote" on the cart page.
 *
 * The product page offers the installation quote as a checkbox before adding to
 * cart. This panel lets the customer change that choice per line from the cart,
 * so the request can be made later in the journey.
 *
 * The cart is the WooCommerce Cart block, so the panel is rendered through
 * WooCommerce's own ExperimentalOrderMeta slot (cart totals sidebar) instead of
 * patching the block's markup. State lives on the cart line only: each item's
 * 'lt-install' Store API data says whether the product offers a quote and
 * whether one is requested, and a change goes back through extensionCartUpdate()
 * (handled by lt_set_cart_item_install_quote() in inc/lt-woocommerce-flooring.php),
 * which returns the updated cart to every block. Nothing here affects price.
 *
 * Markup reuses the product page's install opt-in classes
 * (woocommerce/partials/product-choose-option.php), which the theme already
 * compiles, so this needs no stylesheet rebuild.
 */
(function () {
	if (!window.wp || !window.wp.plugins || !window.wp.element || !window.wc || !window.wc.blocksCheckout) {
		return;
	}

	var el = window.wp.element.createElement;
	var useState = window.wp.element.useState;
	var registerPlugin = window.wp.plugins.registerPlugin;
	var blocksCheckout = window.wc.blocksCheckout;
	var OrderMeta = blocksCheckout.ExperimentalOrderMeta;
	var extensionCartUpdate = blocksCheckout.extensionCartUpdate;
	var decode = window.wp.htmlEntities ? window.wp.htmlEntities.decodeEntities : function (s) { return s; };

	if (!OrderMeta || typeof extensionCartUpdate !== 'function') {
		return;
	}

	/*
	 * Toggling re-keys the line (see lt_set_cart_item_install_quote()), so the
	 * Cart block sees the old key vanish and announces '"X" was removed from your
	 * cart.' — untrue, the line is still there. Silence that notice for the one
	 * line being toggled, through the filter WooCommerce provides for it; real
	 * removals are unaffected.
	 */
	var rekeying = {};
	if (window.wp.hooks) {
		window.wp.hooks.addFilter(
			'woocommerce_show_cart_item_removed_notice',
			'local-tasker/cart-install-quote',
			function (show, item) {
				return item && rekeying[item.key] ? false : show;
			}
		);
	}

	function itemLabel(item) {
		var name = decode(item.name || '');
		var parts = (item.variation || [])
			.map(function (v) { return decode(v.value || ''); })
			.filter(Boolean);
		return parts.length ? name + ' — ' + parts.join(', ') : name;
	}

	function Row(props) {
		var item = props.item;
		var data = item.extensions['lt-install'];
		var busy = props.busyKey === item.key;
		var inputId = 'lt-cart-install-' + item.key;

		return el(
			'label',
			{
				htmlFor: inputId,
				className: 'flex items-start gap-4 px-6 py-4 border-t border-[#ccc] cursor-pointer select-none transition-colors duration-200 hover:bg-lt-snow-drift' + (props.busyKey ? ' opacity-50' : ''),
			},
			el('input', {
				type: 'checkbox',
				id: inputId,
				className: 'sr-only peer',
				checked: !!data.requested,
				disabled: !!props.busyKey,
				onChange: function (e) { props.onToggle(item.key, e.target.checked); },
			}),
			el(
				'span',
				{
					className: 'w-4 h-4 rounded border border-[#b3b3b3] flex items-center justify-center shrink-0 bg-lt-white peer-checked:bg-lt-brand peer-checked:border-lt-brand transition-colors duration-150 mt-1',
					'aria-hidden': 'true',
				},
				el(
					'svg',
					{ className: 'w-3 h-3', viewBox: '0 0 12 12', fill: 'none', xmlns: 'http://www.w3.org/2000/svg' },
					el('path', { d: 'M2 6l3 3 5-5', stroke: 'white', strokeWidth: '1.5', strokeLinecap: 'round', strokeLinejoin: 'round' })
				)
			),
			el(
				'span',
				{ className: 'leading-[1.5] text-[#0d0d0d] text-caption-sm' },
				itemLabel(item),
				busy ? el('span', { className: 'block text-xs mt-1' }, 'Updating…') : null
			)
		);
	}

	function InstallQuotePanel(props) {
		var state = useState('');
		var busyKey = state[0];
		var setBusyKey = state[1];

		if (props.context !== 'woocommerce/cart' || !props.cart) {
			return null;
		}

		var items = (props.cart.cartItems || []).filter(function (item) {
			return item.extensions && item.extensions['lt-install'] && item.extensions['lt-install'].offered;
		});

		if (!items.length) {
			return null;
		}

		function onToggle(key, requested) {
			setBusyKey(key);
			rekeying[key] = true;
			extensionCartUpdate({ namespace: 'lt-install', data: { key: key, requested: requested } })
				.catch(function () {})
				.finally(function () {
					delete rekeying[key];
					setBusyKey('');
				});
		}

		return el(
			'div',
			{ className: 'lt-cart-install-quote mt-4' },
			el(
				'div',
				{ className: 'rounded-2xl border border-[#ccc] overflow-hidden bg-lt-white' },
				el(
					'div',
					{ className: 'px-6 py-4 leading-[1.5] text-[#0d0d0d]' },
					el('span', { className: 'font-semi-ext font-bold text-caption-md' }, 'I would also like an installation quote'),
					el('span', { className: 'block text-xs mt-1' }, 'Tick the products you would like installed. A team member will get in touch regarding installation after checkout.')
				),
				items.map(function (item) {
					return el(Row, { key: item.key, item: item, busyKey: busyKey, onToggle: onToggle });
				})
			)
		);
	}

	registerPlugin('lt-cart-install-quote', {
		render: function () {
			// The slot clones its children with { cart, extensions, context }.
			return el(OrderMeta, null, el(InstallQuotePanel));
		},
		scope: 'woocommerce-checkout',
	});
})();
