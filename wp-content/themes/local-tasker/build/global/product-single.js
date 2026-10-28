/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/global/js/components/area-calculator.js"
/*!*****************************************************!*\
  !*** ./src/global/js/components/area-calculator.js ***!
  \*****************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   autoInitAreaCalculators: () => (/* binding */ autoInitAreaCalculators),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   initAreaCalculators: () => (/* binding */ initAreaCalculators)
/* harmony export */ });
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
    this.mirrors = Array.from(document.querySelectorAll('[data-lt-area-mirror="' + this.root.id + '"]'));
    return this.mirrors;
  }

  /**
   * Recalculate every row and the two totals.
   *
   * @return {number} Total area in sqm.
   */
  updateTotals() {
    let total = 0;
    this.rowsEl.querySelectorAll('.lt-area-row').forEach(row => {
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
    this.getMirrors().forEach(box => {
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
    row.querySelectorAll('.lt-area-row__length, .lt-area-row__width').forEach(input => {
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
        scrollEl.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
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
function initAreaCalculators(scope = document) {
  const instances = [];
  scope.querySelectorAll(ROOT_SELECTOR).forEach(root => {
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
function autoInitAreaCalculators() {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initAreaCalculators());
  } else {
    initAreaCalculators();
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (AreaCalculator);

/***/ },

/***/ "swiper/bundle"
/*!*************************!*\
  !*** external "Swiper" ***!
  \*************************/
(module) {

module.exports = window["Swiper"];

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	const __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		const cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		const module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			const e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			const getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter/value functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			if(Array.isArray(definition)) {
/******/ 				var i = 0;
/******/ 				while(i < definition.length) {
/******/ 					var key = definition[i++];
/******/ 					var binding = definition[i++];
/******/ 					if(!__webpack_require__.o(exports, key)) {
/******/ 						if(binding === 0) {
/******/ 							Object.defineProperty(exports, key, { enumerable: true, value: definition[i++] });
/******/ 						} else {
/******/ 							Object.defineProperty(exports, key, { enumerable: true, get: binding });
/******/ 						}
/******/ 					} else if(binding === 0) { i++; }
/******/ 				}
/******/ 			} else {
/******/ 				for(var key in definition) {
/******/ 					if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 						Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.hasOwn(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
let __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*****************************************!*\
  !*** ./src/global/js/product-single.js ***!
  \*****************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var swiper_bundle__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! swiper/bundle */ "swiper/bundle");
/* harmony import */ var swiper_bundle__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(swiper_bundle__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_area_calculator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/area-calculator */ "./src/global/js/components/area-calculator.js");
/**
 * Product Single — gallery, box calculator, area calculator, choose option, related slider.
 * Vanilla JS only.
 */


(function () {
  'use strict';

  /* ── Constants ─────────────────────────────────────────── */
  const GST_RATE = 0.10;
  // Fallback only — the live figure comes from the calculator's
  // `data-wastage-percent`, written by the backend field (see
  // lt_get_wastage_percent() in inc/lt-woocommerce-flooring.php).
  const DEFAULT_WASTAGE_PCT = 10;

  /* ══════════════════════════════════════════════════════════
     AREA CALCULATOR (shared component — see section 4 below)
     Booted before the product guard so the card keeps working
     even when no product summary is present on the page.
  ══════════════════════════════════════════════════════════ */
  (0,_components_area_calculator__WEBPACK_IMPORTED_MODULE_1__.initAreaCalculators)();

  /* ── Product data from PHP data-attrs ──────────────────── */
  const summary = document.getElementById('lt-product-summary');
  if (!summary) return;
  let cartonSqm = parseFloat(summary.dataset.cartonSqm) || 0;
  let boxPriceEx = parseFloat(summary.dataset.boxPriceEx) || 0;
  let pricePerSqmEx = parseFloat(summary.dataset.pricePerSqmEx) || 0;
  // NOTE: the installation opt-in is a QUOTE REQUEST, not a priced add-on, so
  // no install rate is read here and no price on this page depends on it.

  /* ── Variable product state ────────────────────────────── */
  const requiresVariation = summary.dataset.isVariable === '1';
  let variations = [];
  try {
    variations = JSON.parse(summary.dataset.variations || '[]');
  } catch (e) {
    variations = [];
  }
  let defaultAttributes = {};
  try {
    defaultAttributes = JSON.parse(summary.dataset.defaultAttributes || '{}');
  } catch (e) {
    defaultAttributes = {};
  }
  const selectedAttributes = Object.assign({}, defaultAttributes);
  let selectedVariationId = null;
  let variationInStock = false;
  let recomputeCalculator = function () {};

  /* ══════════════════════════════════════════════════════════
     0. VARIATION SELECTOR (colour / thickness swatches)
  ══════════════════════════════════════════════════════════ */
  const variationSelector = document.getElementById('lt-variation-selector');
  if (requiresVariation && variationSelector) {
    const hint = document.getElementById('lt-variation-hint');
    const priceWas = document.getElementById('lt-price-was');
    const priceNow = document.getElementById('lt-price-now');
    const priceInc = document.getElementById('lt-price-inc');
    const priceBox = document.getElementById('lt-price-box');
    const addToCartBtnEl = document.getElementById('lt-add-to-cart');
    function findVariation() {
      const groupKeys = Array.from(variationSelector.querySelectorAll('.lt-variation-group')).map(function (g) {
        return g.dataset.attribute;
      });
      const complete = groupKeys.every(function (key) {
        return !!selectedAttributes[key];
      });
      if (!complete) return null;
      return variations.find(function (v) {
        return groupKeys.every(function (key) {
          const want = v.attributes[key];
          return !want || want === selectedAttributes[key];
        });
      }) || null;
    }
    function setActiveButtons() {
      variationSelector.querySelectorAll('.lt-variation-group').forEach(function (group) {
        const attr = group.dataset.attribute;
        const label = group.querySelector('.lt-variation-group__selected');
        let selectedName = '';
        group.querySelectorAll('.lt-variation-option').forEach(function (btn) {
          const active = btn.dataset.value === selectedAttributes[attr];
          btn.classList.toggle('border-lt-brand', active);
          btn.classList.toggle('text-lt-brand', active && btn.classList.contains('lt-variation-option--pill'));
          btn.classList.toggle('bg-lt-brand/5', active && btn.classList.contains('lt-variation-option--pill'));
          btn.classList.toggle('border-transparent', !active && btn.classList.contains('lt-variation-option--swatch'));
          btn.classList.toggle('border-[#E9EAEC]', !active && btn.classList.contains('lt-variation-option--pill'));
          if (active) selectedName = btn.dataset.name || btn.title || '';
        });
        if (label) label.textContent = selectedName;
      });
    }

    // WooCommerce renders its own attribute <select> dropdowns alongside our
    // custom swatches/pills (needed so its native variation-matching JS keeps
    // the Add to Cart button + hidden variation_id in sync). Those selects are
    // hidden via CSS; clicking a swatch must drive them the same way a user
    // picking from the dropdown would, via a real `change` event.
    function syncNativeSelects() {
      const form = summary.querySelector('.variations_form');
      if (!form) return;
      Object.keys(selectedAttributes).forEach(function (attr) {
        const select = form.querySelector('select[name="attribute_' + attr + '"]');
        if (select && select.value !== selectedAttributes[attr]) {
          select.value = selectedAttributes[attr];
          select.dispatchEvent(new Event('change', {
            bubbles: true
          }));
        }
      });
    }
    function applyVariation() {
      syncNativeSelects();
      const matched = findVariation();
      selectedVariationId = matched ? matched.variation_id : null;
      variationInStock = matched ? !!matched.in_stock : false;
      if (hint) hint.classList.toggle('hidden', !!matched);
      if (matched) {
        boxPriceEx = matched.price_ex;
        pricePerSqmEx = cartonSqm > 0 ? boxPriceEx / cartonSqm : 0;
        const regularPerSqm = cartonSqm > 0 ? matched.regular_price_ex / cartonSqm : 0;
        const onSale = matched.regular_price_ex > matched.price_ex;
        summary.dataset.boxPriceEx = boxPriceEx;
        summary.dataset.pricePerSqmEx = pricePerSqmEx;
        if (priceNow) priceNow.textContent = formatCurrency(pricePerSqmEx);
        if (priceInc) priceInc.textContent = formatCurrency(pricePerSqmEx * (1 + GST_RATE));
        if (priceBox) priceBox.textContent = formatCurrency(boxPriceEx);
        if (priceWas) {
          priceWas.classList.toggle('hidden', !onSale);
          if (onSale) priceWas.textContent = formatCurrency(regularPerSqm);
        }

        // The installation opt-in never alters the price, so the selected
        // variation's own price is the final one — nothing to reconcile.
      }
      if (addToCartBtnEl) {
        addToCartBtnEl.dataset.variationId = selectedVariationId || 0;
      }
      setActiveButtons();
      recomputeCalculator();
    }
    variationSelector.querySelectorAll('.lt-variation-option').forEach(function (btn) {
      btn.addEventListener('click', function () {
        selectedAttributes[btn.dataset.attribute] = btn.dataset.value;
        applyVariation();
      });
    });
    applyVariation(); // Reflect any pre-selected defaults on load.
  }

  /* ── Format currency (AUD) ─────────────────────────────── */
  function formatCurrency(n) {
    return '$' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }

  /* ══════════════════════════════════════════════════════════
     1. PRODUCT GALLERY
  ══════════════════════════════════════════════════════════ */
  const gallery = document.getElementById('lt-gallery');
  if (gallery) {
    const track = document.getElementById('lt-gallery-track');
    const slides = gallery.querySelectorAll('.lt-gallery__slide');
    const thumbs = gallery.querySelectorAll('.lt-gallery__thumb');
    const dots = gallery.querySelectorAll('.lt-gallery__dot');
    const btnPrev = document.getElementById('lt-gallery-prev');
    const btnNext = document.getElementById('lt-gallery-next');
    const count = slides.length;
    let current = 0;
    function goTo(index) {
      current = Math.max(0, Math.min(index, count - 1));
      track.style.transform = `translateX(-${current * 100}%)`;
      slides.forEach(function (s, i) {
        const hidden = i !== current;
        s.setAttribute('aria-hidden', hidden ? 'true' : 'false');
        // The lightbox trigger inside a hidden slide must leave the tab
        // order with it — a focusable child of an aria-hidden element is
        // both a screen-reader trap and a tab stop on nothing visible.
        const zoom = s.querySelector('.lt-gallery__zoom');
        if (zoom) zoom.setAttribute('tabindex', hidden ? '-1' : '0');
      });
      thumbs.forEach(function (t, i) {
        t.classList.toggle('border-lt-accent', i === current);
        t.classList.toggle('border-[#E2DDD7]', i !== current);
        t.classList.toggle('opacity-100', i === current);
        t.classList.toggle('opacity-50', i !== current);
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
      t.addEventListener('click', function () {
        goTo(parseInt(t.dataset.index, 10));
      });
    });
    dots.forEach(function (d) {
      d.addEventListener('click', function () {
        goTo(parseInt(d.dataset.index, 10));
      });
    });
    if (btnPrev) btnPrev.addEventListener('click', function () {
      goTo(current - 1);
    });
    if (btnNext) btnNext.addEventListener('click', function () {
      goTo(current + 1);
    });

    // Keyboard navigation.
    gallery.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') goTo(current - 1);
      if (e.key === 'ArrowRight') goTo(current + 1);
    });
  }

  /* ══════════════════════════════════════════════════════════
     2. BOX CALCULATOR
  ══════════════════════════════════════════════════════════ */
  const calcWrap = document.getElementById('lt-calculator');
  if (calcWrap && cartonSqm > 0) {
    const areaInput = document.getElementById('lt-calc-area');
    const minusBtn = document.getElementById('lt-calc-minus');
    const plusBtn = document.getElementById('lt-calc-plus');
    const wastageCb = document.getElementById('lt-calc-wastage');
    const wastageBanner = document.getElementById('lt-wastage-banner');
    const resultCoverage = document.getElementById('lt-result-coverage');
    const resultBoxes = document.getElementById('lt-result-boxes');
    const resultTotal = document.getElementById('lt-result-total');
    const addToCartBtn = document.getElementById('lt-add-to-cart');
    const checkoutBtn = document.getElementById('lt-checkout-btn');

    // Wastage percentage is configured in the backend and printed onto the
    // calculator; a missing/invalid attribute keeps the historical 10%.
    const wastagePctRaw = parseFloat(calcWrap.dataset.wastagePercent);
    const wastagePct = isNaN(wastagePctRaw) ? DEFAULT_WASTAGE_PCT : Math.max(0, wastagePctRaw);
    const wastageFactor = 1 + wastagePct / 100;
    function compute() {
      if (!areaInput) return;
      const areaSqm = Math.max(0, parseFloat(areaInput.value) || 0);
      // Boxes cannot be split, so the box count is always rounded up — this
      // is no longer a customer-facing choice.
      const addWastage = wastageCb ? wastageCb.checked : false;
      if (cartonSqm <= 0) return;
      const effective = addWastage ? areaSqm * wastageFactor : areaSqm;
      const boxes = effective > 0 ? Math.ceil(effective / cartonSqm) : 0;
      const coverageSqm = boxes * cartonSqm;
      const subtotalEx = boxes * boxPriceEx;
      const gst = subtotalEx * GST_RATE;
      const totalInc = subtotalEx + gst;
      if (resultCoverage) resultCoverage.textContent = coverageSqm.toFixed(2);
      if (resultBoxes) resultBoxes.textContent = boxes;
      if (resultTotal) resultTotal.textContent = formatCurrency(totalInc);

      // Mirror into the "How it works" Quick estimate box (if present).
      var qeArea = document.getElementById('lt-qe-area');
      var qeBoxes = document.getElementById('lt-qe-boxes');
      var qeTotal = document.getElementById('lt-qe-total');
      if (qeArea) qeArea.textContent = (areaSqm > 0 ? Math.round(areaSqm * 100) / 100 : 0) + ' sqm';
      if (qeBoxes) qeBoxes.textContent = boxes + ' boxes (' + coverageSqm.toFixed(2) + ' sqm)';
      if (qeTotal) qeTotal.textContent = formatCurrency(totalInc);
      const variationOk = !requiresVariation || selectedVariationId && variationInStock;
      const enabled = boxes > 0 && variationOk;
      if (addToCartBtn) {
        addToCartBtn.disabled = !enabled;
        addToCartBtn.dataset.boxes = boxes;
      }
      // Checkout stays hidden until Add to Cart actually succeeds — a box
      // count alone is not a cart, and revealing it here sent the customer
      // to WooCommerce's "Your cart is empty" page.

      // Wastage banner.
      if (wastageBanner) {
        wastageBanner.classList.toggle('hidden', !addWastage);
        wastageBanner.classList.toggle('flex', addWastage);
      }
    }

    // Stepper buttons.
    const STEP = 1;
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
    if (areaInput) areaInput.addEventListener('input', compute);
    if (wastageCb) wastageCb.addEventListener('change', compute);
    recomputeCalculator = compute;

    // Add to Cart via WooCommerce AJAX.
    if (addToCartBtn) {
      addToCartBtn.addEventListener('click', function () {
        const boxes = parseInt(addToCartBtn.dataset.boxes, 10) || 0;
        const productId = addToCartBtn.dataset.productId;
        const nonce = addToCartBtn.dataset.nonce;
        if (!boxes || !productId) return;
        addToCartBtn.disabled = true;
        addToCartBtn.textContent = 'Adding…';
        const formData = new FormData();
        formData.append('action', 'lt_add_flooring_to_cart');
        formData.append('product_id', productId);
        formData.append('quantity', boxes);
        formData.append('nonce', nonce);
        formData.append('area_sqm', areaInput ? areaInput.value : 0);
        formData.append('install_quote', document.getElementById('lt-install-quote-input')?.checked ? '1' : '0');
        formData.append('variation_id', addToCartBtn.dataset.variationId || 0);
        if (requiresVariation) {
          const attrs = {};
          Object.keys(selectedAttributes).forEach(function (key) {
            attrs['attribute_' + key] = selectedAttributes[key];
          });
          formData.append('variation_attributes', JSON.stringify(attrs));
        }
        fetch(ltShopData.ajaxUrl, {
          method: 'POST',
          body: formData
        }).then(function (r) {
          return r.json();
        }).then(function (data) {
          if (data.success) {
            addToCartBtn.textContent = 'Added!';
            if (checkoutBtn) checkoutBtn.classList.remove('hidden');
            // Refresh WC cart fragments.
            document.body.dispatchEvent(new CustomEvent('wc_fragment_refresh'));
            setTimeout(function () {
              addToCartBtn.disabled = false;
              addToCartBtn.textContent = 'Add to Cart';
            }, 2000);
          } else {
            addToCartBtn.disabled = false;
            addToCartBtn.textContent = 'Add to Cart';
          }
        }).catch(function () {
          addToCartBtn.disabled = false;
          addToCartBtn.textContent = 'Add to Cart';
        });
      });
    }
    compute(); // Initial render.
  }

  /* ══════════════════════════════════════════════════════════
     3. INSTALLATION QUOTE OPT-IN
     A quote request, not a priced add-on — the checkbox deliberately has no
     price side effects. Its state is read straight off the input when the
     line is added to the cart, so there is nothing to wire up here.
  ══════════════════════════════════════════════════════════ */

  /* ══════════════════════════════════════════════════════════
     4. AREA CALCULATOR
     Extracted to the shared component in
     `src/global/js/components/area-calculator.js` (booted at the top of this
     file) with its markup in
     `template-parts/components/area-calculator.php`.
     The WooCommerce-only behaviour — "Use This Area" pushing the total into
     #lt-calc-area and scrolling to #lt-calculator — is declared as data
     attributes in `woocommerce/partials/area-calculator.php`.
  ══════════════════════════════════════════════════════════ */

  /* ══════════════════════════════════════════════════════════
     5. RELATED PRODUCTS SLIDER
  ══════════════════════════════════════════════════════════ */
  const relatedSlider = document.getElementById('lt-related-slider');
  if (relatedSlider) {
    new (swiper_bundle__WEBPACK_IMPORTED_MODULE_0___default())(relatedSlider, {
      speed: 400,
      spaceBetween: 20,
      slidesPerView: 1,
      breakpoints: {
        568: {
          slidesPerView: 2
        },
        992: {
          slidesPerView: 3
        },
        1200: {
          slidesPerView: 4
        }
      },
      navigation: {
        nextEl: '.lt-related-next',
        prevEl: '.lt-related-prev'
      }
    });
  }
})();
})();

/******/ })()
;
//# sourceMappingURL=product-single.js.map