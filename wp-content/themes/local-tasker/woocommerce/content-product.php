<?php
/**
 * Product card — used in shop archive loop and related products.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

global $product;

$product_id  = get_the_ID();
$carton_sqm  = (float) get_post_meta( $product_id, 'carton_sqm', true );
$is_variable = $product->is_type( 'variable' );
$is_on_sale  = $product->is_on_sale();

// Per-sqm prices (ex GST). Box price / carton size.
$price_ex         = ( $carton_sqm > 0 ) ? (float) $product->get_price() / $carton_sqm : 0;
$regular_price_ex = ( $carton_sqm > 0 ) ? (float) $product->get_regular_price() / $carton_sqm : 0;
$price_inc        = $price_ex * 1.10;
?>
<li
	id="product-<?php the_ID(); ?>"
	<?php wc_product_class( 'lt-product-card group relative flex flex-col gap-3 pb-1', $product ); ?>
>

	<!-- Image + Badge -->
	<a
		href="<?php the_permalink(); ?>"
		class="lt-product-card__media relative block aspect-square overflow-hidden bg-lt-snow-drift rounded-2xl wd:rounded-lg"
		tabindex="-1"
		aria-hidden="true"
	>
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'woocommerce_single', [ 'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105' ] ); ?>
		<?php else : ?>
			<?php echo wc_placeholder_img( 'woocommerce_single', [ 'class' => 'w-full h-full object-cover' ] ); ?>
		<?php endif; ?>

		<?php if ( $is_on_sale ) : ?>
			<span class="lt-product-card__badge absolute top-2 left-2 z-10 bg-[#ff1c1c] text-lt-white text-[12px] font-semibold tracking-[0.48px] leading-4 px-2 py-1 rounded-[4px]" aria-label="<?php esc_attr_e( 'Sale', 'local-tasker' ); ?>">
				<?php esc_html_e( 'SALE', 'local-tasker' ); ?>
			</span>
		<?php endif; ?>
	</a>

	<!-- Content -->
	<div class="lt-product-card__content flex flex-col flex-1 gap-3">

		<!-- Title -->
		<h3 class="lt-product-card__title text-base leading-[22px] font-bold text-lt-text-primary line-clamp-2 m-0">
			<a
				href="<?php the_permalink(); ?>"
				class="hover:text-lt-brand transition-colors duration-200"
			>
				<?php the_title(); ?>
			</a>
		</h3>

		<!-- Variable: colour swatches + more options indicator -->
		<?php if ( $is_variable ) : ?>
			<?php $swatch_data = lt_get_product_swatches( $product ); ?>
			<div class="lt-product-card__swatches flex items-center gap-1.5">
				<?php foreach ( $swatch_data['swatches'] as $swatch ) : ?>
					<span
						class="lt-swatch inline-block w-2.5 h-2.5 shrink-0 overflow-hidden rounded-full"
						title="<?php echo esc_attr( $swatch['name'] ); ?>"
						aria-hidden="true"
					>
						<img
							src="<?php echo esc_url( $swatch['image'] ); ?>"
							alt="<?php echo esc_attr( $swatch['name'] ); ?>"
							class="w-full h-full object-cover"
							loading="lazy"
						/>
					</span>
				<?php endforeach; ?>
				<p class="lt-product-card__options text-[12px] leading-4 tracking-[0.24px] text-lt-text-muted font-normal m-0">
					<?php esc_html_e( '+ more options', 'local-tasker' ); ?>
				</p>
			</div>
		<?php endif; ?>

		<!-- Price block -->
		<div class="lt-product-card__price mt-auto">
			<?php if ( $carton_sqm > 0 ) : ?>
				<div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
					<?php if ( $is_on_sale && $regular_price_ex > 0 ) : ?>
						<span class="text-caption-md text-lt-text-muted line-through leading-none">
							<?php echo wc_price( $regular_price_ex ); ?>
						</span>
					<?php endif; ?>
					<span class="text-lg font-semibold text-lt-text-primary leading-[21px]">
						<?php echo wc_price( $price_ex ); ?> / sqm
					</span>
				</div>
				<p class="text-caption-xs text-lt-text-muted mt-[3px] m-0">
					<?php
					printf(
						/* translators: %s: GST-inclusive price */
						esc_html__( '(incl. GST %s)', 'local-tasker' ),
						wc_price( $price_inc )
					);
					?>
				</p>
			<?php else : ?>
				<span class="text-caption-sm text-lt-text-muted"><?php esc_html_e( 'Price on request', 'local-tasker' ); ?></span>
			<?php endif; ?>
		</div>

		<!-- Add to Cart -->
		<?php if ( $is_variable ) : ?>
			<a
				href="<?php the_permalink(); ?>"
				class="btn btn--brand w-full mt-2 text-center"
				aria-label="<?php echo esc_attr( sprintf( __( 'Select options for %s', 'local-tasker' ), get_the_title() ) ); ?>"
			>
				<?php esc_html_e( 'Select Options', 'local-tasker' ); ?>
			</a>
		<?php else : ?>
			<a
				href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
				data-product-id="<?php echo esc_attr( $product_id ); ?>"
				data-product-type="simple"
				data-quantity="1"
				class="btn btn--brand w-full mt-2 ajax_add_to_cart add_to_cart_button text-center"
				rel="nofollow"
				aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to cart', 'local-tasker' ), get_the_title() ) ); ?>"
			>
				<?php esc_html_e( 'Add to Cart', 'local-tasker' ); ?>
			</a>
		<?php endif; ?>

	</div><!-- /.lt-product-card__content -->

</li>
