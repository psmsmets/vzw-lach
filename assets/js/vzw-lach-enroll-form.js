let acteurCheckbox = document.getElementById("associate_base_categoryPreferences_0");
let figurantCheckbox = document.getElementById("associate_base_categoryPreferences_1");

let extraFields = document.getElementsByClassName('acteur-figurant');

function hideExtraFields() {
    for (let i = 0; i < extraFields.length; i++) {
        extraFields[i].classList.add("d-none");
    }
}

function showExtraFields() {
    for (let i = 0; i < extraFields.length; i++) {
        extraFields[i].classList.remove("d-none");
    }
}

function ActeurFigurantIsChecked() {
    if ( acteurCheckbox.checked || figurantCheckbox.checked ) {
        showExtraFields();
    } else {
        hideExtraFields();
    }
}

if(typeof(acteurCheckbox) != 'undefined' && acteurCheckbox != null && typeof(figurantCheckbox) != 'undefined' && figurantCheckbox != null) {
    acteurCheckbox.addEventListener( "change", ActeurFigurantIsChecked);
    figurantCheckbox.addEventListener( "change", ActeurFigurantIsChecked);
    ActeurFigurantIsChecked();
}

function DisableSubmit() {
    var submit = document.getElementById("submit");
    submit.disabled = true;
    submit.classList.add('disabled');
    var loader = document.getElementById("loader");
    loader.classList.remove("d-none")
}

function EnableSubmit() {
    var submit = document.getElementById("submit");
    submit.disabled = false;
    submit.classList.remove('disabled');
    var loader = document.getElementById("loader");
    loader.classList.add("d-none")
}

// Disabling form submissions if there are invalid fields
(function() {
    'use strict';
    window.addEventListener('load', function() {
        // Set submit button
        EnableSubmit();
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                DisableSubmit();
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                    EnableSubmit();
                }
                //form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
