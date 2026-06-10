export function isTablet() {
	return window.matchMedia( '(max-width: 1024px)' ).matches;
}

export function isMobile() {
	return window.matchMedia( '(max-width: 767px)' ).matches;
}
