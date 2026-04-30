<?php

// ================== CONFIG & DEPENDENCIES ==================
require __DIR__ . '/../config/config.php';       // Database connection
require __DIR__ . '/../vendor/autoload.php';     // PHPMailer (Composer autoload)

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;


// ================== SESSION + RESPONSE TYPE ==================
session_start();                                 // Start session (needed for CSRF)
header('Content-Type: application/json');        // Response will be JSON


// CSRF CHECK
require __DIR__ . '/../utils/csrf.php';
csrf_protect();


// ================== INPUT SANITIZATION (XSS PROTECTION) ==================
$name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

$errors = [];


// ================== SERVER-SIDE INPUT VALIDATION ==================
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

// ================== FILE UPLOAD HANDLING ==================
$filePath = null;

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {

    $allowed = ['doc', 'pdf', 'xlsx'];
    $maxSize = 2 * 1024 * 1024; // 2MB limit

    $fileName = $_FILES['file']['name'];
    $fileTmp  = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];

    // ================== FILE SIZE VALIDATION ==================
    if ($fileSize > $maxSize) {
        echo json_encode([
            'status' => 'error',
            'errors' => ['file' => 'File size must be less than 2MB']
        ]);
        exit;
    }

    // ================== FILE TYPE VALIDATION ==================
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode([
            'status' => 'error',
            'errors' => ['file' => 'Invalid file type']
        ]);
        exit;
    }

    $uploadDir = __DIR__ . '/uploads/';

    // Create folder if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $newName = time() . "_" . basename($fileName);

    $filePath = $uploadDir . $newName;

    // Move file from temp to uploads folder
    move_uploaded_file($fileTmp, $filePath);
}


// ================== DATABASE INSERT (PREPARED STATEMENT) ==================
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


// ================== EMAIL SENDING (PHPMailer) ==================
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
    $mail->addReplyTo($email, $name);            // User reply

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
    $mailSent = false; // Fail silently (could log error)
}


// ================== FINAL RESPONSE ==================
echo json_encode([
    'status'  => 'success',
    'message' => $mailSent 
        ? 'Message saved & email sent!' 
        : 'Message saved but email failed.'
]);