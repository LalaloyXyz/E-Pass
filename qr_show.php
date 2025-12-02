<?php
require "lib/session.php";
require "config/db.php";
include 'widget/bottom_nav.php';

$fullname = $_SESSION['user']['username'] ?? 'ผู้ใช้';
    
if (isset($_POST['logout'])) {
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style/logout.css">
  <link rel="stylesheet" href="style/qr_show.css">
  <title>รหัส QR ของคุณ</title>
</head>
<body>

  <div class="page-wrapper">
    <div class="qr-container">

      <header class="app-header">
        <form method="POST">
            <button class="logout-btn" name="logout" title="ออกจากระบบ">
                <span aria-hidden="true">&times;</span>
            </button>
        </form>
        <h1>ยืนยันด้วย QR</h1>
      </header>

      <main class="qr-content-card">
          <h2>รหัสยืนยันของคุณ</h2>

          <div class="qr-code-display">
              <img 
                  src="lib/generate_qr.php?uid=<?= urlencode($userId) ?>" 
                  alt="รหัส QR ยืนยันส่วนตัว" 
                  class="qr-image"
              >
          </div>
          <div class="user-info">
              <p><b>ชื่อ:</b> <?= htmlspecialchars($fullname) ?></p>
          </div>
          
          <p class="instruction-text">
              สตาฟจะสแกน <b>รหัส QR</b> เพื่อตรวจสอบ<br>สถานะกิจกรรมของคุณ
          </p>

          <a href="index.php" class="status-link">
              <span class="icon">➜</span> ดูสถานะกิจกรรม
          </a>
      </main>

    </div>
  </div>

  <?php include 'widget/bottom_nav.php'; ?>

</body>
</html>
