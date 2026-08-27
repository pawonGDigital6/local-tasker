<?php
/**
 * ACF field group registration for WooCommerce products and site-wide options.
 *
 * Registers:
 *  1. "Product Options & Specs" — custom meta for flooring product calculations and specs.
 *  2. "Site Options — Supply CTA" — editable content for the supply+install CTA on single product.
 *  3. "Site Options — Reviews" — site-wide Google review cards shown on single product.
 *  4. "Site Options — Work Together CTA" — editable content for the closing CTA on single product.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

// ── 1. Product Options & Specs ──────────────────────────────────────────────
acf_add_local_field_group( [
	'key'      => 'group_lt_product_options',
	'title'    => 'Product Options & Specs',
	'fields'   => [
		[
			'key'           => 'field_lt_sold_by_box',
			'label'         => 'Sold by the Box',
			'name'          => 'sold_by_box',
			'type'          => 'true_false',
			'instructions'  => 'Enable the box calculator on the product page.',
			'default_value' => 1,
			'ui'            => 1,
		],
		[
			'key'              => 'field_lt_carton_sqm',
			'label'            => 'Carton Coverage (sqm)',
			'name'             => 'carton_sqm',
			'type'             => 'number',
			'instructions'     => 'Square metres covered per box/carton.',
			'append'           => 'sqm',
			'min'              => 0,
			'step'             => 0.01,
			'wrapper'          => [ 'width' => '50' ],
			'conditional_logic' => [
				[ [ 'field' => 'field_lt_sold_by_box', 'operator' => '==', 'value' => '1' ] ],
			],
		],
		[
			'key'           => 'field_lt_install_quote_enabled',
			'label'         => 'Installation Quote',
			'name'          => 'install_quote_enabled',
			'type'          => 'true_false',
			'instructions'  => 'Offer the installation option on this product. The customer only asks for a quote — no cost is added and no price changes. Turn off to hide it for this product.',
			'default_value' => 1,
			'ui'            => 1,
		],
	],
	'location' => [
		[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'product' ] ],
	],
	'menu_order'            => 10,
	'position'              => 'normal',
	'style'                 => 'default',
	'label_placement'       => 'top',
	'instruction_placement' => 'label',
] );

// ── 2. Site Options — Supply CTA ────────────────────────────────────────────
acf_add_local_field_group( [
	'key'    => 'group_lt_supply_cta',
	'title'  => 'Site Options — Supply + Install CTA',
	'fields' => [
		[
			'key'   => 'field_lt_supply_cta_label',
			'label' => 'Label (eyebrow)',
			'name'  => 'supply_cta_label',
			'type'  => 'text',
		],
		[
			'key'   => 'field_lt_supply_cta_heading',
			'label' => 'Heading',
			'name'  => 'supply_cta_heading',
			'type'  => 'text',
		],
		[
			'key'   => 'field_lt_supply_cta_content',
			'label' => 'Content',
			'name'  => 'supply_cta_content',
			'type'  => 'textarea',
			'rows'  => 4,
		],
		[
			'key'          => 'field_lt_supply_cta_button',
			'label'        => 'Button',
			'name'         => 'supply_cta_button',
			'type'         => 'link',
			'return_format'=> 'array',
		],
		[
			'key'          => 'field_lt_supply_cta_bg_image',
			'label'        => 'Background Image (optional)',
			'name'         => 'supply_cta_bg_image',
			'type'         => 'image',
			'return_format'=> 'id',
			'preview_size' => 'medium',
		],
	],
	'location' => [
		[ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'acf-options' ] ],
	],
	'menu_order' => 20,
] );

// ── 3. Site Options — Reviews ───────────────────────────────────────────────
acf_add_local_field_group( [
	'key'    => 'group_lt_site_reviews',
	'title'  => 'Site Options — Customer Reviews',
	'fields' => [
		[
			'key'          => 'field_lt_site_review_link',
			'label'        => 'Google Review Link (Leave a Review URL)',
			'name'         => 'site_review_link',
			'type'         => 'url',
			'instructions' => 'Paste your Google Business review link. Leave blank to hide the button.',
		],
		[
			'key'        => 'field_lt_site_reviews',
			'label'      => 'Reviews',
			'name'       => 'site_reviews',
			'type'       => 'repeater',
			'min'        => 0,
			'max'        => 6,
			'layout'     => 'block',
			'button_label' => 'Add Review',
			'sub_fields' => [
				[
					'key'   => 'field_lt_reviewer_name',
					'label' => 'Reviewer Name',
					'name'  => 'reviewer_name',
					'type'  => 'text',
				],
				[
					'key'   => 'field_lt_review_date',
					'label' => 'Date (e.g. "6 months ago")',
					'name'  => 'review_date',
					'type'  => 'text',
				],
				[
					'key'          => 'field_lt_star_rating',
					'label'        => 'Star Rating',
					'name'         => 'star_rating',
					'type'         => 'number',
					'min'          => 1,
					'max'          => 5,
					'default_value'=> 5,
				],
				[
					'key'   => 'field_lt_review_text',
					'label' => 'Review Text',
					'name'  => 'review_text',
					'type'  => 'textarea',
					'rows'  => 4,
				],
			],
		],
	],
	'location' => [
		[ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'acf-options' ] ],
	],
	'menu_order' => 30,
] );

// ── 4. Site Options — Work Together CTA ────────────────────────────────────
acf_add_local_field_group( [
	'key'    => 'group_lt_work_together',
	'title'  => 'Site Options — Work Together CTA',
	'fields' => [
		[
			'key'   => 'field_lt_work_together_heading',
			'label' => 'Heading',
			'name'  => 'work_together_heading',
			'type'  => 'text',
		],
		[
			'key'   => 'field_lt_work_together_content',
			'label' => 'Content',
			'name'  => 'work_together_content',
			'type'  => 'textarea',
			'rows'  => 4,
		],
		[
			'key'          => 'field_lt_work_together_button',
			'label'        => 'Button',
			'name'         => 'work_together_button',
			'type'         => 'link',
			'return_format'=> 'array',
		],
	],
	'location' => [
		[ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'acf-options' ] ],
	],
	'menu_order' => 40,
] );

// ── Register ACF options page ────────────────────────────────────────────────
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page( [
		'page_title'  => 'Theme Options',
		'menu_title'  => 'Theme Options',
		'menu_slug'   => 'acf-options',
		'capability'  => 'manage_options',
		'redirect'    => false,
		'icon_url'    => 'dashicons-admin-generic',
		'position'    => 60,
	] );
}
