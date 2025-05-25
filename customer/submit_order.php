<?php
session_start();
require_once '../dbh.php';
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

$platformFee = isset($data['platformFee']) ? intval($data['platformFee']) : 0;





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
if ($paymentMethod === 'walletBalance') {
    $stmt = $conn->prepare("SELECT balance FROM wallet WHERE cid = ?");
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => '找不到錢包帳戶']);
        exit;
    }
    $wallet = $result->fetch_assoc();
    if ($wallet['balance'] < $totalPrice) {
        echo json_encode(['success' => false, 'error' => '錢包餘額不足，無法完成訂單']);
        exit;
    }
}
// 先根據 paymentMethod 判斷 cardName 要帶什麼值
if ($paymentMethod !== 'walletBalance' && $paymentMethod !== 'cashOnDelivery') {
    $cardName = $paymentMethod; // 直接用 paymentMethod 的值
    $paymentMethod = 'cardName';
} else {
    $cardName = null; // 其他狀況給 null
}
$conn->begin_transaction();



try {
    // ✅ 6. 插入 Transaction 表（補上所有欄位）
    // 準備 SQL，新增 cardName 欄位
    $stmt = $conn->prepare("
    INSERT INTO Transaction 
    (cid, mid, address_text, transactionTime, paymentMethod, cardName, totalPrice, tNote, id, couponCode) 
    VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("iissssiss", $cid, $mid, $address, $paymentMethod, $cardName, $totalPrice, $tNote, $couponId, $couponCode);
    $stmt->execute();

    $transactionId = $stmt->insert_id;

    // ✅ 7. 插入 Orders 表
    foreach ($cartItems as $item) {
        $pid = $item['pid'];
        $price = $item['price'];
        $quantity = $item['quantity'];
        $specialNote = $item['specialNote'] ?? '';
        
        // 插入 Record（預設 pRating 和 pComment 為 NULL）
        $stmt = $conn->prepare("
        INSERT INTO Record (tranId, pid, cid, quantity, price, specialNote) 
        VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiiiis", $transactionId, $pid, $cid, $quantity, $price, $specialNote);
        $stmt->execute();
    }

    // ✅ 8. 使用優惠券則刪除
    if ($couponCode) {
        $stmt = $conn->prepare("UPDATE coupon SET used = 0 WHERE cid = ? AND code = ?");
        $stmt->bind_param("is", $cid, $couponCode);
        $stmt->execute();
    }
     
    // ✅ 9. 若用錢包付從錢包扣錢
    if ($paymentMethod === 'walletBalance') {
        // 扣除顧客錢包的金額
        $stmt4 = $conn->prepare("UPDATE wallet SET balance = balance - ? WHERE cid = ?");
        $stmt4->bind_param("ii", $totalPrice, $cid);
        $stmt4->execute();
    }
    // ✅ 9.1 公司帳戶 + 平台手續費
    $type = 'transaction';  // 固定值

    $stmt = $conn->prepare("INSERT INTO companyaccount (cid, type, increment, time) VALUES (?, ?, ?, CURRENT_TIMESTAMP())");
    $stmt->bind_param("isi", $cid, $type, $platformFee);
    $stmt->execute();

    
    // ✅ 10. 插入 companyaccount 表
    $type = 'transaction';  // 固定值

    $stmt = $conn->prepare("INSERT INTO companyaccount (cid, type, increment, time) VALUES (?, ?, ?, CURRENT_TIMESTAMP())");
    $stmt->bind_param("isi", $cid, $type, $platformFee);
    $stmt->execute();

    // ✅ 11. 刪除該使用者該次時間點的購物車資料
   
    $stmt = $conn->prepare("DELETE FROM CartItem WHERE cid = ? AND cartTime = ? AND mid = ?");
    $stmt->bind_param("isi", $cid, $cartTime, $mid);
    $stmt->execute();


    // ✅ 10. 提交交易
    $conn->commit();
    echo json_encode(['success' => true]);

    


} catch (Exception $e) {
    $conn->rollback();
    error_log("❌ 訂單錯誤：" . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
