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
			<div class="overlay absolute inset-0 w-full h-full pointer-events-none z-1" style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0) 100%);
"></div>
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
						<h1 class="sm:text-h2 text-[1.875rem] leading-[1.20] sm:tracking-[-0.02em] tracking-[0.4px] sm:mb-6 mb-[13px]">
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
						<time class="meta__posted-on flex items-center sm:gap-2 gap-1.5" datetime="<?php echo esc_attr($post_date); ?>">
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
</main><!-- #main -->
<?php
get_footer();
