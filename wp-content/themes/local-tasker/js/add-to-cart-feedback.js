/**
 * Add-to-cart feedback for product cards.
 *
 * WooCommerce's default on a listing leaves two controls stacked in one card:
 * the button you just used, plus an injected "View cart" link. In a responsive
 * grid that card then stands taller than its neighbours and the row reflows.
 *
 * Ordering note, because it is easy to get wrong: WooCommerce binds its
 * added_to_cart handler inside jQuery(function(){…}), i.e. on DOM ready, while
 * this file runs at parse time. So THIS handler is bound first and runs first,
 * before WooCommerce has injected its link. Anything that needs to act on that
 * link must therefore be deferred. queueMicrotask is used rather than
 * setTimeout so the work still lands before the browser paints and nothing
 * flashes on screen.
 *
 * Modes, chosen with the `lt_cards_after_add` filter:
 *
 *   view_cart (default)
 *     The Add to Cart button is hidden and WooCommerce's own link is styled to
 *     take its place, so exactly one control is visible. The hiding is done in
 *     CSS off the `added` class WooCommerce sets itself, which keeps it immune
 *     to handler ordering entirely. Removing the product in the mini-cart puts
 *     the card back to Add to Cart so it can never offer a stale link.
 *
 *   revert
 *     The link is discarded and the button confirms with a brief "Added" before
 *     returning to "Add to Cart". Better suited to this catalogue, where a
 *     customer ordering twelve boxes of one board clicks the same button twelve
 *     times.
 */
(function () {
	var settings = window.ltAddToCart || {};
	var MODE = settings.mode === 'revert' ? 'revert' : 'view_cart';
	var ADDED_LABEL = settings.addedLabel || 'Added';
	var VIEW_CART_LABEL = settings.viewCartLabel || 'View Cart';
	var REVERT_AFTER = 2000;

	if (!window.jQuery) {
		return;
	}

	// Only loop cards are handled. The single product page is a deliberate,
	// high-intent decision where WooCommerce's own behaviour is left alone.
	function cardButton(el) {
		return el && el.closest('.lt-product-card') ? el : null;
	}

	function injectedLink(button) {
		return button.parentNode ? button.parentNode.querySelector('.added_to_cart') : null;
	}

	// Put a card back to its starting state.
	function reset(button) {
		var link = injectedLink(button);

		if (link) {
			link.remove();
		}

		button.classList.remove('added', 'loading', 'is-added');
		delete button.dataset.ltState;
	}

	function showAdded(button) {
		if (button.dataset.ltState === 'added') {
			return;
		}

		var label = button.textContent.trim();

		button.dataset.ltState = 'added';
		button.dataset.ltLabel = label;
		button.textContent = ADDED_LABEL;
		button.classList.add('is-added');

		window.setTimeout(function () {
			// The card may have been replaced by a filter refresh in the meantime.
			if (!button.isConnected || button.dataset.ltState !== 'added') {
				return;
			}

			button.textContent = button.dataset.ltLabel;
			delete button.dataset.ltLabel;
			reset(button);
		}, REVERT_AFTER);
	}

	var $body = window.jQuery(document.body);

	$body.on('added_to_cart', function (event, fragments, cartHash, $button) {
		var button = cardButton($button && $button.length ? $button[0] : null);

		if (!button) {
			return;
		}

		// Deferred: WooCommerce has not injected its link yet at this point.
		window.queueMicrotask(function () {
			if (MODE === 'revert') {
				reset(button);
				showAdded(button);

				return;
			}

			var link = injectedLink(button);

			if (link) {
				// Match the card's title-case wording. The styling itself comes
				// from CSS, which does not care when the link appeared.
				link.textContent = VIEW_CART_LABEL;
				link.setAttribute('aria-label', VIEW_CART_LABEL);
			}
		});
	});

	// Keep the card honest. The mini-cart's remove control carries
	// data-product_id, so the matching card can be restored without asking the
	// server what is in the cart.
	$body.on('removed_from_cart', function (event, fragments, cartHash, $button) {
		if (MODE !== 'view_cart') {
			return;
		}

		var removed = $button && $button.length ? $button[0].getAttribute('data-product_id') : '';

		if (!removed) {
			return;
		}

		// Exact comparison: a substring test would match "add-to-cart=17"
		// inside product 1744.
		document.querySelectorAll('.lt-product-card .add_to_cart_button').forEach(function (button) {
			if (button.getAttribute('data-product_id') === String(removed)) {
				reset(button);
			}
		});
	});
})();
