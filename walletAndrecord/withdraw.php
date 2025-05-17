<?php
session_start();
header('Content-Type: application/json');
include('connect.php');

$cid = $_SESSION['cid'] ?? 0;
if (!$cid) {
    echo json_encode(['success' => false, 'message' => '請先登入']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$amount = (int)($data['amount'] ?? 0);
$bankCode = $data['bankCode'] ?? '';
$accountNumber = $data['accountNumber'] ?? '';

if ($amount <= 0 || !$bankCode || !$accountNumber) {
    echo json_encode(['success' => false, 'message' => '金額或銀行帳號無效']);
    exit;
}

// 檢查帳號是否存在
$check = $conn->prepare("SELECT * FROM cbank WHERE cid = ? AND bankCode = ? AND accountNumber = ?");
$check->bind_param("iss", $cid, $bankCode, $accountNumber);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    $insert = $conn->prepare("INSERT INTO cbank (cid, bankCode, accountNumber) VALUES (?, ?, ?)");
    $insert->bind_param("iss", $cid, $bankCode, $accountNumber);
    $insert->execute();
}

// 檢查餘額
$wallet = $conn->prepare("SELECT balance FROM wallet WHERE cid = ?");
$wallet->bind_param("i", $cid);
$wallet->execute();
$walletRes = $wallet->get_result();
$row = $walletRes->fetch_assoc();

if ($row['balance'] < $amount) {
    echo json_encode(['success' => false, 'message' => '餘額不足']);
    exit;
}

// 扣除餘額
$update = $conn->prepare("UPDATE wallet SET balance = balance - ? WHERE cid = ?");
$update->bind_param("ii", $amount, $cid);
$update->execute();

// 寫入紀錄
$log = $conn->prepare("INSERT INTO wallet_record (cid, type, amount, card_number) VALUES (?, '提款', ?, ?)");
$cardDisplay = $bankCode . '-' . $accountNumber;
$log->bind_param("iis", $cid, $amount, $cardDisplay);
$log->execute();

echo json_encode(['success' => true, 'message' => "已成功提款 $amount 元"]);
