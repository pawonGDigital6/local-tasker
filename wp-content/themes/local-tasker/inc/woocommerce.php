<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package local-tasker
 */

/**
 * WooCommerce setup function.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)
 * @link https://github.com/woocommerce/woocommerce/wiki/Declaring-WooCommerce-support-in-themes
 *
 * @return void
 */
function local_tasker_woocommerce_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 150,
			'single_image_width'    => 300,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 1,
				'max_columns'     => 6,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'local_tasker_woocommerce_setup' );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function local_tasker_woocommerce_scripts() {
	wp_enqueue_style( 'local-tasker-woocommerce-style', get_template_directory_uri() . '/woocommerce.css', array(), _S_VERSION );

	// The header mini-cart badge isn't rendered via WC_Widget_Cart, which is the
	// only place WooCommerce core auto-enqueues wc-cart-fragments. Without it,
	// nothing on the page listens for 'wc_fragment_refresh' and the badge never
	// updates via AJAX (only on a full reload).
	wp_enqueue_script( 'wc-cart-fragments' );

	$font_path   = WC()->plugin_url() . '/assets/fonts/';
	$inline_font = '@font-face {
			font-family: "star";
			src: url("' . $font_path . 'star.eot");
			src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
				url("' . $font_path . 'star.woff") format("woff"),
				url("' . $font_path . 'star.ttf") format("truetype"),
				url("' . $font_path . 'star.svg#star") format("svg");
			font-weight: normal;
			font-style: normal;
		}';

	wp_add_inline_style( 'local-tasker-woocommerce-style', $inline_font );
}
add_action( 'wp_enqueue_scripts', 'local_tasker_woocommerce_scripts' );

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function local_tasker_woocommerce_active_body_class( $classes ) {
	$classes[] = 'woocommerce-active';

	return $classes;
}
add_filter( 'body_class', 'local_tasker_woocommerce_active_body_class' );

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function local_tasker_woocommerce_related_products_args( $args ) {
	$defaults = array(
		'posts_per_page' => 3,
		'columns'        => 3,
	);

	$args = wp_parse_args( $defaults, $args );

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'local_tasker_woocommerce_related_products_args' );

/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'local_tasker_woocommerce_wrapper_before' ) ) {
	/**
	 * Before Content.
	 *
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 *
	 * @return void
	 */
	function local_tasker_woocommerce_wrapper_before() {
		?>
			<main id="primary" class="site-main">
		<?php
	}
}
add_action( 'woocommerce_before_main_content', 'local_tasker_woocommerce_wrapper_before' );

if ( ! function_exists( 'local_tasker_woocommerce_wrapper_after' ) ) {
	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function local_tasker_woocommerce_wrapper_after() {
		?>
			</main><!-- #main -->
		<?php
	}
}
add_action( 'woocommerce_after_main_content', 'local_tasker_woocommerce_wrapper_after' );

/**
 * Sample implementation of the WooCommerce Mini Cart.
 *
 * You can add the WooCommerce Mini Cart to header.php like so ...
 *
	<?php
		if ( function_exists( 'local_tasker_woocommerce_header_cart' ) ) {
			local_tasker_woocommerce_header_cart();
		}
	?>
 */

if ( ! function_exists( 'local_tasker_woocommerce_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function local_tasker_woocommerce_cart_link_fragment( $fragments ) {
		ob_start();
		local_tasker_woocommerce_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'local_tasker_woocommerce_cart_link_fragment' );

if ( ! function_exists( 'local_tasker_woocommerce_cart_link' ) ) {
	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function local_tasker_woocommerce_cart_link() {
		$count = count( WC()->cart->get_cart() );
		?>
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-icon cart-contents relative pr-3" title="<?php esc_attr_e( 'View your shopping cart', 'local-tasker' ); ?>" data-lt-cart-toggle aria-haspopup="dialog" aria-controls="lt-mini-cart-panel" aria-expanded="false">
			<span class="cart-count absolute top-[-6px] right-[0] max-sm:top-[-9px] items-center justify-center bg-lt-brand text-lt-white rounded-full w-[20px] h-[20px] text-caption-sm <?php echo $count > 0 ? 'flex' : 'hidden'; ?>"><?php echo esc_html( $count ); ?></span>
			<svg class="max-md:w-[16px]" width="19" height="21" viewBox="0 0 19 21" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path
					d="M5 6.5V5C5 3.80653 5.47411 2.66193 6.31802 1.81802C7.16193 0.974106 8.30653 0.5 9.5 0.5C10.6935 0.5 11.8381 0.974106 12.682 1.81802C13.5259 2.66193 14 3.80653 14 5V6.5M2.25 6.5C2.05109 6.5 1.86032 6.57902 1.71967 6.71967C1.57902 6.86032 1.5 7.05109 1.5 7.25L0.5 17.375C0.5 18.793 1.707 20 3.125 20H15.875C17.293 20 18.5 18.851 18.5 17.434L17.5 7.25C17.5 7.05109 17.421 6.86032 17.2803 6.71967C17.1397 6.57902 16.9489 6.5 16.75 6.5H2.25Z"
					stroke="#2E2E2E" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</a>
		<?php
	}
}

/**
 * AJAX: add flooring boxes to cart with cart item meta.
 */
function lt_ajax_add_flooring_to_cart(): void {
	check_ajax_referer( 'lt_add_to_cart_' . intval( $_POST['product_id'] ?? 0 ), 'nonce' );

	$product_id   = absint( $_POST['product_id'] ?? 0 );
	$quantity     = absint( $_POST['quantity']   ?? 0 );
	$area_sqm     = (float) ( $_POST['area_sqm'] ?? 0 );
	$variation_id = absint( $_POST['variation_id'] ?? 0 );

	// The installation opt-in — a request for a quote, not a purchased add-on.
	// It records intent only and must never move the price. Products with the
	// "Installation Quote" toggle off never render the checkbox, and the toggle is
	// re-checked here so the flag cannot be posted onto them regardless.
	$install_quote = isset( $_POST['install_quote'] )
		&& in_array( (string) $_POST['install_quote'], [ '1', 'yes', 'true', 'on' ], true )
		&& lt_product_offers_install_quote( $product_id );

	if ( ! $product_id || $quantity < 1 ) {
		wp_send_json_error( [ 'message' => 'Invalid product or quantity.' ] );
	}

	$product = wc_get_product( $product_id );

	if ( $product && $product->is_type( 'variable' ) && ! $variation_id ) {
		wp_send_json_error( [ 'message' => 'Please select all options before adding to cart.' ] );
	}

	$variation_attrs = [];
	if ( $variation_id && isset( $_POST['variation_attributes'] ) ) {
		$raw = json_decode( wp_unslash( $_POST['variation_attributes'] ), true );
		if ( is_array( $raw ) ) {
			foreach ( $raw as $key => $value ) {
				$key = sanitize_key( $key );
				if ( 0 === strpos( $key, 'attribute_' ) ) {
					$variation_attrs[ $key ] = sanitize_text_field( $value );
				}
			}
		}
	}

	$carton_sqm = (float) get_post_meta( $product_id, 'carton_sqm', true );

	/*
	 * Only data that genuinely makes two lines DIFFERENT belongs in the cart-item
	 * data passed to add_to_cart(). WC_Cart::generate_cart_id() hashes every key
	 * *and value* of that array into the cart item id, so including the per-add
	 * measurements (area / boxes / coverage) gave every add a unique id and forced
	 * a brand new cart line instead of topping up the existing one.
	 *
	 * The installation-quote flag no longer changes the price, but it still
	 * belongs in the identity: a line the customer wants quoted and a line they
	 * do not are different requests, and merging them would silently drop the
	 * request from one of them. 'lt_boxed' is a constant marker that flags the
	 * line as box-priced from the moment it is created, which matters because
	 * add_to_cart() calculates totals before we get a chance to write anything
	 * back.
	 */
	$identity_data = [
		'lt_boxed'   => 'yes',
		'lt_install' => $install_quote ? 'yes' : '',
	];

	$added = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation_attrs, $identity_data );

	if ( $added ) {
		/*
		 * Measurements are recorded on the resulting line rather than in its
		 * identity. When an existing line was topped up, WooCommerce has already
		 * summed the quantities, so read the line back for the true box count and
		 * accumulate the area the shopper asked for.
		 */
		$contents = WC()->cart->get_cart_contents();
		if ( isset( $contents[ $added ] ) ) {
			$boxes = (int) $contents[ $added ]['quantity'];

			$contents[ $added ]['lt_boxes']        = $boxes;
			$contents[ $added ]['lt_area_sqm']     = round( (float) ( $contents[ $added ]['lt_area_sqm'] ?? 0 ) + $area_sqm, 2 );
			$contents[ $added ]['lt_coverage_sqm'] = round( $boxes * $carton_sqm, 4 );

			WC()->cart->set_cart_contents( $contents );
			WC()->cart->set_session();
		}

		wp_send_json_success( [
			'cart_count' => WC()->cart->get_cart_contents_count(),
			'cart_url'   => wc_get_cart_url(),
		] );
	} else {
		wp_send_json_error( [ 'message' => 'Could not add to cart.' ] );
	}
}
add_action( 'wp_ajax_lt_add_flooring_to_cart',        'lt_ajax_add_flooring_to_cart' );
add_action( 'wp_ajax_nopriv_lt_add_flooring_to_cart', 'lt_ajax_add_flooring_to_cart' );

/**
 * Flooring is sold by the box but the WooCommerce cart quantity added by
 * lt_ajax_add_flooring_to_cart() is the number of BOXES, priced at the
 * product's raw $/sqm price — so without this, cart/checkout totals come
 * out as (boxes × $/sqm) instead of (boxes × box price). Re-derive each
 * flooring line's unit price from scratch on every totals pass so it
 * matches the box price (and install add-on) shown on the product page.
 */
function lt_apply_flooring_box_pricing( WC_Cart $cart ): void {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	foreach ( $cart->get_cart() as $cart_item ) {
		// 'lt_boxed' marks lines created by the box calculator; 'lt_boxes' is the
		// pre-existing marker, kept so carts already in a customer session on the
		// day this shipped keep their box pricing.
		if ( empty( $cart_item['lt_boxed'] ) && empty( $cart_item['lt_boxes'] ) ) {
			continue;
		}

		$carton_sqm = (float) get_post_meta( $cart_item['product_id'], 'carton_sqm', true );
		if ( $carton_sqm <= 0 ) {
			continue;
		}

		/*
		 * Read the per-sqm rate from a FRESH product object, never from
		 * $cart_item['data'] — that is the object we are about to overwrite with
		 * the box price. woocommerce_before_calculate_totals can fire more than
		 * once per request (session load, then add_to_cart), and re-reading the
		 * mutated object would multiply the already-multiplied price by the
		 * carton size a second time. Deriving from the stored price keeps this
		 * idempotent however many times it runs.
		 */
		$priced_id              = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
		$priced_product         = wc_get_product( $priced_id );
		if ( ! $priced_product ) {
			continue;
		}
		$price_per_sqm          = (float) $priced_product->get_price();
		$regular_price_per_sqm  = (float) $priced_product->get_regular_price();

		/*
		 * Installation is quoted separately, not sold here: 'lt_install' marks a
		 * request for a quote and deliberately does NOT feed into the price.
		 * There is no installation cost anywhere in the catalogue.
		 */

		$cart_item['data']->set_price( $price_per_sqm * $carton_sqm );
		$cart_item['data']->set_regular_price( $regular_price_per_sqm * $carton_sqm );
	}
}
add_action( 'woocommerce_before_calculate_totals', 'lt_apply_flooring_box_pricing' );

/**
 * Enqueue shop archive JS on the shop/archive pages only.
 */
function lt_shop_archive_scripts() {
	if ( is_shop() || is_product_category() || is_product_tag() ) {
		wp_enqueue_script(
			'lt-shop-archive',
			get_template_directory_uri() . '/js/shop-archive.js',
			[],
			'1.2.0',
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);

		$per_page = (int) apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );
		wp_localize_script(
			'lt-shop-archive',
			'ltShopFilter',
			[
				'ajaxUrl'  => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'    => wp_create_nonce( 'lt_shop_load' ),
				'perPage'  => $per_page > 0 ? $per_page : 9,
			]
		);
	}
}
add_action( 'wp_enqueue_scripts', 'lt_shop_archive_scripts' );

/**
 * Enqueue product single JS (built by wp-scripts from js/product-single.js via global build).
 * The file uses Swiper so it must go through the build step.
 * For now registered as a plain script; once bundled it will resolve Swiper correctly.
 */
function lt_product_single_scripts(): void {
	if ( ! is_product() ) {
		return;
	}

	// product-single.js is bundled as part of the global build into build/global/.
	// The entry is registered in src/global/js/main.js — import it there or
	// register a separate entry via the build system. Handle as external script for now.
	$product_single_path = get_template_directory() . '/build/global/product-single.js';
	wp_enqueue_script(
		'lt-product-single',
		get_template_directory_uri() . '/build/global/product-single.js',
		[],
		file_exists( $product_single_path ) ? (string) filemtime( $product_single_path ) : '1.0.0',
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);

	// Pass ajaxUrl + currency symbol to the script.
	wp_localize_script(
		'lt-product-single',
		'ltShopData',
		[
			'ajaxUrl'          => esc_url( admin_url( 'admin-ajax.php' ) ),
			'currencySymbol'   => get_woocommerce_currency_symbol(),
			'currencyPosition' => get_option( 'woocommerce_currency_pos', 'left' ),
		]
	);
}
add_action( 'wp_enqueue_scripts', 'lt_product_single_scripts' );

/**
 * Map quick-filter pill keys to their real product_cat slug.
 *
 * Single source of truth for the pill→category mapping — shared by the query
 * filter below, the AJAX query builder, and the archive template (which reads
 * the same slugs to render `data-lt-cat` on each pill and to pre-highlight the
 * active pill on category archive pages). Keep in sync with the pill labels
 * defined in woocommerce/archive-product.php.
 *
 * @return array<string,string>
 */
function lt_shop_pill_category_map(): array {
	return [
		'spc-hybrid' => 'spc-hybrid-flooring',
		'engineered' => 'engineered-timber-flooring',
		'porcelain'  => 'tiles',
	];
}

/**
 * Apply sidebar + quick-pill filters to the WooCommerce product loop.
 *
 * Uses woocommerce_product_query instead of pre_get_posts so we get the
 * correctly-initialised WC query context without needing is_shop() / is_product_category()
 * checks (those are global WC functions, not WP_Query methods, and are unreliable
 * inside pre_get_posts before WC's own hook has run).
 *
 * @param WP_Query $q The product loop query, already configured by WC_Query.
 */
function lt_shop_filter_product_query( WP_Query $q ): void {
	$tax_query = (array) $q->get( 'tax_query' );

	// ── Sidebar: Colour / Finish ────────────────────────────────────────────
	$colours = isset( $_GET['filter_colour'] )
		? array_filter( array_map( 'sanitize_text_field', (array) $_GET['filter_colour'] ) )
		: [];
	if ( ! empty( $colours ) ) {
		$tax_query[] = [
			'taxonomy' => 'pa_colour',
			'field'    => 'slug',
			'terms'    => $colours,
			'operator' => 'IN',
		];
	}

	// ── Sidebar: Thickness ──────────────────────────────────────────────────
	$thickness = isset( $_GET['filter_thickness'] )
		? array_filter( array_map( 'sanitize_text_field', (array) $_GET['filter_thickness'] ) )
		: [];
	if ( ! empty( $thickness ) ) {
		$tax_query[] = [
			'taxonomy' => 'pa_thickness',
			'field'    => 'slug',
			'terms'    => $thickness,
			'operator' => 'IN',
		];
	}

	// ── Sidebar: Grade ──────────────────────────────────────────────────────
	$grades = isset( $_GET['filter_grade'] )
		? array_filter( array_map( 'sanitize_text_field', (array) $_GET['filter_grade'] ) )
		: [];
	if ( ! empty( $grades ) ) {
		$tax_query[] = [
			'taxonomy' => 'pa_grade',
			'field'    => 'slug',
			'terms'    => $grades,
			'operator' => 'IN',
		];
	}

	// ── Sidebar: Veneer ─────────────────────────────────────────────────────
	$veneers = isset( $_GET['filter_veneer'] )
		? array_filter( array_map( 'sanitize_text_field', (array) $_GET['filter_veneer'] ) )
		: [];
	if ( ! empty( $veneers ) ) {
		$tax_query[] = [
			'taxonomy' => 'pa_veneer',
			'field'    => 'slug',
			'terms'    => $veneers,
			'operator' => 'IN',
		];
	}

	// ── Sidebar: Category radio (single-select) ─────────────────────────────
	$cats = isset( $_GET['product_cat'] )
		? array_filter( array_map( 'sanitize_text_field', (array) $_GET['product_cat'] ) )
		: [];
	if ( ! empty( $cats ) ) {
		$tax_query[] = [
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $cats,
			'operator' => 'IN',
		];
	}

	// ── Quick-pill: on-sale ─────────────────────────────────────────────────
	if ( isset( $_GET['filter'] ) && sanitize_key( $_GET['filter'] ) === 'on-sale' ) {
		$sale_ids    = wc_get_product_ids_on_sale();
		$existing_in = $q->get( 'post__in' );
		$q->set( 'post__in', $existing_in
			? array_intersect( $existing_in, $sale_ids )
			: ( $sale_ids ?: [ 0 ] )
		);
	}

	// ── Quick-pill: new-arrivals (last 30 days) ─────────────────────────────
	if ( isset( $_GET['filter'] ) && sanitize_key( $_GET['filter'] ) === 'new-arrivals' ) {
		$q->set( 'date_query', [ [ 'after' => '30 days ago', 'inclusive' => true ] ] );
	}

	// ── Quick-pill: in-stock ────────────────────────────────────────────────
	// `product_visibility` has no "instock" term — WooCommerce only tags products
	// that are OUT of stock, so "in stock" is the absence of that term. Matching
	// on 'instock' returned zero products and disagreed with the AJAX path.
	if ( isset( $_GET['filter'] ) && sanitize_key( $_GET['filter'] ) === 'in-stock' ) {
		$tax_query[] = [
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'outofstock',
			'operator' => 'NOT IN',
		];
	}

	// ── Sidebar: Availability (in_stock / out_of_stock) ─────────────────────
	$sidebar_avail = isset( $_GET['filter_availability'] ) ? sanitize_key( $_GET['filter_availability'] ) : '';
	if ( 'in_stock' === $sidebar_avail ) {
		$tax_query[] = [
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'outofstock',
			'operator' => 'NOT IN',
		];
	} elseif ( 'out_of_stock' === $sidebar_avail ) {
		$tax_query[] = [
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'outofstock',
			'operator' => 'IN',
		];
	}

	// ── Quick-pill: category shortcuts ─────────────────────────────────────
	$pill_cat_map = lt_shop_pill_category_map();
	if ( isset( $_GET['filter'] ) ) {
		$pill = sanitize_key( $_GET['filter'] );
		if ( array_key_exists( $pill, $pill_cat_map ) ) {
			$tax_query[] = [
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => [ $pill_cat_map[ $pill ] ],
				'operator' => 'IN',
			];
		}
	}

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}
	$q->set( 'tax_query', $tax_query );

	// ── Sidebar: Price range ────────────────────────────────────────────────
	$min = ( isset( $_GET['min_price'] ) && $_GET['min_price'] !== '' ) ? (float) $_GET['min_price'] : null;
	$max = ( isset( $_GET['max_price'] ) && $_GET['max_price'] !== '' && (float) $_GET['max_price'] > 0 ) ? (float) $_GET['max_price'] : null;
	if ( $min !== null || $max !== null ) {
		add_filter( 'posts_where', function ( $where ) use ( $min, $max ) {
			global $wpdb;
			if ( $min !== null ) {
				$where .= $wpdb->prepare(
					" AND {$wpdb->posts}.ID IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_price' AND CAST(meta_value AS DECIMAL(10,2)) >= %f)",
					$min
				);
			}
			if ( $max !== null ) {
				$where .= $wpdb->prepare(
					" AND {$wpdb->posts}.ID IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_price' AND CAST(meta_value AS DECIMAL(10,2)) <= %f)",
					$max
				);
			}
			return $where;
		} );
	}
}
add_action( 'woocommerce_product_query', 'lt_shop_filter_product_query' );

/**
 * Normalise a request array (GET or AJAX POST) into a filter state.
 *
 * @param array $req Raw request.
 * @return array
 */
function lt_shop_get_filters( array $req ): array {
	$slugs = static function ( $v ) {
		return array_values( array_filter( array_map( 'sanitize_title', (array) $v ) ) );
	};
	// Normalise filter_availability: accept 'in_stock' or 'out_of_stock'; anything else is treated as empty.
	$raw_avail = isset( $req['filter_availability'] ) ? sanitize_key( $req['filter_availability'] ) : '';
	$avail     = in_array( $raw_avail, [ 'in_stock', 'out_of_stock' ], true ) ? $raw_avail : '';
	return [
		'product_cat'         => isset( $req['product_cat'] ) ? $slugs( $req['product_cat'] ) : [],
		'filter_colour'       => isset( $req['filter_colour'] ) ? $slugs( $req['filter_colour'] ) : [],
		'filter_thickness'    => isset( $req['filter_thickness'] ) ? $slugs( $req['filter_thickness'] ) : [],
		'filter_grade'        => isset( $req['filter_grade'] ) ? $slugs( $req['filter_grade'] ) : [],
		'filter_veneer'       => isset( $req['filter_veneer'] ) ? $slugs( $req['filter_veneer'] ) : [],
		'min_price'           => ( isset( $req['min_price'] ) && $req['min_price'] !== '' ) ? (float) $req['min_price'] : null,
		'max_price'           => ( isset( $req['max_price'] ) && $req['max_price'] !== '' && (float) $req['max_price'] > 0 ) ? (float) $req['max_price'] : null,
		'filter'              => isset( $req['filter'] ) ? sanitize_key( $req['filter'] ) : 'all',
		'filter_availability' => $avail,
		'orderby'             => isset( $req['orderby'] ) ? sanitize_text_field( $req['orderby'] ) : '',
		'paged'               => max( 1, absint( $req['paged'] ?? 1 ) ),
	];
}

/**
 * Map an orderby key to WP_Query ordering args (mirrors WooCommerce catalog ordering).
 *
 * @param string $orderby Orderby key.
 * @return array
 */
function lt_shop_orderby_args( string $orderby ): array {
	switch ( $orderby ) {
		case 'price':
			return [ 'orderby' => 'meta_value_num', 'meta_key' => '_price', 'order' => 'ASC' ];
		case 'price-desc':
			return [ 'orderby' => 'meta_value_num', 'meta_key' => '_price', 'order' => 'DESC' ];
		case 'rating':
			return [ 'orderby' => 'meta_value_num', 'meta_key' => '_wc_average_rating', 'order' => 'DESC' ];
		case 'date':
			return [ 'orderby' => 'date', 'order' => 'DESC' ];
		case 'popularity':
			return [ 'orderby' => 'meta_value_num', 'meta_key' => 'total_sales', 'order' => 'DESC' ];
		case 'menu_order':
		default:
			return [ 'orderby' => 'menu_order title', 'order' => 'ASC' ];
	}
}

/**
 * Build a products WP_Query from a filter state — used by the AJAX endpoint so
 * results match the server-rendered archive exactly.
 *
 * @param array $f        Filter state from lt_shop_get_filters().
 * @param int   $per_page Products per page.
 * @return WP_Query
 */
function lt_shop_build_query( array $f, int $per_page ): WP_Query {
	$args = array_merge(
		[
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'paged'               => $f['paged'],
			'ignore_sticky_posts' => true,
			'tax_query'           => [ 'relation' => 'AND' ],
			'meta_query'          => [ 'relation' => 'AND' ],
		],
		lt_shop_orderby_args( $f['orderby'] ?: (string) get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) )
	);

	// Respect catalog visibility.
	$hidden = [ 'exclude-from-catalog' ];
	if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
		$hidden[] = 'outofstock';
	}
	$args['tax_query'][] = [
		'taxonomy' => 'product_visibility',
		'field'    => 'name',
		'terms'    => $hidden,
		'operator' => 'NOT IN',
	];

	if ( ! empty( $f['product_cat'] ) ) {
		$args['tax_query'][] = [ 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $f['product_cat'], 'operator' => 'IN' ];
	}
	if ( ! empty( $f['filter_colour'] ) ) {
		$args['tax_query'][] = [ 'taxonomy' => 'pa_colour', 'field' => 'slug', 'terms' => $f['filter_colour'], 'operator' => 'IN' ];
	}
	if ( ! empty( $f['filter_thickness'] ) ) {
		$args['tax_query'][] = [ 'taxonomy' => 'pa_thickness', 'field' => 'slug', 'terms' => $f['filter_thickness'], 'operator' => 'IN' ];
	}
	if ( ! empty( $f['filter_grade'] ) ) {
		$args['tax_query'][] = [ 'taxonomy' => 'pa_grade', 'field' => 'slug', 'terms' => $f['filter_grade'], 'operator' => 'IN' ];
	}
	if ( ! empty( $f['filter_veneer'] ) ) {
		$args['tax_query'][] = [ 'taxonomy' => 'pa_veneer', 'field' => 'slug', 'terms' => $f['filter_veneer'], 'operator' => 'IN' ];
	}

	// Quick pills.
	switch ( $f['filter'] ) {
		case 'on-sale':
			$sale = wc_get_product_ids_on_sale();
			$args['post__in'] = $sale ? $sale : [ 0 ];
			break;
		case 'new-arrivals':
			$args['date_query'] = [ [ 'after' => '30 days ago', 'inclusive' => true ] ];
			break;
		case 'in-stock':
			$args['tax_query'][] = [ 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'outofstock', 'operator' => 'NOT IN' ];
			break;
		case 'spc-hybrid':
		case 'engineered':
		case 'porcelain':
			$pill_cat_map = lt_shop_pill_category_map();
			$args['tax_query'][] = [ 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => [ $pill_cat_map[ $f['filter'] ] ], 'operator' => 'IN' ];
			break;
	}

	// ── Sidebar: Availability filter ─────────────────────────────────────────
	if ( 'in_stock' === $f['filter_availability'] ) {
		$args['tax_query'][] = [ 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'outofstock', 'operator' => 'NOT IN' ];
	} elseif ( 'out_of_stock' === $f['filter_availability'] ) {
		$args['tax_query'][] = [ 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'outofstock', 'operator' => 'IN' ];
	}

	// Price range.
	if ( null !== $f['min_price'] ) {
		$args['meta_query'][] = [ 'key' => '_price', 'value' => $f['min_price'], 'compare' => '>=', 'type' => 'DECIMAL(12,2)' ];
	}
	if ( null !== $f['max_price'] ) {
		$args['meta_query'][] = [ 'key' => '_price', 'value' => $f['max_price'], 'compare' => '<=', 'type' => 'DECIMAL(12,2)' ];
	}

	return new WP_Query( apply_filters( 'lt_shop_ajax_query_args', $args, $f ) );
}

/**
 * Count in-stock / out-of-stock products within the current filter set.
 *
 * Deliberately reuses lt_shop_build_query() rather than assembling its own
 * conditions: the counts are produced by exactly the same category, attribute,
 * price, pill and visibility logic as the product grid, so the two can never
 * drift apart. Availability itself is neutralised first, so the numbers answer
 * "within everything else currently selected, how many are in / out of stock".
 *
 * Stock state comes from WooCommerce's own `product_visibility` → `outofstock`
 * term, the same signal WooCommerce uses for "hide out of stock items".
 *
 * @param array $f Filter state from lt_shop_get_filters().
 * @return array{in_stock:int,out_of_stock:int}
 */
function lt_shop_availability_counts( array $f ): array {
	$base                        = $f;
	$base['filter_availability'] = '';
	$base['paged']               = 1;

	$count = static function ( array $state, string $availability ): int {
		$state['filter_availability'] = $availability;
		// per_page of 1 keeps the row fetch trivial; found_posts is still the full total.
		return (int) lt_shop_build_query( $state, 1 )->found_posts;
	};

	return [
		'in_stock'     => $count( $base, 'in_stock' ),
		'out_of_stock' => $count( $base, 'out_of_stock' ),
	];
}

/**
 * AJAX: filtered / paginated product grid for the shop archive.
 * Returns rendered product cards + pagination metadata.
 */
function lt_ajax_shop_load(): void {
	check_ajax_referer( 'lt_shop_load', 'nonce' );

	$f        = lt_shop_get_filters( wp_unslash( $_POST ) );
	$per_page = isset( $_POST['per_page'] ) ? min( 48, max( 1, absint( $_POST['per_page'] ) ) ) : (int) apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );
	if ( $per_page < 1 ) {
		$per_page = 9;
	}

	$query = lt_shop_build_query( $f, $per_page );

	// Prime the global loop so wc_get_template_part('content','product') works.
	global $wp_query, $woocommerce_loop;
	$original          = $wp_query;
	$wp_query          = $query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	$woocommerce_loop['columns'] = wc_get_default_products_per_row();

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			wc_get_template_part( 'content', 'product' );
		}
	}
	$html = ob_get_clean();

	$wp_query = $original; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	wp_reset_postdata();

	wp_send_json_success( [
		'html'         => $html,
		'found'        => (int) $query->found_posts,
		'max_pages'    => (int) $query->max_num_pages,
		'page'         => $f['paged'],
		'has_more'     => $f['paged'] < (int) $query->max_num_pages,
		// Recomputed per request so the sidebar counts track the active filters.
		'availability' => lt_shop_availability_counts( $f ),
	] );
}
add_action( 'wp_ajax_lt_shop_load', 'lt_ajax_shop_load' );
add_action( 'wp_ajax_nopriv_lt_shop_load', 'lt_ajax_shop_load' );

/**
 * Enqueue the pixel-perfect storefront CSS on shop, product and front (hero) pages,
 * loaded after the compiled Tailwind bundle so it can fine-tune the design.
 */
function lt_enqueue_storefront_css(): void {
	$needs = ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() || is_product() ) ) || is_front_page() || is_page_template();
	if ( ! $needs ) {
		return;
	}
	$rel = '/assets/css/lt-storefront.css';
	wp_enqueue_style(
		'lt-storefront',
		get_template_directory_uri() . $rel,
		[ 'local-tasker-custom-style' ],
		file_exists( get_template_directory() . $rel ) ? filemtime( get_template_directory() . $rel ) : _S_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'lt_enqueue_storefront_css', 30 );

/**
 * Keep the header cart-count badge live without a full page reload.
 *
 * Neither the classic cart AJAX flow (wc-cart.js only fires 'updated_wc_div')
 * nor the Cart/Checkout blocks (which update the wc/store/cart data store,
 * not jQuery events) ever touch our header markup. js/cart-badge.js listens
 * for both signal types and re-fetches the authoritative count straight from
 * the Store API, so it works regardless of which cart experience is active.
 */
function lt_enqueue_cart_badge_script(): void {
	$rel = '/js/cart-badge.js';
	wp_enqueue_script(
		'lt-cart-badge',
		get_template_directory_uri() . $rel,
		[],
		file_exists( get_template_directory() . $rel ) ? filemtime( get_template_directory() . $rel ) : _S_VERSION,
		true
	);
	wp_localize_script(
		'lt-cart-badge',
		'ltCartBadge',
		[ 'storeApiUrl' => esc_url_raw( rest_url( 'wc/store/v1/cart' ) ) ]
	);
}
add_action( 'wp_enqueue_scripts', 'lt_enqueue_cart_badge_script' );

/**
 * Enqueue the header mini-cart drawer script (open/close, escape, overlay).
 */
function lt_enqueue_mini_cart_script(): void {
	$rel = '/js/mini-cart.js';
	wp_enqueue_script(
		'lt-mini-cart',
		get_template_directory_uri() . $rel,
		[ 'jquery' ],
		file_exists( get_template_directory() . $rel ) ? filemtime( get_template_directory() . $rel ) : _S_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'lt_enqueue_mini_cart_script' );

if ( ! function_exists( 'local_tasker_woocommerce_header_cart' ) ) {
	/**
	 * Header cart icon + mini-cart drawer.
	 *
	 * The icon still links to the cart page (works with JS disabled); with JS
	 * enabled, js/mini-cart.js intercepts the click and slides the drawer open
	 * instead. The drawer's contents come from WC_Widget_Cart, which renders
	 * the `div.widget_shopping_cart_content` markup WooCommerce already knows
	 * how to refresh via `wc-cart-fragments` on every add/remove — no extra
	 * fragment wiring needed.
	 *
	 * @return void
	 */
	function local_tasker_woocommerce_header_cart() {
		local_tasker_woocommerce_cart_link();
		?>
		<!-- Mini Cart: overlay -->
		<div id="lt-mini-cart-overlay" class="fixed inset-0 bg-black/40 z-40 hidden" aria-hidden="true"></div>

		<!-- Mini Cart: drawer -->
		<aside
			id="lt-mini-cart-panel"
			class="lt-mini-cart fixed inset-y-0 right-0 z-50 w-[380px] max-w-[90vw] bg-lt-white shadow-2xl overflow-y-auto translate-x-full transition-transform duration-300"
			aria-label="<?php esc_attr_e( 'Shopping cart', 'local-tasker' ); ?>"
			aria-hidden="true"
		>
			<div class="flex items-center justify-between px-5 py-4 border-b border-[#E9EAEC]">
				<span class="text-body font-bold text-lt-text-primary font-semi-ext"><?php esc_html_e( 'Your Cart', 'local-tasker' ); ?></span>
				<button type="button" id="lt-mini-cart-close" class="p-1 rounded hover:bg-lt-snow-drift transition-colors" aria-label="<?php esc_attr_e( 'Close cart', 'local-tasker' ); ?>">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
					</svg>
				</button>
			</div>
			<div class="lt-mini-cart__content p-5">
				<?php the_widget( 'WC_Widget_Cart', array( 'title' => '' ) ); ?>
			</div>
		</aside>
		<?php
	}
}
