<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

session_start();
header('Content-Type: application/json');

// ===================== CSRF CHECK =====================
require __DIR__ . '/../utils/csrf.php';
csrf_protect();



$fname = trim($_POST['first_name'] ?? '');
$lname = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

$errors = [];

// ===================== VALIDATION =====================
if (empty($fname)) {
    $errors[] = "First name required";
} elseif (! preg_match("/^[a-zA-Z ]+$/", $fname)) {
    $errors[] = "First name must contain only letters";
}

if (empty($lname)) {
    $errors[] = "Last name required";
}

if (empty($email)) {
    $errors[] = "Email is required";
} elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

if (empty($pass)) {
    $errors[] = "Password is required";
} elseif (strlen($pass) < 6) {
    $errors[] = "Password must be at least 6 characters long";
}

if (! empty($errors)) {
    echo json_encode([
        'status'  => 'error',
        'message' => implode(', ', $errors),
    ]);
    exit;
}

// ===================== CHECK EXISTING EMAIL =====================
$stmt = $conn->prepare("SELECT id FROM users_info WHERE email = ?");
if (! $stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database prepare failed']);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Email already registered. Please login or use another email',
    ]);
    exit;
}

$stmt->close();

// ===================== HASH PASSWORD =====================
$hashedPassword = password_hash($pass, PASSWORD_BCRYPT);

// ===================== INSERT USER =====================
$stmt = $conn->prepare("INSERT INTO users_info (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
if (! $stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database prepare failed']);
    exit;
}

$stmt->bind_param("ssss", $fname, $lname, $email, $hashedPassword);

if (! $stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to register user']);
    exit;
}

$user_id = $stmt->insert_id;
$stmt->close();

// ===================== SESSION STORE =====================
$_SESSION['temp_user_id']    = $user_id;
$_SESSION['temp_otp']        = rand(100000, 999999);
$_SESSION['temp_otp_expiry'] = time() + 300; // 5 minutes

// ===================== SEND EMAIL =====================
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = "aadityabansal522@gmail.com";
    $mail->Password   = "zmft oopr fqkm kaoc";
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom($mail->Username, 'OTP Verification');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Your OTP";
    $mail->Body    = "Your OTP is: <b>{$_SESSION['temp_otp']}</b>";

    $mail->send();

    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to send OTP email',
    ]);
}
