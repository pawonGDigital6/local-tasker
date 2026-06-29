/**
 * Blog With Filter — Vanilla JS
 *
 * Finds every `.acf-block.lt-blogs-filter` element on the page and
 * bootstraps an independent BlogsFilter instance for each one.
 * Instances share no state, so multiple blocks on the same page work
 * without any conflicts.
 *
 * Depends on:
 *   window.bwfData.ajaxUrl  — injected by wp_localize_script() in functions.php
 *
 * @package YourTheme
 * @since   1.0.0
 */
(function () {
  "use strict";

  // ─── Class ───────────────────────────────────────────────────────────────

  /**
   * Manages one instance of the Blog With Filter block.
   */
  class BlogsFilter {
    /**
     * @param {HTMLElement} blockEl - Root <section> element for this block.
     */
    constructor(blockEl) {
      // ── Config from data attributes ──────────────────────────────────
      this.block = blockEl;
      this.nonce = blockEl.dataset.nonce || "";
      this.postsPerPage = parseInt(blockEl.dataset.postsPerPage, 10) || 6;
      this.ajaxUrl = window.bwfData?.ajaxUrl ?? "/wp-admin/admin-ajax.php";

      // ── Per-instance state ───────────────────────────────────────────
      // Tracks what the grid is currently showing so "Load More" always
      // fetches the correct next page for the active filter.
      this.currentCategory = 0; // 0 = no filter (all categories)
      this.currentPage = 1;
      this.isLoading = false;

      // ── Scoped DOM references ────────────────────────────────────────
      // All selectors query INSIDE blockEl only, never document-level.
      this.grid = blockEl.querySelector(".blog-lists");
      this.loadMoreWrap = blockEl.querySelector(".ld-btn-wrap");
      this.loadMoreBtn = blockEl.querySelector(".blog-load-more-btn");
      this.filterBtns = [...blockEl.querySelectorAll(".filter-btn")];

      // Guard: bail if the expected DOM isn't present.
      if (!this.grid) {
        console.warn("[BlogsFilter] .blog-lists not found.", blockEl);
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

          const categoryId = parseInt(btn.dataset.category, 10);

          // No-op when clicking the already-active tab.
          if (categoryId === this.currentCategory) return;

          this._onFilterChange(btn, categoryId);
        });
      });

      // Load More button.
      this.loadMoreBtn?.addEventListener("click", () => {
        if (this.isLoading) return;
        this._onLoadMore();
      });
    }

    // ─── Actions ─────────────────────────────────────────────────────────

    /**
     * Handle a filter tab click.
     * Resets pagination and replaces the grid with page 1 of the new filter.
     *
     * @param {HTMLButtonElement} activeBtn  The clicked button element.
     * @param {number}            categoryId The category term_id (0 = all).
     */
    _onFilterChange(activeBtn, categoryId) {
      // Update visual + ARIA active state across all tabs.
      this.filterBtns.forEach((btn) => {
        btn.classList.remove("active");
        btn.setAttribute("aria-pressed", "false");
      });
      activeBtn.classList.add("active");
      activeBtn.setAttribute("aria-pressed", "true");

      // Reset pagination state before fetching.
      this.currentCategory = categoryId;
      this.currentPage = 1;

      // Replace grid contents with the first page of the new filter.
      this._fetchPosts(/* replace: */ true);
    }

    /**
     * Handle Load More click.
     * Advances the page counter and appends posts for the active filter.
     */
    _onLoadMore() {
      this.currentPage++;
      this._fetchPosts(/* replace: */ false);
    }

    // ─── Data Fetching ────────────────────────────────────────────────────

    /**
     * Fetch posts from the server via AJAX.
     *
     * @param {boolean} replace  true  → replace grid innerHTML (filter change).
     *                           false → append to grid (load more).
     * @returns {Promise<void>}
     */
    async _fetchPosts(replace) {
      this.isLoading = true;
      this._setLoadingState(true);

      const body = new URLSearchParams({
        action: "bwf_load_posts",
        nonce: this.nonce,
        category_id: this.currentCategory,
        page: this.currentPage,
        posts_per_page: this.postsPerPage,
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

        // Inject rendered card HTML.
        if (replace) {
          this.grid.innerHTML = html;
        } else {
          this.grid.insertAdjacentHTML("beforeend", html);
        }

        // Show or hide Load More based on whether a next page exists.
        this.loadMoreWrap?.classList.toggle("hidden", !has_more);
      } catch (err) {
        console.error("[BlogsFilter] Fetch error:", err);

        // Roll back the page counter so a retry is for the correct page.
        if (!replace) {
          this.currentPage = Math.max(1, this.currentPage - 1);
        }
      } finally {
        this.isLoading = false;
        this._setLoadingState(false);
      }
    }

    // ─── UI Helpers ───────────────────────────────────────────────────────

    /**
     * Toggle the loading UI — disables interactive controls and shows
     * feedback text on the Load More button.
     *
     * @param {boolean} isLoading
     */
    _setLoadingState(isLoading) {
      // Load More button feedback.
      if (this.loadMoreBtn) {
        this.loadMoreBtn.disabled = isLoading;

        if (isLoading) {
          // Preserve original label so we can restore it exactly.
          this.loadMoreBtn.dataset.originalText =
            this.loadMoreBtn.textContent.trim();
          this.loadMoreBtn.textContent = "Loading\u2026"; // "Loading…"
        } else {
          this.loadMoreBtn.textContent =
            this.loadMoreBtn.dataset.originalText || "Load More Articles";
        }
      }

      // Disable filter tabs mid-request to prevent race conditions.
      this.filterBtns.forEach((btn) => (btn.disabled = isLoading));
    }
  }

  // ─── Bootstrap ───────────────────────────────────────────────────────────

  /**
   * Initialise a BlogsFilter instance for each block on the page.
   * Runs after the DOM is ready regardless of how the script is loaded.
   */
  function init() {
    document
      .querySelectorAll(".acf-block.lt-blogs-filter")
      .forEach((blockEl) => new BlogsFilter(blockEl));
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    // Script loaded with `defer` or after DOM is ready — init immediately.
    init();
  }
})();
