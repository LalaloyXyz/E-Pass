<?php
session_start();
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>เข้าสู่ระบบ</h2>

    <?php if ($error): ?>
        <div style="color:red;"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="login-db.php" onsubmit="return validateForm()">
        <div>
            <input name="email" id="email" required placeholder="อีเมล">
        </div>
        <div>
            <input type="password" name="password" id="password" required placeholder="รหัสผ่าน">
            <button type="button" id="toggle-password" onclick="togglePassword()">แสดง</button>
        </div>
        <div>
            <button type="submit">เข้าสู่ระบบ</button>
        </div>
    </form>

    <p>ยังไม่มีบัญชี? <a href="signup.php">ลงทะเบียน</a></p>

    <script>
        function validateForm() {
            const email = document.getElementById('email').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert("กรุณากรอกอีเมลให้ถูกต้อง");
                return false;
            }
            return true;
        }

        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleBtn = document.getElementById('toggle-password');
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleBtn.textContent = "ซ่อน";
            } else {
                passwordField.type = "password";
                toggleBtn.textContent = "แสดง";
            }
        }
    </script>
</body>
</html>
