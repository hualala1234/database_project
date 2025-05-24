<?php
session_start();
require_once("../dbh.php");

header('Content-Type: application/json'); // ✅ 告訴瀏覽器這是 JSON

$data = json_decode(file_get_contents("php://input"), true);
$cid = $_SESSION['cid'] ?? '';
$cartTime = $_SESSION['cartTime'] ?? '';
$pid = $data['pid'] ?? null;
$mid = $data['mid'] ?? null;

if ($cid && $cartTime && $pid && $mid) {
    // 刪除商品
    $stmt = $conn->prepare("DELETE FROM CartItem WHERE cid = ? AND cartTime = ? AND pid = ? AND mid = ?");
    $stmt->bind_param("isii", $cid, $cartTime, $pid, $mid);
    $success = $stmt->execute();
    
    if ($success) {
        // 如果購物車已完全清空，可以考慮整體清空 cartTime：
        $stmt = $conn->prepare("SELECT COUNT(*) AS totalCount FROM CartItem WHERE cid = ? AND cartTime = ?");
        $stmt->bind_param("is", $cid, $cartTime);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (isset($result['totalCount']) && $result['totalCount'] == 0) {
            unset($_SESSION['cartTime']);
        }
        // 返回成功訊息
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete item"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Missing parameter"]);
}
?>
