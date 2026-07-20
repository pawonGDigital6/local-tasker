<?php
/**
 * Filters sidebar — Category (single-select), Price Range, Colour/Finish, Thickness.
 * Desktop: persistent column. Mobile: off-canvas drawer triggered by a button.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

// Active filter values from query string (sanitised).
$active_cat        = isset( $_GET['product_cat'] ) ? sanitize_text_field( (string) ( is_array( $_GET['product_cat'] ) ? reset( $_GET['product_cat'] ) : $_GET['product_cat'] ) ) : '';

// On taxonomy archive pages (e.g. /product-category/spc-hybrid-flooring/), the
// category is encoded in the URL path — there is no ?product_cat= param. Detect
// this and use the queried term slug as the active category so the sidebar radio
// is pre-selected when a user lands directly on a category archive URL.
if ( $active_cat === '' && function_exists( 'is_product_category' ) && is_product_category() ) {
	$queried = get_queried_object();
	if ( $queried instanceof WP_Term ) {
		$active_cat = $queried->slug;
	}
}

$price_min         = ( isset( $_GET['min_price'] ) && $_GET['min_price'] !== '' ) ? (float) $_GET['min_price'] : '';
$price_max         = ( isset( $_GET['max_price'] ) && $_GET['max_price'] !== '' ) ? (float) $_GET['max_price'] : '';
$active_colours    = isset( $_GET['filter_colour'] ) ? array_map( 'sanitize_text_field', (array) $_GET['filter_colour'] ) : [];
$active_thickness  = isset( $_GET['filter_thickness'] ) ? array_map( 'sanitize_text_field', (array) $_GET['filter_thickness'] ) : [];
$active_pill_get   = isset( $_GET['filter'] ) ? sanitize_key( $_GET['filter'] ) : '';

$has_active_filters = $active_cat !== '' || $price_min !== '' || $price_max !== '' || ! empty( $active_colours ) || ! empty( $active_thickness ) || ( $active_pill_get !== '' && $active_pill_get !== 'all' );

// Helper: build a filter URL preserving current query minus pagination.
function lt_filter_url( array $params ): string {
	$base = remove_query_arg( 'paged' );
	return add_query_arg( array_map( 'rawurlencode', $params ), $base );
}

// Fetch product categories with counts.
$categories = get_terms( [
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'orderby'    => 'count',
	'order'      => 'DESC',
] );

// Fetch pa_colour attribute terms (display attribute).
$colour_terms = get_terms( [ 'taxonomy' => 'pa_colour', 'hide_empty' => true ] );
if ( is_wp_error( $colour_terms ) ) { $colour_terms = []; }

// Fetch pa_thickness attribute terms.
$thickness_terms = get_terms( [ 'taxonomy' => 'pa_thickness', 'hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC' ] );
if ( is_wp_error( $thickness_terms ) ) { $thickness_terms = []; }

$clear_url = remove_query_arg( [ 'product_cat', 'min_price', 'max_price', 'filter_colour', 'filter_thickness', 'filter', 'paged' ] );

// Total published products (used for "All Flooring" count label).
$total_products_obj = wp_count_posts( 'product' );
$total_products_count = isset( $total_products_obj->publish ) ? (int) $total_products_obj->publish : 0;

// "Showing X-Y of Z" — initial values from the archive query. The Load-More /
// filter JS keeps these live afterwards (see js/shop-archive.js).
global $wp_query;
$lt_result_total = ( $wp_query instanceof WP_Query ) ? (int) $wp_query->found_posts : 0;
$lt_result_shown = ( $wp_query instanceof WP_Query ) ? (int) $wp_query->post_count : 0;
$lt_result_range = $lt_result_shown > 0 ? '1-' . $lt_result_shown : '0';
?>

<!-- Mobile: filter toggle button (hidden on desktop) -->
<div class="button-count-holder md:hidden block mb-11">
	<button
		type="button"
		id="lt-filter-open"
		class="lt-filter-open md:hidden inline-flex items-center justify-center gap-2 h-[30px] px-4 rounded-[20px] border border-[#e5e7eb] bg-lt-white text-[13px] font-semibold text-[#1e2939]"
		aria-expanded="false"
		aria-controls="lt-filters-sidebar"
	>
		<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
			<g clip-path="url(#clip0_50513_1204)">
			<path d="M12.2477 2.33301H8.16504" stroke="#1E2939" stroke-width="1.16648" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M5.83269 2.33301H1.75" stroke="#1E2939" stroke-width="1.16648" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M12.2482 6.99902H6.99902" stroke="#1E2939" stroke-width="1.16648" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M4.66621 6.99902H1.75" stroke="#1E2939" stroke-width="1.16648" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M12.2482 11.665H9.33203" stroke="#1E2939" stroke-width="1.16648" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M6.99917 11.665H1.75" stroke="#1E2939" stroke-width="1.16648" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M8.16504 1.16602V3.49898" stroke="#1E2939" stroke-width="1.16648" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M4.66602 5.83203V8.165" stroke="#1E2939" stroke-width="1.16648" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M9.33203 10.498V12.831" stroke="#1E2939" stroke-width="1.16648" stroke-linecap="round" stroke-linejoin="round"/>
			</g>
			<defs>
			<clipPath id="clip0_50513_1204">
			<rect width="13.9978" height="13.9978" fill="white"/>
			</clipPath>
			</defs>
		</svg>

		<?php esc_html_e( 'Filters', 'local-tasker' ); ?>
		<?php if ( $has_active_filters ) : ?>
			<span class="lt-filter-count bg-lt-brand text-lt-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center" aria-label="<?php esc_attr_e( 'Active filters', 'local-tasker' ); ?>">
				<?php echo esc_html( ( $active_cat !== '' ? 1 : 0 ) + count( $active_colours ) + count( $active_thickness ) + ( $price_min !== '' || $price_max !== '' ? 1 : 0 ) ); ?>
			</span>
		<?php endif; ?>
	</button>
	<!-- Span Coount Result -->
	<span class="count-result text-caption-sm text-[#6A7282] block mt-4">
		Showing 
		<span class="count"><?php echo esc_html( $lt_result_range ); ?></span> of <span class="total"><?php echo esc_html( $lt_result_total ); ?></span>
	</span>
 </div>
<!-- Mobile: overlay -->
<div id="lt-filter-overlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" aria-hidden="true"></div>

<!-- Sidebar -->
<aside
	id="lt-filters-sidebar"
	class="lt-shop-filters
		w-[240px] shrink-0
		max-md:fixed max-md:inset-y-0 max-md:left-0 max-md:z-50 max-md:w-[300px] max-md:max-w-[85vw]
		max-md:bg-lt-white max-md:overflow-y-auto max-md:shadow-2xl
		max-md:-translate-x-full max-md:transition-transform max-md:duration-300
	"
	aria-label="<?php esc_attr_e( 'Product filters', 'local-tasker' ); ?>"
>
	<form
		id="lt-filter-form"
		method="get"
		action="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
		class="p-0 md:p-0"
	>
		<!-- Mobile: sidebar header -->
		<div class="md:hidden flex items-center justify-between px-5 py-4 border-b border-[#E9EAEC]">
			<span class="text-body font-bold text-lt-text-primary font-semi-ext"><?php esc_html_e( 'FILTERS', 'local-tasker' ); ?></span>
			<button type="button" id="lt-filter-close" class="p-1 rounded hover:bg-lt-snow-drift transition-colors" aria-label="<?php esc_attr_e( 'Close filters', 'local-tasker' ); ?>">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
				</svg>
			</button>
		</div>

		<div class="p-5 md:p-0 flex flex-col gap-4">

			<!-- Heading (desktop) — divider under the title comes from lt-storefront.css -->
			<div class="lt-filters-title hidden md:block">
				<p class="font-semi-ext text-[12px] font-bold text-[#131313] tracking-[0.72px] uppercase m-0">
					<?php esc_html_e( 'FILTERS', 'local-tasker' ); ?>
				</p>
			</div>

			<!-- ── Category ── -->
			<div class="lt-filter-group" data-filter-group="category">
				<button
					type="button"
					class="lt-filter-group__toggle w-full flex items-center justify-between text-left gap-2 group"
					aria-expanded="true"
					aria-controls="filter-category-body"
				>
					<span class="font-semi-ext text-[13px] font-bold text-[#0a0d1a]"><?php esc_html_e( 'Category', 'local-tasker' ); ?></span>
					<svg class="w-4 h-4 text-lt-text-muted shrink-0 transition-transform duration-200 group-aria-[expanded=false]:rotate-180 mr-[-2px]" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<ul id="filter-category-body" class="mt-3 flex flex-col gap-2 list-none p-0 m-0">
					<!-- All option -->
					<li>
						<label class="flex items-center justify-between gap-2 cursor-pointer group/label">
							<span class="flex items-center gap-2">
								<input
									type="radio"
									name="product_cat"
									value=""
									class="lt-filter-checkbox sr-only peer"
									<?php checked( $active_cat === '' ); ?>
									data-all-cats
								>
								<span class="lt-filter-checkbox__ui w-4 h-4 rounded-full border border-[#D1D5DB] flex items-center justify-center shrink-0 peer-checked:bg-lt-brand peer-checked:border-lt-brand transition-colors duration-150" aria-hidden="true">
									<span class="hidden peer-checked:block w-1.5 h-1.5 rounded-full bg-lt-white"></span>
								</span>
								<span class="text-[13.5px] text-[#374151] peer-checked:text-lt-text-primary group-hover/label:text-lt-brand transition-colors duration-150">
									<?php esc_html_e( 'All Flooring', 'local-tasker' ); ?>
								</span>
							</span>
							<span class="text-[11px] text-[#6b7280] shrink-0"><?php echo esc_html( $total_products_count ); ?></span>
						</label>
					</li>
					<?php if ( ! is_wp_error( $categories ) ) :
						foreach ( $categories as $cat ) :
							$is_checked = $active_cat === $cat->slug;
					?>
						<li>
							<label class="flex items-center justify-between gap-2 cursor-pointer group/label">
								<span class="flex items-center gap-2">
									<input
										type="radio"
										name="product_cat"
										value="<?php echo esc_attr( $cat->slug ); ?>"
										class="lt-filter-checkbox sr-only peer"
										<?php checked( $is_checked ); ?>
									>
									<span class="lt-filter-checkbox__ui w-4 h-4 rounded-full border border-[#D1D5DB] flex items-center justify-center shrink-0 peer-checked:bg-lt-brand peer-checked:border-lt-brand transition-colors duration-150" aria-hidden="true">
										<span class="hidden peer-checked:block w-1.5 h-1.5 rounded-full bg-lt-white"></span>
									</span>
									<span class="text-[13.5px] text-[#374151] peer-checked:text-lt-text-primary group-hover/label:text-lt-brand transition-colors duration-150">
										<?php echo esc_html( $cat->name ); ?>
									</span>
								</span>
								<span class="text-[11px] text-[#6b7280] shrink-0"><?php echo esc_html( $cat->count ); ?></span>
							</label>
						</li>
					<?php endforeach; endif; ?>
				</ul>
			</div><!-- /.lt-filter-group category -->

			<div class="h-px bg-[#E9EAEC]"></div>

			<!-- ── Price Range ── -->
			<div class="lt-filter-group" data-filter-group="price">
				<button
					type="button"
					class="lt-filter-group__toggle w-full flex items-center justify-between text-left gap-2 group"
					aria-expanded="true"
					aria-controls="filter-price-body"
				>
					<span class="font-semi-ext text-[13px] font-bold text-[#0a0d1a]"><?php esc_html_e( 'Price Range', 'local-tasker' ); ?></span>
					<svg class="w-4 h-4 text-lt-text-muted shrink-0 transition-transform duration-200 group-aria-[expanded=false]:rotate-180 mr-[-2px]" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<div id="filter-price-body" class="mt-3">
					<div class="flex items-center gap-2">
						<div class="flex-1">
							<label for="lt-price-min" class="sr-only"><?php esc_html_e( 'Minimum price', 'local-tasker' ); ?></label>
							<div class="relative">
								<span class="absolute left-3 top-1/2 -translate-y-1/2 text-caption-sm text-[#0A0D1A] pointer-events-none" aria-hidden="true">$</span>
								<input
									type="number"
									id="lt-price-min"
									name="min_price"
									min="0"
									step="1"
									value="<?php echo esc_attr( $price_min ); ?>"
									placeholder="0"
									class="placeholder:text-[#0A0D1A] w-full h-[34px] border border-[#e5e5df] rounded-md pl-6 pr-3 text-[13px] text-lt-text-primary bg-lt-white focus:outline-none focus:ring-2 focus:ring-lt-brand/30 focus:border-lt-brand"
								>
							</div>
						</div>
						<span class="text-caption-sm text-[#0A0D1A] shrink-0">–</span>
						<div class="flex-1">
							<label for="lt-price-max" class="sr-only"><?php esc_html_e( 'Maximum price', 'local-tasker' ); ?></label>
							<div class="relative">
								<span class="absolute left-3 top-1/2 -translate-y-1/2 text-caption-sm text-[#0A0D1A] pointer-events-none" aria-hidden="true">$</span>
								<input
									type="number"
									id="lt-price-max"
									name="max_price"
									min="0"
									step="1"
									value="<?php echo esc_attr( $price_max ); ?>"
									placeholder="300"
									class="placeholder:text-[#0A0D1A] w-full h-[34px] border border-[#e5e5df] rounded-md pl-6 pr-3 text-[13px] text-lt-text-primary bg-lt-white focus:outline-none focus:ring-2 focus:ring-lt-brand/30 focus:border-lt-brand"
								>
							</div>
						</div>
					</div>
				</div>
			</div><!-- /.lt-filter-group price -->

			<div class="h-px bg-[#E9EAEC]"></div>

			<!-- ── Colour / Finish ── -->
			<?php if ( ! empty( $colour_terms ) ) : ?>
			<div class="lt-filter-group" data-filter-group="colour">
				<button
					type="button"
					class="lt-filter-group__toggle w-full flex items-center justify-between text-left gap-2 group"
					aria-expanded="true"
					aria-controls="filter-colour-body"
				>
					<span class="font-semi-ext text-[13px] font-bold text-[#0a0d1a]"><?php esc_html_e( 'Colour / Finish', 'local-tasker' ); ?></span>
					<svg class="w-4 h-4 text-lt-text-muted shrink-0 transition-transform duration-200 group-aria-[expanded=false]:rotate-180 mr-[-2px]" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<ul id="filter-colour-body" class="mt-3 flex flex-col gap-2 list-none p-0 m-0">
					<?php foreach ( $colour_terms as $term ) :
						$is_checked = in_array( $term->slug, $active_colours, true );
					?>
						<li>
							<label class="flex items-center justify-between gap-2 cursor-pointer group/label">
								<span class="flex items-center gap-2">
									<input
										type="checkbox"
										name="filter_colour[]"
										value="<?php echo esc_attr( $term->slug ); ?>"
										class="lt-filter-checkbox sr-only peer"
										<?php checked( $is_checked ); ?>
									>
									<span class="lt-filter-checkbox__ui w-4 h-4 rounded border border-[#D1D5DB] flex items-center justify-center shrink-0 peer-checked:bg-lt-brand peer-checked:border-lt-brand transition-colors duration-150" aria-hidden="true">
										
									</span>
									<span class="text-[13.5px] text-[#374151] peer-checked:text-lt-text-primary group-hover/label:text-lt-brand transition-colors duration-150">
										<?php echo esc_html( $term->name ); ?>
									</span>
								</span>
								<span class="text-[11px] text-[#6b7280] shrink-0"><?php echo esc_html( $term->count ); ?></span>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
			</div><!-- /.lt-filter-group colour -->

			<div class="h-px bg-[#E9EAEC]"></div>
			<?php endif; ?>

			<!-- ── Thickness ── -->
			<?php if ( ! empty( $thickness_terms ) ) : ?>
			<div class="lt-filter-group" data-filter-group="thickness">
				<button
					type="button"
					class="lt-filter-group__toggle w-full flex items-center justify-between text-left gap-2 group"
					aria-expanded="true"
					aria-controls="filter-thickness-body"
				>
				<span class="font-semi-ext text-[13px] font-bold text-[#0a0d1a]"><?php esc_html_e( 'Thickness', 'local-tasker' ); ?></span>
				<svg class="w-4 h-4 text-lt-text-muted shrink-0 transition-transform duration-200 group-aria-[expanded=false]:rotate-180 mr-[-2px]" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				</button>
				<ul id="filter-thickness-body" class="mt-3 flex flex-col gap-2 list-none p-0 m-0">
					<?php foreach ( $thickness_terms as $term ) :
						$is_checked = in_array( $term->slug, $active_thickness, true );
					?>
						<li>
							<label class="flex items-center justify-between gap-2 cursor-pointer group/label">
								<span class="flex items-center gap-2">
									<input
										type="checkbox"
										name="filter_thickness[]"
										value="<?php echo esc_attr( $term->slug ); ?>"
										class="lt-filter-checkbox sr-only peer"
										<?php checked( $is_checked ); ?>
									>
									<span class="lt-filter-checkbox__ui w-4 h-4 rounded border border-[#D1D5DB] flex items-center justify-center shrink-0 peer-checked:bg-lt-brand peer-checked:border-lt-brand transition-colors duration-150" aria-hidden="true">
									</span>
									<span class="text-[13.5px] text-[#374151] peer-checked:text-lt-text-primary group-hover/label:text-lt-brand transition-colors duration-150">
										<?php echo esc_html( $term->name ); ?>
									</span>
								</span>
								<span class="text-[11px] text-[#6b7280] shrink-0"><?php echo esc_html( $term->count ); ?></span>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
			</div><!-- /.lt-filter-group thickness -->
			<?php endif; ?>
			
			<div class="h-px bg-[#E9EAEC]"></div>

			<!-- Clear all + Apply (mobile) -->
			<div class="flex flex-col gap-3 pt-2">
				<a
					href="<?php echo esc_url( $clear_url ); ?>"
					id="lt-clear-filters"
					class="inline-flex gap-1 items-center font-semi-ext text-[12.5px] font-bold text-[#f26522] hover:underline text-center<?php echo $has_active_filters ? ' ' : ' hidden'; ?>"
				>
					<span class="icon mb-[5px]">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M7.19824 3.94824L5.85254 5.28125L7.19824 6.61426C7.2321 6.65658 7.2596 6.70101 7.28076 6.74756C7.30192 6.79411 7.3125 6.84701 7.3125 6.90625C7.3125 7.01628 7.2723 7.11149 7.19189 7.19189C7.11149 7.2723 7.01628 7.3125 6.90625 7.3125C6.84701 7.3125 6.79411 7.30192 6.74756 7.28076C6.70101 7.2596 6.65658 7.2321 6.61426 7.19824L5.28125 5.85254L3.94824 7.19824C3.90592 7.2321 3.86149 7.2596 3.81494 7.28076C3.76839 7.30192 3.71549 7.3125 3.65625 7.3125C3.54622 7.3125 3.45101 7.2723 3.37061 7.19189C3.2902 7.11149 3.25 7.01628 3.25 6.90625C3.25 6.84701 3.26058 6.79411 3.28174 6.74756C3.3029 6.70101 3.3304 6.65658 3.36426 6.61426L4.70996 5.28125L3.36426 3.94824C3.3304 3.90592 3.3029 3.86149 3.28174 3.81494C3.26058 3.76839 3.25 3.71549 3.25 3.65625C3.25 3.54622 3.2902 3.45101 3.37061 3.37061C3.45101 3.2902 3.54622 3.25 3.65625 3.25C3.71549 3.25 3.76839 3.26058 3.81494 3.28174C3.86149 3.3029 3.90592 3.3304 3.94824 3.36426L5.28125 4.70996L6.61426 3.36426C6.65658 3.3304 6.70101 3.3029 6.74756 3.28174C6.79411 3.26058 6.84701 3.25 6.90625 3.25C7.01628 3.25 7.11149 3.2902 7.19189 3.37061C7.2723 3.45101 7.3125 3.54622 7.3125 3.65625C7.3125 3.71549 7.30192 3.76839 7.28076 3.81494C7.2596 3.86149 7.2321 3.90592 7.19824 3.94824ZM10.5625 5.28125C10.5625 6.00911 10.4229 6.69466 10.1436 7.33789C9.87272 7.98112 9.49821 8.54183 9.02002 9.02002C8.54183 9.49821 7.98112 9.87272 7.33789 10.1436C6.69466 10.4229 6.00911 10.5625 5.28125 10.5625C4.55339 10.5625 3.86784 10.4229 3.22461 10.1436C2.58138 9.87272 2.02067 9.49821 1.54248 9.02002C1.06429 8.54183 0.689779 7.98112 0.418945 7.33789C0.139648 6.69466 0 6.00911 0 5.28125C0 4.55339 0.139648 3.86784 0.418945 3.22461C0.689779 2.58138 1.06429 2.02067 1.54248 1.54248C2.02067 1.06429 2.58138 0.689778 3.22461 0.418945C3.86784 0.139648 4.55339 0 5.28125 0C6.00911 0 6.69466 0.139648 7.33789 0.418945C7.98112 0.698242 8.53971 1.07487 9.01367 1.54883C9.48763 2.02279 9.86426 2.58138 10.1436 3.22461C10.4229 3.86784 10.5625 4.55339 10.5625 5.28125ZM9.75 5.28125C9.75 4.66341 9.63151 4.08366 9.39453 3.54199C9.16602 3.00033 8.84863 2.52637 8.44238 2.12012C8.03613 1.71387 7.56217 1.39648 7.02051 1.16797C6.47884 0.930989 5.89909 0.8125 5.28125 0.8125C4.66341 0.8125 4.08366 0.930989 3.54199 1.16797C3.00033 1.39648 2.52637 1.71387 2.12012 2.12012C1.71387 2.52637 1.39648 3.00033 1.16797 3.54199C0.93099 4.08366 0.8125 4.66341 0.8125 5.28125C0.8125 5.89909 0.93099 6.47884 1.16797 7.02051C1.39648 7.56217 1.71387 8.03613 2.12012 8.44238C2.52637 8.84863 3.00033 9.16602 3.54199 9.39453C4.08366 9.63151 4.66341 9.75 5.28125 9.75C5.89909 9.75 6.47884 9.63151 7.02051 9.39453C7.56217 9.16602 8.03613 8.84863 8.44238 8.44238C8.84863 8.03613 9.16602 7.56217 9.39453 7.02051C9.63151 6.47884 9.75 5.89909 9.75 5.28125Z" fill="#F26522"/>
						</svg>
					</span>
						<span class="text"><?php esc_html_e( 'Clear All Filters', 'local-tasker' ); ?></span>
				</a>
				<button type="submit" class="md:hidden btn btn--brand w-full">
					<?php esc_html_e( 'Apply Filters', 'local-tasker' ); ?>
				</button>
			</div>

		</div><!-- /.inner padding -->
	</form>
</aside>