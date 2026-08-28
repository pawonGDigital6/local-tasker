/**
 * Shop archive — AJAX filtering, sort, quick-pills, Load More + sidebar UX.
 *
 * Category pills vs. sidebar Category filter — independent, both apply (AND)
 * ─────────────────────────────────────────────────────────────────────────
 * • Category pills (spc-hybrid, engineered, porcelain, …) and the sidebar
 *   Category radio are independent controls: clicking a pill never checks a
 *   sidebar radio, and changing the sidebar radio never (de)activates a pill.
 * • Every request always sends BOTH signals when present: `filter=<pillKey>`
 *   for whichever pill is active, and `product_cat=<slug>` for whichever
 *   sidebar category is checked. The backend (lt_shop_build_query() /
 *   lt_shop_filter_product_query() in inc/woocommerce.php) resolves each into
 *   its own tax_query clause and combines them with the default AND relation
 *   — so picking a pill AND a sidebar category narrows to products matching
 *   both, exactly like any other pair of filter facets (colour + thickness,
 *   etc). No client-side "pick a winner" logic needed.
 * • Flag pills (on-sale, new-arrivals, in-stock) send filter= the same way;
 *   the backend applies them as their own, separate condition.
 *
 * Taxonomy archive page support
 * ──────────────────────────────
 * On category archive URLs (/product-category/…), PHP independently computes
 * both the active pill and the checked sidebar radio from the same queried
 * term — they naturally agree on first page load without any JS involved.
 *
 * Progressive enhancement: if ltShopFilter is missing, the script falls back
 * to plain GET form submission so the shop still works without JS.
 *
 * Vanilla JS, no framework.
 */
(function () {
	'use strict';

	/* ─────────────────────── element refs ─────────────────────── */

	var form     = document.getElementById('lt-filter-form');
	var sidebar  = document.getElementById('lt-filters-sidebar');
	var overlay  = document.getElementById('lt-filter-overlay');
	var btnOpen  = document.getElementById('lt-filter-open');
	var btnClose = document.getElementById('lt-filter-close');
	var sortSel  = document.getElementById('lt-sort-select');

	var grid        = document.querySelector('.lt-products-grid');
	var countEl     = document.querySelector('[data-lt-result-count]');
	var rangeCount  = document.querySelector('.count-result .count');
	var rangeTotal  = document.querySelector('.count-result .total');
	var loadWrap    = document.querySelector('[data-lt-loadmore]');
	var loadBtn     = document.querySelector('[data-lt-loadmore-btn]');
	var pillsNav    = document.querySelector('[data-lt-archive-cat]'); // pills <nav>
	var pills       = Array.prototype.slice.call(document.querySelectorAll('[data-lt-pill]'));
	var clearLink   = document.getElementById('lt-clear-filters');
	var clearPill   = document.getElementById('lt-clear-pill');

	var AJAX = (typeof window.ltShopFilter !== 'undefined') && grid;
	var state = {
		page: loadWrap ? parseInt(loadWrap.dataset.page, 10) || 1 : 1,
		loading: false
	};

	var FLAG_PILLS = { 'on-sale': true, 'new-arrivals': true, 'in-stock': true };

	/* ─────────────────────── helpers ───────────────────────── */

	function debounce(fn, wait) {
		var t;
		return function () {
			var ctx = this, a = arguments;
			clearTimeout(t);
			t = setTimeout(function () { fn.apply(ctx, a); }, wait);
		};
	}

	function stripEmptyPriceFields() {
		['lt-price-min', 'lt-price-max'].forEach(function (id) {
			var el = document.getElementById(id);
			if (el && el.value === '') el.removeAttribute('name');
			else if (el && !el.hasAttribute('name')) el.setAttribute('name', id === 'lt-price-min' ? 'min_price' : 'max_price');
		});
	}

	/** Return the currently active pill key ('all' if none). */
	function activePillKey() {
		var el = pills.find(function (p) { return p.classList.contains('is-active') || p.getAttribute('aria-current') === 'true'; });
		return el ? el.dataset.ltPill : 'all';
	}

	// Attribute facets rendered by lt_render_filter_facet() in
	// woocommerce/partials/shop-filters.php. Adding a facet there only needs its
	// request key appended here for AJAX filtering and clear-state to follow.
	var ATTR_FACETS = ['filter_colour', 'filter_thickness', 'filter_grade', 'filter_veneer'];

	/** Any attribute facet checkbox currently ticked? */
	function hasCheckedFacet() {
		if (!form) return false;
		return ATTR_FACETS.some(function (name) {
			return !!form.querySelector('[name="' + name + '[]"]:checked');
		});
	}

	/** Show/hide both "Clear All Filters" controls based on the live filter state. */
	function refreshClearVisibility() {
		var checkedCat = form && form.querySelector('[name="product_cat"]:checked');
		var min = document.getElementById('lt-price-min');
		var max = document.getElementById('lt-price-max');
		var pill = activePillKey();
		var checkedAvail = form && form.querySelector('[name="filter_availability"]:checked');

		var hasActive = !!(
			(checkedCat && checkedCat.value) ||
			pill !== 'all' ||
			hasCheckedFacet() ||
			(min && min.value !== '') ||
			(max && max.value !== '') ||
			(checkedAvail && checkedAvail.value)
		);

		if (clearLink) clearLink.classList.toggle('hidden', !hasActive);
		if (clearPill) clearPill.classList.toggle('hidden', !hasActive);
	}

	/** Collect the current filter state into URLSearchParams for AJAX. */
	function collectParams() {
		var params = new URLSearchParams();
		var pill = activePillKey();

		// Pill and sidebar category are independent — send both whenever present.
		// The backend ANDs them together with everything else (see file header).
		if (pill !== 'all') {
			params.set('filter', pill);
		}
		var checkedCat = form && form.querySelector('[name="product_cat"]:checked');
		if (checkedCat && checkedCat.value) {
			params.set('product_cat', checkedCat.value);
		}

		// Attribute facet checkboxes (colour, thickness, grade, veneer).
		if (form) {
			ATTR_FACETS.forEach(function (name) {
				form.querySelectorAll('[name="' + name + '[]"]:checked').forEach(function (c) {
					params.append(name + '[]', c.value);
				});
			});
		}

		var min = document.getElementById('lt-price-min');
		var max = document.getElementById('lt-price-max');
		if (min && min.value !== '') params.set('min_price', min.value);
		if (max && max.value !== '') params.set('max_price', max.value);

		if (sortSel && sortSel.value) params.set('orderby', sortSel.value);

		// Availability radio (sidebar).
		var checkedAvail = form && form.querySelector('[name="filter_availability"]:checked');
		if (checkedAvail && checkedAvail.value) {
			params.set('filter_availability', checkedAvail.value);
		}

		return params;
	}

	/**
	 * Activate a pill by key, updating aria-current and is-active classes.
	 * Does NOT trigger AJAX — callers do that. Purely visual; does not touch
	 * the sidebar Category radios (the two controls are independent).
	 */
	function activatePill(key) {
		pills.forEach(function (p) {
			var match = p.dataset.ltPill === key;
			p.classList.toggle('is-active', match);
			p.setAttribute('aria-current', match ? 'true' : 'false');
		});
	}

	/**
	 * Write the server-computed availability counts into the sidebar.
	 *
	 * The payload is authoritative — it comes from the same query that produced
	 * the grid — so a missing/!malformed value leaves the existing number alone
	 * rather than showing a guess.
	 *
	 * @param {{in_stock:number, out_of_stock:number}|undefined} counts
	 */
	function updateAvailabilityCounts(counts) {
		if (!counts) return;
		Object.keys(counts).forEach(function (key) {
			var value = counts[key];
			if (typeof value === 'undefined' || value === null) return;
			document
				.querySelectorAll('[data-lt-avail-count="' + key + '"]')
				.forEach(function (el) { el.textContent = value; });
		});
	}

	/**
	 * Apply the server's per-option counts to the sidebar.
	 *
	 * The payload answers "how many products would this option return, given
	 * everything else that is currently selected" — computed by
	 * lt_shop_facet_counts() from the same query that built the grid. An option
	 * worth nothing is dimmed and disabled so a dead-end combination such as
	 * Accessories + Beige simply can't be reached.
	 *
	 * Two options are never locked: one the user already ticked (they must be able
	 * to untick it) and the "All" reset (data-all-cats), which is the way back out.
	 *
	 * @param {Object.<string, Object.<string, number>>|undefined} facets
	 */
	function applyFacetCounts(facets) {
		if (!facets || !form) return;

		Object.keys(facets).forEach(function (key) {
			var counts = facets[key];
			if (!counts) return;

			// product_cat is a radio group; the attribute facets are checkbox arrays.
			var selector = key === 'product_cat' ? '[name="product_cat"]' : '[name="' + key + '[]"]';

			form.querySelectorAll(selector).forEach(function (input) {
				if (!Object.prototype.hasOwnProperty.call(counts, input.value)) return;

				var n = counts[input.value];
				var option = input.closest('[data-lt-filter-option]');
				var countEl = option && option.querySelector('[data-lt-facet-count]');
				if (countEl) countEl.textContent = n;

				var lock = n === 0 && !input.checked && !input.hasAttribute('data-all-cats');
				input.disabled = lock;
				if (option) option.classList.toggle('is-disabled', lock);
			});
		});
	}

	/* ──────────────── category sub-category branches ──────────────── */

	/** Open or close one parent category's sub-category list. */
	function setBranchOpen(branch, open) {
		if (!branch) return;
		var btn = branch.querySelector('[data-lt-cat-toggle]');
		var panel = btn && document.getElementById(btn.getAttribute('aria-controls'));
		if (btn) btn.setAttribute('aria-expanded', String(open));
		if (panel) panel.setAttribute('data-open', String(open));
	}

	/** Selecting a category reveals its children, so the next choice down is in view. */
	function revealBranchFor(input) {
		if (!input) return;
		setBranchOpen(input.closest('[data-lt-cat-branch]'), true);
	}

	/* ───────────────────── AJAX fetch + render ───────────────────── */

	function fetchProducts(append) {
		if (state.loading) return;
		state.loading = true;

		if (!append) {
			state.page = 1;
			grid.classList.add('is-loading');
		} else if (loadBtn) {
			loadBtn.classList.add('is-loading');
			loadBtn.setAttribute('disabled', 'disabled');
		}

		var params = collectParams();
		var pageToLoad = append ? state.page + 1 : 1;

		var body = new URLSearchParams(params.toString());
		body.set('action', 'lt_shop_load');
		body.set('nonce', window.ltShopFilter.nonce);
		body.set('paged', pageToLoad);
		body.set('per_page', (loadWrap && loadWrap.dataset.perPage) || window.ltShopFilter.perPage || 9);

		// Keep the address bar in sync (page 1 state only).
		if (!append) {
			var url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
			window.history.replaceState(null, '', url);
		}

		fetch(window.ltShopFilter.ajaxUrl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
			credentials: 'same-origin'
		})
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (!res || !res.success) throw new Error('bad response');
				var d = res.data;

				if (append) {
					grid.insertAdjacentHTML('beforeend', d.html);
					state.page = d.page;
				} else {
					grid.innerHTML = d.html || emptyMarkup();
				}

				if (countEl) {
					countEl.textContent = d.found + ' ' + (d.found === 1 ? 'result' : 'results');
				}
				// "Showing X-Y of Z" — Y = cards currently in the grid, Z = total.
				if (rangeCount || rangeTotal) {
					var shown = grid.querySelectorAll('li.product').length;
					if (rangeCount) rangeCount.textContent = shown > 0 ? '1-' + shown : '0';
					if (rangeTotal) rangeTotal.textContent = d.found;
				}
				if (loadWrap) {
					loadWrap.dataset.page = d.page;
					loadWrap.classList.toggle('is-hidden', !d.has_more);
				}

				// Availability counts — recomputed server-side against the same
				// filter set as the grid, so they stay in step with the results.
				updateAvailabilityCounts(d.availability);

				// Per-option counts + dead-end locking, from the same query.
				// Appending a page doesn't change the filter set, so leave the
				// sidebar untouched on Load More.
				if (!append) applyFacetCounts(d.facets);

				// Re-init WooCommerce AJAX add-to-cart on new nodes.
				if (window.jQuery && window.jQuery.fn.wc_setup_ajax_add_to_cart) {
					window.jQuery(document.body).trigger('wc_fragment_refresh');
				}
			})
			.catch(function () {
				if (!append) grid.innerHTML = emptyMarkup(true);
			})
			.finally(function () {
				state.loading = false;
				grid.classList.remove('is-loading');
				if (loadBtn) {
					loadBtn.classList.remove('is-loading');
					loadBtn.removeAttribute('disabled');
				}
			});
	}

	function emptyMarkup(error) {
		return '<li class="lt-shop-archive__empty py-16 text-center" style="grid-column:1/-1;list-style:none">' +
			'<p class="text-body-lg text-lt-text-muted">' +
			(error ? 'Something went wrong. Please try again.' : 'No products match your filters.') +
			'</p></li>';
	}

	/* ───────────────────────── bindings ───────────────────────── */

	if (form) {
		// Category radios + attribute facet checkboxes.
		form.querySelectorAll('.lt-filter-checkbox').forEach(function (cb) {
			cb.addEventListener('change', function () {
				if (cb.name === 'product_cat') revealBranchFor(cb);
				refreshClearVisibility();
				if (AJAX) fetchProducts(false); else submitFallback();
			});
		});

		// Sub-category disclosure arrows. type="button" keeps them out of submit,
		// and they sit outside the <label> so toggling never selects the category.
		form.querySelectorAll('[data-lt-cat-toggle]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				setBranchOpen(btn.closest('[data-lt-cat-branch]'), btn.getAttribute('aria-expanded') !== 'true');
			});
		});

		// Price inputs.
		var priceHandler = debounce(function () { if (AJAX) fetchProducts(false); else submitFallback(); }, 500);
		['lt-price-min', 'lt-price-max'].forEach(function (id) {
			var el = document.getElementById(id);
			if (el) el.addEventListener('input', function () {
				refreshClearVisibility();
				priceHandler();
			});
		});

		form.addEventListener('submit', function (e) {
			if (AJAX) { e.preventDefault(); fetchProducts(false); }
			else stripEmptyPriceFields();
		});
	}

	// Sort dropdown.
	if (sortSel) {
		sortSel.addEventListener('change', function () {
			if (AJAX) {
				fetchProducts(false);
			} else {
				var url = new URL(window.location.href);
				url.searchParams.set('orderby', sortSel.value);
				url.searchParams.delete('paged');
				window.location.href = url.toString();
			}
		});
	}

	// Quick-filter pills.
	pills.forEach(function (pill) {
		pill.addEventListener('click', function (e) {
			if (!AJAX) return; // let the link navigate
			e.preventDefault();
			var pillKey = pill.dataset.ltPill;
			activatePill(pillKey);
			refreshClearVisibility();
			fetchProducts(false);
		});
	});

	// Clear all filters.
	[clearLink, clearPill].forEach(function (el) {
		if (el && AJAX) {
			el.addEventListener('click', function (e) {
				e.preventDefault();
				// Reset all checkboxes (colour, thickness).
				if (form) {
					form.querySelectorAll('input[type="checkbox"]').forEach(function (c) { c.checked = false; });
					// Reset category radio to "All Flooring".
					var allCatEl = form.querySelector('[data-all-cats]');
					if (allCatEl) allCatEl.checked = true;
					// Clear price inputs.
					['lt-price-min', 'lt-price-max'].forEach(function (id) {
						var input = document.getElementById(id); if (input) input.value = '';
					});
					// Reset availability radios.
					form.querySelectorAll('[name="filter_availability"]').forEach(function (r) { r.checked = false; });
				}
				// Reset pills to "All".
				activatePill('all');
				if (sortSel) sortSel.value = 'menu_order';
				refreshClearVisibility();
				fetchProducts(false);
			});
		}
	});

	// Load More.
	if (loadBtn && AJAX) {
		loadBtn.addEventListener('click', function () { fetchProducts(true); });
	}

	function submitFallback() {
		stripEmptyPriceFields();
		form.submit();
	}

	/* ─────────────── sidebar accordion + mobile drawer ─────────────── */

	document.querySelectorAll('.lt-filter-group__toggle').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var expanded = btn.getAttribute('aria-expanded') === 'true';
			btn.setAttribute('aria-expanded', String(!expanded));
			var body = document.getElementById(btn.getAttribute('aria-controls'));
			if (body) { body.hidden = expanded; body.style.display = expanded ? 'none' : ''; }
		});
	});

	function openSidebar() {
		if (!sidebar) return;
		sidebar.classList.remove('max-md:-translate-x-full');
		sidebar.classList.add('max-md:translate-x-0');
		if (overlay) overlay.classList.remove('hidden');
		document.body.style.overflow = 'hidden';
		if (btnOpen) btnOpen.setAttribute('aria-expanded', 'true');
	}
	function closeSidebar() {
		if (!sidebar) return;
		sidebar.classList.add('max-md:-translate-x-full');
		sidebar.classList.remove('max-md:translate-x-0');
		if (overlay) overlay.classList.add('hidden');
		document.body.style.overflow = '';
		if (btnOpen) { btnOpen.setAttribute('aria-expanded', 'false'); btnOpen.focus(); }
	}
	if (btnOpen)  btnOpen.addEventListener('click', openSidebar);
	if (btnClose) btnClose.addEventListener('click', closeSidebar);
	if (overlay)  overlay.addEventListener('click', closeSidebar);
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && overlay && !overlay.classList.contains('hidden')) closeSidebar();
	});

	// No initial pill/sidebar reconciliation needed: PHP computes both the
	// active pill and the checked sidebar radio independently from the same
	// queried term/GET params, so they already agree without any JS involved.

})();