<?php
/**
 * "Let's Work Together" CTA — last section on single product page before footer.
 * Content editable via ACF options page fields (work_together_* group).
 * Falls back to hardcoded defaults if ACF options not set.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

$heading = function_exists( 'get_field' ) ? get_field( 'work_together_heading', 'option' ) : '';
$content = function_exists( 'get_field' ) ? get_field( 'work_together_content', 'option' ) : '';
$btn     = function_exists( 'get_field' ) ? get_field( 'work_together_button',  'option' ) : null;

// Defaults.
if ( ! $heading ) $heading = __( "Let's Work Together", 'local-tasker' );
if ( ! $content ) $content = __( "Whether you're planning a flooring renovation, upgrading a kitchen, or sourcing products and installation services, our team is here to help. Get in touch to discuss your project, request a quote, or speak with our team about the right solution for your space.", 'local-tasker' );
if ( ! $btn )     $btn     = [ 'url' => '/contact', 'title' => __( 'Contact Us', 'local-tasker' ), 'target' => '_self' ];
?>

<section class="lt-work-together-cta bg-lt-white-lilac py-14 md:py-20" aria-label="<?php esc_attr_e( "Let's work together", 'local-tasker' ); ?>">
	<div class="container">
		<div class="text-center max-w-[700px] mx-auto">

			<?php if ( $heading ) : ?>
				<h2 class="text-h3 font-bold font-semi-ext text-lt-text-primary mb-4">
					<?php echo wp_kses_post( $heading ); ?>
				</h2>
			<?php endif; ?>

			<?php if ( $content ) : ?>
				<p class="text-body text-lt-text-muted leading-[1.6] mb-8 m-0">
					<?php echo wp_kses_post( $content ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $btn && ! empty( $btn['url'] ) ) : ?>
				<a
					href="<?php echo esc_url( $btn['url'] ); ?>"
					target="<?php echo esc_attr( $btn['target'] ?: '_self' ); ?>"
					class="btn btn--brand"
				>
					<?php echo esc_html( $btn['title'] ); ?>
				</a>
			<?php endif; ?>

		</div>
	</div>
</section>
