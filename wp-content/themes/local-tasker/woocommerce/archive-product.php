<?php
/**
 * Shop archive — product listing with sidebar filters and results grid.
 *
 * Layout supports ACF blocks placed above and below the WC loop via the
 * "WooCommerce Shop Loop" placeholder block (acf-block/wc-shop-loop) in the
 * block editor on the Shop page.
 *
 * @package local-tasker
 */

defined('ABSPATH') || exit;

// Filter pill taxonomy map. 'term' must be the REAL product_cat slug — pulled
// from lt_shop_pill_category_map() (inc/woocommerce.php) so this can never drift
// out of sync with the AJAX/query layer that uses the same map again.
$lt_pill_cat_map = function_exists('lt_shop_pill_category_map') ? lt_shop_pill_category_map() : [];
$lt_filter_pills = [
	'all' => ['label' => __('All', 'local-tasker')],
	'on-sale' => ['label' => __('On Sale', 'local-tasker')],
	'new-arrivals' => ['label' => __('New Arrivals', 'local-tasker')],
	// 'in-stock' => ['label' => __('In Stock', 'local-tasker')],
	// 'spc-hybrid' => ['label' => __('SPC Hybrid', 'local-tasker'), 'tax' => 'product_cat', 'term' => $lt_pill_cat_map['spc-hybrid'] ?? ''],
	// 'engineered' => ['label' => __('Engineered', 'local-tasker'), 'tax' => 'product_cat', 'term' => $lt_pill_cat_map['engineered'] ?? ''],
	// 'porcelain' => ['label' => __('Porcelain', 'local-tasker'), 'tax' => 'product_cat', 'term' => $lt_pill_cat_map['porcelain'] ?? ''],
];

$active_pill = isset($_GET['filter']) ? sanitize_key($_GET['filter']) : 'all';
if (!array_key_exists($active_pill, $lt_filter_pills)) {
	$active_pill = 'all';
}

// On taxonomy archive pages (e.g. /product-category/spc-hybrid-flooring/, or any
// of its sub-categories), there is no ?filter= GET param. Map the queried term
// (or any of its ancestor terms) to the pill key so the correct pill is
// highlighted on initial load. Uses real term hierarchy, not slug-string guessing,
// so it correctly matches sub-categories regardless of slug naming pattern
// (e.g. "9mm-spc-hybrid-flooring" is a child of "spc-hybrid-flooring" but doesn't
// share a string prefix with it).
$lt_archive_cat_slug = ''; // The current archive's term slug (for JS sync).
if ($active_pill === 'all' && function_exists('is_product_category') && is_product_category()) {
	$lt_queried = get_queried_object();
	if ($lt_queried instanceof WP_Term) {
		$lt_archive_cat_slug = $lt_queried->slug;
		$lt_ancestor_ids = get_ancestors($lt_queried->term_id, 'product_cat', 'taxonomy');
		foreach ($lt_filter_pills as $key => $pill) {
			if (empty($pill['tax']) || empty($pill['term'])) {
				continue;
			}
			$pill_term = get_term_by('slug', $pill['term'], 'product_cat');
			if (!$pill_term) {
				continue;
			}
			if ($lt_queried->term_id === $pill_term->term_id || in_array($pill_term->term_id, $lt_ancestor_ids, true)) {
				$active_pill = $key;
				break;
			}
		}
	}
}

// Any filter active — quick pill or sidebar (category, price, colour, thickness).
$lt_has_any_active_filter = $active_pill !== 'all'
	|| !empty($_GET['product_cat'])
	|| (isset($_GET['min_price']) && $_GET['min_price'] !== '')
	|| (isset($_GET['max_price']) && $_GET['max_price'] !== '')
	|| !empty($_GET['filter_colour'])
	|| !empty($_GET['filter_thickness']);

$lt_clear_all_url = remove_query_arg(['filter', 'product_cat', 'min_price', 'max_price', 'filter_colour', 'filter_thickness', 'paged']);

global $wp_query;
$result_count = $wp_query ? $wp_query->found_posts : 0;

// ── Resolve which page's blocks power this archive ──────────────────────────
// Category archives can override the default Shop page content by assigning
// a "Content Page" via ACF (taxonomy_lt6b01a2e3f401, field lt_category_content_page,
// location: taxonomy == product_cat). That page should be built with the same
// acf-block/wc-shop-loop placeholder pattern as the Shop page. Falls back to
// the Shop page when no override is assigned (or we're not on a category archive).
$content_page_id = wc_get_page_id('shop');
if (function_exists('is_product_category') && is_product_category()) {
	$lt_cat_term = get_queried_object();
	if ($lt_cat_term instanceof WP_Term && function_exists('get_field')) {
		$lt_cat_content_page_id = get_field('lt_category_content_page', $lt_cat_term);
		if ($lt_cat_content_page_id) {
			$content_page_id = (int) $lt_cat_content_page_id;
		}
	}
}

// ── Parse content page blocks, split around wc-shop-loop placeholder ───────
$shop_content = get_post_field('post_content', $content_page_id);
$all_blocks = parse_blocks($shop_content);

$loop_index = null;
foreach ($all_blocks as $i => $blk) {
	if (($blk['blockName'] ?? '') === 'acf-block/wc-shop-loop') {
		$loop_index = $i;
		break;
	}
}

// Blocks before placeholder render above the WC section; blocks after render below.
// If the placeholder hasn't been placed yet, all page blocks fall below the loop.
$blocks_before = $loop_index !== null ? array_slice($all_blocks, 0, $loop_index) : [];
$blocks_after = $loop_index !== null ? array_slice($all_blocks, $loop_index + 1) : $all_blocks;

get_header();

// ── Blocks above the shop loop (e.g. ACF inner-hero, CTA, etc.) ────────────
foreach ($blocks_before as $block) {
	echo render_block($block); // phpcs:ignore WordPress.Security.EscapeOutput
}
?>
<main id="primary" class="site-main lt-shop-archive bg-lt-white md:bg-[#f7f7f5]">
	<!-- ── Results bar ─────────────────────────────────────── -->
	<div class="lt-shop-archive__bar">
		<div class="container">
			<?php get_template_part('template-parts/components/breadcrumb'); ?>
			<div class="flex flex-wrap justify-between items-center gap-x-4 md:gap-y-5 gap-y-6">
				<!-- Title + count -->
				<div class="flex items-center md:gap-[5px] gap-3 shrink-0">
					<h1
						class="sm:text-[24px] text-[20px] leading-none font-bold text-[#0a0d1a] m-0 font-semi-ext tracking-[-0.48px]">
						<?php esc_html_e('All Products', 'local-tasker'); ?>
					</h1>
					<span
						class="inline-flex items-center h-6 px-3 rounded-full bg-lt-white border border-[#e5e5df] sm:text-[13px] text-caption-xs font-semibold text-[#6b7280] shrink-0"
						aria-live="polite" data-lt-result-count>
						<?php
						printf(
							/* translators: %d: product count */
							esc_html(_n('%d result', '%d results', $result_count, 'local-tasker')),
							$result_count
						);
						?>
					</span>
				</div>
				<div class="right-navs flex md:justify-end items-start wd:w-[72.9%] gap-4 max-md:flex-wrap w-full">
					<!-- Filter pills -->
					<nav class="lt-filter-pills md:flex-1 overflow-auto scrollbar-thin scrollbar-thumb-[#0a65fc78] scrollbar-track-lt-snow-drift max-md:mr-[-32px] max-md:w-[calc(100%+32px)] max-sm:mr-[-16px] max-sm:w-[calc(100%+16px)] max-sm:pb-3"
						aria-label="<?php esc_attr_e('Quick filters', 'local-tasker'); ?>"
						data-lt-archive-cat="<?php echo esc_attr($lt_archive_cat_slug); ?>">
						<div class="holder flex md:flex-wrap items-center gap-2">
							<?php foreach ($lt_filter_pills as $key => $pill):
								$is_active = $active_pill === $key;
								$pill_url = $key === 'all'
									? remove_query_arg('filter')
									: add_query_arg('filter', $key);
								?>
								<a href="<?php echo esc_url($pill_url); ?>" data-lt-pill="<?php echo esc_attr($key); ?>"
									<?php if (!empty($pill['term'])): ?>data-lt-cat="<?php echo esc_attr($pill['term']); ?>"
									<?php endif; ?>
									class="lt-filter-pill inline-flex items-center h-8 px-4 rounded-full border text-[13px] font-semibold whitespace-nowrap transition-colors duration-200 last:mr-4 <?php echo $is_active ? 'bg-lt-brand text-lt-white border-[#0a0d1a]' : 'bg-lt-white text-[#6b7280] border-[#e5e5df] hover:border-lt-brand hover:text-lt-brand'; ?>"
									aria-current="<?php echo $is_active ? 'true' : 'false'; ?>">
									<?php echo esc_html($pill['label']); ?>
								</a>
							<?php endforeach; ?>
							<a href="<?php echo esc_url($lt_clear_all_url); ?>" id="lt-clear-pill"
								class="hidden! lt-filter-pill lt-filter-pill--clear text-[13px] font-semibold text-[#f26522] hover:underline shrink-0<?php echo $lt_has_any_active_filter ? '' : ' hidden'; ?>">
								<?php esc_html_e('Clear All Filters', 'local-tasker'); ?>
							</a>
						</div>
					</nav>
					<!-- Sort -->
					<div class="lt-shop-sort flex items-center gap-2 shrink-0 max-md:w-full max-md:justify-end">
						<label for="lt-sort-select" class="text-[13px] text-[#6b7280] shrink-0">
							<?php esc_html_e('Sort:', 'local-tasker'); ?>
						</label>
						<div class="relative">
							<select id="lt-sort-select"
								class="lt-shop-sort__select appearance-none h-9 bg-lt-white border border-[#e5e5df] rounded-md text-[13px] font-semibold text-[#0a0d1a] pl-3 pr-8 cursor-pointer focus:outline-none focus:ring-2 focus:ring-lt-brand/30 focus:border-lt-brand"
								aria-label="<?php esc_attr_e('Sort products', 'local-tasker'); ?>">
								<?php
								$current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
								$orderby_options = apply_filters('woocommerce_catalog_orderby', [
									'menu_order' => __('Default Sorting', 'local-tasker'),
									'popularity' => __('Best Selling', 'local-tasker'),
									'date' => __('Latest', 'local-tasker'),
									'price' => __('Price: Low to High', 'local-tasker'),
									'price-desc' => __('Price: High to Low', 'local-tasker'),
									'rating' => __('Average Rating', 'local-tasker'),
								]);
								foreach ($orderby_options as $id => $name):
									?>
									<option value="<?php echo esc_attr($id); ?>" <?php selected($current_orderby, $id); ?>>
										<?php echo esc_html($name); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-lt-text-muted"
								viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- /.lt-shop-archive__bar -->
	<!-- ── Main content: sidebar + grid ───────────────────── -->
	<div class="container md:pt-[57px] pt-8 md:pb-[86px] pb-16 max-md:mt-[-66px]">
		<div class="lt-shop-archive__layout flex flex-wrap wd:gap-[7.7%] md:gap-[4%] gap-0 items-start">
			<!-- ── Sidebar (Filters) ── -->
			<?php get_template_part('woocommerce/partials/shop-filters'); ?>
			<!-- ── Product grid ── -->
			<div class="lt-shop-archive__main md:flex-1 md:min-w-0" id="lt-product-grid">
				<?php if (woocommerce_product_loop()): ?>
					<?php woocommerce_product_loop_start(); ?>
					<?php
					while (have_posts()) {
						the_post();
						wc_get_template_part('content', 'product');
					}
					?>
					<?php woocommerce_product_loop_end(); ?>
					<?php
					global $wp_query;
					$lt_max_pages = (int) $wp_query->max_num_pages;
					$lt_cur_page = max(1, (int) get_query_var('paged'));
					$lt_per_page = (int) $wp_query->get('posts_per_page');
					?>
					<!-- Load more (AJAX) — falls back to pagination without JS -->
					<div class="lt-shop-loadmore-wrap<?php echo $lt_cur_page >= $lt_max_pages ? ' is-hidden' : ''; ?>"
						data-lt-loadmore data-page="<?php echo esc_attr($lt_cur_page); ?>"
						data-max-pages="<?php echo esc_attr($lt_max_pages); ?>"
						data-per-page="<?php echo esc_attr($lt_per_page); ?>"
						data-nonce="<?php echo esc_attr(wp_create_nonce('lt_shop_load')); ?>">
						<button type="button" class="lt-load-more" data-lt-loadmore-btn>
							<span class="lt-load-more__label">
								<?php esc_html_e('Load More Products', 'local-tasker'); ?>
							</span>
							<span class="lt-load-more__spinner" aria-hidden="true"></span>
						</button>
					</div>
					<noscript>
						<div class="lt-shop-archive__pagination mt-10">
							<?php woocommerce_pagination(); ?>
						</div>
					</noscript>
				<?php else: ?>
					<div class="lt-shop-archive__empty py-20 text-center">
						<p class="text-body-lg text-lt-text-muted">
							<?php esc_html_e('No products found.', 'local-tasker'); ?>
						</p>
						<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn--brand mt-6">
							<?php esc_html_e('Back to Shop', 'local-tasker'); ?>
						</a>
					</div>
				<?php endif; ?>
			</div><!-- /#lt-product-grid -->
		</div><!-- /.lt-shop-archive__layout -->
	</div>
</main>
<?php
// ── Blocks below the shop loop (e.g. ACF CTA, promo strips, etc.) ─────────
foreach ($blocks_after as $block) {
	echo render_block($block); // phpcs:ignore WordPress.Security.EscapeOutput
}

get_footer();