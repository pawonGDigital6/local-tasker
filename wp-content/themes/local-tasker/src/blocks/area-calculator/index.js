import './editor.scss';
import './style.scss';
import { initAreaCalculators } from '../../global/js/components/area-calculator';

/**
 * Editor preview.
 *
 * ACF renders the block preview over AJAX and the front-end view script is not
 * loaded in the editor, so the card would otherwise render with no room rows.
 * Booting the shared component on every DOM change keeps the preview live;
 * `initAreaCalculators()` skips cards it has already booted.
 */
if (typeof window !== 'undefined' && typeof MutationObserver !== 'undefined') {
	let scheduled = false;

	const boot = () => {
		scheduled = false;
		initAreaCalculators();
	};

	const schedule = () => {
		if (scheduled) return;
		scheduled = true;
		window.requestAnimationFrame(boot);
	};

	window.addEventListener('DOMContentLoaded', schedule);
	new MutationObserver(schedule).observe(document.documentElement, {
		childList: true,
		subtree: true,
	});
}
