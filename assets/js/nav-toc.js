require('bootstrap');

function loadNavLink(navLink) {
    if (!$(navLink).length) {
        return;
    }
    var target = $('#'+$(navLink).attr('aria-controls'));

    $.getJSON( $(navLink).attr('data-source') ).done(function( data ) {
        if (data.success) {
            $(target).html(data.html)
        }
    });

}

function getActiveNavItem(toc) {
    return $(toc).find('.nav-item.active').first()[0];
}

function deactivateNavItem(navItem) {
    $(navItem).removeClass('active');
}

function activateNavItem(navItem) {
    $(navItem).addClass('active');
}

function closeNavList(navItem) {
    if ( !$(navItem).parent().hasClass('nav-list') ) {
        $(navItem).parent().find('a.nav-link[data-toggle=collapse]').each( function() {
            $( $(this).attr('data-target') ).collapse('hide');
        });
    };
}

$('.nav-toc').on('click', 'a[data-toggle=load]', function (e) {
    e.preventDefault()
    var newItem = $(this).closest('.nav-item')[0];
    var oldItem = getActiveNavItem( $(this).closest('[role=toclist]')[0] );

    if ( newItem === oldItem) return;

    deactivateNavItem(oldItem);
    activateNavItem(newItem);
    closeNavList(newItem);

    loadNavLink(this);
})

$('.nav-toc > .nav-item > a[data-toggle=collapse]').each( function () {
    var icon = $(this).children('i');
    var target = $(this).attr('data-target');

    $(target).on('hide.bs.collapse', function () {
        $(icon).addClass('fa-rotate-180');
    });
    $(target).on('show.bs.collapse', function () {
        $(icon).removeClass('fa-rotate-180');
    });
});
