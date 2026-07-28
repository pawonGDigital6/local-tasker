const quotePop = {
	init: function () {
		this.cacheDom();
		this.bindEvents();
	},

	cacheDom: function () {
		this.quoteOpeners = document.querySelectorAll('a[href="#quote-popup"], a[data-quote-popup]');
		this.quoteCloser = document.querySelector('.quote-pop-closer');
	},

	bindEvents: function () {
		this.quoteOpeners.forEach((quoteOpener) => {
			quoteOpener.addEventListener('click', (e) => this.quotePopOpen(e));
		});

		this.quoteCloser?.addEventListener('click', () => this.quotePopClose());

		// Close popup when clicking outside .gqf-form
		document.addEventListener('click', (e) => this.handleOutsideClick(e));
	},

	quotePopOpen: function (e) {
		e.preventDefault();
		// Stop propagation so the opening click doesn't instantly trigger handleOutsideClick
		e.stopPropagation();
		document.body.classList.add('opened-quote-popup');
	},

	quotePopClose: function () {
		document.body.classList.remove('opened-quote-popup');
	},

	handleOutsideClick: function (e) {
		// Only check if the popup is currently open
		if (document.body.classList.contains('opened-quote-popup')) {
			const form = document.querySelector('.gqf-form');

			// Close if the click target is NOT the form and NOT inside the form
			if (form && !form.contains(e.target)) {
				this.quotePopClose();
			}
		}
	},
};

export default quotePop;
