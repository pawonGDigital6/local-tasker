/**
 * Product Single — gallery, box calculator, area calculator, choose option, related slider.
 * Vanilla JS only.
 */
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

(function () {
	'use strict';

	/* ── Constants ─────────────────────────────────────────── */
	const GST_RATE      = 0.10;
	const WASTAGE_FACTOR = 1.10;

	/* ── Product data from PHP data-attrs ──────────────────── */
	const summary       = document.getElementById('lt-product-summary');
	if (!summary) return;

	let cartonSqm       = parseFloat(summary.dataset.cartonSqm) || 0;
	let boxPriceEx      = parseFloat(summary.dataset.boxPriceEx) || 0;
	let pricePerSqmEx   = parseFloat(summary.dataset.pricePerSqmEx) || 0;

	/* ── Format currency (AUD) ─────────────────────────────── */
	function formatCurrency(n) {
		return '$' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	}

	/* ══════════════════════════════════════════════════════════
	   1. PRODUCT GALLERY
	══════════════════════════════════════════════════════════ */
	const gallery     = document.getElementById('lt-gallery');
	if (gallery) {
		const track     = document.getElementById('lt-gallery-track');
		const slides    = gallery.querySelectorAll('.lt-gallery__slide');
		const thumbs    = gallery.querySelectorAll('.lt-gallery__thumb');
		const dots      = gallery.querySelectorAll('.lt-gallery__dot');
		const btnPrev   = document.getElementById('lt-gallery-prev');
		const btnNext   = document.getElementById('lt-gallery-next');
		const count     = slides.length;
		let   current   = 0;

		function goTo(index) {
			current = Math.max(0, Math.min(index, count - 1));
			track.style.transform = `translateX(-${current * 100}%)`;

			slides.forEach(function (s, i) { s.setAttribute('aria-hidden', i !== current ? 'true' : 'false'); });

			thumbs.forEach(function (t, i) {
				t.classList.toggle('border-lt-brand', i === current);
				t.classList.toggle('border-transparent', i !== current);
				t.setAttribute('aria-selected', i === current ? 'true' : 'false');
			});

			dots.forEach(function (d, i) {
				const active = i === current;
				d.classList.toggle('bg-lt-brand', active);
				d.classList.toggle('w-[20px]', active);
				d.classList.toggle('bg-lt-white/60', !active);
				d.classList.toggle('w-[8px]', !active);
				d.setAttribute('aria-selected', active ? 'true' : 'false');
			});
		}

		thumbs.forEach(function (t) {
			t.addEventListener('click', function () { goTo(parseInt(t.dataset.index, 10)); });
		});
		dots.forEach(function (d) {
			d.addEventListener('click', function () { goTo(parseInt(d.dataset.index, 10)); });
		});
		if (btnPrev) btnPrev.addEventListener('click', function () { goTo(current - 1); });
		if (btnNext) btnNext.addEventListener('click', function () { goTo(current + 1); });

		// Keyboard navigation.
		gallery.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft')  goTo(current - 1);
			if (e.key === 'ArrowRight') goTo(current + 1);
		});
	}

	/* ══════════════════════════════════════════════════════════
	   2. BOX CALCULATOR
	══════════════════════════════════════════════════════════ */
	const calcWrap     = document.getElementById('lt-calculator');
	if (calcWrap && cartonSqm > 0) {

		const areaInput    = document.getElementById('lt-calc-area');
		const minusBtn     = document.getElementById('lt-calc-minus');
		const plusBtn      = document.getElementById('lt-calc-plus');
		const roundUpCb    = document.getElementById('lt-calc-round-up');
		const wastageCb    = document.getElementById('lt-calc-wastage');
		const wastageBanner = document.getElementById('lt-wastage-banner');
		const resultCoverage = document.getElementById('lt-result-coverage');
		const resultBoxes    = document.getElementById('lt-result-boxes');
		const resultTotal    = document.getElementById('lt-result-total');
		const addToCartBtn   = document.getElementById('lt-add-to-cart');
		const checkoutBtn    = document.getElementById('lt-checkout-btn');

		function compute() {
			if (!areaInput) return;
			const areaSqm    = Math.max(0, parseFloat(areaInput.value) || 0);
			const roundUp    = roundUpCb  ? roundUpCb.checked  : true;
			const addWastage = wastageCb  ? wastageCb.checked  : false;

			if (cartonSqm <= 0) return;

			const effective  = addWastage ? areaSqm * WASTAGE_FACTOR : areaSqm;
			const boxes      = effective > 0
				? (roundUp ? Math.ceil(effective / cartonSqm) : Math.floor(effective / cartonSqm))
				: 0;
			const coverageSqm = boxes * cartonSqm;
			const subtotalEx  = boxes * boxPriceEx;
			const gst         = subtotalEx * GST_RATE;
			const totalInc    = subtotalEx + gst;

			if (resultCoverage) resultCoverage.textContent = coverageSqm.toFixed(2);
			if (resultBoxes)    resultBoxes.textContent    = boxes;
			if (resultTotal)    resultTotal.textContent    = formatCurrency(totalInc);

			const enabled = boxes > 0;
			if (addToCartBtn) {
				addToCartBtn.disabled = !enabled;
				addToCartBtn.dataset.boxes = boxes;
			}
			if (checkoutBtn) {
				checkoutBtn.classList.toggle('hidden', !enabled);
			}

			// Wastage banner.
			if (wastageBanner) {
				wastageBanner.classList.toggle('hidden',   !addWastage);
				wastageBanner.classList.toggle('flex',      addWastage);
			}
		}

		// Stepper buttons.
		const STEP = 0.5;
		if (minusBtn) {
			minusBtn.addEventListener('click', function () {
				const v = parseFloat(areaInput.value) || 0;
				areaInput.value = Math.max(0, parseFloat((v - STEP).toFixed(1)));
				compute();
			});
		}
		if (plusBtn) {
			plusBtn.addEventListener('click', function () {
				const v = parseFloat(areaInput.value) || 0;
				areaInput.value = parseFloat((v + STEP).toFixed(1));
				compute();
			});
		}

		if (areaInput)  areaInput.addEventListener('input',  compute);
		if (roundUpCb)  roundUpCb.addEventListener('change', compute);
		if (wastageCb)  wastageCb.addEventListener('change', compute);

		// Add to Cart via WooCommerce AJAX.
		if (addToCartBtn) {
			addToCartBtn.addEventListener('click', function () {
				const boxes     = parseInt(addToCartBtn.dataset.boxes, 10) || 0;
				const productId = addToCartBtn.dataset.productId;
				const nonce     = addToCartBtn.dataset.nonce;
				if (!boxes || !productId) return;

				addToCartBtn.disabled    = true;
				addToCartBtn.textContent = 'Adding…';

				const formData = new FormData();
				formData.append('action',     'lt_add_flooring_to_cart');
				formData.append('product_id', productId);
				formData.append('quantity',   boxes);
				formData.append('nonce',      nonce);
				formData.append('area_sqm',   areaInput ? areaInput.value : 0);
				formData.append('option',     document.querySelector('[name="lt_purchase_option"]:checked')?.value || 'purchase-only');

				fetch(ltShopData.ajaxUrl, { method: 'POST', body: formData })
					.then(function (r) { return r.json(); })
					.then(function (data) {
						if (data.success) {
							addToCartBtn.textContent = 'Added!';
							if (checkoutBtn) checkoutBtn.classList.remove('hidden');
							// Refresh WC cart fragments.
							document.body.dispatchEvent(new CustomEvent('wc_fragment_refresh'));
							setTimeout(function () {
								addToCartBtn.disabled    = false;
								addToCartBtn.textContent = 'Add to Cart';
							}, 2000);
						} else {
							addToCartBtn.disabled    = false;
							addToCartBtn.textContent = 'Add to Cart';
						}
					})
					.catch(function () {
						addToCartBtn.disabled    = false;
						addToCartBtn.textContent = 'Add to Cart';
					});
			});
		}

		compute(); // Initial render.
	}

	/* ══════════════════════════════════════════════════════════
	   3. CHOOSE OPTION — switches effective price in calculator
	══════════════════════════════════════════════════════════ */
	document.querySelectorAll('[name="lt_purchase_option"]').forEach(function (radio) {
		radio.addEventListener('change', function () {
			// Update visual state.
			document.querySelectorAll('.lt-choose-option__row').forEach(function (row) {
				const inp = row.querySelector('[name="lt_purchase_option"]');
				const isActive = inp && inp.checked;
				row.classList.toggle('border-lt-brand', isActive);
				row.classList.toggle('bg-lt-brand/5',   isActive);
				row.classList.toggle('border-[#E9EAEC]', !isActive);
			});

			// Update price used by calculator.
			const newPricePerSqm = parseFloat(radio.dataset.pricePerSqmEx) || pricePerSqmEx;
			pricePerSqmEx = newPricePerSqm;
			boxPriceEx    = cartonSqm > 0 ? newPricePerSqm * cartonSqm : boxPriceEx;
			// Re-read boxPriceEx via summary attr update then recompute.
			summary.dataset.boxPriceEx    = boxPriceEx;
			summary.dataset.pricePerSqmEx = pricePerSqmEx;
			// Re-trigger calculator compute (event on area input).
			const areaInput = document.getElementById('lt-calc-area');
			if (areaInput) areaInput.dispatchEvent(new Event('input'));
		});
	});

	/* ══════════════════════════════════════════════════════════
	   4. AREA CALCULATOR
	══════════════════════════════════════════════════════════ */
	const areaCalcEl = document.getElementById('lt-area-calculator');
	const addRowBtn  = document.getElementById('lt-area-add-row');
	const rowsEl     = document.getElementById('lt-area-rows');
	const totalEl    = document.getElementById('lt-area-total');
	const wastageEl  = document.getElementById('lt-area-wastage');
	const useAreaBtn = document.getElementById('lt-use-this-area');
	const rowTemplate = document.getElementById('lt-area-row-template');

	if (areaCalcEl && rowsEl && rowTemplate) {

		function updateAreaTotals() {
			let total = 0;
			rowsEl.querySelectorAll('.lt-area-row').forEach(function (row) {
				const l = parseFloat(row.querySelector('.lt-area-row__length')?.value) || 0;
				const w = parseFloat(row.querySelector('.lt-area-row__width')?.value)  || 0;
				const area = l * w;
				const areaEl = row.querySelector('.lt-area-row__area');
				if (areaEl) areaEl.textContent = area.toFixed(2);
				total += area;
			});
			if (totalEl)   totalEl.textContent   = total.toFixed(2) + ' sqm';
			if (wastageEl) wastageEl.textContent  = (total * WASTAGE_FACTOR).toFixed(2) + ' sqm';
			areaCalcEl.dataset.totalArea = total;
		}

		function addRow() {
			const clone = rowTemplate.content.cloneNode(true);
			const row   = clone.querySelector('.lt-area-row');

			row.querySelectorAll('.lt-area-row__length, .lt-area-row__width').forEach(function (input) {
				input.addEventListener('input', updateAreaTotals);
			});

			row.querySelector('.lt-area-row__remove').addEventListener('click', function () {
				row.remove();
				updateAreaTotals();
				if (rowsEl.children.length === 0) addRow();
			});

			rowsEl.appendChild(clone);
		}

		if (addRowBtn) addRowBtn.addEventListener('click', addRow);

		// "Use This Area" — writes total into the main calculator then scrolls up to it.
		if (useAreaBtn) {
			useAreaBtn.addEventListener('click', function () {
				const totalArea = parseFloat(areaCalcEl.dataset.totalArea) || 0;
				const mainInput = document.getElementById('lt-calc-area');
				if (mainInput) {
					mainInput.value = totalArea.toFixed(2);
					mainInput.dispatchEvent(new Event('input'));
				}
				const calcEl = document.getElementById('lt-calculator');
				if (calcEl) calcEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
			});
		}

		// Init one row.
		addRow();
	}

	/* ══════════════════════════════════════════════════════════
	   5. RELATED PRODUCTS SLIDER
	══════════════════════════════════════════════════════════ */
	const relatedSlider = document.getElementById('lt-related-slider');
	if (relatedSlider) {
		new Swiper(relatedSlider, {
			speed: 400,
			spaceBetween: 20,
			slidesPerView: 1,
			breakpoints: {
				568:  { slidesPerView: 2 },
				992:  { slidesPerView: 3 },
				1200: { slidesPerView: 4 },
			},
			navigation: {
				nextEl: '.lt-related-next',
				prevEl: '.lt-related-prev',
			},
		});
	}

})();
