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
        // Arrow function preserves 'this' context
        this.searchOpeners.forEach((searchOpener) => {
            searchOpener.addEventListener('click', () => this.searchPopOpen());
        });

        // Optional chaining (?.) prevents crashes if closer doesn't exist
        this.searchCloser?.addEventListener('click', () => this.searchPopClose());
    },

    searchPopOpen: function () {
        document.body.classList.add('opened-search-popup');
    },

    searchPopClose: function () {
        document.body.classList.remove('opened-search-popup');
    },
};

export default searchPop;