/**
 * Project With Filter — Vanilla JS
 *
 * Bootstraps an independent ProjectsFilter instance for every
 * `.acf-block.lt-projects` on the page. Instances share no state, so multiple
 * blocks on one page work without conflicts.
 *
 * Behaviour:
 *   - Taxonomy tabs  → AJAX-replace the grid with the first N of that category.
 *   - "View All"     → AJAX-replace the grid with EVERY project in the active
 *                      category, then hide the button.
 *
 * AJAX URL is read from window.pwfData (falls back to bwfData, then the
 * standard admin-ajax path) so it is correct on sub-directory installs.
 *
 * @package Local Tasker
 * @since   1.0.0
 */
(function () {
  "use strict";

  class ProjectsFilter {
    /**
     * @param {HTMLElement} blockEl - Root <section> element for this block.
     */
    constructor(blockEl) {
      // ── Config from data attributes ──────────────────────────────────
      this.block = blockEl;
      this.nonce = blockEl.dataset.nonce || "";
      this.postsPerPage = parseInt(blockEl.dataset.postsPerPage, 10) || 3;
      this.ajaxUrl =
        window.pwfData?.ajaxUrl ??
        window.bwfData?.ajaxUrl ??
        "/wp-admin/admin-ajax.php";

      // ── Per-instance state ───────────────────────────────────────────
      this.currentCategory = 0; // 0 = all categories
      this.isLoading = false;

      // ── Scoped DOM references (always query INSIDE blockEl) ───────────
      this.grid = blockEl.querySelector(".project-lists");
      this.loadMoreWrap = blockEl.querySelector(".ld-btn-wrap");
      this.viewAllBtn = blockEl.querySelector(".project-load-more-btn");
      this.filterBtns = [...blockEl.querySelectorAll(".filter-btn")];

      // Guard: bail if the expected DOM isn't present.
      if (!this.grid) {
        console.warn("[ProjectsFilter] .project-lists not found.", blockEl);
        return;
      }

      this._bindEvents();
    }

    // ─── Event Binding ───────────────────────────────────────────────────

    _bindEvents() {
      // Category filter tabs.
      this.filterBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
          if (this.isLoading) return;

          const categoryId = parseInt(btn.dataset.category, 10) || 0;

          // No-op when clicking the already-active tab.
          if (categoryId === this.currentCategory) return;

          this._onFilterChange(btn, categoryId);
        });
      });

      // "View All Projects" button.
      this.viewAllBtn?.addEventListener("click", () => {
        if (this.isLoading) return;
        this._fetch({ viewAll: true });
      });
    }

    // ─── Actions ─────────────────────────────────────────────────────────

    /**
     * Handle a filter tab click: swap the active state and load page 1 of
     * the newly selected category.
     *
     * @param {HTMLButtonElement} activeBtn  The clicked button.
     * @param {number}            categoryId The lt_project_type term_id (0 = all).
     */
    _onFilterChange(activeBtn, categoryId) {
      this.filterBtns.forEach((btn) => {
        btn.classList.remove("active");
        btn.setAttribute("aria-pressed", "false");
      });
      activeBtn.classList.add("active");
      activeBtn.setAttribute("aria-pressed", "true");

      this.currentCategory = categoryId;
      this._fetch({ viewAll: false });
    }

    // ─── Data Fetching ────────────────────────────────────────────────────

    /**
     * Fetch projects via AJAX and replace the grid.
     *
     * @param {{viewAll: boolean}} opts
     * @returns {Promise<void>}
     */
    async _fetch({ viewAll }) {
      this.isLoading = true;
      this._setLoadingState(true, viewAll);

      const body = new URLSearchParams({
        action: "pwf_load_posts",
        nonce: this.nonce,
        term_id: this.currentCategory,
        posts_per_page: this.postsPerPage,
        view_all: viewAll ? 1 : 0,
      });

      try {
        const response = await fetch(this.ajaxUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
          },
          body: body.toString(),
        });

        if (!response.ok) {
          throw new Error(`Server responded with ${response.status}.`);
        }

        const json = await response.json();

        if (!json.success) {
          throw new Error(json.data?.message ?? "Unknown AJAX error.");
        }

        const { html, has_more } = json.data;

        // Both filter changes and "View All" replace the grid contents.
        this.grid.innerHTML = html;

        // "View All" always exhausts the list; otherwise defer to has_more.
        this.loadMoreWrap?.classList.toggle("hidden", viewAll || !has_more);
      } catch (err) {
        console.error("[ProjectsFilter] Fetch error:", err);
      } finally {
        this.isLoading = false;
        this._setLoadingState(false, viewAll);
      }
    }

    // ─── UI Helpers ───────────────────────────────────────────────────────

    /**
     * Toggle the loading UI — disables interactive controls and shows
     * feedback on the button while a request is in flight.
     *
     * @param {boolean} isLoading
     * @param {boolean} viewAll   Whether the in-flight request is a "View All".
     */
    _setLoadingState(isLoading, viewAll) {
      // Toggle the white veil + spinner overlay on the grid.
      this.block.classList.toggle("is-loading", isLoading);

      if (this.viewAllBtn) {
        this.viewAllBtn.disabled = isLoading;

        if (isLoading && viewAll) {
          this.viewAllBtn.dataset.originalText =
            this.viewAllBtn.textContent.trim();
          this.viewAllBtn.textContent = "Loading…";
        } else if (!isLoading) {
          this.viewAllBtn.textContent =
            this.viewAllBtn.dataset.originalText || "View All Projects";
        }
      }

      // Disable filter tabs mid-request to prevent race conditions.
      this.filterBtns.forEach((btn) => (btn.disabled = isLoading));
    }
  }

  // ─── Bootstrap ───────────────────────────────────────────────────────────

  function init() {
    document
      .querySelectorAll(".acf-block.lt-projects")
      .forEach((blockEl) => new ProjectsFilter(blockEl));
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
