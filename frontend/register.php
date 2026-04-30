<?php
    session_start();

    if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <div class="container py-5">

        <div class="row">

            <!-- LEFT -->
            <div class="col-md-5 mb-4">
                <h3>Join Us</h3>
                <p>Create an account to unlock all features and enjoy a seamless experience on our platform.</p>

                <p>* Easy product browsing and ordering</p>

                <p>* Secure and fast checkout process</p>

                <p>Already have an account? <a href="login.php">Login here</a></p>

                <img src="assets/images/register.png" class="img-fluid mt-3" alt="Register Image">
            </div>

            <!-- RIGHT -->

            <div class="col-md-7">
                <h3>Registration</h3>
                <p class="text-muted">* Fields are mandatory</p>
                <div id="success" class="alert d-none">
                    <!-- success -->
                </div>

                <form id="registerForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="mb-3">
                        <input type="text" name="first_name" id="first_name" class="form-control"
                            placeholder="First Name">
                        <div class="invalid-feedback" id="fnameError"></div>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name">
                        <div class="invalid-feedback" id="lnameError"></div>
                    </div>

                    <div class="mb-3">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>

                    <div class="mb-3">
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="Password">
                        <div class="invalid-feedback" id="passError"></div>
                    </div>

                    <div class="mb-3">
                        <input type="password" id="confirm_password" class="form-control"
                            placeholder="Confirm Password">
                        <div class="invalid-feedback" id="cpassError"></div>
                    </div>

                    <button class="btn btn-primary">Register</button>
                </form>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/register.js"></script>

</body>

</html>