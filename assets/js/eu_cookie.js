// eu cookie
$('#cookie-law-modal').modal('show')
var cookieBar = document.getElementById('cookie-law-modal'),
    button = document.getElementById('cookie-law-close-button');

function hideCookieBar() {
    cookieBar.style.display = 'none';
    $('#cookie-law-modal').modal('hide')
}

function setCookieAccepted() {
    expiry = new Date();
    expiry.setTime(expiry.getTime()+( 180 * 1000 * 60 * 60 * 24));
    document.cookie = "cookie_law_accepted=1; expires=" + expiry.toGMTString();
}

button.onclick = function() {
    hideCookieBar();
    setCookieAccepted();

    return false;
}
