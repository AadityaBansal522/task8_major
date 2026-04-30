$(document).ready(function () {

    $('#registerForm').submit(function (e) {
        e.preventDefault();

        let formData = $(this).serialize();

        let isValid = true;

        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        let fname = $('#first_name').val().trim();
        let lname = $('#last_name').val().trim();
        let email = $('#email').val().trim();
        let pass = $('#password').val();
        let cpass = $('#confirm_password').val();

        if (fname === '') {
            $('#first_name').addClass('is-invalid');
            $('#fnameError').text('First name required');
            isValid = false;
        }

        if (lname === '') {
            $('#last_name').addClass('is-invalid');
            $('#lnameError').text('Last name required');
            isValid = false;
        }

        if (!validateEmail(email)) {
            $('#email').addClass('is-invalid');
            $('#emailError').text('Invalid email');
            isValid = false;
        }

        if (pass.length < 6) {
            $('#password').addClass('is-invalid');
            $('#passError').text('Min 6 characters');
            isValid = false;
        }

        if (pass !== cpass) {
            $('#confirm_password').addClass('is-invalid');
            $('#cpassError').text('Passwords do not match');
            isValid = false;
        }

        if (!isValid) return;

        $.post('save_register_ajax.php', formData, function (res) {

            if (res.status === 'success') {
                window.location.href = "verify_otp.php";
            } else {
                $('#success').removeClass('d-none alert-success')
                    .addClass('alert alert-danger')
                    .text(res.message);
            }

        }, 'json');
    });

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

});