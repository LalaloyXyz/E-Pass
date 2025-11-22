<?php
session_start();
require '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>

<hr>
<a href="login.php">Login</a> | <a href="register.php">Sign-up</a>
<br><br>

<h3>สมัครสมาชิก</h3>

<form action="signup-db.php" method="POST">

    <label>ชื่อ-นามสกุล:</label><br>
    <input type="text" name="username"><br><br>

    <label>Email:</label><br>
    <input type="email" name="email"><br><br>

    <label>Password:</label><br>
    <input type="password" name="password"><br><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="confirmpassword"><br><br>

    <button type="submit" name="signup">Sign Up</button>

</form>

</body>
</html>
