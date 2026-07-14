<?php
/**
 * Local Tasker — Storefront bootstrap.
 *
 * Wires together the flooring engine, the shop filter engine and the AJAX handler,
 * and enqueues the storefront + single-product assets only where they're needed.
 *
 * Loaded from functions.php inside the `class_exists( 'WooCommerce' )` guard.
 *
 * @package local-tasker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/lt-woocommerce-flooring.php';
require_once get_template_directory() . '/inc/lt-shop-filter.php';
require_once get_template_directory() . '/inc/class-shop-filter-ajax.php';

/**
 * Does the current request need the shop archive assets?
 *
 * @return bool
 */
function lt_needs_shop_assets() {
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
		return true;
	}
	if ( is_singular() && ( has_block( 'acf-block/shop-archive' ) || has_block( 'acf-block/hero' ) ) ) {
		return true;
	}
	return false;
}

/**
 * Enqueue storefront (archive + hero search) and single-product assets.
 *
 * @return void
 */
function lt_enqueue_storefront_assets() {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();

	// Archive / hero search.
	if ( lt_needs_shop_assets() ) {
		wp_enqueue_style(
			'lt-storefront',
			$uri . '/assets/css/lt-storefront.css',
			array(),
			file_exists( $dir . '/assets/css/lt-storefront.css' ) ? filemtime( $dir . '/assets/css/lt-storefront.css' ) : _S_VERSION
		);

		wp_enqueue_script(
			'lt-shop-filter',
			$uri . '/assets/js/lt-shop-filter.js',
			array(),
			file_exists( $dir . '/assets/js/lt-shop-filter.js' ) ? filemtime( $dir . '/assets/js/lt-shop-filter.js' ) : _S_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);

		wp_localize_script(
			'lt-shop-filter',
			'ltShop',
			array(
				'ajaxUrl' => esc_url( admin_url( 'admin-ajax.php' ) ),
			)
		);
	}

	// Single product.
	if ( function_exists( 'is_product' ) && is_product() ) {
		// Storefront CSS carries the shared tokens + button styles.
		wp_enqueue_style(
			'lt-storefront',
			$uri . '/assets/css/lt-storefront.css',
			array(),
			file_exists( $dir . '/assets/css/lt-storefront.css' ) ? filemtime( $dir . '/assets/css/lt-storefront.css' ) : _S_VERSION
		);
		wp_enqueue_style(
			'lt-single-product',
			$uri . '/assets/css/lt-single-product.css',
			array( 'lt-storefront' ),
			file_exists( $dir . '/assets/css/lt-single-product.css' ) ? filemtime( $dir . '/assets/css/lt-single-product.css' ) : _S_VERSION
		);

		wp_enqueue_script(
			'lt-flooring-calculator',
			$uri . '/assets/js/lt-flooring-calculator.js',
			array(),
			file_exists( $dir . '/assets/js/lt-flooring-calculator.js' ) ? filemtime( $dir . '/assets/js/lt-flooring-calculator.js' ) : _S_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'lt_enqueue_storefront_assets', 20 );

/**
 * Does the Shop page's own content contain the shop-archive block?
 *
 * Used by archive-product.php to decide whether the block should replace the
 * default WooCommerce product loop.
 *
 * @return bool
 */
function lt_shop_page_has_archive_block() {
	if ( ! function_exists( 'wc_get_page_id' ) ) {
		return false;
	}
	$shop_page_id = wc_get_page_id( 'shop' );
	if ( $shop_page_id <= 0 ) {
		return false;
	}
	$content = get_post_field( 'post_content', $shop_page_id );
	return $content && has_block( 'acf-block/shop-archive', $content );
}

/**
 * When the single-product form is submitted via "Proceed to Checkout"
 * (hidden field lt_checkout=1), redirect to the checkout after the item is added.
 *
 * @param string $url Default redirect URL.
 * @return string
 */
function lt_checkout_redirect( $url ) {
	if ( isset( $_REQUEST['lt_checkout'] ) && '1' === $_REQUEST['lt_checkout'] && function_exists( 'wc_get_checkout_url' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return wc_get_checkout_url();
	}
	return $url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'lt_checkout_redirect' );

/**
 * Populate the product-quantity add-to-cart with the calculator's box count.
 *
 * The single product form posts `quantity` (boxes) natively, so nothing extra is
 * required here — this filter simply guarantees a sane minimum of 1.
 *
 * @param float      $qty     Quantity.
 * @param WC_Product $product Product.
 * @return float
 */
function lt_min_quantity( $qty, $product ) {
	return max( 1, (float) $qty );
}
add_filter( 'woocommerce_add_to_cart_quantity', 'lt_min_quantity', 10, 2 );
