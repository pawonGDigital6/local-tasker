<?php
/**
 * "How it works" — calculator process explainer card on single product.
 *
 * Self-contained card (blue-tinted header + numbered steps + a live "Quick
 * estimate" box that mirrors the main calculator results). Sits beside the
 * Area Calculator card.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

$steps = [
	[
		'title' => __( 'Enter your area', 'local-tasker' ),
		'desc'  => __( 'Use the calculator below so you always know the final unit price before adding to cart.', 'local-tasker' ),
	],
	[
		'title' => __( 'Enter your total area in sqm', 'local-tasker' ),
		'desc'  => __( 'Type in the number of square metres you need to cover.', 'local-tasker' ),
	],
	[
		'title' => __( 'We calculate your boxes', 'local-tasker' ),
		'desc'  => __( 'We round up to the nearest full box and show the total coverage and price upfront — no surprises at checkout.', 'local-tasker' ),
	],
];
?>

<div class="lt-how-it-works h-full bg-lt-white border border-[#E2DDD7] rounded-xl overflow-hidden">

	<!-- Header -->
	<div class="px-6 py-5 bg-[#0A65FC06] border-b-[2px] border-lt-brand">
		<h2 class="text-body font-bold font-semi-ext text-lt-text-primary m-0">
			<?php esc_html_e( 'How it works', 'local-tasker' ); ?>
		</h2>
	</div>

	<div class="p-5">

		<ol class="flex flex-col gap-7 list-none p-0 m-0" role="list">
			<?php foreach ( $steps as $i => $step ) :
				$num     = $i + 1;
				$is_last = $i === count( $steps ) - 1;
			?>
				<li class="lt-how-it-works__step flex gap-5 relative">

					<!-- Number + connector line -->
					<div class="flex flex-col items-center shrink-0">
						<div class="w-8 h-8 rounded-full bg-lt-brand/[0.07] flex items-center justify-center shrink-0">
							<span class="font-bold text-lt-brand leading-none">
								<?php echo esc_html( $num ); ?>
							</span>
						</div>
					</div>

					<!-- Content -->
					<div class="itemss">
						<div class="font-medium text-lt-text-primary leading-[1.4] m-0">
							<?php echo esc_html( $step['title'] ); ?>
						</div>
						<p class="text-caption-md text-lt-text-muted leading-[1.5] mt-[3px] m-0">
							<?php echo esc_html( $step['desc'] ); ?>
						</p>
					</div>

				</li>
			<?php endforeach; ?>
		</ol>

		<!-- Quick estimate — mirrors the main calculator (populated by product-single.js) -->
		<div class="lt-how-it-works__estimate mt-5 rounded-xl border border-[#0A65FC18] bg-[#0A65FC06] p-4">
			<p class="text-caption-md font-bold text-lt-text-primary m-0 mb-3">
				<?php esc_html_e( 'Quick estimate', 'local-tasker' ); ?>
			</p>
			<div class="flex flex-col gap-2">
				<div class="flex items-center justify-between gap-4">
					<span class="text-caption-sm text-lt-text-muted"><?php esc_html_e( 'Your area', 'local-tasker' ); ?></span>
					<span class="text-caption-sm font-semibold text-lt-text-primary" id="lt-qe-area">0 sqm</span>
				</div>
				<div class="flex items-center justify-between gap-4">
					<span class="text-caption-sm text-lt-text-muted"><?php esc_html_e( 'Boxes needed', 'local-tasker' ); ?></span>
					<span class="text-caption-sm font-semibold text-lt-text-primary" id="lt-qe-boxes">0 boxes</span>
				</div>
				<div class="flex items-center justify-between gap-4 pt-2 border-t border-lt-brand/20">
					<span class="text-caption-sm text-lt-text-muted"><?php esc_html_e( 'Total price', 'local-tasker' ); ?></span>
					<span class="text-caption-sm font-bold text-lt-brand" id="lt-qe-total">$0.00</span>
				</div>
			</div>
		</div>

	</div>

</div><!-- /.lt-how-it-works -->
