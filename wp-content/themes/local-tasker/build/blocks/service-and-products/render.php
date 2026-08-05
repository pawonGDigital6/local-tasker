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
$class_name = 'acf-block lt-services-and-products py-16';

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
$section_title = get_field('section_title');
$section_content = get_field('section_content');
$section_sub_text = get_field('section_sub_text');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="container">
		<div class="rows flex flex-wrap justify-between items-center lg:gap-6 gap-[3.125rem]">
			<!-- Section Heading -->
			<div class="section-heading lg:w-[37.6%] w-full max-sm:text-center">
				<?php if ($section_title): ?>
					<h2
						class="section-title text-h3 leading-[1.30] font-semi-ext font-bold sm:tracking-[-1px] tracking-[0.07px] text-lt-onyx sm:mb-[6px] mb-[3px] wd:pr-5">
						<?php echo esc_html($section_title); ?>
					</h2>
				<?php endif; ?>
				<?php if ($section_sub_text): ?>
					<div
						class="section-sub-text snm:tracking-[0.02em] tracking-[-0.015px] mb-[1.875rem] max-sm:text-caption-md">
						<?php echo esc_html($section_sub_text); ?>
					</div>
				<?php endif; ?>
				<?php if ($section_content): ?>
					<div class="section-content text-[0.93rem] text-lt-onyx font-semibold tick-ul sm:mb-[1.875rem] mb-5">
						<?php echo $section_content; ?>
					</div>
				<?php endif; ?>
				<?php
				$link = get_field('section_button');
				if ($link):
					$link_url = $link['url'];
					$link_title = $link['title'];
					$link_target = $link['target'] ? $link['target'] : '_self';
					?>
					<a class="btn btn--brand sm:min-w-[270px]" href="<?php echo esc_url($link_url); ?>"
						target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
				<?php endif; ?>
			</div>
			<!-- product service Tab -->
			<div class="cs-tab wd:w-[48.7%] lg:w-[58.4%] w-full">
				<!--  Tab Head  -->
				<div class="cs-tab__head bg-lt-snow-drift">
					<ul>
						<li><button class="cs-tab__head-title active" data-tab-target="tab-1">Services</button></li>
						<li><button class="cs-tab__head-title" data-tab-target="tab-2">Products</button></li>
					</ul>
				</div><!--  End of Tab Head  -->
				<!--  Tab Content  -->
				<div class="cs-tab__content-holder">
					<!--  Tab List    -->
					<div id="tab-1" class="tab-content active">
						<div class="content">
							<?php
							// Retrieve the array of post objects from the ACF Relationship field
							$posts = get_field('services_prod');
							if ($posts):
								// CRITICAL: You must declare the global $post variable before the loop 
								// for setup_postdata() to work correctly, especially inside Gutenberg blocks.
								global $post;
								?>
								<div class="services-grid grid sm:grid-cols-2 gap-x-[0.875rem] sm:gap-y-[1.18rem] gap-y-2">
									<?php
									// 2. Loop through the ACF array
									foreach ($posts as $post):
										// 3. Set up the post data so standard WP functions (the_title, etc.) work
										setup_postdata($post);
										?>
										<!-- Single Service HTML Item (BEM Methodology) -->
										<article id="post-<?php the_ID(); ?>" <?php post_class('service-card transition-shadow duration-[420ms] hover:shadow-lg flex items-center gap-4 sm:p-[14px] p-[14px_19px_14px_16px] bg-lt-white rounded-lg relative'); ?>>
											<?php if (has_post_thumbnail()): ?>
												<div class="service-card__media w-[73px] h-[73px] shrink-0 rounded-lg overflow-hidden">
													<?php the_post_thumbnail('medium', array('class' => 'service-card__image w-full h-full object-cover')); ?>
												</div>
											<?php endif; ?>
											<div class="service-card__content">
												<h3
													class="service-card__title sm:text-body text-caption-md line-clamp-2 overflow-hidden font-regular sm:leading-[1.4] mb-1 font-base tracking-[-0.02em] text-lt-onyx">
													<?php the_title(); ?>
												</h3>
												<div
													class="service-card__excerpt text-caption-sm leading-[1.33] tracking-[0.25px] text-body">
													<?php
													// Explicitly trim words to maintain uniform card heights in a grid
													echo wp_trim_words(get_the_excerpt(), 10, '&hellip;');
													?>
												</div>
											</div>
											<!-- Added aria-label for accessibility since the link contains no text -->
											<a href="<?php echo esc_url(get_permalink()); ?>" class="streched-link absolute inset-0"
												aria-label="<?php echo esc_attr(get_the_title()); ?>"></a>
										</article>
									<?php endforeach; ?>
								</div>
								<?php
								// 4. Restore global post data after the foreach loop
								wp_reset_postdata();
								?>
							<?php else: ?>
								<!-- Fallback UI if no services exist -->
								<div class="services-grid__empty text-center">
									<p><?php esc_html_e('No services are currently available. Please check back soon.', 'local-task'); ?>
									</p>
								</div>
							<?php endif; ?>
						</div>
					</div><!--  End of Tab List    -->
					<!--  Tab List    -->
					<div id="tab-2" class="tab-content">
						<div class="content">
							<?php
							// Retrieve the array of WooCommerce product category IDs from the ACF Taxonomy field
							$category_ids = get_field('prod_services');

							// Check if we have data and ensure it's an array
							if ($category_ids && is_array($category_ids)):
								?>
								<!-- Grid Wrapper -->
								<div class="products-grid grid sm:grid-cols-2 gap-x-[0.875rem] gap-y-[1.18rem]">
									<?php
									foreach ($category_ids as $cat_id):
										// Fetch the full term object using the ID from ACF
										$category = get_term($cat_id, 'product_cat');

										// Skip this iteration if the category doesn't exist or returns an error
										if (!$category || is_wp_error($category)) {
											continue;
										}

										// Get the category link
										$category_link = get_term_link($category);

										// Get the WooCommerce category thumbnail ID
										$thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
										?>
										<!-- Single Category HTML Item -->
										<article id="category-<?php echo esc_attr($category->term_id); ?>"
											class="product-card transition-shadow duration-[420ms] hover:shadow-lg flex items-center gap-4 sm:p-[14px] p-[14px_19px_14px_16px] bg-lt-white rounded-lg relative">
											<div class="product-card__media w-[73px] h-[73px] shrink-0 rounded-lg overflow-hidden bg-lt-snow-drift">
												<?php
												// Fetches the WooCommerce Category Image
												echo wp_get_attachment_image($thumbnail_id, 'medium', false, array(
													'class' => 'product-card__image w-full h-full object-cover'
												));
												?>
											</div>
											<div class="product-card__content">
												<h3
													class="product-card__title sm:text-body text-caption-md line-clamp-2 overflow-hidden font-regular sm:leading-[1.4] mb-1 font-base tracking-[-0.02em] text-lt-onyx">
													<?php echo esc_html($category->name); ?>
												</h3>
												<div class="text-caption-sm leading-[1.33] tracking-[0.25px] text-body">
													<?php
													// Pulls from the WooCommerce category description field and limits length
													echo wp_trim_words($category->description, 8, '&hellip;');
													?>
												</div>
											</div>
											<!-- A11y hidden link stretching over the whole card -->
											<a href="<?php echo esc_url($category_link); ?>" class="streched-link absolute inset-0"
												aria-label="<?php echo esc_attr($category->name); ?>"></a>
										</article>
									<?php endforeach; ?>
								</div>
							<?php else: ?>
								<!-- Fallback UI -->
								<div class="products-grid__empty text-center">
									<p><?php esc_html_e('No related categories are currently available.', 'local-task'); ?> </p>
								</div>
							<?php endif; ?>
						</div>
					</div><!--  End of Tab List    -->
				</div>
			</div>
		</div>
	</div>
</section>