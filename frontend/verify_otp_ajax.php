<?php
require __DIR__ . '/../config/config.php';
session_start();

header('Content-Type: application/json');

$enteredOtp = $_POST['otp'];

if ($enteredOtp == $_SESSION['temp_otp']) {

    $user_id = $_SESSION['temp_user_id'];

    $stmt = $conn->prepare("UPDATE users_info SET is_email_verified=1 WHERE id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();

    echo json_encode([
        'status'=>'success',
        'message'=>'Email verified! <a href="login.php">Click here to login</a>'
    ]);

} else {
    echo json_encode(['status'=>'error','message'=>'Invalid OTP']);
}