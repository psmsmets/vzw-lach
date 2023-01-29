// event 
$(document).ready(function(){

    // func
    $('#toggle-completed-adverts').click(function(e) {
        var icon = $(this).children("i");

        if ($(icon).hasClass("bi-eye-slash-fill")){
            $('.completed').show(400);
            $(icon).removeClass("bi-eye-slash-fill");
            $(icon).addClass("bi-eye-fill");
        } else {
            $('.completed').hide(400);
            $(icon).removeClass("bi-eye-fill");
            $(icon).addClass("bi-eye-slash-fill");
        }
    });
    $('.completed').hide();
});
