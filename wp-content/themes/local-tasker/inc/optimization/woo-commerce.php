<?php
/**
 * Dequeue WooCommerce Square Block style on non-eCommerce pages.
 */

/**
 * Dequeue WooCommerce, Square Blocks, and React/GTM Block dependencies on non-eCommerce pages.
 */
add_action('wp_enqueue_scripts', 'purge_woocommerce_and_square_assets', 9999);
add_action('wp_print_styles', 'purge_woocommerce_and_square_assets', 9999);

/**
 * Is this a page where the WooCommerce cart/checkout block bundles are needed?
 */
function lt_is_ecommerce_page()
{
	if (!class_exists('WooCommerce')) {
		return true;
	}

	return is_woocommerce() || is_product() || is_cart() || is_checkout() || is_account_page();
}

/**
 * Block-only styles that some plugins print too late for wp_dequeue_style()
 * to catch them. Suppressed at tag level instead - see below.
 */
function lt_blocked_block_style_handles()
{
	return array(
		'wc-square-cart-checkout-block',
		'wc-square-cart-checkout-blocks',
	);
}

/**
 * Drop late-printed cart/checkout block styles on non-eCommerce pages.
 *
 * WooCommerce Square enqueues this stylesheet after wp_print_styles has already
 * run its callbacks, so dequeuing alone leaves it in the <head> (~30 KB of
 * render-blocking CSS on every page).
 */
add_filter('style_loader_tag', 'lt_filter_block_style_tag', 10, 2);

function lt_filter_block_style_tag($tag, $handle)
{
	if (is_admin() || lt_is_ecommerce_page()) {
		return $tag;
	}

	return in_array($handle, lt_blocked_block_style_handles(), true) ? '' : $tag;
}

function purge_woocommerce_and_square_assets()
{
	// 1. Safety Check: Exit early if WooCommerce isn't active
	if (!class_exists('WooCommerce')) {
		return;
	}

	// 2. Strict Conditional Check: Target only non-eCommerce pages
	// (Exclude Shop, Product Pages, Cart, Checkout, and My Account)
	if (!lt_is_ecommerce_page()) {

		// The GTM WooCommerce Blocks integration only reports on the Cart and
		// Checkout blocks, but it declares wp-data as a dependency - which pulls
		// React, ReactDOM and ~19 core scripts onto every page. Dequeuing the
		// dependants below is what actually removes that chain; dequeuing
		// wp-data on its own does nothing while a queued script still needs it.
		wp_dequeue_script('gtm4wp-woocommerce-blocks');

		/**
		 * DEQUEUE SCRIPTS (JS)
		 */
		// Core WooCommerce Ajax, Fragments, and Block UI
		wp_dequeue_script('woocommerce');
		wp_dequeue_script('wc-add-to-cart');
		wp_dequeue_script('wc-cart-fragments');
		wp_dequeue_script('jquery-blockui');

		// Target GTM / WooCommerce Blocks and the React data-dependency tree
		global $wp_scripts;
		if (!empty($wp_scripts->queue)) {
			foreach ($wp_scripts->queue as $handle) {
				if (
					strpos($handle, 'wc-block') !== false ||
					strpos($handle, 'square') !== false ||
					strpos($handle, 'wp-block') !== false ||
					strpos($handle, 'wp-data') !== false ||
					strpos($handle, 'wp-components') !== false
				) {
					// Dequeue only. Deregistering these removes them from the
					// registry entirely, so any other plugin that declares them
					// as a dependency silently loses its own script as well.
					wp_dequeue_script($handle);
				}
			}
		}

		/**
		 * DEQUEUE STYLES (CSS)
		 */
		// Core WooCommerce and Layout CSS
		wp_dequeue_style('woocommerce-layout');
		wp_dequeue_style('woocommerce-general');
		wp_dequeue_style('woocommerce-smallscreen');

		// Loop through and killer-strike all Square and WooCommerce dynamic Block CSS handles
		global $wp_styles;
		if (!empty($wp_styles->queue)) {
			foreach ($wp_styles->queue as $handle) {
				if (
					strpos($handle, 'square') !== false ||
					strpos($handle, 'wc-block') !== false ||
					strpos($handle, 'woocommerce') !== false
				) {
					// Keep the theme's own WooCommerce styling: the header
					// mini-cart and cart badge render on every page.
					if (strpos($handle, 'local-tasker') !== false || 'lt-storefront' === $handle) {
						continue;
					}

					wp_dequeue_style($handle);
				}
			}
		}
	}
}
