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

// Build full list: main image first, then gallery images.
$all_ids = $main_id ? array_merge( [ $main_id ], $attachment_ids ) : $attachment_ids;
$count   = count( $all_ids );
?>

<div class="lt-gallery" id="lt-gallery" data-count="<?php echo esc_attr( $count ); ?>">

	<!-- Main image -->
	<div class="lt-gallery__main relative overflow-hidden rounded-2xl bg-lt-white-lilac aspect-[560/448]">

		<!-- Slides -->
		<div class="lt-gallery__track flex transition-transform duration-400 ease-in-out h-full" id="lt-gallery-track">
			<?php foreach ( $all_ids as $index => $id ) : ?>
				<div
					class="lt-gallery__slide shrink-0 w-full h-full"
					aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>"
					id="lt-slide-<?php echo esc_attr( $index ); ?>"
				>
					<?php echo wp_get_attachment_image( $id, 'woocommerce_single', false, [
						'class'   => 'w-full h-full object-cover',
						'loading' => $index === 0 ? 'eager' : 'lazy',
					] ); ?>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Prev / Next -->
		<?php if ( $count > 1 ) : ?>
			<button
				type="button"
				id="lt-gallery-prev"
				class="lt-gallery__arrow lt-gallery__arrow--prev absolute left-3 top-1/2 -translate-y-1/2 z-10 flex items-center justify-center w-10 h-10 rounded-full bg-lt-white/80 hover:bg-lt-white border border-[#E9EAEC] transition-colors duration-200 shadow-sm"
				aria-label="<?php esc_attr_e( 'Previous image', 'local-tasker' ); ?>"
			>
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
			<button
				type="button"
				id="lt-gallery-next"
				class="lt-gallery__arrow lt-gallery__arrow--next absolute right-3 top-1/2 -translate-y-1/2 z-10 flex items-center justify-center w-10 h-10 rounded-full bg-lt-white/80 hover:bg-lt-white border border-[#E9EAEC] transition-colors duration-200 shadow-sm"
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
						class="lt-gallery__dot w-[8px] h-[8px] rounded-full border-0 transition-all duration-200 <?php echo $index === 0 ? 'bg-lt-brand w-[20px]' : 'bg-lt-white/60 hover:bg-lt-white'; ?>"
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
					class="lt-gallery__thumb w-full aspect-square rounded-xl overflow-hidden border-2 transition-all duration-200 <?php echo $index === 0 ? 'border-lt-brand' : 'border-transparent hover:border-[#D1D5DB]'; ?>"
					role="tab"
					aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
					aria-controls="lt-slide-<?php echo esc_attr( $index ); ?>"
					data-index="<?php echo esc_attr( $index ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'View image %d', 'local-tasker' ), $index + 1 ) ); ?>"
				>
					<?php echo wp_get_attachment_image( $id, 'thumbnail', false, [ 'class' => 'w-full h-full object-cover' ] ); ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</div><!-- /.lt-gallery -->
