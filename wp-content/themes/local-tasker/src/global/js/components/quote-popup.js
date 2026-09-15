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

		// Escape closes the popup, so keyboard users are never trapped in it
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && document.body.classList.contains('opened-quote-popup')) {
				this.quotePopClose();
			}
		});
	},

	quotePopOpen: function (e) {
		e.preventDefault();
		// Stop propagation so the opening click doesn't instantly trigger handleOutsideClick
		e.stopPropagation();
		// Remember the opener so focus can be returned to it on close
		this.lastOpener = e.currentTarget;
		document.body.classList.add('opened-quote-popup');
		this.quoteCloser?.focus();
	},

	quotePopClose: function () {
		if (!document.body.classList.contains('opened-quote-popup')) {
			return;
		}
		document.body.classList.remove('opened-quote-popup');
		this.lastOpener?.focus();
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
