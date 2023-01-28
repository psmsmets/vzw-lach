// event 
$(document).ready(function(){

    // func
    $('#toggle-past-events').click(function(e) {
        var icon = $(this).children("i");

        if ($(icon).hasClass("bi-eye-slash-fill")){
            $('.past').show(400);
            $(icon).removeClass("bi-eye-slash-fill");
            $(icon).addClass("bi-eye-fill");
        } else {
            $('.past').hide(400);
            $(icon).removeClass("bi-eye-fill");
            $(icon).addClass("bi-eye-slash-fill");
        }
    });
    $('.past').hide();
});
