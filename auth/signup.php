<?php
session_start();
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="../style/signup.css">
</head>
<body>

<div class="card">
    <h2>สมัครสมาชิก</h2>

    <?php if ($error): ?>
        <div class="message error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="message success-box"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="signup-db.php" onsubmit="return validateForm()">

        <div class="input-group">
            <label>ชื่อผู้ใช้</label>
            <div class="input-wrapper">
                <input type="text" name="username" id="username" required placeholder="Username">
            </div>
        </div>

        <div class="input-group">
            <label>อีเมล</label>
            <div class="input-wrapper">
                <input type="email" name="email" id="email" required placeholder="example@gmail.com">
            </div>
        </div>

        <div class="input-group">
            <label>รหัสผ่าน</label>
            <div class="input-wrapper">
                <input type="password" name="password" id="password" required placeholder="••••••••">
                <ion-icon name="eye-outline" class="toggle-icon" data-target="password"></ion-icon>
            </div>
        </div>

        <div class="input-group">
            <label>ยืนยันรหัสผ่าน</label>
            <div class="input-wrapper">
                <input type="password" name="confirmpassword" id="confirmpassword" required placeholder="••••••••">
            </div>
        </div>

        <button type="submit" name="signup">สมัครสมาชิก</button>
    </form>

    <p style="margin-top: 15px;">มีบัญชีแล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
</div>

<script>
function validateForm() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmpassword = document.getElementById('confirmpassword').value;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert("กรุณากรอกอีเมลให้ถูกต้อง");
        return false;
    }

    if (password !== confirmpassword) {
        alert("รหัสผ่านไม่ตรงกัน");
        return false;
    }

    return true;
}

// Toggle password icon
const toggleIcons = document.querySelectorAll('.toggle-icon');
toggleIcons.forEach(icon => {
    icon.addEventListener('click', () => {
        const targetId = icon.dataset.target;
        const field = document.getElementById(targetId);
        if (field.type === "password") {
            field.type = "text";
            icon.name = "eye-off-outline";
        } else {
            field.type = "password";
            icon.name = "eye-outline";
        }
    });
});
</script>

</body>
</html>
