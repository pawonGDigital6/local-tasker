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
$class_name = 'acf-block lt-process sm:py-[3.375rem_0.875rem] pt-16 pb-0';

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
$op_pre_title = get_field('op_pre_title');
$op_sec_title = get_field('op_sec_title');
$op_sec_content = get_field('op_sec_content');
$button_blue = get_field('button_color');
$op_process_content = get_field('op_process_content');
$op_process_display_as_cont = get_field('op_display_process_as_content');
$size = 'full';
$is_single_video_use = get_field('op_single_vid_use');
$op_single_video = get_field('op_single_video');
$op_single_image = get_field('op_single_image');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="container">
		<div class="rows flex flex-wrap lg:gap-8 gap-12.5 lg:justify-between">
			<!-- Cols -->
			<div class="content-col lg:w-[50%] w-full">
				<!-- Pre Title -->
				<?php if ($op_pre_title): ?>
					<span
						class="block pre-title sm:mb-7 mb-4.5 text-caption-md uppercase text-[#2A429B] opacity-50"><?php echo esc_html($op_pre_title); ?></span>
				<?php endif; ?>
				<!-- Section Title -->
				<?php if ($op_sec_title): ?>
					<h2
						class="section-title capitalize sm:mb-6 mb-4.5 text-h3 leading-[1.30] font-bold font-semi-ext text-lt-text-primary sm:tracking-[-1px]">
						<?php echo esc_html($op_sec_title); ?>
					</h2>
				<?php endif; ?>
				<?php if ($op_sec_content): ?>
					<div class="section-content sm:text-body-lg text-caption-md"><?php echo $op_sec_content; ?></div>
				<?php endif; ?>
				<?php
				$link = get_field('op_sec_button');
				if ($link):
					$link_url = $link['url'];
					$link_title = $link['title'];
					$link_target = $link['target'] ? $link['target'] : '_self';
					?>
					<!-- Btn wrap -->
					<div class="btn-wrap sm:mt-10 smlr:mt-6 mt-3">
						<a class="btn  smlr:min-w-[270px] <?php echo $button_blue ? 'btn--brand' : 'btn--accent' ?>"
							href="<?php echo esc_url($link_url); ?>"
							target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
					</div>
				<?php endif; ?>
				<?php
				if ($is_single_video_use && !empty($op_single_video) && is_array($op_single_video)):
					$video_url = esc_url($op_single_video['url']);
					$mime_type = esc_attr($op_single_video['mime_type']);
					$video_title = esc_attr($op_single_video['title']);
					?>
					<!-- Single Video -->
					<div class="single-video flex justify-center sm:mt-14 w-screen ml-[calc((100%-8px)-50vw)] max-lg:hidden">
						<video autoplay loop playsinline muted>
							<source src="<?php echo $video_url; ?>" type="<?php echo $mime_type; ?>">
						</video>
					</div>
					<?php
					// 2. Default Fallback: If anything else is selected (or nothing is selected) and the image exists, render the image
				elseif (!empty($op_single_image)): ?>
					<!-- Single Image -->
					<div class="single-img flex justify-center sm:mt-14 w-screen ml-[calc((100%-8px)-50vw)] max-lg:hidden">
						<?php echo wp_get_attachment_image($op_single_image, $size); ?>
					</div>
				<?php endif; ?>
			</div>
			<!-- Process Col -->
			<div
				class="process-col lg:w-[46.4%] w-full h-full sm:border sm:border-[#E6E6E6] sm:rounded-[8px] bg-lt-white flex flex-col gap-6 sm:p-12 lg:my-[2.5625rem_-9.3125rem] z-10 lg:mb-19.25">
				<?php
				if ($op_process_display_as_cont):
					if ($op_process_content): ?>
						<div class="process-content">
							<?php echo wp_kses_post($op_process_content); ?>
						</div>
					<?php endif;
				else:
					if (have_rows('op_process_lists')): ?>
						<?php
						$count = 1;
						while (have_rows('op_process_lists')):
							the_row();
							$op_pro_title = get_sub_field('op_pro_title');
							$op_pro_text = get_sub_field('op_pro_text');
							?>
							<!-- List -->
							<div
								class="process-list flex sm:gap-6 gap-4 sm:pb-6 sm:border-b sm:border-[#E6E6E6] last:pb-0 last:border-0">
								<div
									class="numb inline-flex items-center justify-center shrink-0 sm:w-10 sm:h-10 w-8 h-8 p-1 text-lt-white bg-lt-brand rounded-full sm:text-body-xl leading-none font-bold">
									<?php echo $count; ?>
								</div>
								<div class="text sm:-mt-1"> <!-- Fixed negative margin syntax: sm:-mt-1 is clean Tailwind for ~4px -->
									<?php if ($op_pro_title): ?>
										<h3
											class="process-list__title sm:mb-2 mb-1 text-lt-brand sm:text-body-xl text-body sm:leading-[normal] font-bold font-semi-ext sm:tracking-[-0.8px]">
											<?php echo esc_html($op_pro_title); ?>
										</h3>
									<?php endif; ?>
									<?php if ($op_pro_text): ?>
										<div class="process-list__text text-lt-text-secondary sm:text-caption-md text-caption-sm">
											<?php echo $op_pro_text; ?>
										</div>
									<?php endif; ?>
								</div>
							</div><!-- End of List -->
							<?php
							$count++;
						endwhile;
						?>
					<?php endif; ?>
				<?php endif; ?>
			</div><!-- End of Process Col -->
		</div>
	</div>
	<?php
	if ($is_single_video_use && !empty($op_single_video) && is_array($op_single_video)):
		$video_url = esc_url($op_single_video['url']);
		$mime_type = esc_attr($op_single_video['mime_type']);
		$video_title = esc_attr($op_single_video['title']);
		?>
		<!-- Single Video -->
		<div class="single-video mt-16 lg:hidden">
			<video autoplay loop playsinline muted>
				<source src="<?php echo $video_url; ?>" type="<?php echo $mime_type; ?>">
			</video>
		</div>
		<?php
		// 2. Default Fallback: If anything else is selected (or nothing is selected) and the image exists, render the image
	elseif (!empty($op_single_image)): ?>
		<!-- Single Image -->
		<div class="single-img mt-16 lg:hidden">
			<?php echo wp_get_attachment_image($op_single_image, $size); ?>
		</div>
	<?php endif; ?>
</section>