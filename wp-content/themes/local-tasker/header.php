<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package local-tasker
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text"
			href="#primary"><?php esc_html_e('Skip to content', 'local-tasker'); ?></a>
		<!-- Top bar -->
		<div class="top-bar bg-lt-onyx py-2">
			<div class="container">
				<span
					class="uppercase text-lt-white text-center block md:text-caption-sm text-caption-xs tracking-[1px] leading-[1.30]">Free
					Shipping |
					Seamless returns
					| CALL ON
				</span>
			</div>
		</div>
		<!-- Site Header -->
		<header id="masthead" class="site-header py-[20px]">
			<!-- Header Top -->
			<div class="site-header__top">
				<div class="container flex flex-col nowrap">
					<!-- Header Top -->
					<div class="site-header__top-inner flex justify-between items-center">
						<!-- Left -->
						<div class="site-header__top-left md:inline-flex hidden">
							<a href="tel:02046340122"
								class="inline-flex items-center gap-2 lt-text-secondary no-underline text-caption-md leading-[1.71] hover:text-lt-brand transition-colors">
								<span class="icon">
									<svg width="15" height="14" viewBox="0 0 15 14" fill="none"
										xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd"
											d="M1.86227 0.594972C2.77502 -0.312528 4.27802 -0.151277 5.04227 0.870223L5.98877 2.13322C6.61127 2.96422 6.55577 4.12522 5.81702 4.85947L5.63852 5.03797C5.61828 5.1129 5.61622 5.19159 5.63252 5.26747C5.67977 5.57347 5.93552 6.22147 7.00652 7.28647C8.07752 8.35147 8.73002 8.60647 9.04052 8.65447C9.11876 8.67021 9.19956 8.6679 9.27677 8.64772L9.58277 8.34322C10.2398 7.69072 11.2478 7.56847 12.0608 8.01022L13.4933 8.79022C14.721 9.45622 15.0308 11.1242 14.0258 12.124L12.96 13.183C12.624 13.5167 12.1725 13.795 11.622 13.8467C10.2645 13.9735 7.10177 13.8115 3.77702 10.5062C0.674271 7.42072 0.0787703 4.72972 0.00302025 3.40372C-0.0344797 2.73322 0.28202 2.16622 0.68552 1.76572L1.86227 0.594972ZM4.14227 1.54447C3.76202 1.03672 3.05402 0.996223 2.65502 1.39297L1.47752 2.56297C1.23002 2.80897 1.11152 3.08047 1.12652 3.33997C1.18652 4.39372 1.66652 6.82147 4.57052 9.70897C7.61702 12.7375 10.4303 12.8282 11.5178 12.7262C11.7398 12.706 11.9603 12.5905 12.1665 12.3857L13.2315 11.326C13.665 10.8955 13.5698 10.111 12.9563 9.77797L11.5238 8.99872C11.1278 8.78422 10.6643 8.85472 10.3763 9.14122L10.035 9.48097L9.63752 9.08197C10.035 9.48097 10.0343 9.48172 10.0335 9.48172L10.0328 9.48322L10.0305 9.48547L10.0253 9.48997L10.014 9.50047C9.98237 9.52986 9.94825 9.55646 9.91202 9.57997C9.85202 9.61972 9.77252 9.66397 9.67277 9.70072C9.47027 9.77647 9.20177 9.81697 8.87027 9.76597C8.22002 9.66622 7.35827 9.22297 6.21302 8.08447C5.06852 6.94597 4.62152 6.08947 4.52102 5.43997C4.46927 5.10847 4.51052 4.83997 4.58702 4.63747C4.62912 4.52352 4.6894 4.41715 4.76552 4.32247L4.78952 4.29622L4.80002 4.28497L4.80452 4.28047L4.80677 4.27822L4.80827 4.27672L5.02427 4.06222C5.34527 3.74197 5.39027 3.21172 5.08802 2.80747L4.14227 1.54447Z"
											fill="#2E2E2E" />
									</svg>
								</span>
								<span class="text"> 020 4634 0122</span>
							</a>
						</div>
						<!-- Center -->
						<div class="site-header__top-center flex items-center gap-[12px]">
							<!-- HamnBurger -->
							<div
								class="menu-hamn-burger flex flex-col justify-between w-[24px] h-[20px] max-smlr:w-[18px] max-smlr:h-[13px] md:hidden cursor-pointer shrink-0">
								<span class="h-[2px] w-full bg-lt-text-secondary rounded-[2px] block"></span>
								<span class="h-[2px] w-full bg-lt-text-secondary rounded-[2px] block"></span>
								<span class="h-[2px] w-full bg-lt-text-secondary rounded-[2px] block"></span>
							</div><!-- End of HamnBurger -->
							<!-- Site Logo -->
							<div class="site-logo max-md:w-[180px] max-xs:w-[140px]">
								<?php if (has_custom_logo()):
									the_custom_logo();
								endif;
								?>
							</div>
							<!-- End of Site Logo -->
						</div>
						<!-- Right -->
						<div class="site-header__top-right flex items-center flex justify-end gap-[20px] max-sm:gap-[12px]">
							<!-- User  -->
							<a href="#" class="icon-link">
								<svg class="max-md:w-[16px]" width="20" height="18" viewBox="0 0 20 18" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_774_479)">
										<path
											d="M14.125 5.75C13.941 8.228 12.062 10.25 9.99998 10.25C7.93798 10.25 6.05598 8.229 5.87498 5.75C5.68798 3.172 7.51498 1.25 9.99998 1.25C12.484 1.25 14.313 3.219 14.125 5.75Z"
											stroke="#2E2E2E" stroke-linecap="round" stroke-linejoin="round" />
										<path
											d="M1.01709 17.747C1.78309 13.5 5.92209 11.25 10.0001 11.25C14.0781 11.25 18.2171 13.5 18.9841 17.747"
											stroke="#2E2E2E" stroke-miterlimit="10" />
									</g>
									<defs>
										<clipPath id="clip0_774_479">
											<rect width="20" height="18" fill="white" />
										</clipPath>
									</defs>
								</svg>
							</a>
							<!-- search  -->
							<button class="search-button search-pop-opener appearance-button cursor-pointer">
								<svg class="max-md:w-[16px]" width="20" height="20" viewBox="0 0 20 20" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_774_483)">
										<path
											d="M8.36388 1.0005C7.38967 0.989147 6.42289 1.17122 5.51955 1.53618C4.61621 1.90114 3.79428 2.44172 3.10136 3.12661C2.40844 3.81151 1.85831 4.62709 1.48285 5.52611C1.10739 6.42513 0.914062 7.38972 0.914062 8.364C0.914062 9.33828 1.10739 10.3029 1.48285 11.2019C1.85831 12.1009 2.40844 12.9165 3.10136 13.6014C3.79428 14.2863 4.61621 14.8269 5.51955 15.1918C6.42289 15.5568 7.38967 15.7389 8.36388 15.7275C10.3019 15.7049 12.1529 14.9192 13.5154 13.5407C14.8779 12.1622 15.6421 10.3022 15.6421 8.364C15.6421 6.42581 14.8779 4.56576 13.5154 3.18729C12.1529 1.80882 10.3019 1.02309 8.36388 1.0005Z"
											stroke="#2E2E2E" stroke-miterlimit="10" />
										<path d="M13.8569 13.8594L18.9999 19.0024" stroke="#2E2E2E"
											stroke-miterlimit="10" stroke-linecap="round" />
									</g>
									<defs>
										<clipPath id="clip0_774_483">
											<rect width="20" height="20" fill="white" />
										</clipPath>
									</defs>
								</svg>
							</button>
							<!-- Cart  -->
							<a href="#" class="cart-icon relative pr-3">
								<span
									class="absolute top-[-6px] right-[0] max-sm:top-[-9px] flex items-center justify-center bg-lt-brand text-lt-white rounded-full w-[20px] h-[20px] text-caption-sm">2</span>
								<svg class="max-md:w-[16px]" width="19" height="21" viewBox="0 0 19 21" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<path
										d="M5 6.5V5C5 3.80653 5.47411 2.66193 6.31802 1.81802C7.16193 0.974106 8.30653 0.5 9.5 0.5C10.6935 0.5 11.8381 0.974106 12.682 1.81802C13.5259 2.66193 14 3.80653 14 5V6.5M2.25 6.5C2.05109 6.5 1.86032 6.57902 1.71967 6.71967C1.57902 6.86032 1.5 7.05109 1.5 7.25L0.5 17.375C0.5 18.793 1.707 20 3.125 20H15.875C17.293 20 18.5 18.851 18.5 17.434L17.5 7.25C17.5 7.05109 17.421 6.86032 17.2803 6.71967C17.1397 6.57902 16.9489 6.5 16.75 6.5H2.25Z"
										stroke="#2E2E2E" stroke-linecap="round" stroke-linejoin="round" />
								</svg>
							</a>
						</div>
					</div>
				</div>
			</div>
			<!-- Header Bottom -->
			<div class="site-header__bottom">
				<div class="container">
					<nav id="site-navigation" class="main-navigation text-caption-md  font-primary font-bold">
						<div class="menu-close-btn-holder text-right cursor-pointer lg:hidden mb-10">
							<div class="menu-close-btn inline-block">
								<svg class="w-[18px]" width="14" height="14" viewBox="0 0 14 14" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<path
										d="M12.2197 0.21967C12.5126 -0.0732233 12.9873 -0.0732233 13.2802 0.21967C13.5731 0.512563 13.5731 0.987324 13.2802 1.28022L7.81049 6.74994L13.2802 12.2197C13.5731 12.5126 13.5731 12.9873 13.2802 13.2802C12.9873 13.5731 12.5126 13.5731 12.2197 13.2802L6.74994 7.81049L1.28022 13.2802C0.987324 13.5731 0.512563 13.5731 0.21967 13.2802C-0.0732233 12.9873 -0.0732233 12.5126 0.21967 12.2197L5.6894 6.74994L0.21967 1.28022C-0.0732233 0.987324 -0.0732233 0.512563 0.21967 0.21967C0.512563 -0.0732233 0.987324 -0.0732233 1.28022 0.21967L6.74994 5.6894L12.2197 0.21967Z"
										fill="#2E2E2E" />
								</svg>
							</div>
						</div>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								'menu_id' => 'primary-menu',
							)
						);
						?>
					</nav><!-- #site-navigation -->
				</div>
			</div>
		</header><!-- #masthead -->