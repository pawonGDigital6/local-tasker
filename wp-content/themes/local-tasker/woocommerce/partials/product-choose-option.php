<?php
/**
 * Choose Option — Purchase Only vs Purchase & Install.
 * Selecting a row updates the effective unit price in the calculator via JS data attrs.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

global $product;

$product_id      = $product->get_id();
$carton_sqm      = (float) get_post_meta( $product_id, 'carton_sqm', true );
$has_price       = '' !== $product->get_price();
$current_price_ex = (float) $product->get_price();
$install_rate_ex = (float) get_post_meta( $product_id, 'install_rate_per_sqm', true );
$has_install     = $install_rate_ex > 0;

// This widget only makes sense when there's a real price and a box to purchase — bail rather
// than render a fabricated "$0.00 / sqm" for products with no price or no box coverage.
if ( ! $has_price || $carton_sqm <= 0 ) {
	return;
}

// _price is already the $/sqm figure — no conversion needed to show it.
$price_per_sqm_ex         = $current_price_ex;
$install_price_per_sqm_ex = $has_install ? ( $price_per_sqm_ex + $install_rate_ex ) : 0;
?>

<div class="lt-choose-option mb-5" id="lt-choose-option">

	<div class="rounded-2xl border border-[#ccc] overflow-hidden">

		<!-- Head -->
		<div class="flex items-center justify-between gap-4 px-6 py-4 bg-[#e8e8e8] font-semi-ext font-bold text-base leading-[1.5] text-[#0d0d0d]">
			<span><?php esc_html_e( 'Choose Option', 'local-tasker' ); ?></span>
			<span><?php esc_html_e( 'Price', 'local-tasker' ); ?></span>
		</div>

		<!-- Purchase Only -->
		<label class="lt-choose-option__row flex items-center justify-between gap-4 px-6 py-4 border-t border-[#ccc] cursor-pointer transition-colors duration-200 hover:bg-lt-snow-drift">
			<span class="flex items-center gap-4">
				<input
					type="radio"
					name="lt_purchase_option"
					value="purchase-only"
					class="lt-option-radio sr-only peer"
					checked
					data-price-per-sqm-ex="<?php echo esc_attr( $price_per_sqm_ex ); ?>"
					aria-label="<?php esc_attr_e( 'Purchase Only', 'local-tasker' ); ?>"
				>
				<!-- Custom radio -->
				<span class="lt-option-radio__ui w-4 h-4 rounded-full border border-[#b3b3b3] flex items-center justify-center shrink-0 bg-lt-white peer-checked:border-lt-brand peer-checked:bg-lt-brand transition-colors duration-150" aria-hidden="true">
					<span class="w-1.5 h-1.5 rounded-full bg-lt-white"></span>
				</span>
				<span class="font-semi-ext font-bold text-caption-md leading-[1.5] text-[#0d0d0d]"><?php esc_html_e( 'Purchase Only', 'local-tasker' ); ?></span>
			</span>
			<span class="text-body-lg leading-[1.5] text-[#7f7f7f] shrink-0">
				<span id="lt-option-price-purchase-only"><?php echo wp_kses_post( wc_price( $price_per_sqm_ex ) ); ?></span><span class="text-caption-sm"> / sqm</span>
			</span>
		</label>

		<!-- Purchase & Install -->
		<?php if ( $has_install ) : ?>
			<label class="lt-choose-option__row flex items-center justify-between gap-4 px-6 py-4 border-t border-[#ccc] cursor-pointer transition-colors duration-200 hover:bg-lt-snow-drift">
				<span class="flex items-center gap-4">
					<input
						type="radio"
						name="lt_purchase_option"
						value="purchase-install"
						class="lt-option-radio sr-only peer"
						data-price-per-sqm-ex="<?php echo esc_attr( $install_price_per_sqm_ex ); ?>"
						aria-label="<?php esc_attr_e( 'Purchase and Install', 'local-tasker' ); ?>"
					>
					<span class="lt-option-radio__ui w-4 h-4 rounded-full border border-[#b3b3b3] flex items-center justify-center shrink-0 bg-lt-white peer-checked:border-lt-brand peer-checked:bg-lt-brand transition-colors duration-150" aria-hidden="true">
						<span class="w-1.5 h-1.5 rounded-full bg-lt-white"></span>
					</span>
					<span class="font-semi-ext font-bold text-caption-md leading-[1.5] text-[#0d0d0d]"><?php esc_html_e( 'Purchase & Install', 'local-tasker' ); ?></span>
				</span>
				<span class="text-body-lg leading-[1.5] text-[#7f7f7f] shrink-0">
					<span id="lt-option-price-purchase-install"><?php echo wp_kses_post( wc_price( $install_price_per_sqm_ex ) ); ?></span><span class="text-caption-sm"> / sqm</span>
				</span>
			</label>
		<?php endif; ?>

		<!-- Need more info -->
		<div class="px-6 py-4 bg-[#e8e8e8] border-t border-[#ccc] text-center">
			<p class="m-0 text-body-lg font-semibold leading-[1.5] text-[#0d0d0d]">
				<?php
				printf(
					/* translators: %s: phone link */
					esc_html__( 'Need More Information? %s', 'local-tasker' ),
					'<a href="tel:' . esc_attr( get_option( 'woocommerce_store_phone', '' ) ) . '" class="text-lt-accent underline hover:no-underline">' . esc_html__( 'Call Us', 'local-tasker' ) . '</a>'
				);
				?>
			</p>
		</div>

	</div>

</div><!-- /.lt-choose-option -->
