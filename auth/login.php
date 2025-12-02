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
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/login.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<div class="card">
    <h2>เข้าสู่ระบบ</h2>

    <?php if ($error): ?>
        <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="login-db.php" onsubmit="return validateForm()">

        <div class="input-group">
            <label>อีเมล</label>
            <input name="email" id="email" required placeholder="example@gmail.com">
        </div>

        <div class="input-group">
            <label>รหัสผ่าน</label>
            <div style="position: relative;">
                <input type="password" name="password" id="password" required placeholder="••••••••">
                <ion-icon 
                    name="eye-outline" 
                    id="toggle-password" 
                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 20px; color: #6E9F56;">
                </ion-icon>
            </div>
        </div>
        <button type="submit">เข้าสู่ระบบ</button>
    </form>

    <p style="margin-top: 15px;">ยังไม่มีบัญชี? <a href="signup.php">ลงทะเบียน</a></p>
</div>

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
            toggleBtn.textContent = "ซ่อนรหัสผ่าน";
        } else {
            passwordField.type = "password";
            toggleBtn.textContent = "แสดงรหัสผ่าน";
        }
    }
</script>
<script>
    const passwordField = document.getElementById('password');
    const toggleBtn = document.getElementById('toggle-password');

    toggleBtn.addEventListener('click', () => {
        if (passwordField.type === "password") {
            passwordField.type = "text";
             toggleBtn.name = "eye-off-outline"; // ไอคอนตาปิด
        } else {
            passwordField.type = "password";
            toggleBtn.name = "eye-outline"; // ไอคอนตาเปิด
        }
    });
</script>

</body>
</html>
