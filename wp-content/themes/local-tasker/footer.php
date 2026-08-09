<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package local-tasker
 */

$footer_logo = get_field('footer_logo', 'options');
$nav_heading_one = get_field('nav_heading_one', 'options');
$nav_heading_two = get_field('nav_heading_two', 'options');
$nav_heading_three = get_field('nav_heading_three', 'options');
$nav_heading_four = get_field('nav_heading_four', 'options');
$copyright_text = get_field('copyright_text', 'options');
?>
<!-- Global Quote Form -->
<div class="global-quote-form-pop fixed inset-0 bg-[rgba(0,0,0,0.8)] z-99 h-dvh overflow-auto invisible opacity-0">
	<div class="container h-full w-full">
		<div class="holder py-10">
			<div class="gqf-form max-w-[1224px] mx-auto theme-shadow rounded-[20px] overflow-hidden">
				<!-- Header -->
				<div class="gqf-form__header sm:p-10 p-6 theme-bg-grad">
					<div class="top sm:pb-8 pb-6 border-b border-[#FFFFFF33] relative">
						<div class="logo flex justify-center">
							<img class="sm:w-[228px] w-[140px]"
								src="<?php echo site_url(); ?>/wp-content/uploads/2026/06/logo-white.png" alt="">
						</div>
						<div class="absolute right-0 sm:top-2 top-0 max-sm:w-[17px] cursor-pointer quote-pop-closer">
							<svg width="21" height="22" viewBox="0 0 21 22" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd"
									d="M10.5099 13.3392L17.5806 20.4099C17.9557 20.7851 18.4646 20.9959 18.9952 20.9959C19.5258 20.9959 20.0347 20.7851 20.4099 20.4099C20.7851 20.0347 20.9959 19.5258 20.9959 18.9952C20.9959 18.4646 20.7851 17.9558 20.4099 17.5806L13.3366 10.5099L20.4086 3.43925C20.5942 3.25347 20.7415 3.03294 20.842 2.79024C20.9424 2.54755 20.9941 2.28744 20.9941 2.02477C20.994 1.76211 20.9422 1.50203 20.8416 1.25938C20.741 1.01673 20.5937 0.796268 20.4079 0.610579C20.2221 0.42489 20.0016 0.277611 19.7589 0.17715C19.5162 0.0766893 19.2561 0.0250146 18.9934 0.0250765C18.7308 0.0251384 18.4707 0.0769354 18.228 0.177511C17.9854 0.278086 17.7649 0.425469 17.5792 0.611246L10.5099 7.68191L3.43922 0.611246C3.25482 0.420141 3.0342 0.267674 2.79024 0.162743C2.54628 0.0578111 2.28387 0.00251585 2.01832 8.38717e-05C1.75276 -0.00234811 1.48938 0.0481315 1.24354 0.148577C0.997699 0.249023 0.774326 0.397424 0.586453 0.58512C0.39858 0.772816 0.249969 0.996049 0.149291 1.24179C0.0486138 1.48754 -0.00211408 1.75087 6.7489e-05 2.01643C0.00224906 2.28199 0.0572962 2.54445 0.161998 2.78851C0.266699 3.03257 0.418957 3.25333 0.609889 3.43791L7.68322 10.5099L0.611223 17.5819C0.420291 17.7665 0.268033 17.9873 0.163331 18.2313C0.0586301 18.4754 0.0035823 18.7378 0.00140073 19.0034C-0.000780844 19.269 0.0499471 19.5323 0.150625 19.778C0.251302 20.0238 0.399913 20.247 0.587786 20.4347C0.77566 20.6224 0.999032 20.7708 1.24487 20.8712C1.49071 20.9717 1.75409 21.0222 2.01965 21.0197C2.2852 21.0173 2.54762 20.962 2.79158 20.8571C3.03553 20.7522 3.25615 20.5997 3.44056 20.4086L10.5099 13.3392Z"
									fill="white" />
							</svg>
						</div>
					</div>
					<!-- Bottom -->
					<div class="bottom sm:pt-8 pt-6 ">
						<h2 class="text-h4 text-center text-white">Request a Quote</h2>
					</div>
					<!--  -->
				</div><!-- End of Header -->
				<!-- Body -->
				<div class="gqf-form__body sm:p-8 p-6 bg-white">
					<div class="th-form-style">
						<iframe src="<?php echo get_template_directory_uri(); ?>/calculator/index.html"
							style="width:100%;height:1200px;border:0;" loading="lazy"></iframe>
						<?php //echo do_shortcode('[contact-form-7 id="ffcf842" title="Quote Form"]'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Global Search Form -->
<div class="global-search-pop fixed inset-0 bg-lt-onyx z-99 pt-10 pb-[9rem] invisible opacity-0">
	<div class="container h-full w-full">
		<div class="global-search-pop__closer-holder w-[18px] h-[18px] ml-auto cursor-pointer search-pop-closer">
			<svg class="w-[18px]" width="14" height="14" viewBox="0 0 14 14" fill="none"
				xmlns="http://www.w3.org/2000/svg">
				<path
					d="M12.2197 0.21967C12.5126 -0.0732233 12.9873 -0.0732233 13.2802 0.21967C13.5731 0.512563 13.5731 0.987324 13.2802 1.28022L7.81049 6.74994L13.2802 12.2197C13.5731 12.5126 13.5731 12.9873 13.2802 13.2802C12.9873 13.5731 12.5126 13.5731 12.2197 13.2802L6.74994 7.81049L1.28022 13.2802C0.987324 13.5731 0.512563 13.5731 0.21967 13.2802C-0.0732233 12.9873 -0.0732233 12.5126 0.21967 12.2197L5.6894 6.74994L0.21967 1.28022C-0.0732233 0.987324 -0.0732233 0.512563 0.21967 0.21967C0.512563 -0.0732233 0.987324 -0.0732233 1.28022 0.21967L6.74994 5.6894L12.2197 0.21967Z"
					fill="#ffffff" />
			</svg>
		</div>
		<div class="global-search-pop__holder h-full w-full flex items-center justify-center">
			<?php
			// Check if the native search form function exists before running it
			if (function_exists('get_search_form')) {
				get_search_form();
			}
			?>
		</div>
	</div>
</div>
<!-- Site Overlay -->
<div class="site-overlay fixed inset-0 bg-lt-onyx opacity-50 z-[98] hidden"></div>
<footer id="colophon" class="site-footer pt-16 md:pb-[4.5rem] sm:pb-10 pb-4 bg-[#1C1C1C] text-lt-snow-drift">
	<div class="container">
		<!-- Footer Top -->
		<div class="footer-top flex flex-wrap justify-between wd:gap-8 gap-[3.43rem] sm:pb-8 pb-[2.875rem]">
			<!-- Left -->
			<div class="left-cols wd:w-[32%] lg:w-[20%] w-full">
				<?php
				$size = 'full';
				if ($footer_logo):
					?>
					<div class="footer-logo-max-sm:pr-4">
						<a class="inline-block" href="<?php echo home_url(); ?>">
							<?php
							$url = wp_get_attachment_url($footer_logo);
							echo wp_get_attachment_image($footer_logo, $size);
							?>
						</a>
					</div>
					<?php
				endif; ?>
				<div class="social-links sm:pt-[3.5rem] pt-[2.5rem]">
					<ul class="flex flex-wrap gap-4">
						<?php if (have_rows('social_links', 'options')): ?>
							<?php while (have_rows('social_links', 'options')):
								the_row();
								$social_icon = get_sub_field('social_icon', 'options');
								$size = 'full';
								$link_url = get_sub_field('link_url', 'options');
								?>
								<li>
									<?php if ($link_url): ?>
										<a class="w-[32px] h-[32px] rounded-full bg-lt-text-secondary flex justify-center items-center  hover:translate-y-[-2px] p-1.5"
											target="_blank" href="<?php echo esc_html($link_url); ?>">
											<?php
											if ($social_icon) {
												$url = wp_get_attachment_url($social_icon);
												echo wp_get_attachment_image($social_icon, $size);
											}
											; ?>
										</a>
									<?php endif; ?>
								</li>
							<?php endwhile; ?>
						<?php endif; ?>
					</ul>
				</div>
			</div>
			<!-- Right Col -->
			<div
				class="right-cols flex flex-wrap sm:gap-8 gap-11 justify-between wd:w-[62.8%] lg:w-[70%] w-full text-caption-md font-medium font-primary">
				<!-- Cols -->
				<div class="cols md:w-[21%] max-sm:w-[46%] max-smlr:w-full">
					<?php if ($nav_heading_one): ?>
						<h2
							class="title text-[15px] sm:mb-[1.3125rem] mb-[1.0125rem] font-semi-ext font-bold leading-[1.46]">
							<?php echo esc_html($nav_heading_one); ?>
						</h2>
					<?php endif; ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer-menu-1',
							'menu_id' => 'footer-menu-one',
						)
					);
					?>
				</div>
				<!-- Cols -->
				<div class="cols md:w-[21%] max-sm:w-[46%] max-smlr:w-full">
					<?php if ($nav_heading_two): ?>
						<h2
							class="title text-[15px] sm:mb-[1.3125rem] mb-[1.0125rem] font-semi-ext font-bold leading-[1.46]">
							<?php echo esc_html($nav_heading_two); ?>
						</h2>
					<?php endif; ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer-menu-2',
							'menu_id' => 'footer-menu-two',
						)
					);
					?>
				</div>
				<!-- Cols -->
				<div class="cols md:w-[21%] max-sm:w-[46%] max-smlr:w-full">
					<?php if ($nav_heading_three): ?>
						<h2
							class="title text-[15px] sm:mb-[1.3125rem] mb-[1.0125rem] font-semi-ext font-bold leading-[1.46]">
							<?php echo esc_html($nav_heading_three); ?>
						</h2>
					<?php endif; ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer-menu-3',
							'menu_id' => 'footer-menu-three',
						)
					);
					?>
				</div>
				<!-- Cols -->
				<div class="cols md:w-[21%] max-sm:w-[46%] max-smlr:w-full">
					<?php if ($nav_heading_four): ?>
						<h2
							class="title text-[15px] sm:mb-[1.3125rem] mb-[1.0125rem] font-semi-ext font-bold leading-[1.46]">
							<?php echo esc_html($nav_heading_four); ?>
						</h2>
					<?php endif; ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer-menu-4',
							'menu_id' => 'footer-menu-four',
						)
					);
					?>
				</div>
			</div>
		</div>
		<!-- Footer Bottom -->
		<div
			class="footer-bottom max-md:text-center border-t border-[#5858581A] flex max-md:flex-col max-md:gap-4 justify-between items-center text-caption-sm tracking-[0.48px] pt-[2.40rem] gap-14">
			<?php if ($copyright_text): ?>
				<div class="c-text">
					<?php echo $copyright_text; ?>
				</div>
			<?php endif; ?>
			<div class="footer-b-nav">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer-bottom',
						'menu_id' => 'footer-menu-bottom',
					)
				);
				?>
			</div>
		</div>
	</div>
</footer><!-- #colophon -->
</div><!-- #page -->
<?php wp_footer(); ?>
</body>

</html>