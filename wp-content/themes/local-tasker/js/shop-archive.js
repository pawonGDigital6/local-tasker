/**
 * Shop archive — AJAX filtering, sort, quick-pills, Load More + sidebar UX.
 *
 * Progressive enhancement: if the localized ltShopFilter object is missing, the
 * script falls back to plain GET form submission (full page reload) so the shop
 * still works. With AJAX available, filter/sort/pill changes swap the grid in
 * place and "Load More" appends the next page.
 *
 * Vanilla JS, no framework.
 */
(function () {
	'use strict';

	var form     = document.getElementById('lt-filter-form');
	var sidebar  = document.getElementById('lt-filters-sidebar');
	var overlay  = document.getElementById('lt-filter-overlay');
	var btnOpen  = document.getElementById('lt-filter-open');
	var btnClose = document.getElementById('lt-filter-close');
	var sortSel  = document.getElementById('lt-sort-select');

	var grid        = document.querySelector('.lt-products-grid');
	var countEl     = document.querySelector('[data-lt-result-count]');
	var loadWrap    = document.querySelector('[data-lt-loadmore]');
	var loadBtn     = document.querySelector('[data-lt-loadmore-btn]');
	var pills       = Array.prototype.slice.call(document.querySelectorAll('[data-lt-pill]'));
	var clearLink   = document.querySelector('.lt-shop-filters .text-lt-accent');

	var AJAX = (typeof window.ltShopFilter !== 'undefined') && grid;
	var state = {
		page: loadWrap ? parseInt(loadWrap.dataset.page, 10) || 1 : 1,
		loading: false
	};

	/* ───────────────────────── helpers ───────────────────────── */

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

	function activePill() {
		var el = pills.find(function (p) { return p.classList.contains('is-active') || p.getAttribute('aria-current') === 'true'; });
		return el ? el.dataset.ltPill : 'all';
	}

	/** Collect the current filter state into URLSearchParams. */
	function collectParams() {
		var params = new URLSearchParams();

		var checkedCat = form.querySelector('[name="product_cat"]:checked');
		if (checkedCat && checkedCat.value) params.set('product_cat', checkedCat.value);
		form.querySelectorAll('[name="filter_colour[]"]:checked').forEach(function (c) {
			params.append('filter_colour[]', c.value);
		});
		form.querySelectorAll('[name="filter_thickness[]"]:checked').forEach(function (c) {
			params.append('filter_thickness[]', c.value);
		});

		var min = document.getElementById('lt-price-min');
		var max = document.getElementById('lt-price-max');
		if (min && min.value !== '') params.set('min_price', min.value);
		if (max && max.value !== '') params.set('max_price', max.value);

		var pill = activePill();
		if (pill && pill !== 'all') params.set('filter', pill);

		if (sortSel && sortSel.value) params.set('orderby', sortSel.value);

		return params;
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
				if (loadWrap) {
					loadWrap.dataset.page = d.page;
					loadWrap.classList.toggle('is-hidden', !d.has_more);
				}

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
		// Category (radio) / colour / thickness (checkboxes).
		form.querySelectorAll('.lt-filter-checkbox').forEach(function (cb) {
			cb.addEventListener('change', function () {
				if (AJAX) fetchProducts(false); else submitFallback();
			});
		});

		// Price inputs.
		var priceHandler = debounce(function () { if (AJAX) fetchProducts(false); else submitFallback(); }, 500);
		['lt-price-min', 'lt-price-max'].forEach(function (id) {
			var el = document.getElementById(id);
			if (el) el.addEventListener('input', priceHandler);
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
			pills.forEach(function (p) { p.classList.remove('is-active'); p.setAttribute('aria-current', 'false'); });
			pill.classList.add('is-active');
			pill.setAttribute('aria-current', 'true');
			fetchProducts(false);
		});
	});

	// Clear all filters.
	if (clearLink && AJAX) {
		clearLink.addEventListener('click', function (e) {
			e.preventDefault();
			form.querySelectorAll('input[type="checkbox"]').forEach(function (c) { c.checked = false; });
			var allCatEl = form.querySelector('[data-all-cats]');
			if (allCatEl) allCatEl.checked = true;
			['lt-price-min', 'lt-price-max'].forEach(function (id) {
				var el = document.getElementById(id); if (el) el.value = '';
			});
			pills.forEach(function (p) {
				var isAll = p.dataset.ltPill === 'all';
				p.classList.toggle('is-active', isAll);
				p.setAttribute('aria-current', isAll ? 'true' : 'false');
			});
			if (sortSel) sortSel.value = 'menu_order';
			fetchProducts(false);
		});
	}

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
})();
