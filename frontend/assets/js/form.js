$(document).ready(function () {

    $("#contactForm").validationEngine();

    $('#contactForm').submit(function (e) {

        if (!$("#contactForm").validationEngine('validate')) { //jquery Validation to validate all input field with validate[]
            e.preventDefault();
            return false;
        }

        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this);

        $('#success').addClass('d-none').text('');

        $.ajax({
            url: 'save_contact_ajax.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',

            success: function (response) {
                if (response.status === 'success') {
                    $('#success')
                        .removeClass('d-none alert-danger')
                        .addClass('alert alert-success')
                        .text(response.message);

                    form[0].reset();
                } else {
                    $('#success')
                        .removeClass('d-none alert-success')
                        .addClass('alert alert-danger')
                        .text("Validation failed on server.");
                }
            },

            error: function () {
                $('#success')
                    .removeClass('d-none alert-success')
                    .addClass('alert alert-danger')
                    .text("Server Error!");
            }
        });
    });
});