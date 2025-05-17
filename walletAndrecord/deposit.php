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

// 檢查帳號是否已存在於 cbank（若無則新增）
$check = $conn->prepare("SELECT * FROM cbank WHERE cid = ? AND bankCode = ? AND accountNumber = ?");
$check->bind_param("iss", $cid, $bankCode, $accountNumber);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    $insert = $conn->prepare("INSERT INTO cbank (cid, bankCode, accountNumber) VALUES (?, ?, ?)");
    $insert->bind_param("iss", $cid, $bankCode, $accountNumber);
    $insert->execute();
}

// 增加餘額
$updateWallet = $conn->prepare("UPDATE wallet SET balance = balance + ? WHERE cid = ?");
$updateWallet->bind_param("ii", $amount, $cid);
$updateWallet->execute();

// 寫入交易紀錄
$log = $conn->prepare("INSERT INTO wallet_record (cid, type, amount, card_number) VALUES (?, '儲值', ?, ?)");
$cardDisplay = $bankCode . '-' . $accountNumber;
$log->bind_param("iis", $cid, $amount, $cardDisplay);
$log->execute();

echo json_encode(['success' => true, 'message' => "已成功儲值 $amount 元"]);
