<?php
session_start();
include('../dbh.php');

if (!isset($_SESSION['cid'], $_SESSION['cartTime'])) {
    http_response_code(400);
    echo json_encode(["error" => "未登入"]);
    exit;
}

$cid = $_SESSION['cid'];
$cartTime = $_SESSION['cartTime'];
$mid = isset($_GET['mid']) ? intval($_GET['mid']) : 0;

$sql = "SELECT c.pid, p.pName, p.price, p.pPicture, c.quantity, c.specialNote,
               (p.price * c.quantity) AS total
        FROM CartItem c
        JOIN Product p ON c.pid = p.pid
        WHERE c.cid = ? AND c.cartTime = ? AND c.mid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $cid, $cartTime, $mid);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
echo json_encode([
    "cartTime" => $cartTime,
    "items" => $items
]);

?>
