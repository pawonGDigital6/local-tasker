<?php
/**
 * Local Tasker — Flooring catalogue helpers on top of stock WooCommerce.
 *
 * The one place that controls box pricing/coverage/install rate is the
 * "Product Options & Specs" ACF panel on the product edit screen (see
 * inc/acf-product-fields.php: Sold by the Box / Carton Coverage / Install
 * Rate). This file does NOT duplicate that — it only adds:
 *
 *   • Colour swatch helpers used by the archive card and single product page.
 *   • Cart line display + order meta so the chosen area/install option is
 *     visible on the cart, order, and in the installation notification email.
 *
 * @package local-tasker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 *  Colour swatches (archive card + single product variation selector)
 * ---------------------------------------------------------------------- */

/**
 * Variation image swatches for a product, one per `pa_colour` attribute term.
 *
 * Each swatch = [ 'name' => 'Warm Chestnut', 'image' => 'https://.../thumb.jpg' ].
 * For each colour term, the first matching variation is used to source the
 * thumbnail (the variation's own image, falling back to the parent product's
 * featured image if the variation has none set).
 *
 * @param int|WC_Product $product Product.
 * @param int            $limit   Max swatches to return inline.
 * @return array{swatches: array<int,array{name:string,image:string}>, total:int}
 */
function lt_get_product_swatches( $product, $limit = 3 ) {
	if ( is_numeric( $product ) ) {
		$product = wc_get_product( (int) $product );
	}
	$out = array( 'swatches' => array(), 'total' => 0 );
	if ( ! $product instanceof WC_Product || ! $product->is_type( 'variable' ) ) {
		return $out;
	}

	$terms = wc_get_product_terms( $product->get_id(), 'pa_colour', array( 'fields' => 'all' ) );
	if ( empty( $terms ) ) {
		return $out;
	}

	$out['total'] = count( $terms );

	$parent_image_id = $product->get_image_id();

	foreach ( array_slice( $terms, 0, $limit ) as $term ) {
		$image_id = 0;

		foreach ( $product->get_children() as $variation_id ) {
			$variation = wc_get_product( $variation_id );
			if ( ! $variation ) {
				continue;
			}
			$attributes = $variation->get_attributes();
			$value      = $attributes['pa_colour'] ?? '';
			if ( $value !== $term->slug ) {
				continue;
			}
			$image_id = $variation->get_image_id();
			break;
		}

		if ( ! $image_id ) {
			$image_id = $parent_image_id;
		}

		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : wc_placeholder_img_src( 'thumbnail' );

		$out['swatches'][] = array(
			'name'  => $term->name,
			'image' => $image_url,
		);
	}

	return $out;
}

/**
 * Resolve the image to use for a single variation attribute value (e.g. one colour swatch).
 * Prefers the matching variation's own image, falling back to the parent product's image.
 *
 * @param WC_Product $product  Variable product.
 * @param string     $taxonomy Attribute taxonomy (e.g. 'pa_colour').
 * @param string     $slug     Attribute term slug to match.
 * @return string Image URL, or '' if none found.
 */
function lt_get_variation_attribute_image( $product, $taxonomy, $slug ) {
	$image_id = 0;

	foreach ( $product->get_children() as $variation_id ) {
		$variation = wc_get_product( $variation_id );
		if ( ! $variation ) {
			continue;
		}
		$attributes = $variation->get_attributes();
		if ( ( $attributes[ $taxonomy ] ?? '' ) !== $slug ) {
			continue;
		}
		$image_id = $variation->get_image_id();
		break;
	}

	if ( ! $image_id ) {
		$image_id = $product->get_image_id();
	}

	return $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
}

/**
 * Best-effort colour-name → hex map for swatch dots (fallback only).
 *
 * @param string $name Colour name.
 * @return string Hex colour.
 */
function lt_guess_swatch_hex( $name ) {
	$key = strtolower( trim( $name ) );
	$map = array(
		'natural oak'   => '#c9a877',
		'warm chestnut' => '#8a5a3b',
		'blackbutt'     => '#b98b5e',
		'spotted gum'   => '#9c6b45',
		'raw oak'       => '#d8c19a',
		'driftwood oak' => '#b7a488',
		'arctic grey'   => '#b7bcc0',
		'dark grey'     => '#4a4f55',
		'french grey'   => '#9aa0a6',
		'warm white'    => '#efe9dd',
		'white'         => '#f4f4f2',
		'walnut'        => '#5b3a29',
		'latte'         => '#c7ad8c',
		'zen'           => '#d7cdbc',
	);
	foreach ( $map as $needle => $hex ) {
		if ( false !== strpos( $key, $needle ) ) {
			return $hex;
		}
	}
	return '#c9b79c';
}

/* -------------------------------------------------------------------------
 *  Cart / order — carry the chosen coverage + install option
 * ---------------------------------------------------------------------- */

/**
 * Show coverage / install details under the cart line item. Reads the same
 * 'lt_area_sqm' / 'lt_install' cart-item-data keys that the box-calculator
 * AJAX handler (inc/woocommerce.php) sets on add-to-cart.
 *
 * @param array $items Item data.
 * @param array $cart_item Cart item.
 * @return array
 */
function lt_cart_item_display( $items, $cart_item ) {
	if ( ! empty( $cart_item['lt_area_sqm'] ) ) {
		$items[] = array(
			'key'   => __( 'Area', 'local-tasker' ),
			'value' => wc_clean( $cart_item['lt_area_sqm'] ) . ' m²',
		);
	}
	if ( ! empty( $cart_item['lt_install'] ) ) {
		$items[] = array(
			'key'   => __( 'Service', 'local-tasker' ),
			'value' => __( 'Purchase & Install', 'local-tasker' ),
		);
	}
	return $items;
}
add_filter( 'woocommerce_get_item_data', 'lt_cart_item_display', 10, 2 );

/**
 * Persist coverage / install onto the order line item.
 *
 * @param WC_Order_Item_Product $item   Order item.
 * @param string                $unused Cart key (unused, required by the hook signature).
 * @param array                 $values Cart values.
 */
function lt_add_order_item_meta( $item, $unused, $values ) {
	if ( ! empty( $values['lt_area_sqm'] ) ) {
		$item->add_meta_data( __( 'Area (m²)', 'local-tasker' ), $values['lt_area_sqm'], true );
	}
	if ( ! empty( $values['lt_install'] ) ) {
		$item->add_meta_data( '_lt_install', 'yes', true );
		$item->add_meta_data( __( 'Service', 'local-tasker' ), __( 'Purchase & Install', 'local-tasker' ), true );
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'lt_add_order_item_meta', 10, 3 );

/* -------------------------------------------------------------------------
 *  Installation notification emails (customer + store manager)
 *  Fulfils the "Installation Form" requirement in the brief.
 * ---------------------------------------------------------------------- */

/**
 * When an order containing an install line is placed, email the customer and the
 * store manager so someone can reach out about installation.
 *
 * @param int $order_id Order id.
 */
function lt_maybe_send_install_emails( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	// Only once per order.
	if ( $order->get_meta( '_lt_install_notified' ) ) {
		return;
	}

	$has_install = false;
	$lines       = array();
	foreach ( $order->get_items() as $item ) {
		if ( 'yes' === $item->get_meta( '_lt_install' ) ) {
			$has_install = true;
			$area        = $item->get_meta( __( 'Area (m²)', 'local-tasker' ) );
			$lines[]     = trim( $item->get_name() . ( $area ? " — {$area} m²" : '' ) );
		}
	}

	if ( ! $has_install ) {
		return;
	}

	$mailer     = WC()->mailer();
	$store_name = get_bloginfo( 'name' );
	$order_no   = $order->get_order_number();
	$list_html  = '<ul><li>' . implode( '</li><li>', array_map( 'esc_html', $lines ) ) . '</li></ul>';

	/* --- Customer --- */
	$cust_to      = $order->get_billing_email();
	$cust_subject = sprintf( __( '%s — we\'ll be in touch about installation', 'local-tasker' ), $store_name );
	$cust_body    = '<p>' . sprintf( esc_html__( 'Thanks for your order #%s.', 'local-tasker' ), esc_html( $order_no ) ) . '</p>'
		. '<p>' . esc_html__( 'You selected installation on the following item(s):', 'local-tasker' ) . '</p>'
		. $list_html
		. '<p>' . esc_html__( 'A member of our team will contact you shortly to arrange the installation details.', 'local-tasker' ) . '</p>';

	if ( $cust_to ) {
		$mailer->send(
			$cust_to,
			$cust_subject,
			$mailer->wrap_message( __( 'Installation requested', 'local-tasker' ), $cust_body )
		);
	}

	/* --- Store manager --- */
	$admin_to      = apply_filters( 'lt_install_admin_recipient', get_option( 'admin_email' ), $order );
	$admin_subject = sprintf( __( '[%1$s] Installation requested — order #%2$s', 'local-tasker' ), $store_name, $order_no );
	$admin_body    = '<p>' . sprintf(
			esc_html__( 'Order #%1$s from %2$s requested installation.', 'local-tasker' ),
			esc_html( $order_no ),
			esc_html( trim( $order->get_formatted_billing_full_name() ) )
		) . '</p>'
		. '<p>' . esc_html__( 'Contact:', 'local-tasker' ) . ' ' . esc_html( $order->get_billing_email() ) . ' / ' . esc_html( $order->get_billing_phone() ) . '</p>'
		. $list_html
		. '<p><a href="' . esc_url( $order->get_edit_order_url() ) . '">' . esc_html__( 'View order', 'local-tasker' ) . '</a></p>';

	$mailer->send(
		$admin_to,
		$admin_subject,
		$mailer->wrap_message( __( 'Installation requested', 'local-tasker' ), $admin_body )
	);

	$order->update_meta_data( '_lt_install_notified', current_time( 'mysql' ) );
	$order->save();
}
add_action( 'woocommerce_checkout_order_processed', 'lt_maybe_send_install_emails', 20, 1 );
add_action( 'woocommerce_store_api_checkout_order_processed', 'lt_maybe_send_install_emails', 20, 1 );
