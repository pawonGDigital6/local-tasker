<?php
/**
 * WooCommerce product attribute taxonomies: Colour / Finish and Thickness.
 *
 * On the first request this seeds each attribute into WooCommerce's attribute
 * table (so it appears under Products → Attributes in the admin) and immediately
 * calls register_taxonomy() for the current request.
 *
 * From the second request onward WooCommerce reads its own table on init:5 and
 * registers the taxonomies itself — our callback sees taxonomy_exists() = true
 * and exits early, so there is zero overhead after setup.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'lt_register_product_attributes', 10 );

/**
 * Ensure pa_colour and pa_thickness exist as WC attribute taxonomies.
 */
function lt_register_product_attributes(): void {
	if ( ! function_exists( 'wc_create_attribute' ) || ! function_exists( 'wc_attribute_taxonomy_name' ) ) {
		return;
	}

	$attrs = [
		[
			'name'         => 'Colour / Finish',
			'slug'         => 'colour',
			'type'         => 'select',
			'order_by'     => 'menu_order',
			'has_archives' => false,
		],
		[
			'name'         => 'Thickness',
			'slug'         => 'thickness',
			'type'         => 'select',
			'order_by'     => 'name',
			'has_archives' => false,
		],
	];

	foreach ( $attrs as $attr ) {
		$taxonomy = wc_attribute_taxonomy_name( $attr['slug'] ); // pa_colour / pa_thickness

		// Already registered by WC on this request — nothing to do.
		if ( taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		// Seed the WC attribute DB row if it doesn't exist yet (idempotent).
		if ( ! wc_attribute_taxonomy_id_by_name( $attr['slug'] ) ) {
			$result = wc_create_attribute( [
				'name'         => $attr['name'],
				'slug'         => $attr['slug'],
				'type'         => $attr['type'],
				'order_by'     => $attr['order_by'],
				'has_archives' => $attr['has_archives'],
			] );

			if ( is_wp_error( $result ) ) {
				continue;
			}

			// Bust WC's attribute cache so it picks up the new row on the next request.
			delete_transient( 'wc_attribute_taxonomies' );
		}

		// Register the taxonomy for this request (WC handles subsequent requests itself).
		register_taxonomy(
			$taxonomy,
			[ 'product' ],
			lt_product_attribute_args( $attr['name'], $attr['slug'] )
		);
	}
}

/**
 * Build standard taxonomy args for a WooCommerce product attribute.
 *
 * @param string $label  Human-readable label (e.g. "Colour / Finish").
 * @param string $slug   Attribute slug without pa_ prefix (e.g. "colour").
 * @return array
 */
function lt_product_attribute_args( string $label, string $slug ): array {
	return [
		'label'             => $label,
		'labels'            => [
			'name'          => $label,
			'singular_name' => $label,
			'menu_name'     => $label,
			'all_items'     => 'All ' . $label,
			'add_new_item'  => 'Add New ' . $label,
			'new_item_name' => 'New ' . $label,
			'edit_item'     => 'Edit ' . $label,
			'update_item'   => 'Update ' . $label,
			'search_items'  => 'Search ' . $label,
		],
		'public'            => true,
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => [ 'slug' => 'product-' . $slug, 'with_front' => false ],
		'show_in_rest'      => true,
	];
}
