/**
 * Keeps the header cart-count badge live without a full page reload.
 *
 * Classic (shortcode) cart/checkout only fire jQuery events like
 * 'updated_wc_div' / 'wc_fragment_refresh' — they never touch our header
 * markup directly. The Cart/Checkout blocks don't fire those jQuery events
 * at all; they update the '@woocommerce/store' Redux-ish data store instead.
 * Rather than branching on which one is active, just re-fetch the
 * authoritative count from the Store API cart endpoint whenever either
 * signal fires and paint it into every .cart-count badge on the page.
 */
(function () {
	function updateBadge(count) {
		document.querySelectorAll('.cart-count').forEach(function (el) {
			el.textContent = count;
			el.classList.toggle('hidden', count <= 0);
			el.classList.toggle('flex', count > 0);
		});
	}

	function fetchCartCount() {
		if (!window.ltCartBadge || !window.ltCartBadge.storeApiUrl) {
			return;
		}
		fetch(window.ltCartBadge.storeApiUrl, {
			credentials: 'same-origin',
			headers: { Accept: 'application/json' },
		})
			.then(function (response) {
				return response.json();
			})
			.then(function (data) {
				if (data && Array.isArray(data.items)) {
					updateBadge(data.items.length);
				}
			})
			.catch(function () {});
	}

	document.addEventListener('DOMContentLoaded', function () {
		if (window.jQuery) {
			window.jQuery(document.body).on(
				'wc_fragment_refresh added_to_cart removed_from_cart updated_wc_div updated_cart_totals wc_cart_emptied applied_coupon removed_coupon',
				fetchCartCount
			);
		}

		// WooCommerce Blocks (Cart/Checkout blocks) push updates through wp.data.
		if (window.wp && window.wp.data && typeof window.wp.data.subscribe === 'function') {
			var lastCount = null;
			window.wp.data.subscribe(function () {
				var store = window.wp.data.select('wc/store/cart');
				if (!store) {
					return;
				}
				var cartData = store.getCartData ? store.getCartData() : null;
				if (cartData && Array.isArray(cartData.items) && cartData.items.length !== lastCount) {
					lastCount = cartData.items.length;
					updateBadge(cartData.items.length);
				}
			});
		}
	});
})();
