<?php
/**
 * "Supply + Install = Unbeatable Savings" CTA — shown on single product below related products.
 * Content editable via ACF options page fields (supply_cta_* group).
 * Falls back to hardcoded defaults if ACF options not set.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

$label   = function_exists( 'get_field' ) ? get_field( 'supply_cta_label',   'option' ) : '';
$heading = function_exists( 'get_field' ) ? get_field( 'supply_cta_heading', 'option' ) : '';
$content = function_exists( 'get_field' ) ? get_field( 'supply_cta_content', 'option' ) : '';
$btn     = function_exists( 'get_field' ) ? get_field( 'supply_cta_button',  'option' ) : null;

// Defaults.
if ( ! $label )   $label   = __( 'SERVICES & INSTALLATIONS', 'local-tasker' );
if ( ! $heading ) $heading = __( 'Supply + Install = Unbeatable Savings.', 'local-tasker' );
if ( ! $content ) $content = __( "Why pay a premium? Buy your materials directly from our warehouse at wholesale prices, and hire a verified local installer through our platform to save money, backed by a team built to handle projects at scale.", 'local-tasker' );
if ( ! $btn )     $btn     = [ 'url' => '/services', 'title' => __( 'View Services', 'local-tasker' ), 'target' => '_self' ];

$bg_image = function_exists( 'get_field' ) ? get_field( 'supply_cta_bg_image', 'option' ) : null;
?>

<section class="lt-supply-cta relative overflow-hidden py-14 md:py-20" aria-label="<?php esc_attr_e( 'Supply and install services', 'local-tasker' ); ?>">

	<!-- Background -->
	<?php if ( $bg_image ) : ?>
		<div class="abs-bg absolute inset-0 w-full h-full z-0 pointer-events-none">
			<?php echo wp_get_attachment_image( is_array( $bg_image ) ? $bg_image['ID'] : $bg_image, 'full', false, [ 'class' => 'w-full h-full object-cover' ] ); ?>
			<div class="absolute inset-0 bg-[#0A1628]/85"></div>
		</div>
	<?php else : ?>
		<div class="absolute inset-0 bg-[#0A1628] z-0 pointer-events-none"></div>
	<?php endif; ?>

	<div class="container relative z-10">
		<div class="max-w-[640px]">

			<?php if ( $label ) : ?>
				<p class="text-caption-xs font-bold uppercase tracking-[0.12em] text-lt-accent mb-3 m-0">
					<?php echo esc_html( $label ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h2 class="text-h3 font-bold font-semi-ext text-lt-white leading-[1.15] mb-4">
					<?php echo wp_kses_post( $heading ); ?>
				</h2>
			<?php endif; ?>

			<?php if ( $content ) : ?>
				<p class="text-body text-lt-white/75 leading-[1.6] mb-8 m-0">
					<?php echo wp_kses_post( $content ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $btn && ! empty( $btn['url'] ) ) : ?>
				<a
					href="<?php echo esc_url( $btn['url'] ); ?>"
					target="<?php echo esc_attr( $btn['target'] ?: '_self' ); ?>"
					class="btn btn--accent"
				>
					<?php echo esc_html( $btn['title'] ); ?>
				</a>
			<?php endif; ?>

		</div>
	</div>

</section>
