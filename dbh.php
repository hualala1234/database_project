<?php
// 資料庫連線參數
$servername = "localhost";      // 資料庫伺服器
$username = "root";             // 資料庫使用者名稱
$password = "";                 // 資料庫密碼
$dbname = "database";   // 資料庫名稱

// 建立資料庫連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
