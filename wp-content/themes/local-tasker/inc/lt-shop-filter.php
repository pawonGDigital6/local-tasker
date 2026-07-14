<?php
/**
 * Local Tasker — Shop archive filter engine.
 *
 * A single source of truth for turning a filter request (from GET params on first
 * load, or from the AJAX body on subsequent interactions) into a WP_Query, and for
 * rendering the three regions of the archive: toolbar, sidebar and product grid.
 *
 * Both the `acf-block/shop-archive` block and the WooCommerce `archive-product.php`
 * template call the same helpers, so server-rendered and AJAX-rendered markup are
 * byte-for-byte identical.
 *
 * Taxonomies / attributes used
 * ----------------------------
 *   product_cat    Category      (SPC Hybrid, Engineered Timber, Tiles, Decking, …)
 *   pa_colour      Colour/Finish (Natural Oak, Dark Grey, Warm White, …)
 *   pa_thickness   Thickness     (6mm, 7mm, 8.5mm, 9mm, 10mm, 12mm+)
 *
 * @package local-tasker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalise a raw request array into a clean, typed filter state.
 *
 * @param array $req Raw request ($_GET or AJAX $_POST).
 * @return array
 */
function lt_shop_parse_request( array $req ) {
	$to_slugs = static function ( $value ) {
		if ( is_string( $value ) ) {
			$value = array_filter( array_map( 'trim', explode( ',', $value ) ) );
		}
		return array_values( array_unique( array_map( 'sanitize_title', (array) $value ) ) );
	};

	return array(
		'paged'      => isset( $req['paged'] ) ? max( 1, absint( $req['paged'] ) ) : 1,
		'per_page'   => isset( $req['per_page'] ) ? min( 48, max( 1, absint( $req['per_page'] ) ) ) : 9,
		'category'   => ! empty( $req['category'] ) ? sanitize_title( wp_unslash( $req['category'] ) ) : '',
		'colour'     => isset( $req['colour'] ) ? $to_slugs( wp_unslash( $req['colour'] ) ) : array(),
		'thickness'  => isset( $req['thickness'] ) ? $to_slugs( wp_unslash( $req['thickness'] ) ) : array(),
		'min_price'  => isset( $req['min_price'] ) && '' !== $req['min_price'] ? (float) $req['min_price'] : null,
		'max_price'  => isset( $req['max_price'] ) && '' !== $req['max_price'] ? (float) $req['max_price'] : null,
		'on_sale'    => ! empty( $req['on_sale'] ),
		'in_stock'   => ! empty( $req['in_stock'] ),
		'new'        => ! empty( $req['new'] ),
		'search'     => isset( $req['search'] ) ? sanitize_text_field( wp_unslash( $req['search'] ) ) : '',
		'orderby'    => isset( $req['orderby'] ) ? sanitize_key( $req['orderby'] ) : 'popularity',
	);
}

/**
 * Build WP_Query args from a parsed filter state.
 *
 * @param array $state Parsed state from lt_shop_parse_request().
 * @return array
 */
function lt_shop_build_query_args( array $state ) {
	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => $state['per_page'],
		'paged'               => $state['paged'],
		'ignore_sticky_posts' => true,
		'tax_query'           => array( 'relation' => 'AND' ),
		'meta_query'          => array( 'relation' => 'AND' ),
	);

	// Hide out-of-stock if the store is configured to.
	if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'outofstock',
			'operator' => 'NOT IN',
		);
	}

	// Category.
	if ( $state['category'] && 'all' !== $state['category'] ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $state['category'],
		);
	}

	// Colour / finish.
	if ( ! empty( $state['colour'] ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'pa_colour',
			'field'    => 'slug',
			'terms'    => $state['colour'],
			'operator' => 'IN',
		);
	}

	// Thickness.
	if ( ! empty( $state['thickness'] ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'pa_thickness',
			'field'    => 'slug',
			'terms'    => $state['thickness'],
			'operator' => 'IN',
		);
	}

	// Price range (matches WooCommerce's own _price meta).
	if ( null !== $state['min_price'] ) {
		$args['meta_query'][] = array(
			'key'     => '_price',
			'value'   => $state['min_price'],
			'compare' => '>=',
			'type'    => 'DECIMAL(12,2)',
		);
	}
	if ( null !== $state['max_price'] ) {
		$args['meta_query'][] = array(
			'key'     => '_price',
			'value'   => $state['max_price'],
			'compare' => '<=',
			'type'    => 'DECIMAL(12,2)',
		);
	}

	// In stock only.
	if ( $state['in_stock'] ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'outofstock',
			'operator' => 'NOT IN',
		);
	}

	// On sale only.
	if ( $state['on_sale'] ) {
		$on_sale_ids        = wc_get_product_ids_on_sale();
		$on_sale_ids[]      = 0; // Guarantee an empty result rather than "all".
		$args['post__in']   = $on_sale_ids;
	}

	// New arrivals (last 30 days).
	if ( $state['new'] ) {
		$args['date_query'] = array(
			array( 'after' => '30 days ago' ),
		);
	}

	// Search.
	if ( '' !== $state['search'] ) {
		$args['s'] = $state['search'];
	}

	// Sort.
	switch ( $state['orderby'] ) {
		case 'price':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_price';
			$args['order']    = 'ASC';
			break;
		case 'price-desc':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_price';
			$args['order']    = 'DESC';
			break;
		case 'date':
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			break;
		case 'rating':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_wc_average_rating';
			$args['order']    = 'DESC';
			break;
		case 'popularity':
		default:
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = 'total_sales';
			$args['order']    = 'DESC';
			break;
	}

	return apply_filters( 'lt_shop_query_args', $args, $state );
}

/**
 * Render the product grid for a query (cards only; caller supplies the wrapper).
 *
 * @param WP_Query $query Query.
 * @return void Echoes markup.
 */
function lt_shop_render_cards( WP_Query $query ) {
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/woocommerce/product-card' );
		}
		wp_reset_postdata();
	} else {
		echo '<p class="lt-shop__empty col-span-full">'
			. esc_html__( 'No products match your filters. Try clearing a few.', 'local-tasker' )
			. '</p>';
	}
}

/**
 * Category chips for the toolbar (top level product categories).
 *
 * @return WP_Term[]
 */
function lt_shop_get_categories() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Attribute terms with counts for the sidebar.
 *
 * @param string $taxonomy e.g. pa_colour.
 * @return WP_Term[]
 */
function lt_shop_get_attribute_terms( $taxonomy ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Render the toolbar (title, result count, category chips, sort).
 *
 * @param array $state Parsed filter state.
 * @param int   $found Total results found.
 * @return void
 */
function lt_shop_render_toolbar( array $state, $found ) {
	$categories = lt_shop_get_categories();
	$chips      = array(
		array( 'key' => 'all',      'label' => __( 'All', 'local-tasker' ) ),
		array( 'key' => 'on_sale',  'label' => __( 'On Sale', 'local-tasker' ) ),
		array( 'key' => 'new',      'label' => __( 'New Arrivals', 'local-tasker' ) ),
		array( 'key' => 'in_stock', 'label' => __( 'In Stock', 'local-tasker' ) ),
	);
	$sort_options = array(
		'popularity' => __( 'Best Selling', 'local-tasker' ),
		'date'       => __( 'Newest', 'local-tasker' ),
		'price'      => __( 'Price: Low to High', 'local-tasker' ),
		'price-desc' => __( 'Price: High to Low', 'local-tasker' ),
		'rating'     => __( 'Top Rated', 'local-tasker' ),
	);
	?>
	<div class="lt-shop__toolbar">
		<div class="lt-shop__toolbar-title">
			<h1 class="lt-shop__heading"><?php esc_html_e( 'All Products', 'local-tasker' ); ?></h1>
			<span class="lt-shop__count" data-lt-count><?php echo esc_html( sprintf( _n( '%s result', '%s results', $found, 'local-tasker' ), number_format_i18n( $found ) ) ); ?></span>
		</div>

		<div class="lt-shop__chips" role="group" aria-label="<?php esc_attr_e( 'Quick filters', 'local-tasker' ); ?>">
			<?php
			$no_flags = ! $state['on_sale'] && ! $state['new'] && ! $state['in_stock'];
			foreach ( $chips as $chip ) :
				$active = ( 'all' === $chip['key'] && $no_flags ) || ( 'all' !== $chip['key'] && ! empty( $state[ $chip['key'] ] ) );
				?>
				<button type="button"
					class="lt-chip<?php echo $active ? ' is-active' : ''; ?>"
					data-lt-chip="<?php echo esc_attr( $chip['key'] ); ?>"
					aria-pressed="<?php echo $active ? 'true' : 'false'; ?>">
					<?php echo esc_html( $chip['label'] ); ?>
				</button>
			<?php endforeach; ?>

			<?php foreach ( $categories as $cat ) :
				$active = $state['category'] === $cat->slug;
				?>
				<button type="button"
					class="lt-chip lt-chip--cat<?php echo $active ? ' is-active' : ''; ?>"
					data-lt-chip-cat="<?php echo esc_attr( $cat->slug ); ?>"
					aria-pressed="<?php echo $active ? 'true' : 'false'; ?>">
					<?php echo esc_html( $cat->name ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="lt-shop__sort">
			<label for="lt-shop-sort" class="lt-shop__sort-label"><?php esc_html_e( 'Sort:', 'local-tasker' ); ?></label>
			<select id="lt-shop-sort" class="lt-shop__sort-select" data-lt-sort>
				<?php foreach ( $sort_options as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $state['orderby'], $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<?php
}

/**
 * Render one collapsible sidebar group of checkbox filters.
 *
 * @param string $title    Group title.
 * @param string $filter   Filter key (colour|thickness).
 * @param string $taxonomy Source taxonomy.
 * @param array  $selected Selected slugs.
 * @return void
 */
function lt_shop_render_filter_group( $title, $filter, $taxonomy, array $selected ) {
	$terms = lt_shop_get_attribute_terms( $taxonomy );
	if ( empty( $terms ) ) {
		return;
	}
	?>
	<div class="lt-facet" data-lt-facet>
		<button type="button" class="lt-facet__head" aria-expanded="true">
			<span class="lt-facet__title"><?php echo esc_html( $title ); ?></span>
			<svg class="lt-facet__chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		</button>
		<div class="lt-facet__body">
			<?php foreach ( $terms as $term ) : ?>
				<label class="lt-facet__option">
					<input type="checkbox"
						class="lt-facet__input"
						data-lt-filter="<?php echo esc_attr( $filter ); ?>"
						value="<?php echo esc_attr( $term->slug ); ?>"
						<?php checked( in_array( $term->slug, $selected, true ) ); ?> />
					<span class="lt-facet__label"><?php echo esc_html( $term->name ); ?></span>
					<span class="lt-facet__count"><?php echo esc_html( $term->count ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Render the full sidebar (category list, price range, colour, thickness).
 *
 * @param array $state Parsed filter state.
 * @return void
 */
function lt_shop_render_sidebar( array $state ) {
	$categories = lt_shop_get_categories();
	?>
	<aside class="lt-shop__sidebar" aria-label="<?php esc_attr_e( 'Product filters', 'local-tasker' ); ?>">
		<div class="lt-shop__sidebar-head">
			<span class="lt-shop__filters-title"><?php esc_html_e( 'Filters', 'local-tasker' ); ?></span>
		</div>

		<!-- Category -->
		<div class="lt-facet" data-lt-facet>
			<button type="button" class="lt-facet__head" aria-expanded="true">
				<span class="lt-facet__title"><?php esc_html_e( 'Category', 'local-tasker' ); ?></span>
				<svg class="lt-facet__chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<div class="lt-facet__body">
				<label class="lt-facet__option">
					<input type="radio" name="lt-cat" class="lt-facet__input" data-lt-filter="category" value="" <?php checked( '' === $state['category'] ); ?> />
					<span class="lt-facet__label"><?php esc_html_e( 'All Products', 'local-tasker' ); ?></span>
				</label>
				<?php foreach ( $categories as $cat ) : ?>
					<label class="lt-facet__option">
						<input type="radio" name="lt-cat" class="lt-facet__input" data-lt-filter="category" value="<?php echo esc_attr( $cat->slug ); ?>" <?php checked( $state['category'], $cat->slug ); ?> />
						<span class="lt-facet__label"><?php echo esc_html( $cat->name ); ?></span>
						<span class="lt-facet__count"><?php echo esc_html( $cat->count ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Price Range -->
		<div class="lt-facet" data-lt-facet>
			<button type="button" class="lt-facet__head" aria-expanded="true">
				<span class="lt-facet__title"><?php esc_html_e( 'Price Range', 'local-tasker' ); ?></span>
				<svg class="lt-facet__chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<div class="lt-facet__body">
				<div class="lt-price-range">
					<input type="number" inputmode="numeric" min="0" class="lt-price-range__input" data-lt-filter="min_price" placeholder="<?php esc_attr_e( 'Min', 'local-tasker' ); ?>" value="<?php echo null !== $state['min_price'] ? esc_attr( $state['min_price'] ) : ''; ?>" aria-label="<?php esc_attr_e( 'Minimum price', 'local-tasker' ); ?>" />
					<span class="lt-price-range__sep">&ndash;</span>
					<input type="number" inputmode="numeric" min="0" class="lt-price-range__input" data-lt-filter="max_price" placeholder="<?php esc_attr_e( 'Max', 'local-tasker' ); ?>" value="<?php echo null !== $state['max_price'] ? esc_attr( $state['max_price'] ) : ''; ?>" aria-label="<?php esc_attr_e( 'Maximum price', 'local-tasker' ); ?>" />
				</div>
			</div>
		</div>

		<!-- Colour / Finish -->
		<?php lt_shop_render_filter_group( __( 'Colour / Finish', 'local-tasker' ), 'colour', 'pa_colour', $state['colour'] ); ?>

		<!-- Thickness -->
		<?php lt_shop_render_filter_group( __( 'Thickness', 'local-tasker' ), 'thickness', 'pa_thickness', $state['thickness'] ); ?>

		<button type="button" class="lt-shop__clear" data-lt-clear>
			<svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
			<?php esc_html_e( 'Clear All Filters', 'local-tasker' ); ?>
		</button>
	</aside>
	<?php
}

/**
 * Render the entire shop archive component (toolbar + sidebar + grid + load-more).
 *
 * Shared by the block and the archive-product.php template.
 *
 * @param array $overrides Optional forced state (e.g. per_page from the block).
 * @return void
 */
function lt_render_shop_archive( array $overrides = array() ) {
	// Merge GET params with any block overrides, then parse.
	$raw   = array_merge( $_GET, $overrides ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$state = lt_shop_parse_request( $raw );

	$args  = lt_shop_build_query_args( $state );
	$query = new WP_Query( $args );

	$found    = (int) $query->found_posts;
	$has_more = $query->max_num_pages > $state['paged'];
	$nonce    = wp_create_nonce( 'lt_shop_filter' );
	?>
	<div class="lt-shop"
		data-lt-shop
		data-nonce="<?php echo esc_attr( $nonce ); ?>"
		data-per-page="<?php echo esc_attr( $state['per_page'] ); ?>"
		data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">

		<?php lt_shop_render_toolbar( $state, $found ); ?>

		<div class="lt-shop__body">
			<?php lt_shop_render_sidebar( $state ); ?>

			<div class="lt-shop__main">
				<div class="lt-shop__grid" data-lt-grid aria-live="polite" aria-busy="false">
					<?php lt_shop_render_cards( $query ); ?>
				</div>

				<div class="lt-shop__more<?php echo $has_more ? '' : ' is-hidden'; ?>" data-lt-more-wrap>
					<button type="button" class="lt-btn lt-btn--outline" data-lt-more>
						<?php esc_html_e( 'Load More', 'local-tasker' ); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
