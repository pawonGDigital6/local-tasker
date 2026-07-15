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

	<div class="grid grid-cols-[1fr_auto] text-caption-xs font-semibold uppercase tracking-[0.07em] text-lt-text-muted px-1 mb-2">
		<span><?php esc_html_e( 'Choose Option', 'local-tasker' ); ?></span>
		<span><?php esc_html_e( 'Price', 'local-tasker' ); ?></span>
	</div>

	<!-- Purchase Only -->
	<label class="lt-choose-option__row flex items-center justify-between gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 border-lt-brand bg-lt-brand/5 mb-2">
		<span class="flex items-center gap-3">
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
			<span class="lt-option-radio__ui w-[18px] h-[18px] rounded-full border-2 border-lt-brand flex items-center justify-center shrink-0 bg-lt-white peer-checked:bg-lt-brand transition-colors duration-150" aria-hidden="true">
				<span class="w-2 h-2 rounded-full bg-lt-white"></span>
			</span>
			<span class="text-caption-md font-semibold text-lt-text-primary"><?php esc_html_e( 'Purchase Only', 'local-tasker' ); ?></span>
		</span>
		<span class="text-body font-bold text-lt-text-primary shrink-0">
			<span id="lt-option-price-purchase-only"><?php echo wp_kses_post( wc_price( $price_per_sqm_ex ) ); ?></span><span class="text-caption-sm font-normal text-lt-text-muted"> / sqm</span>
		</span>
	</label>

	<!-- Purchase & Install -->
	<?php if ( $has_install ) : ?>
		<label class="lt-choose-option__row flex items-center justify-between gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 border-[#E9EAEC] hover:border-lt-brand/40">
			<span class="flex items-center gap-3">
				<input
					type="radio"
					name="lt_purchase_option"
					value="purchase-install"
					class="lt-option-radio sr-only peer"
					data-price-per-sqm-ex="<?php echo esc_attr( $install_price_per_sqm_ex ); ?>"
					aria-label="<?php esc_attr_e( 'Purchase and Install', 'local-tasker' ); ?>"
				>
				<span class="lt-option-radio__ui w-[18px] h-[18px] rounded-full border-2 border-[#D1D5DB] flex items-center justify-center shrink-0 bg-lt-white peer-checked:border-lt-brand peer-checked:bg-lt-brand transition-colors duration-150" aria-hidden="true">
					<span class="w-2 h-2 rounded-full bg-lt-white"></span>
				</span>
				<span class="text-caption-md font-semibold text-lt-text-primary"><?php esc_html_e( 'Purchase & Install', 'local-tasker' ); ?></span>
			</span>
			<span class="text-body font-bold text-lt-text-primary shrink-0">
				<span id="lt-option-price-purchase-install"><?php echo wp_kses_post( wc_price( $install_price_per_sqm_ex ) ); ?></span><span class="text-caption-sm font-normal text-lt-text-muted"> / sqm</span>
			</span>
		</label>
	<?php endif; ?>

	<!-- Need more info -->
	<p class="text-caption-sm text-lt-text-muted text-center mt-3 m-0">
		<?php
		printf(
			/* translators: %s: phone link */
			esc_html__( 'Need More Information? %s', 'local-tasker' ),
			'<a href="tel:' . esc_attr( get_option( 'woocommerce_store_phone', '' ) ) . '" class="text-lt-brand font-semibold hover:underline">' . esc_html__( 'Call Us', 'local-tasker' ) . '</a>'
		);
		?>
	</p>

</div><!-- /.lt-choose-option -->
