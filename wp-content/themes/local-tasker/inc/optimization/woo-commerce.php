<?php
/**
 * Dequeue WooCommerce Square Block style on non-eCommerce pages.
 */

/**
 * Dequeue WooCommerce, Square Blocks, and React/GTM Block dependencies on non-eCommerce pages.
 */
add_action('wp_enqueue_scripts', 'purge_woocommerce_and_square_assets', 9999);
add_action('wp_print_styles', 'purge_woocommerce_and_square_assets', 9999);

function purge_woocommerce_and_square_assets()
{
	// 1. Safety Check: Exit early if WooCommerce isn't active
	if (!class_exists('WooCommerce')) {
		return;
	}

	// 2. Strict Conditional Check: Target only non-eCommerce pages
	// (Exclude Shop, Product Pages, Cart, Checkout, and My Account)
	if (!is_woocommerce() && !is_product() && !is_cart() && !is_checkout() && !is_account_page()) {

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
					wp_dequeue_script($handle);
					wp_deregister_script($handle);
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
					wp_dequeue_style($handle);
					wp_deregister_style($handle);
				}
			}
		}
	}
}
