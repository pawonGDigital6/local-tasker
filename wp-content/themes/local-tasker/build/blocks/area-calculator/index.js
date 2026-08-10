/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/blocks/area-calculator/index.js"
/*!*********************************************!*\
  !*** ./src/blocks/area-calculator/index.js ***!
  \*********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./editor.scss */ "./src/blocks/area-calculator/editor.scss");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/blocks/area-calculator/style.scss");
/* harmony import */ var _global_js_components_area_calculator__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../global/js/components/area-calculator */ "./src/global/js/components/area-calculator.js");




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
    (0,_global_js_components_area_calculator__WEBPACK_IMPORTED_MODULE_2__.initAreaCalculators)();
  };
  const schedule = () => {
    if (scheduled) return;
    scheduled = true;
    window.requestAnimationFrame(boot);
  };
  window.addEventListener('DOMContentLoaded', schedule);
  new MutationObserver(schedule).observe(document.documentElement, {
    childList: true,
    subtree: true
  });
}

/***/ },

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
    if (this.totalEl) this.totalEl.textContent = total.toFixed(2) + ' sqm';
    if (this.wastageEl) this.wastageEl.textContent = (total * this.wastageFactor).toFixed(2) + ' sqm';
    this.root.dataset.totalArea = total;
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

/***/ "./src/blocks/area-calculator/editor.scss"
/*!************************************************!*\
  !*** ./src/blocks/area-calculator/editor.scss ***!
  \************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "./src/blocks/area-calculator/style.scss"
/*!***********************************************!*\
  !*** ./src/blocks/area-calculator/style.scss ***!
  \***********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


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
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		const deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			let notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				let [chunkIds, fn, priority] = deferred[i];
/******/ 				let fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					const r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
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
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		const installedChunks = {
/******/ 			"area-calculator/index": 0,
/******/ 			"area-calculator/style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		const webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			let [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		const chunkLoadingGlobal = globalThis["webpackChunkacf_first_block"] ||= [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	let __webpack_exports__ = __webpack_require__.O(undefined, ["area-calculator/style-index"], () => (__webpack_require__("./src/blocks/area-calculator/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map