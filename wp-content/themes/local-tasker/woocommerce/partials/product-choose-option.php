<?php
/**
 * Installation quote opt-in.
 *
 * Replaces the former "Purchase Only / Purchase & Install" price table. The
 * customer now ASKS FOR A QUOTE rather than buying installation up front, so
 * this widget deliberately never touches the price — ticking it only sets a
 * flag that rides the cart line through to the order and the installation
 * notification email.
 *
 * The "Installation Rate" field is now purely the "do we install this product?"
 * switch: no rate, no offer, nothing to ask about.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

global $product;

$product_id      = $product->get_id();
$install_rate_ex = (float) get_post_meta( $product_id, 'install_rate_per_sqm', true );

if ( $install_rate_ex <= 0 ) {
	return;
}
?>

<div class="lt-install-quote mb-5" id="lt-install-quote">

	<div class="rounded-2xl border border-[#ccc] overflow-hidden">

		<!-- Opt-in -->
		<label class="lt-install-quote__row flex items-center gap-4 px-6 py-4 cursor-pointer select-none transition-colors duration-200 hover:bg-lt-snow-drift">
			<input
				type="checkbox"
				id="lt-install-quote-input"
				name="lt_install_quote"
				value="1"
				class="lt-install-quote__input sr-only peer"
			>
			<!-- Custom checkbox -->
			<span
				class="lt-install-quote__ui w-4 h-4 rounded border border-[#b3b3b3] flex items-center justify-center shrink-0 bg-lt-white peer-checked:bg-lt-brand peer-checked:border-lt-brand transition-colors duration-150"
				aria-hidden="true"
			>
				<svg class="w-3 h-3" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M2 6l3 3 5-5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</span>
			<span class="font-semi-ext font-bold text-caption-md leading-[1.5] text-[#0d0d0d]">
				<?php esc_html_e( 'I would also like an installation quote', 'local-tasker' ); ?>
			</span>
		</label>

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

</div><!-- /.lt-install-quote -->
