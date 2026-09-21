/**
 * Header mini-cart drawer.
 *
 * Clicking the cart icon opens a slide-out panel (WC_Widget_Cart markup)
 * instead of navigating to the cart page. The link keeps its real
 * `href="/cart/"` so it still works with JS disabled or for middle-click /
 * "open in new tab".
 */
(function () {
	var panel = document.getElementById('lt-mini-cart-panel');
	var overlay = document.getElementById('lt-mini-cart-overlay');

	if (!panel || !overlay) {
		return;
	}

	function openPanel() {
		panel.classList.remove('translate-x-full');
		overlay.classList.remove('hidden');
		panel.setAttribute('aria-hidden', 'false');
		document.querySelectorAll('[data-lt-cart-toggle]').forEach(function (toggle) {
			toggle.setAttribute('aria-expanded', 'true');
		});
		document.body.classList.add('overflow-hidden');
	}

	function closePanel() {
		panel.classList.add('translate-x-full');
		overlay.classList.add('hidden');
		panel.setAttribute('aria-hidden', 'true');
		document.querySelectorAll('[data-lt-cart-toggle]').forEach(function (toggle) {
			toggle.setAttribute('aria-expanded', 'false');
		});
		document.body.classList.remove('overflow-hidden');
	}

	// Delegated so it keeps working after wc-cart-fragments replaces the
	// `a.cart-contents` element's markup on every cart update.
	document.addEventListener('click', function (event) {
		var toggle = event.target.closest('[data-lt-cart-toggle]');
		if (toggle) {
			event.preventDefault();
			openPanel();
			return;
		}

		if (event.target.closest('#lt-mini-cart-close') || event.target === overlay) {
			closePanel();
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			closePanel();
		}
	});

	// Open automatically after a product is added, classic AJAX add-to-cart flow.
	if (window.jQuery) {
		window.jQuery(document.body).on('added_to_cart', openPanel);
	}

	/* ──────────────────────────────────────────────────────────────
	   Quantity stepper

	   Adding from a product card fixes the quantity at one, so the drawer is
	   where it can be changed. The markup comes from
	   lt_mini_cart_item_quantity() (inc/woocommerce.php) and is replaced
	   wholesale on every cart-fragment refresh, so every handler here is
	   delegated off the document rather than bound to the controls.
	   ────────────────────────────────────────────────────────────── */
	var settings = window.ltMiniCart || {};
	var inFlight = false;

	function stepperOf(el) {
		return el && el.closest ? el.closest('.lt-mini-cart__qty') : null;
	}

	// Disable the whole row while a change is in flight. Two quick clicks would
	// otherwise race, and the slower response would win and set a stale figure.
	function setBusy(stepper, busy) {
		inFlight = busy;
		stepper.querySelectorAll('button, input').forEach(function (control) {
			control.disabled = busy;
		});
		stepper.style.opacity = busy ? '0.5' : '';
	}

	function commit(stepper, quantity) {
		if (inFlight || !settings.ajaxUrl) {
			return;
		}

		var key = stepper.dataset.cartItemKey;
		var max = parseInt(stepper.dataset.max, 10) || 0;

		quantity = Math.max(1, quantity);
		if (max > 0) {
			quantity = Math.min(quantity, max);
		}

		var input = stepper.querySelector('.lt-mini-cart__qty-input');
		if (input && parseInt(input.value, 10) === quantity) {
			// Clamped back to what is already in the cart — nothing to send, but
			// the field may be showing the out-of-range figure the user typed.
			input.value = quantity;
			return;
		}
		if (input) {
			input.value = quantity;
		}

		setBusy(stepper, true);

		var body = new FormData();
		body.append('action', 'lt_update_cart_item_qty');
		body.append('cart_item_key', key);
		body.append('quantity', quantity);
		body.append('nonce', settings.nonce || '');

		fetch(settings.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
			.then(function (response) { return response.json(); })
			.then(function () {
				// Repaint from WooCommerce's own fragments: the line, the
				// subtotal and the header badge all update from one source.
				inFlight = false;
				document.body.dispatchEvent(new CustomEvent('wc_fragment_refresh'));
			})
			.catch(function () {
				setBusy(stepper, false);
			});
	}

	document.addEventListener('click', function (event) {
		var button = event.target.closest('[data-lt-qty-step]');
		if (!button) {
			return;
		}

		event.preventDefault();

		var stepper = stepperOf(button);
		var input = stepper && stepper.querySelector('.lt-mini-cart__qty-input');
		if (!stepper || !input) {
			return;
		}

		commit(stepper, (parseInt(input.value, 10) || 1) + parseInt(button.dataset.ltQtyStep, 10));
	});

	// Typed edits commit on blur / Enter rather than per keystroke, so typing
	// "12" does not first send a 1.
	document.addEventListener('change', function (event) {
		var input = event.target.closest('.lt-mini-cart__qty-input');
		if (!input) {
			return;
		}
		var stepper = stepperOf(input);
		if (stepper) {
			commit(stepper, parseInt(input.value, 10) || 1);
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key !== 'Enter') {
			return;
		}
		var input = event.target.closest('.lt-mini-cart__qty-input');
		if (input) {
			event.preventDefault();
			input.blur();
		}
	});
})();
