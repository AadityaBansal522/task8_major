<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>Navbar</title>
</head>
<body>

<!-- LOADER -->
<div id="loader" 
     class="position-fixed top-0 start-0 w-100 vh-100 d-none d-flex justify-content-center align-items-center"
     style="backdrop-filter: blur(6px); z-index: 9999;">
    
    <!-- Your GIF -->
    <img src="assets/images/ajax-loader.gif" alt="Loading..." width="50">
</div>

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light px-3">
        
        <!-- Logo -->
        <a class="navbar-brand" href="#">
            <img src="assets/images/logo-dark.png" height="40"/>
        </a>

        <!-- Toggle button (for mobile) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="./index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="register.php">Register</a>
                </li>
            </ul>
        </div>

    </nav>
</header>

<script>
    $(document).ajaxStart(function (){
        $("#loader").removeClass("d-none");
    }).ajaxStop(function () {
        $("#loader").addClass("d-none");
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>