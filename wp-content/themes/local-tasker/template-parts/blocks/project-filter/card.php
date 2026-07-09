<?php
/**
 * Template Part: Project Card
 *
 * Reusable card for the "Project With Filter" ACF block. Rendering the card in
 * ONE place guarantees the SSR grid and the AJAX (filter / view-all) responses
 * are pixel-identical.
 *
 * Used by:
 *   - src/blocks/project-with-filter/render.php   (initial server render)
 *   - inc/class-projects-filter-ajax.php           (AJAX: filter + view all)
 *
 * Must be called from within a WP_Query loop after the_post() is invoked.
 *
 * @package Local Tasker
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

// ─── Post Data ────────────────────────────────────────────────────────────────
$post_id           = get_the_ID();
$post_title        = get_the_title();
$post_thumb        = get_the_post_thumbnail_url($post_id, 'large');

// Per-project ACF fields.
//
// IMPORTANT: pass the explicit $post_id. During the block's initial (SSR)
// render, ACF is inside a block context, so get_field('name') WITHOUT an id
// resolves against the BLOCK's fields (empty) rather than the loop post — the
// fields would show up only after AJAX (which has no block context). Passing
// the post id makes it correct in both the SSR and AJAX render paths.
$pj_location       = get_field('pj_location', $post_id);
$pj_flooring_type  = get_field('pj_flooring_type', $post_id);
$proj_total_area   = get_field('proj_total_area', $post_id);
$pj_completed_date = get_field('pj_completed_date', $post_id);
?>
<article class="proj-card-two flex flex-col overflow-hidden rounded-[10px] bg-lt-white theme-shadow relative group">
	<!-- Feature Image -->
	<div class="proj-card__feat-img relative aspect-[4/3] w-full overflow-hidden bg-lt-white-lilac">
		<?php if ($post_thumb): ?>
			<img class="absolute inset-0 h-full w-full object-cover will-change-transform transition-transform duration-600 group-hover:scale-105 origin-center" src="<?php echo esc_url($post_thumb); ?>"
				alt="<?php echo esc_attr($post_title); ?>" loading="lazy" decoding="async">
		<?php endif; ?>
	</div>
	<!-- Body -->
	<div class="proj-card__body flex flex-col p-6">
		<?php if ($pj_location): ?>
			<!-- Location -->
			<span class="loc flex items-center gap-2 text-caption-md leading-5 tracking-[-0.15px] text-[#6a7282]">
				<span class="icon shrink-0" aria-hidden="true">
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13.3346 6.66634C13.3346 9.99501 9.64197 13.4617 8.40197 14.5323C8.28645 14.6192 8.14583 14.6662 8.0013 14.6662C7.85677 14.6662 7.71615 14.6192 7.60064 14.5323C6.36064 13.4617 2.66797 9.99501 2.66797 6.66634C2.66797 5.25185 3.22987 3.8953 4.23007 2.89511C5.23026 1.89491 6.58681 1.33301 8.0013 1.33301C9.41579 1.33301 10.7723 1.89491 11.7725 2.89511C12.7727 3.8953 13.3346 5.25185 13.3346 6.66634Z" stroke="#6A7282" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
						<path d="M8 8.66699C9.10457 8.66699 10 7.77156 10 6.66699C10 5.56242 9.10457 4.66699 8 4.66699C6.89543 4.66699 6 5.56242 6 6.66699C6 7.77156 6.89543 8.66699 8 8.66699Z" stroke="#6A7282" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</span>
				<span class="text"><?php echo esc_html($pj_location); ?></span>
			</span>
		<?php endif; ?>

		<!-- Title -->
		<h3 class="proj-card__title mt-2 font-base text-body-xl font-medium leading-[1.4] tracking-[-0.45px] text-lt-onyx">
			<?php echo esc_html($post_title); ?>
		</h3>

		<!-- Excerpt -->
		<?php if (has_excerpt() || get_the_content()): ?>
			<div class="exp mt-2 line-clamp-2 text-caption-md leading-5 tracking-[-0.15px] text-lt-text-muted">
				<?php echo esc_html(get_the_excerpt()); ?>
			</div>
		<?php endif; ?>

		<!-- Meta -->
		<?php if ($pj_flooring_type || $proj_total_area): ?>
			<div class="meta mt-4 flex gap-4">
				<?php if ($pj_flooring_type): ?>
					<span class="meta-type flex flex-col">
						<span class="val text-caption-md leading-5 tracking-[-0.15px] text-lt-onyx"><?php echo esc_html($pj_flooring_type); ?></span>
						<span class="lbl mt-1 text-caption-sm leading-4 text-[#6a7282]">Flooring Type</span>
					</span>
				<?php endif; ?>
				<?php if ($proj_total_area): ?>
					<span class="meta-area flex flex-col">
						<span class="val text-caption-md leading-5 tracking-[-0.15px] text-lt-onyx"><?php echo esc_html($proj_total_area); ?></span>
						<span class="lbl mt-1 text-caption-sm leading-4 text-[#6a7282]">Total Area</span>
					</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ($pj_completed_date): ?>
			<!-- Completed date -->
			<div class="completed-date mt-4 flex items-center gap-2 border-t border-[#f3f4f6] pt-[17px] text-caption-sm leading-4 text-[#6a7282]">
				<span class="icon shrink-0" aria-hidden="true">
					<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M4 1V3" stroke="#6A7282" stroke-linecap="round" stroke-linejoin="round" />
						<path d="M8 1V3" stroke="#6A7282" stroke-linecap="round" stroke-linejoin="round" />
						<path d="M9.5 2H2.5C1.94772 2 1.5 2.44772 1.5 3V10C1.5 10.5523 1.94772 11 2.5 11H9.5C10.0523 11 10.5 10.5523 10.5 10V3C10.5 2.44772 10.0523 2 9.5 2Z" stroke="#6A7282" stroke-linecap="round" stroke-linejoin="round" />
						<path d="M1.5 5H10.5" stroke="#6A7282" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</span>
				<span class="text"><?php echo esc_html($pj_completed_date); ?></span>
			</div>
		<?php endif; ?>
	</div>
	<a href="<?php the_permalink(); ?>" class="stretched-link"></a>
</article>
