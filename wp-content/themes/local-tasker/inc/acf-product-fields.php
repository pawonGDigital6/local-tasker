<?php
/**
 * ACF field group registration for WooCommerce products and site-wide options.
 *
 * Registers:
 *  1. "Product Options & Specs" — custom meta for flooring product calculations and specs.
 *  2. "Site Options — Supply CTA" — editable content for the supply+install CTA on single product.
 *  3. "Site Options — Reviews" — site-wide Google review cards shown on single product.
 *  4. "Site Options — Calculator" — site-wide defaults for the box / area calculators.
 *  5. "Site Options — Work Together CTA" — editable content for the closing CTA on single product.
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
			// Coverage is supplied to 3 decimals (e.g. 2.085 sqm); a 0.01 step made the
			// admin form round that on every save, which moved the box price.
			'step'             => 0.001,
			'wrapper'          => [ 'width' => '50' ],
			'conditional_logic' => [
				[ [ 'field' => 'field_lt_sold_by_box', 'operator' => '==', 'value' => '1' ] ],
			],
		],
		[
			// Per-product override. Blank falls back to the site-wide default in
			// Theme Options, so most products never need touching.
			'key'               => 'field_lt_wastage_percent',
			'label'             => 'Wastage Allowance (%)',
			'name'              => 'wastage_percent',
			'type'              => 'number',
			'instructions'      => 'Percentage added when the customer ticks "add wastage" on the box calculator. Leave blank to use the site default set under Theme Option → Site Options — Calculator. Enter 0 to hide the wastage option on this product.',
			'append'            => '%',
			'min'               => 0,
			'max'               => 100,
			'step'              => 0.5,
			'placeholder'       => 'Site default',
			'wrapper'           => [ 'width' => '50' ],
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
		[
			// Off by default: only products switched on here show "Order Sample".
			'key'           => 'field_lt_sample_enabled',
			'label'         => 'Offer Samples',
			'name'          => 'sample_enabled',
			'type'          => 'true_false',
			'instructions'  => 'Show the "Order Sample" button on this product. Samples are free; the customer pays the sample delivery charge set under Theme Option → Site Options — Samples, which is dropped when the order also includes flooring.',
			'default_value' => 0,
			'ui'            => 1,
		],
		[
			// Display label only — never feeds into any price calculation.
			'key'           => 'field_lt_price_unit',
			'label'         => 'Price Unit Label',
			'name'          => 'price_unit',
			'type'          => 'select',
			'instructions'  => 'Text shown after the price (e.g. "$12.00 / sqm"). Only changes the label — the price itself is unaffected.',
			'choices'       => [
				'sqm'    => '/ sqm (default)',
				'unit'   => '/ unit',
				'each'   => '/ each',
				'none'   => 'No label',
				'custom' => 'Custom…',
			],
			'default_value' => 'sqm',
			'return_format' => 'value',
			'wrapper'       => [ 'width' => '50' ],
		],
		[
			'key'               => 'field_lt_price_unit_custom',
			'label'             => 'Custom Unit Label',
			'name'              => 'price_unit_custom',
			'type'              => 'text',
			'instructions'      => 'e.g. "tub", "roll", "litre". Shown as "/ tub".',
			'maxlength'         => 20,
			'wrapper'           => [ 'width' => '50' ],
			'conditional_logic' => [
				[ [ 'field' => 'field_lt_price_unit', 'operator' => '==', 'value' => 'custom' ] ],
			],
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
		[ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'theme-option' ] ],
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
		[ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'theme-option' ] ],
	],
	'menu_order' => 30,
] );

// ── 4. Site Options — Calculator ────────────────────────────────────────────
acf_add_local_field_group( [
	'key'    => 'group_lt_calculator_options',
	'title'  => 'Site Options — Calculator',
	'fields' => [
		[
			'key'           => 'field_lt_default_wastage_percent',
			'label'         => 'Default Wastage Allowance (%)',
			'name'          => 'default_wastage_percent',
			'type'          => 'number',
			'instructions'  => 'Percentage added when the customer ticks "add wastage" on the box calculator and the area calculator. Individual products can override this under Product Options & Specs. Enter 0 to hide the wastage option site-wide.',
			'append'        => '%',
			'min'           => 0,
			'max'           => 100,
			'step'          => 0.5,
			'default_value' => 10,
			'wrapper'       => [ 'width' => '50' ],
		],
	],
	'location' => [
		[ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'theme-option' ] ],
	],
	'menu_order' => 10,
] );

// ── 4b. Site Options — Samples ──────────────────────────────────────────────
acf_add_local_field_group( [
	'key'    => 'group_lt_sample_options',
	'title'  => 'Site Options — Samples',
	'fields' => [
		[
			'key'           => 'field_lt_samples_enabled',
			'label'         => 'Enable Order Sample',
			'name'          => 'samples_enabled',
			'type'          => 'true_false',
			'instructions'  => 'Site-wide switch for the "Order Sample" button. When off, no product shows it (whatever its own "Offer Samples" setting) and samples already in carts are removed.',
			'ui'            => 1,
			'default_value' => 1,
		],
		[
			'key'           => 'field_lt_sample_delivery_fee',
			'label'         => 'Sample Delivery Charge',
			'name'          => 'sample_delivery_fee',
			'type'          => 'number',
			'instructions'  => 'Charged on orders that contain only samples. Dropped automatically when the order also includes flooring (normal delivery applies instead). Samples are delivered only to areas with a delivery rate.',
			'prepend'       => '$',
			'min'           => 0,
			'step'          => 0.01,
			'default_value' => 15,
			'wrapper'       => [ 'width' => '50' ],
		],
		[
			'key'           => 'field_lt_sample_max_per_order',
			'label'         => 'Maximum Samples per Order',
			'name'          => 'sample_max_per_order',
			'type'          => 'number',
			'min'           => 1,
			'step'          => 1,
			'default_value' => 3,
			'wrapper'       => [ 'width' => '50' ],
		],
	],
	'location' => [
		[ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'theme-option' ] ],
	],
	'menu_order' => 11,
] );

// ── 5. Site Options — Work Together CTA ────────────────────────────────────
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
		[ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'theme-option' ] ],
	],
	'menu_order' => 40,
] );

// The Site Options groups above sit on the "Theme Option" page, which is
// registered in ACF (acf-json/ui_options_page_6a2f7a74dcd74.json) alongside
// Global Settings. Both store values as options, so get_field( ..., 'option' )
// reads the same data whichever page saved it.
