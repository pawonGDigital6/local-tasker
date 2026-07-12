<?php
/**
 * ACF Block: Location Tab
 *
 * Renders a set of store locations as a tabbed interface. Each row of the
 * `lc_tab_lists` repeater is one location: the tab button label plus a panel
 * containing contact / opening-hours info and an embedded map.
 *
 * @param array $block The block settings and attributes.
 */

// Support custom "anchor" values.
$anchor = '';
if (!empty($block['anchor'])) {
	$anchor = 'id="' . esc_attr($block['anchor']) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'acf-block lt-location-tab py-16';

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

// A stable, unique id keeps panel ids/ARIA unique when several of these blocks
$block_uid = 'lc-' . (!empty($block['id']) ? sanitize_html_class($block['id']) : wp_unique_id());
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="wd:container-bx container">
		<?php if (have_rows('lc_tab_lists')): ?>
			<div class="lc-tab">
				<!-- Tab Head -->
				<div class="lc-tab__head">
					<ul role="tablist">
						<?php
						$ti = 0;
						while (have_rows('lc_tab_lists')):
							the_row();
							$ti++;
							$lc_tab_heading = get_sub_field('lc_tab_heading');
							if (!$lc_tab_heading) {
								continue;
							}
							?>
							<li role="presentation">
								<button type="button" role="tab"
									class="lc-tab__btn sm:tracking-[0.02em]! font-semibold!<?php echo 1 === $ti ? ' active' : ''; ?>"
									id="<?php echo esc_attr($block_uid . '-tab-' . $ti); ?>"
									data-tab-target="<?php echo esc_attr($block_uid . '-panel-' . $ti); ?>"
									aria-controls="<?php echo esc_attr($block_uid . '-panel-' . $ti); ?>"
									aria-selected="<?php echo 1 === $ti ? 'true' : 'false'; ?>">
									<?php echo esc_html($lc_tab_heading); ?>
								</button>
							</li>
						<?php endwhile; ?>
					</ul>
				</div><!-- End of Tab Head -->
				<!-- Tab Panels -->
				<div class="lc-tab__panels">
					<?php
					$pi = 0;
					while (have_rows('lc_tab_lists')):
						the_row();
						$pi++;
						$location_heading = get_sub_field('location_heading');
						$lc_tab_ph_numb = get_sub_field('lc_tab_ph_numb');
						$lc_tab_wo_hours = get_sub_field('lc_tab_wo_hours');
						$lc_tab_wh_text = get_sub_field('lc_tab_wh_text');
						$lc_tab_map = get_sub_field('lc_tab_location_map');
						$link = get_sub_field('lc_tab_cta_btn');
						?>
						<div id="<?php echo esc_attr($block_uid . '-panel-' . $pi); ?>"
							class="lc-tab__panel<?php echo 1 === $pi ? ' active' : ''; ?>" role="tabpanel"
							aria-labelledby="<?php echo esc_attr($block_uid . '-tab-' . $pi); ?>">
							<div class="flex flex-col lg:flex-row gap-5">
								<!-- Info -->
								<div
									class="lc-tab__info flex flex-col gap-6 rounded-[20px] border border-[#e5e7eb] bg-lt-white-lilac sm:p-8 p-6 lg:w-[413px] lg:shrink-0"
									style="box-shadow: 0px 2px 4px 0px #0000000D inset;">
									<?php if ($location_heading): ?>
										<h3 class="text-h5 font-bold leading-[1.20] sm:tracking-[-0.02em] text-lt-onyx">
											<?php echo esc_html($location_heading); ?>
										</h3>
									<?php endif; ?>
									<hr class="m-0 h-px w-full border-0 bg-[#e5e7eb]" />
									<div class="flex flex-col gap-6">
										<?php if ($lc_tab_ph_numb): ?>
											<!-- Phone -->
											<div class="flex items-center gap-3">
												<span class="icon shrink-0 text-lt-brand" aria-hidden="true">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
														stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round">
														<path
															d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
													</svg>
												</span>
												<a class="text-body-lg font-bold text-lt-onyx transition-colors duration-320 hover:text-lt-brand"
													href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $lc_tab_ph_numb)); ?>">
													<?php echo esc_html($lc_tab_ph_numb); ?>
												</a>
											</div>
										<?php endif; ?>
										<?php if ($lc_tab_wo_hours || $lc_tab_wh_text): ?>
											<!-- Opening Hours -->
											<div class="flex items-start gap-3">
												<span class="icon shrink-0 text-lt-brand mt-0.5" aria-hidden="true">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
														stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round">
														<circle cx="12" cy="12" r="10" />
														<polyline points="12 6 12 12 16 14" />
													</svg>
												</span>
												<div class="flex flex-col gap-2">
													<?php if ($lc_tab_wo_hours): ?>
														<span class="text-body-lg font-bold text-lt-onyx">
															<?php echo esc_html($lc_tab_wo_hours); ?>
														</span>
													<?php endif; ?>
													<?php if ($lc_tab_wh_text): ?>
														<div class="oh-content leading-[1.5] tracking-[0.02em] text-lt-onyx">
															<?php echo $lc_tab_wh_text; ?>
														</div>
													<?php endif; ?>
												</div>
											</div>
										<?php endif; ?>
									</div>
									<?php
									if ($link):
										$link_url = $link['url'];
										$link_title = $link['title'];
										$link_target = $link['target'] ? $link['target'] : '_self';
										?>
										<div class="btn-wrap mt-2">
											<a class="btn btn--brand self-start max-sm:w-full" href="<?php echo esc_url($link_url); ?>"
												target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
										</div>
									<?php endif; ?>
								</div><!-- End of Info -->
								<!-- Map -->
								<?php if ($lc_tab_map): ?>
									<div
										class="lc-tab__map relative w-full overflow-hidden rounded-[20px] bg-lt-white-lilac min-h-[398px] lg:min-h-[470px] [&_iframe]:absolute [&_iframe]:inset-0 [&_iframe]:h-full [&_iframe]:w-full [&_iframe]:border-0 border border-[#E6E6E6]">
										<?php echo $lc_tab_map; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endwhile; ?>
				</div><!-- End of Tab Panels -->
			</div>
		<?php endif; ?>
	</div>
</section>