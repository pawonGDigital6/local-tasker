function siteHeader() {
	// Select the header element
	const header = document.querySelector('.site-header');

	// Nothing to sticky-toggle without a header; bail before binding listeners
	// so the scroll handler can never throw on pages that omit it.
	if (!header) {
		return;
	}

	function checkScroll() {
		// Check if the page is scrolled down more than 200 pixels
		if (window.scrollY >= 200) {
			header.classList.add('is-sticky');
		} else {
			header.classList.remove('is-sticky');
		}
	}

	// Run on scroll
	window.addEventListener('scroll', checkScroll);
	window.addEventListener('load', checkScroll);

	// Run on page load (handles middle-of-screen refreshes)
	document.addEventListener('DOMContentLoaded', checkScroll);

}

export default siteHeader;