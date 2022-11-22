require('../css/vzw-lach.scss');

require('bootstrap');
//require('jquery.scrollTo');
//require('@fortawesome/fontawesome-pro/css/all.min.css');

//var $ = require('jquery');
//window.$ = $;
//window.jQuery = $;

// Hide Header on on scroll down
var didScroll;
var lastScrollTop = 0;
var delta = 10;
var navbarHeight = $('#nav-categories').outerHeight();

$(window).scroll(function(event){
    didScroll = true;
});

setInterval(function() {
    if (didScroll) {
        hasScrolled();
        didScroll = false;
    }
}, 250);

function hasScrolled() {
    var st = $(window).scrollTop();

    // Make sure they scroll more than delta
    if (Math.abs(lastScrollTop - st) <= delta){
        return;
    }
    
    // If they scrolled down and are past the navbar, add class .nav-up.
    // This is necessary so you never see what is "behind" the navbar.
    if (st > lastScrollTop && st > navbarHeight){
        // Scroll Down
        $('#nav-categories').animate({'top':'-40px'},250);
    } else {
        // Scroll Up
        if(st + $(window).height() < $(document).height()) {
            $('#nav-categories').animate({'top':'0'},250);
        }
    }
    
    lastScrollTop = st;
}

function addAnimation(id, effect) {
    $('#'+id).removeClass().addClass(effect + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
        $(this).removeClass();
    });
};

function toClipboard(id){
    var element = document.getElementById(id);
    var textArea = document.createElement("textarea");
    textArea.value = element.textContent;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand("Copy");
    textArea.remove();
};

function getSessionTimeout( sessionModal, sessionWarn, sessionTime, sessionLogout ) {
    $.getJSON("/api/session/timeout", function(result) {
        var logout = true;
        if (result.success) { 
            if (result.elapsed < sessionWarn) {
                logout = false;
                $(sessionModal).modal('hide');
            } else if (result.elapsed < sessionTime) {
                $('#sessionModalRemaining').html(sessionTime-elapsed);
                $(sessionModal).modal('show');
            }
        }
        if (logout) { 
            document.location.replace(sessionLogout.data('logout'));
        }
    }).fail(function() {
        document.location.reload(true); // force to get the login window
    });
}

function sessionHandler( sessionModal ){

    if ($(sessionModal).length == 0 ) return;

    var sessionTime = 1200;
    var sessionWarn = 900;
    var sessionLogout = $('#sessionModal [data-logout]');

    getSessionTimeout( sessionModal, sessionWarn, sessionTime, sessionLogout );

    $(sessionLogout).click(function() {
        document.location.replace(sessionLogout.data('logout'));
    });

    $(sessionModal).on('hide.bs.modal', function (e) {
        $.getJSON("/api/session/keep-alive");
    });

    var sessionInterval = setInterval(function() {
        getSessionTimeout( sessionModal, sessionWarn, sessionTime, sessionLogout );
    }, 15000);
}

sessionHandler($('#sessionModal'));


$(document).ready(function(){

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

    $(function() {
       $('[data-toggle="popover"]').popover();
    })

    $(function () {
        'use strict'
        $('[data-toggle="offcanvas"]').on('click', function(){
            $('.offcanvas-collapse').toggleClass('open')
        })
    });

    $('#navbarNav').on('hide.bs.collapse', function () {
      $('#navbarSearch').collapse('hide');
    });

    $('#toTop').click(function(e){
        $.scrollTo(0, 700); 
    })

    /*  init back to top icon */
    var scrollPos; 
    var toTopVisible=false;
    var toTop = document.getElementById('toTop');
 
    /* Every time the window is scrolled ... */
    $(window).scroll( function(){

        scrollPos = $(window).scrollTop();

        // show back to top
        if ( scrollPos >= 400 && !toTopVisible ){
            toTopVisible=true;
            toTop.style.visibility="visible";
            toTop.style.opacity="0";
            $('#toTop').animate({'opacity':'1'},750);
        } 
        if ( scrollPos < 400 && toTopVisible ){
            toTopVisible=false;
            $('#toTop').animate({'opacity':'0'},750,function(){ 
                toTop.style.visibility="hidden";
            });
        }
 
    });

});


$(window).on( "load", function() {
});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
