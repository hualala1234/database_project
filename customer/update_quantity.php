<?php
session_start();
include('../dbh.php');

if (!isset($_SESSION['cid'], $_SESSION['cartTime'])) {
    http_response_code(400);
    echo json_encode(["error" => "未登入"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$cid = $_SESSION['cid'];
$cartTime = $_SESSION['cartTime'];
$pid = $data['pid'];
$quantity = $data['quantity'];

$sql = "UPDATE CartItem SET quantity = ? WHERE cid = ? AND cartTime = ? AND pid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisi", $quantity, $cid, $cartTime, $pid);
$stmt->execute();

if ($quantity <= 0) {
    $sql = "DELETE FROM CartItem WHERE cid = ? AND cartTime = ? AND pid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $cid, $cartTime, $pid);
    $stmt->execute();
    echo json_encode(["deleted" => true]);
    exit;
}

// 取得該商品的單價
$sql = "SELECT price FROM Product WHERE pid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$price = floatval($row['price']);
$total = $price * $quantity;

// 算整個購物車小計
$sql = "SELECT SUM(p.price * c.quantity) AS subtotal 
        FROM CartItem c 
        JOIN Product p ON c.pid = p.pid 
        WHERE c.cid = ? AND c.cartTime = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $cid, $cartTime);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$subtotal = floatval($row['subtotal']);

echo json_encode([
    "success" => true,
    "price" => $price,
    "total" => $total,
    "subtotal" => $subtotal
]);
?>
<!-- 更新結帳頁面數量 -->