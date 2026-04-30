<?php
session_start();

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="row">

        <!-- LEFT -->
        <div class="col-md-5 mb-4">
            <h3>Contact</h3>
            <p>Feel free to reach out to us through any of the methods below:</p>

            <p>Email:<a href="mailto:contact@orientaloutsourcing.com">contact@orientaloutsourcing.com</a></p>

            <p>Phone:<a href="tel:+1234567890">+123 456 7890</a></p>

            <p>Address: SCO 64-b, City Heart, Kharar, Punjab, India, 140301</p>

            <img src="assets/images/contact.png" class="img-fluid mt-3" alt="Contact Image">
        </div>

        <!-- RIGHT -->
        <div class="col-md-7">
            <h3>Send Message</h3>
            <p class="text-muted">* Fields are mandatory</p>

            <div id="success" class="alert d-none"></div>

            <form id="contactForm" enctype="multipart/form-data" method="POST">

                <!-- CSRF TOKEN -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label>Name *</label>
                    <input type="text" name="name" class="form-control" id="name">
                    <div class="invalid-feedback" id="nameError"></div>
                </div>

                <div class="mb-3">
                    <label>Email *</label>
                    <input type="text" name="email" class="form-control" id="email">
                    <div class="invalid-feedback" id="emailError"></div>
                </div>

                <div class="mb-3">
                    <label>Message *</label>
                    <textarea name="message" class="form-control" id="message"></textarea>
                    <div class="invalid-feedback" id="messageError"></div>
                </div>

                <div class="mb-3">
                    <label>Upload File (optional)</label>
                    <input type="file" name="file" id="file" class="form-control">
                    <div class="invalid-feedback" id="fileError"></div>
                </div>

                <button class="btn btn-primary">Submit</button>
            </form>

        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/form.js"></script>

</body>
</html>