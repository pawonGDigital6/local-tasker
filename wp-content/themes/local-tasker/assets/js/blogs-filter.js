/**
 * Blog With Filter block — category filtering + load more.
 * Scoped per block instance via [data-block-id] so multiple blocks
 * on the same page work independently.
 */
(function () {
	'use strict';

	document.querySelectorAll('.lt-blogs-filter[data-block-id]').forEach(function (block) {
		const nonce        = block.dataset.nonce;
		const perPage      = parseInt(block.dataset.postsPerPage, 10) || 3;
		const grid         = block.querySelector('.blog-lists');
		const loadMoreWrap = block.querySelector('.ld-btn-wrap');
		const loadMoreBtn  = block.querySelector('.blog-load-more-btn');
		const filterBtns   = block.querySelectorAll('.filter-btn');

		let currentCategory = 0;
		let currentPage     = 1;
		let isLoading       = false;

		// ── Helpers ────────────────────────────────────────────────────────── //

		function setLoading(state) {
			isLoading = state;
			if (loadMoreBtn) {
				loadMoreBtn.disabled    = state;
				loadMoreBtn.textContent = state ? 'Loading…' : 'Load More Articles';
			}
		}

		function fetchPosts(opts) {
			const { category, page, replace } = opts;

			if (isLoading) return;
			setLoading(true);

			const body = new FormData();
			body.append('action',         'bwf_load_posts');
			body.append('nonce',          nonce);
			body.append('category_id',    category);
			body.append('page',           page);
			body.append('posts_per_page', perPage);

			fetch(bwfData.ajaxUrl, { method: 'POST', body: body })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (!res.success) return;

					const html     = res.data.html     || '';
					const hasMore  = res.data.has_more || false;

					if (replace) {
						grid.innerHTML = html;
						currentPage = page;
					} else {
						grid.insertAdjacentHTML('beforeend', html);
						currentPage = page;
					}

					if (loadMoreWrap) {
						loadMoreWrap.classList.toggle('hidden', !hasMore);
					}
				})
				.catch(function () {
					// Silently restore button state on network error.
				})
				.finally(function () {
					setLoading(false);
				});
		}

		// ── Filter buttons ─────────────────────────────────────────────────── //

		filterBtns.forEach(function (btn) {
			btn.addEventListener('click', function () {
				const category = parseInt(btn.dataset.category, 10) || 0;

				filterBtns.forEach(function (b) {
					b.classList.remove('active');
					b.setAttribute('aria-pressed', 'false');
				});
				btn.classList.add('active');
				btn.setAttribute('aria-pressed', 'true');

				currentCategory = category;
				fetchPosts({ category: currentCategory, page: 1, replace: true });
			});
		});

		// ── Load More ──────────────────────────────────────────────────────── //

		if (loadMoreBtn) {
			loadMoreBtn.addEventListener('click', function () {
				fetchPosts({ category: currentCategory, page: currentPage + 1, replace: false });
			});
		}
	});
})();
