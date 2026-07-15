<?php
/**
 * ACF Block Template: Popular Products
 *
 * Renders a filterable grid of WooCommerce products (top-level `product_cat`
 * tabs, AJAX-powered) ordered by popularity, plus a "View All" link to the
 * shop page. Shares its query engine (`lt_shop_build_query_args()`) and card
 * partial (`woocommerce/content-product.php`) with the rest of the shop, so
 * markup stays consistent everywhere products are listed.
 *
 * @param array $block The block settings and attributes.
 */

if (!defined('ABSPATH')) {
	exit;
}

// Support custom "anchor" values.
$anchor = '';
if (!empty($block['anchor'])) {
	$anchor = 'id="' . esc_attr($block['anchor']) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'acf-block lt-popular-products py-16';

if (!empty($block['className'])) {
	$class_name .= ' ' . $block['className'];
}

if (!empty($block['align'])) {
	$class_name .= ' align' . $block['align'];
}

$class_name .= generate_block_settings_classnames()['class_name'];

// Block Style.
$style_attr = '';
$block_style = generate_block_settings_classnames()['block_style'];

if ($block_style !== '') {
	$style_attr = 'style="' . $block_style . '"';
}

// ─── Unique Block Instance ID ─────────────────────────────────────────────────
// Lets the JS scope every selector to THIS block, so multiple instances on one
// page never conflict.
$block_id = !empty($block['id']) ? $block['id'] : 'ppf-' . wp_unique_id();

// ─── ACF Fields ───────────────────────────────────────────────────────────────
$pp_sec_title      = get_field('pp_sec_title') ?: __('Popular Products', 'local-tasker');
$pp_posts_per_page  = (int) get_field('pp_posts_per_page') ?: 8;
$pp_view_all_url    = get_field('pp_view_all_url') ?: get_permalink(wc_get_page_id('shop'));

$nonce = wp_create_nonce('ppf_ajax_nonce');

// ─── Category Tabs ────────────────────────────────────────────────────────────
// Top-level product categories, same source used by the shop archive toolbar.
$product_cats = class_exists('WooCommerce') && function_exists('lt_shop_get_categories')
	? lt_shop_get_categories()
	: [];

// ─── Initial Query ────────────────────────────────────────────────────────────
$initial_query = new WP_Query([]);

if (class_exists('WooCommerce') && function_exists('lt_shop_build_query_args')) {
	$initial_state = lt_shop_parse_request([
		'per_page' => $pp_posts_per_page,
		'orderby'  => 'popularity',
	]);
	$initial_query = new WP_Query(lt_shop_build_query_args($initial_state));
}
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>
	data-block-id="<?php echo esc_attr($block_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"
	data-posts-per-page="<?php echo esc_attr($pp_posts_per_page); ?>">
	<div class="container">
		<!-- Section Header -->
		<div class="text-center flex flex-col items-center sm:gap-[30px] gap-6 sm:mb-[45px] mb-6">
			<?php if ($pp_sec_title): ?>
				<h2
					class="sec-title font-semi-ext font-bold text-lt-onyx sm:text-[2.5rem] text-2xl sm:leading-[1.375] leading-8 sm:tracking-[-1px] tracking-normal">
					<?php echo esc_html($pp_sec_title); ?>
				</h2>
			<?php endif; ?>
			<!-- Category Filter -->
			<div
				class="filter-holder w-full overflow-auto scrollbar-thin scrollbar-thumb-[#0a65fc78] scrollbar-track-lt-snow-drift max-md:mr-[-32px] max-md:w-[calc(100%+32px)] max-sm:mr-[-16px] max-sm:w-[calc(100%+16px)]">
				<ul class="th-filter filter-popular-products flex justify-center sm:gap-20 gap-6 m-0 whitespace-nowrap sm:pb-0 pb-4"
					role="list">
					<li>
						<button class="filter-btn active" data-category=""
							aria-pressed="true"><?php esc_html_e('All', 'local-tasker'); ?></button>
					</li>
					<?php if (!empty($product_cats)): ?>
						<?php foreach ($product_cats as $term): ?>
							<li>
								<button class="filter-btn" data-category="<?php echo esc_attr($term->slug); ?>" aria-pressed="false">
									<?php echo esc_html($term->name); ?>
								</button>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div><!-- End Category Filter -->
		</div><!-- End Section Header -->
		<!-- Product Grid — same card markup/classes as the WooCommerce Shop Loop, but 4 columns on desktop -->
		<ul class="popular-products-list lt-products-grid grid grid-cols-2 md:grid-cols-3 wd:grid-cols-4 gap-x-4 gap-y-6 wd:gap-[55px]" aria-live="polite" aria-busy="false">
			<?php
			if ($initial_query->have_posts()):
				while ($initial_query->have_posts()):
					$initial_query->the_post();
					wc_get_template_part('content', 'product');
				endwhile;
			else:
				echo '<li class="col-span-full text-center text-lt-text-placeholder py-8">'
					. esc_html__('No products found.', 'local-tasker')
					. '</li>';
			endif;

			// Restore the main query globals after our custom loop.
			wp_reset_postdata();
			?>
		</ul><!-- End Product Grid -->
		<!-- View All -->
		<div class="ld-btn-wrap flex justify-center sm:mt-[45px] mt-10">
			<a href="<?php echo esc_url($pp_view_all_url); ?>" class="btn btn--brand cursor-pointer max-sm:w-full"
				aria-label="<?php esc_attr_e('View all products', 'local-tasker'); ?>">
				<?php esc_html_e('View All', 'local-tasker'); ?>
			</a>
		</div><!-- End View All -->
	</div>
</section>
