<?php
session_start();
require_once '../dbh.php';
header('Content-Type: application/json');

// ✅ 1. 檢查是否登入
if (!isset($_SESSION['cid'])) {
    echo json_encode(['success' => false, 'error' => '未登入']);
    exit;
}

$cid = $_SESSION['cid'];

// ✅ 2. 解析前端 JSON 資料
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'error' => '無法解析 JSON']);
    exit;
}

// ✅ 3. 檢查必要欄位
$requiredFields = ['mid', 'totalPrice', 'paymentMethod', 'tNote', 'address_text', 'cartItems'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field])) {
        echo json_encode(['success' => false, 'error' => "缺少欄位：$field"]);
        exit;
    }
}

// ✅ 4. 變數準備
$mid = intval($data['mid']);
$totalPrice = intval($data['totalPrice']);
$paymentMethod = $data['paymentMethod'];
$tNote = $data['tNote'];
$address = $data['address_text'];
$cartItems = $data['cartItems']; // 購物車項目
$couponCode = $data['couponCode'] ?? null;
$couponId = $data['id'] ?? null;
$cartTime = $data['cartTime'];
// ✅ 5. 開始資料庫交易
$conn->begin_transaction();

try {
    // ✅ 6. 插入 Transaction 表（補上所有欄位）
    $stmt = $conn->prepare("
        INSERT INTO Transaction 
        (cid, mid, address_text, transactionTime, paymentMethod, totalPrice, tNote, id) 
        VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissssi", $cid, $mid, $address, $paymentMethod, $totalPrice, $tNote, $couponId);
    $stmt->execute();

    $transactionId = $stmt->insert_id;

    // ✅ 7. 插入 Orders 表
    foreach ($cartItems as $item) {
        $pid = $item['pid'];
        $price = $item['price'];
        $quantity = $item['quantity'];
        $specialNote = $item['specialNote'] ?? '';
        
        $stmt = $conn->prepare("
        INSERT INTO Orders (cid, cartTime, pid, price, quantity, mid, specialNote, tranId) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isiiiisi", $cid, $cartTime, $pid, $price, $quantity, $mid, $specialNote, $transactionId);
    

        $stmt->execute();
    }

    // ✅ 8. 使用優惠券則刪除
    if ($couponCode) {
        $stmt = $conn->prepare("DELETE FROM coupons WHERE cid = ? AND code = ?");
        $stmt->bind_param("is", $cid, $couponCode);
        $stmt->execute();
    }
     
    // ✅ 9. 若用錢包付從錢包扣錢
    if ($paymentMethod === 'wallet') {
        // 扣除顧客錢包的金額
        $stmt4 = $conn->prepare("UPDATE wallet SET balance = balance - ? WHERE cid = ?");
        $stmt4->bind_param("ii", $totalPrice, $cid);
        $stmt4->execute();
    }

    // ✅ 10. 刪除該使用者該次時間點的購物車資料
   
    $stmt = $conn->prepare("DELETE FROM CartItem WHERE cid = ? AND cartTime = ? AND mid = ?");
    $stmt->bind_param("isi", $cid, $cartTime, $mid);
    $stmt->execute();


    // ✅ 11. 提交交易
    $conn->commit();
    echo json_encode(['success' => true]);

    


} catch (Exception $e) {
    $conn->rollback();
    error_log("❌ 訂單錯誤：" . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
