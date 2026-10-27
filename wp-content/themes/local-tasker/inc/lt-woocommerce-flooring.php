<?php
/**
 * Local Tasker — Flooring catalogue helpers on top of stock WooCommerce.
 *
 * The one place that controls box pricing/coverage is the "Product Options &
 * Specs" ACF panel on the product edit screen (see inc/acf-product-fields.php:
 * Sold by the Box / Carton Coverage). This file does NOT duplicate that — it
 * only adds:
 *
 *   • Colour swatch helpers used by the archive card and single product page.
 *   • The installation opt-in, offered on every product and priced nowhere.
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

		// A swatch is a variation image, not the main product image — keep
		// WooCommerce's own placeholder rather than the theme product one.
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : lt_wc_default_placeholder_src( 'thumbnail' );

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
 *  Installation opt-in on the stock add-to-cart form
 * ---------------------------------------------------------------------- */

/**
 * Does this product offer the installation option?
 *
 * Driven by the "Installation Quote" toggle in the Product Options & Specs
 * panel, which defaults to ON. The theme reads product meta directly rather
 * than through get_field(), so ACF's default_value never gets a chance to fill
 * in for a product whose meta row does not exist yet — every product that
 * predates the toggle, plus any created outside the editor. An empty value must
 * therefore mean enabled: only an explicit "off" hides the option.
 *
 * Single source of truth for the toggle — the partial, the cart-item filter and
 * the calculator's AJAX handler all defer to it, so display and persistence
 * cannot disagree.
 *
 * @param int $product_id Product id.
 * @return bool
 */
function lt_product_offers_install_quote( $product_id ): bool {
	// A free sample is never installed (see inc/lt-samples.php).
	if ( function_exists( 'lt_get_sample_product_id' ) && (int) $product_id === lt_get_sample_product_id() && (int) $product_id > 0 ) {
		return false;
	}

	$enabled = get_post_meta( (int) $product_id, 'install_quote_enabled', true );

	if ( '' === $enabled || null === $enabled ) {
		return true;
	}

	return (bool) $enabled;
}

/**
 * Unit suffix printed after a product's price, e.g. "/ sqm".
 *
 * Driven by the product's "Price Unit Label" field. Display only — prices and
 * the box calculator are untouched. An empty value (every product saved before
 * the field existed) falls back to "sqm", so existing products look the same.
 *
 * @param WC_Product|int $product Product or product id.
 * @return string Suffix including the leading "/ ", or '' for no label.
 */
function lt_get_price_unit_suffix( $product ): string {
	if ( $product instanceof WC_Product ) {
		$product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	} else {
		$product_id = (int) $product;
	}

	$unit = (string) get_post_meta( $product_id, 'price_unit', true );

	switch ( $unit ) {
		case 'none':
			return '';
		case 'unit':
			$label = __( 'unit', 'local-tasker' );
			break;
		case 'each':
			$label = __( 'each', 'local-tasker' );
			break;
		case 'custom':
			$label = trim( (string) get_post_meta( $product_id, 'price_unit_custom', true ) );
			if ( '' === $label ) {
				return '';
			}
			break;
		default:
			$label = __( 'sqm', 'local-tasker' );
	}

	return '/ ' . $label;
}

/**
 * Wastage allowance, as a percentage, for a product's calculators.
 *
 * Resolution order: the product's own "Wastage Allowance (%)" field, then the
 * site-wide default in Theme Options, then 10 — the figure the calculator was
 * hard-coded to before either field existed, so nothing changes for a site that
 * has not filled them in yet.
 *
 * An explicit 0 is a real value at every level: it means "no wastage option",
 * and the calculators hide the row rather than offering a 0% tick. Only a blank
 * field falls through to the next level.
 *
 * Single source of truth for the percentage — the box calculator, its banner and
 * the area calculator all read it here, so the three cannot drift apart.
 *
 * @param WC_Product|int $product Optional. Product or product id. Site default when omitted.
 * @return float Percentage between 0 and 100.
 */
function lt_get_wastage_percent( $product = 0 ): float {
	$fallback = 10.0;

	if ( $product instanceof WC_Product ) {
		$product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	} else {
		$product_id = (int) $product;
	}

	// Product override.
	if ( $product_id ) {
		$override = get_post_meta( $product_id, 'wastage_percent', true );
		if ( '' !== $override && null !== $override ) {
			return lt_clamp_wastage_percent( $override );
		}
	}

	// Site-wide default.
	if ( function_exists( 'get_field' ) ) {
		$site_default = get_field( 'default_wastage_percent', 'option' );
		if ( '' !== $site_default && null !== $site_default && false !== $site_default ) {
			return lt_clamp_wastage_percent( $site_default );
		}
	}

	return $fallback;
}

/**
 * Keep an authored wastage figure inside a sane 0–100 range.
 *
 * @param mixed $value Raw field value.
 * @return float
 */
function lt_clamp_wastage_percent( $value ): float {
	return (float) max( 0, min( 100, (float) $value ) );
}

/**
 * Format a wastage percentage for display, trimming trailing zeros ("10.0" → "10").
 *
 * @param float $percent Percentage.
 * @return string
 */
function lt_format_wastage_percent( float $percent ): string {
	return rtrim( rtrim( number_format( $percent, 2, '.', '' ), '0' ), '.' );
}

/**
 * Print the installation opt-in inside WooCommerce's own add-to-cart form.
 *
 * Box-priced products add to the cart over AJAX and render the partial
 * themselves (see woocommerce/partials/product-summary.php). Everything else
 * posts WooCommerce's stock form, and a checkbox only travels with that POST if
 * it lives inside the form — which is what this hook is for. The partial gates
 * itself on the product's "Installation Quote" toggle, so there is nothing to
 * check here beyond staying on the single-product page.
 */
function lt_render_install_optin(): void {
	if ( ! is_product() ) {
		return;
	}
	get_template_part( 'woocommerce/partials/product-choose-option' );
}
add_action( 'woocommerce_after_add_to_cart_button', 'lt_render_install_optin' );

/**
 * Carry the installation opt-in from the stock add-to-cart form onto the cart line.
 *
 * The box calculator's AJAX handler sets 'lt_install' itself (see
 * inc/woocommerce.php), so lines it created are left alone; this covers every
 * product added through WooCommerce's own form. The flag records intent only —
 * it never touches the price.
 *
 * A product with the toggle off never renders the checkbox, so it should never
 * see this input — but the flag is re-checked against the toggle anyway, so a
 * hand-crafted POST cannot attach an installation request to a product that
 * does not offer one.
 *
 * @param array $cart_item_data Cart item data.
 * @param int   $product_id     Product being added.
 * @return array
 */
function lt_add_install_cart_item_data( $cart_item_data, $product_id = 0 ) {
	// Already decided by the calculator's AJAX handler (which verified its own nonce).
	if ( isset( $cart_item_data['lt_install'] ) ) {
		return $cart_item_data;
	}

	if ( ! lt_product_offers_install_quote( $product_id ) ) {
		return $cart_item_data;
	}

	// WooCommerce's single-product add-to-cart form carries no nonce of its own;
	// this is a display-only flag, handled the same way Woo handles the rest of
	// that form's input.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$raw = isset( $_REQUEST['lt_install_quote'] ) ? (string) wc_clean( wp_unslash( $_REQUEST['lt_install_quote'] ) ) : '';

	if ( in_array( $raw, array( '1', 'yes', 'true', 'on' ), true ) ) {
		$cart_item_data['lt_install'] = 'yes';
	}

	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'lt_add_install_cart_item_data', 10, 2 );

/* -------------------------------------------------------------------------
 *  Installation opt-in on the cart page
 * ---------------------------------------------------------------------- */

/**
 * The cart-item data a line was created with, for a given installation choice.
 *
 * WC_Cart::generate_cart_id() hashes this array into the line's key, and both
 * add-to-cart paths put the install flag in it: the calculator's AJAX handler
 * passes [ lt_boxed, lt_install ] (inc/woocommerce.php) and the stock form gets
 * [ lt_install ] from lt_add_install_cart_item_data() only when ticked. Rebuilding
 * it lets the cart toggle give a line the same key the product page would have,
 * so a later add from the product page still tops up the right line.
 *
 * @param array $cart_item Cart item.
 * @param bool  $install   Installation quote requested.
 * @return array|null Null for lines whose identity cannot be rebuilt (legacy
 *                    'lt_boxes'-only lines from before 'lt_boxed' existed).
 */
function lt_install_cart_identity( array $cart_item, bool $install ): ?array {
	if ( ! empty( $cart_item['lt_boxed'] ) ) {
		return array(
			'lt_boxed'   => 'yes',
			'lt_install' => $install ? 'yes' : '',
		);
	}
	if ( ! empty( $cart_item['lt_boxes'] ) ) {
		return null;
	}
	return $install ? array( 'lt_install' => 'yes' ) : array();
}

/**
 * Turn the installation quote on or off for a cart line.
 *
 * Re-keys the line the way the product page would have keyed it. If the cart
 * already holds a line with that key (same product, same choice), the two are
 * merged — exactly what adding it again from the product page does. When the
 * line's current key does not match the rebuilt identity (data added by
 * something other than this theme), the flag is changed in place instead, so
 * nothing this code does not understand is lost.
 *
 * Price is never touched: the flag is a request for a quote, nothing more.
 *
 * @param string $cart_item_key Cart item key.
 * @param bool   $requested     Whether a quote is wanted.
 * @return bool True when the line exists and now carries the requested choice.
 */
function lt_set_cart_item_install_quote( string $cart_item_key, bool $requested ): bool {
	$cart = WC()->cart;
	if ( ! $cart ) {
		return false;
	}

	$contents = $cart->get_cart_contents();
	if ( ! isset( $contents[ $cart_item_key ] ) ) {
		return false;
	}

	$item = $contents[ $cart_item_key ];

	// Same gate as the product page: no toggle, no request.
	if ( $requested && ! lt_product_offers_install_quote( $item['product_id'] ) ) {
		return false;
	}

	$current = ! empty( $item['lt_install'] );
	if ( $current === $requested ) {
		return true;
	}

	if ( $requested ) {
		$item['lt_install'] = 'yes';
	} elseif ( ! empty( $item['lt_boxed'] ) ) {
		$item['lt_install'] = '';
	} else {
		unset( $item['lt_install'] );
	}

	$new_key      = $cart_item_key;
	$old_identity = lt_install_cart_identity( $item, $current );
	$new_identity = lt_install_cart_identity( $item, $requested );

	if ( null !== $old_identity && null !== $new_identity ) {
		$product_id   = (int) $item['product_id'];
		$variation_id = (int) $item['variation_id'];
		$variation    = (array) ( $item['variation'] ?? array() );

		if ( $cart->generate_cart_id( $product_id, $variation_id, $variation, $old_identity ) === $cart_item_key ) {
			$new_key = $cart->generate_cart_id( $product_id, $variation_id, $variation, $new_identity );
		}
	}

	if ( $new_key !== $cart_item_key && isset( $contents[ $new_key ] ) ) {
		// Merge into the matching line, as a second add from the product page would.
		$target             = $contents[ $new_key ];
		$target['quantity'] = (float) $target['quantity'] + (float) $item['quantity'];

		if ( ! empty( $item['lt_area_sqm'] ) || ! empty( $target['lt_area_sqm'] ) ) {
			$target['lt_area_sqm'] = round( (float) ( $target['lt_area_sqm'] ?? 0 ) + (float) ( $item['lt_area_sqm'] ?? 0 ), 2 );
		}
		if ( isset( $target['lt_boxes'] ) ) {
			$carton_sqm                = (float) get_post_meta( $target['product_id'], 'carton_sqm', true );
			$target['lt_boxes']        = (int) $target['quantity'];
			$target['lt_coverage_sqm'] = round( $target['lt_boxes'] * $carton_sqm, 4 );
		}

		$contents[ $new_key ] = $target;
		unset( $contents[ $cart_item_key ] );
	} else {
		// Swap the key in place so the line keeps its position in the cart.
		$item['key'] = $new_key;
		$rebuilt     = array();
		foreach ( $contents as $key => $line ) {
			if ( $key === $cart_item_key ) {
				$rebuilt[ $new_key ] = $item;
			} else {
				$rebuilt[ $key ] = $line;
			}
		}
		$contents = $rebuilt;
	}

	$cart->set_cart_contents( $contents );
	$cart->calculate_totals();
	$cart->set_session();

	return true;
}

/**
 * Expose the installation choice to the Cart block, and accept changes from it.
 *
 * The cart and checkout pages are WooCommerce blocks, which read the cart through
 * the Store API rather than PHP templates. Each cart item gets an 'lt-install'
 * entry ({ offered, requested }) and the same namespace takes updates from
 * js/cart-install-quote.js via extensionCartUpdate(), which returns the fresh
 * cart so every block repaints from one source.
 */
function lt_register_install_quote_store_api(): void {
	if ( ! function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {
		return;
	}

	woocommerce_store_api_register_endpoint_data(
		array(
			'endpoint'        => \Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema::IDENTIFIER,
			'namespace'       => 'lt-install',
			'data_callback'   => static function ( $cart_item ) {
				return array(
					'offered'   => ! empty( $cart_item['product_id'] ) && lt_product_offers_install_quote( $cart_item['product_id'] ),
					'requested' => ! empty( $cart_item['lt_install'] ),
				);
			},
			'schema_callback' => static function () {
				return array(
					'offered'   => array(
						'description' => __( 'Whether this product offers an installation quote.', 'local-tasker' ),
						'type'        => 'boolean',
						'readonly'    => true,
					),
					'requested' => array(
						'description' => __( 'Whether an installation quote is requested for this line.', 'local-tasker' ),
						'type'        => 'boolean',
						'readonly'    => true,
					),
				);
			},
			'schema_type'     => ARRAY_A,
		)
	);

	woocommerce_store_api_register_update_callback(
		array(
			'namespace' => 'lt-install',
			'callback'  => static function ( $data ) {
				$key       = isset( $data['key'] ) ? wc_clean( (string) $data['key'] ) : '';
				$requested = ! empty( $data['requested'] ) && 'false' !== $data['requested'];

				if ( '' === $key || ! lt_set_cart_item_install_quote( $key, $requested ) ) {
					throw new \Automattic\WooCommerce\StoreApi\Exceptions\RouteException(
						'lt_install_quote_invalid',
						esc_html__( 'We could not update the installation quote request. Please refresh the page and try again.', 'local-tasker' ),
						400
					);
				}
			},
		)
	);
}
// WooCommerce fires woocommerce_blocks_loaded on plugins_loaded, before the
// theme is loaded, so register straight away when it has already run.
if ( did_action( 'woocommerce_blocks_loaded' ) ) {
	lt_register_install_quote_store_api();
} else {
	add_action( 'woocommerce_blocks_loaded', 'lt_register_install_quote_store_api' );
}

/**
 * Load the cart-page installation panel (js/cart-install-quote.js).
 *
 * Plain script rather than a webpack entry, like the other cart helpers in js/,
 * so it needs no build step. It only renders inside the Cart block.
 */
function lt_enqueue_cart_install_quote_script(): void {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
		return;
	}

	$rel = '/js/cart-install-quote.js';
	wp_enqueue_script(
		'lt-cart-install-quote',
		get_template_directory_uri() . $rel,
		array( 'wc-blocks-checkout', 'wp-element', 'wp-plugins', 'wp-html-entities', 'wp-hooks' ),
		file_exists( get_template_directory() . $rel ) ? filemtime( get_template_directory() . $rel ) : _S_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'lt_enqueue_cart_install_quote_script' );

/**
 * A one-line pointer in the mini-cart drawer to where the installation quote
 * can be requested — the drawer itself carries no checkbox.
 *
 * Printed as the last row of the item list (woocommerce_mini_cart_contents runs
 * inside the drawer's <ul>), so it sits above the sticky subtotal and buttons
 * without changing their layout, and it is re-rendered with every cart-fragment
 * refresh like the rest of the drawer. Shown only while the cart holds a
 * product that offers an installation quote. Markup uses utility classes the
 * theme already compiles, so no stylesheet rebuild is needed.
 */
function lt_mini_cart_install_quote_note(): void {
	if ( ! WC()->cart ) {
		return;
	}

	$offered = false;
	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( ! empty( $cart_item['product_id'] ) && lt_product_offers_install_quote( $cart_item['product_id'] ) ) {
			$offered = true;
			break;
		}
	}
	if ( ! $offered ) {
		return;
	}
	?>
	<li class="lt-mini-cart__install-note list-none mb-6 rounded-lg bg-lt-snow-drift px-4 py-3 text-caption-sm leading-[1.5] text-lt-text-secondary">
		<?php
		printf(
			/* translators: %s: link to the cart page. */
			esc_html__( 'Need installation? You can request an installation quote on the product page or in your %s.', 'local-tasker' ),
			'<a href="' . esc_url( wc_get_cart_url() ) . '" class="text-lt-brand underline hover:no-underline">' . esc_html__( 'cart', 'local-tasker' ) . '</a>'
		);
		?>
	</li>
	<?php
}
add_action( 'woocommerce_mini_cart_contents', 'lt_mini_cart_install_quote_note' );

/* -------------------------------------------------------------------------
 *  Delivery cost note (product page, cart, checkout)
 * ---------------------------------------------------------------------- */

/**
 * Lowest delivery price per shipping zone, read from the live shipping setup.
 *
 * Nothing is hardcoded: each enabled method in each zone contributes its
 * cheapest rate — a Flexible Shipping method its lowest rule cost, a flat rate
 * its base cost — so editing a rate or a zone in WooCommerce → Shipping changes
 * the note with no code change. Zones with no priced method (e.g. "Rest of the
 * World" with nothing set up) are left out.
 *
 * Display only; the price a customer pays is still calculated by the shipping
 * methods at checkout.
 *
 * @return array<int,array{zone:string,from:float}> Cheapest first.
 */
function lt_get_delivery_zone_prices(): array {
	static $prices = null;
	if ( null !== $prices ) {
		return $prices;
	}

	$prices = array();
	if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
		return $prices;
	}

	foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
		$lowest = null;

		foreach ( $zone['shipping_methods'] as $method ) {
			if ( 'yes' !== $method->enabled ) {
				continue;
			}

			$costs = array();
			if ( 'flexible_shipping_single' === $method->id ) {
				foreach ( (array) $method->get_option( 'method_rules', array() ) as $rule ) {
					if ( isset( $rule['cost_per_order'] ) && is_numeric( $rule['cost_per_order'] ) ) {
						$costs[] = (float) $rule['cost_per_order'];
					}
				}
			} elseif ( 'flat_rate' === $method->id && is_numeric( $method->get_option( 'cost' ) ) ) {
				$costs[] = (float) $method->get_option( 'cost' );
			}

			if ( $costs ) {
				$lowest = null === $lowest ? min( $costs ) : min( $lowest, min( $costs ) );
			}
		}

		if ( null !== $lowest ) {
			$prices[] = array(
				'zone' => (string) $zone['zone_name'],
				'from' => $lowest,
			);
		}
	}

	usort(
		$prices,
		static function ( $a, $b ) {
			return $a['from'] <=> $b['from'];
		}
	);

	return $prices;
}

/**
 * A delivery price as plain text, without cents when it is a whole amount ("$160").
 *
 * @param float $amount Amount.
 * @return string
 */
function lt_format_delivery_price( float $amount ): string {
	$decimals = ( floor( $amount ) === $amount ) ? 0 : wc_get_price_decimals();
	return html_entity_decode( wp_strip_all_tags( wc_price( $amount, array( 'decimals' => $decimals ) ) ), ENT_QUOTES, 'UTF-8' );
}

/**
 * Store phone number for "call us" links, digits only.
 *
 * Same source as the installation opt-in's "Call Us" link: the WooCommerce
 * store phone when set, otherwise the business number.
 *
 * @return string
 */
function lt_get_store_call_number(): string {
	$phone = preg_replace( '/[^0-9+]/', '', (string) get_option( 'woocommerce_store_phone', '' ) );
	return '' !== $phone ? $phone : '1300807127';
}

/**
 * Feed the cart/checkout delivery note (js/cart-delivery-note.js).
 *
 * Plain script like the other cart helpers in js/, rendered through the Cart
 * and Checkout blocks' own order-summary slot.
 */
function lt_enqueue_cart_delivery_note_script(): void {
	if ( ! function_exists( 'is_cart' ) || ! ( is_cart() || is_checkout() ) || is_order_received_page() ) {
		return;
	}

	$prices = lt_get_delivery_zone_prices();
	if ( ! $prices ) {
		return;
	}

	$rel = '/js/cart-delivery-note.js';
	wp_enqueue_script(
		'lt-cart-delivery-note',
		get_template_directory_uri() . $rel,
		array( 'wc-blocks-checkout', 'wp-element', 'wp-plugins' ),
		file_exists( get_template_directory() . $rel ) ? filemtime( get_template_directory() . $rel ) : _S_VERSION,
		true
	);
	wp_localize_script(
		'lt-cart-delivery-note',
		'ltDeliveryNote',
		array(
			'zones' => array_map(
				static function ( $row ) {
					return array(
						'zone' => $row['zone'],
						'from' => lt_format_delivery_price( $row['from'] ),
					);
				},
				$prices
			),
			'phone' => lt_get_store_call_number(),
			// Lets the note read correctly for a cart that holds only samples.
			'sampleProductId' => function_exists( 'lt_get_sample_product_id' ) ? lt_get_sample_product_id() : 0,
			'sampleFee'       => function_exists( 'lt_sample_delivery_fee' ) ? lt_format_delivery_price( lt_sample_delivery_fee() ) : '',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'lt_enqueue_cart_delivery_note_script' );

/* -------------------------------------------------------------------------
 *  Cart / order — carry the chosen coverage + install option
 * ---------------------------------------------------------------------- */

/**
 * Is this cart line a flooring line sold by the box?
 *
 * True only for lines added through the box calculator (which sets 'lt_boxes')
 * on a product that actually carries a carton size. Those are exactly the lines
 * lt_apply_flooring_box_pricing() re-prices per box, so the two stay in step: if
 * a line is priced by the box, its quantity is a box count, and vice versa.
 *
 * @param array $cart_item Cart item.
 * @return bool
 */
function lt_cart_item_is_boxed( array $cart_item ): bool {
	if ( empty( $cart_item['product_id'] ) ) {
		return false;
	}
	// 'lt_boxed' is the marker set on every calculator line; 'lt_boxes' is the
	// pre-existing one, accepted so carts already in a session keep working.
	if ( empty( $cart_item['lt_boxed'] ) && empty( $cart_item['lt_boxes'] ) ) {
		return false;
	}
	return (float) get_post_meta( $cart_item['product_id'], 'carton_sqm', true ) > 0;
}

/**
 * Format a per-box coverage for display.
 *
 * Keeps up to three decimals but drops trailing zeros, so 2.888 stays "2.888",
 * 2.880 reads "2.88" and a whole number reads "3" rather than "3.000".
 *
 * @param float $carton_sqm Square metres covered by one box.
 * @return string
 */
function lt_format_coverage( float $carton_sqm ): string {
	return rtrim( rtrim( number_format( $carton_sqm, 3, '.', '' ), '0' ), '.' );
}

/**
 * Show coverage / install details under the cart line item.
 *
 * Boxed lines show the product's per-box coverage; everything else falls back to
 * the 'lt_area_sqm' the box-calculator AJAX handler (inc/woocommerce.php) records
 * on add-to-cart.
 *
 * Feeds `wc_get_formatted_cart_item_data()`, so this one function drives the
 * line detail in the mini-cart drawer, the cart page and the checkout review —
 * they cannot disagree with each other.
 *
 * @param array $items Item data.
 * @param array $cart_item Cart item.
 * @return array
 */
function lt_cart_item_display( $items, $cart_item ) {
	if ( lt_cart_item_is_boxed( $cart_item ) ) {
		// Per-box coverage, straight from the product's "Carton Coverage (sqm)"
		// field — the same value that drives the calculator and the box price, so
		// nothing here is hardcoded or product-specific. The box count itself is
		// already visible on the quantity line ("4 × $132.48").
		$carton_sqm = (float) get_post_meta( $cart_item['product_id'], 'carton_sqm', true );

		$items[] = array(
			'key'   => __( 'Coverage', 'local-tasker' ),
			/* translators: %s: square metres covered by one box, e.g. "2.888". */
			'value' => sprintf( __( '%s sqm / box', 'local-tasker' ), lt_format_coverage( $carton_sqm ) ),
		);
	} elseif ( ! empty( $cart_item['lt_area_sqm'] ) ) {
		// No usable box data — keep the original area line rather than show a
		// box count we cannot verify.
		$items[] = array(
			'key'   => __( 'Area', 'local-tasker' ),
			'value' => wc_clean( $cart_item['lt_area_sqm'] ) . ' m²',
		);
	}
	if ( ! empty( $cart_item['lt_install'] ) ) {
		$items[] = array(
			'key'   => __( 'Installation', 'local-tasker' ),
			'value' => __( 'Quote requested', 'local-tasker' ),
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
	// Mirror the "Coverage" line the cart / mini-cart showed, plus the box count
	// the quantity column implied, so the order record matches what the customer
	// confirmed and the warehouse can pick against it.
	if ( lt_cart_item_is_boxed( (array) $values ) ) {
		$carton_sqm = (float) get_post_meta( $values['product_id'], 'carton_sqm', true );

		$item->add_meta_data(
			__( 'Coverage', 'local-tasker' ),
			/* translators: %s: square metres covered by one box, e.g. "2.888". */
			sprintf( __( '%s sqm / box', 'local-tasker' ), lt_format_coverage( $carton_sqm ) ),
			true
		);
		$item->add_meta_data( __( 'Boxes', 'local-tasker' ), (int) $item->get_quantity(), true );
	}
	if ( ! empty( $values['lt_area_sqm'] ) ) {
		$item->add_meta_data( __( 'Area (m²)', 'local-tasker' ), $values['lt_area_sqm'], true );
	}
	if ( ! empty( $values['lt_install'] ) ) {
		// '_lt_install' stays the machine-readable flag the order-processed hook
		// reads; the visible line is what the customer and the office both see on
		// the order, so it must read as a request rather than a purchase.
		$item->add_meta_data( '_lt_install', 'yes', true );
		$item->add_meta_data( __( 'Installation', 'local-tasker' ), __( 'Quote requested', 'local-tasker' ), true );
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
	$cust_subject = sprintf( __( '%s — your installation quote request', 'local-tasker' ), $store_name );
	$cust_body    = '<p>' . sprintf( esc_html__( 'Thanks for your order #%s.', 'local-tasker' ), esc_html( $order_no ) ) . '</p>'
		. '<p>' . esc_html__( 'You asked for an installation quote on the following item(s):', 'local-tasker' ) . '</p>'
		. $list_html
		. '<p>' . esc_html__( 'A member of our team will be in touch shortly with a quote. Installation is not included in your order total.', 'local-tasker' ) . '</p>';

	if ( $cust_to ) {
		$mailer->send(
			$cust_to,
			$cust_subject,
			$mailer->wrap_message( __( 'Installation quote requested', 'local-tasker' ), $cust_body )
		);
	}

	/* --- Store manager --- */
	$admin_to      = apply_filters( 'lt_install_admin_recipient', get_option( 'admin_email' ), $order );
	$admin_subject = sprintf( __( '[%1$s] Installation quote requested — order #%2$s', 'local-tasker' ), $store_name, $order_no );
	$admin_body    = '<p>' . sprintf(
			esc_html__( 'Order #%1$s from %2$s requested an installation quote.', 'local-tasker' ),
			esc_html( $order_no ),
			esc_html( trim( $order->get_formatted_billing_full_name() ) )
		) . '</p>'
		. '<p>' . esc_html__( 'Contact:', 'local-tasker' ) . ' ' . esc_html( $order->get_billing_email() ) . ' / ' . esc_html( $order->get_billing_phone() ) . '</p>'
		. $list_html
		. '<p><a href="' . esc_url( $order->get_edit_order_url() ) . '">' . esc_html__( 'View order', 'local-tasker' ) . '</a></p>';

	$mailer->send(
		$admin_to,
		$admin_subject,
		$mailer->wrap_message( __( 'Installation quote requested', 'local-tasker' ), $admin_body )
	);

	$order->update_meta_data( '_lt_install_notified', current_time( 'mysql' ) );
	$order->save();
}
add_action( 'woocommerce_checkout_order_processed', 'lt_maybe_send_install_emails', 20, 1 );
add_action( 'woocommerce_store_api_checkout_order_processed', 'lt_maybe_send_install_emails', 20, 1 );
