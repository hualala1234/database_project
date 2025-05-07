<?php
ini_set('display_errors', 0);  // 禁用錯誤顯示
error_reporting(E_ALL);  // 記錄所有錯誤

session_start();
require_once("../dbh.php");

header('Content-Type: application/json');  // 確保返回 JSON 格式

$data = json_decode(file_get_contents("php://input"), true);

// 檢查必須的數據是否存在
if (!isset($data['pid']) || !isset($data['mid']) || !isset($data['quantity'])) {
    echo json_encode(["success" => false, "error" => "Missing parameters"]);
    exit;
}

$pid = intval($data['pid']);
$mid = intval($data['mid']);
$cid = $_SESSION['cid'] ?? null; // 登入時記得設置
$cartTime = $_SESSION['cartTime'] ?? date("Y-m-d H:i:s"); // 記住這一輪購物車時間

if (!$cid) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$quantity = intval($data['quantity']);
$note = $data['note'] ?? ''; // 默認為空特殊指示

// 建立 cart (if not exist)
$conn->query("INSERT IGNORE INTO Cart (cid, cartTime) VALUES ($cid, '$cartTime')");

// 插入項目
$sql_insert = "INSERT INTO CartItem (cid, cartTime, pid, mid, quantity, specialNote)
               VALUES (?, ?, ?, ?, ?, ?)
               ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("issiis", $cid, $cartTime, $pid, $mid, $quantity, $note);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);  // 返回 SQL 錯誤訊息
}
?>
