(self["webpackChunk"] = self["webpackChunk"] || []).push([["vzw-lach-enroll-form"],{

/***/ "./assets/js/vzw-lach-enroll-form.js":
/*!*******************************************!*\
  !*** ./assets/js/vzw-lach-enroll-form.js ***!
  \*******************************************/
/***/ (() => {

var acteurCheckbox = document.getElementById("associate_base_categoryPreferences_0");
var figurantCheckbox = document.getElementById("associate_base_categoryPreferences_1");
var extraFields = document.getElementsByClassName('acteur-figurant');
function hideExtraFields() {
  for (var i = 0; i < extraFields.length; i++) {
    extraFields[i].classList.add("d-none");
  }
}
function showExtraFields() {
  for (var i = 0; i < extraFields.length; i++) {
    extraFields[i].classList.remove("d-none");
  }
}
function ActeurFigurantIsChecked() {
  if (acteurCheckbox.checked || figurantCheckbox.checked) {
    showExtraFields();
  } else {
    hideExtraFields();
  }
}
if (typeof acteurCheckbox != 'undefined' && acteurCheckbox != null && typeof figurantCheckbox != 'undefined' && figurantCheckbox != null) {
  acteurCheckbox.addEventListener("change", ActeurFigurantIsChecked);
  figurantCheckbox.addEventListener("change", ActeurFigurantIsChecked);
  ActeurFigurantIsChecked();
}
function DisableSubmit() {
  var submit = document.getElementById("submit");
  submit.disabled = true;
  submit.classList.add('disabled');
  var loader = document.getElementById("loader");
  loader.classList.remove("d-none");
}
function EnableSubmit() {
  var submit = document.getElementById("submit");
  submit.disabled = false;
  submit.classList.remove('disabled');
  var loader = document.getElementById("loader");
  loader.classList.add("d-none");
}

// Disabling form submissions if there are invalid fields
(function () {
  'use strict';

  window.addEventListener('load', function () {
    // Set submit button
    EnableSubmit();
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function (form) {
      form.addEventListener('submit', function (event) {
        DisableSubmit();
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
          EnableSubmit();
        }
        //form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("./assets/js/vzw-lach-enroll-form.js"));
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QUFJLENBQUNiLGdCQUFnQixDQUFDLFFBQVEsRUFBRSxVQUFTYyxLQUFLLEVBQUU7UUFDNUNiLGFBQWEsRUFBRTtRQUNmLElBQUlZLElBQUksQ0FBQ0UsYUFBYSxFQUFFLEtBQUssS0FBSyxFQUFFO1VBQ2hDRCxLQUFLLENBQUNFLGNBQWMsRUFBRTtVQUN0QkYsS0FBSyxDQUFDRyxlQUFlLEVBQUU7VUFDdkJaLFlBQVksRUFBRTtRQUNsQjtRQUNBO01BQ0osQ0FBQyxFQUFFLEtBQUssQ0FBQztJQUNiLENBQUMsQ0FBQztFQUNOLENBQUMsRUFBRSxLQUFLLENBQUM7QUFDYixDQUFDLEdBQUciLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanMvdnp3LWxhY2gtZW5yb2xsLWZvcm0uanMiXSwic291cmNlc0NvbnRlbnQiOlsibGV0IGFjdGV1ckNoZWNrYm94ID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJhc3NvY2lhdGVfYmFzZV9jYXRlZ29yeVByZWZlcmVuY2VzXzBcIik7XG5sZXQgZmlndXJhbnRDaGVja2JveCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwiYXNzb2NpYXRlX2Jhc2VfY2F0ZWdvcnlQcmVmZXJlbmNlc18xXCIpO1xuXG5sZXQgZXh0cmFGaWVsZHMgPSBkb2N1bWVudC5nZXRFbGVtZW50c0J5Q2xhc3NOYW1lKCdhY3RldXItZmlndXJhbnQnKTtcblxuZnVuY3Rpb24gaGlkZUV4dHJhRmllbGRzKCkge1xuICAgIGZvciAobGV0IGkgPSAwOyBpIDwgZXh0cmFGaWVsZHMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgZXh0cmFGaWVsZHNbaV0uY2xhc3NMaXN0LmFkZChcImQtbm9uZVwiKTtcbiAgICB9XG59XG5cbmZ1bmN0aW9uIHNob3dFeHRyYUZpZWxkcygpIHtcbiAgICBmb3IgKGxldCBpID0gMDsgaSA8IGV4dHJhRmllbGRzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgIGV4dHJhRmllbGRzW2ldLmNsYXNzTGlzdC5yZW1vdmUoXCJkLW5vbmVcIik7XG4gICAgfVxufVxuXG5mdW5jdGlvbiBBY3RldXJGaWd1cmFudElzQ2hlY2tlZCgpIHtcbiAgICBpZiAoIGFjdGV1ckNoZWNrYm94LmNoZWNrZWQgfHwgZmlndXJhbnRDaGVja2JveC5jaGVja2VkICkge1xuICAgICAgICBzaG93RXh0cmFGaWVsZHMoKTtcbiAgICB9IGVsc2Uge1xuICAgICAgICBoaWRlRXh0cmFGaWVsZHMoKTtcbiAgICB9XG59XG5cbmlmKHR5cGVvZihhY3RldXJDaGVja2JveCkgIT0gJ3VuZGVmaW5lZCcgJiYgYWN0ZXVyQ2hlY2tib3ggIT0gbnVsbCAmJiB0eXBlb2YoZmlndXJhbnRDaGVja2JveCkgIT0gJ3VuZGVmaW5lZCcgJiYgZmlndXJhbnRDaGVja2JveCAhPSBudWxsKSB7XG4gICAgYWN0ZXVyQ2hlY2tib3guYWRkRXZlbnRMaXN0ZW5lciggXCJjaGFuZ2VcIiwgQWN0ZXVyRmlndXJhbnRJc0NoZWNrZWQpO1xuICAgIGZpZ3VyYW50Q2hlY2tib3guYWRkRXZlbnRMaXN0ZW5lciggXCJjaGFuZ2VcIiwgQWN0ZXVyRmlndXJhbnRJc0NoZWNrZWQpO1xuICAgIEFjdGV1ckZpZ3VyYW50SXNDaGVja2VkKCk7XG59XG5cbmZ1bmN0aW9uIERpc2FibGVTdWJtaXQoKSB7XG4gICAgdmFyIHN1Ym1pdCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwic3VibWl0XCIpO1xuICAgIHN1Ym1pdC5kaXNhYmxlZCA9IHRydWU7XG4gICAgc3VibWl0LmNsYXNzTGlzdC5hZGQoJ2Rpc2FibGVkJyk7XG4gICAgdmFyIGxvYWRlciA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwibG9hZGVyXCIpO1xuICAgIGxvYWRlci5jbGFzc0xpc3QucmVtb3ZlKFwiZC1ub25lXCIpXG59XG5cbmZ1bmN0aW9uIEVuYWJsZVN1Ym1pdCgpIHtcbiAgICB2YXIgc3VibWl0ID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoXCJzdWJtaXRcIik7XG4gICAgc3VibWl0LmRpc2FibGVkID0gZmFsc2U7XG4gICAgc3VibWl0LmNsYXNzTGlzdC5yZW1vdmUoJ2Rpc2FibGVkJyk7XG4gICAgdmFyIGxvYWRlciA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKFwibG9hZGVyXCIpO1xuICAgIGxvYWRlci5jbGFzc0xpc3QuYWRkKFwiZC1ub25lXCIpXG59XG5cbi8vIERpc2FibGluZyBmb3JtIHN1Ym1pc3Npb25zIGlmIHRoZXJlIGFyZSBpbnZhbGlkIGZpZWxkc1xuKGZ1bmN0aW9uKCkge1xuICAgICd1c2Ugc3RyaWN0JztcbiAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcignbG9hZCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICAvLyBTZXQgc3VibWl0IGJ1dHRvblxuICAgICAgICBFbmFibGVTdWJtaXQoKTtcbiAgICAgICAgLy8gRmV0Y2ggYWxsIHRoZSBmb3JtcyB3ZSB3YW50IHRvIGFwcGx5IGN1c3RvbSBCb290c3RyYXAgdmFsaWRhdGlvbiBzdHlsZXMgdG9cbiAgICAgICAgdmFyIGZvcm1zID0gZG9jdW1lbnQuZ2V0RWxlbWVudHNCeUNsYXNzTmFtZSgnbmVlZHMtdmFsaWRhdGlvbicpO1xuICAgICAgICAvLyBMb29wIG92ZXIgdGhlbSBhbmQgcHJldmVudCBzdWJtaXNzaW9uXG4gICAgICAgIHZhciB2YWxpZGF0aW9uID0gQXJyYXkucHJvdG90eXBlLmZpbHRlci5jYWxsKGZvcm1zLCBmdW5jdGlvbihmb3JtKSB7XG4gICAgICAgICAgICBmb3JtLmFkZEV2ZW50TGlzdGVuZXIoJ3N1Ym1pdCcsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgICAgICAgICAgRGlzYWJsZVN1Ym1pdCgpO1xuICAgICAgICAgICAgICAgIGlmIChmb3JtLmNoZWNrVmFsaWRpdHkoKSA9PT0gZmFsc2UpIHtcbiAgICAgICAgICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgICAgICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICAgICAgICAgICAgICAgIEVuYWJsZVN1Ym1pdCgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAvL2Zvcm0uY2xhc3NMaXN0LmFkZCgnd2FzLXZhbGlkYXRlZCcpO1xuICAgICAgICAgICAgfSwgZmFsc2UpO1xuICAgICAgICB9KTtcbiAgICB9LCBmYWxzZSk7XG59KSgpO1xuIl0sIm5hbWVzIjpbImFjdGV1ckNoZWNrYm94IiwiZG9jdW1lbnQiLCJnZXRFbGVtZW50QnlJZCIsImZpZ3VyYW50Q2hlY2tib3giLCJleHRyYUZpZWxkcyIsImdldEVsZW1lbnRzQnlDbGFzc05hbWUiLCJoaWRlRXh0cmFGaWVsZHMiLCJpIiwibGVuZ3RoIiwiY2xhc3NMaXN0IiwiYWRkIiwic2hvd0V4dHJhRmllbGRzIiwicmVtb3ZlIiwiQWN0ZXVyRmlndXJhbnRJc0NoZWNrZWQiLCJjaGVja2VkIiwiYWRkRXZlbnRMaXN0ZW5lciIsIkRpc2FibGVTdWJtaXQiLCJzdWJtaXQiLCJkaXNhYmxlZCIsImxvYWRlciIsIkVuYWJsZVN1Ym1pdCIsIndpbmRvdyIsImZvcm1zIiwidmFsaWRhdGlvbiIsIkFycmF5IiwicHJvdG90eXBlIiwiZmlsdGVyIiwiY2FsbCIsImZvcm0iLCJldmVudCIsImNoZWNrVmFsaWRpdHkiLCJwcmV2ZW50RGVmYXVsdCIsInN0b3BQcm9wYWdhdGlvbiJdLCJzb3VyY2VSb290IjoiIn0=