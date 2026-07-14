<?php
/**
 * WooCommerce Shop Loop placeholder block.
 *
 * On the frontend, archive-product.php handles all WC output — this block is
 * purely a positional marker in the block editor. Nothing is rendered here
 * outside of a REST/editor context.
 *
 * @package local-tasker
 */

// Only render the placeholder in the block editor preview.
if ( ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
	return;
}
?>
<div class="lt-wc-shop-loop-placeholder">
	<div class="lt-wc-shop-loop-placeholder__icon">🛍️</div>
	<p class="lt-wc-shop-loop-placeholder__title">WooCommerce Shop Loop</p>
	<p class="lt-wc-shop-loop-placeholder__desc">
		The product filter sidebar and product grid will render here on the Shop page.<br>
		Add ACF blocks above or below this block to control page layout.
	</p>
</div>
