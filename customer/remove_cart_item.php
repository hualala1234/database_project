<?php
session_start();
require_once("../dbh.php");

$data = json_decode(file_get_contents("php://input"), true);
$cid = $_SESSION['cid'] ?? '';
$cartTime = $_SESSION['cartTime'] ?? '';
$pid = $data['pid'];
$mid = $data['mid'];

if ($cid && $cartTime && $pid && $mid) {
    $stmt = $conn->prepare("DELETE FROM CartItem WHERE cid = ? AND cartTime = ? AND pid = ? AND mid = ?");
    $stmt->bind_param("isii", $cid, $cartTime, $pid, $mid);
    $stmt->execute();
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
?>
<!-- 移除購物車商品 -->