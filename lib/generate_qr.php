<?php
require "phpqrcode/qrlib.php";

$user_id = $_GET["uid"] ?? '';
if(!$user_id) die("No user ID");

// Return just user id in QR (legacy mode)
$data = "USER:$user_id";
header("Content-Type: image/png");
QRcode::png($data);
