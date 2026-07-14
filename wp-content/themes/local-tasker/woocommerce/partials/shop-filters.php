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
$price_min         = ( isset( $_GET['min_price'] ) && $_GET['min_price'] !== '' ) ? (float) $_GET['min_price'] : '';
$price_max         = ( isset( $_GET['max_price'] ) && $_GET['max_price'] !== '' ) ? (float) $_GET['max_price'] : '';
$active_colours    = isset( $_GET['filter_colour'] ) ? array_map( 'sanitize_text_field', (array) $_GET['filter_colour'] ) : [];
$active_thickness  = isset( $_GET['filter_thickness'] ) ? array_map( 'sanitize_text_field', (array) $_GET['filter_thickness'] ) : [];

$has_active_filters = $active_cat !== '' || $price_min !== '' || $price_max !== '' || ! empty( $active_colours ) || ! empty( $active_thickness );

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

$clear_url = remove_query_arg( [ 'product_cat', 'min_price', 'max_price', 'filter_colour', 'filter_thickness', 'paged' ] );

// Total published products (used for "All Flooring" count label).
$total_products_obj = wp_count_posts( 'product' );
$total_products_count = isset( $total_products_obj->publish ) ? (int) $total_products_obj->publish : 0;
?>

<!-- Mobile: filter toggle button (hidden on desktop) -->
<button
	type="button"
	id="lt-filter-open"
	class="md:hidden fixed bottom-5 left-1/2 -translate-x-1/2 z-40 btn btn--brand shadow-lg flex items-center gap-2"
	aria-expanded="false"
	aria-controls="lt-filters-sidebar"
>
	<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
		<path d="M2 5h14M5 9h8M8 13h2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
	</svg>
	<?php esc_html_e( 'Filters', 'local-tasker' ); ?>
	<?php if ( $has_active_filters ) : ?>
		<span class="lt-filter-count bg-lt-white text-lt-brand text-caption-xs font-bold w-5 h-5 rounded-full flex items-center justify-center" aria-label="<?php esc_attr_e( 'Active filters', 'local-tasker' ); ?>">
			<?php echo esc_html( ( $active_cat !== '' ? 1 : 0 ) + count( $active_colours ) + count( $active_thickness ) + ( $price_min !== '' || $price_max !== '' ? 1 : 0 ) ); ?>
		</span>
	<?php endif; ?>
</button>

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

		<div class="p-5 md:p-0 flex flex-col gap-6">

			<!-- Heading (desktop) -->
			<p class="hidden md:block text-caption-sm font-bold text-lt-text-primary tracking-[0.08em] uppercase m-0">
				<?php esc_html_e( 'FILTERS', 'local-tasker' ); ?>
			</p>

			<!-- ── Category ── -->
			<div class="lt-filter-group" data-filter-group="category">
				<button
					type="button"
					class="lt-filter-group__toggle w-full flex items-center justify-between text-left gap-2 group"
					aria-expanded="true"
					aria-controls="filter-category-body"
				>
					<span class="text-caption-md font-bold text-lt-text-primary"><?php esc_html_e( 'Category', 'local-tasker' ); ?></span>
					<svg class="w-4 h-4 text-lt-text-muted shrink-0 transition-transform duration-200 group-aria-[expanded=false]:rotate-180" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
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
								<span class="text-caption-sm text-lt-text-secondary peer-checked:font-semibold peer-checked:text-lt-text-primary group-hover/label:text-lt-brand transition-colors duration-150">
									<?php esc_html_e( 'All Flooring', 'local-tasker' ); ?>
								</span>
							</span>
							<span class="text-caption-xs text-lt-text-muted"><?php echo esc_html( $total_products_count ); ?></span>
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
										<span class="w-1.5 h-1.5 rounded-full bg-lt-white <?php echo $is_checked ? '' : 'hidden'; ?>"></span>
									</span>
									<span class="text-caption-sm text-lt-text-secondary peer-checked:font-semibold peer-checked:text-lt-text-primary group-hover/label:text-lt-brand transition-colors duration-150">
										<?php echo esc_html( $cat->name ); ?>
									</span>
								</span>
								<span class="text-caption-xs text-lt-text-muted"><?php echo esc_html( $cat->count ); ?></span>
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
					<span class="text-caption-md font-bold text-lt-text-primary"><?php esc_html_e( 'Price Range', 'local-tasker' ); ?></span>
					<svg class="w-4 h-4 text-lt-text-muted shrink-0 transition-transform duration-200 group-aria-[expanded=false]:rotate-180" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
				<div id="filter-price-body" class="mt-3">
					<div class="flex items-center gap-2">
						<div class="flex-1">
							<label for="lt-price-min" class="sr-only"><?php esc_html_e( 'Minimum price', 'local-tasker' ); ?></label>
							<div class="relative">
								<span class="absolute left-3 top-1/2 -translate-y-1/2 text-caption-sm text-lt-text-muted pointer-events-none" aria-hidden="true">$</span>
								<input
									type="number"
									id="lt-price-min"
									name="min_price"
									min="0"
									step="1"
									value="<?php echo esc_attr( $price_min ); ?>"
									placeholder="0"
									class="w-full border border-[#D1D5DB] rounded-lg pl-6 pr-3 py-2 text-caption-sm text-lt-text-primary bg-lt-white focus:outline-none focus:ring-2 focus:ring-lt-brand/30 focus:border-lt-brand"
								>
							</div>
						</div>
						<span class="text-caption-sm text-lt-text-muted shrink-0">–</span>
						<div class="flex-1">
							<label for="lt-price-max" class="sr-only"><?php esc_html_e( 'Maximum price', 'local-tasker' ); ?></label>
							<div class="relative">
								<span class="absolute left-3 top-1/2 -translate-y-1/2 text-caption-sm text-lt-text-muted pointer-events-none" aria-hidden="true">$</span>
								<input
									type="number"
									id="lt-price-max"
									name="max_price"
									min="0"
									step="1"
									value="<?php echo esc_attr( $price_max ); ?>"
									placeholder="300"
									class="w-full border border-[#D1D5DB] rounded-lg pl-6 pr-3 py-2 text-caption-sm text-lt-text-primary bg-lt-white focus:outline-none focus:ring-2 focus:ring-lt-brand/30 focus:border-lt-brand"
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
					<span class="text-caption-md font-bold text-lt-text-primary"><?php esc_html_e( 'Colour / Finish', 'local-tasker' ); ?></span>
					<svg class="w-4 h-4 text-lt-text-muted shrink-0 transition-transform duration-200 group-aria-[expanded=false]:rotate-180" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
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
										<svg class="w-3 h-3 text-lt-white <?php echo $is_checked ? '' : 'hidden'; ?>" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 6l3 3 5-5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
									</span>
									<span class="text-caption-sm text-lt-text-secondary peer-checked:font-semibold peer-checked:text-lt-text-primary group-hover/label:text-lt-brand transition-colors duration-150">
										<?php echo esc_html( $term->name ); ?>
									</span>
								</span>
								<span class="text-caption-xs text-lt-text-muted"><?php echo esc_html( $term->count ); ?></span>
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
					<span class="text-caption-md font-bold text-lt-text-primary"><?php esc_html_e( 'Thickness', 'local-tasker' ); ?></span>
					<svg class="w-4 h-4 text-lt-text-muted shrink-0 transition-transform duration-200 group-aria-[expanded=false]:rotate-180" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
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
										<svg class="w-3 h-3 text-lt-white <?php echo $is_checked ? '' : 'hidden'; ?>" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 6l3 3 5-5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
									</span>
									<span class="text-caption-sm text-lt-text-secondary peer-checked:font-semibold peer-checked:text-lt-text-primary group-hover/label:text-lt-brand transition-colors duration-150">
										<?php echo esc_html( $term->name ); ?>
									</span>
								</span>
								<span class="text-caption-xs text-lt-text-muted"><?php echo esc_html( $term->count ); ?></span>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
			</div><!-- /.lt-filter-group thickness -->
			<?php endif; ?>

			<!-- Clear all + Apply (mobile) -->
			<div class="flex flex-col gap-3 pt-2">
				<?php if ( $has_active_filters ) : ?>
					<a href="<?php echo esc_url( $clear_url ); ?>" class="text-caption-sm font-semibold text-lt-accent hover:underline text-center">
						<?php esc_html_e( 'Clear All Filters', 'local-tasker' ); ?>
					</a>
				<?php endif; ?>
				<button type="submit" class="md:hidden btn btn--brand w-full">
					<?php esc_html_e( 'Apply Filters', 'local-tasker' ); ?>
				</button>
			</div>

		</div><!-- /.inner padding -->
	</form>
</aside>
