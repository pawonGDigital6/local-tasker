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
$class_name = 'acf-block lt-contact bg-lt-white md:bg-[#f9fafb] smlr:py-16 py-8';

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

// ==================================== Dynamic Fields (global options)
$size               = 'full';
$cm_contact_logo    = get_field('cm_contact_logo', 'options');
$cm_contact_descrip = get_field('cm_contact_descrip', 'options');
$cm_ph_numb         = get_field('cm_ph_numb', 'options');
$cm_email           = get_field('cm_email', 'options');
$cm_cf7_shortcode   = get_field('cm_cf7_shortcode', 'options');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<div class="wd:container-bx container">
		<div class="flex flex-col gap-8 md:flex-row justify-between md:items-start md:gap-[55px]">
			<!-- Contact Info Card -->
			<div
				class="lt-contact__info theme-bg-grad relative flex w-full flex-col items-center gap-[26px] overflow-hidden rounded-[16px] p-[33px] text-center text-lt-white shadow-[inset_0px_2px_4px_0px_rgba(0,0,0,0.05)] md:w-[413px] md:shrink-0 md:gap-8 md:rounded-[20px] md:p-10">
				<?php if ($cm_contact_logo): ?>
					<?php echo wp_get_attachment_image($cm_contact_logo, $size, false, [
						'class' => 'h-[27px] md:h-8 w-auto',
						'alt'   => get_bloginfo('name'),
					]); ?>
				<?php endif; ?>

				<span class="h-px w-full bg-lt-white/20"></span>

				<?php if ($cm_contact_descrip): ?>
					<div class="text-[11.7px] leading-[1.6] text-lt-white md:text-caption-md [&_p]:mb-0">
						<?php echo $cm_contact_descrip; ?>
					</div>
				<?php endif; ?>

				<div class="flex w-full flex-col items-center gap-10 md:gap-12">
					<?php if ($cm_ph_numb): ?>
						<!-- Phone -->
						<div class="flex w-full items-center gap-5 md:gap-6">
							<span class="inline-flex size-[27px] shrink-0 items-center justify-center text-lt-white md:size-8" aria-hidden="true">
								<svg viewBox="0 0 32 32" fill="none" class="h-full w-full" xmlns="http://www.w3.org/2000/svg">
									<path d="M16.0615 1.1377C19.9784 1.1377 23.7352 2.69419 26.5049 5.46387C29.2744 8.23343 30.83 11.9896 30.8301 15.9062C30.8301 19.8231 29.2745 23.5799 26.5049 26.3496C23.7352 29.1193 19.9784 30.6758 16.0615 30.6758C12.1447 30.6757 8.3878 29.1192 5.61816 26.3496C2.84869 23.58 1.29297 19.823 1.29297 15.9062C1.29309 11.9897 2.84882 8.23341 5.61816 5.46387C8.3878 2.69424 12.1447 1.13775 16.0615 1.1377ZM10.9404 10.4219C9.05318 8.07537 7.79655 9.87679 6.76465 10.9092C5.57356 12.0995 6.70222 16.5383 11.0869 20.9238C15.4729 25.3091 19.9108 26.4365 21.1016 25.248C22.1331 24.215 23.9371 22.9567 21.5898 21.0713C19.2417 19.1847 18.5852 20.1125 17.5205 21.1758C16.7762 21.9212 14.8944 20.3679 13.2695 18.7432C11.6453 17.1168 10.0928 15.2357 10.835 14.4902C11.9 13.4255 12.8271 12.7686 10.9404 10.4219ZM15.7705 10.04C15.3898 10.04 15.082 10.3479 15.082 10.7285C15.0821 11.1092 15.3899 11.417 15.7705 11.417C18.4306 11.4172 20.5947 13.5816 20.5947 16.2402C20.595 16.6207 20.9027 16.9287 21.2832 16.9287C21.6636 16.9286 21.9714 16.6206 21.9717 16.2402C21.9717 12.8217 19.1904 10.0402 15.7705 10.04ZM16.4277 6.35059C16.0471 6.35059 15.7393 6.65845 15.7393 7.03906C15.7393 7.41973 16.0471 7.72754 16.4277 7.72754C20.7597 7.72757 24.2831 11.2515 24.2832 15.583C24.2832 15.9636 24.5911 16.2714 24.9717 16.2715C25.3523 16.2715 25.6602 15.9637 25.6602 15.583C25.66 10.4927 21.518 6.35062 16.4277 6.35059Z" fill="currentColor" />
								</svg>
							</span>
							<span class="flex flex-col items-start gap-3 text-left">
								<span class="text-[11.7px] text-lt-white/80 md:text-caption-md">Phone</span>
								<a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $cm_ph_numb)); ?>"
									class="text-[15px] font-bold text-lt-white transition-opacity duration-300 hover:opacity-80 focus-visible:opacity-80 md:text-body-lg">
									<?php echo esc_html($cm_ph_numb); ?>
								</a>
							</span>
						</div>
					<?php endif; ?>

					<?php if ($cm_email): ?>
						<!-- Email -->
						<div class="flex w-full items-center gap-5 md:gap-6">
							<span class="inline-flex size-[27px] shrink-0 items-center justify-center text-lt-white md:size-8" aria-hidden="true">
								<svg viewBox="0 0 32 32" fill="none" class="h-full w-full" xmlns="http://www.w3.org/2000/svg">
									<path d="M26.9542 25.146C26.3097 23.2279 23.7411 22.0273 21.9097 21.2225C21.1923 20.9082 19.206 20.3751 18.9673 19.4717C18.8819 19.1463 18.8934 18.8394 18.9635 18.5453C18.852 18.5672 18.7388 18.5785 18.6252 18.5789H17.4319C16.9741 18.5784 16.5351 18.3963 16.2114 18.0725C15.8877 17.7487 15.7056 17.3097 15.7052 16.8519C15.7052 15.9005 16.4796 15.1271 17.4319 15.1271H18.6252C19.0195 15.1271 19.3932 15.2605 19.6947 15.4986C20.1236 15.4421 20.5469 15.3494 20.9603 15.2215C21.4822 14.1281 21.8892 12.8205 21.9804 11.6951C22.3699 6.87973 19.4179 4.06245 15.1852 4.54949C12.1078 4.90373 10.2694 7.19845 10.0707 10.1527C9.86969 13.1639 10.9862 15.3879 12.1721 17.0193C12.6915 17.7325 13.2371 18.1911 13.1532 19.0506C13.0559 20.0669 11.9692 20.3501 11.1919 20.6625C10.271 21.0324 9.27898 21.5937 8.8105 21.8532C7.19674 22.7444 5.42554 23.8176 5.02746 25.2858C4.14586 28.5396 7.12314 29.5252 9.58106 29.9802C11.6905 30.3693 14.0691 30.4001 16.0255 30.4001C19.5644 30.4001 25.9279 30.2583 26.9542 27.5981C27.246 26.8433 27.1209 25.6404 26.9542 25.146Z" fill="currentColor" />
									<path d="M19.3798 16.3654C19.298 16.2396 19.1863 16.1362 19.0545 16.0645C18.9227 15.9928 18.7752 15.9551 18.6252 15.9548H17.4319C17.3119 15.9516 17.1924 15.9725 17.0805 16.0162C16.9686 16.0599 16.8667 16.1256 16.7806 16.2094C16.6946 16.2932 16.6262 16.3934 16.5795 16.504C16.5328 16.6147 16.5087 16.7336 16.5087 16.8537C16.5087 16.9738 16.5328 17.0927 16.5795 17.2033C16.6262 17.314 16.6946 17.4142 16.7806 17.4979C16.8667 17.5817 16.9686 17.6474 17.0805 17.6912C17.1924 17.7349 17.3119 17.7558 17.4319 17.7526H18.6252C18.7894 17.7524 18.9503 17.707 19.0903 17.6212C19.2302 17.5354 19.3438 17.4127 19.4185 17.2665C21.0822 17.1356 22.5292 16.6274 23.5449 15.877C23.7782 16.0274 24.054 16.1154 24.3516 16.1154H24.4265C24.6236 16.1155 24.8188 16.0767 25.001 16.0012C25.1831 15.9257 25.3486 15.8151 25.4879 15.6756C25.6272 15.5361 25.7377 15.3705 25.813 15.1883C25.8883 15.0061 25.9269 14.8108 25.9266 14.6137V11.6159C25.9267 11.3328 25.8464 11.0555 25.695 10.8162C25.5436 10.5769 25.3275 10.3855 25.0716 10.2642C24.8514 5.45049 20.8665 1.59961 15.9986 1.59961C11.1308 1.59961 7.14519 5.45049 6.92599 10.2642C6.66993 10.3853 6.45355 10.5766 6.30205 10.8159C6.15056 11.0553 6.07019 11.3327 6.07031 11.6159V14.6137C6.07006 14.8107 6.10864 15.0059 6.18384 15.188C6.25904 15.3701 6.36939 15.5357 6.50859 15.6751C6.64778 15.8146 6.8131 15.9253 6.99508 16.0008C7.17706 16.0764 7.37215 16.1153 7.56919 16.1154H7.64503C7.84215 16.1154 8.03732 16.0765 8.21939 16.001C8.40147 15.9255 8.56688 15.8149 8.70617 15.6754C8.84546 15.5359 8.95589 15.3704 9.03117 15.1882C9.10645 15.006 9.14508 14.8108 9.14487 14.6137V11.6159C9.14473 11.3367 9.06649 11.0632 8.91901 10.8261C8.77154 10.5891 8.56069 10.398 8.31031 10.2745C8.52439 6.21849 11.8902 2.98553 15.9986 2.98553C20.1055 2.98553 23.4729 6.21849 23.686 10.2745C23.4359 10.3982 23.2253 10.5894 23.078 10.8264C22.9307 11.0634 22.8526 11.3369 22.8524 11.6159V14.6137C22.8524 14.8127 22.8911 14.998 22.9593 15.1711C22.085 15.7967 20.8124 16.2425 19.3798 16.3654Z" fill="currentColor" />
								</svg>
							</span>
							<span class="flex flex-col items-start gap-3 text-left">
								<span class="text-[11.7px] text-lt-white/80 md:text-caption-md">Email</span>
								<a href="mailto:<?php echo esc_attr($cm_email); ?>"
									class="break-all text-[15px] font-bold text-lt-white transition-opacity duration-300 hover:opacity-80 focus-visible:opacity-80 md:text-body-lg">
									<?php echo esc_html($cm_email); ?>
								</a>
							</span>
						</div>
					<?php endif; ?>

					<?php if (have_rows('cm_social_links', 'options')): ?>
						<!-- Follow us -->
						<div class="flex flex-col items-center gap-2">
							<span class="text-[11.7px] text-lt-white/80 md:text-caption-md">Follow us</span>
							<ul class="m-0 flex list-none items-center gap-[13px] p-0 md:gap-4" role="list">
								<?php while (have_rows('cm_social_links', 'options')):
									the_row();
									$cm_social_link = get_sub_field('cm_social_link', 'options');
									$cm_social_icon = get_sub_field('cm_social_icon', 'options');
									$social_host = $cm_social_link ? wp_parse_url($cm_social_link, PHP_URL_HOST) : '';
									?>
									<li>
										<a href="<?php echo esc_url($cm_social_link ?: '#'); ?>"
											class="p-2 inline-flex size-[33px] items-center justify-center rounded-full bg-lt-white transition-transform duration-300 hover:-translate-y-0.5 focus-visible:-translate-y-0.5 md:size-10"
											<?php echo $social_host ? 'aria-label="' . esc_attr($social_host) . '"' : ''; ?> target="_blank">
											<?php if ($cm_social_icon): ?>
												<?php echo wp_get_attachment_image($cm_social_icon, $size, false, [
													'class' => 'size-5 md:size-6 w-auto object-contain',
													'alt'   => '',
												]); ?>
											<?php endif; ?>
										</a>
									</li>
								<?php endwhile; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div><!-- End Contact Info Card -->

			<!-- Contact Form -->
			<div
				class="lt-contact__form th-form-style w-full rounded-[24px] border border-[#f3f4f6] bg-lt-white p-[25px] shadow-[0px_1px_1.5px_0px_rgba(0,0,0,0.1),0px_1px_1px_0px_rgba(0,0,0,0.1)] md:flex-1 md:rounded-[20px] md:border-[#e6e6e6] md:p-8 md:shadow-none">
				<?php if ($cm_cf7_shortcode): ?>
					<?php echo do_shortcode($cm_cf7_shortcode); ?>
				<?php endif; ?>
			</div><!-- End Contact Form -->
		</div>
	</div>
</section>
