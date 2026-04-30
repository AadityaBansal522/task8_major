<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token (call this when rendering forms)
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}


function validate_csrf_token($token) {
    if (!isset($token) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

//Verification- CSRF
function csrf_protect() {
    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid CSRF token'
        ]);
        exit;
    }
}