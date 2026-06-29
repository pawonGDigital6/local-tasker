<div class="breadcrumb md:mb-10.5 mb-13.5">
	<?php if (function_exists('rank_math_the_breadcrumbs')): ?>
		<ul
			class="bread-lists flex items-center max-wd:justify-center gap-2.75 text-white text-caption-sm font-semibold tracking-[0.48px] mb-0">
			<?php
			// Fetch the raw breadcrumb array from Rank Math
			$crumbs = RankMath\Frontend\Breadcrumbs::get()->get_crumbs();

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