<?php
/**
 * Local Tasker — flooring samples ("Order Sample").
 *
 * Client rules (confirmed 2026-10-27):
 *   • Up to 3 samples per order (Theme Options → Site Options — Samples).
 *   • Samples are free; the customer pays a flat sample delivery charge ($15),
 *     which disappears when the same order includes full flooring.
 *   • Samples only go where flooring is delivered (the Metro Melbourne and VIC
 *     Outer Metro zones), or can be collected with local pickup.
 *   • Offered on every product by default; the "Offer Samples" toggle turns it off
 *     per product, and Theme Option → "Enable Order Sample" turns it off site-wide.
 *   • Sample orders are ordinary WooCommerce orders — same fulfilment process.
 *
 * How it fits WooCommerce without touching the shipping matrix:
 *   • Every sample is a line of ONE hidden, $0, weightless "Flooring Sample"
 *     product (created on first use, id kept in the lt_sample_product_id option).
 *     The line records which flooring product and colour it is a sample of, and
 *     is renamed "Sample: <product>" for the cart, checkout, emails and order.
 *     Flooring stock is never touched.
 *   • Delivery: weight 0 means samples add nothing to a flooring order's
 *     weight-based rate — that is the waiver. A cart holding ONLY samples gets
 *     its delivery rates swapped for the flat sample charge. Zones with no rate
 *     still get none, so the delivery-area rule comes from the existing zones.
 *
 * @package local-tasker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 *  Settings
 * ---------------------------------------------------------------------- */

/**
 * Sample delivery charge, from Theme Options (default $15).
 *
 * @return float
 */
function lt_sample_delivery_fee(): float {
	$fee = function_exists( 'get_field' ) ? get_field( 'sample_delivery_fee', 'option' ) : '';
	return ( '' === $fee || null === $fee || false === $fee ) ? 15.0 : max( 0.0, (float) $fee );
}

/**
 * Maximum samples per order, from Theme Options (default 3).
 *
 * @return int
 */
function lt_sample_max_per_order(): int {
	$max = function_exists( 'get_field' ) ? get_field( 'sample_max_per_order', 'option' ) : '';
	return ( '' === $max || null === $max || false === $max ) ? 3 : max( 1, (int) $max );
}

/**
 * Site-wide "Enable Order Sample" switch, from Theme Options.
 *
 * On until it is saved as off, so samples keep working before the Theme
 * Options page has ever been saved.
 *
 * @return bool
 */
function lt_samples_enabled(): bool {
	$on = function_exists( 'get_field' ) ? get_field( 'samples_enabled', 'option' ) : null;
	return null === $on || '' === $on || (bool) $on;
}

/**
 * Does this product offer samples? Driven by its "Offer Samples" toggle, and
 * only while the site-wide switch is on.
 *
 * On unless explicitly switched off ('0'), so products saved before the toggle
 * existed, or never saved since, offer samples. The hidden sample product never
 * offers a sample of itself.
 *
 * @param int $product_id Product id (parent id for variations).
 * @return bool
 */
function lt_product_offers_samples( $product_id ): bool {
	$product_id = (int) $product_id;
	if ( ! lt_samples_enabled() || ( $product_id && $product_id === lt_get_sample_product_id() ) ) {
		return false;
	}
	return '0' !== (string) get_post_meta( $product_id, 'sample_enabled', true );
}

/**
 * One-time switch to "on by default".
 *
 * While samples were off by default, the product edit screen saved
 * sample_enabled = '0' for any product edited without touching the toggle, so
 * '0' then meant "never switched on", not "switched off". Clearing those once
 * lets every existing product follow the new default. Later '0' values (a
 * product deliberately switched off) are kept.
 */
function lt_samples_default_on_migration(): void {
	if ( get_option( 'lt_samples_default_on' ) ) {
		return;
	}
	delete_metadata( 'post', 0, 'sample_enabled', '0', true );
	update_option( 'lt_samples_default_on', 1 );
}
add_action( 'init', 'lt_samples_default_on_migration' );

/* -------------------------------------------------------------------------
 *  The hidden sample product
 * ---------------------------------------------------------------------- */

/**
 * Id of the hidden "Flooring Sample" product, optionally creating it.
 *
 * @param bool $create Create it when missing (or trashed).
 * @return int 0 when it does not exist and $create is false.
 */
function lt_get_sample_product_id( bool $create = false ): int {
	$id      = (int) get_option( 'lt_sample_product_id', 0 );
	$product = $id ? wc_get_product( $id ) : null;

	if ( $product && 'publish' === $product->get_status() && $product->is_type( 'simple' ) ) {
		return $id;
	}

	return $create ? lt_create_sample_product() : 0;
}

/**
 * Create the hidden sample product.
 *
 * $0, no weight (so it never moves a weight-based delivery rate), no tax, no
 * stock tracking, one per line, hidden from the shop and search.
 *
 * @return int Product id, 0 on failure.
 */
function lt_create_sample_product(): int {
	$product = new WC_Product_Simple();
	$product->set_name( __( 'Flooring Sample', 'local-tasker' ) );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'hidden' );
	$product->set_regular_price( '0' );
	$product->set_tax_status( 'none' );
	$product->set_manage_stock( false );
	$product->set_stock_status( 'instock' );
	$product->set_sold_individually( true );
	$product->set_virtual( false );
	$product->set_weight( '' );
	$product->set_reviews_allowed( false );
	$product->set_short_description( __( 'Free flooring sample. Created automatically by the theme for the "Order Sample" button — do not delete.', 'local-tasker' ) );
	$product->update_meta_data( 'install_quote_enabled', '0' );
	$product->update_meta_data( 'sold_by_box', '0' );

	$id = (int) $product->save();
	if ( $id ) {
		update_option( 'lt_sample_product_id', $id, false );
	}
	return $id;
}

/**
 * Is this cart line a sample?
 *
 * @param array $cart_item Cart item.
 * @return bool
 */
function lt_is_sample_cart_item( array $cart_item ): bool {
	$sample_id = lt_get_sample_product_id();
	return $sample_id && (int) ( $cart_item['product_id'] ?? 0 ) === $sample_id;
}

/**
 * Number of sample lines in the cart.
 *
 * @return int
 */
function lt_cart_sample_count(): int {
	if ( ! WC()->cart ) {
		return 0;
	}
	$count = 0;
	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( lt_is_sample_cart_item( $cart_item ) ) {
			++$count;
		}
	}
	return $count;
}

/* -------------------------------------------------------------------------
 *  Adding a sample
 * ---------------------------------------------------------------------- */

/**
 * Set while lt_ajax_add_sample_to_cart() adds a line, so the add-to-cart
 * validation below can refuse the sample product from any other route.
 *
 * @var bool
 */
$GLOBALS['lt_adding_sample'] = false;

/**
 * AJAX: add a sample of a flooring product to the cart.
 */
function lt_ajax_add_sample_to_cart(): void {
	check_ajax_referer( 'lt_add_sample', 'nonce' );

	$product_id   = absint( $_POST['product_id'] ?? 0 );
	$variation_id = absint( $_POST['variation_id'] ?? 0 );
	$product      = $product_id ? wc_get_product( $product_id ) : null;

	if ( ! $product || 'publish' !== $product->get_status() || ! lt_product_offers_samples( $product_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Samples are not available for this product.', 'local-tasker' ) ) );
	}

	$label = $product->get_name();

	if ( $product->is_type( 'variable' ) ) {
		// Only one colour / finish: nothing to choose, so a card (or a page
		// with no colour picked) gets that one.
		$children = $product->get_children();
		if ( ! $variation_id && 1 === count( $children ) ) {
			$variation_id = (int) $children[0];
		}
		$variation = $variation_id ? wc_get_product( $variation_id ) : null;
		if ( ! $variation || $variation->get_parent_id() !== $product_id ) {
			wp_send_json_error( array( 'message' => __( 'Please choose a colour / finish first.', 'local-tasker' ) ) );
		}
		$names = array();
		foreach ( $variation->get_attributes() as $taxonomy => $slug ) {
			if ( '' === (string) $slug ) {
				continue;
			}
			$term    = taxonomy_exists( $taxonomy ) ? get_term_by( 'slug', $slug, $taxonomy ) : false;
			$names[] = $term ? $term->name : $slug;
		}
		if ( $names ) {
			$label .= ' — ' . implode( ', ', $names );
		}
	} else {
		$variation_id = 0;
	}

	$max = lt_sample_max_per_order();

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( lt_is_sample_cart_item( $cart_item )
			&& (int) $cart_item['lt_sample_for'] === $product_id
			&& (int) $cart_item['lt_sample_variation'] === $variation_id ) {
			wp_send_json_error( array( 'message' => __( 'A sample of this product is already in your cart.', 'local-tasker' ) ) );
		}
	}

	if ( lt_cart_sample_count() >= $max ) {
		wp_send_json_error(
			array(
				/* translators: %d: maximum number of samples per order. */
				'message' => sprintf( _n( 'You can order up to %d sample per order.', 'You can order up to %d samples per order.', $max, 'local-tasker' ), $max ),
			)
		);
	}

	$sample_id = lt_get_sample_product_id( true );
	if ( ! $sample_id ) {
		wp_send_json_error( array( 'message' => __( 'Could not add the sample. Please try again.', 'local-tasker' ) ) );
	}

	$GLOBALS['lt_adding_sample'] = true;
	$added = WC()->cart->add_to_cart(
		$sample_id,
		1,
		0,
		array(),
		array(
			'lt_sample_for'       => $product_id,
			'lt_sample_variation' => $variation_id,
			'lt_sample_label'     => $label,
		)
	);
	$GLOBALS['lt_adding_sample'] = false;

	if ( ! $added ) {
		wp_send_json_error( array( 'message' => __( 'Could not add the sample. Please try again.', 'local-tasker' ) ) );
	}

	wp_send_json_success(
		array(
			'cart_count'   => WC()->cart->get_cart_contents_count(),
			'sample_count' => lt_cart_sample_count(),
			'max'          => $max,
		)
	);
}
add_action( 'wp_ajax_lt_add_sample_to_cart', 'lt_ajax_add_sample_to_cart' );
add_action( 'wp_ajax_nopriv_lt_add_sample_to_cart', 'lt_ajax_add_sample_to_cart' );

/**
 * Only the "Order Sample" button may put the sample product in the cart.
 *
 * Stops a hand-made add-to-cart URL or Store API call adding a bare $0 sample
 * with no flooring product attached.
 *
 * @param bool $passed     Validation result so far.
 * @param int  $product_id Product being added.
 * @return bool
 */
function lt_block_direct_sample_add( $passed, $product_id ) {
	$sample_id = lt_get_sample_product_id();
	if ( $sample_id && (int) $product_id === $sample_id && empty( $GLOBALS['lt_adding_sample'] ) ) {
		wc_add_notice( __( 'Please use the "Order Sample" button on a product page to add samples.', 'local-tasker' ), 'error' );
		return false;
	}
	return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'lt_block_direct_sample_add', 10, 2 );

/**
 * Re-check the sample rules whenever the cart is validated (cart page,
 * checkout, Store API checkout) — the last word before an order is placed.
 *
 * Drops sample lines that lost their flooring product (or every sample line
 * while samples are switched off site-wide), and refuses checkout while the
 * cart holds more samples than allowed.
 */
function lt_check_cart_samples(): void {
	if ( ! WC()->cart ) {
		return;
	}

	$enabled = lt_samples_enabled();
	foreach ( WC()->cart->get_cart() as $key => $cart_item ) {
		if ( lt_is_sample_cart_item( $cart_item ) && ( ! $enabled || empty( $cart_item['lt_sample_for'] ) ) ) {
			WC()->cart->remove_cart_item( $key );
		}
	}

	$max = lt_sample_max_per_order();
	if ( lt_cart_sample_count() > $max ) {
		wc_add_notice(
			/* translators: %d: maximum number of samples per order. */
			sprintf( _n( 'You can order up to %d sample per order. Please remove the extra samples.', 'You can order up to %d samples per order. Please remove the extra samples.', $max, 'local-tasker' ), $max ),
			'error'
		);
	}
}
add_action( 'woocommerce_check_cart_items', 'lt_check_cart_samples' );

/* -------------------------------------------------------------------------
 *  How a sample line looks (cart, mini-cart, checkout, order)
 * ---------------------------------------------------------------------- */

/**
 * Name the line after the flooring it samples and borrow that product's image.
 *
 * Runs when a line is added and every time the cart is rebuilt from the
 * session, so every view — block cart/checkout (Store API), mini-cart drawer,
 * and the order line created at checkout — shows "Sample: <product>".
 *
 * @param array $cart_item Cart item.
 * @return array
 */
function lt_dress_sample_cart_item( $cart_item ) {
	if ( ! is_array( $cart_item ) || ! lt_is_sample_cart_item( $cart_item ) || empty( $cart_item['lt_sample_label'] ) ) {
		return $cart_item;
	}

	/* translators: %s: flooring product (and colour) the sample is of. */
	$cart_item['data']->set_name( sprintf( __( 'Sample: %s', 'local-tasker' ), $cart_item['lt_sample_label'] ) );

	$source   = wc_get_product( ! empty( $cart_item['lt_sample_variation'] ) ? $cart_item['lt_sample_variation'] : $cart_item['lt_sample_for'] );
	$image_id = $source ? $source->get_image_id() : 0;
	if ( ! $image_id && $source && $source->get_parent_id() ) {
		$parent   = wc_get_product( $source->get_parent_id() );
		$image_id = $parent ? $parent->get_image_id() : 0;
	}
	if ( $image_id ) {
		$cart_item['data']->set_image_id( $image_id );
	}

	return $cart_item;
}
add_filter( 'woocommerce_add_cart_item', 'lt_dress_sample_cart_item', 20 );
add_filter( 'woocommerce_get_cart_item_from_session', 'lt_dress_sample_cart_item', 20 );

/**
 * Link a sample line to the flooring product, not the hidden sample product.
 *
 * Feeds the block cart (Store API) and the mini-cart alike.
 *
 * @param string $permalink Permalink.
 * @param array  $cart_item Cart item.
 * @return string
 */
function lt_sample_cart_item_permalink( $permalink, $cart_item ) {
	if ( is_array( $cart_item ) && lt_is_sample_cart_item( $cart_item ) && ! empty( $cart_item['lt_sample_for'] ) ) {
		return get_permalink( (int) $cart_item['lt_sample_for'] );
	}
	return $permalink;
}
add_filter( 'woocommerce_cart_item_permalink', 'lt_sample_cart_item_permalink', 10, 2 );

/**
 * Record on the order line which product the sample is of.
 *
 * @param WC_Order_Item_Product $item   Order item.
 * @param string                $unused Cart key.
 * @param array                 $values Cart values.
 */
function lt_add_sample_order_item_meta( $item, $unused, $values ) {
	if ( ! lt_is_sample_cart_item( (array) $values ) || empty( $values['lt_sample_for'] ) ) {
		return;
	}
	$item->add_meta_data( '_lt_sample_for', (int) $values['lt_sample_for'], true );
	if ( ! empty( $values['lt_sample_variation'] ) ) {
		$item->add_meta_data( '_lt_sample_variation', (int) $values['lt_sample_variation'], true );
	}
	$item->add_meta_data( __( 'Sample of', 'local-tasker' ), $values['lt_sample_label'] ?? '', true );
}
add_action( 'woocommerce_checkout_create_order_line_item', 'lt_add_sample_order_item_meta', 10, 3 );

/* -------------------------------------------------------------------------
 *  Delivery: flat sample charge, waived with flooring
 * ---------------------------------------------------------------------- */

/**
 * Does this shipping package hold nothing but samples?
 *
 * @param array $package Shipping package.
 * @return bool
 */
function lt_package_is_samples_only( array $package ): bool {
	if ( empty( $package['contents'] ) ) {
		return false;
	}
	foreach ( $package['contents'] as $cart_item ) {
		if ( ! lt_is_sample_cart_item( $cart_item ) ) {
			return false;
		}
	}
	return true;
}

/**
 * Charge the flat sample delivery fee on a samples-only cart.
 *
 * Each zone's own delivery rate is replaced by one "Sample delivery" rate at
 * the sample fee, so samples go only where flooring is delivered (no zone rate,
 * no sample rate). Local pickup is left exactly as configured. Carts that also
 * hold flooring are not touched: the sample product weighs nothing, so the
 * normal weight-based rate applies and the sample charge is simply not there.
 *
 * @param WC_Shipping_Rate[] $rates   Rates.
 * @param array              $package Package.
 * @return WC_Shipping_Rate[]
 */
function lt_sample_package_rates( $rates, $package ) {
	if ( ! is_array( $rates ) || ! lt_package_is_samples_only( (array) $package ) ) {
		return $rates;
	}

	$fee      = lt_sample_delivery_fee();
	$out      = array();
	$has_ship = false;

	foreach ( $rates as $rate_id => $rate ) {
		if ( in_array( $rate->get_method_id(), array( 'pickup_location', 'local_pickup' ), true ) ) {
			$out[ $rate_id ] = $rate;
			continue;
		}
		if ( $has_ship ) {
			continue; // One sample delivery option is enough.
		}
		$has_ship = true;
		$rate->set_label( __( 'Sample delivery', 'local-tasker' ) );
		$rate->set_cost( wc_format_decimal( $fee, wc_get_price_decimals() ) );
		$rate->set_taxes( array() );
		$out[ $rate_id ] = $rate;
	}

	return $out;
}
add_filter( 'woocommerce_package_rates', 'lt_sample_package_rates', 100, 2 );

/* -------------------------------------------------------------------------
 *  Admin: switch samples on/off for many products at once
 * ---------------------------------------------------------------------- */

/**
 * Add "Offer samples" / "Stop offering samples" to Products → Bulk actions,
 * so samples can be switched on for every flooring product in one go (filter
 * the list by a flooring category, select all, apply).
 *
 * @param array $actions Bulk actions.
 * @return array
 */
function lt_sample_bulk_actions( $actions ) {
	$actions['lt_samples_on']  = __( 'Offer samples', 'local-tasker' );
	$actions['lt_samples_off'] = __( 'Stop offering samples', 'local-tasker' );
	return $actions;
}
add_filter( 'bulk_actions-edit-product', 'lt_sample_bulk_actions' );

/**
 * Apply the bulk sample toggle. Writes the same meta the "Offer Samples"
 * field on the product edit screen saves.
 *
 * @param string $redirect Redirect URL.
 * @param string $action   Bulk action.
 * @param int[]  $ids      Selected product ids.
 * @return string
 */
function lt_sample_handle_bulk_action( $redirect, $action, $ids ) {
	if ( ! in_array( $action, array( 'lt_samples_on', 'lt_samples_off' ), true ) ) {
		return $redirect;
	}
	$value = 'lt_samples_on' === $action ? '1' : '0';
	$done  = 0;
	foreach ( (array) $ids as $id ) {
		$id = (int) $id;
		if ( $id && current_user_can( 'edit_post', $id ) && $id !== lt_get_sample_product_id() ) {
			update_post_meta( $id, 'sample_enabled', $value );
			++$done;
		}
	}
	return add_query_arg( array( 'lt_samples_updated' => $done, 'lt_samples_state' => $value ), $redirect );
}
add_filter( 'handle_bulk_actions-edit-product', 'lt_sample_handle_bulk_action', 10, 3 );

/**
 * Confirm a bulk sample toggle.
 */
function lt_sample_bulk_action_notice(): void {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	if ( ! isset( $_GET['lt_samples_updated'] ) ) {
		return;
	}
	$count = absint( $_GET['lt_samples_updated'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$on    = '1' === sanitize_text_field( wp_unslash( $_GET['lt_samples_state'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$text  = $on
		/* translators: %d: number of products. */
		? sprintf( _n( 'Samples switched on for %d product.', 'Samples switched on for %d products.', $count, 'local-tasker' ), $count )
		/* translators: %d: number of products. */
		: sprintf( _n( 'Samples switched off for %d product.', 'Samples switched off for %d products.', $count, 'local-tasker' ), $count );
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $text ) . '</p></div>';
}
add_action( 'admin_notices', 'lt_sample_bulk_action_notice' );

/* -------------------------------------------------------------------------
 *  Product page button
 * ---------------------------------------------------------------------- */

/**
 * Print the "Order Sample" button for products using WooCommerce's own
 * add-to-cart form. Calculator products print it inside the calculator's
 * action row instead (woocommerce/partials/product-calculator.php).
 */
function lt_render_sample_button_in_form(): void {
	if ( ! is_product() ) {
		return;
	}
	global $product;
	if ( ! $product instanceof WC_Product || ( get_post_meta( $product->get_id(), 'sold_by_box', true ) && (float) get_post_meta( $product->get_id(), 'carton_sqm', true ) > 0 ) ) {
		return;
	}
	get_template_part( 'woocommerce/partials/product-sample', null, array( 'context' => 'form' ) );
}
add_action( 'woocommerce_after_add_to_cart_button', 'lt_render_sample_button_in_form', 20 );

/**
 * Load the "Order Sample" script on product pages that offer samples.
 */
function lt_enqueue_product_sample_script(): void {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	if ( ! lt_product_offers_samples( get_queried_object_id() ) ) {
		return;
	}
	lt_enqueue_sample_script();
}
add_action( 'wp_enqueue_scripts', 'lt_enqueue_product_sample_script' );

/**
 * Enqueue js/product-sample.js once. Every "Order Sample" button carries its
 * own product id (data-product-id), so the same script serves the single
 * product page and product cards (woocommerce/content-product.php), including
 * cards added to the page later over AJAX.
 *
 * Also asks for wc-cart-fragments, which the asset optimisation strips off
 * non-shop pages: it is what refreshes the mini-cart after a sample is added.
 */
function lt_enqueue_sample_script(): void {
	if ( wp_script_is( 'lt-product-sample', 'enqueued' ) ) {
		return;
	}

	wp_enqueue_script( 'wc-cart-fragments' );

	$rel = '/js/product-sample.js';
	wp_enqueue_script(
		'lt-product-sample',
		get_template_directory_uri() . $rel,
		array(),
		file_exists( get_template_directory() . $rel ) ? filemtime( get_template_directory() . $rel ) : _S_VERSION,
		true
	);
	wp_localize_script(
		'lt-product-sample',
		'ltSample',
		array(
			'ajaxUrl'   => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'nonce'     => wp_create_nonce( 'lt_add_sample' ),
			'labels'    => array(
				'adding'   => __( 'Adding…', 'local-tasker' ),
				'added'    => __( 'Sample added', 'local-tasker' ),
				'default'  => __( 'Order Sample', 'local-tasker' ),
				'pickVar'  => __( 'Please choose a colour / finish first.', 'local-tasker' ),
				'error'    => __( 'Could not add the sample. Please try again.', 'local-tasker' ),
			),
		)
	);
}
