<?php
// db.php
$host = "localhost"; // 資料庫主機
// $host = "127.0.0.1";
$username = "root";  // 資料庫使用者
$password = "";      // 資料庫密碼
$dbname = "junglebite"; // 資料庫名稱

// 建立與資料庫的連線
$conn = new mysqli($host, $username, $password, $dbname);

// 檢查連線是否成功
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
