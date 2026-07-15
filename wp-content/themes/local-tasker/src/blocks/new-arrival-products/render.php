<?php
/**
 * ACF Block Template: New Arrival Products
 *
 * Renders a grid of the newest WooCommerce products plus an "Explore" link to
 * the shop page. Mirrors the Popular Products block, minus the category tabs
 * (the design has none, so no AJAX filtering is needed here) and ordered by
 * date instead of popularity.
 *
 * Shares its query engine (`lt_shop_build_query_args()`) and card partial
 * (`woocommerce/content-product.php`) with the rest of the shop, so markup
 * stays consistent everywhere products are listed.
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
$class_name = 'acf-block lt-new-arrival-products py-16';

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

// ─── ACF Fields ───────────────────────────────────────────────────────────────
$na_sec_title = get_field('na_sec_title') ?: __('New Arrivals', 'local-tasker');
$na_posts_per_page = (int) get_field('na_posts_per_page') ?: 4;
$na_view_all_url = get_field('na_view_all_url') ?: get_permalink(wc_get_page_id('shop'));

// ─── Query ────────────────────────────────────────────────────────────────────
// Newest first — the shared shop engine already supports `orderby => 'date'`.
$initial_query = new WP_Query([]);

if (class_exists('WooCommerce') && function_exists('lt_shop_build_query_args')) {
	$initial_state = lt_shop_parse_request([
		'per_page' => $na_posts_per_page,
		'orderby' => 'date',
	]);
	$initial_query = new WP_Query(lt_shop_build_query_args($initial_state));
}
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="container">
		<!-- Section Header -->
		<?php if ($na_sec_title): ?>
			<div class="text-center flex flex-col items-center sm:mb-[45px] mb-6">
				<h2
					class="sec-title font-semi-ext font-bold text-lt-onyx sm:text-[2.5rem] text-2xl sm:leading-[1.375] leading-8 sm:tracking-[-1px] tracking-normal">
					<?php echo esc_html($na_sec_title); ?>
				</h2>
			</div><!-- End Section Header -->
		<?php endif; ?>
		<!-- Product Grid — same card markup/classes as the WooCommerce Shop Loop -->
		<ul
			class="new-arrival-products-list lt-products-grid grid grid-cols-2 md:grid-cols-3 wd:grid-cols-4 gap-x-4 gap-y-6 wd:gap-[55px]">
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
		<?php
		$link = get_field('nap_btbn');
		if ($link):
			$link_url = $link['url'];
			$link_title = $link['title'];
			$link_target = $link['target'] ? $link['target'] : '_self';
			?>
			<!-- Explore -->
			<div class="ld-btn-wrap flex justify-center sm:mt-[45px] mt-10">
				<a class="btn btn--brand" href="<?php echo esc_url($link_url); ?>"
					target="<?php echo esc_attr($link_target); ?>">
					<?php echo esc_html($link_title); ?>
				</a>
			</div><!-- End Explore -->
		<?php endif; ?>
	</div>
</section>