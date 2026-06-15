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
										<a class="w-[32px] h-[32px] rounded-full bg-lt-text-secondary flex justify-center items-center  hover:translate-y-[-2px] p-1"
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