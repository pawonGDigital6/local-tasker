<?php
/**
 * Local Tasker — Flooring / sqm commerce engine.
 *
 * Adds the domain logic the flooring catalogue needs on top of stock WooCommerce:
 *
 *   • Per-product "sold by the box, priced per m²" metadata
 *       - _lt_pricing_unit      sqm | length | each   (default: each)
 *       - _lt_sqm_per_box       coverage of one box in m²  (the "Carton Size")
 *       - _lt_price_per_sqm     display $/m² (optional — derived from box price if blank)
 *       - _lt_install_available whether "Purchase & Install" is offered
 *       - _lt_install_per_sqm   optional install price per m² (adds a fee line)
 *
 *   • Helpers used by the archive card, single product template and calculator.
 *   • Cart + order plumbing so a chosen coverage / install option survives to the order.
 *   • Installation notification emails (customer + store manager) per the brief.
 *
 * NOTE ON THE PRICING MODEL
 * -------------------------
 * The native WooCommerce price is treated as the price of ONE BOX (the unit the
 * customer actually buys). $/m² is a display + calculator figure. The calculator
 * converts the desired area into a whole number of boxes and adds that quantity to
 * the cart, so all totals, tax and shipping stay 100% native WooCommerce.
 *
 * @package local-tasker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 *  Read helpers
 * ---------------------------------------------------------------------- */

/**
 * Resolve a WC_Product from an id or object.
 *
 * @param int|WC_Product $product Product id or object.
 * @return WC_Product|null
 */
function lt_resolve_product( $product ) {
	if ( $product instanceof WC_Product ) {
		return $product;
	}
	if ( is_numeric( $product ) ) {
		$resolved = wc_get_product( (int) $product );
		return $resolved ? $resolved : null;
	}
	return null;
}

/**
 * Pricing unit for a product: 'sqm', 'length' or 'each'.
 *
 * @param int|WC_Product $product Product.
 * @return string
 */
function lt_get_pricing_unit( $product ) {
	$product = lt_resolve_product( $product );
	if ( ! $product ) {
		return 'each';
	}
	$unit = $product->get_meta( '_lt_pricing_unit' );
	return in_array( $unit, array( 'sqm', 'length', 'each' ), true ) ? $unit : 'each';
}

/** Whether a product is priced/sold by the square metre. */
function lt_is_sqm_product( $product ) {
	return 'sqm' === lt_get_pricing_unit( $product );
}

/**
 * Coverage of a single box in m² (the carton size).
 *
 * @param int|WC_Product $product Product.
 * @return float 0 when not set.
 */
function lt_get_sqm_per_box( $product ) {
	$product = lt_resolve_product( $product );
	if ( ! $product ) {
		return 0.0;
	}
	return (float) $product->get_meta( '_lt_sqm_per_box' );
}

/**
 * Display price per m². Falls back to (box price ÷ sqm per box).
 *
 * @param int|WC_Product $product Product.
 * @param bool           $sale    Use the sale price when true, regular otherwise.
 * @return float
 */
function lt_get_price_per_sqm( $product, $sale = true ) {
	$product = lt_resolve_product( $product );
	if ( ! $product ) {
		return 0.0;
	}

	$explicit = $product->get_meta( '_lt_price_per_sqm' );
	if ( '' !== $explicit && null !== $explicit && ! $sale ) {
		return (float) $explicit;
	}

	$sqm_per_box = lt_get_sqm_per_box( $product );
	$box_price   = (float) ( $sale ? $product->get_price() : $product->get_regular_price() );

	if ( $sqm_per_box > 0 ) {
		return $box_price / $sqm_per_box;
	}

	// Last resort: use the explicit figure even for the sale slot.
	return '' !== $explicit ? (float) $explicit : 0.0;
}

/** Regular (non-sale) price of one box. */
function lt_get_box_price( $product, $sale = true ) {
	$product = lt_resolve_product( $product );
	if ( ! $product ) {
		return 0.0;
	}
	return (float) ( $sale ? $product->get_price() : $product->get_regular_price() );
}

/** Whether "Purchase & Install" is offered for a product. */
function lt_install_available( $product ) {
	$product = lt_resolve_product( $product );
	if ( ! $product ) {
		return false;
	}
	return wc_string_to_bool( $product->get_meta( '_lt_install_available' ) );
}

/** Optional install price per m² (0 when not charging in-cart). */
function lt_get_install_per_sqm( $product ) {
	$product = lt_resolve_product( $product );
	if ( ! $product ) {
		return 0.0;
	}
	return (float) $product->get_meta( '_lt_install_per_sqm' );
}

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
	$product = lt_resolve_product( $product );
	$out     = array( 'swatches' => array(), 'total' => 0 );
	if ( ! $product || ! $product->is_type( 'variable' ) ) {
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
 *  Price display markup (shared by card + single)
 * ---------------------------------------------------------------------- */

/**
 * Price HTML matching the Figma card / single: struck regular + emphasised $/m².
 *
 * @param int|WC_Product $product Product.
 * @return string
 */
function lt_price_html( $product ) {
	$product = lt_resolve_product( $product );
	if ( ! $product ) {
		return '';
	}

	// Non-flooring products fall back to WooCommerce's own price html.
	if ( ! lt_is_sqm_product( $product ) ) {
		return '<span class="lt-price">' . $product->get_price_html() . '</span>';
	}

	$sale_sqm = lt_get_price_per_sqm( $product, true );
	$reg_sqm  = lt_get_price_per_sqm( $product, false );
	$on_sale  = $product->is_on_sale() && $reg_sqm > $sale_sqm;

	ob_start();
	?>
	<span class="lt-price lt-price--sqm">
		<?php if ( $on_sale ) : ?>
			<del class="lt-price__was"><?php echo wp_kses_post( wc_price( $reg_sqm ) ); ?></del>
		<?php endif; ?>
		<ins class="lt-price__now">
			<?php echo wp_kses_post( wc_price( $sale_sqm ) ); ?><span class="lt-price__unit"> / sqm</span>
		</ins>
	</span>
	<?php
	return ob_get_clean();
}

/* -------------------------------------------------------------------------
 *  Admin — product data fields
 * ---------------------------------------------------------------------- */

/**
 * Render the flooring fields inside the "Product data → General" panel.
 */
function lt_add_product_flooring_fields() {
	echo '<div class="options_group lt-flooring-options">';

	woocommerce_wp_select(
		array(
			'id'      => '_lt_pricing_unit',
			'label'   => __( 'Pricing unit', 'local-tasker' ),
			'options' => array(
				'each'   => __( 'Each / standard', 'local-tasker' ),
				'sqm'    => __( 'Per m² (sold by the box)', 'local-tasker' ),
				'length' => __( 'Per length (structural)', 'local-tasker' ),
			),
			'desc_tip' => true,
			'description' => __( 'Choose “Per m²” for flooring so the box calculator and $/m² pricing appear.', 'local-tasker' ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_lt_sqm_per_box',
			'label'       => __( 'Coverage per box (m²)', 'local-tasker' ),
			'placeholder' => 'e.g. 0.983',
			'desc_tip'    => true,
			'description' => __( 'Carton size in m². Used to convert an area into a whole number of boxes.', 'local-tasker' ),
			'type'        => 'number',
			'custom_attributes' => array( 'step' => '0.0001', 'min' => '0' ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_lt_price_per_sqm',
			'label'       => __( 'Price per m² (optional)', 'local-tasker' ),
			'placeholder' => __( 'Auto from box price', 'local-tasker' ),
			'desc_tip'    => true,
			'description' => __( 'Leave blank to derive from the box price ÷ coverage.', 'local-tasker' ),
			'type'        => 'number',
			'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
		)
	);

	woocommerce_wp_checkbox(
		array(
			'id'          => '_lt_install_available',
			'label'       => __( 'Offer installation', 'local-tasker' ),
			'description' => __( 'Show the “Purchase & Install” option on the product page.', 'local-tasker' ),
		)
	);

	woocommerce_wp_text_input(
		array(
			'id'          => '_lt_install_per_sqm',
			'label'       => __( 'Install price per m² (optional)', 'local-tasker' ),
			'placeholder' => '0.00',
			'desc_tip'    => true,
			'description' => __( 'If set, an installation fee (this × coverage) is added to the cart line.', 'local-tasker' ),
			'type'        => 'number',
			'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
		)
	);

	echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'lt_add_product_flooring_fields' );

/**
 * Persist the flooring fields on save.
 *
 * @param int $post_id Product id.
 */
function lt_save_product_flooring_fields( $post_id ) {
	$product = wc_get_product( $post_id );
	if ( ! $product ) {
		return;
	}

	$unit = isset( $_POST['_lt_pricing_unit'] ) ? sanitize_text_field( wp_unslash( $_POST['_lt_pricing_unit'] ) ) : 'each';
	$product->update_meta_data( '_lt_pricing_unit', in_array( $unit, array( 'sqm', 'length', 'each' ), true ) ? $unit : 'each' );

	$product->update_meta_data( '_lt_sqm_per_box', isset( $_POST['_lt_sqm_per_box'] ) ? wc_format_decimal( wp_unslash( $_POST['_lt_sqm_per_box'] ) ) : '' );
	$product->update_meta_data( '_lt_price_per_sqm', isset( $_POST['_lt_price_per_sqm'] ) && '' !== $_POST['_lt_price_per_sqm'] ? wc_format_decimal( wp_unslash( $_POST['_lt_price_per_sqm'] ) ) : '' );
	$product->update_meta_data( '_lt_install_available', isset( $_POST['_lt_install_available'] ) ? 'yes' : 'no' );
	$product->update_meta_data( '_lt_install_per_sqm', isset( $_POST['_lt_install_per_sqm'] ) && '' !== $_POST['_lt_install_per_sqm'] ? wc_format_decimal( wp_unslash( $_POST['_lt_install_per_sqm'] ) ) : '' );

	$product->save();
}
add_action( 'woocommerce_process_product_meta', 'lt_save_product_flooring_fields' );

/* -------------------------------------------------------------------------
 *  Cart / order — carry the chosen coverage + install option
 * ---------------------------------------------------------------------- */

/**
 * Capture the coverage (m²) and install choice posted from the single product form.
 *
 * @param array $data     Existing cart item data.
 * @param int   $product_id Product id.
 * @return array
 */
function lt_add_cart_item_data( $data, $product_id ) {
	if ( isset( $_POST['lt_area_sqm'] ) && '' !== $_POST['lt_area_sqm'] ) {
		$data['lt_area_sqm'] = round( (float) wp_unslash( $_POST['lt_area_sqm'] ), 2 );
	}
	if ( isset( $_POST['lt_install'] ) && 'yes' === $_POST['lt_install'] && lt_install_available( $product_id ) ) {
		$data['lt_install'] = 'yes';
	}
	// Ensure each configuration is treated as a distinct line.
	if ( ! empty( $data['lt_area_sqm'] ) || ! empty( $data['lt_install'] ) ) {
		$data['lt_unique'] = md5( microtime() . wp_rand() );
	}
	return $data;
}
add_filter( 'woocommerce_add_cart_item_data', 'lt_add_cart_item_data', 10, 2 );

/**
 * Show coverage / install details under the cart line item.
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
 * Add an installation fee line when the product carries an install $/m² and the
 * customer selected install. Runs during totals calculation.
 *
 * @param WC_Cart $cart Cart.
 */
function lt_add_install_fee( $cart ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	$fee = 0.0;
	foreach ( $cart->get_cart() as $cart_item ) {
		if ( empty( $cart_item['lt_install'] ) || empty( $cart_item['lt_area_sqm'] ) ) {
			continue;
		}
		$per_sqm = lt_get_install_per_sqm( $cart_item['product_id'] );
		if ( $per_sqm > 0 ) {
			$fee += $per_sqm * (float) $cart_item['lt_area_sqm'];
		}
	}

	if ( $fee > 0 ) {
		$cart->add_fee( __( 'Installation', 'local-tasker' ), round( $fee, 2 ), true );
	}
}
add_action( 'woocommerce_cart_calculate_fees', 'lt_add_install_fee' );

/**
 * Persist coverage / install onto the order line item.
 *
 * @param WC_Order_Item_Product $item          Order item.
 * @param string                $cart_item_key Cart key.
 * @param array                 $values        Cart values.
 */
function lt_add_order_item_meta( $item, $cart_item_key, $values ) {
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
