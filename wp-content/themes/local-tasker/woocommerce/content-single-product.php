<?php
/**
 * Product single — full content: gallery + summary + calculator + details + related.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

global $product;
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<!-- ── Gallery + Summary ────────────────────────────────── -->
	<section class="lt-product-single__top bg-[#F7F6F4] py-8 md:py-12">
		<div class="container">
			<div class="flex flex-wrap lg:flex-nowrap gap-8 lg:gap-9">

				<!-- Gallery -->
				<div class="lt-product-gallery w-full lg:w-[45%] shrink-0">
					<?php get_template_part( 'woocommerce/partials/product-gallery' ); ?>
				</div>

				<!-- Summary -->
				<div class="lt-product-summary flex-1 min-w-0">
					<?php get_template_part( 'woocommerce/partials/product-summary' ); ?>
				</div>

			</div>
		</div>
	</section>

	<!-- ── Trust / USP strip ───────────────────────────────── -->
	<?php get_template_part( 'woocommerce/partials/product-trust-strip' ); ?>

	<!-- ── Area Calculator + How it works ───────────────────── -->
	<?php
	$_product_id  = $product->get_id();
	$_carton_sqm  = (float) get_post_meta( $_product_id, 'carton_sqm', true );
	$_sold_by_box = (bool)  get_post_meta( $_product_id, 'sold_by_box', true );
	if ( $_sold_by_box && $_carton_sqm > 0 ) :
	?>
	<section class="lt-product-calc-explainer bg-[#F7F6F4] py-10 md:py-14">
		<div class="container">
			<div class="flex flex-wrap lg:flex-nowrap gap-8 lg:gap-9 items-stretch">

				<!-- Area Calculator — card has its own built-in header/title -->
				<div class="w-full lg:flex-1 min-w-0">
					<?php get_template_part( 'woocommerce/partials/area-calculator' ); ?>
				</div>

				<!-- How it works — self-contained card with its own header -->
				<div class="w-full lg:flex-1 min-w-0">
					<?php get_template_part( 'woocommerce/partials/product-how-it-works' ); ?>
				</div>

			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- ── Details / Spec table ─────────────────────────────── -->
	<?php get_template_part( 'woocommerce/partials/product-details' ); ?>

	<!-- ── Related products ─────────────────────────────────── -->
	<?php
	$related_ids = wc_get_related_products( $product->get_id(), 8 );
	if ( ! empty( $related_ids ) ) {
		get_template_part( 'woocommerce/partials/product-related', null, [ 'ids' => $related_ids ] );
	}
	?>

	<?php do_action( 'woocommerce_after_single_product' ); ?>

</div>
