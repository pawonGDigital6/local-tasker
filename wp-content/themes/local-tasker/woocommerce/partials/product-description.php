<?php
/**
 * Product description — the product's main editor content, in its own card.
 *
 * This is post_content, NOT the short description: the spec table directly
 * below is built from post_excerpt and is rendered by product-details.php.
 * The two are separate fields and neither one falls back to the other.
 *
 * The card chrome is copied from .lt-spec-table so the pair reads as one
 * family — same 1px #cccccc border, same radius, same grey header bar. Only
 * the body typography differs, and that lives in assets/css/lt-single-product.css,
 * because this content is free-form editor HTML rather than the fixed
 * two-column table the spec card renders.
 *
 * Self-gating: products with no description print nothing at all, so the 88
 * products still waiting on copy show the page exactly as they do today.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$lt_description = (string) $product->get_description();

if ( '' === trim( wp_strip_all_tags( $lt_description ) ) ) {
	return;
}
?>

<section class="lt-product-description bg-lt-white" aria-label="<?php esc_attr_e( 'Product description', 'local-tasker' ); ?>">
	<div class="container">

		<div class="lt-description-card max-w-[1300px] mx-auto rounded-2xl border border-[#cccccc] overflow-hidden">

			<!-- Header — matches the Details card above it -->
			<div class="text-center px-6 py-5 bg-[#E8E8E8] border-b border-[#cccccc]">
				<h2 class="text-body font-semibold font-semi-ext text-lt-text-primary m-0 leading-none relative top-[2px]">
					<?php esc_html_e( 'Product Description', 'local-tasker' ); ?>
				</h2>
			</div>

			<!-- Body — editor content, migrated as written. wpautop() restores the
			     paragraph breaks the editor implies; the_content filters are
			     deliberately not run so no plugin can inject anything here. -->
			<div class="lt-product-description__body">
				<?php echo wp_kses_post( wpautop( $lt_description ) ); ?>
			</div>

		</div>

	</div>
</section>
