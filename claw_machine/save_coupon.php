<?php
// 資料庫連線設定
$host = 'localhost';
$dbname = 'junglebite';
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

// 可改成從 session 抓 customer ID（如果你有登入機制）
session_start();
$cid = $_SESSION['cid'] ?? null;


try {
    // ✅ 建立資料庫連線
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ 檢查今天是否已玩過
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM coupons
        WHERE cid = :cid
          AND DATE(created_at) = CURDATE()
    ");
    $stmt->execute([':cid' => $cid]);
    $playedToday = $stmt->fetchColumn();

    if ($playedToday > 3) {
        echo json_encode(['status' => 'error', 'message' => '今天已經玩過一次了']);
        exit;
    }

    // ✅ 沒玩過就儲存資料
    $stmt = $pdo->prepare("INSERT INTO coupons (message, code, created_at,cid) VALUES (:message, :code, :created_at, :cid)");
    $stmt->execute([
        ':message' => $input['prizeMessage'],
        ':code' => $input['prizeCode'],
        ':created_at' => $input['time'],
        ':cid' => $cid,
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Coupon saved']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
