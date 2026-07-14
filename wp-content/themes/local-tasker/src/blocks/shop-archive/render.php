<?php
/**
 * ACF Block: Shop Archive.
 *
 * Renders the full storefront archive (toolbar + sidebar filters + product grid +
 * AJAX load-more) via the shared lt_render_shop_archive() helper, so the block and
 * the native /shop/ template stay identical.
 *
 * @param array $block ACF block settings and attributes.
 * @package local-tasker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Anchor.
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
	$anchor = 'id="' . esc_attr( $block['anchor'] ) . '" ';
}

// Class name.
$class_name = 'acf-block lt-shop-block';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}
$settings    = generate_block_settings_classnames();
$class_name .= $settings['class_name'];

$style_attr = '';
if ( '' !== $settings['block_style'] ) {
	$style_attr = 'style="' . esc_attr( $settings['block_style'] ) . '"';
}

// Optional block overrides.
$overrides = array();
$per_page  = get_field( 'sa_per_page' );
if ( $per_page ) {
	$overrides['per_page'] = (int) $per_page;
}
$default_cat = get_field( 'sa_default_category' );
if ( $default_cat && empty( $_GET['category'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$overrides['category'] = sanitize_title( is_object( $default_cat ) ? $default_cat->slug : $default_cat );
}

// Editor gets a lightweight placeholder (the live query is heavy for previews).
$is_preview = ! empty( $block['is_preview'] );
?>
<section <?php echo $anchor; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>class="<?php echo esc_attr( $class_name ); ?>" <?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="container">
		<?php
		if ( $is_preview ) {
			echo '<div class="lt-shop__editor-note">' . esc_html__( 'Shop Archive — the live product grid, filters and sort render on the front end.', 'local-tasker' ) . '</div>';
		} elseif ( function_exists( 'lt_render_shop_archive' ) ) {
			lt_render_shop_archive( $overrides );
		}
		?>
	</div>
</section>
