<?php
/**
 * Template Part: search Product Card Card
 *
 * @package Local Tasker
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

// ─── Post Data ────────────────────────────────────────────────────────────────

$post_id = get_the_ID();
$post_url = get_permalink();
$post_title = get_the_title();
$post_thumb = get_the_post_thumbnail_url($post_id, 'large');
?>
<div class="blog-card flex flex-col justify-between">
	<div class="blog-card__up">
		<!-- Thumbnail -->
		<div
			class="blog-card__img smlr:aspect-[3/2.12] aspect-[3/1.99] relative rounded-[10px] overflow-hidden bg-lt-white-lilac mb-4">
			<a class="block" href="<?php echo esc_url($post_url); ?>" tabindex="-1" aria-hidden="true">
				<?php if ($post_thumb): ?>
					<img class="absolute w-full h-full object-cover" src="<?php echo esc_url($post_thumb); ?>"
						alt="<?php echo esc_attr($post_title); ?>" loading="lazy" decoding="async">
				<?php endif; ?>
			</a>
		</div><!-- End Thumbnail -->
		<div class="blog-card__text-holder wd:pr-6 pr-2">
			<!-- Title -->
			<h3
				class="blog-card__title mb-2 sm:text-body-xl text-body-lg sm:leading-[1.4] leading-[1.38] font-semibold font-base tracking-[-0.45px]">
				<a class="text-lt-text-primary transition-color duration-320 hover:text-lt-brand"
					href="<?php echo esc_url($post_url); ?>">
					<?php echo esc_html($post_title); ?>
				</a>
			</h3>
		</div>
	</div><!-- End blog-card__up -->
	<!-- CTA -->
	<div class="btn-wrap sm:mt-2 mt-[15px]">
		<a class="blog-card__btn group inline-flex w-fit items-center gap-3 text-lt-brand md:text-body text-caption-sm leading-none sm:tracking-[-0.31px] tracking-[0.06px]"
			href="<?php echo esc_url($post_url); ?>">
			<span class="text"><?php esc_html_e('View Product', 'lt-theme'); ?></span>
			<span
				class="icon transition-transform duration-400 shrink-0 group-hover:translate-x-0.5 smlr:w-[12px] w-[10px] smlr:mt-[3px] mt-[1px]"
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