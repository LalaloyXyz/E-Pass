<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {

    // --- Clean input ---
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmpassword = $_POST['confirmpassword'] ?? '';

    // --- Validate ---
    if ($username === '' || $email === '' || $password === '' || $confirmpassword === '') {
        $_SESSION['error'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
        header("Location: signup.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "รูปแบบอีเมลไม่ถูกต้อง";
        header("Location: signup.php");
        exit;
    }

    if ($password !== $confirmpassword) {
        $_SESSION['error'] = "รหัสผ่านไม่ตรงกัน";
        header("Location: signup.php");
        exit;
    }

    try {

        // 1) Check duplicate email
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn()) {
            $_SESSION['error'] = "อีเมลนี้ถูกใช้แล้ว";
            header("Location: signup.php");
            exit;
        }

        // 2) Insert user
        $sql = "INSERT INTO users (username, email, password, role) 
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $result = $stmt->execute([
            $username,
            $email,
            $hashedPassword,
            'user'
        ]);

        if ($result) {
            $_SESSION['success'] = "สมัครสมาชิกสำเร็จ โปรดเข้าสู่ระบบ";
            header("Location: login.php");
            exit;
        } else {
            $_SESSION['error'] = "สมัครสมาชิกไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
            header("Location: signup.php");
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "มีปัญหาในการเชื่อมต่อฐานข้อมูล";
        header("Location: signup.php");
        exit;
    }

} else {
    header("Location: signup.php");
    exit;
}
?>
