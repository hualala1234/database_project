<?php
session_start();
require_once("../dbh.php");

header('Content-Type: application/json');

if (!isset($_SESSION['cid']) || empty($_SESSION['cid'])) {
    echo json_encode(["count" => 0]);
    exit;
}

$cid = $_SESSION['cid'];
$cartTime = $_SESSION['cartTime'] ?? '';

if ($cartTime) {
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT mid) AS storeCount FROM CartItem WHERE cid = ? AND cartTime = ?");
    $stmt->bind_param("is", $cid, $cartTime);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    $storeCount = $result['storeCount'] ?? 0;

    // 除錯輸出
    error_log("storeCount: " . $storeCount);  // 在伺服器日誌中顯示結果
    echo json_encode(["count" => $storeCount]);
} else {
    echo json_encode(["count" => 0]);
}

?>
