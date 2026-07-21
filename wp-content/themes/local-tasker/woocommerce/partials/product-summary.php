<?php
/**
 * Product summary — right-hand column on single product.
 * Contains: tags, title, rating, pricing, trust line, calculator, choose option.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

global $product;

$product_id          = $product->get_id();
$carton_sqm          = (float) get_post_meta( $product_id, 'carton_sqm', true );
$sold_by_box         = (bool)  get_post_meta( $product_id, 'sold_by_box', true );
$install_rate_ex     = (float) get_post_meta( $product_id, 'install_rate_per_sqm', true );
$has_install_option  = $install_rate_ex > 0;

$is_variable         = $product->is_type( 'variable' );

$is_on_sale          = $product->is_on_sale();
$has_price           = '' !== $product->get_price();
$regular_price_ex    = (float) $product->get_regular_price();
$current_price_ex    = (float) $product->get_price();

// _price is already the $/sqm figure — no conversion needed to show it.
$price_per_sqm_ex         = $has_price ? $current_price_ex : 0;
$regular_price_per_sqm_ex = $has_price ? $regular_price_ex : 0;
$price_per_sqm_inc        = $price_per_sqm_ex * 1.10;

// Box price is derived by multiplying the sqm price by the box's coverage — only
// meaningful once both a price and a carton coverage exist.
$has_box_price = $has_price && $carton_sqm > 0;
$box_price_ex  = $has_box_price ? $price_per_sqm_ex * $carton_sqm : 0;

// Tags.
$tags = wc_get_product_tag_list( $product_id, ', ' );

// Rating.
$rating_count  = $product->get_rating_count();
$average       = $product->get_average_rating();

// Variation data — swatch/pill selector + per-variation prices for the calculator.
$variation_groups = array();
$variations_json  = array();
$default_attrs    = array();

if ( $is_variable ) {
	$default_attrs = $product->get_variation_default_attributes();

	foreach ( $product->get_variation_attributes() as $taxonomy => $values ) {
		$options = array();
		foreach ( $values as $slug ) {
			if ( '' === $slug ) {
				continue; // "Any" placeholder — not a selectable option.
			}
			$term  = get_term_by( 'slug', $slug, $taxonomy );
			$name  = $term ? $term->name : $slug;
			$hex   = '';
			$image = '';
			if ( 'pa_colour' === $taxonomy ) {
				$hex = $term ? get_term_meta( $term->term_id, 'lt_swatch_hex', true ) : '';
				if ( ! $hex ) {
					$hex = lt_guess_swatch_hex( $name );
				}
				// Prefer the variation's own image (matches the archive/loop swatches) over a flat colour.
				$image = lt_get_variation_attribute_image( $product, $taxonomy, $slug );
			}
			$options[] = array(
				'slug'  => $slug,
				'name'  => $name,
				'hex'   => $hex,
				'image' => $image,
			);
		}
		if ( $options ) {
			$variation_groups[] = array(
				'taxonomy'  => $taxonomy,
				'label'     => wc_attribute_label( $taxonomy ),
				'is_colour' => 'pa_colour' === $taxonomy,
				'options'   => $options,
			);
		}
	}

	foreach ( $product->get_children() as $variation_id ) {
		$variation = wc_get_product( $variation_id );
		if ( ! $variation instanceof WC_Product_Variation || ! $variation->exists() ) {
			continue;
		}
		$variations_json[] = array(
			'variation_id'      => $variation_id,
			'attributes'        => $variation->get_variation_attributes( false ),
			'price_ex'          => (float) $variation->get_price(),
			'regular_price_ex'  => (float) $variation->get_regular_price(),
			'in_stock'          => $variation->is_in_stock(),
		);
	}
}
?>

<div
	class="lt-product-summary"
	id="lt-product-summary"
	data-product-id="<?php echo esc_attr( $product_id ); ?>"
	data-carton-sqm="<?php echo esc_attr( $carton_sqm ?: 0 ); ?>"
	data-box-price-ex="<?php echo esc_attr( $box_price_ex ); ?>"
	data-price-per-sqm-ex="<?php echo esc_attr( $price_per_sqm_ex ); ?>"
	data-install-rate-ex="<?php echo esc_attr( $install_rate_ex ); ?>"
	data-is-variable="<?php echo esc_attr( $is_variable ? '1' : '0' ); ?>"
	data-variations="<?php echo esc_attr( wp_json_encode( $variations_json ) ); ?>"
	data-default-attributes="<?php echo esc_attr( wp_json_encode( $default_attrs ) ); ?>"
>

	<!-- Tags -->
	<?php if ( $tags ) : ?>
		<div class="lt-product-summary__tags flex flex-wrap gap-2 mb-3">
			<?php
			// Render each tag as a pill.
			$tag_ids = $product->get_tag_ids();
			foreach ( $tag_ids as $tag_id ) :
				$tag = get_term( $tag_id, 'product_tag' );
				if ( ! $tag || is_wp_error( $tag ) ) continue;
			?>
				<a
					href="<?php echo esc_url( get_term_link( $tag ) ); ?>"
					class="inline-block text-caption-xs font-semibold uppercase tracking-[0.07em] text-lt-text-muted border border-[#E9EAEC] rounded-full px-3 py-1 hover:border-lt-brand hover:text-lt-brand transition-colors duration-200"
				>
					<?php echo esc_html( $tag->name ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<!-- Title -->
	<h1 class="lt-product-summary__title text-h4 leading-[1.2] font-bold font-semi-ext text-lt-text-primary mb-3">
		<?php the_title(); ?>
	</h1>

	<!-- Star rating + review count -->
	<?php if ( $rating_count > 0 ) : ?>
		<div class="lt-product-summary__rating flex items-center gap-2 mb-4">
			<div class="flex items-center gap-[2px]" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %s out of 5', 'local-tasker' ), $average ) ); ?>">
				<?php for ( $i = 1; $i <= 5; $i++ ) :
					$fill = $i <= round( (float) $average ) ? '#F59E0B' : '#E5E7EB';
				?>
					<svg width="16" height="16" viewBox="0 0 16 16" fill="<?php echo esc_attr( $fill ); ?>" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M8 1l1.854 4.146H14l-3.427 2.708 1.236 4.146L8 9.764l-3.809 2.236 1.236-4.146L2 5.146h4.146L8 1z"/>
					</svg>
				<?php endfor; ?>
			</div>
			<a href="#lt-reviews" class="text-caption-sm text-lt-text-muted hover:text-lt-brand underline transition-colors duration-200">
				<?php
				printf(
					/* translators: %d: number of reviews */
					esc_html( _n( '%d Review', '%d Reviews', $rating_count, 'local-tasker' ) ),
					$rating_count
				);
				?>
			</a>
		</div>
	<?php endif; ?>

	<!-- Pricing -->
	<div class="lt-product-summary__pricing mb-1">
		<?php if ( $has_price ) : ?>
			<div class="flex flex-wrap items-end gap-x-3 gap-y-1">
				<span id="lt-price-was" class="text-body-lg text-lt-text-muted line-through leading-none<?php echo ( $is_on_sale && $regular_price_per_sqm_ex > 0 ) ? '' : ' hidden'; ?>"><?php echo wc_price( $regular_price_per_sqm_ex ); ?></span>
				<span id="lt-price-now" class="text-h4 font-bold text-lt-text-primary leading-none"><?php echo wc_price( $price_per_sqm_ex ); ?></span>
				<span class="text-body text-lt-text-muted leading-none">/ sqm</span>
			</div>
			<p class="text-caption-sm text-lt-text-muted mt-1 m-0">(incl. GST <span id="lt-price-inc"><?php echo wc_price( $price_per_sqm_inc ); ?></span> / sqm)</p>

			<!-- Box price badge -->
			<div class="mt-3<?php echo ( $sold_by_box && $has_box_price ) ? '' : ' hidden'; ?>" id="lt-price-box-wrap">
				<span class="inline-block text-caption-sm font-medium text-lt-accent bg-lt-accent/8 rounded-[4px] px-[7px] py-[3px]">
					<?php esc_html_e( 'Box price', 'local-tasker' ); ?> <span id="lt-price-box"><?php echo wc_price( $box_price_ex ); ?></span>
					<span class="font-normal"><?php esc_html_e( 'ex GST', 'local-tasker' ); ?></span>
				</span>
			</div>
		<?php else : ?>
			<p class="text-body text-lt-text-muted"><?php esc_html_e( 'Price on request', 'local-tasker' ); ?></p>
		<?php endif; ?>
	</div>

	<!-- Trust line -->
	<p class="lt-product-summary__trust text-caption-sm text-lt-text-muted mb-5 m-0 flex flex-wrap gap-x-3 gap-y-1 items-center">
		<?php if ( $sold_by_box ) : ?>
			<span><?php esc_html_e( 'Sold by the box', 'local-tasker' ); ?></span>
			<span class="text-[#D1D5DB]" aria-hidden="true">·</span>
		<?php endif; ?>
		<span><?php esc_html_e( '5-star rated', 'local-tasker' ); ?></span>
		<span class="text-[#D1D5DB]" aria-hidden="true">·</span>
		<span><?php esc_html_e( 'Free delivery over $199', 'local-tasker' ); ?></span>
	</p>

	<!-- Variation selector (colour / thickness) -->
	<?php if ( $is_variable && $variation_groups ) : ?>
		<div class="lt-variation-selector mb-5" id="lt-variation-selector">
			<?php foreach ( $variation_groups as $group ) : ?>
				<div class="lt-variation-group mb-4" data-attribute="<?php echo esc_attr( $group['taxonomy'] ); ?>">
					<div class="flex items-center justify-between mb-2">
						<span class="text-caption-xs font-semibold uppercase tracking-[0.07em] text-lt-text-muted"><?php echo esc_html( $group['label'] ); ?></span>
						<span class="lt-variation-group__selected text-caption-sm text-lt-text-primary font-medium"></span>
					</div>
					<div class="flex flex-wrap gap-2">
						<?php foreach ( $group['options'] as $option ) :
							$is_default = isset( $default_attrs[ $group['taxonomy'] ] ) && $default_attrs[ $group['taxonomy'] ] === $option['slug'];
						?>
							<?php if ( $group['is_colour'] ) : ?>
								<?php
								$swatch_style = $option['image']
									? 'background-image:url(' . esc_url( $option['image'] ) . ');background-size:cover;background-position:center;'
									: 'background-color:' . esc_attr( $option['hex'] ) . ';';
								?>
								<button
									type="button"
									class="lt-variation-option lt-variation-option--swatch w-9 h-9 rounded-full border-2 overflow-hidden transition-all duration-150 <?php echo $is_default ? 'border-lt-brand' : 'border-transparent'; ?>"
									style="<?php echo esc_attr( $swatch_style ); ?>"
									data-attribute="<?php echo esc_attr( $group['taxonomy'] ); ?>"
									data-value="<?php echo esc_attr( $option['slug'] ); ?>"
									data-name="<?php echo esc_attr( $option['name'] ); ?>"
									aria-label="<?php echo esc_attr( $option['name'] ); ?>"
									title="<?php echo esc_attr( $option['name'] ); ?>"
								></button>
							<?php else : ?>
								<button
									type="button"
									class="lt-variation-option lt-variation-option--pill px-3 py-2 rounded-lg border text-caption-sm font-semibold transition-colors duration-150 <?php echo $is_default ? 'border-lt-brand text-lt-brand bg-lt-brand/5' : 'border-[#E9EAEC] text-lt-text-primary'; ?>"
									data-attribute="<?php echo esc_attr( $group['taxonomy'] ); ?>"
									data-value="<?php echo esc_attr( $option['slug'] ); ?>"
									data-name="<?php echo esc_attr( $option['name'] ); ?>"
								>
									<?php echo esc_html( $option['name'] ); ?>
								</button>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
			<p class="lt-variation-hint text-caption-sm text-lt-text-muted m-0 hidden" id="lt-variation-hint">
				<?php esc_html_e( 'Please select all options above.', 'local-tasker' ); ?>
			</p>
		</div>
	<?php endif; ?>

	<div class="h-px bg-[#E9EAEC] mb-5"></div>

	<!-- ── Choose Option ── (purchase/install choice — only meaningful when there's a box price to base it on) -->
	<?php if ( $has_box_price ) : ?>
		<?php get_template_part( 'woocommerce/partials/product-choose-option' ); ?>
	<?php endif; ?>

	<!-- ── Box Calculator ── -->
	<?php if ( $sold_by_box && $has_box_price ) : ?>
		<?php get_template_part( 'woocommerce/partials/product-calculator' ); ?>
	<?php else : ?>
		<!-- Simple add to cart for non-box products -->
		<?php woocommerce_template_single_add_to_cart(); ?>
	<?php endif; ?>


</div><!-- /.lt-product-summary -->
