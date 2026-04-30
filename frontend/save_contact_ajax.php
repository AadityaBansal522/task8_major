<?php

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../vendor/autoload.php'; 

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

session_start();
header('Content-Type: application/json');


// CSRF CHECK importing from ./utils folder;

require __DIR__ . '/../utils/csrf.php';
csrf_protect();


// INPUT SANITIZATION
$name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

$errors = [];


// INPUT VALIDATIONs
if ($name === "") {
    $errors['name'] = "Name is required";
}

if ($email === "") {
    $errors['email'] = "Email is required";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $errors['email'] = "Invalid email format";
}

if ($message === "") {
    $errors['message'] = "Message is required";
}

// If validation fails → return errors
if (!empty($errors)) {
    echo json_encode([
        'status' => 'error',
        'errors' => $errors,
    ]);
    exit;
}

// FILE UPLOAD HANDLING
$filePath = null;

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {

    $allowed = ['doc', 'pdf', 'xlsx'];
    $maxSize = 2 * 1024 * 1024; // 2MB limit

    $fileName = $_FILES['file']['name'];
    $fileTmp  = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];

    // FILE SIZE VALIDATION
    if ($fileSize > $maxSize) {
        echo json_encode([
            'status' => 'error',
            'errors' => ['file' => 'File size must be less than 2MB']
        ]);
        exit;
    }

    // FILE TYPE VALIDATION
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode([
            'status' => 'error',
            'errors' => ['file' => 'Invalid file type']
        ]);
        exit;
    }

    $uploadDir = __DIR__ . '/uploads/';

    // Create folder if not exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newName = time() . "_" . basename($fileName);

    $filePath = $uploadDir . $newName;

    move_uploaded_file($fileTmp, $filePath);
}


// DB INSERT
$stmt = $conn->prepare("INSERT INTO contacts_info (contact_name, contact_email, contact_message, file_path) VALUES (?, ?, ?, ?)");

$stmt->bind_param("ssss", $name, $email, $message, $filePath);

// Execute query
if (!$stmt->execute()) {
    echo json_encode([
        'status' => 'error',
        'errors' => ['db' => 'Database error occurred'],
    ]);
    exit;
}

$stmt->close();


// Form Submition Email Sending
$mailSent = false;

try {
    $mail = new PHPMailer(true);

    // SMTP CONFIGURATION
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = "aadityabansal522@gmail.com";
    $mail->Password   = 'zmft oopr fqkm kaoc';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // EMAIL HEADERS
    $mail->setFrom($mail->Username, 'Contact Form');
    $mail->addAddress($mail->Username);          // Receiver
    $mail->addReplyTo($email, $name);        

    // EMAIL CONTENT
    $mail->isHTML(true);
    $mail->Subject = "New Contact Message";
    $mail->Body    = "
        <h4>New Message</h4>
        <p><b>Name:</b> {$name}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Message:</b> {$message}</p>
    ";

    // Attach file if uploaded
    if ($filePath) {
        $mail->addAttachment($filePath);
    }

    // Send email
    $mail->send();
    $mailSent = true;

} catch (Exception $e) {
    $mailSent = false;
}

echo json_encode([
    'status'  => 'success',
    'message' => $mailSent 
        ? 'Message saved & email sent!' 
        : 'Message saved but email failed.'
]);