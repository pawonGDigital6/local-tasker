<?php
/**
 * Template Part: Blog Card
 *
 * Reusable card for the Blog With Filter ACF block.
 * Must be called from within a WP_Query loop after the_post() is invoked.
 *
 * Used by:
 *   - acf-blocks/blogs-filter/block.php          (initial server render)
 *   - inc/class-blogs-filter-ajax.php             (AJAX: filter + load more)
 *
 * @package YourTheme
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

// ─── Post Data ────────────────────────────────────────────────────────────────

$post_id = get_the_ID();
$post_url = get_permalink();
$post_title = get_the_title();
$post_date = get_the_date('M j, Y');
$post_thumb = get_the_post_thumbnail_url($post_id, 'large');

// ─── Estimated Read Time ──────────────────────────────────────────────────────
//
// Average reading speed: ~200 wpm. Minimum shown is 1 min.

$content = get_post_field('post_content', $post_id);
$word_count = str_word_count(wp_strip_all_tags($content));
$read_time = max(1, (int) ceil($word_count / 200));

// ─── Primary Category ─────────────────────────────────────────────────────────
//
// Use the first assigned category as the card badge.

$all_cats = get_the_category();
// $primary_cat = $all_cats[0] ?? null;
?>
<div class="blog-card flex flex-col justify-between">
	<div class="blog-card__up">
		<!-- Thumbnail -->
		<div class="blog-card__img smlr:aspect-[3/2.12] aspect-[3/1.99] relative rounded-[10px] overflow-hidden bg-lt-white-lilac mb-4">
			<a class="block" href="<?php echo esc_url($post_url); ?>" tabindex="-1" aria-hidden="true">
				<?php if ($post_thumb): ?>
					<img class="absolute w-full h-full object-cover" src="<?php echo esc_url($post_thumb); ?>"
						alt="<?php echo esc_attr($post_title); ?>" loading="lazy" decoding="async">
				<?php endif; ?>
			</a>
			<?php if (!empty($all_cats)): ?>
				<div class="absolute top-4 left-4 flex flex-wrap gap-2">
					<?php foreach ($all_cats as $cats): ?>
						<div class="cat p-[7px_13px] bg-lt-white rounded-[50px] inline-flex sm:text-caption-md text-caption-xs max-sm:font-bold leading-none tracking-[-0.15px]">
							<?php echo esc_html($cats->name); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div><!-- End Thumbnail -->
		<div class="blog-card__text-holder wd:pr-6 pr-2">
			<!-- Meta -->
			<div
				class="meta flex items-center gap-6 text-lt-text-placeholder sm:text-caption-md text-caption-sm sm:tracking-[-0.15px] tracking-[0.06px] mb-2">
				<time class="blog-card__posted-on" datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>">
					<?php echo esc_html($post_date); ?>
				</time>
				<span class="blog-card__read-time flex items-center gap-1">
					<span class="icon shrink-0" aria-hidden="true">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="url(#clip0_50547_3157)">
								<path
									d="M8.00065 14.6663C11.6825 14.6663 14.6673 11.6816 14.6673 7.99967C14.6673 4.31778 11.6825 1.33301 8.00065 1.33301C4.31875 1.33301 1.33398 4.31778 1.33398 7.99967C1.33398 11.6816 4.31875 14.6663 8.00065 14.6663Z"
									stroke="#9CA3AF" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M8 4V8L10.6667 9.33333" stroke="#9CA3AF" stroke-width="1.33333" stroke-linecap="round"
									stroke-linejoin="round" />
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
				</span>
			</div><!-- End Meta -->
			<!-- Title -->
			<h3
				class="blog-card__title mb-2 sm:text-body-xl text-body-lg sm:leading-[1.4] leading-[1.38] font-semibold font-base tracking-[-0.45px]">
				<a class="text-lt-text-primary transition-color duration-320 hover:text-lt-brand"
					href="<?php echo esc_url($post_url); ?>">
					<?php echo esc_html($post_title); ?>
				</a>
			</h3>
			<!-- Excerpt -->
			<div class="blog-card__excerpt line-clamp-2 max-sm:text-caption-md sm:tracking-[-0.31px] tracking-[-0.15px]">
				<?php echo esc_html(get_the_excerpt()); ?>
			</div>
		</div>
	</div><!-- End blog-card__up -->
	<!-- CTA -->
	<div class="btn-wrap sm:mt-2 mt-[15px]">
		<a class="blog-card__btn group inline-flex w-fit items-center gap-3 text-lt-brand md:text-body text-caption-sm leading-none sm:tracking-[-0.31px] tracking-[0.06px]"
			href="<?php echo esc_url($post_url); ?>">
			<span class="text"><?php esc_html_e('Read More', 'lt-theme'); ?></span>
			<span class="icon transition-transform duration-400 shrink-0 group-hover:translate-x-0.5 smlr:w-[12px] w-[10px] smlr:mt-[3px] mt-[1px]"
				aria-hidden="true">
				<svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g clip-path="url(#clip0_168_6617)">
						<path d="M1.16602 7.5H12.8327" stroke="#0A65FC" stroke-width="1.66667" stroke-linecap="round"
							stroke-linejoin="round" />
						<path d="M7 1.66699L12.8333 7.50033L7 13.3337" stroke="#0A65FC" stroke-width="1.66667"
							stroke-linecap="round" stroke-linejoin="round" />
					</g>
					<defs>
						<clipPath id="clip0_168_6617">
							<rect width="14" height="15" fill="white" />
						</clipPath>
					</defs>
				</svg>
			</span>
		</a>
	</div><!-- End CTA -->
</div><!-- End blog-card -->