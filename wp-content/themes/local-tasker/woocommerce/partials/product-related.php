<?php
/**
 * "You May Also Like" related products slider.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

$related_ids = $args['ids'] ?? [];
if ( empty( $related_ids ) ) {
	return;
}

$related_products = array_filter( array_map( 'wc_get_product', $related_ids ) );
if ( empty( $related_products ) ) {
	return;
}
?>

<section class="lt-related-products bg-lt-white-lilac py-10 md:py-14" aria-label="<?php esc_attr_e( 'Related products', 'local-tasker' ); ?>">
	<div class="container">

		<div class="flex items-center justify-between gap-4 mb-8">
			<h2 class="text-h4 font-bold font-semi-ext text-lt-text-primary m-0">
				<?php esc_html_e( 'You May Also Like', 'local-tasker' ); ?>
			</h2>
			<!-- Slider arrows -->
			<div class="flex gap-2 shrink-0">
				<button
					type="button"
					class="lt-related-prev flex items-center justify-center w-10 h-10 rounded-full border border-[#E9EAEC] bg-lt-white hover:border-lt-brand transition-colors duration-200"
					aria-label="<?php esc_attr_e( 'Previous products', 'local-tasker' ); ?>"
				>
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<button
					type="button"
					class="lt-related-next flex items-center justify-center w-10 h-10 rounded-full border border-[#E9EAEC] bg-lt-white hover:border-lt-brand transition-colors duration-200"
					aria-label="<?php esc_attr_e( 'Next products', 'local-tasker' ); ?>"
				>
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
			</div>
		</div>

		<!-- Swiper -->
		<div class="lt-related-slider swiper" id="lt-related-slider">
			<div class="swiper-wrapper">
				<?php foreach ( $related_products as $related ) :
					$rid         = $related->get_id();
					$carton_sqm  = (float) get_post_meta( $rid, 'carton_sqm', true );
					$price_ex    = (float) $related->get_price();
					$reg_ex      = (float) $related->get_regular_price();
					$ppsm_ex     = ( $carton_sqm > 0 ) ? $price_ex / $carton_sqm : 0;
					$reg_ppsm_ex = ( $carton_sqm > 0 ) ? $reg_ex / $carton_sqm : 0;
					$ppsm_inc    = $ppsm_ex * 1.10;
					$on_sale     = $related->is_on_sale();
				?>
					<div class="swiper-slide">
						<article <?php echo wc_product_class( 'lt-product-card group relative flex flex-col bg-lt-white rounded-2xl overflow-hidden transition-shadow duration-300 hover:shadow-xl h-full', $related ); ?>>

							<a href="<?php echo esc_url( get_permalink( $rid ) ); ?>" class="relative block overflow-hidden bg-lt-white-lilac" style="aspect-ratio:4/3" tabindex="-1" aria-hidden="true">
								<?php echo get_the_post_thumbnail( $rid, 'woocommerce_single', [ 'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105' ] ); ?>
								<?php if ( $on_sale ) : ?>
									<span class="absolute top-3 left-3 bg-[#E02020] text-lt-white text-caption-xs font-bold uppercase tracking-[0.08em] px-2 py-[5px] rounded"><?php esc_html_e( 'SALE', 'local-tasker' ); ?></span>
								<?php endif; ?>
							</a>

							<div class="flex flex-col flex-1 p-4 gap-[6px]">
								<h3 class="text-caption-md font-semibold leading-[1.35] text-lt-text-primary line-clamp-2 m-0">
									<a href="<?php echo esc_url( get_permalink( $rid ) ); ?>" class="hover:text-lt-brand transition-colors duration-200">
										<?php echo esc_html( $related->get_name() ); ?>
									</a>
								</h3>
								<?php if ( $related->is_type( 'variable' ) ) : ?>
									<p class="text-caption-sm text-lt-brand font-medium leading-none m-0"><?php esc_html_e( '+ more options', 'local-tasker' ); ?></p>
								<?php endif; ?>
								<div class="mt-auto pt-1">
									<?php if ( $carton_sqm > 0 ) : ?>
										<div class="flex flex-wrap items-baseline gap-x-[6px]">
											<?php if ( $on_sale && $reg_ppsm_ex > 0 ) : ?>
												<span class="text-caption-md text-lt-text-muted line-through"><?php echo wc_price( $reg_ppsm_ex ); ?></span>
											<?php endif; ?>
											<span class="text-body font-bold text-lt-text-primary"><?php echo wc_price( $ppsm_ex ); ?></span>
											<span class="text-caption-sm text-lt-text-muted">/ sqm</span>
										</div>
										<p class="text-caption-xs text-lt-text-muted mt-[3px] m-0"><?php printf( esc_html__( '(incl. GST %s)', 'local-tasker' ), wc_price( $ppsm_inc ) ); ?></p>
									<?php endif; ?>
								</div>
								<a
									href="<?php echo esc_url( $related->add_to_cart_url() ); ?>"
									class="btn btn--brand w-full mt-2 text-center"
									aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to cart', 'local-tasker' ), $related->get_name() ) ); ?>"
								>
									<?php esc_html_e( 'Add to Cart', 'local-tasker' ); ?>
								</a>
							</div>

						</article>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

	</div>
</section>
