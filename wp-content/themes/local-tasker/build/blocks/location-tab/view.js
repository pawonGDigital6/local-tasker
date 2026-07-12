/******/ (() => { // webpackBootstrap
/*!*****************************************!*\
  !*** ./src/blocks/location-tab/view.js ***!
  \*****************************************/
/**
 * Location Tab — front-end tab switching.
 *
 * Scoped per `.lc-tab` instance so multiple Location Tab blocks can coexist on
 * one page without interfering with each other. Mirrors the interaction of the
 * Services & Products block's tabs.
 */
function initLocationTab(root) {
  const buttons = root.querySelectorAll(".lc-tab__btn");
  const panels = root.querySelectorAll(".lc-tab__panel");
  buttons.forEach(button => {
    button.addEventListener("click", () => {
      const targetId = button.getAttribute("data-tab-target");
      buttons.forEach(btn => {
        btn.classList.remove("active");
        btn.setAttribute("aria-selected", "false");
      });
      button.classList.add("active");
      button.setAttribute("aria-selected", "true");
      panels.forEach(panel => panel.classList.remove("active"));
      const target = root.querySelector(`#${CSS.escape(targetId)}`);
      if (target) {
        target.classList.add("active");
      }
    });
  });
}
document.querySelectorAll(".lt-location-tab .lc-tab").forEach(initLocationTab);
/******/ })()
;
//# sourceMappingURL=view.js.map