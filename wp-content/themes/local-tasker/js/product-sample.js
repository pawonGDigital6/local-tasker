/**
 * "Order Sample" button on the single product page.
 *
 * Adds a free sample of the product (and the colour / finish currently picked)
 * through lt_add_sample_to_cart (inc/lt-samples.php), which enforces the
 * per-order limit and refuses a variable product with no colour chosen. The
 * server's message is shown as is, so the rules live in one place.
 *
 * On success it fires the same native `wc_fragment_refresh` the box calculator
 * fires, which refreshes and opens the mini-cart drawer (js/mini-cart.js).
 */
(function () {
	var cfg = window.ltSample;
	if (!cfg) {
		return;
	}

	function selectedVariationId() {
		// Box calculator products: product-single.js keeps the chosen variation here.
		var calcBtn = document.getElementById('lt-add-to-cart');
		if (calcBtn && calcBtn.dataset.variationId) {
			return calcBtn.dataset.variationId;
		}
		// WooCommerce's own variations form.
		var input = document.querySelector('form.variations_form input[name="variation_id"]');
		return input && input.value ? input.value : '0';
	}

	function init() {
		var btn = document.getElementById('lt-order-sample');
		var status = document.getElementById('lt-sample-status');
		if (!btn) {
			return;
		}

		function say(message) {
			if (!status) {
				return;
			}
			status.textContent = message || '';
			status.classList.toggle('hidden', !message);
		}

		btn.addEventListener('click', function () {
			say('');
			btn.disabled = true;
			btn.textContent = cfg.labels.adding;

			var body = new FormData();
			body.append('action', 'lt_add_sample_to_cart');
			body.append('nonce', cfg.nonce);
			body.append('product_id', cfg.productId);
			body.append('variation_id', selectedVariationId());

			fetch(cfg.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (res && res.success) {
						btn.textContent = cfg.labels.added;
						document.body.dispatchEvent(new CustomEvent('wc_fragment_refresh'));
						setTimeout(function () {
							btn.disabled = false;
							btn.textContent = cfg.labels.default;
						}, 2000);
						return;
					}
					btn.disabled = false;
					btn.textContent = cfg.labels.default;
					say(res && res.data && res.data.message ? res.data.message : cfg.labels.error);
				})
				.catch(function () {
					btn.disabled = false;
					btn.textContent = cfg.labels.default;
					say(cfg.labels.error);
				});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
