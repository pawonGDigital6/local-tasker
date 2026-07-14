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
})();
