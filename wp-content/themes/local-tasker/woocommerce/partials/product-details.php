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

<section class="lt-product-details bg-lt-white py-12 md:py-20" aria-label="<?php esc_attr_e( 'Product details', 'local-tasker' ); ?>">
	<div class="container">

		<div class="lt-spec-table max-w-[1300px] mx-auto rounded-2xl border border-[#cccccc] overflow-hidden">

			<!-- Header -->
			<div class="text-center px-6 py-5 bg-[#E8E8E8] border-b border-[#cccccc]">
				<h2 class="text-body font-semibold font-semi-ext text-lt-text-primary m-0 leading-none relative top-[2px]">
					<?php esc_html_e( 'Details', 'local-tasker' ); ?>
				</h2>
			</div>

			<!-- Spec table (from the product short description WYSIWYG) -->
			<div class="lt-spec-table__body">
				<?php echo wp_kses_post( $excerpt ); ?>
			</div>
			<div class="table-foot table-foot text-center px-6 py-5 bg-[#E8E8E8] h-[57px]"></div>

		</div>

	</div>
</section>
