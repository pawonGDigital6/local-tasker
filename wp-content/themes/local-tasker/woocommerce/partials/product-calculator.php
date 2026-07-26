<?php
/**
 * Box calculator — "How much flooring do you need?"
 *
 * @package local-tasker
 */

defined('ABSPATH') || exit;

global $product;

$product_id = $product->get_id();
$nonce = wp_create_nonce('lt_add_to_cart_' . $product_id);
?>
<div class="lt-calculator" id="lt-calculator"
	aria-label="<?php esc_attr_e('Flooring quantity calculator', 'local-tasker'); ?>">
	<h2 class="text-caption-sm font-bold text-lt-text-primary mb-4 font-semi-ext">
		<?php esc_html_e('How much flooring do you need?', 'local-tasker'); ?>
	</h2>
	<!-- sqm input row -->
	<div class="flex items-center gap-3 mb-4">
		<!-- Stepper -->
		<div class="lt-stepper flex items-center border border-[#E9EAEC] rounded-xl overflow-hidden bg-lt-white"
			role="group" aria-label="<?php esc_attr_e('Square metres', 'local-tasker'); ?>">
			<button type="button" id="lt-calc-minus"
				class="lt-stepper__btn w-11 h-11 flex items-center justify-center text-lt-text-secondary hover:bg-lt-snow-drift transition-colors duration-150 shrink-0"
				aria-label="<?php esc_attr_e('Decrease square metres', 'local-tasker'); ?>">
				<svg width="16" height="2" viewBox="0 0 16 2" fill="none" xmlns="http://www.w3.org/2000/svg"
					aria-hidden="true">
					<path d="M1 1h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
				</svg>
			</button>
			<label for="lt-calc-area"
				class="sr-only"><?php esc_html_e('Area in square metres', 'local-tasker'); ?></label>
			<input type="number" id="lt-calc-area"
				class="lt-stepper__input w-16 text-center border-x border-[#E9EAEC] h-11 text-body font-bold text-lt-text-primary bg-transparent focus:outline-none"
				value="0" min="0" step="1" inputmode="decimal" aria-live="polite">
			<button type="button" id="lt-calc-plus"
				class="lt-stepper__btn w-11 h-11 flex items-center justify-center text-lt-text-secondary hover:bg-lt-snow-drift transition-colors duration-150 shrink-0"
				aria-label="<?php esc_attr_e('Increase square metres', 'local-tasker'); ?>">
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"
					aria-hidden="true">
					<path d="M8 1v14M1 8h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
				</svg>
			</button>
		</div>
		<span class="text-caption-sm text-lt-text-muted shrink-0"><?php esc_html_e('sqm', 'local-tasker'); ?></span>
		<!-- Area calculator anchor link -->
		<a href="#lt-area-calculator"
			class="ml-auto text-caption-sm text-lt-brand font-semibold hover:underline flex items-center gap-1 shrink-0"
			aria-label="<?php esc_attr_e('Jump to area calculator', 'local-tasker'); ?>">
			<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
				<g clip-path="url(#clip0_50512_7708)">
					<path
						d="M9 1H3C2.44772 1 2 1.44772 2 2V10C2 10.5523 2.44772 11 3 11H9C9.55228 11 10 10.5523 10 10V2C10 1.44772 9.55228 1 9 1Z"
						stroke="#0A65FC" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M4 3H8" stroke="#0A65FC" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M8 7V9" stroke="#0A65FC" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M8 5H8.005" stroke="#0A65FC" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M6 5H6.005" stroke="#0A65FC" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M4 5H4.005" stroke="#0A65FC" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M6 7H6.005" stroke="#0A65FC" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M4 7H4.005" stroke="#0A65FC" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M6 9H6.005" stroke="#0A65FC" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M4 9H4.005" stroke="#0A65FC" stroke-linecap="round" stroke-linejoin="round" />
				</g>
				<defs>
					<clipPath id="clip0_50512_7708">
						<rect width="12" height="12" fill="white" />
					</clipPath>
				</defs>
			</svg>
			<?php esc_html_e('Area calculator ↓', 'local-tasker'); ?>
		</a>
	</div>
	<!-- Checkboxes -->
	<div class="flex flex-col gap-[10px] mb-4">
		<label class="flex items-start gap-3 cursor-pointer select-none">
			<input type="checkbox" id="lt-calc-round-up" class="sr-only peer" checked>
			<span
				class="lt-checkbox__ui mt-[2px] w-4 h-4 bg-white rounded border border-[#D1D5DB] flex items-center justify-center shrink-0 peer-checked:bg-lt-brand peer-checked:border-lt-brand transition-colors duration-150"
				aria-hidden="true">
				<svg class="w-3 h-3 text-lt-white" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M2 6l3 3 5-5" stroke="white" stroke-width="1.5" stroke-linecap="round"
						stroke-linejoin="round" />
				</svg>
			</span>
			<span class="text-caption-sm text-lt-text-secondary leading-[1.4]">
				<?php esc_html_e("We'll round up to full boxes", 'local-tasker'); ?>
			</span>
		</label>
		<label class="flex items-start gap-3 cursor-pointer select-none">
			<input type="checkbox" id="lt-calc-wastage" class="sr-only peer">
			<span
				class="lt-checkbox__ui mt-[2px] w-4 h-4 bg-white rounded border border-[#D1D5DB] flex items-center justify-center shrink-0 peer-checked:bg-lt-brand peer-checked:border-lt-brand transition-colors duration-150"
				aria-hidden="true">
				<svg class="w-3 h-3 text-lt-white" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M2 6l3 3 5-5" stroke="white" stroke-width="1.5" stroke-linecap="round"
						stroke-linejoin="round" />
				</svg>
			</span>
			<span class="text-caption-sm text-lt-text-secondary leading-[1.4]">
				<?php esc_html_e('We recommend adding 10% for wastage', 'local-tasker'); ?>
			</span>
		</label>
	</div>
	<!-- Wastage confirmation banner -->
	<div id="lt-wastage-banner"
		class="hidden items-center gap-2 bg-lt-accent/8 border border-lt-accent/20 rounded-lg px-4 py-3 mb-4"
		role="status" aria-live="polite">
		<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
			class="text-lt-accent shrink-0">
			<circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.3" />
			<path d="M8 5v3.5M8 10.5v.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
		</svg>
		<span class="text-caption-sm text-lt-accent font-medium">
			<?php esc_html_e('10% wastage has been included in your calculation.', 'local-tasker'); ?>
		</span>
	</div>
	<!-- Result cards -->
	<div class="grid grid-cols-3 gap-3 mb-5" role="region"
		aria-label="<?php esc_attr_e('Calculator results', 'local-tasker'); ?>">
		<div
			class="lt-calc-result flex flex-col items-center text-center bg-lt-white border border-[#E9EAEC] rounded-lg px-3 py-3 gap-1">
			<span
				class="text-caption-xs text-lt-text-muted leading-[1.3]"><?php esc_html_e('Coverage approx.', 'local-tasker'); ?></span>
			<span class="text-body-lg font-bold text-lt-text-primary leading-none" id="lt-result-coverage"
				aria-live="polite">0</span>
			<span class="text-caption-xs text-lt-text-muted"><?php esc_html_e('sqm', 'local-tasker'); ?></span>
		</div>
		<div
			class="lt-calc-result flex flex-col items-center text-center bg-lt-white border border-[#E9EAEC] rounded-lg px-3 py-3 gap-1">
			<span
				class="text-caption-xs text-lt-text-muted leading-[1.3]"><?php esc_html_e('Boxes approx.', 'local-tasker'); ?></span>
			<span class="text-body-lg font-bold text-lt-text-primary leading-none" id="lt-result-boxes"
				aria-live="polite">0</span>
			<span class="text-caption-xs text-lt-text-muted"><?php esc_html_e('boxes', 'local-tasker'); ?></span>
		</div>
		<div
			class="lt-calc-result flex flex-col items-center text-center bg-lt-white border border-[#E9EAEC] rounded-lg px-3 py-3 gap-1">
			<span
				class="text-caption-xs text-lt-text-muted leading-[1.3]"><?php esc_html_e('Total incl. GST', 'local-tasker'); ?></span>
			<span class="text-body-lg font-bold text-lt-brand leading-none" id="lt-result-total"
				aria-live="polite">$0.00</span>
		</div>
	</div>
	<!-- Actions -->
	<div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
		<button type="button" id="lt-add-to-cart"
			class="btn btn--brand flex-1 py-[14px]! disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer transition-colors duration-75"
			data-product-id="<?php echo esc_attr($product_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"
			disabled aria-label="<?php esc_attr_e('Add to cart', 'local-tasker'); ?>">
			<?php esc_html_e('Add to Cart', 'local-tasker'); ?>
		</button>
		<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" id="lt-checkout-btn"
			class="btn bg-transparent flex-1 py-[14px]! border border-[#A5A5A5] text-center hidden hover:bg-lt-brand hover:text-lt-white hover:border-lt-brand transition-colors duration-75"
			aria-label="<?php esc_attr_e('Proceed to checkout', 'local-tasker'); ?>">
			<?php esc_html_e('Proceed to Checkout', 'local-tasker'); ?>
		</a>
	</div>
</div><!-- /.lt-calculator -->