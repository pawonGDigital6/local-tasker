<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package local-tasker
 */

get_header();


?>
<main id="primary" class="site-main">
	<?php
	while (have_posts()):
		the_post();
		$post_id = get_the_ID();
		$post_url = get_permalink();
		$post_title = get_the_title();
		$post_excerpt = get_the_excerpt();
		$post_date = get_the_date('M j, Y');
		$post_thumb = get_the_post_thumbnail_url($post_id, 'full');
		$all_cats = get_the_category();
		$content = get_the_content();
		$author = get_the_author();
		$word_count = str_word_count(wp_strip_all_tags($content));
		$read_time = max(1, (int) ceil($word_count / 200));
		?>
		<section class="lt-inner-hero-wd relative flex items-end md:py-14 py-8 md:min-h-[622px] min-h-[450px]">
			<!-- Overlay -->
			<div class="overlay absolute inset-0 w-full h-full pointer-events-none z-1"
				style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0) 100%);">
			</div>
			<!-- BG Img -->
			<?php if ($post_thumb): ?>
				<div class="abs-img absolute inset-0 w-full h-full z-0 pointer-events-none">
					<img class="absolute w-full h-full object-cover" src="<?php echo esc_url($post_thumb); ?>"
						alt="<?php echo esc_attr($post_title); ?>" loading="lazy" decoding="async">
				</div>
			<?php endif; ?>
			<div class="wd:container-bx container">
				<div class="content md:max-w-[896px] text-lt-white relative z-1">
					<?php if (!empty($all_cats)): ?>
						<div class="flex flex-wrap gap-2 sm:mb-6 mb-5">
							<?php foreach ($all_cats as $cats): ?>
								<div
									class="cat p-[7px_13px] bg-lt-brand rounded-[50px] inline-flex sm:text-caption-md text-caption-sm leading-none tracking-[-0.15px]">
									<?php echo esc_html($cats->name); ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<!-- Title -->
					<?php if ($post_title): ?>
						<h1
							class="sm:text-h2 text-[1.875rem] leading-[1.20] sm:tracking-[-0.02em] tracking-[0.4px] sm:mb-6 mb-[13px]">
							<?php echo esc_html($post_title); ?>
						</h1>
					<?php endif; ?>
					<!-- Content -->
					<!-- Meta -->
					<div
						class="meta flex flex-wrap items-center gap-x-[27px] max-sm:gap-x-[34px] gap-y-2 max-sm:text-caption-sm text-lt-white tracking-[0.02em] mb-2">
						<!-- Author -->
						<?php if ($author): ?>
							<span class="author flex items-center sm:gap-2 gap-1.5">
								<span class="icon">
									<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path
											d="M12.6673 14V12.6667C12.6673 11.9594 12.3864 11.2811 11.8863 10.781C11.3862 10.281 10.7079 10 10.0007 10H6.00065C5.29341 10 4.61513 10.281 4.11503 10.781C3.61494 11.2811 3.33398 11.9594 3.33398 12.6667V14"
											stroke="#E5E7EB" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
										<path
											d="M8.00065 7.33333C9.47341 7.33333 10.6673 6.13943 10.6673 4.66667C10.6673 3.19391 9.47341 2 8.00065 2C6.52789 2 5.33398 3.19391 5.33398 4.66667C5.33398 6.13943 6.52789 7.33333 8.00065 7.33333Z"
											stroke="#E5E7EB" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</span>
								<span class="text"> <?php echo $author; ?></span>
							</span>
						<?php endif; ?>
						<!-- Posted On -->
						<time class="meta__posted-on flex items-center sm:gap-2 gap-1.5"
							datetime="<?php echo esc_attr($post_date); ?>">
							<span class="icon">
								<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_50547_5915)">
										<path d="M5.33398 1.33301V3.99967" stroke="#E5E7EB" stroke-width="1.33333"
											stroke-linecap="round" stroke-linejoin="round" />
										<path d="M10.666 1.33301V3.99967" stroke="#E5E7EB" stroke-width="1.33333"
											stroke-linecap="round" stroke-linejoin="round" />
										<path
											d="M12.6667 2.66699H3.33333C2.59695 2.66699 2 3.26395 2 4.00033V13.3337C2 14.07 2.59695 14.667 3.33333 14.667H12.6667C13.403 14.667 14 14.07 14 13.3337V4.00033C14 3.26395 13.403 2.66699 12.6667 2.66699Z"
											stroke="#E5E7EB" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M2 6.66699H14" stroke="#E5E7EB" stroke-width="1.33333" stroke-linecap="round"
											stroke-linejoin="round" />
									</g>
									<defs>
										<clipPath id="clip0_50547_5915">
											<rect width="16" height="16" fill="white" />
										</clipPath>
									</defs>
								</svg>
							</span>
							<span class="text"><?php echo esc_html($post_date); ?></span>
						</time>
						<!-- Read -->
						<span class="meta__read-time flex items-center sm:gap-2 gap-1.5">
							<span class="icon shrink-0" aria-hidden="true">
								<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_50547_3157)">
										<path
											d="M8.00065 14.6663C11.6825 14.6663 14.6673 11.6816 14.6673 7.99967C14.6673 4.31778 11.6825 1.33301 8.00065 1.33301C4.31875 1.33301 1.33398 4.31778 1.33398 7.99967C1.33398 11.6816 4.31875 14.6663 8.00065 14.6663Z"
											stroke="#9CA3AF" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M8 4V8L10.6667 9.33333" stroke="#9CA3AF" stroke-width="1.33333"
											stroke-linecap="round" stroke-linejoin="round" />
									</g>
									<defs>
										<clipPath id="clip0_50547_3157">
											<rect width="16" height="16" fill="white" />
										</clipPath>
									</defs>
								</svg>
							</span>
							<span class="text">
								<?php
								/* translators: %d: estimated read time in minutes */
								echo esc_html(
									sprintf(
										/* translators: %d: number of minutes */
										_n('%d min read', '%d mins read', $read_time, 'lt-theme'),
										$read_time
									)
								);
								?>
							</span>
						</span><!-- End of Read -->
					</div><!-- End Meta -->
				</div>
		</section>
		<?php
	endwhile; // End of the loop.
	?>
	<!-- Main Blog Content -->
	<section class=" blog-content sm:bg-[#F9FAFB] pt-12 default-single">
		<div class="wd:container-bx container">
			<div class="rows flex flex-wrap justify-between gap-4">
				<!-- Left -->
				<div class="left flex flex-col gap-8 lg:w-[67.4%] md:w-[62%] w-full">
					<div class="entry-content sm:bg-white sm:p-8 sm:rounded-[10px] sm:theme-shadow">
						<?php the_content(); ?>
					</div>
					<!-- Share -->
					<div
						class="share sm:bg-white sm:rounded-[10px] sm:theme-shadow sm:px-8 py-6 max-sm:border-t max-sm:border-[#F3F4F6] flex sm:items-center justify-between sm:gap-4 gap-3 max-sm:flex-col">
						<div class="title text-body-lg font-medium text-lt-text-primary shrink-0">Share this article</div>
						<ul class="social-links flex flex-wrap gap-3 m-0">
							<li>
								<a class="w-10 h-10 rounded-full bg-lt-white-lilac text-lt-text-secondary flex justify-center items-center"
									href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($post_url); ?>"
									target="_blank" rel="noopener noreferrer"
									aria-label="<?php esc_attr_e('Share on Facebook', 'lt-theme'); ?>">
									<svg width="9" height="15" viewBox="0 0 9 15" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path
											d="M7.99935 0.666992H5.99935C5.11529 0.666992 4.26745 1.01818 3.64233 1.6433C3.01721 2.26842 2.66602 3.11627 2.66602 4.00033V6.00033H0.666016V8.66699H2.66602V14.0003H5.33268V8.66699H7.33268L7.99935 6.00033H5.33268V4.00033C5.33268 3.82351 5.40292 3.65395 5.52794 3.52892C5.65297 3.4039 5.82254 3.33366 5.99935 3.33366H7.99935V0.666992Z"
											stroke="#364153" stroke-width="1.33333" stroke-linecap="round"
											stroke-linejoin="round" />
									</svg>
								</a>
							</li>
							<li>
								<a class="w-10 h-10 rounded-full bg-lt-white-lilac text-lt-text-secondary flex justify-center items-center"
									href="https://twitter.com/intent/tweet?url=<?php echo urlencode($post_url); ?>&text=<?php echo urlencode($post_title); ?>"
									target="_blank" rel="noopener noreferrer"
									aria-label="<?php esc_attr_e('Share on X', 'lt-theme'); ?>">
									<svg xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewBox="0 0 24 24"
										fill="none" xmlns="http://www.w3.org/2000/svg">
										<path
											d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.451-6.231Zm-1.161 17.52h1.833L7.084 4.126H5.117l11.966 15.644Z"
											fill="#364153"></path>
									</svg>
								</a>
							</li>
							<li>
								<a class="w-10 h-10 rounded-full bg-lt-white-lilac text-lt-text-secondary flex justify-center items-center"
									href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($post_url); ?>"
									target="_blank" rel="noopener noreferrer"
									aria-label="<?php esc_attr_e('Share on LinkedIn', 'lt-theme'); ?>">
									<svg width="16" height="16" viewBox="0 0 16 16" fill="none"
										xmlns="http://www.w3.org/2000/svg">
										<path
											d="M10.666 5.33301C11.7269 5.33301 12.7443 5.75444 13.4944 6.50458C14.2446 7.25473 14.666 8.27214 14.666 9.33301V13.9997H11.9993V9.33301C11.9993 8.97939 11.8589 8.64025 11.6088 8.3902C11.3588 8.14015 11.0196 7.99967 10.666 7.99967C10.3124 7.99967 9.97326 8.14015 9.72321 8.3902C9.47316 8.64025 9.33268 8.97939 9.33268 9.33301V13.9997H6.66602V9.33301C6.66602 8.27214 7.08744 7.25473 7.83759 6.50458C8.58773 5.75444 9.60515 5.33301 10.666 5.33301Z"
											stroke="#364153" stroke-width="1.33333" stroke-linecap="round"
											stroke-linejoin="round" />
										<path d="M4.00065 6H1.33398V14H4.00065V6Z" stroke="#364153" stroke-width="1.33333"
											stroke-linecap="round" stroke-linejoin="round" />
										<path
											d="M2.66732 3.99967C3.4037 3.99967 4.00065 3.40272 4.00065 2.66634C4.00065 1.92996 3.4037 1.33301 2.66732 1.33301C1.93094 1.33301 1.33398 1.92996 1.33398 2.66634C1.33398 3.40272 1.93094 3.99967 2.66732 3.99967Z"
											stroke="#364153" stroke-width="1.33333" stroke-linecap="round"
											stroke-linejoin="round" />
									</svg>
								</a>
							</li>
							<li>
								<a class="w-10 h-10 rounded-full bg-lt-white-lilac text-lt-text-secondary flex justify-center items-center"
									href="mailto:?subject=<?php echo rawurlencode($post_title); ?>&body=<?php echo rawurlencode($post_url); ?>"
									aria-label="<?php esc_attr_e('Share via Email', 'lt-theme'); ?>">
									<svg width="16" height="16" viewBox="0 0 16 16" fill="none"
										xmlns="http://www.w3.org/2000/svg">
										<path
											d="M13.334 2.66699H2.66732C1.93094 2.66699 1.33398 3.26395 1.33398 4.00033V12.0003C1.33398 12.7367 1.93094 13.3337 2.66732 13.3337H13.334C14.0704 13.3337 14.6673 12.7367 14.6673 12.0003V4.00033C14.6673 3.26395 14.0704 2.66699 13.334 2.66699Z"
											stroke="#364153" stroke-width="1.33333" stroke-linecap="round"
											stroke-linejoin="round" />
										<path
											d="M14.6673 4.66699L8.68732 8.46699C8.4815 8.59594 8.24353 8.66433 8.00065 8.66433C7.75777 8.66433 7.5198 8.59594 7.31398 8.46699L1.33398 4.66699"
											stroke="#364153" stroke-width="1.33333" stroke-linecap="round"
											stroke-linejoin="round" />
									</svg>
								</a>
							</li>
							<li>
								<a class="w-10 h-10 rounded-full bg-lt-white-lilac text-lt-text-secondary flex justify-center items-center"
									href="<?php echo esc_url($post_url); ?>"
									aria-label="<?php esc_attr_e('Copy link', 'lt-theme'); ?>">
									<svg width="16" height="16" viewBox="0 0 16 16" fill="none"
										xmlns="http://www.w3.org/2000/svg">
										<g clip-path="url(#clip0_50547_5983)">
											<path
												d="M6.66602 8.66697C6.95232 9.04972 7.31759 9.36642 7.73705 9.5956C8.15651 9.82477 8.62035 9.96105 9.09712 9.99519C9.57388 10.0293 10.0524 9.96055 10.5002 9.79349C10.9481 9.62643 11.3548 9.36502 11.6927 9.02697L13.6927 7.02697C14.2999 6.3983 14.6359 5.55629 14.6283 4.6823C14.6207 3.80831 14.2701 2.97227 13.6521 2.35424C13.0341 1.73621 12.198 1.38565 11.324 1.37806C10.45 1.37046 9.60802 1.70644 8.97935 2.31364L7.83268 3.45364"
												stroke="#364153" stroke-width="1.33333" stroke-linecap="round"
												stroke-linejoin="round" />
											<path
												d="M9.33347 7.33381C9.04716 6.95106 8.68189 6.63435 8.26243 6.40518C7.84297 6.17601 7.37913 6.03973 6.90237 6.00559C6.4256 5.97144 5.94708 6.04023 5.49924 6.20729C5.0514 6.37435 4.64472 6.63576 4.3068 6.97381L2.3068 8.97381C1.69961 9.60248 1.36363 10.4445 1.37122 11.3185C1.37881 12.1925 1.72938 13.0285 2.3474 13.6465C2.96543 14.2646 3.80147 14.6151 4.67546 14.6227C5.54945 14.6303 6.39146 14.2943 7.02013 13.6871L8.16013 12.5471"
												stroke="#364153" stroke-width="1.33333" stroke-linecap="round"
												stroke-linejoin="round" />
										</g>
										<defs>
											<clipPath id="clip0_50547_5983">
												<rect width="16" height="16" fill="white" />
											</clipPath>
										</defs>
									</svg>
								</a>
							</li>
						</ul>
					</div><!-- End of Share -->
				</div>
				<!-- Right Side bar -->
				<div class="aside flex flex-col gap-8 lg:w-[30%] md:w-[35%] w-full">
					<?php
					$recent_posts_query = new WP_Query([
						'post_type' => 'post',
						'post_status' => 'publish',
						'posts_per_page' => 4,
						'post__not_in' => [$post_id],
						'orderby' => 'date',
						'order' => 'DESC',
					]);
					if ($recent_posts_query->have_posts()):
						?>
						<!-- Article -->
						<div class="article-list widget bg-lt-white border border-[#E5E7EB] rounded-[10px] p-6">
							<h2 class="widget-title flex items-center gap-2 text-body-lg font-medium text-lt-text-primary mb-6">
								<span class="icon shrink-0" aria-hidden="true">
									<svg width="18" height="18" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M14.6673 4L9.19398 9.47333L6.16065 6.44L1.33398 11.2667" stroke="#0A65FC"
											stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M10.667 4H14.667V8" stroke="#0A65FC" stroke-width="1.33333" stroke-linecap="round"
											stroke-linejoin="round" />
									</svg>
								</span> Trending Articles
							</h2>
							<ul class="flex flex-col gap-6 m-0">
								<?php
								while ($recent_posts_query->have_posts()):
									$recent_posts_query->the_post();
									$w_content = get_post_field('post_content', get_the_ID());
									$w_word_count = str_word_count(wp_strip_all_tags($w_content));
									$w_read_time = max(1, (int) ceil($w_word_count / 200));
									?>
									<li>
										<div class="flex items-center gap-4 group relative">
											<div
												class="w-20 h-20 shrink-0 rounded-[10px] overflow-hidden bg-lt-white-lilac img-full-cover">
												<?php echo get_the_post_thumbnail(get_the_ID(), 'thumbnail'); ?>
											</div>
											<div class="flex flex-col gap-2">
												<h3
													class="text-caption-md font-bold font-base tracking-[-0.15px] text-lt-text-primary line-clamp-2 transition-colors duration-320 group-hover:text-lt-brand">
													<?php the_title(); ?>
												</h3>
												<span class="flex items-center gap-1.5 text-caption-sm text-lt-text-placeholder">
													<span class="icon shrink-0" aria-hidden="true">
														<svg width="14" height="14" viewBox="0 0 16 16" fill="none"
															xmlns="http://www.w3.org/2000/svg">
															<g clip-path="url(#clip0_trend_read)">
																<path
																	d="M8.00065 14.6663C11.6825 14.6663 14.6673 11.6816 14.6673 7.99967C14.6673 4.31778 11.6825 1.33301 8.00065 1.33301C4.31875 1.33301 1.33398 4.31778 1.33398 7.99967C1.33398 11.6816 4.31875 14.6663 8.00065 14.6663Z"
																	stroke="#9CA3AF" stroke-width="1.33333" stroke-linecap="round"
																	stroke-linejoin="round" />
																<path d="M8 4V8L10.6667 9.33333" stroke="#9CA3AF" stroke-width="1.33333"
																	stroke-linecap="round" stroke-linejoin="round" />
															</g>
															<defs>
																<clipPath id="clip0_trend_read">
																	<rect width="16" height="16" fill="white" />
																</clipPath>
															</defs>
														</svg>
													</span>
													<span class="text">
														<?php
														echo esc_html(
															sprintf(
																/* translators: %d: number of minutes */
																_n('%d min read', '%d mins read', $w_read_time, 'lt-theme'),
																$w_read_time
															)
														);
														?>
													</span>
												</span>
											</div>
											<a class="stretched-link" href="<?php the_permalink(); ?>"></a>
										</div>
									</li>
								<?php endwhile; ?>
							</ul>
						</div>
						<?php
					endif;
					wp_reset_postdata();

					$blog_categories = get_categories([
						'hide_empty' => true,
						'orderby' => 'name',
						'order' => 'ASC',
					]);
					if (!empty($blog_categories)):
						?>
						<!-- Categories -->
						<div class="categories widget bg-lt-white border border-[#E5E7EB] rounded-[10px] p-6">
							<h2 class="widget-title flex items-center gap-2 text-body-lg font-medium text-lt-text-primary mb-6">
								Categories </h2>
							<ul class="flex flex-col gap-3 m-0">
								<?php foreach ($blog_categories as $cat): ?>
									<li>
										<a class="flex items-center justify-between gap-4 text-lt-text-secondary transition-colors duration-320 hover:text-lt-brand"
											href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
											<span class="font-semibold tracking-[0.02em]"><?php echo esc_html($cat->name); ?></span>
											<span
												class="text-lt-text-placeholder text-caption-md shrink-0">(<?php echo esc_html($cat->count); ?>)</span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<!-- Cf7 News Letter Form -->
					<div
						class="news-letter th-form-style theme-bg-grad rounded-[10px] p-6 text-lt-white flex flex-col gap-6 text-center">
						<div>
							<h2 class="widget-title text-body-lg font-medium mb-2 text-lt-white">Stay Updated</h2>
							<p class="text-caption-md text-lt-white/80">Get the latest design tips delivered to your inbox.</p>
						</div>
						<?php // swap in the real CF7 shortcode once available, e.g. echo do_shortcode('[contact-form-7 id="..." title="Newsletter"]'); ?>
					</div>
				</div>
			</div>
		</div>
	</section><!-- End of Main Blog Content -->
	<?php
	$related_cat_ids = !empty($all_cats) ? wp_list_pluck($all_cats, 'term_id') : [];
	$related_query = new WP_Query([
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => 6,
		'post__not_in' => [$post_id],
		'category__in' => $related_cat_ids,
	]);
	if (!$related_query->have_posts()):
		wp_reset_postdata();
		$related_query = new WP_Query([
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => 6,
			'post__not_in' => [$post_id],
		]);
	endif;
	if ($related_query->have_posts()):
		?>
		<!-- Related posts -->
		<section class="related-post py-16 bg-[#F9FAFB]">
			<div class="wd:container-bx container max-sm:px-0">
				<h2 class="section-title text-h4 font-medium tracking-[0.4px] text-lt-text-primary mb-8 max-sm:px-4">Related
					Articles</h2>
				<div class="slide-holder max-sm:pl-4">
					<div class="related-post-carousel swiper w-full">
						<div class="swiper-wrapper">
							<?php while ($related_query->have_posts()):
								$related_query->the_post(); ?>
								<div class="swiper-slide">
									<?php get_template_part('template-parts/blocks/blogs-filter/card'); ?>
								</div>
							<?php endwhile; ?>
						</div>
					</div>
				</div>
				<div class="swiper-pagination justify-center mt-8 static! max-sm:px-4"></div>
			</div>
		</section><!-- End of Related posts -->
		<?php
	endif;
	wp_reset_postdata();
	?>
</main><!-- #main -->
<?php
get_footer();
