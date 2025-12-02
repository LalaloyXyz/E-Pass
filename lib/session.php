<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Configure session for better performance and security
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

if(!isset($_SESSION["user"])){
    header("Location: auth/login.php");
    exit;
}

$userId   = $_SESSION['user']['id'];
$fullname = $_SESSION['user']['username'] ?? '';
$email    = $_SESSION['user']['email'];
$role     = $_SESSION['user']['role'];
?>
