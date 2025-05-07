<?php
session_start();
include('../dbh.php');
$mid = isset($_GET['mid']) ? intval($_GET['mid']) : 0;

if (!isset($_SESSION['cid'], $_SESSION['cartTime'])) {
    http_response_code(400);
    echo json_encode(["error" => "未登入"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$pid = $data['pid'];
$cid = $_SESSION['cid'];
$cartTime = $_SESSION['cartTime'];

$sql = "DELETE FROM CartItem WHERE cid = ? AND cartTime = ? AND pid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $cid, $cartTime, $pid);
$stmt->execute();

// 檢查購物車是否還有商品
$sqlCheck = "SELECT COUNT(*) FROM CartItem WHERE cid = ? AND cartTime = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("is", $cid, $cartTime);
$stmtCheck->execute();
$stmtCheck->bind_result($itemCount);
$stmtCheck->fetch();
if ($itemCount == 0) {
    echo json_encode(["redirect" => "index.php"]);
    exit;
}
echo json_encode(["success" => true]);

?>
