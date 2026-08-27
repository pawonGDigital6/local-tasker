<?php
/**
 * Installation opt-in.
 *
 * Replaces the former "Purchase Only / Purchase & Install" price table. The
 * customer now ASKS FOR A QUOTE rather than buying installation up front, so
 * this widget deliberately never touches the price — ticking it only sets a
 * flag that rides the cart line through to the order and the installation
 * notification email.
 *
 * There is no installation cost to configure. The only per-product setting is
 * the "Installation Quote" toggle (Product Options & Specs), which defaults to
 * ON — when it is off the option is hidden entirely for that product.
 *
 * Self-gating, and it prints its own leading divider, so both render paths (the
 * calculator branch in product-summary.php and lt_render_install_optin() inside
 * the stock add-to-cart form) can call it unconditionally without leaving a
 * stray separator behind when the toggle is off.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product || ! lt_product_offers_install_quote( $product->get_id() ) ) {
	return;
}
?>

<div class="h-px bg-[#E9EAEC] mb-5"></div>

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
					'<a href="tel:+61373020482' . esc_attr( get_option( 'woocommerce_store_phone', '' ) ) . '" class="text-lt-accent underline hover:no-underline">' . esc_html__( 'Call Us', 'local-tasker' ) . '</a>'
				);
				?>
			</p>
		</div>

	</div>

</div><!-- /.lt-install-quote -->
