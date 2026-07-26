<?php
/**
 * Trust / USP strip — 3 icon-badge columns between the product top section and spec table.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

$items = [
	[
		'title' => __( 'Rapid Growth Proven Scale', 'local-tasker' ),
		'desc'  => __( 'Rapidly growing, built for projects of any size.', 'local-tasker' ),
		'icon'  => '<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4 20l6-6 4 4 8-10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="4" cy="20" r="1.5" fill="currentColor"/><circle cx="10" cy="14" r="1.5" fill="currentColor"/><circle cx="14" cy="18" r="1.5" fill="currentColor"/><circle cx="22" cy="10" r="1.5" fill="currentColor"/></svg>',
	],
	[
		'title' => __( 'Built for Volume, Priced to Win', 'local-tasker' ),
		'desc'  => __( 'High quality materials at scale, without inflated retail charges.', 'local-tasker' ),
		'icon'  => '<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M14 4l2.5 5 5.5.8-4 3.9.95 5.5L14 16.75l-4.95 2.45.95-5.5L6 9.8l5.5-.8L14 4z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
	],
	[
		'title' => __( 'Supply to Install — Sorted', 'local-tasker' ),
		'desc'  => __( 'Products and installation services, all handled in one streamlined process.', 'local-tasker' ),
		'icon'  => '<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 14.5l5.5 5.5 12.5-12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
	],
];
?>

<section class="lt-product-trust-strip bg-lt-white border-y border-[#E9EAEC] py-8 md:py-10 hidden" aria-label="<?php esc_attr_e( 'Why choose us', 'local-tasker' ); ?>">
	<div class="container">
		<div class="grid sm:grid-cols-3 gap-6 md:gap-10">
			<?php foreach ( $items as $item ) : ?>
				<div class="lt-trust-item flex sm:flex-col items-start sm:items-center sm:text-center gap-4">

					<div class="lt-trust-item__icon w-14 h-14 rounded-2xl bg-lt-brand/8 text-lt-brand flex items-center justify-center shrink-0">
						<?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput — SVG markup ?>
					</div>

					<div>
						<h3 class="text-caption-md font-bold text-lt-text-primary mb-1 m-0">
							<?php echo esc_html( $item['title'] ); ?>
						</h3>
						<p class="text-caption-sm text-lt-text-muted leading-[1.5] m-0">
							<?php echo esc_html( $item['desc'] ); ?>
						</p>
					</div>

				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
