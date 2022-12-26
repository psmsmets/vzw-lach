(self["webpackChunk"] = self["webpackChunk"] || []).push([["ea-lightbox-prevent-scroll"],{

/***/ "./assets/js/ea-lightbox-prevent-scroll.js":
/*!*************************************************!*\
  !*** ./assets/js/ea-lightbox-prevent-scroll.js ***!
  \*************************************************/
/***/ (() => {

// Disabling scrolling of ea-lightbox modals
(function () {
  'use strict';

  window.addEventListener('load', function () {
    // Fetch all lightboxes
    var thumbs = document.getElementsByClassName('ea-lightbox-thumbnail');
    // Loop over them and prevent scrolling
    var noScroll = Array.prototype.filter.call(thumbs, function (thumb) {
      thumb.href = "javascript:void(0)";
    });
  }, false);
})();

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("./assets/js/ea-lightbox-prevent-scroll.js"));
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiZWEtbGlnaHRib3gtcHJldmVudC1zY3JvbGwuNzZmZmZkMjEuanMiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7QUFBQTtBQUNBLENBQUMsWUFBVztFQUNSLFlBQVk7O0VBQ1pBLE1BQU0sQ0FBQ0MsZ0JBQWdCLENBQUMsTUFBTSxFQUFFLFlBQVc7SUFDdkM7SUFDQSxJQUFJQyxNQUFNLEdBQUdDLFFBQVEsQ0FBQ0Msc0JBQXNCLENBQUMsdUJBQXVCLENBQUM7SUFDckU7SUFDQSxJQUFJQyxRQUFRLEdBQUdDLEtBQUssQ0FBQ0MsU0FBUyxDQUFDQyxNQUFNLENBQUNDLElBQUksQ0FBQ1AsTUFBTSxFQUFFLFVBQVNRLEtBQUssRUFBRTtNQUMvREEsS0FBSyxDQUFDQyxJQUFJLEdBQUcsb0JBQW9CO0lBQ3JDLENBQUMsQ0FBQztFQUNOLENBQUMsRUFBRSxLQUFLLENBQUM7QUFDYixDQUFDLEdBQUciLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanMvZWEtbGlnaHRib3gtcHJldmVudC1zY3JvbGwuanMiXSwic291cmNlc0NvbnRlbnQiOlsiLy8gRGlzYWJsaW5nIHNjcm9sbGluZyBvZiBlYS1saWdodGJveCBtb2RhbHNcbihmdW5jdGlvbigpIHtcbiAgICAndXNlIHN0cmljdCc7XG4gICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoJ2xvYWQnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgLy8gRmV0Y2ggYWxsIGxpZ2h0Ym94ZXNcbiAgICAgICAgdmFyIHRodW1icyA9IGRvY3VtZW50LmdldEVsZW1lbnRzQnlDbGFzc05hbWUoJ2VhLWxpZ2h0Ym94LXRodW1ibmFpbCcpO1xuICAgICAgICAvLyBMb29wIG92ZXIgdGhlbSBhbmQgcHJldmVudCBzY3JvbGxpbmdcbiAgICAgICAgdmFyIG5vU2Nyb2xsID0gQXJyYXkucHJvdG90eXBlLmZpbHRlci5jYWxsKHRodW1icywgZnVuY3Rpb24odGh1bWIpIHtcbiAgICAgICAgICAgIHRodW1iLmhyZWYgPSBcImphdmFzY3JpcHQ6dm9pZCgwKVwiO1xuICAgICAgICB9KTtcbiAgICB9LCBmYWxzZSk7XG59KSgpO1xuIl0sIm5hbWVzIjpbIndpbmRvdyIsImFkZEV2ZW50TGlzdGVuZXIiLCJ0aHVtYnMiLCJkb2N1bWVudCIsImdldEVsZW1lbnRzQnlDbGFzc05hbWUiLCJub1Njcm9sbCIsIkFycmF5IiwicHJvdG90eXBlIiwiZmlsdGVyIiwiY2FsbCIsInRodW1iIiwiaHJlZiJdLCJzb3VyY2VSb290IjoiIn0=