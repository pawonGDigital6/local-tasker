<?php
/**
 * Product details — just the product's short description (excerpt), as-is.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

global $product;

$excerpt = (string) $product->get_short_description();

if ( '' === trim( wp_strip_all_tags( $excerpt ) ) ) {
	return;
}
?>

<section class="lt-product-details bg-lt-white py-10 md:py-14" aria-label="<?php esc_attr_e( 'Product details', 'local-tasker' ); ?>">
	<div class="container">

		<h2 class="text-h4 font-bold font-semi-ext text-lt-text-primary mb-8 text-center">
			<?php esc_html_e( 'Details', 'local-tasker' ); ?>
		</h2>

		<div class="max-w-[860px] mx-auto lt-spec-summary text-caption-md text-lt-text-secondary leading-[1.6]">
			<?php echo wp_kses_post( wpautop( $excerpt ) ); ?>
		</div>

	</div>
</section>
