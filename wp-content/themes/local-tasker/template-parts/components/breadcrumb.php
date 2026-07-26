<?php
/**
 * Breadcrumb component (Rank Math).
 *
 * Rank Math only prepends a parent crumb on singular views when the post type
 * was registered with `has_archive` enabled — see
 * RankMath\Frontend\Breadcrumbs::add_crumbs_post_type_archive().
 *
 * Post types such as lt_store_location and lt_location are registered with
 * archives disabled, because their landing pages are real editor-managed Pages
 * rather than post type archives. Enabling `has_archive` is not an option: the
 * archive slug is derived from the rewrite slug, so it would collide with those
 * existing pages. Instead the parent crumb is resolved from the landing Page
 * below, keeping trails consistent without touching post type registration.
 *
 * @package local-tasker
 */

if (!function_exists('local_tasker_breadcrumb_parent_pages')) {

	/**
	 * Map of post type => landing page path.
	 *
	 * Paths are resolved at runtime with get_page_by_path(), so no page IDs or
	 * URLs are hard coded. Nested paths ('company/our-locations') work too.
	 *
	 * @return array<string, string>
	 */
	function local_tasker_breadcrumb_parent_pages()
	{
		/**
		 * Filters the post type => landing page path map used for breadcrumbs.
		 *
		 * @param array<string, string> $map Post type key => page path.
		 */
		return (array) apply_filters(
			'local_tasker/breadcrumbs/parent_pages',
			array(
				'lt_store_location' => 'store-location',
				'lt_location'       => 'locations',
				'lt_projects'       => 'project',
			)
		);
	}
}

if (!function_exists('local_tasker_breadcrumb_add_parent_page')) {

	/**
	 * Insert the landing page crumb on singular views of the mapped post types.
	 *
	 * @param array $crumbs Rank Math breadcrumb trail.
	 * @return array
	 */
	function local_tasker_breadcrumb_add_parent_page($crumbs)
	{
		$map = local_tasker_breadcrumb_parent_pages();

		if (empty($crumbs) || !is_singular(array_keys($map))) {
			return $crumbs;
		}

		$post_type = get_post_type();
		$page      = get_page_by_path($map[$post_type]);

		// Bail quietly if the page was renamed, trashed or never created —
		// a missing crumb beats a link to a 404.
		if (!$page || 'publish' !== $page->post_status) {
			return $crumbs;
		}

		// Label mirrors what Rank Math shows for post types that do have an
		// archive (the plural label), so trails stay consistent site-wide.
		$type_object = get_post_type_object($post_type);

		$crumb = array(
			$type_object ? $type_object->labels->name : get_the_title($page),
			get_permalink($page),
			'hide_in_schema' => false,
		);

		// Already present — nothing to do.
		foreach ($crumbs as $existing) {
			if (!empty($existing[1]) && untrailingslashit($existing[1]) === untrailingslashit($crumb[1])) {
				return $crumbs;
			}
		}

		// If an archive is ever enabled for this post type, Rank Math will add
		// its own archive crumb. Replace it rather than showing both.
		$archive_link = get_post_type_archive_link($post_type);
		if ($archive_link) {
			foreach ($crumbs as $index => $existing) {
				if (!empty($existing[1]) && untrailingslashit($existing[1]) === untrailingslashit($archive_link)) {
					$crumbs[$index] = $crumb;
					return $crumbs;
				}
			}
		}

		// Otherwise slot it in where the archive crumb would have gone: directly
		// after Home, which Rank Math adds first when enabled.
		$position = (untrailingslashit($crumbs[0][1] ?? '') === untrailingslashit(home_url('/'))) ? 1 : 0;

		array_splice($crumbs, $position, 0, array($crumb));

		return $crumbs;
	}
}
?>
<div class="breadcrumb md:mb-10.5 mb-13.5">
	<?php if (function_exists('rank_math_the_breadcrumbs')): ?>
		<ul
			class="bread-lists flex items-center max-wd:justify-center gap-2.75 text-white text-caption-sm font-semibold tracking-[0.48px] mb-0">
			<?php
			// Fetch the raw breadcrumb array from Rank Math
			$crumbs = RankMath\Frontend\Breadcrumbs::get()->get_crumbs();

			// Restore the parent landing page crumb for archive-less post types.
			$crumbs = local_tasker_breadcrumb_add_parent_page($crumbs);

			if (!empty($crumbs) && is_array($crumbs)) {
				$count = count($crumbs);
				foreach ($crumbs as $key => $crumb) {
					$is_last = ($key + 1 === $count);

					// Render Breadcrumb Link
					if ($is_last || empty($crumb[1])) {
						echo '<li class="active">' . esc_html($crumb[0]) . '</li>';
					} else {
						echo '<li><a class="text-inherit hover:text-lt-accent transition-colors duration-320" href="' . esc_url($crumb[1]) . '">' . esc_html($crumb[0]) . '</a></li>';
					}

					// Render Custom SVG Separator (if not the last element)
					if (!$is_last) {
						echo '<li class="sep">';
						echo '<svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">';
						echo '<path d="M0.46405 0.463867L4.83905 4.83887L0.46405 9.21387" stroke="white" stroke-width="1.3125" />';
						echo '</svg>';
						echo '</li>';
					}
				}
			}
			?>
		</ul>
	<?php endif; ?>
</div>
