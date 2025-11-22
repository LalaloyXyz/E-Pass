<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "รูปแบบอีเมลไม่ถูกต้อง";
        header("Location: login.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $loginSuccess = false;

        if ($user) {
            // First, try hashed password
            if (password_verify($password, $user['password'])) {
                $loginSuccess = true;
            } 
            // If hashed check fails, allow plain password for admin or staff only
            elseif (in_array($user['role'], ['admin', 'staff']) && $password === $user['password']) {
                $loginSuccess = true;
            }
        }

        if ($loginSuccess) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];

            if ($user['role'] === 'admin') {
                header("Location: ../admin/index.php");
            } elseif ($user['role'] === 'staff') {
                header("Location: ../staff/index.php");
            } else {
                header("Location: ../index.php");
            }
            exit;
        } else {
            $_SESSION['login_error'] = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
            header("Location: login.php");
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['login_error'] = "เกิดข้อผิดพลาดในการเข้าสู่ระบบ";
        header("Location: login.php");
        exit;
    }
}
?>
