<?php
/**
 * "How it works" — 4-step calculator process explainer on single product.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

$steps = [
	[
		'title' => __( 'Enter your area', 'local-tasker' ),
		'desc'  => __( "We'll use this to determine how many boxes you'll need to cover your floor.", 'local-tasker' ),
	],
	[
		'title' => __( 'Enter your total area in sqm', 'local-tasker' ),
		'desc'  => __( 'Type your total sqm directly or use the Area Calculator to add individual room dimensions.', 'local-tasker' ),
	],
	[
		'title' => __( 'We calculate the boxes', 'local-tasker' ),
		'desc'  => __( 'Our calculator rounds up to full boxes and can include an optional 10% wastage allowance.', 'local-tasker' ),
	],
	[
		'title' => __( 'Quote estimate', 'local-tasker' ),
		'desc'  => __( 'You get an instant breakdown: coverage area, number of boxes, and total price including GST.', 'local-tasker' ),
	],
];
?>

<div class="lt-how-it-works">

<ol class="flex flex-col gap-0 list-none p-0 m-0" role="list">
		<?php foreach ( $steps as $i => $step ) :
			$num     = $i + 1;
			$is_last = $i === count( $steps ) - 1;
		?>
			<li class="lt-how-it-works__step flex gap-4 relative">

				<!-- Number + connector line -->
				<div class="flex flex-col items-center shrink-0">
					<div class="w-8 h-8 rounded-full bg-lt-brand/10 flex items-center justify-center shrink-0">
						<span class="text-caption-xs font-bold text-lt-brand leading-none">
							<?php echo esc_html( $num ); ?>
						</span>
					</div>
					<?php if ( ! $is_last ) : ?>
						<div class="w-px flex-1 bg-[#E9EAEC] mt-1 mb-1 min-h-[24px]" aria-hidden="true"></div>
					<?php endif; ?>
				</div>

				<!-- Content -->
				<div class="pb-<?php echo $is_last ? '0' : '4'; ?>">
					<p class="text-caption-sm font-semibold text-lt-text-primary leading-[1.4] m-0">
						<?php echo esc_html( $step['title'] ); ?>
					</p>
					<p class="text-caption-xs text-lt-text-muted leading-[1.5] mt-[3px] m-0">
						<?php echo esc_html( $step['desc'] ); ?>
					</p>
				</div>

			</li>
		<?php endforeach; ?>
	</ol>

</div><!-- /.lt-how-it-works -->
