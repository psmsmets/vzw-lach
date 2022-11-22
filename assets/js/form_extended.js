function initSubmit() {
   $('#submit').prop('disabled', false);
   $("#submit").html('<i class="fas fa-paper-plane"></i> Verzend');
}

// Disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Append to form
    $(".needs-validation").append('<div class="form-group row pt-3"><div class="col-sm-10"><button type="submit" class="btn btn-primary" id="submit" value="Send"></button><small id="submitHelp" class="form-text text-muted d-none">Gelieve de pagina <strong>niet</strong> te herladen tijdens het verzenden.</small></div></div>');
    // Set submit button
    initSubmit();
    // Replace label by href 
    $("label[for$='_form_terms']").html("Ik heb het <a href=\"/privacy\">privacybeleid</a> gelezen en ga hiermee akkoord.");
    // Bootstrap custom-select fix (attribute ends with)
    $('select').addClass('custom-select');
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        $('#submit').prop('disabled', 'disabled');
        $('#submit').html('<i class="fas fa-sync-alt"></i> Verwerken');
        $('#submitHelp').removeClass( "d-none" );
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
          initSubmit();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
