<?php
session_start();
require_once '../dbh.php';
header('Content-Type: application/json');

if (!isset($_SESSION['cid'])) {
    echo json_encode(['success' => false, 'error' => '未登入']);
    exit;
}
$mid = isset($_GET['mid']) ? intval($_GET['mid']) : 0;

$cid = $_SESSION['cid'];
$totalPrice = $data['totalPrice'];
$paymentMethod = $data['paymentMethod'];
$cardName = $data['cardName'];
$tNote = $data['tNote'];
$couponCode = $data['couponCode'] ?? null;
$cartItems = $data['cartItems'];  // 陣列
$cartTime = date('Y-m-d H:i:s');

// 開始交易
$conn->begin_transaction();

try {
    // 1️⃣ 插入 Transaction 資料表
    $stmt = $conn->prepare("INSERT INTO Transaction (cid, mid, totalPrice, paymentMethod, cardName, tNote, address_text, id)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidsss", $cid, $mid, $totalPrice, $paymentMethod, $cardName, $tNote, $address_text, $id);

    $stmt->execute();

    // 2️⃣ 插入每筆商品進 Orders 資料表
    $stmt2 = $conn->prepare("INSERT INTO Orders (cid, cartTime, pid, price, quantity, mid) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $stmt2->bind_param("issiis", $cid, $cartTime, $item['pid'], $item['price'], $item['quantity'], $mid);
        $stmt2->execute();
    }

    // 3️⃣ 刪除優惠券
    if ($couponCode) {
        $stmt3 = $conn->prepare("DELETE FROM coupons WHERE cid = ? AND code = ?");
        $stmt3->bind_param("is", $cid, $couponCode);
        $stmt3->execute();
    }

    // 4️⃣ 如果付款方式是「錢包」→ 扣款
    if ($paymentMethod === 'wallet') {
        $stmt4 = $conn->prepare("UPDATE cwallet SET balance = balance - ? WHERE cid = ?");
        $stmt4->bind_param("ii", $totalPrice, $cid);
        $stmt4->execute();
    }

    // 5️⃣ 清空購物車
    $stmt = $conn->prepare("DELETE FROM CartItem WHERE cid = ?");
    $stmt->bind_param("i", $cid);
    $stmt->execute();


    // 提交
    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
