const searchPop = {
	init: function () {
		this.cacheDom();
		this.bindEvents();
	},

	cacheDom: function () {
		this.searchOpeners = document.querySelectorAll('.search-pop-opener');
		this.searchCloser = document.querySelector('.search-pop-closer');
	},

	bindEvents: function () {
		// Classic check guarantees no crash even without optional chaining support
		if (this.searchOpeners && this.searchOpeners.length > 0) {
			this.searchOpeners.forEach((searchOpener) => {
				searchOpener.addEventListener('click', () => this.searchPopOpen(searchOpener));
			});
		}

		if (this.searchCloser) {
			this.searchCloser.addEventListener('click', () => this.searchPopClose());
		}

		// Escape closes the popup, so keyboard users are never trapped in it
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && document.body.classList.contains('opened-search-popup')) {
				this.searchPopClose();
			}
		});
	},

	searchPopOpen: function (opener) {
		// Remember the opener so focus can be returned to it on close
		this.lastOpener = opener || null;
		document.body.classList.add('opened-search-popup');
		const searchInput = document.querySelector('.global-search-pop input[type="search"]');
		if (searchInput) {
			setTimeout(() => {
				searchInput.focus();
			}, 100);
		}
	},

	searchPopClose: function () {
		if (!document.body.classList.contains('opened-search-popup')) {
			return;
		}
		document.body.classList.remove('opened-search-popup');
		if (this.lastOpener) {
			this.lastOpener.focus();
		}
	},
};

export default searchPop;