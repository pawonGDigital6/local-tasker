const heroSearch = {
	init() {
		this.cacheDom();
		if (!this.form) return;
		this.subCatsMap = JSON.parse(this.form.dataset.subCats || '{}');
		this.bindEvents();
	},

	cacheDom() {
		this.form = document.querySelector('.hero-search__form');
		if (!this.form) return;
		this.mainCatSelect = this.form.querySelector('#hero-product-cat');
		this.subCatSelect = this.form.querySelector('#hero-sub-cat');
	},

	bindEvents() {
		this.mainCatSelect.addEventListener('change', () => this.onMainCatChange());
		this.subCatSelect.addEventListener('change', () => this.updateSelectState(this.subCatSelect));
		this.form.addEventListener('submit', (e) => this.onSubmit(e));
	},

	onMainCatChange() {
		const selectedOption = this.mainCatSelect.selectedOptions[0];
		const termId = selectedOption?.dataset.termId;
		const children = (termId && this.subCatsMap[termId]) || [];
		const placeholder = this.subCatSelect.dataset.placeholder || '';

		this.subCatSelect.innerHTML = `<option value="">${placeholder}</option>`;
		children.forEach(({ slug, name }) => {
			const opt = document.createElement('option');
			opt.value = slug;
			opt.textContent = name;
			this.subCatSelect.appendChild(opt);
		});

		this.subCatSelect.disabled = children.length === 0;
		this.updateSelectState(this.mainCatSelect);
		this.updateSelectState(this.subCatSelect);
	},

	onSubmit(e) {
		e.preventDefault();
		const catSlug = this.subCatSelect.value || this.mainCatSelect.value;
		const url = new URL(this.form.action || window.location.href);

		if (catSlug) {
			url.searchParams.set('product_cat', catSlug);
		}

		window.location.href = url.toString();
	},

	updateSelectState(select) {
		select.classList.toggle('is-selected', !!select.value);
	},
};

heroSearch.init();
