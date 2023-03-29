require('../css/vzw-lach.scss');

import * as bootstrap from 'bootstrap'
import { createPopper } from '@popperjs/core';

var Clipboard = require('clipboard');

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
const tooltipList = [...tooltipTriggerList].map(tooltipTrigger => new bootstrap.Tooltip(tooltipTrigger))

const toastElementList = document.querySelectorAll('.toast')
const toastList = [...toastElementList].map(toastElement => new bootstrap.Toast(toastElement))
Array.prototype.filter.call(toastList, function(toast, {delay = 7500}) {
    toast.show();
});

function setTooltip(btn, message) {
    btn.tooltip('hide')
        .attr('data-original-title', message)
        .tooltip('show');
}

function hideTooltip(btn) {
    setTimeout(function() {
        btn.tooltip('hide');
    }, 1000);
}

const cbTriggerList = document.querySelectorAll('[data-clipboard-text]')
const cbList = [...cbTriggerList].map(cbTrigger => new Clipboard(cbTrigger))
Array.prototype.filter.call(cbList, function(clipboard) {

    clipboard.on('success', function (e) {
        //console.info('Action:', e.action);
        //console.info('Text:', e.text);
        //console.info('Trigger:', e.trigger);
        //e.trigger.style.transition = "transform .5s ease-in-out";
        //e.trigger.style.transform = "rotate(20deg) scale(1.5)";

        e.clearSelection();
        return false;

    });

    /*clipboard.on('error', function (e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
    });*/
});

$(window).on( "load", function() {
    const asyncElList = document.querySelectorAll('[data-provider]')
    const asyncList = [...asyncElList].map(asyncEl => api_load_async(asyncEl))
});

function api_load_async(element) {

    if (element.dataset.spinner) element.innerHTML = '<div class="text-center my-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'

    $.getJSON( "/api/private/" + element.dataset.provider, {
        format: "json",
        tag: element.dataset.tag
    })
    .done(function(data) {
        if (data.success) {
            element.innerHTML = data.html;
            $('.past').hide();
        }
    });

}
