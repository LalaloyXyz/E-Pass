<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Clean input ---
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // --- Validate email ---
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "รูปแบบอีเมลไม่ถูกต้อง";
        header("Location: login.php");
        exit;
    }

    try {
        // --- Check if email exists ---
        $stmt = $pdo->prepare("SELECT id, username, email, password, role 
                               FROM users 
                               WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no user found
        if (!$user || empty($user['email'])) {
            $_SESSION['login_error'] = "ไม่พบบัญชีผู้ใช้นี้ในระบบ";
            header("Location: login.php");
            exit;
        }

        $loginSuccess = false;

        // 1) Try hashed password first
        if (password_verify($password, $user['password'])) {
            $loginSuccess = true;
        }
        // 2) Allow plain passwords only for admin/staff legacy accounts
        elseif (in_array($user['role'], ['admin', 'staff']) && $password === $user['password']) {
            $loginSuccess = true;
        }

        // If password incorrect
        if (!$loginSuccess) {
            $_SESSION['login_error'] = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
            header("Location: login.php");
            exit;
        }

        // --- Login OK: set session ---
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'email'    => $user['email'],
            'role'     => $user['role']
        ];

        // --- Redirect by role ---
        switch ($user['role']) {
            case 'admin':
                header("Location: ../admin/index.php");
                break;

            case 'staff':
                header("Location: ../staff/index.php");
                break;

            default:
                header("Location: ../index.php");
                break;
        }
        exit;

    } catch (PDOException $e) {
        $_SESSION['login_error'] = "ระบบมีปัญหา กรุณาลองใหม่อีกครั้ง";
        header("Location: login.php");
        exit;
    }
}
?>
