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
$class_name = 'acf-block lt-team sm:pt-9.5 sm:pb-22 py-16';

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


?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="container">
		<div class="container-inner wd:px-6">
			<!-- Section Header -->
			<div class="section-header flex flex-wrap justify-between md:gap-8 gap-4.5 sm:mb-14 mb-12.5">
				<?php if ($team_sec_title = get_field('team_sec_title')): ?>
					<h2
						class="sec-title md:w-[36%] w-full capitalize smlr:text-h3 text-[1.875rem] smlr:leading-[1.30] leading-[1.26] font-bold font-semi-ext text-lt-text-primary sm:tracking-[-1px]">
						<?php echo esc_html($team_sec_title); ?>
					</h2>
				<?php endif; ?>
				<?php if ($team_sec_content = get_field('team_sec_content')): ?>
					<div class="sec-text md:w-[60.1%] w-full max-sm:text-caption-md sm:leading-[1.62]">
						<?php echo $team_sec_content; ?>
					</div>
				<?php endif; ?>
			</div><!-- End of Section Header -->
			<div class="team-single-img grid wd:grid-cols-2 gap-5">
				<!-- Rows -->
				<div class="team-list grid smlr:grid-cols-2 gap-5">
					<?php if (have_rows('team_list')): ?>
						<?php while (have_rows('team_list')):
							the_row();
							$size = 'full';
							$team_img = get_sub_field('team_img');
							$team_name = get_sub_field('team_name');
							$team_designation = get_sub_field('team_designation');
							$sec_add_img = get_field('sec_add_img');
							?>
							<div class="team-col">
								<div class="team-card relative flex items-end px-4 py-5 rounded-[8px] overflow-hidden min-h-[307px] sm:h-full">
									<?php
									if ($team_img):
										?>
										<div class="abs-img absolute inset-0 w-full h-full">
											<?php
											echo wp_get_attachment_image($team_img, $size, false, array('class' => 'w-full h-full object-cover'));
											?>
										</div>
										<?php
									endif;
									?>
									<div
										class="team-card__cont py-[16px_19px] px-6 min-w-[238px] bg-[#1A1A1ACC] backdrop-blur-[6.900000095367432px] rounded-[8px]">
										<?php if ($team_name): ?>
											<h3
												class="team-card__name mb-[1px] text-[1.375rem] leading-[normal] font-medium font-primary text-lt-white">
												<?php echo esc_html($team_name); ?>
											</h3>
										<?php endif; ?>
										<?php if ($team_designation): ?>
											<div class="team-card__desig text-lt-accent text-caption-sm leading-none tracking-[0.01em]">
												<?php echo esc_html($team_designation); ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				</div><!-- End of row -->
				<!-- Single Image -->
				<?php
				if ($sec_add_img):
					?>
					<div class="singe-img rounded-[8px] overflow-hidden min-h-[307px]">
						<?php
						echo wp_get_attachment_image($sec_add_img, $size, false, array('class' => 'w-full h-full object-cover'));
						?>
					</div>
					<?php
				endif;
				?>
				<!-- End of Single Image -->
			</div>
		</div>
	</div>
</section>