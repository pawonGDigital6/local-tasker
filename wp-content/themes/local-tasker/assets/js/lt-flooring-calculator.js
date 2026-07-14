/**
 * Local Tasker — Flooring calculator.
 *
 * Powers the "How much flooring do you need?" widget on the single product page:
 *   • Converts a desired area (m²) into a whole number of boxes.
 *   • Optionally rounds up to full boxes and/or adds 10% wastage.
 *   • Shows live coverage, box count and an incl-GST total.
 *   • Drives the room-by-room area calculator (length × width → area).
 *   • Writes the resulting box quantity + area into the hidden form fields so the
 *     native WooCommerce add-to-cart posts the right values.
 *
 * Self-initialising, dependency-free.
 */
(function () {
	'use strict';

	var money = null;

	/**
	 * Format a number as currency. Uses the symbol already rendered by WooCommerce
	 * in the total node, falling back to a plain 2dp number with a leading $.
	 */
	function formatMoney(value, sampleEl) {
		if (!money && sampleEl) {
			var txt = sampleEl.textContent.trim();
			var m = txt.match(/^([^\d\-]*)/);
			money = { prefix: m ? m[1] : '$' };
		}
		var prefix = money ? money.prefix : '$';
		return prefix + value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	}

	function num(el, fallback) {
		var v = parseFloat(el && el.value);
		return isNaN(v) ? (fallback || 0) : v;
	}

	function initCalc(form) {
		if (form.__ltCalcInit) { return; }
		form.__ltCalcInit = true;

		var sqmPerBox   = parseFloat(form.getAttribute('data-sqm-per-box')) || 0;
		var boxPriceInc = parseFloat(form.getAttribute('data-box-price-incl')) || 0;

		var sqmInput   = form.querySelector('[data-lt-sqm]');
		var roundUp    = form.querySelector('[data-lt-roundup]');
		var wastage    = form.querySelector('[data-lt-wastage]');
		var coverageEl = form.querySelector('[data-lt-coverage]');
		var boxesEl    = form.querySelector('[data-lt-boxes]');
		var totalEl    = form.querySelector('[data-lt-total]');
		var qtyField   = form.querySelector('[data-lt-qty]');
		var areaField  = form.querySelector('[data-lt-area-input]');

		function recalc() {
			var sqm = num(sqmInput, 0);
			if (sqm < 0) { sqm = 0; }

			var effective = wastage && wastage.checked ? sqm * 1.10 : sqm;

			var boxes, coverage;
			if (sqmPerBox > 0) {
				var raw = effective / sqmPerBox;
				boxes = (roundUp && roundUp.checked) ? Math.ceil(raw) : Math.round(raw * 100) / 100;
				coverage = (roundUp && roundUp.checked) ? boxes * sqmPerBox : effective;
			} else {
				boxes = Math.max(1, Math.ceil(effective));
				coverage = effective;
			}

			var qty = Math.max(1, Math.ceil(boxes));
			var total = qty * boxPriceInc;

			if (coverageEl) { coverageEl.textContent = coverage.toFixed(2); }
			if (boxesEl) { boxesEl.textContent = (roundUp && roundUp.checked) ? qty : boxes; }
			if (totalEl) { totalEl.textContent = formatMoney(total, totalEl); }
			if (qtyField) { qtyField.value = qty; }
			if (areaField) { areaField.value = coverage.toFixed(2); }
		}

		[sqmInput, roundUp, wastage].forEach(function (el) {
			if (!el) { return; }
			el.addEventListener('input', recalc);
			el.addEventListener('change', recalc);
		});

		// Stepper (+/- one box worth of area).
		form.querySelectorAll('[data-lt-step]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var dir = parseFloat(btn.getAttribute('data-lt-step')) || 0;
				var stepBy = sqmPerBox > 0 ? sqmPerBox : 1;
				var next = num(sqmInput, 0) + dir * stepBy;
				var min = parseFloat(sqmInput.getAttribute('min')) || 0;
				sqmInput.value = Math.max(min, Math.round(next * 100) / 100);
				recalc();
			});
		});

		initArea(form, sqmInput, recalc);
		initActions(form);

		recalc();
	}

	function initArea(form, sqmInput, recalc) {
		var toggle = form.querySelector('[data-lt-area-toggle]');
		var panel  = form.querySelector('[data-lt-area]');
		if (toggle && panel) {
			toggle.addEventListener('click', function () {
				var open = panel.hasAttribute('hidden');
				if (open) { panel.removeAttribute('hidden'); } else { panel.setAttribute('hidden', ''); }
				toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			});
		}

		var body    = form.querySelector('[data-lt-rooms-body]');
		var addBtn  = form.querySelector('[data-lt-room-add]');
		var totalEl = form.querySelector('[data-lt-area-total]');
		var wasteEl = form.querySelector('[data-lt-area-wastage]');
		var useBtn  = form.querySelector('[data-lt-area-use]');
		if (!body) { return; }

		function recalcRooms() {
			var total = 0;
			body.querySelectorAll('[data-lt-room]').forEach(function (row) {
				var len = num(row.querySelector('.lt-area__len'), 0);
				var wid = num(row.querySelector('.lt-area__wid'), 0);
				var area = len * wid;
				var cell = row.querySelector('[data-lt-room-area]');
				if (cell) { cell.textContent = area.toFixed(2); }
				total += area;
			});
			if (totalEl) { totalEl.textContent = total.toFixed(2); }
			if (wasteEl) { wasteEl.textContent = (total * 1.10).toFixed(2); }
			return total;
		}

		body.addEventListener('input', recalcRooms);

		body.addEventListener('click', function (e) {
			var rm = e.target.closest('[data-lt-room-remove]');
			if (rm && body.querySelectorAll('[data-lt-room]').length > 1) {
				rm.closest('[data-lt-room]').remove();
				recalcRooms();
			}
		});

		if (addBtn) {
			addBtn.addEventListener('click', function () {
				var rows = body.querySelectorAll('[data-lt-room]');
				var clone = rows[rows.length - 1].cloneNode(true);
				clone.querySelectorAll('input').forEach(function (input) {
					if (input.classList.contains('lt-area__name')) {
						input.value = 'Room ' + (rows.length + 1);
					} else {
						input.value = '';
					}
				});
				var cell = clone.querySelector('[data-lt-room-area]');
				if (cell) { cell.textContent = '0.00'; }
				body.appendChild(clone);
				recalcRooms();
			});
		}

		if (useBtn) {
			useBtn.addEventListener('click', function () {
				var total = recalcRooms();
				if (total > 0 && sqmInput) {
					sqmInput.value = total.toFixed(2);
					recalc();
				}
			});
		}

		recalcRooms();
	}

	function initActions(form) {
		var flag = form.querySelector('[data-lt-checkout-flag]');
		var checkoutBtn = form.querySelector('[data-lt-checkout]');
		var addBtn = form.querySelector('[data-lt-add]');

		if (checkoutBtn && flag) {
			checkoutBtn.addEventListener('click', function () { flag.value = '1'; });
		}
		if (addBtn && flag) {
			addBtn.addEventListener('click', function () { flag.value = ''; });
		}

		// Wishlist — visual toggle + event a wishlist plugin can hook into.
		var wish = form.querySelector('[data-lt-wishlist]');
		if (wish) {
			wish.addEventListener('click', function () {
				wish.classList.toggle('is-active');
				var id = (form.querySelector('[name="add-to-cart"]') || {}).value;
				document.dispatchEvent(new CustomEvent('lt:wishlist', { detail: { productId: id } }));
			});
		}
	}

	function boot() {
		document.querySelectorAll('form[data-lt-calc]').forEach(initCalc);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
