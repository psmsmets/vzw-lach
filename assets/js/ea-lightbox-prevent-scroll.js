// Disabling scrolling of ea-lightbox modals
(function() {
    'use strict';
    window.addEventListener('load', function() {
        // Fetch all lightboxes
        var thumbs = document.getElementsByClassName('ea-lightbox-thumbnail');
        // Loop over them and prevent scrolling
        var noScroll = Array.prototype.filter.call(thumbs, function(thumb) {
            thumb.href = "javascript:void(0)";
        });
    }, false);
})();
