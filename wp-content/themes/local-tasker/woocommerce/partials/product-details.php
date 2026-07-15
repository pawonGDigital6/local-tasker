<?php
/**
 * Product details / spec table.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

global $product;

$product_id = $product->get_id();
$has_price  = '' !== $product->get_price();

// _price is already the $/sqm figure — no conversion needed to show it.
$price_per_sqm_ex  = $has_price ? (float) $product->get_price() : 0;
$price_per_sqm_inc = $price_per_sqm_ex * 1.10;

if ( $has_price ) {
	$price_row = wc_price( $price_per_sqm_ex ) . ' <span class="text-lt-text-muted font-normal text-caption-xs">ex GST</span> <span class="text-lt-text-muted font-normal text-caption-xs">' . sprintf( __( '(incl. GST %s)', 'local-tasker' ), wc_price( $price_per_sqm_inc ) ) . '</span>';
} else {
	$price_row = esc_html__( 'Price on request', 'local-tasker' );
}

$spec_fields = [
	'Price'                 => $price_row,
	'Dimension'             => get_post_meta( $product_id, 'spec_dimension', true ),
	'Veneer'                => get_post_meta( $product_id, 'spec_veneer', true ),
	'Finish'                => get_post_meta( $product_id, 'spec_finish', true ),
	'Packaging QTY'         => get_post_meta( $product_id, 'spec_packaging_qty', true ),
	'Packaging Weight'      => get_post_meta( $product_id, 'spec_packaging_weight', true ),
	'Bevel'                 => get_post_meta( $product_id, 'spec_bevel', true ),
	'Profile'               => get_post_meta( $product_id, 'spec_profile', true ),
	'Formaldehyde Emission' => get_post_meta( $product_id, 'spec_formaldehyde', true ),
	'Sustainability'        => get_post_meta( $product_id, 'spec_sustainability', true ),
	'Warranty'              => get_post_meta( $product_id, 'spec_warranty', true ),
];

$spec_fields = array_filter( $spec_fields, fn( $v ) => $v !== '' && $v !== false && $v !== null );

if ( empty( $spec_fields ) ) {
	return;
}
?>

<section class="lt-product-details bg-lt-white py-10 md:py-14" aria-label="<?php esc_attr_e( 'Product details', 'local-tasker' ); ?>">
	<div class="container">

		<h2 class="text-h4 font-bold font-semi-ext text-lt-text-primary mb-8 text-center">
			<?php esc_html_e( 'Details', 'local-tasker' ); ?>
		</h2>

		<div class="max-w-[860px] mx-auto">
			<table class="lt-spec-table w-full border-collapse" role="table">
				<tbody>
					<?php foreach ( $spec_fields as $label => $value ) : ?>
						<tr class="lt-spec-table__row border-b border-[#E9EAEC] last:border-0">
							<th
								scope="row"
								class="lt-spec-table__label text-caption-md text-lt-text-muted font-normal text-left py-4 pr-6 w-[36%] align-top"
							>
								<?php echo esc_html( $label ); ?>
							</th>
							<td class="lt-spec-table__value text-caption-md text-lt-text-primary font-medium py-4 align-top">
								<?php echo wp_kses_post( $value ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

	</div>
</section>
