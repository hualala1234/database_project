<?php
session_start();

// 取得目前登入的 cid
$cid = $_SESSION['cid'];

// 資料庫連線
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_customer";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 查詢該用戶的過敏資料
$sql = "SELECT c.cid, c.name, c.email, a.allergens, a.other_allergen
        FROM customer c
        LEFT JOIN allergy a ON c.cid = a.cid
        WHERE c.cid = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("i", $cid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "顧客 ID: " . $row['cid'] . "<br>";
    echo "顧客姓名: " . $row['name'] . "<br>";
    echo "顧客郵箱: " . $row['email'] . "<br>";
    echo "過敏原: " . $row['allergens'] . "<br>";
    echo "其他過敏原: " . $row['other_allergen'] . "<br>";
} else {
    echo "沒有找到顧客資料";
}

// 關閉連線
$stmt->close();
$conn->close();
?>
