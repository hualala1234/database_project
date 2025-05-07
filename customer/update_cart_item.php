<?php
session_start();
require_once("../dbh.php");

header('Content-Type: application/json'); // ⚠️ 確保是 JSON 格式

$data = json_decode(file_get_contents("php://input"), true);
$cid = $_SESSION['cid'] ?? '';
$cartTime = $_SESSION['cartTime'] ?? '';
$pid = $data['pid'] ?? '';
$mid = $data['mid'] ?? '';
$quantity = $data['quantity'] ?? '';

$response = ['success' => false]; // 預設回傳值

if ($cid && $cartTime && $pid && $mid && $quantity > 0) {  // 修改為 quantity > 0
    $stmt = $conn->prepare("UPDATE CartItem SET quantity = ? WHERE cid = ? AND cartTime = ? AND pid = ? AND mid = ?");
    $stmt->bind_param("iisii", $quantity, $cid, $cartTime, $pid, $mid);
    $stmt->execute();

    $response['success'] = true;
} elseif ($cid && $cartTime && $pid && $mid && $quantity <= 0) { // 如果數量 <= 0 刪除商品
    $sql = "DELETE FROM CartItem WHERE cid = ? AND cartTime = ? AND pid = ? AND mid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $cid, $cartTime, $pid, $mid);  // 加入 mid 條件
    $stmt->execute();

    $response['success'] = true;
}

// 最後統一只輸出一次 JSON
echo json_encode($response);
?>
