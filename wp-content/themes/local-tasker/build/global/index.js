/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/global/js/components/navigation.js"
/*!************************************************!*\
  !*** ./src/global/js/components/navigation.js ***!
  \************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

const navigation = {
  // Initialize the navigation
  init() {
    this.cacheDOM();
    this.bindEvents();
  },
  // Cache the DOM elements
  cacheDOM() {
    this.body = document.body;
    this.primaryMenu = document.querySelector('.main-navigation');

    // Fallbacks to avoid null errors
    this.hamnBurger = document.querySelector('.menu-hamn-burger');
    this.menuToggle = this.hamnBurger;
    this.menuCloser = document.querySelector('.menu-close-btn');
    this.siteOverlay = document.querySelector('.site-overlay');
    if (this.primaryMenu) {
      this.navItems = this.primaryMenu.querySelectorAll('.menu-item-has-children');
    } else {
      this.navItems = [];
    }
  },
  // Bind events
  bindEvents() {
    if (this.menuToggle) {
      this.menuToggle.addEventListener('click', this.openMenu.bind(this));
    }
    if (this.menuCloser) {
      this.menuCloser.addEventListener('click', this.closeMenu.bind(this));
    }
    if (this.siteOverlay) {
      this.siteOverlay.addEventListener('click', this.closeMenu.bind(this));
    }
    this.addIconHasChildren();
    this.subMenuSlideToggle();
  },
  // Toggle menu
  openMenu() {
    this.body.classList.add('menu-open');
  },
  closeMenu() {
    this.body.classList.remove('menu-open');
  },
  // Add icon to menu items with children
  addIconHasChildren() {
    this.navItems.forEach(item => {
      // Prevent adding multiple icons if function runs twice
      if (item.querySelector('.icon')) return;
      const icon = document.createElement('span');
      icon.classList.add('icon');
      icon.innerHTML = '<span></span><span></span>';
      item.appendChild(icon);
    });
  },
  subMenuSlideToggle() {
    this.navItems.forEach(item => {
      const menuHasChildIcon = item.querySelector('.icon');
      if (menuHasChildIcon) {
        menuHasChildIcon.addEventListener('click', function (e) {
          e.preventDefault(); // Prevents accidental page jumps

          const $parentItem = jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).parent('.menu-item-has-children');
          const $subMenu = $parentItem.find('.sub-menu');

          // Toggle current submenu while closing others
          $subMenu.slideToggle();
          jquery__WEBPACK_IMPORTED_MODULE_0___default()('.main-navigation .sub-menu').not($subMenu).slideUp();

          // Toggle active class on icon while resetting others
          jquery__WEBPACK_IMPORTED_MODULE_0___default()(this).toggleClass('active');
          jquery__WEBPACK_IMPORTED_MODULE_0___default()('.main-navigation .icon').not(jquery__WEBPACK_IMPORTED_MODULE_0___default()(this)).removeClass('active');
        });
      }
    });
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (navigation);

/***/ },

/***/ "./src/global/js/main.js"
/*!*******************************!*\
  !*** ./src/global/js/main.js ***!
  \*******************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/dom-ready */ "@wordpress/dom-ready");
/* harmony import */ var _wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_navigation__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/navigation */ "./src/global/js/components/navigation.js");


_wordpress_dom_ready__WEBPACK_IMPORTED_MODULE_0___default()(() => {
  _components_navigation__WEBPACK_IMPORTED_MODULE_1__["default"].init();
});

/***/ },

/***/ "./src/global/tailwind/tailwind.css"
/*!******************************************!*\
  !*** ./src/global/tailwind/tailwind.css ***!
  \******************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "./src/global/scss/main.scss"
/*!***********************************!*\
  !*** ./src/global/scss/main.scss ***!
  \***********************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "jquery"
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
(module) {

module.exports = window["jQuery"];

/***/ },

/***/ "@wordpress/dom-ready"
/*!**********************************!*\
  !*** external ["wp","domReady"] ***!
  \**********************************/
(module) {

module.exports = window["wp"]["domReady"];

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
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
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*****************************!*\
  !*** ./src/global/index.js ***!
  \*****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _js_main__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./js/main */ "./src/global/js/main.js");
/* harmony import */ var _scss_main_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./scss/main.scss */ "./src/global/scss/main.scss");
/* harmony import */ var _tailwind_tailwind_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./tailwind/tailwind.css */ "./src/global/tailwind/tailwind.css");



})();

/******/ })()
;
//# sourceMappingURL=index.js.map