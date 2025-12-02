<?php
require "../lib/session.php";
require "../config/db.php";

if ($role != 'admin' && $role != 'staff') {
    header("Location: ../index.php");
    exit;
}

// Get all activities from database
$activities = $pdo->query("SELECT * FROM activities ORDER BY id")->fetchAll();

if (isset($_POST['logout'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกกิจกรรม - Staff</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/index_staff.css">
    <link rel="stylesheet" href="../style/logout.css">
</head>
<body>
    <form method="POST">
        <button class="logout-btn" name="logout">×</button>
    </form>

    <div class="container">
        <h1>เลือกกิจกรรม</h1>
        
        <?php if (empty($activities)): ?>
            <div class="empty-state">
                <p>ยังไม่มีกิจกรรมในระบบ</p>
            </div>
        <?php else: ?>
            <div class="activities-grid">
                <?php foreach($activities as $activity): ?>
                    <a href="staff_base.php?name=<?= urlencode($activity['name']) ?>" class="activity-card">
                        <?php if (!empty($activity['img_url'])): ?>
                            <img src="../<?= htmlspecialchars($activity['img_url']) ?>" alt="<?= htmlspecialchars($activity['name']) ?>">
                        <?php else: ?>
                            <div class="no-image">📋</div>
                        <?php endif; ?>
                        <div class="activity-name"><?= htmlspecialchars($activity['name']) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>