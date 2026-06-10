import { createSignal, createEffect } from './signal';
import { isTablet } from './check-device';
import { slideToggle, slideUp } from './slide-toggle';

/**
 * Navigation module to handle menu functionality, including toggling and submenu alignment.
 */
export function Navigation() {
	// Selectors
	const siteNavigationSelector = '.main-navigation';
	const menuTogglerSelector = '.menu-toggle';
	const menuItemsWithSubmenusSelector = '.menu-item-has-children';
	const submenuSelector = '.sub-menu';

	const siteNavigation = document.querySelector( siteNavigationSelector );

	// If the site navigation doesn't exist, return early
	if ( ! siteNavigation ) {
		return;
	}

	const menuToggler = siteNavigation.querySelector( menuTogglerSelector );
	const menuItemsWithSubmenus = siteNavigation.querySelectorAll(
		menuItemsWithSubmenusSelector
	);

	// Menu Open State
	const [ isMenuOpen, setIsMenuOpen ] = createSignal( false );

	// Effects for menu toggle
	createEffect( () => {
		document.body.classList.toggle( 'menu-open', isMenuOpen() );

		if ( menuToggler ) {
			menuToggler.setAttribute( 'aria-expanded', isMenuOpen() );
		}

		resetAllSubmenus();
	} );

	/**
	 * Initialize the navigation by appending arrows, aligning submenus, and adding event listeners.
	 */
	function init() {
		appendSubmenuArrow();
		submenuAlignment();
		eventListeners();
	}

	/**
	 * Add event listeners for toggling the menu and submenus.
	 */
	function eventListeners() {
		if ( menuToggler ) {
			menuToggler.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				setIsMenuOpen( ! isMenuOpen() );
			} );
		}

		if ( menuItemsWithSubmenus.length ) {
			menuItemsWithSubmenus.forEach( ( menuItem ) => {
				menuItem.addEventListener( 'click', ( e ) => {
					toggleSubmenu( e, menuItem );
				} );
			} );
		}

		window.addEventListener( 'resize', () => {
			if ( ! isTablet() ) {
				setIsMenuOpen( false );

				resetAllSubmenus();
			}
			submenuAlignment();
		} );
	}

	/**
	 * Align submenus to prevent them from overflowing the viewport.
	 */
	function submenuAlignment() {
		// Get all top-level menu items with submenus
		const topLevelMenuItemsWithSubmenus = siteNavigation.querySelectorAll(
			'.menu > .menu-item-has-children'
		);

		// If there are no top-level menu items with submenus, return early
		if ( ! topLevelMenuItemsWithSubmenus.length ) {
			return;
		}

		const windowWidth = window.innerWidth;

		topLevelMenuItemsWithSubmenus.forEach( ( menuItem ) => {
			if ( ! isTablet() ) {
				const submenus = menuItem.querySelectorAll( submenuSelector );

				menuItem.classList.remove( 'align-right' );

				if ( ! submenus.length ) {
					return;
				}

				for ( const submenu of submenus ) {
					const { right: submenuRightEdge } =
						submenu.getBoundingClientRect();

					menuItem.classList.toggle(
						'align-right',
						windowWidth - 20 < submenuRightEdge
					);

					if ( windowWidth - 20 < submenuRightEdge ) {
						break;
					}
				}
			} else {
				menuItem.classList.remove( 'align-right' );
			}
		} );
	}

	/**
	 * Append arrows to menu items with submenus.
	 */
	function appendSubmenuArrow() {
		if ( ! menuItemsWithSubmenus.length ) {
			return;
		}

		menuItemsWithSubmenus.forEach( ( menuItem ) => {
			const submenu = menuItem.querySelector( submenuSelector );

			if ( ! submenu ) {
				return;
			}

			const arrow = document.createElement( 'span' );
			arrow.classList.add( 'submenu-arrow' );
			menuItem.insertBefore( arrow, submenu );
		} );
	}

	/**
	 * Toggle the submenu for a specific menu item.
	 *
	 * @param {Event} event - The click event.
	 * @param {HTMLElement} menuItem - The menu item to toggle.
	 */
	function toggleSubmenu( event, menuItem ) {
		event.preventDefault();

		const submenu = menuItem.querySelector( submenuSelector );

		if ( ! submenu ) {
			return;
		}

		if ( submenu.contains( event.target ) || event.target == submenu ) {
			return;
		}

		if (
			event.target.matches( 'a' ) &&
			menuItem.classList.contains( 'submenu-open' )
		) {
			const href = event.target.getAttribute( 'href' );

			if ( href && href !== '#' ) {
				window.location.href = href;
			}
		}

		updateSubmenuState( menuItem );
	}

	/**
	 * Update the state of the submenu for the given menu item.
	 *
	 * @param {HTMLElement} menuItem - The menu item to update.
	 */
	function updateSubmenuState( menuItem ) {
		const siblings = menuItem.parentNode.children;

		// Filter siblings to get those with the class 'menu-item-has-children'
		const siblingsWithChildren = Array.from( siblings ).filter(
			( sibling ) =>
				sibling.classList.contains( 'menu-item-has-children' )
		);

		// Get all children of the menu item with the class 'menu-item-has-children'
		const menuChildren = Array.from(
			menuItem.querySelectorAll( menuItemsWithSubmenusSelector )
		);

		// If there are siblings with children, get their children
		const siblingsChildren = [];
		if ( siblingsWithChildren.length ) {
			siblingsWithChildren.forEach( ( sibling ) => {
				if ( sibling === menuItem ) {
					return;
				}
				const children = sibling.querySelectorAll(
					menuItemsWithSubmenusSelector
				);
				siblingsChildren.push( ...children );
			} );
		}

		// Combine siblings with children, all siblings, and menu children into one array
		const menuItems = [
			...siblingsWithChildren,
			...menuChildren,
			...siblingsChildren,
		];

		menuItems.forEach( ( item ) => {
			const submenu = item.querySelector( submenuSelector );
			if ( ! submenu ) return;

			if ( item === menuItem ) {
				menuItem.classList.toggle( 'submenu-open' );
				slideToggle( submenu );
			} else {
				item.classList.remove( 'submenu-open' );
				slideUp( submenu );
			}
		} );
	}

	/**
	 * Reset all submenus.
	 */
	function resetAllSubmenus() {
		if ( ! menuItemsWithSubmenus.length ) return;
		menuItemsWithSubmenus.forEach( ( item ) => {
			item.classList.remove( 'submenu-open' );
			const submenu = item.querySelector( submenuSelector );
			if ( ! submenu ) return;
			submenu.removeAttribute( 'style' );
		} );
	}

	init();
}
