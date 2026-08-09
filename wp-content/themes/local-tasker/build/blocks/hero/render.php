<?php

/**

 * ACF Block template.

 *

 * @param array $block The block settings and attributes.

 */



// Support custom "anchor" values.

$anchor = '';

if (!empty($block['anchor'])) {

	$anchor = 'id="' . esc_attr($block['anchor']) . '" ';

}



// Create class attribute allowing for custom "className" and "align" values.

$class_name = 'acf-block lt-hero relative md:pt-[5.3125rem] md:pb-[5.8rem] pt-13 pb-8';



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



$size = 'full';

$hero_image = get_field('hero_image');

$pre_title_image = get_field('pre_title_image');

$rating_image = get_field('rating_image');

$pre_title = get_field('pre_title');

$hero_title = get_field('hero_title');

$hero_video_bg = get_field('hero_video_bg');

$is_bg_video = get_field('is_bg_video');

$hero_cta_primary = get_field('hero_cta_primary');

$hero_cta_secondary = get_field('hero_cta_secondary');

$search_placeholder_service = get_field('hero_search_placeholder_service') ?: __('Product or Service...', 'local-tasker');

$search_placeholder_category = get_field('hero_search_placeholder_category') ?: __('Category...', 'local-tasker');

$shop_page_id = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;

$hero_search_url = get_field('hero_search_url') ?: ($shop_page_id > 0 ? get_permalink($shop_page_id) : home_url('/'));



// Build static navigation items for the search dropdowns.
$shop_page_url = function_exists('wc_get_page_id') ? get_permalink(wc_get_page_id('shop')) : home_url('/shop/');

// Helper function to safely get WooCommerce term link or fallback
if (!function_exists('lt_get_product_cat_url')) {
	function lt_get_product_cat_url($slug) {
		$link = get_term_link($slug, 'product_cat');
		if (is_wp_error($link)) {
			return home_url('/product-category/' . $slug . '/');
		}
		return $link;
	}
}

$flooring_subcategories = [
	[
		'name' => __('SPC Hybrid', 'local-tasker'),
		'url'  => lt_get_product_cat_url('spc-hybrid-flooring'),
	],
	[
		'name' => __('European Oak Engineered Timber', 'local-tasker'),
		'url'  => lt_get_product_cat_url('engineered-timber-flooring'),
	],
	[
		'name' => __('Australian Species Engineered Timber', 'local-tasker'),
		'url'  => lt_get_product_cat_url('australian-species-engineered-timber'),
	],
	[
		'name' => __('Flooring Accessories', 'local-tasker'),
		'url'  => lt_get_product_cat_url('flooring-accessories'),
	],
	[
		'name' => __('Tiles', 'local-tasker'),
		'url'  => lt_get_product_cat_url('tiles'),
	],
];

$structural_subcategories = [
	[
		'name' => __('LVL F11', 'local-tasker'),
		'url'  => lt_get_product_cat_url('lvl-f11'),
	],
	[
		'name' => __('LVL F17', 'local-tasker'),
		'url'  => lt_get_product_cat_url('lvl-f17'),
	],
];

// Top-level static navigation items definition
$static_navigation = [
	'flooring' => [
		'name'         => __('Flooring', 'local-tasker'),
		'url'          => $shop_page_url,
		'redirect'     => 'false',
		'subcategories' => $flooring_subcategories,
	],
	'structural-materials' => [
		'name'         => __('Structural Materials', 'local-tasker'),
		'url'          => lt_get_product_cat_url('structural-materials'),
		'redirect'     => 'false',
		'subcategories' => $structural_subcategories,
	],
	'renovations' => [
		'name'         => __('Renovations', 'local-tasker'),
		'url'          => home_url('/services/home-renovations/'),
		'redirect'     => 'true',
		'subcategories' => [],
	],
	'cabinetry' => [
		'name'         => __('Cabinetry', 'local-tasker'),
		'url'          => home_url('/services/custom-cabinetry/'),
		'redirect'     => 'true',
		'subcategories' => [],
	],
];

// Reformat subcategories map for passing to JavaScript
$sub_cats_map = [];
foreach ($static_navigation as $key => $item) {
	if (!empty($item['subcategories'])) {
		foreach ($item['subcategories'] as $subcat) {
			$sub_cats_map[$key][] = [
				'slug' => $subcat['url'],
				'name' => $subcat['name'],
			];
		}
	}
}

?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<?php

	/**

	 * BACKGROUND LAYER LOGIC

	 * Prioritize Video only if the toggle is explicitly active and a valid file array is present.

	 */

	if ($is_bg_video && !empty($hero_video_bg) && is_array($hero_video_bg)):
		$video_url = esc_url($hero_video_bg['url']);
		$mime_type = esc_attr($hero_video_bg['mime_type']);
		$video_title = esc_attr($hero_video_bg['title']);
		?>
		<div class="abs-bg-video absolute inset-0 w-full h-full pointer-events-none">
			<video class="w-full h-full object-cover" autoplay loop playsinline muted>
				<source src="<?php echo $video_url; ?>" type="<?php echo $mime_type; ?>">
			</video>
		</div>
		<?php

		/**
		 * FALLBACK IMAGE LAYER
		 * Triggers if the Video Toggle is turned off, or if the field lacks a valid video array asset.
		 */
	elseif (!empty($hero_image)):

		?>
		<div class="abs-bg-video absolute inset-0 w-full h-full z-0">
			<?php

			// Senior Note: Passing Tailwind layout rules directly into WordPress native render context
		
			echo wp_get_attachment_image($hero_image, $size, false, [

				'class' => 'w-full h-full object-cover',

				'sizes' => '100vw'

			]);

			?>
		</div>
	<?php endif; ?>
	<!-- Abs BG -->
	<div class="abs-bg absolute inset-0" style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.42) 1%, rgba(0, 0, 0, 0) 37%, rgba(0, 0, 0, 0) 42%),

radial-gradient(52.86% 52.86% at 50% 47.14%, rgba(0, 0, 0, 0.65) 0%, rgba(5, 52, 131, 0.396875) 79.62%, rgba(10, 101, 252, 0.1625) 100%);

"></div>
	<div class="container relative">
		<!-- Pre Heading -->
		<div
			class="pre-header flex max-sm:flex-col sm:gap-4 gap-1 items-center text-center justify-center sm:mb-[1.85rem] mb-[1.25rem]">
			<div class="imgs flex  gap-2 items-center">
				<?php

				if ($pre_title_image):

					$url = wp_get_attachment_url($pre_title_image);

					echo wp_get_attachment_image($pre_title_image, $size);

				endif;

				if ($rating_image):

					$url = wp_get_attachment_url($rating_image);

					echo wp_get_attachment_image($rating_image, $size);

				endif;

				?>
			</div>
			<!-- Pre Title -->
			<?php if ($pre_title): ?>
				<span
					class="text-white text-caption-md [&_*:last-child]:mb-0 [&_strong]:font-semibold"><?php echo $pre_title; ?></span>
			<?php endif; ?>
		</div>
		<!-- Hero Title -->
		<?php if ($hero_title): ?>
			<h1
				class="hero-title text-white text-center font-bold max-w-[892px] mx-auto leading-none font-semi-ext max-md:leading-[1.23]">
				<?php echo esc_html($hero_title); ?>
			</h1>
		<?php endif; ?>
		<!-- Feature Lists -->
		<div class="feature-list grid sm:grid-cols-3 gap-6 sm:mt-[48px] mt-10 wd:px-5">
			<?php if (have_rows('features_item')): ?>
				<?php while (have_rows('features_item')):

					the_row();

					$feature_item_icon = get_sub_field('feature_item_icon');

					$feature_title = get_sub_field('feature_title');

					$feature_text = get_sub_field('feature_text');

					?>
					<!-- Item -->
					<div class="feature-list__item text-center text-lt-white max-sml:flex max-sml:gap-4">
						<?php

						if ($feature_item_icon):

							?>
							<div class="icon sm:mb-[18px] mb-4 flex justify-center shrink-0 max-sm:w-[21px] max-sm:h-[21px] max-sm:mt-[3px] sml:mx-auto">
								<?php
								echo wp_get_attachment_image($feature_item_icon, $size);
								; ?>
							</div>
							<?php

						endif;

						?>
						<div class="content-holder max-sml:text-left">
							<?php if ($feature_title): ?>
								<h2
									class="feature-heading sm:text-body-xl text-body-lg leading-[1.4] font-bold tracking-[-0.8px] font-semi-ext sm:mb-[11px] mb-2">
									<?php echo esc_html($feature_title); ?>
								</h2>
							<?php endif; ?>
							<?php if ($feature_text): ?>
								<div class="feature-text max-w-[302px] mx-auto tracking-[0.02em] max-sml:text-caption-md"><?php echo $feature_text; ?></div>
							<?php endif; ?>
						</div>
					</div><!-- End of Item -->
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
		<!-- End of Feature Lists -->
		<!-- Hero Search Form -->
		<div class="hero-search sm:mt-14 mt-12">
			<form action="<?php echo esc_url($hero_search_url); ?>" method="get" class="hero-search__form"
				data-sub-cats="<?php echo esc_attr(wp_json_encode($sub_cats_map)); ?>">
				<div class="hero-search__bar">
					<!-- Product / Service dropdown -->
					<div class="hero-search__field">
						<select class="hero-search__select" id="hero-product-cat"
							aria-label="<?php echo esc_attr($search_placeholder_service); ?>"
							data-placeholder="<?php echo esc_attr($search_placeholder_service); ?>">
							<option value=""><?php echo esc_html($search_placeholder_service); ?></option>
							<?php foreach ($static_navigation as $key => $item): ?>
								<option value="<?php echo esc_url($item['url']); ?>"
									data-term-id="<?php echo esc_attr($key); ?>"
									data-redirect="<?php echo esc_attr($item['redirect']); ?>">
									<?php echo esc_html($item['name']); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="hero-search__divider" aria-hidden="true"></div>
					<!-- Category dropdown -->
					<div class="hero-search__field">
						<select class="hero-search__select" id="hero-sub-cat"
							aria-label="<?php echo esc_attr($search_placeholder_category); ?>"
							data-placeholder="<?php echo esc_attr($search_placeholder_category); ?>">
							<option value=""><?php echo esc_html($search_placeholder_category); ?></option>
						</select>
					</div>
				</div>
				<button type="submit" class="btn btn--brand hero-search__btn">
					<?php esc_html_e('Search', 'local-tasker'); ?>
				</button>
			</form>
		</div>
		<!-- CTA Buttons -->
	</div>
</section>