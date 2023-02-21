require('../css/vzw-lach.scss');

import * as bootstrap from 'bootstrap'

//const $ = require('jquery');
//window.$ = $;
//window.jQuery = $;

// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
//require('bootstrap');

// or you can include specific pieces
//require('bootstrap/js/dist/tooltip');
//require('bootstrap/js/dist/popover');
//require('bootstrap/js/dist/toast');

const tooltipTriggerList = document.querySelectorAll('[data-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

const toastElList = document.querySelectorAll('.toast')
const toastList = [...toastElList].map(toastEl => new bootstrap.Toast(toastEl))
Array.prototype.filter.call(toastList, function(toast, {delay = 7500}) {
    toast.show();
});


$(window).on( "load", function() {
    const asyncElList = document.querySelectorAll('[data-provider]')
    const asyncList = [...asyncElList].map(asyncEl => api_load_async(asyncEl))
});

function api_load_async(element) {

    if (element.dataset.spinner) element.innerHTML = '<div class="text-center my-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'

    $.getJSON( "/api/private/" + element.dataset.provider, {
        format: "json"
    })
    .done(function(data) {
        if (data.success) {
            element.innerHTML = data.html;
        }
    });

}
