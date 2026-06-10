// slide-toggle.js
export function slideToggle( element, duration = 400 ) {
	const isHidden = window.getComputedStyle( element ).display === 'none';

	// Temporarily set display to block (or the correct value) to calculate height
	element.style.display = 'block'; // Use 'block' or the appropriate display value
	const targetHeight = element.scrollHeight; // Get the full height of the element

	if ( isHidden ) {
		// If the element was hidden, we slide it down
		element.style.height = '0px'; // Start collapsed at 0 height
		element.style.overflow = 'hidden'; // Prevent overflow when collapsing
		element.style.transition = `height ${ duration }ms ease-in-out`; // Smooth transition

		// Start expanding the element
		setTimeout( () => {
			element.style.height = `${ targetHeight }px`; // Expand it to full height
		}, 10 ); // Small delay to ensure transition happens

		// Start expanding the element
		setTimeout( () => {
			element.style.height = `auto`; // Expand it to full height
		}, duration ); // Small delay to ensure transition happens
	} else {
		// If the element is visible, we slide it up (collapse it)
		element.style.transition = `height ${ duration }ms ease-in-out`; // Smooth transition
		element.style.height = `${ targetHeight }px`; // Expand it to full height

		// Start collapsing the element
		setTimeout( () => {
			element.style.height = '0px'; // Collapse to 0 height
		}, 10 );

		// After animation ends, hide it with display: none
		setTimeout( () => {
			element.style.display = 'none'; // Set display to none after the collapse
		}, duration ); // Ensure display changes after the animation completes
	}
}

export function slideUp( element, duration = 400 ) {
	const isHidden = window.getComputedStyle( element ).display === 'none';

	if ( isHidden ) return;

	// Temporarily set display to block (or the correct value) to calculate height
	element.style.display = 'block'; // Use 'block' or the appropriate display value
	const targetHeight = element.scrollHeight; // Get the full height of the element

	// If the element is visible, we slide it up (collapse it)
	element.style.transition = `height ${ duration }ms ease-in-out`; // Smooth transition

	element.style.height = `${ targetHeight }px`; // Expand it to full height

	// Start collapsing the element
	setTimeout( () => {
		element.style.height = '0px'; // Collapse to 0 height
	}, 10 );

	// After animation ends, hide it with display: none
	setTimeout( () => {
		element.style.display = 'none'; // Set display to none after the collapse
	}, duration ); // Ensure display changes after the animation completes
}

export function slideDown( element, duration = 400 ) {
	const isHidden = window.getComputedStyle( element ).display === 'none';

	if ( ! isHidden ) return;

	// Temporarily set display to block (or the correct value) to calculate height
	element.style.display = 'block'; // Use 'block' or the appropriate display value
	const targetHeight = element.scrollHeight; // Get the full height of the element

	// If the element was hidden, we slide it down
	element.style.height = '0px'; // Start collapsed at 0 height
	element.style.overflow = 'hidden'; // Prevent overflow when collapsing
	element.style.transition = `height ${ duration }ms ease-in-out`; // Smooth transition

	// Start expanding the element
	setTimeout( () => {
		element.style.height = `${ targetHeight }px`; // Expand it to full height
	}, 10 ); // Small delay to ensure transition happens
	// Start expanding the element
	setTimeout( () => {
		element.style.height = `auto`; // Expand it to full height
	}, duration ); // Small delay to ensure transition happens
}
