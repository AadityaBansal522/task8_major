$(document).ready(function () {

    $('#contactForm').submit(function (e) {
        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this); // IMPORTANT

        let name = $('#name').val().trim();
        let email = $('#email').val().trim();
        let message = $('#message').val().trim();
        let file = $('#file')[0].files[0];

        let isValid = true;

        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#success').addClass('d-none').text('');

        // -------- VALIDATION --------

        if (name === '') {
            $('#name').addClass('is-invalid');
            $('#nameError').text('Name is required');
            isValid = false;
        }

        if (email === '') {
            $('#email').addClass('is-invalid');
            $('#emailError').text('Email is required');
            isValid = false;
        } else if (!validateEmail(email)) {
            $('#email').addClass('is-invalid');
            $('#emailError').text('Invalid email');
            isValid = false;
        }

        if (message === '') {
            $('#message').addClass('is-invalid');
            $('#messageError').text('Message is required');
            isValid = false;
        }

        // FILE VALIDATION
        if (file) {
            let allowed = ['doc', 'pdf', 'xlsx'];
            let ext = file.name.split('.').pop().toLowerCase();

            if (!allowed.includes(ext)) {
                $('#file').addClass('is-invalid');
                $('#fileError').text('Only doc, pdf, xlsx allowed');
                isValid = false;
            }
        }

        if (!isValid) return;

        // -------- AJAX --------

        $.ajax({
            url: 'save_contact_ajax.php',
            type: 'POST',
            data: formData,
            contentType: false,   // IMPORTANT
            processData: false,   // IMPORTANT
            dataType: 'json',

            success: function (response) {

                if (response.status === 'success') {
                    $('#success')
                        .removeClass('d-none alert-danger')
                        .addClass('alert alert-success')
                        .text(response.message);

                    form[0].reset();
                }
                else {
                    let errors = response.errors;

                    if (errors.name) {
                        $('#name').addClass('is-invalid');
                        $('#nameError').text(errors.name);
                    }

                    if (errors.email) {
                        $('#email').addClass('is-invalid');
                        $('#emailError').text(errors.email);
                    }

                    if (errors.message) {
                        $('#message').addClass('is-invalid');
                        $('#messageError').text(errors.message);
                    }

                    if (errors.file) {
                        $('#file').addClass('is-invalid');
                        $('#fileError').text(errors.file);
                    }
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

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
});