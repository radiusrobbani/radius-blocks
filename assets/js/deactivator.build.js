/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************!*\
  !*** ./src/deactivator.js ***!
  \****************************/
if (window.ultimate_blocks) {
  window.ultimate_blocks.forEach(function (block) {
    if (!block.active) {
      wp.blocks.unregisterBlockType(block.name);
    }
  });
}
/******/ })()
;