<?php
session_start();
header('Content-Type: application/json');

require "config/db.php";

$qrdata = $_POST["qrdata"] ?? '';
$activity_name = $_POST["activity_name"] ?? '';

if (!$qrdata || !$activity_name) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบ']);
    exit;
}

if (strpos($qrdata, "USER:") !== 0) {
    echo json_encode(['success' => false, 'message' => 'QR Code ไม่ถูกต้อง']);
    exit;
}

$user_id = intval(str_replace("USER:", "", $qrdata));

// Lookup activity ID from name
$stmt = $pdo->prepare("SELECT id FROM activities WHERE name = ?");
$stmt->execute([$activity_name]);
$activity = $stmt->fetch();

if (!$activity) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบฐาน']);
    exit;
}

$activity_id = $activity['id'];

// Log activity (ignore duplicates)
$stmt = $pdo->prepare("INSERT IGNORE INTO activity_log(user_id, activity_id, done_at) VALUES(?,?,NOW())");
$affected = $stmt->execute([$user_id, $activity_id]);

// Check if it was a duplicate
if ($stmt->rowCount() === 0) {
    echo json_encode([
        'success' => false, 
        'message' => "User #$user_id เคยสแกนแล้ว"
    ]);
    exit;
}

echo json_encode([
    'success' => true, 
    'message' => "User #$user_id ผ่านฐาน: " . htmlspecialchars($activity_name)
]);
?>