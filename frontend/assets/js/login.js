$(document).ready(function () {

    $('#loginForm').submit(function (e) {
        e.preventDefault();

        let email = $('#email').val().trim();
        let password = $('#password').val();

        let isValid = true;

        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#success').addClass('d-none').text('');

        if (email === '') {
            $('#email').addClass('is-invalid');
            $('#emailError').text('Email required');
            isValid = false;
        }

        if (password === '') {
            $('#password').addClass('is-invalid');
            $('#passError').text('Password required');
            isValid = false;
        }

        if (!isValid) return;

        $.post('login_ajax.php', $('#loginForm').serialize(), function (res) {

            if (res.status === 'success') {
                $('#success')
                    .removeClass('d-none alert-danger')
                    .addClass('alert alert-success')
                    .text(res.message);

                // redirect (optional)
                setTimeout(() => {
                    window.location.href = "index.php";
                }, 1500);

            } else {
                $('#success')
                    .removeClass('d-none alert-success')
                    .addClass('alert alert-danger')
                    .text(res.message);
            }

        }, 'json');
    });

});