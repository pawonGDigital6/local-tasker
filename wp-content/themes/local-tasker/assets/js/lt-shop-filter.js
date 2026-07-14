/**
 * Local Tasker — Shop archive filter.
 *
 * Self-initialises on every [data-lt-shop] root (works for both the block and the
 * native /shop/ archive). Reads the sidebar facets, quick-filter chips, sort and
 * price inputs, then requests matching products from admin-ajax and swaps the grid.
 *
 * Endpoint: action=lt_filter_products (see inc/class-shop-filter-ajax.php).
 * Dependency-free.
 */
(function () {
	'use strict';

	function debounce(fn, wait) {
		var t;
		return function () {
			var ctx = this, args = arguments;
			clearTimeout(t);
			t = setTimeout(function () { fn.apply(ctx, args); }, wait);
		};
	}

	function ajaxUrl(root) {
		return root.getAttribute('data-ajax') ||
			(window.ltShop && window.ltShop.ajaxUrl) ||
			'/wp-admin/admin-ajax.php';
	}

	function Shop(root) {
		this.root = root;
		this.grid = root.querySelector('[data-lt-grid]');
		this.count = root.querySelector('[data-lt-count]');
		this.moreWrap = root.querySelector('[data-lt-more-wrap]');
		this.moreBtn = root.querySelector('[data-lt-more]');
		this.nonce = root.getAttribute('data-nonce');
		this.perPage = parseInt(root.getAttribute('data-per-page'), 10) || 9;
		this.url = ajaxUrl(root);
		this.page = 1;
		this.loading = false;
		this.bind();
	}

	Shop.prototype.state = function () {
		var root = this.root;
		var checked = function (sel) {
			return Array.prototype.map.call(root.querySelectorAll(sel + ':checked'), function (el) { return el.value; })
				.filter(Boolean);
		};

		var catRadio = root.querySelector('[data-lt-filter="category"]:checked');
		var sortEl = root.querySelector('[data-lt-sort]');
		var minEl = root.querySelector('[data-lt-filter="min_price"]');
		var maxEl = root.querySelector('[data-lt-filter="max_price"]');

		return {
			category: catRadio ? catRadio.value : '',
			colour: checked('[data-lt-filter="colour"]'),
			thickness: checked('[data-lt-filter="thickness"]'),
			min_price: minEl ? minEl.value : '',
			max_price: maxEl ? maxEl.value : '',
			on_sale: root.querySelector('[data-lt-chip="on_sale"].is-active') ? 1 : '',
			new: root.querySelector('[data-lt-chip="new"].is-active') ? 1 : '',
			in_stock: root.querySelector('[data-lt-chip="in_stock"].is-active') ? 1 : '',
			orderby: sortEl ? sortEl.value : 'popularity'
		};
	};

	Shop.prototype.fetch = function (append) {
		if (this.loading) { return; }
		this.loading = true;
		if (this.grid) { this.grid.setAttribute('aria-busy', 'true'); }
		this.root.classList.add('is-loading');

		var s = this.state();
		var body = new URLSearchParams();
		body.set('action', 'lt_filter_products');
		body.set('nonce', this.nonce);
		body.set('paged', append ? ++this.page : (this.page = 1));
		body.set('per_page', this.perPage);
		body.set('append', append ? 1 : '');
		body.set('category', s.category);
		body.set('min_price', s.min_price);
		body.set('max_price', s.max_price);
		body.set('orderby', s.orderby);
		body.set('on_sale', s.on_sale);
		body.set('new', s.new);
		body.set('in_stock', s.in_stock);
		s.colour.forEach(function (v) { body.append('colour[]', v); });
		s.thickness.forEach(function (v) { body.append('thickness[]', v); });

		var self = this;
		fetch(this.url, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
			credentials: 'same-origin'
		})
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (!res || !res.success) { throw new Error('bad response'); }
				var d = res.data;
				if (append) {
					self.grid.insertAdjacentHTML('beforeend', d.html);
				} else {
					self.grid.innerHTML = d.html;
				}
				self.updateCount(d.found_posts);
				self.toggleMore(d.has_more);
				if (typeof jQuery !== 'undefined') {
					jQuery(document.body).trigger('wc_fragment_refresh');
				}
			})
			.catch(function () {
				if (!append && self.grid) {
					self.grid.innerHTML = '<p class="lt-shop__empty">Something went wrong. Please try again.</p>';
				}
			})
			.finally(function () {
				self.loading = false;
				self.root.classList.remove('is-loading');
				if (self.grid) { self.grid.setAttribute('aria-busy', 'false'); }
			});
	};

	Shop.prototype.updateCount = function (n) {
		if (!this.count) { return; }
		var label = n === 1 ? 'result' : 'results';
		this.count.textContent = (n || 0).toLocaleString() + ' ' + label;
	};

	Shop.prototype.toggleMore = function (has) {
		if (!this.moreWrap) { return; }
		this.moreWrap.classList.toggle('is-hidden', !has);
	};

	Shop.prototype.setChip = function (chip, on) {
		chip.classList.toggle('is-active', on);
		chip.setAttribute('aria-pressed', on ? 'true' : 'false');
	};

	Shop.prototype.bind = function () {
		var self = this;
		var root = this.root;

		// Facets: category / colour / thickness.
		root.addEventListener('change', function (e) {
			if (e.target.matches('[data-lt-filter], [data-lt-sort]')) {
				// Keep category chips in sync when a category radio changes.
				if (e.target.matches('[data-lt-filter="category"]')) {
					self.syncCategoryChips(e.target.value);
				}
				self.fetch(false);
			}
		});

		// Price inputs (debounced).
		var priceHandler = debounce(function () { self.fetch(false); }, 500);
		root.querySelectorAll('[data-lt-filter="min_price"], [data-lt-filter="max_price"]').forEach(function (el) {
			el.addEventListener('input', priceHandler);
		});

		// Quick-filter chips (on_sale / new / in_stock / all).
		root.querySelectorAll('[data-lt-chip]').forEach(function (chip) {
			chip.addEventListener('click', function () {
				var key = chip.getAttribute('data-lt-chip');
				if (key === 'all') {
					root.querySelectorAll('[data-lt-chip]').forEach(function (c) {
						self.setChip(c, c.getAttribute('data-lt-chip') === 'all');
					});
				} else {
					self.setChip(root.querySelector('[data-lt-chip="all"]'), false);
					self.setChip(chip, !chip.classList.contains('is-active'));
					if (!root.querySelector('[data-lt-chip].is-active')) {
						self.setChip(root.querySelector('[data-lt-chip="all"]'), true);
					}
				}
				self.fetch(false);
			});
		});

		// Category chips.
		root.querySelectorAll('[data-lt-chip-cat]').forEach(function (chip) {
			chip.addEventListener('click', function () {
				var slug = chip.getAttribute('data-lt-chip-cat');
				var active = chip.classList.contains('is-active');
				root.querySelectorAll('[data-lt-chip-cat]').forEach(function (c) { self.setChip(c, false); });
				self.setChip(chip, !active);
				var value = active ? '' : slug;
				var radio = root.querySelector('[data-lt-filter="category"][value="' + value + '"]');
				if (radio) { radio.checked = true; }
				self.fetch(false);
			});
		});

		// Facet accordion heads.
		root.querySelectorAll('.lt-facet__head').forEach(function (head) {
			head.addEventListener('click', function () {
				var open = head.getAttribute('aria-expanded') === 'true';
				head.setAttribute('aria-expanded', open ? 'false' : 'true');
				head.closest('[data-lt-facet]').classList.toggle('is-collapsed', open);
			});
		});

		// Clear all.
		var clear = root.querySelector('[data-lt-clear]');
		if (clear) {
			clear.addEventListener('click', function () {
				root.querySelectorAll('[data-lt-filter="colour"], [data-lt-filter="thickness"]').forEach(function (c) { c.checked = false; });
				var allCat = root.querySelector('[data-lt-filter="category"][value=""]');
				if (allCat) { allCat.checked = true; }
				var minEl = root.querySelector('[data-lt-filter="min_price"]');
				var maxEl = root.querySelector('[data-lt-filter="max_price"]');
				if (minEl) { minEl.value = ''; }
				if (maxEl) { maxEl.value = ''; }
				root.querySelectorAll('[data-lt-chip]').forEach(function (c) {
					self.setChip(c, c.getAttribute('data-lt-chip') === 'all');
				});
				root.querySelectorAll('[data-lt-chip-cat]').forEach(function (c) { self.setChip(c, false); });
				var sortEl = root.querySelector('[data-lt-sort]');
				if (sortEl) { sortEl.value = 'popularity'; }
				self.fetch(false);
			});
		}

		// Load more.
		if (this.moreBtn) {
			this.moreBtn.addEventListener('click', function () { self.fetch(true); });
		}
	};

	Shop.prototype.syncCategoryChips = function (value) {
		var self = this;
		this.root.querySelectorAll('[data-lt-chip-cat]').forEach(function (c) {
			self.setChip(c, c.getAttribute('data-lt-chip-cat') === value && value !== '');
		});
	};

	function boot() {
		document.querySelectorAll('[data-lt-shop]').forEach(function (root) {
			if (root.__ltShop) { return; }
			root.__ltShop = new Shop(root);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
