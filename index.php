<?php
require "lib/session.php";
require "config/db.php";
include 'widget/bottom_nav.php';

// Config
$IMG_W = 100;
$IMG_H = 100;
$SUCCESS_THRESHOLD = 6;
$show_text = true;

// Handle logout (only once)
if (isset($_POST['logout'])) {
    session_start();
    session_unset();
    session_destroy();
    header("Location: auth/login.php");
    exit;
}

// Optimized: Get all activities with completion status in a single query (fixes N+1 problem)
// This JOIN query is much faster than executing separate queries in a loop
$stmt = $pdo->prepare("
    SELECT 
        a.*,
        CASE WHEN al.user_id IS NOT NULL THEN 1 ELSE 0 END as completed
    FROM activities a
    LEFT JOIN activity_log al ON a.id = al.activity_id AND al.user_id = ?
    ORDER BY a.id
");
$stmt->execute([$userId]);
$activities = $stmt->fetchAll();

// Count completed activities
$doneCount = 0;
foreach ($activities as $activity) {
    if ($activity['completed']) {
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
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/index.css">
    <link rel="stylesheet" href="style/logout.css">
</head>
<body>

    <form method="POST">
        <button class="logout-btn" name="logout">×</button>
    </form>

    <div class="page-content">
        <?php if ($showSuccess): ?>
            <div class="redeem-btn">รับรางวัล</div>
        <?php endif; ?>

        <div class="activities-frame">
            <ul>
            <?php foreach ($activities as $activity): ?>
                <?php
                    $completed = (bool)$activity['completed'];
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
