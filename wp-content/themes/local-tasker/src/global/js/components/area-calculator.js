/**
 * Area Calculator — shared component (vanilla JS, no dependencies).
 *
 * Drives every `[data-lt-area-calc]` card rendered by
 * `template-parts/components/area-calculator.php`:
 *
 *   - clones the row <template> to add "Add another area" rows,
 *   - recalculates length × width per row plus the grand total,
 *   - keeps the "with wastage" figure in sync (percentage from `data-wastage`),
 *   - always keeps at least one row on screen,
 *   - mirrors both totals into any companion box that points back at the card
 *     (`data-lt-area-mirror="<card id>"`, e.g. the "How it works" summary),
 *   - optionally pushes the total into a host input (`data-target` on the CTA)
 *     and scrolls to another element (`data-scroll-to`).
 *
 * Everything is scoped to the card's root element and instances are marked as
 * initialised, so any number of calculators can share one page — the standalone
 * ACF block and the WooCommerce single-product card use the exact same code.
 *
 * @package local-tasker
 * @since   1.0.0
 */

const ROOT_SELECTOR = '[data-lt-area-calc]';
const DEFAULT_WASTAGE = 10; // Percent.

class AreaCalculator {
	/**
	 * @param {HTMLElement} root - Root element of one calculator card.
	 */
	constructor(root) {
		this.root = root;
		this.rowsEl = root.querySelector('.lt-area-calc__rows');
		this.templateEl = root.querySelector('.lt-area-calc__template');
		this.addBtn = root.querySelector('.lt-area-calc__add');
		this.totalEl = root.querySelector('.lt-area-calc__total');
		this.wastageEl = root.querySelector('.lt-area-calc__wastage');
		this.useBtn = root.querySelector('.lt-area-calc__use');

		// Wastage is authored as a percentage (10 → ×1.10).
		const wastage = parseFloat(root.dataset.wastage);
		this.wastageFactor = 1 + (isNaN(wastage) ? DEFAULT_WASTAGE : wastage) / 100;

		// Companion boxes (e.g. the "How it works" summary) that mirror the totals.
		this.mirrors = null;

		this.updateTotals = this.updateTotals.bind(this);
		this.addRow = this.addRow.bind(this);
		this.handleUse = this.handleUse.bind(this);

		if (this.addBtn) {
			this.addBtn.addEventListener('click', this.addRow);
		}
		if (this.useBtn) {
			this.useBtn.addEventListener('click', this.handleUse);
		}

		// Init one row.
		this.addRow();
	}

	/**
	 * Companion boxes that mirror this card's totals.
	 *
	 * Looked up lazily (and cached once found) because the mirroring markup is a
	 * sibling rendered after the card — with `data-lt-area-mirror` set to this
	 * card's id, so several calculators on a page each drive their own box.
	 *
	 * @return {HTMLElement[]} Matching mirror containers.
	 */
	getMirrors() {
		if (this.mirrors && this.mirrors.length) return this.mirrors;
		if (!this.root.id) return [];

		this.mirrors = Array.from(
			document.querySelectorAll('[data-lt-area-mirror="' + this.root.id + '"]')
		);

		return this.mirrors;
	}

	/**
	 * Recalculate every row and the two totals.
	 *
	 * @return {number} Total area in sqm.
	 */
	updateTotals() {
		let total = 0;

		this.rowsEl.querySelectorAll('.lt-area-row').forEach((row) => {
			const l = parseFloat(row.querySelector('.lt-area-row__length')?.value) || 0;
			const w = parseFloat(row.querySelector('.lt-area-row__width')?.value) || 0;
			const area = l * w;
			const areaEl = row.querySelector('.lt-area-row__area');
			if (areaEl) areaEl.textContent = area.toFixed(2);
			total += area;
		});

		const totalText = total.toFixed(2) + ' sqm';
		const wastageText = (total * this.wastageFactor).toFixed(2) + ' sqm';

		if (this.totalEl) this.totalEl.textContent = totalText;
		if (this.wastageEl) this.wastageEl.textContent = wastageText;
		this.root.dataset.totalArea = total;

		this.getMirrors().forEach((box) => {
			const mirrorTotal = box.querySelector('[data-lt-area-mirror-field="total"]');
			const mirrorWastage = box.querySelector('[data-lt-area-mirror-field="wastage"]');
			if (mirrorTotal) mirrorTotal.textContent = totalText;
			if (mirrorWastage) mirrorWastage.textContent = wastageText;
		});

		return total;
	}

	/**
	 * Clone the row template, wire it up and append it.
	 */
	addRow() {
		const clone = this.templateEl.content.cloneNode(true);
		const row = clone.querySelector('.lt-area-row');
		if (!row) return;

		row.querySelectorAll('.lt-area-row__length, .lt-area-row__width').forEach((input) => {
			input.addEventListener('input', this.updateTotals);
		});

		const removeBtn = row.querySelector('.lt-area-row__remove');
		if (removeBtn) {
			removeBtn.addEventListener('click', () => {
				row.remove();
				this.updateTotals();
				// Never leave the card empty.
				if (this.rowsEl.children.length === 0) this.addRow();
			});
		}

		this.rowsEl.appendChild(clone);
	}

	/**
	 * CTA handler.
	 *
	 * Purely opt-in host-page integration: when the CTA carries `data-target`
	 * the total is written into that input (and an `input` event dispatched so
	 * the host recalculates); `data-scroll-to` then scrolls that element into
	 * view. Without either attribute the CTA is inert — or, when rendered as a
	 * link, simply follows its href.
	 *
	 * @param {Event} event - Click event.
	 */
	handleUse(event) {
		const targetSel = this.useBtn.dataset.target || '';
		const scrollSel = this.useBtn.dataset.scrollTo || '';

		if (!targetSel && !scrollSel) return;

		const totalArea = parseFloat(this.root.dataset.totalArea) || 0;

		if (targetSel) {
			const targetInput = document.querySelector(targetSel);
			if (targetInput) {
				event.preventDefault();
				targetInput.value = totalArea.toFixed(2);
				targetInput.dispatchEvent(new Event('input'));
			}
		}

		if (scrollSel) {
			const scrollEl = document.querySelector(scrollSel);
			if (scrollEl) {
				event.preventDefault();
				scrollEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		}
	}
}

/**
 * Boot every calculator inside `scope` that has not been booted yet.
 *
 * Safe to call more than once (e.g. from the block view script *and* the
 * product-single bundle on a page that happens to have both).
 *
 * @param {Document|HTMLElement} scope - Search root. Defaults to `document`.
 * @return {AreaCalculator[]} The instances created by this call.
 */
export function initAreaCalculators(scope = document) {
	const instances = [];

	scope.querySelectorAll(ROOT_SELECTOR).forEach((root) => {
		if (root.dataset.ltAreaCalcReady === '1') return;

		// Bail on incomplete markup rather than throwing.
		if (!root.querySelector('.lt-area-calc__rows') || !root.querySelector('.lt-area-calc__template')) {
			return;
		}

		root.dataset.ltAreaCalcReady = '1';
		instances.push(new AreaCalculator(root));
	});

	return instances;
}

/**
 * Run `initAreaCalculators()` as soon as the DOM is parsed.
 */
export function autoInitAreaCalculators() {
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => initAreaCalculators());
	} else {
		initAreaCalculators();
	}
}

export default AreaCalculator;
