<?php
session_start();
header('Content-Type: application/json');
include('../dbh.php');

$cid = $_SESSION['cid'] ?? 0;
if (!$cid) {
    echo json_encode(['success' => false, 'message' => '請先登入']);
    exit;
}

// 1. 確認錢包餘額是否足夠
$check = $conn->prepare("SELECT balance FROM wallet WHERE cid = ?");
$check->bind_param("i", $cid);
$check->execute();
$result = $check->get_result();
$row = $result->fetch_assoc();
if (!$row || $row['balance'] < 499) {
    echo json_encode(['success' => false, 'message' => '餘額不足，無法加入 VIP']);
    exit;
}

// 2. 扣款 -499 元
$updateWallet = $conn->prepare("UPDATE Wallet SET balance = balance - 499 WHERE cid = ?");
$updateWallet->bind_param("i", $cid);
$updateWallet->execute();

// 3. 設定 VIP 時間為現在
$setVip = $conn->prepare("UPDATE customer SET vipTime = NOW() WHERE cid = ?");
$setVip->bind_param("i", $cid);
$setVip->execute();


// 4. companyaccount +499
$addrecord = $conn->prepare("INSERT INTO companyaccount (cid, type, increment, time) VALUES (?, 'vip', 499, NOW())");
$addrecord->bind_param("i", $cid);
$addrecord->execute();

// 4. 回傳成功
echo json_encode(['success' => true, 'message' => 'VIP 加入成功']);
