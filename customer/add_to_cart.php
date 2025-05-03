<?php
session_start();
require_once("../dbh.php");

$data = json_decode(file_get_contents("php://input"), true);
$pid = intval($data['pid']);
$mid = intval($data['mid']);
$cid = $_SESSION['cid']; // 登入時記得設置
$cartTime = $_SESSION['cartTime'] ?? date("Y-m-d H:i:s"); // 記住這一輪購物車時間
$_SESSION['cartTime'] = $cartTime;

$quantity = intval($data['quantity']);
$note = $data['note'];

// 建立 cart (if not exist)
$conn->query("INSERT IGNORE INTO Cart (cid, cartTime) VALUES ($cid, '$cartTime')");

// 插入項目
$sql_insert = "INSERT INTO CartItem (cid, cartTime, pid, mid, quantity, specialNote)
               VALUES (?, ?, ?, ?, ?, ?)
               ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("issiis", $cid, $cartTime, $pid, $mid, $quantity, $note);
$stmt->execute();

echo json_encode(["success" => true]);
?>
