<?php
$host   = 'localhost';
$user   = 'root';
$pass   = '';
$dbname = 'login_deliveryPerson';  // ← 這行絕對不能漏

// 建立連線
$conn = new mysqli($host, $user, $pass, $dbname);

// 確認連線
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
