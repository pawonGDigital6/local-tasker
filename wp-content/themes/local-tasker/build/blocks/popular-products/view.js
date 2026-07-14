/******/ (() => { // webpackBootstrap
/*!*********************************************!*\
  !*** ./src/blocks/popular-products/view.js ***!
  \*********************************************/
/**
 * Popular Products — Vanilla JS
 *
 * Bootstraps an independent PopularProductsFilter instance for every
 * `.acf-block.lt-popular-products` on the page. Instances share no state, so
 * multiple blocks on one page work without conflicts.
 *
 * Behaviour:
 *   - Category tabs → AJAX-replace the grid with the first N products of
 *                      that `product_cat` (empty slug = all categories).
 *
 * AJAX URL is read from window.ppfData (falls back to the standard
 * admin-ajax path) so it is correct on sub-directory installs.
 *
 * @package Local Tasker
 * @since   1.0.0
 */
(function () {
  "use strict";

  class PopularProductsFilter {
    /**
     * @param {HTMLElement} blockEl - Root <section> element for this block.
     */
    constructor(blockEl) {
      // ── Config from data attributes ──────────────────────────────────
      this.block = blockEl;
      this.nonce = blockEl.dataset.nonce || "";
      this.postsPerPage = parseInt(blockEl.dataset.postsPerPage, 10) || 8;
      this.ajaxUrl = window.ppfData?.ajaxUrl ?? "/wp-admin/admin-ajax.php";

      // ── Per-instance state ───────────────────────────────────────────
      this.currentCategory = ""; // "" = all categories
      this.isLoading = false;

      // ── Scoped DOM references (always query INSIDE blockEl) ───────────
      this.grid = blockEl.querySelector(".popular-products-list");
      this.filterBtns = [...blockEl.querySelectorAll(".filter-btn")];

      // Guard: bail if the expected DOM isn't present.
      if (!this.grid) {
        console.warn("[PopularProductsFilter] .popular-products-list not found.", blockEl);
        return;
      }
      this._bindEvents();
    }

    // ─── Event Binding ───────────────────────────────────────────────────

    _bindEvents() {
      // Category filter tabs.
      this.filterBtns.forEach(btn => {
        btn.addEventListener("click", () => {
          if (this.isLoading) return;
          const category = btn.dataset.category || "";

          // No-op when clicking the already-active tab.
          if (category === this.currentCategory) return;
          this._onFilterChange(btn, category);
        });
      });
    }

    // ─── Actions ─────────────────────────────────────────────────────────

    /**
     * Handle a filter tab click: swap the active state and load the newly
     * selected category.
     *
     * @param {HTMLButtonElement} activeBtn The clicked button.
     * @param {string}            category  The product_cat slug ("" = all).
     */
    _onFilterChange(activeBtn, category) {
      this.filterBtns.forEach(btn => {
        btn.classList.remove("active");
        btn.setAttribute("aria-pressed", "false");
      });
      activeBtn.classList.add("active");
      activeBtn.setAttribute("aria-pressed", "true");
      this.currentCategory = category;
      this._fetch();
    }

    // ─── Data Fetching ────────────────────────────────────────────────────

    /**
     * Fetch products via AJAX and replace the grid.
     *
     * @returns {Promise<void>}
     */
    async _fetch() {
      this.isLoading = true;
      this._setLoadingState(true);
      const body = new URLSearchParams({
        action: "ppf_load_products",
        nonce: this.nonce,
        category: this.currentCategory,
        posts_per_page: this.postsPerPage
      });
      try {
        const response = await fetch(this.ajaxUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
          },
          body: body.toString()
        });
        if (!response.ok) {
          throw new Error(`Server responded with ${response.status}.`);
        }
        const json = await response.json();
        if (!json.success) {
          throw new Error(json.data?.message ?? "Unknown AJAX error.");
        }
        this.grid.innerHTML = json.data.html;
      } catch (err) {
        console.error("[PopularProductsFilter] Fetch error:", err);
      } finally {
        this.isLoading = false;
        this._setLoadingState(false);
      }
    }

    // ─── UI Helpers ───────────────────────────────────────────────────────

    /**
     * Toggle the loading UI — disables interactive controls while a request
     * is in flight.
     *
     * @param {boolean} isLoading
     */
    _setLoadingState(isLoading) {
      // Toggle the white veil + spinner overlay on the grid.
      this.block.classList.toggle("is-loading", isLoading);

      // Disable filter tabs mid-request to prevent race conditions.
      this.filterBtns.forEach(btn => btn.disabled = isLoading);
    }
  }

  // ─── Bootstrap ───────────────────────────────────────────────────────────

  function init() {
    document.querySelectorAll(".acf-block.lt-popular-products").forEach(blockEl => new PopularProductsFilter(blockEl));
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
/******/ })()
;
//# sourceMappingURL=view.js.map