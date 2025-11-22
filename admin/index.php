<?php
require "../lib/session.php";
require "../config/db.php";

if ($role != 'admin') {
    header("Location: ../index.php");
    exit;
}

$activityImgW = 200;
$activityImgH = 200;
$uploadError = '';
$uploadPATH = '../uploads/';

// Add new activity
if(isset($_POST["new_name"]) && $_POST["new_name"]!="" && isset($_POST["new_key"])){
    $imgPath = '';
    if (!empty($_FILES['new_img']['name'])) {
        // Check for upload errors
        if ($_FILES['new_img']['error'] !== UPLOAD_ERR_OK) {
            $errMsg = "Unknown upload error";
            switch($_FILES['new_img']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errMsg = "File too large. Maximum size: " . ini_get('upload_max_filesize');
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errMsg = "File upload was incomplete";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errMsg = "No file was uploaded";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errMsg = "Missing temporary folder";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errMsg = "Failed to write file to disk";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errMsg = "File upload stopped by extension";
                    break;
            }
            $uploadError = "Upload error: " . $errMsg;
        } else if (!empty($_FILES['new_img']['tmp_name']) && is_uploaded_file($_FILES['new_img']['tmp_name'])) {
            if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
            $fileName = basename($_FILES['new_img']['name']);
            $targetPath = $uploadPATH . time() . "_" . preg_replace('/[^\w.]+/', '', $fileName);
            $tmpName = $_FILES['new_img']['tmp_name'];
            [$w, $h, $type] = @getimagesize($tmpName) ?: [0,0,0];
            
            if($w && $h && $type) {
                // Always resize to reduce file size (handles large files)
                switch($type){
                    case IMAGETYPE_JPEG: $src = @imagecreatefromjpeg($tmpName); break;
                    case IMAGETYPE_PNG: $src = @imagecreatefrompng($tmpName); break;
                    case IMAGETYPE_GIF: $src = @imagecreatefromgif($tmpName); break;
                    default: $src = false;
                }
                if($src) {
                    $dst = imagescale($src, $activityImgW, $activityImgH);
                    if($dst && imagejpeg($dst, $targetPath, 85)) {
                        imagedestroy($src); 
                        imagedestroy($dst);
                        $imgPath = $targetPath;
                    } else {
                        $uploadError = "Failed to resize and save image";
                        imagedestroy($src);
                        if($dst) imagedestroy($dst);
                    }
                } else {
                    $uploadError = "Invalid or unsupported image format";
                }
            } else {
                $uploadError = "Could not read image dimensions";
            }
        } else {
            $uploadError = "File upload failed - no temporary file";
        }
    }
    if(empty($uploadError)) {
        $stmt = $pdo->prepare("INSERT INTO activities(name, staff_key, img_url) VALUES(?,?,?)");
        $stmt->execute([$_POST["new_name"], $_POST["new_key"], $imgPath]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Update activity name + image
if(isset($_POST["edit_id"]) && isset($_POST["edit_name"]) && isset($_POST["edit_key"])){
    $imgPath = $_POST['existing_img_url'] ?? '';
    $editError = '';
    if (!empty($_FILES['edit_img']['name'])) {
        // Check for upload errors
        if ($_FILES['edit_img']['error'] !== UPLOAD_ERR_OK) {
            $errMsg = "Unknown upload error";
            switch($_FILES['edit_img']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errMsg = "File too large. Maximum size: " . ini_get('upload_max_filesize');
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errMsg = "File upload was incomplete";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errMsg = "No file was uploaded";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errMsg = "Missing temporary folder";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errMsg = "Failed to write file to disk";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errMsg = "File upload stopped by extension";
                    break;
            }
            $editError = "Upload error: " . $errMsg;
        } else if (!empty($_FILES['edit_img']['tmp_name']) && is_uploaded_file($_FILES['edit_img']['tmp_name'])) {
            if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
            $fileName = basename($_FILES['edit_img']['name']);
            $targetPath = $uploadPATH . time() . "_" . preg_replace('/[^\w.]+/', '', $fileName);
            $tmpName = $_FILES['edit_img']['tmp_name'];
            [$w, $h, $type] = @getimagesize($tmpName) ?: [0,0,0];
            
            if($w && $h && $type) {
                // Always resize to reduce file size (handles large files)
                switch($type){
                    case IMAGETYPE_JPEG: $src = @imagecreatefromjpeg($tmpName); break;
                    case IMAGETYPE_PNG: $src = @imagecreatefrompng($tmpName); break;
                    case IMAGETYPE_GIF: $src = @imagecreatefromgif($tmpName); break;
                    default: $src = false;
                }
                if($src) {
                    $dst = imagescale($src, $activityImgW, $activityImgH);
                    if($dst && imagejpeg($dst, $targetPath, 85)) {
                        // Delete old image if exists
                        if($imgPath && file_exists($imgPath)) {
                            @unlink($imgPath);
                        }
                        imagedestroy($src); 
                        imagedestroy($dst);
                        $imgPath = $targetPath;
                    } else {
                        $editError = "Failed to resize and save image";
                        imagedestroy($src);
                        if($dst) imagedestroy($dst);
                    }
                } else {
                    $editError = "Invalid or unsupported image format";
                }
            } else {
                $editError = "Could not read image dimensions";
            }
        } else {
            $editError = "File upload failed - no temporary file";
        }
    }
    if(empty($editError)) {
        $stmt = $pdo->prepare("UPDATE activities SET name=?, staff_key=?, img_url=? WHERE id=?");
        $stmt->execute([$_POST["edit_name"], $_POST["edit_key"], $imgPath, $_POST["edit_id"]]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $uploadError = $editError;
    }
}

// Delete activity
if(isset($_GET["delete"])){
    $id = intval($_GET["delete"]);
    // Delete the image file
    $stmt = $pdo->prepare("SELECT img_url FROM activities WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if($row && $row['img_url']) {
        $img_file = $row['img_url'];
        if(file_exists($img_file)) unlink($img_file);
    }
    $stmt = $pdo->prepare("DELETE FROM activities WHERE id=?");
    $stmt->execute([$id]);
}

$activities = $pdo->query("SELECT * FROM activities ORDER BY id")->fetchAll();
?>

<h2>Manage Activities</h2>

<?php if (!empty($uploadError)): ?>
<div style="color: red; padding: 10px; background: #ffe0e0; border: 1px solid red; margin: 10px 0;">
    Error: <?= htmlspecialchars($uploadError) ?>
</div>
<?php endif; ?>

<h3>Add New Activity</h3>
<form method="POST" enctype="multipart/form-data">
    Name: <input type="text" name="new_name" required>
    Staff Key: <input type="text" name="new_key" required>
    Image: <input type="file" name="new_img" accept="image/*">
    <button type="submit">Add</button>
</form>

<h3>Existing Activities</h3>
<ul>
<?php foreach($activities as $row): ?>
<li>
    <form style="display:inline;" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
        Name: <input type="text" name="edit_name" value="<?= htmlspecialchars($row['name']) ?>">
        Staff Key: <input type="text" name="edit_key" value="<?= htmlspecialchars($row['staff_key']) ?>">
        Image: <input type="file" name="edit_img" accept="image/*">
        <?php
            $img_url = $row['img_url'] ?? '';
            if ($img_url && file_exists($img_url)) echo '<img src="'.htmlspecialchars($img_url).'" alt="" style="max-width:60px;vertical-align:middle;">';
            echo '<input type="hidden" name="existing_img_url" value="'.htmlspecialchars($img_url).'">';
        ?>
        <button type="submit">Update</button>
    </form>
    <a href="?delete=<?= $row['id'] ?>">Delete</a>
</li>
<?php endforeach; ?>
</ul>
