<?php
require __DIR__ . '/../config/config.php';

session_start();
header('Content-Type: application/json');

// CSRF CHECK
require __DIR__ . '/../utils/csrf.php';
csrf_protect();

// SANITIZE INPUT
$email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
$password = $_POST['password'] ?? '';

$errors = [];

// VALIDATION
if ($email === '') {
    $errors[] = "Email required";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

if ($password === '') {
    $errors[] = "Password required";
}

if ($errors) {
    echo json_encode(['status'=>'error','message'=>implode(', ', $errors)]);
    exit;
}

// CHECK USER
$stmt = $conn->prepare("SELECT id, password, is_email_verified, first_name FROM users_info WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status'=>'error','message'=>'Invalid email or password']);
    exit;
}

$user = $result->fetch_assoc();

// VERIFY PASSWORD
if (!password_verify($password, $user['password'])) {
    echo json_encode(['status'=>'error','message'=>'Invalid email or password']);
    exit;
}

// CHECK EMAIL VERIFIED
if ($user['is_email_verified'] == 0) {
    echo json_encode([
        'status'=>'error',
        'message'=>'Please verify your email before login'
    ]);
    exit;
}

// LOGIN SUCCESS → STORE SESSION
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['first_name'];

echo json_encode([
    'status'=>'success',
    'message'=>'Login successful! Welcome '.$user['first_name']
]);