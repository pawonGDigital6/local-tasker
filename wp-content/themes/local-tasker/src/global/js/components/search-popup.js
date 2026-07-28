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
				searchOpener.addEventListener('click', () => this.searchPopOpen());
			});
		}

		if (this.searchCloser) {
			this.searchCloser.addEventListener('click', () => this.searchPopClose());
		}
	},

	searchPopOpen: function () {
		document.body.classList.add('opened-search-popup');
	},

	searchPopClose: function () {
		document.body.classList.remove('opened-search-popup');
	},
};

export default searchPop;