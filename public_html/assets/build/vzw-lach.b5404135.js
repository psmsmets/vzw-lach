"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[668],{491:(t,r,e)=>{var n=e(138),o=e(755);function a(t){return function(t){if(Array.isArray(t))return i(t)}(t)||function(t){if("undefined"!=typeof Symbol&&null!=t[Symbol.iterator]||null!=t["@@iterator"])return Array.from(t)}(t)||function(t,r){if(!t)return;if("string"==typeof t)return i(t,r);var e=Object.prototype.toString.call(t).slice(8,-1);"Object"===e&&t.constructor&&(e=t.constructor.name);if("Map"===e||"Set"===e)return Array.from(t);if("Arguments"===e||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(e))return i(t,r)}(t)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function i(t,r){(null==r||r>t.length)&&(r=t.length);for(var e=0,n=new Array(r);e<r;e++)n[e]=t[e];return n}e(291);var u=e(152),l=(a(document.querySelectorAll('[data-toggle="tooltip"]')).map((function(t){return new n.u(t)})),a(document.querySelectorAll(".toast")).map((function(t){return new n.FN(t)})));Array.prototype.filter.call(l,(function(t,r){r.delay;t.show()}));var c=a(document.querySelectorAll("[data-clipboard-text]")).map((function(t){return new u(t)}));Array.prototype.filter.call(c,(function(t){t.on("success",(function(t){return t.clearSelection(),!1}))})),o(window).on("load",(function(){a(document.querySelectorAll("[data-provider]")).map((function(t){return function(t){t.dataset.spinner&&(t.innerHTML='<div class="text-center my-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');o.getJSON("/api/private/"+t.dataset.provider,{format:"json"}).done((function(r){r.success&&(t.innerHTML=r.html,o(".past").hide())}))}(t)}))}))},291:(t,r,e)=>{e.r(r)}},t=>{t.O(0,[755,472],(()=>{return r=491,t(t.s=r);var r}));t.O()}]);