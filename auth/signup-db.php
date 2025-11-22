<?php
session_start();
require '../config/db.php'; 

if (isset($_POST['signup'])) {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];

    if (empty($username) || empty($email) || empty($password) || empty($confirmpassword)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: signup.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email address.";
        header("Location: signup.php");
        exit;
    }

    if ($password !== $confirmpassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: signup.php");
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['error'] = "Email already registered.";
        header("Location: signup.php");
        exit;
    }

    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $result = $stmt->execute([$username, $email, $hashedPassword, 'user']);

    if ($result) {
        $_SESSION['success'] = "Registration successful. Please login.";
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: signup.php");
        exit;
    }
} else {
    header("Location: signup.php");
    exit;
}
