/**
 * "Order Sample" buttons — single product page and product cards.
 *
 * Any element with data-lt-sample-add and data-product-id adds a free sample
 * of that product through lt_add_sample_to_cart (inc/lt-samples.php), which
 * enforces the per-order limit and refuses a variable product with no colour
 * chosen. The server's message is shown as is, so the rules live in one place.
 *
 * One delegated click handler, so cards added to the page later over AJAX
 * (Popular Products tabs, Load More) work without re-binding. On the product
 * page the colour / finish currently picked is sent; cards only carry this
 * button for simple products (variable cards link to the product page).
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

	// A card keeps its status line next to its button; the product page prints
	// it under the note, apart from the button (product-calculator.php).
	function statusFor(btn) {
		var wrap = btn.closest('[data-lt-sample]');
		return (wrap && wrap.querySelector('[data-lt-sample-status]')) || document.getElementById('lt-sample-status');
	}

	function say(status, message) {
		if (!status) {
			return;
		}
		status.textContent = message || '';
		status.classList.toggle('hidden', !message);
	}

	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-lt-sample-add]');
		if (!btn || btn.disabled) {
			return;
		}
		e.preventDefault();

		var status = statusFor(btn);
		say(status, '');
		btn.disabled = true;
		btn.textContent = cfg.labels.adding;

		var body = new FormData();
		body.append('action', 'lt_add_sample_to_cart');
		body.append('nonce', cfg.nonce);
		body.append('product_id', btn.dataset.productId);
		body.append('variation_id', btn.id === 'lt-order-sample' ? selectedVariationId() : '0');

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
				say(status, res && res.data && res.data.message ? res.data.message : cfg.labels.error);
			})
			.catch(function () {
				btn.disabled = false;
				btn.textContent = cfg.labels.default;
				say(status, cfg.labels.error);
			});
	});
})();
