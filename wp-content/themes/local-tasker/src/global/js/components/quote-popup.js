const quotePop = {
	init: function () {
		this.cacheDom();
		this.bindEvents();
	},

	cacheDom: function () {
		this.quoteOpeners = document.querySelectorAll('a[href="#quote-popup"]');
		this.quoteCloser = document.querySelector('.quote-pop-closer');
	},

	bindEvents: function () {
		this.quoteOpeners.forEach((quoteOpener) => {
			// Pass 'e' from the event listener argument into the function
			quoteOpener.addEventListener('click', (e) => this.quotePopOpen(e));
		});

		this.quoteCloser?.addEventListener('click', () => this.quotePopClose());
	},

	// Accept 'e' as a parameter here
	quotePopOpen: function (e) {
		e.preventDefault();
		document.body.classList.add('opened-quote-popup');
	},

	quotePopClose: function () {
		document.body.classList.remove('opened-quote-popup');
	},
};

export default quotePop;
