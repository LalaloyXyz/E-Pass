<?php
require "lib/session.php";
require "config/db.php";
include 'widget/bottom_nav.php';

// Config
$IMG_W = 100;
$IMG_H = 100;
$SUCCESS_THRESHOLD = 6;
$show_text = true;

// Get all activities
$activities = $pdo->query("SELECT * FROM activities ORDER BY id")->fetchAll();

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: auth/login.php");
    exit;
}

// Prepare log query
$logStmt = $pdo->prepare("SELECT 1 FROM activity_log WHERE user_id=? AND activity_id=?");

// Count completed activities
$doneCount = 0;
foreach ($activities as $activity) {
    $logStmt->execute([$userId, $activity['id']]);
    if ($logStmt->fetch()) {
        $doneCount++;
    }
}

// Check if threshold reached
$showSuccess = $doneCount >= $SUCCESS_THRESHOLD;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Activity Status</title>
<link rel="stylesheet" href="style/index.css">
</head>
<body>
<form method="POST">
    <button class="logout-btn" name="logout">×</button>
</form>

<div class="page-content">
    <?php if ($showSuccess): ?>
        <div class="redeem-btn">REDEEM !</div>
    <?php endif; ?>

    <div class="activities-frame">
        <ul>
        <?php foreach ($activities as $activity): ?>
            <?php
                $logStmt->execute([$userId, $activity['id']]);
                $completed = $logStmt->fetch();
                $class = $completed ? "done" : "not-done";
                $imgStyle = $completed ? "" : "gray";
            ?>
            <li class="<?= $class ?>">
                <?php if (!empty($activity['img_url'])): ?>
                    <img 
                        src="<?= htmlspecialchars($activity['img_url']) ?>" 
                        class="<?= $imgStyle ?>">
                <?php endif; ?>
                <?php if (!$completed): ?>
                    <div class="activity-name"><?= htmlspecialchars($activity['name']) ?></div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>

</body>
</html>
