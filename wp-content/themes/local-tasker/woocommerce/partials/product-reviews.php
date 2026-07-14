<?php
/**
 * "Feedback From Real Customers" reviews section on single product.
 * Pulls from ACF options page repeater field `site_reviews` (registered in inc/acf-product-fields.php).
 * Falls back to WooCommerce product reviews if no options reviews are configured.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

// Try ACF options page first (site-wide Google reviews).
$reviews     = [];
$review_link = '';
if ( function_exists( 'get_field' ) ) {
	$raw_reviews = get_field( 'site_reviews', 'option' );
	if ( is_array( $raw_reviews ) ) {
		$reviews = $raw_reviews;
	}
	$review_link = get_field( 'site_review_link', 'option' );
}

// Show max 6.
$reviews = array_slice( $reviews, 0, 6 );

if ( empty( $reviews ) ) {
	return;
}
?>

<section class="lt-product-reviews bg-lt-white py-14 md:py-20" aria-label="<?php esc_attr_e( 'Customer reviews', 'local-tasker' ); ?>">
	<div class="container">

		<!-- Heading -->
		<div class="text-center mb-10 md:mb-12">
			<h2 class="text-h3 font-bold font-semi-ext text-lt-text-primary mb-2">
				<?php esc_html_e( 'Feedback From Real Customers', 'local-tasker' ); ?>
			</h2>
			<p class="text-body text-lt-text-muted m-0">
				<?php esc_html_e( 'See what our customers are saying about us.', 'local-tasker' ); ?>
			</p>
		</div>

		<!-- Review grid -->
		<div class="grid sm:grid-cols-2 md:grid-cols-3 gap-5 mb-10">
			<?php foreach ( $reviews as $review ) :
				$name    = ! empty( $review['reviewer_name'] )  ? $review['reviewer_name']  : __( 'Customer', 'local-tasker' );
				$date    = ! empty( $review['review_date'] )    ? $review['review_date']    : '';
				$stars   = ! empty( $review['star_rating'] )    ? (int) $review['star_rating'] : 5;
				$stars   = max( 1, min( 5, $stars ) );
				$text    = ! empty( $review['review_text'] )    ? $review['review_text']    : '';
				$initial = mb_strtoupper( mb_substr( $name, 0, 1 ) );
			?>
				<article class="lt-review-card bg-lt-white-lilac rounded-2xl p-5 flex flex-col gap-3">

					<!-- Reviewer header -->
					<div class="flex items-center gap-3">
						<div class="w-10 h-10 rounded-full bg-lt-brand/15 flex items-center justify-center shrink-0">
							<span class="text-caption-sm font-bold text-lt-brand leading-none" aria-hidden="true">
								<?php echo esc_html( $initial ); ?>
							</span>
						</div>
						<div class="min-w-0">
							<p class="text-caption-sm font-semibold text-lt-text-primary leading-none m-0 truncate">
								<?php echo esc_html( $name ); ?>
							</p>
							<?php if ( $date ) : ?>
								<p class="text-caption-xs text-lt-text-muted leading-none mt-[3px] m-0">
									<?php echo esc_html( $date ); ?>
								</p>
							<?php endif; ?>
						</div>
						<!-- Google G icon -->
						<div class="ml-auto shrink-0" aria-label="Google review">
							<svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
								<path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
								<path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
								<path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
							</svg>
						</div>
					</div>

					<!-- Stars -->
					<div class="flex gap-[3px]" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %d out of 5 stars', 'local-tasker' ), $stars ) ); ?>">
						<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
							<svg width="14" height="14" viewBox="0 0 14 14" fill="<?php echo $i <= $stars ? '#F59E0B' : '#E5E7EB'; ?>" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M7 1l1.545 3.09 3.41.495-2.465 2.4.582 3.39L7 8.755l-3.072 1.62.582-3.39L2.045 4.585l3.41-.495L7 1z"/>
							</svg>
						<?php endfor; ?>
					</div>

					<!-- Review text -->
					<?php if ( $text ) : ?>
						<p class="text-caption-sm text-lt-text-secondary leading-[1.55] m-0 line-clamp-4">
							<?php echo esc_html( $text ); ?>
						</p>
					<?php endif; ?>

				</article>
			<?php endforeach; ?>
		</div>

		<!-- Leave a review CTA -->
		<?php if ( $review_link ) : ?>
			<div class="text-center">
				<a
					href="<?php echo esc_url( $review_link ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					class="btn btn--brand"
				>
					<?php esc_html_e( 'Leave a Review', 'local-tasker' ); ?>
				</a>
			</div>
		<?php endif; ?>

	</div>
</section>
