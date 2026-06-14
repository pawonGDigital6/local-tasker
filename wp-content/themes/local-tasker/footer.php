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

?>
	<div class="site-overlay fixed inset-0 bg-lt-onyx opacity-50 z-[98] hidden"></div>
	<footer id="colophon" class="site-footer pt-16 md:pb-[4.5rem] sm:pb-10 pb-4 bg-[#1C1C1C] text-lt-snow-drift">
		<div class="container">
			<!-- Footer Top -->
			<div class="footer-top flex flex-wrap justify-between wd:gap-8 gap-[3.43rem] sm:pb-8 pb-[2.875rem]">
				<!-- Left -->
				<div class="left-cols wd:w-[32%] lg:w-[20%] w-full">
					<img src="http://localhost/local-tasker/wp-content/uploads/2026/06/logo-white.png" alt="">
					<div class="social-links sm:pt-[3.5rem] pt-[2.5rem]">
						<ul class="flex flex-wrap gap-4">
							<li>
								<a class="w-[32px] h-[32px] rounded-full bg-lt-text-secondary! flex justify-center items-center  hover:translate-y-[-2px]" target="_blank" href="https://www.facebook.com/localtasker.aus">
									<img src="http://localhost/local-tasker/wp-content/uploads/2026/06/facebook.svg" alt="">
								</a>
							</li>
							<li>
								<a class="w-[32px] h-[32px] rounded-full bg-lt-text-secondary! flex justify-center items-center  hover:translate-y-[-2px]" target="_blank" href="#">
									<img src="http://localhost/local-tasker/wp-content/uploads/2026/06/twiter.svg" alt="">
								</a>
							</li>
							<li>
								<a class="w-[32px] h-[32px] rounded-full bg-lt-text-secondary! flex justify-center items-center  hover:translate-y-[-2px]" target="_blank" href="#">
									<img src="http://localhost/local-tasker/wp-content/uploads/2026/06/linkedIn.svg" alt="">
								</a>
							</li>
						</ul>
					</div>
				</div>
				<!-- Right Col -->
				<div class="right-cols flex flex-wrap sm:gap-8 gap-11 justify-between wd:w-[62.8%] lg:w-[70%] w-full text-caption-md font-medium font-primary">
					<!-- Cols -->
					<div class="cols max-md:w-[21%] max-sm:w-[46%] max-smlr:w-full">
						<h2 class="title text-[15px] mb-[1.3125rem] font-semi-ext font-bold leading-[1.46]">Navigation</h2>
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
					<div class="cols max-md:w-[21%] max-sm:w-[46%] max-smlr:w-full">
						<h2 class="title text-[15px] mb-[1.3125rem] font-semi-ext font-bold leading-[1.46]">Our Products</h2>
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
					<div class="cols max-md:w-[21%] max-sm:w-[46%] max-smlr:w-full">
						<h2 class="title text-[15px] mb-[1.3125rem] font-semi-ext font-bold leading-[1.46]">Resources</h2>
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
					<div class="cols max-md:w-[21%] max-sm:w-[46%] max-smlr:w-full">
						<h2 class="title text-[15px] mb-[1.3125rem] font-semi-ext font-bold leading-[1.46]">Popular Categories</h2>
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
			 <div class="footer-bottom max-md:text-center border-t border-[#5858581A] flex max-md:flex-col max-md:gap-4 justify-between items-center text-caption-sm tracking-[0.48px] pt-[2.40rem] gap-14">
				<div>© Copyright 2026 – All Rights Reserved. Hosted and Managed by <a target="_blank" href="https://digitalsix.com.au/">Digital Six</a></div>
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
