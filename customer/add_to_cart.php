<?php
header('Content-Type: application/json'); 
session_start();
require_once("../dbh.php");

// 假如 cartTime 尚未設定，固定下來
if (!isset($_SESSION['cartTime'])) {
    $_SESSION['cartTime'] = date("Y-m-d H:i:s");
}
$cartTime = $_SESSION['cartTime'];

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['pid']) || !isset($data['mid']) || !isset($data['quantity'])) {
    echo json_encode(["success" => false, "error" => "Missing parameters"]);
    exit;
}

$pid = intval($data['pid']);
$mid = intval($data['mid']);
$cid = $_SESSION['cid'] ?? null;

if (!$cid) {
    echo json_encode(["success" => false, "error" => "User not logged in", "debug" => $_SESSION]);
    exit;
}

$quantity = intval($data['quantity']);
$note = $data['note'] ?? '';

$conn->query("INSERT IGNORE INTO Cart (cid, cartTime) VALUES ($cid, '$cartTime')");

$sql_insert = "INSERT INTO CartItem (cid, cartTime, pid, mid, quantity, specialNote)
               VALUES (?, ?, ?, ?, ?, ?)
               ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("issiis", $cid, $cartTime, $pid, $mid, $quantity, $note);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Database error",
        "mysqli_error" => $stmt->error
    ]);
}
?>
