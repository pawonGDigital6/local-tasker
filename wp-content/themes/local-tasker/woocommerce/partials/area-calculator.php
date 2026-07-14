<?php
/**
 * Area calculator — collapsible panel that feeds into the main calculator.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;
?>

<div
	id="lt-area-calculator"
	class="lt-area-calc bg-lt-white border border-[#E9EAEC] rounded-2xl overflow-hidden"
	aria-label="<?php esc_attr_e( 'Area calculator', 'local-tasker' ); ?>"
>

	<!-- Header -->
	<div class="flex items-center justify-between px-5 py-4 bg-lt-snow-drift border-b border-[#E9EAEC]">
		<div class="flex items-center gap-2">
			<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-lt-brand shrink-0" aria-hidden="true">
				<rect x="1" y="1" width="6" height="6" rx="0.5" stroke="currentColor" stroke-width="1.2"/>
				<rect x="9" y="1" width="6" height="6" rx="0.5" stroke="currentColor" stroke-width="1.2"/>
				<rect x="1" y="9" width="6" height="6" rx="0.5" stroke="currentColor" stroke-width="1.2"/>
				<rect x="9" y="9" width="6" height="6" rx="0.5" stroke="currentColor" stroke-width="1.2"/>
			</svg>
			<span class="text-caption-md font-bold text-lt-text-primary"><?php esc_html_e( 'Area Calculator', 'local-tasker' ); ?></span>
		</div>
		<span class="text-caption-xs text-lt-text-muted"><?php esc_html_e( 'Calculate your total area', 'local-tasker' ); ?></span>
	</div>

	<div class="p-5">

		<!-- Column headers -->
		<div class="grid grid-cols-[1fr_80px_80px_70px_32px] gap-2 mb-2 px-1">
			<span class="text-caption-xs font-semibold text-lt-text-muted uppercase tracking-[0.06em]"><?php esc_html_e( 'Room / Area', 'local-tasker' ); ?></span>
			<span class="text-caption-xs font-semibold text-lt-text-muted uppercase tracking-[0.06em] text-center"><?php esc_html_e( 'Length (m)', 'local-tasker' ); ?></span>
			<span class="text-caption-xs font-semibold text-lt-text-muted uppercase tracking-[0.06em] text-center"><?php esc_html_e( 'Width (m)', 'local-tasker' ); ?></span>
			<span class="text-caption-xs font-semibold text-lt-text-muted uppercase tracking-[0.06em] text-center"><?php esc_html_e( 'Area', 'local-tasker' ); ?></span>
			<span class="sr-only"><?php esc_html_e( 'Remove row', 'local-tasker' ); ?></span>
		</div>

		<!-- Room rows container -->
		<div id="lt-area-rows" class="flex flex-col gap-2">
			<!-- First row rendered via JS on init; template below -->
		</div>

		<!-- Row template (hidden, cloned by JS) -->
		<template id="lt-area-row-template">
			<div class="lt-area-row grid grid-cols-[1fr_80px_80px_70px_32px] gap-2 items-center">
				<input
					type="text"
					class="lt-area-row__name border border-[#E9EAEC] rounded-lg px-3 py-2 text-caption-sm text-lt-text-primary bg-lt-white focus:outline-none focus:ring-2 focus:ring-lt-brand/30 focus:border-lt-brand"
					placeholder="<?php esc_attr_e( 'Living Room', 'local-tasker' ); ?>"
					aria-label="<?php esc_attr_e( 'Room name', 'local-tasker' ); ?>"
				>
				<input
					type="number"
					class="lt-area-row__length border border-[#E9EAEC] rounded-lg px-2 py-2 text-caption-sm text-center text-lt-text-primary bg-lt-white focus:outline-none focus:ring-2 focus:ring-lt-brand/30 focus:border-lt-brand"
					placeholder="0"
					min="0"
					step="0.01"
					inputmode="decimal"
					aria-label="<?php esc_attr_e( 'Room length in metres', 'local-tasker' ); ?>"
				>
				<input
					type="number"
					class="lt-area-row__width border border-[#E9EAEC] rounded-lg px-2 py-2 text-caption-sm text-center text-lt-text-primary bg-lt-white focus:outline-none focus:ring-2 focus:ring-lt-brand/30 focus:border-lt-brand"
					placeholder="0"
					min="0"
					step="0.01"
					inputmode="decimal"
					aria-label="<?php esc_attr_e( 'Room width in metres', 'local-tasker' ); ?>"
				>
				<span
					class="lt-area-row__area text-caption-sm font-semibold text-lt-text-primary text-center"
					aria-label="<?php esc_attr_e( 'Calculated area', 'local-tasker' ); ?>"
					aria-live="polite"
				>0</span>
				<button
					type="button"
					class="lt-area-row__remove w-8 h-8 flex items-center justify-center rounded-lg text-lt-text-muted hover:text-[#E02020] hover:bg-red-50 transition-colors duration-150"
					aria-label="<?php esc_attr_e( 'Remove this area', 'local-tasker' ); ?>"
				>
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
					</svg>
				</button>
			</div>
		</template>

		<!-- Add row -->
		<button
			type="button"
			id="lt-area-add-row"
			class="mt-3 flex items-center gap-2 text-caption-sm font-semibold text-lt-brand hover:underline"
		>
			<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<path d="M7 1v12M1 7h12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
			</svg>
			<?php esc_html_e( 'Add another area', 'local-tasker' ); ?>
		</button>

		<!-- Totals -->
		<div class="mt-5 pt-4 border-t border-[#E9EAEC] flex flex-col gap-[6px]">
			<div class="flex items-center justify-between">
				<span class="text-caption-sm text-lt-text-muted"><?php esc_html_e( 'Total area', 'local-tasker' ); ?></span>
				<span class="text-caption-sm font-bold text-lt-text-primary" id="lt-area-total" aria-live="polite">0 sqm</span>
			</div>
			<div class="flex items-center justify-between">
				<span class="text-caption-sm text-lt-text-muted"><?php esc_html_e( 'With 10% wastage', 'local-tasker' ); ?></span>
				<span class="text-caption-sm font-semibold text-lt-text-muted" id="lt-area-wastage" aria-live="polite">0 sqm</span>
			</div>
		</div>

		<!-- Use This Area CTA -->
		<button
			type="button"
			id="lt-use-this-area"
			class="btn btn--accent w-full mt-4"
			aria-label="<?php esc_attr_e( 'Use this area in the calculator', 'local-tasker' ); ?>"
		>
			<?php esc_html_e( 'Use This Area', 'local-tasker' ); ?>
		</button>

	</div>
</div><!-- /.lt-area-calc -->
