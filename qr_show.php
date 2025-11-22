<?php
require "lib/session.php";
require "config/db.php";
include 'widget/bottom_nav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Code</title>
</head>
<body>
    <h2>Your QR Code</h2>
    <img src="lib/generate_qr.php?uid=<?= urlencode($userId) ?>" width="200">
    Staff scan this QR to verify your activity.<br>
    <br><a href="index.php">View activity status</a>
</body>
</html>