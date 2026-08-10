<?php
/**
 * "How it works" — shared, context-free explainer card.
 *
 * Blue-tinted header + numbered steps + an optional summary box. Designed to sit
 * beside `template-parts/components/area-calculator.php` in a two-column row.
 *
 * Used by the WooCommerce single product page (where the summary box is the
 * product "Quick estimate", fed by product-single.js through element ids) and by
 * the standalone `acf-block/area-calculator` block (where the summary box mirrors
 * the sibling calculator's totals — no pricing, no product data).
 *
 * @param array $args {
 *     Optional. Component arguments.
 *
 *     @type string $title    Card heading. Default "How it works".
 *     @type array  $steps    List of steps, each `['title' => '', 'desc' => '']`.
 *     @type array  $estimate {
 *         Summary box. Omit or leave empty to hide the box entirely.
 *
 *         @type string $title Box heading. Default "Quick estimate".
 *         @type array  $rows  {
 *             Each row:
 *             @type string $label    Left-hand label.
 *             @type string $value    Initial right-hand value.
 *             @type string $id       Optional element id for the value (host JS hook).
 *             @type string $mirror   Optional area-calculator field to mirror: "total" | "wastage".
 *             @type bool   $divider  Adds the top rule used by the last row.
 *             @type bool   $emphasis Renders the value in bold brand colour.
 *         }
 *     }
 *     @type string $mirror_of Root id of the area calculator this box mirrors.
 *     @type string $class     Extra class names for the root element.
 * }
 *
 * @package local-tasker
 */

defined('ABSPATH') || exit;

$lt_hiw = wp_parse_args(
	isset($args) && is_array($args) ? $args : array(),
	array(
		'title' => __('How it works', 'local-tasker'),
		'steps' => array(),
		'estimate' => array(),
		'mirror_of' => '',
		'class' => '',
	)
);

$lt_hiw_estimate = wp_parse_args(
	is_array($lt_hiw['estimate']) ? $lt_hiw['estimate'] : array(),
	array(
		'title' => __('Quick estimate', 'local-tasker'),
		'rows' => array(),
	)
);
?>
<div
	class="lt-how-it-works h-full bg-lt-white border border-[#E2DDD7] rounded-xl overflow-hidden<?php echo $lt_hiw['class'] ? ' ' . esc_attr($lt_hiw['class']) : ''; ?>">

	<!-- Header -->
	<div class="px-6 py-5 bg-[#0A65FC06] border-b-[2px] border-lt-brand">
		<h2 class="text-body font-bold font-semi-ext text-lt-text-primary m-0">
			<?php echo esc_html($lt_hiw['title']); ?>
		</h2>
	</div>

	<div class="p-5">

		<?php if (!empty($lt_hiw['steps'])): ?>
			<ol class="flex flex-col gap-7 list-none p-0 m-0" role="list">
				<?php
				foreach ($lt_hiw['steps'] as $i => $step):
					$num = $i + 1;
					?>
					<li class="lt-how-it-works__step flex gap-5 relative">

						<!-- Number + connector line -->
						<div class="flex flex-col items-center shrink-0">
							<div class="w-8 h-8 rounded-full bg-lt-brand/[0.07] flex items-center justify-center shrink-0">
								<span class="font-bold text-lt-brand leading-none">
									<?php echo esc_html($num); ?>
								</span>
							</div>
						</div>

						<!-- Content -->
						<div class="itemss">
							<?php if (!empty($step['title'])): ?>
								<div class="font-medium text-lt-text-primary leading-[1.4] m-0">
									<?php echo esc_html($step['title']); ?>
								</div>
							<?php endif; ?>
							<?php if (!empty($step['desc'])): ?>
								<p class="text-caption-md text-lt-text-muted leading-[1.5] mt-[3px] m-0">
									<?php echo esc_html($step['desc']); ?>
								</p>
							<?php endif; ?>
						</div>

					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

		<?php if (!empty($lt_hiw_estimate['rows'])): ?>
			<!-- Summary box — values are written by the host (product calculator or sibling area calculator) -->
			<div class="lt-how-it-works__estimate mt-5 rounded-xl border border-[#0A65FC18] bg-[#0A65FC06] p-4"
				<?php if ($lt_hiw['mirror_of']): ?>data-lt-area-mirror="<?php echo esc_attr($lt_hiw['mirror_of']); ?>" <?php endif; ?>>
				<?php if (!empty($lt_hiw_estimate['title'])): ?>
					<p class="text-caption-md font-bold text-lt-text-primary m-0 mb-3">
						<?php echo esc_html($lt_hiw_estimate['title']); ?>
					</p>
				<?php endif; ?>
				<div class="flex flex-col gap-2">
					<?php
					foreach ($lt_hiw_estimate['rows'] as $row):
						$row = wp_parse_args(
							$row,
							array(
								'label' => '',
								'value' => '',
								'id' => '',
								'mirror' => '',
								'divider' => false,
								'emphasis' => false,
							)
						);
						$row_class = 'flex items-center justify-between gap-4';
						if ($row['divider']) {
							$row_class .= ' pt-2 border-t border-lt-brand/20';
						}
						$value_class = $row['emphasis']
							? 'text-caption-sm font-bold text-lt-brand'
							: 'text-caption-sm font-semibold text-lt-text-primary';
						?>
						<div class="<?php echo esc_attr($row_class); ?>">
							<span class="text-caption-sm text-lt-text-muted"><?php echo esc_html($row['label']); ?></span>
							<span class="<?php echo esc_attr($value_class); ?>"
								<?php if ($row['id']): ?>id="<?php echo esc_attr($row['id']); ?>" <?php endif; ?>
								<?php if ($row['mirror']): ?>data-lt-area-mirror-field="<?php echo esc_attr($row['mirror']); ?>" <?php endif; ?>><?php echo esc_html($row['value']); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

	</div>

</div><!-- /.lt-how-it-works -->
