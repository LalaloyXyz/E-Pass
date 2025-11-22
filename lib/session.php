<?php
session_start();

if(!isset($_SESSION["user"])){
    header("Location: auth/login.php");
    exit;
}

$userId   = $_SESSION['user']['id'];
$fullname = $_SESSION['user']['fullname'] ?? '';
$email    = $_SESSION['user']['email'];
$role     = $_SESSION['user']['role'];
?>
