<?php
/**
 * Product gallery — main image + thumbnails + prev/next.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attachment_ids = $product->get_gallery_image_ids();
$main_id        = $product->get_image_id();

// Build full list: main image first, then gallery images. The main slot is
// always present — a 0 id means the product has no featured image, and that
// one slide renders the placeholder. Gallery images are left as they are.
$all_ids = array_merge( [ (int) $main_id ], $attachment_ids );
$count   = count( $all_ids );

// Lightbox group id. Fancybox is bound site-wide to `[data-fancybox]` in
// `src/global/js/components/global-fancybox.js`, so the gallery only has to
// declare the group — no extra library and no per-page init.
$lightbox_group = 'lt-product-gallery';
?>

<div class="lt-gallery" id="lt-gallery" data-count="<?php echo esc_attr( $count ); ?>">

	<!-- Main image -->
	<div class="lt-gallery__main relative overflow-hidden rounded-xl border border-[#E2DDD7] bg-lt-white-lilac aspect-[560/448]">

		<!-- Slides -->
		<div class="lt-gallery__track flex transition-transform duration-400 ease-in-out h-full" id="lt-gallery-track">
			<?php foreach ( $all_ids as $index => $id ) : ?>
				<div
					class="lt-gallery__slide shrink-0 w-full h-full"
					aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>"
					id="lt-slide-<?php echo esc_attr( $index ); ?>"
				>
					<?php if ( $id ) : ?>
						<?php
						$full_src = wp_get_attachment_image_url( $id, 'full' );
						$caption  = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
						if ( '' === $caption ) {
							$caption = get_the_title();
						}
						?>
						<a
							href="<?php echo esc_url( $full_src ); ?>"
							class="lt-gallery__zoom block w-full h-full cursor-zoom-in"
							data-fancybox="<?php echo esc_attr( $lightbox_group ); ?>"
							data-caption="<?php echo esc_attr( $caption ); ?>"
							aria-label="<?php echo esc_attr( sprintf( __( 'View image %d larger', 'local-tasker' ), $index + 1 ) ); ?>"
							<?php // Off-screen slides are aria-hidden, so keep them out of the tab order too; the JS keeps this in sync. ?>
							tabindex="<?php echo $index === 0 ? '0' : '-1'; ?>"
						>
							<?php echo wp_get_attachment_image( $id, 'woocommerce_single', false, [
								'class'   => 'w-full h-full object-cover',
								'loading' => $index === 0 ? 'eager' : 'lazy',
							] ); ?>
						</a>
					<?php else : ?>
						<?php echo wc_placeholder_img( 'woocommerce_single', [ 'class' => 'w-full h-full object-cover' ] ); ?>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Zoom affordance — overlays the image, so it must sit out of the flow.
		     Click-through only: the slide's own link is what opens the lightbox. -->
		<?php if ( $main_id || $attachment_ids ) : ?>
			<span class="lt-gallery__zoom-hint pointer-events-none absolute right-3 top-3 z-10 flex items-center justify-center w-9 h-9 rounded-full bg-lt-white/80 border border-[#E9EAEC] text-lt-text-secondary shadow-sm" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<circle cx="7" cy="7" r="5" stroke="currentColor" stroke-width="1.5"/>
					<path d="M7 5v4M5 7h4M10.8 10.8L14 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
				</svg>
			</span>
		<?php endif; ?>

		<!-- Prev / Next -->
		<?php if ( $count > 1 ) : ?>
			<button
				type="button"
				id="lt-gallery-prev"
				class="lt-gallery__arrow cursor-pointer lt-gallery__arrow--prev absolute left-3 top-1/2 -translate-y-1/2 z-10 flex items-center justify-center w-10 h-10 rounded-full bg-lt-white/80 hover:bg-lt-white border border-[#E9EAEC] transition-colors duration-200 shadow-sm"
				aria-label="<?php esc_attr_e( 'Previous image', 'local-tasker' ); ?>"
			>
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
			<button
				type="button"
				id="lt-gallery-next"
				class="lt-gallery__arrow cursor-pointer lt-gallery__arrow--next absolute right-3 top-1/2 -translate-y-1/2 z-10 flex items-center justify-center w-10 h-10 rounded-full bg-lt-white/80 hover:bg-lt-white border border-[#E9EAEC] transition-colors duration-200 shadow-sm"
				aria-label="<?php esc_attr_e( 'Next image', 'local-tasker' ); ?>"
			>
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>

			<!-- Dot indicators -->
			<div class="lt-gallery__dots absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-[6px] z-10" role="tablist" aria-label="<?php esc_attr_e( 'Gallery images', 'local-tasker' ); ?>">
				<?php foreach ( $all_ids as $index => $id ) : ?>
					<button
						type="button"
						class="cursor-pointer lt-gallery__dot w-[8px] h-[8px] rounded-full border-0 transition-all duration-200 <?php echo $index === 0 ? 'bg-lt-brand w-[20px]' : 'bg-lt-white/60 hover:bg-lt-white'; ?>"
						role="tab"
						aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
						aria-controls="lt-slide-<?php echo esc_attr( $index ); ?>"
						data-index="<?php echo esc_attr( $index ); ?>"
						aria-label="<?php echo esc_attr( sprintf( __( 'Image %d', 'local-tasker' ), $index + 1 ) ); ?>"
					></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div><!-- /.lt-gallery__main -->

	<!-- Thumbnails -->
	<?php if ( $count > 1 ) : ?>
		<div class="lt-gallery__thumbs grid grid-cols-4 gap-2 mt-3" role="tablist" aria-label="<?php esc_attr_e( 'Image thumbnails', 'local-tasker' ); ?>">
			<?php foreach ( $all_ids as $index => $id ) : ?>
				<button
					type="button"
					class="lt-gallery__thumb w-full aspect-square rounded-md overflow-hidden border-2 transition-all duration-200 cursor-pointer focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-lt-brand <?php echo $index === 0 ? 'border-lt-accents' : 'border-[#E2DDD7] hover:border-[#D1D5DB] opacity-50'; ?>"
					role="tab"
					aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
					aria-controls="lt-slide-<?php echo esc_attr( $index ); ?>"
					data-index="<?php echo esc_attr( $index ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'View image %d', 'local-tasker' ), $index + 1 ) ); ?>"
				>
					<?php if ( $id ) : ?>
						<?php echo wp_get_attachment_image( $id, 'thumbnail', false, [ 'class' => 'w-full h-full object-cover' ] ); ?>
					<?php else : ?>
						<?php echo wc_placeholder_img( 'thumbnail', [ 'class' => 'w-full h-full object-cover' ] ); ?>
					<?php endif; ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</div><!-- /.lt-gallery -->
