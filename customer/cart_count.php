<?php
session_start();
require_once("../dbh.php");

if (!isset($_SESSION['cid']) || empty($_SESSION['cid'])) {
    echo json_encode(["count" => 0]);
    exit;
}

$cid = $_SESSION['cid'];
$cartTime = $_SESSION['cartTime'] ?? '';

$sql = "SELECT SUM(quantity) AS total FROM CartItem WHERE cid = ? AND cartTime = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $cid, $cartTime);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

// 如果沒有結果，設為 0
$totalCount = isset($result['total']) ? $result['total'] : 0;

echo json_encode(["count" => $totalCount]);
?>
