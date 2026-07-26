<?php
/**
 * Area calculator — collapsible panel that feeds into the main calculator.
 *
 * @package local-tasker
 */

defined('ABSPATH') || exit;

// Shared grid track: three flexible input columns + area value + remove button.
// `minmax(0,1fr)` lets the input columns shrink on narrow screens instead of
// overflowing the card (the previous fixed-px tracks broke on mobile).
$lt_area_cols = 'grid grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_minmax(40px,auto)_24px] gap-2 sm:gap-3';
?>
<div id="lt-area-calculator" class="lt-area-calc h-full bg-lt-white border border-[#E2DDD7] rounded-xl overflow-hidden"
	aria-label="<?php esc_attr_e('Area calculator', 'local-tasker'); ?>">
	<!-- Header -->
	<div class="flex items-center justify-between gap-3 px-5 py-4 bg-[#F6922207] border-b-[2px] border-[#F69222]">
		<div class="flex items-center gap-2 min-w-0">
			<svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path
					d="M13.548 1.50488H4.51607C3.6847 1.50488 3.01074 2.17884 3.01074 3.01021V15.0528C3.01074 15.8842 3.6847 16.5581 4.51607 16.5581H13.548C14.3794 16.5581 15.0533 15.8842 15.0533 15.0528V3.01021C15.0533 2.17884 14.3794 1.50488 13.548 1.50488Z"
					stroke="#F69222" stroke-width="1.50532" stroke-linecap="round" stroke-linejoin="round" />
				<path d="M6.02148 4.51562H12.0428" stroke="#F69222" stroke-width="1.50532" stroke-linecap="round"
					stroke-linejoin="round" />
				<path d="M12.043 10.5371V13.5478" stroke="#F69222" stroke-width="1.50532" stroke-linecap="round"
					stroke-linejoin="round" />
				<path d="M12.043 7.52637H12.0503" stroke="#F69222" stroke-width="1.50532" stroke-linecap="round"
					stroke-linejoin="round" />
				<path d="M9.03223 7.52637H9.03952" stroke="#F69222" stroke-width="1.50532" stroke-linecap="round"
					stroke-linejoin="round" />
				<path d="M6.02148 7.52637H6.02878" stroke="#F69222" stroke-width="1.50532" stroke-linecap="round"
					stroke-linejoin="round" />
				<path d="M9.03223 10.5371H9.03952" stroke="#F69222" stroke-width="1.50532" stroke-linecap="round"
					stroke-linejoin="round" />
				<path d="M6.02148 10.5371H6.02878" stroke="#F69222" stroke-width="1.50532" stroke-linecap="round"
					stroke-linejoin="round" />
				<path d="M9.03223 13.5479H9.03952" stroke="#F69222" stroke-width="1.50532" stroke-linecap="round"
					stroke-linejoin="round" />
				<path d="M6.02148 13.5479H6.02878" stroke="#F69222" stroke-width="1.50532" stroke-linecap="round"
					stroke-linejoin="round" />
			</svg>
			<span
				class="text-base font-bold font-semi-ext text-lt-text-primary truncate"><?php esc_html_e('Area Calculator', 'local-tasker'); ?></span>
		</div>
		<span
			class="text-caption-sm text-lt-text-muted shrink-0 max-xs:hidden"><?php esc_html_e('Calculate your total area', 'local-tasker'); ?></span>
	</div>
	<div class="sm:py-10 sm:px-6 p-4">
		<!-- Column headers -->
		<div class="pb-4 border-b-[1.2px] border-[#E2DDD766] <?php echo esc_attr($lt_area_cols); ?> mb-2 px-1">
			<span
				class="text-caption-sm font-semibold text-lt-text-muted tracking-[0.04em] truncate"><?php esc_html_e('Room', 'local-tasker'); ?></span>
			<span class="text-caption-sm font-semibold text-lt-text-muted tracking-[0.04em] truncate">
				<span class="sm:hidden">L (m)</span><span
					class="hidden sm:inline"><?php esc_html_e('Length (m)', 'local-tasker'); ?></span>
			</span>
			<span class="text-caption-sm font-semibold text-lt-text-muted tracking-[0.04em] truncate">
				<span class="sm:hidden">W (m)</span><span
					class="hidden sm:inline"><?php esc_html_e('Width (m)', 'local-tasker'); ?></span>
			</span>
			<span class="text-caption-sm font-semibold text-lt-text-muted tracking-[0.04em] truncate">
				<span class="sm:hidden">sqm</span><span
					class="hidden sm:inline"><?php esc_html_e('Area (sqm)', 'local-tasker'); ?></span>
			</span>
			<span class="sr-only"><?php esc_html_e('Remove row', 'local-tasker'); ?></span>
		</div>
		<!-- Room rows container -->
		<div id="lt-area-rows" class="flex flex-col gap-2">
			<!-- First row rendered via JS on init; template below -->
		</div>
		<!-- Row template (hidden, cloned by JS) -->
		<template id="lt-area-row-template">
			<div class="lt-area-row <?php echo esc_attr($lt_area_cols); ?> items-center pb-2 border-b-[1.2px] border-[#E2DDD766]">
				<input type="text"
					class="lt-area-row__name min-w-0 w-full border border-[#E2DDD7] rounded bg-[#F7F6F4] px-2 py-1 text-caption-sm text-lt-text-primary focus:outline-none focus:ring-2 focus:ring-lt-brand/30 focus:border-lt-brand"
					placeholder="<?php esc_attr_e('Living Room', 'local-tasker'); ?>"
					aria-label="<?php esc_attr_e('Room name', 'local-tasker'); ?>">
				<input type="number"
					class="lt-area-row__length min-w-0 w-full border border-[#E2DDD7] rounded bg-[#F7F6F4] px-2 py-1 text-caption-sm text-lt-text-primary focus:outline-none focus:ring-2 focus:ring-lt-brand/30 focus:border-lt-brand"
					placeholder="0" min="0" step="0.01" inputmode="decimal"
					aria-label="<?php esc_attr_e('Room length in metres', 'local-tasker'); ?>">
				<input type="number"
					class="lt-area-row__width min-w-0 w-full border border-[#E2DDD7] rounded bg-[#F7F6F4] px-2 py-1 text-caption-sm text-lt-text-primary focus:outline-none focus:ring-2 focus:ring-lt-brand/30 focus:border-lt-brand"
					placeholder="0" min="0" step="0.01" inputmode="decimal"
					aria-label="<?php esc_attr_e('Room width in metres', 'local-tasker'); ?>">
				<span class="holder flex gap-1 justify-between items-center shrink-0 w-[70px]">
					<span class="lt-area-row__area text-caption-sm font-semibold text-lt-text-primary text-center"
						aria-label="<?php esc_attr_e('Calculated area', 'local-tasker'); ?>" aria-live="polite">0</span>
					<button type="button"
						class="cursor-pointer lt-area-row__remove w-6 h-6 flex items-center justify-center rounded text-lt-text-muted hover:text-[#E02020] hover:bg-red-50 transition-colors duration-150 shrink-0"
						aria-label="<?php esc_attr_e('Remove this area', 'local-tasker'); ?>">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="url(#clip0_50512_7825)">
								<path d="M1.95703 3.91406H13.6986" stroke="#6B6B6B" stroke-width="1.30461"
									stroke-linecap="round" stroke-linejoin="round" />
								<path
									d="M12.394 3.91406V13.0464C12.394 13.6987 11.7417 14.351 11.0894 14.351H4.56633C3.91403 14.351 3.26172 13.6987 3.26172 13.0464V3.91406"
									stroke="#6B6B6B" stroke-width="1.30461" stroke-linecap="round" stroke-linejoin="round" />
								<path
									d="M5.21875 3.91392V2.6093C5.21875 1.95699 5.87106 1.30469 6.52336 1.30469H9.13259C9.7849 1.30469 10.4372 1.95699 10.4372 2.6093V3.91392"
									stroke="#6B6B6B" stroke-width="1.30461" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M6.52344 7.17578V11.0896" stroke="#6B6B6B" stroke-width="1.30461"
									stroke-linecap="round" stroke-linejoin="round" />
								<path d="M9.13184 7.17578V11.0896" stroke="#6B6B6B" stroke-width="1.30461"
									stroke-linecap="round" stroke-linejoin="round" />
							</g>
							<defs>
								<clipPath id="clip0_50512_7825">
									<rect width="15.6554" height="15.6554" fill="white" />
								</clipPath>
							</defs>
						</svg>
					</button>
				</span>
			</div>
		</template>
		<!-- Add row -->
		<button type="button" id="lt-area-add-row"
			class="mt-6 flex items-center gap-2 text-caption-sm font-semibold text-lt-brand hover:underline cursor-pointer">
			<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"
				aria-hidden="true">
				<path d="M7 1v12M1 7h12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
			</svg>
			<?php esc_html_e('Add another area', 'local-tasker'); ?>
		</button>
		<!-- Totals — two big-number columns -->
		<div class="mt-5 pt-4 border-t border-[#E2DDD7] grid grid-cols-2 gap-4">
			<div class="flex flex-col gap-1 min-w-0">
				<span class="text-caption-sm text-lt-text-muted"><?php esc_html_e('Total area', 'local-tasker'); ?></span>
				<span class="text-2xl font-bold text-lt-text-primary leading-none break-words" id="lt-area-total"
					aria-live="polite">0 </span>
				<span class="quat text-caption-sm text-lt-text-muted block">sqm</span>
			</div>
			<div class="flex flex-col gap-1 min-w-0 text-right">
				<span
					class="text-caption-sm text-lt-text-muted"><?php esc_html_e('With 10% wastage', 'local-tasker'); ?></span>
				<span class="text-body-lg font-medium text-lt-text-primary leading-none break-words" id="lt-area-wastage"
					aria-live="polite">0 sqm</span>
				<span
					class="text-caption-sm text-lt-accent font-medium"><?php esc_html_e('+10% included', 'local-tasker'); ?></span>
			</div>
		</div>
		<!-- Use This Area CTA -->
		<button type="button" id="lt-use-this-area"
			class="w-full mt-5 rounded-md bg-lt-accent text-lt-white font-medium text-caption-md py-3 transition-colors duration-200 hover:bg-lt-accent/90 cursor-pointer"
			aria-label="<?php esc_attr_e('Use this area in the calculator', 'local-tasker'); ?>">
			<?php esc_html_e('Use This Area', 'local-tasker'); ?>
		</button>
	</div>
</div><!-- /.lt-area-calc -->