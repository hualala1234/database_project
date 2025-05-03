<?php
// 資料庫連線設定
$host = 'localhost';
$dbname = 'database';
$user = 'root';
$pass = '';

// 設定回應格式為 JSON
header('Content-Type: application/json');

// 取得並解析 JSON 輸入
$input = json_decode(file_get_contents('php://input'), true);

// 檢查必要欄位是否存在
if (!isset($input['prizeMessage'], $input['prizeCode'], $input['time'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

try {
    // 建立資料庫連線
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 準備 SQL 插入語句
    $stmt = $pdo->prepare("INSERT INTO coupons (message, code, created_at) VALUES (:message, :code, :created_at)");
    $stmt->execute([
        ':message' => $input['prizeMessage'],
        ':code' => $input['prizeCode'],
        ':created_at' => $input['time']
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Coupon saved']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
