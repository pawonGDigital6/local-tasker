<?php
/**
 * ACF Block Template: Pricing Showcase ("Better Flooring Prices Through Scale")
 *
 * A dynamic heading plus a static grid of WooCommerce products (reusing the
 * same product-card partial as the shop loop / Popular Products block) and a
 * "View All" button. Used on Service Inner pages.
 *
 * @param array $block The block settings and attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Support custom "anchor" values.
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
	$anchor = 'id="' . esc_attr( $block['anchor'] ) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'acf-block lt-pricing-showcase py-16';

if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$class_name .= generate_block_settings_classnames()['class_name'];

// Block Style.
$style_attr  = '';
$block_style = generate_block_settings_classnames()['block_style'];

if ( '' !== $block_style ) {
	$style_attr = 'style="' . $block_style . '"';
}

// ─── ACF Fields ───────────────────────────────────────────────────────────
$ps_title           = get_field( 'ps_title' ) ?: __( 'Better Flooring Prices Through Scale', 'local-tasker' );
$ps_category        = get_field( 'ps_category' );
$ps_products_count   = (int) get_field( 'ps_products_count' ) ?: 4;
$ps_on_sale_only     = (bool) get_field( 'ps_on_sale_only' );
$ps_view_all_label   = get_field( 'ps_view_all_label' ) ?: __( 'View All Products', 'local-tasker' );
$ps_view_all_url     = get_field( 'ps_view_all_url' ) ?: get_permalink( wc_get_page_id( 'shop' ) );

// ─── Product Query ────────────────────────────────────────────────────────
$products_query = new WP_Query( array() );

if ( class_exists( 'WooCommerce' ) ) {
	$query_args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $ps_products_count,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	);

	if ( $ps_category ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $ps_category,
			),
		);
	}

	if ( $ps_on_sale_only ) {
		$sale_ids = wc_get_product_ids_on_sale();
		$query_args['post__in'] = ! empty( $sale_ids ) ? $sale_ids : array( 0 );
	}

	$products_query = new WP_Query( $query_args );
}
?>
<section <?php echo esc_attr( $anchor ); ?> class="<?php echo esc_attr( $class_name ); ?>" <?php echo $style_attr; ?>>
	<div class="wd:container-bx container">

		<!-- Section Heading (fully dynamic) -->
		<?php if ( $ps_title ) : ?>
			<h2 class="sec-title text-center sm:text-[1.875rem] text-body-xl sm:leading-[1.2] leading-[normal] text-lt-text-primary sm:tracking-[0.4px] tracking-[-0.95px] max-sm:text-h2 mb-10">
				<?php echo esc_html( $ps_title ); ?>
			</h2>
		<?php endif; ?>

		<!-- Product Grid — reuses the same card markup/classes as the WooCommerce Shop Loop -->
		<ul class="lt-products-grid grid grid-cols-1 smlr:grid-cols-2 md:grid-cols-3 wd:grid-cols-4 gap-5">
			<?php
			if ( $products_query->have_posts() ) :
				while ( $products_query->have_posts() ) :
					$products_query->the_post();
					wc_get_template_part( 'content', 'product' );
				endwhile;
			else :
				echo '<li class="col-span-full text-center text-lt-text-placeholder py-8">'
					. esc_html__( 'No products found.', 'local-tasker' )
					. '</li>';
			endif;

			wp_reset_postdata();
			?>
		</ul>

		<!-- View All -->
		<div class="ld-btn-wrap flex justify-center mt-12">
			<a href="<?php echo esc_url( $ps_view_all_url ); ?>" class="btn btn--brand md:min-w-[256px] cursor-pointer max-smlr:w-full">
				<?php echo esc_html( $ps_view_all_label ); ?>
			</a>
		</div>

	</div>
</section>
