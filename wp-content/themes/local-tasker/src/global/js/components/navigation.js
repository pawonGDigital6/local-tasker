import $ from 'jquery';

const navigation = {
	// Initialize the navigation
	init() {
		this.cacheDOM();
		this.bindEvents();
	},

	// Cache the DOM elements
	cacheDOM() {
		this.body = document.body;
		this.primaryMenu = document.querySelector('.main-navigation');

		// Fallbacks to avoid null errors
		this.hamnBurger = document.querySelector('.menu-hamn-burger');
		this.menuToggle = this.hamnBurger;
		this.menuCloser = document.querySelector('.menu-close-btn');
		this.siteOverlay = document.querySelector('.site-overlay');

		if (this.primaryMenu) {
			this.navItems = this.primaryMenu.querySelectorAll(
				'.menu-item-has-children'
			);
		} else {
			this.navItems = [];
		}
	},

	// Bind events
	bindEvents() {
		if (this.menuToggle) {
			this.menuToggle.addEventListener('click', this.openMenu.bind(this));
		}
		if (this.menuCloser) {
			this.menuCloser.addEventListener('click', this.closeMenu.bind(this));
		}
		if (this.siteOverlay) {
			this.siteOverlay.addEventListener('click', this.closeMenu.bind(this));
		}

		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && this.body.classList.contains('menu-open')) {
				this.closeMenu();
			}
		});

		this.addIconHasChildren();
		this.subMenuSlideToggle();
	},

	// Toggle menu
	openMenu() {
		this.body.classList.add('menu-open');
		if (this.menuToggle) {
			this.menuToggle.setAttribute('aria-expanded', 'true');
		}
	},

	closeMenu() {
		this.body.classList.remove('menu-open');
		if (this.menuToggle) {
			this.menuToggle.setAttribute('aria-expanded', 'false');
			// Send focus back to the control that opened the menu.
			this.menuToggle.focus();
		}
	},

	// Add icon to menu items with children
	addIconHasChildren() {
		this.navItems.forEach((item) => {
			// Prevent adding multiple icons if function runs twice
			if (item.querySelector('.icon')) return;

			const icon = document.createElement('button');
			icon.type = 'button';
			icon.classList.add('icon');
			icon.setAttribute('aria-expanded', 'false');

			const itemLink = item.querySelector(':scope > a');
			const itemLabel = itemLink ? itemLink.textContent.trim() : '';
			icon.setAttribute(
				'aria-label',
				itemLabel ? `Show ${itemLabel} sub-menu` : 'Show sub-menu'
			);

			icon.innerHTML = '<span></span><span></span>';
			item.appendChild(icon);
		});
	},

	subMenuSlideToggle() {
		this.navItems.forEach((item) => {
			const menuHasChildIcon = item.querySelector('.icon');

			if (menuHasChildIcon) {
				menuHasChildIcon.addEventListener('click', function (e) {
					e.preventDefault(); // Prevents accidental page jumps

					const $parentItem = $(this).parent('.menu-item-has-children');
					const $subMenu = $parentItem.find('.sub-menu');

					// Toggle current submenu while closing others
					$subMenu.slideToggle();
					$('.main-navigation .sub-menu').not($subMenu).slideUp();

					// Toggle active class on icon while resetting others
					$(this).toggleClass('active');
					$('.main-navigation .icon').not($(this)).removeClass('active');

					// Keep the announced state in step with the visual one
					this.setAttribute(
						'aria-expanded',
						$(this).hasClass('active') ? 'true' : 'false'
					);
					$('.main-navigation .icon')
						.not($(this))
						.attr('aria-expanded', 'false');
				});
			}
		});
	},
};

export default navigation;