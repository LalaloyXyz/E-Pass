<?php
require "../lib/session.php";
require "../config/db.php";

if ($role != 'admin' && $role != 'staff') {
    header("Location: ../index.php");
    exit;
}

// Get and validate activity name
$activityName = $_GET['name'] ?? '';
if (!$activityName) die("No activity specified.");

$stmt = $pdo->prepare("SELECT * FROM activities WHERE name = ?");
$stmt->execute([$activityName]);
$activity = $stmt->fetch();
if (!$activity) die("Activity not found");

// Staff Key session logic
$sessionKey = "staff_base_{$activity['name']}_verified";
$isStaffVerified = $_SESSION[$sessionKey] ?? false;

if (!$isStaffVerified) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputKey = $_POST['staff_key'] ?? '';
        if ($inputKey === $activity['staff_key']) {
            $_SESSION[$sessionKey] = true;
            $isStaffVerified = true;
        } else {
            $error = "Staff Key ไม่ถูกต้อง";
        }
    }
}

if (!$isStaffVerified):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Key Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/camScan.css">
</head>
<body class="verify-bg">
    <div class="verify-container">
    <h2>ป้อน Staff Key (<?= htmlspecialchars($activity['name']) ?>)</h2>
    <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="staff_key" placeholder="Staff Key" required autocomplete="off">
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
    </div>
</body>
</html>
<?php
exit;
endif;
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สแกน QR Code</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../style/camScan.css">
<script src="https://unpkg.com/html5-qrcode"></script>

</head>
<body>

<div class="header">
    <div>
        <div><?= htmlspecialchars($activity['name']) ?></div>
        <small>จ่อ QR Code ให้กล้องอ่าน</small>
    </div>
    <button class="close-btn" onclick="stopScanner()">×</button>
</div>

<!-- Scanner Container -->
<div id="preview">
    <div id="videoContainer"></div>
</div>

<script>
let html5QrCode;
let scanning = true;
const activityName = "<?= htmlspecialchars($activity['name']) ?>";

function showToast(msg, type = "success") {
    const toast = document.createElement("div");
    toast.className = type === "error" ? "toast error" : "toast";
    toast.textContent = msg;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add("show"), 10);

    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

// QR Scanner Boot
document.addEventListener("DOMContentLoaded", async () => {

    html5QrCode = new Html5Qrcode("videoContainer");

    const cameras = await Html5Qrcode.getCameras();
    if (cameras.length === 0) {
        showToast("ไม่พบกล้อง", "error");
        return;
    }

    let backCamera = cameras.find(cam =>
        cam.label.toLowerCase().includes("back") ||
        cam.label.toLowerCase().includes("rear") ||
        cam.label.toLowerCase().includes("environment")
    );

    if (!backCamera) backCamera = cameras[0];

    html5QrCode.start(
        backCamera.id,
        { fps: 60, qrbox: 300 },
        onScanSuccess,
        () => {}
    );
});

async function onScanSuccess(qrText) {
    if (!scanning) return;
    scanning = false;

    showToast("พบ QR! กำลังตรวจสอบ...");

    try {
        // ส่งข้อมูลผ่าน AJAX แทนการ submit form
        const formData = new FormData();
        formData.append('qrdata', qrText);
        formData.append('activity_name', activityName);

        const response = await fetch('staff_verify.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message || "สแกนสำเร็จ!");
        } else {
            showToast(result.message || "เกิดข้อผิดพลาด", "error");
        }

    } catch (error) {
        showToast("เกิดข้อผิดพลาดในการเชื่อมต่อ", "error");
    }

    // เปิดให้สแกนต่อได้เลย ไม่ต้องรีเฟรช
    setTimeout(() => {
        scanning = true;
    }, 1500);
}

function stopScanner() {
    scanning = false;

    if (html5QrCode && html5QrCode._isScanning) {

        html5QrCode.stop(
            () => {   // success
                window.location.href = 'index.php';
            },
            (err) => { // fail
                console.warn("Stop failed:", err);
                window.location.href = 'index.php';
            }
        );

    } else {
        window.location.href = 'index.php';
    }
}
</script>

</body>
</html>