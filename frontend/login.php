<?php
    session_start();

    // CSRF TOKEN
    if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <h3>Login</h3>

        <div id="success" class="alert d-none"></div>

        <form id="loginForm">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="mb-3">
                <input type="text" name="email" id="email" class="form-control validate[required,custom[email]]"
                    placeholder="Email">
                <div class="invalid-feedback" id="emailError"></div>
            </div>

            <div class="mb-3">
                <input type="password" name="password" id="password" class="form-control validate[required]"
                    placeholder="Password">
                <div class="invalid-feedback" id="passError"></div>
            </div>

            <button class="btn btn-primary">Login</button>
        </form>
    </div>

    <script src="assets/js/login.js"></script>

</body>

</html>