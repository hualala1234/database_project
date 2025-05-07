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
        // 檢查該商家的商品是否已經全部刪除
        $stmt = $conn->prepare("SELECT COUNT(*) AS itemCount FROM CartItem WHERE cid = ? AND cartTime = ? AND mid = ?");
        $stmt->bind_param("isi", $cid, $cartTime, $mid);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (isset($result['itemCount']) && $result['itemCount'] == 0) {
            // 若該商家已經沒有商品，從 session 中移除該商家的 cartTime
            unset($_SESSION['cartTime'][$mid]);
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
