const tab = {
    init: function () {
        this.cacheDom();
        this.bindEvents();
    },

    cacheDom: function () {
        this.tab = document.querySelector(".cs-tab");
        this.tabTitleLists = document.querySelectorAll(".cs-tab__head-title");
        this.tabContentLists = document.querySelectorAll(".tab-content");
    },

    bindEvents: function () {
        this.tabTitleLists.forEach((tabTitleList) => {
            tabTitleList.addEventListener("click", () => {
                this.tabTitleLists.forEach((tab) => {
                    tab.classList.remove("active");
                });
                tabTitleList.classList.add("active");
                this.switchTab(tabTitleList);
            });
        });
    },

    switchTab: function (tabTitleList) {
        const tabListTitleAttrValue = tabTitleList.getAttribute("data-tab-target");
        const respectiveTabContent = this.tab.querySelector(
            `#${tabListTitleAttrValue}`
        );
        this.tabContentLists.forEach((tabContentList) => {
            tabContentList.classList.remove("active");
        });
        respectiveTabContent.classList.add("active");
    }
};

tab.init();
