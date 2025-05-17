<?php
session_start();
include('../dbh.php');

$cid = $_SESSION['cid'] ?? null;
$reserveId = $_POST['ReserveTransid'] ?? null;

$adult = $_POST['adult'] ?? 0;
$children = $_POST['children'] ?? 0;
$deposite = $_POST['deposite'] ?? 0;
$refund = $deposite * ($adult + $children);

if (!$cid || !$reserveId || $refund <= 0) {
    exit("參數錯誤，無法取消預約");
}

$conn->begin_transaction();
try {
    // 刪除預約資料
    $stmt = $conn->prepare("DELETE FROM ReserveTrans WHERE ReserveTransid = ? AND Reservationcid = ?");
    $stmt->bind_param("ii", $reserveId, $cid);
    $stmt->execute();
    if ($stmt->affected_rows === 0) throw new Exception("找不到該筆預約或非本人訂位");
    $stmt->close();

    // 退訂金
    $stmt = $conn->prepare("UPDATE wallet SET balance = balance + ? WHERE cid = ?");
    $stmt->bind_param("di", $refund, $cid);
    $stmt->execute();
    if ($stmt->affected_rows === 0) throw new Exception("退費失敗");
    $stmt->close();

    $conn->commit();
    echo "已取消預約並退回 NT$" . number_format($refund, 0);
} catch (Exception $e) {
    $conn->rollback();
    echo "取消失敗：" . $e->getMessage();
}
?>
