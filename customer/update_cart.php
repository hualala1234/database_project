<?php
session_start();
require_once("../dbh.php");

$data = json_decode(file_get_contents("php://input"), true);
$cid = $_SESSION['cid'];
$cartTime = $_SESSION['cartTime'];

$pid = intval($data['pid']);
$mid = intval($data['mid']);
$quantity = intval($data['quantity']);
$note = $data['note'];

$sql = "UPDATE CartItem
        SET quantity = ?, specialNote = ?
        WHERE cid = ? AND cartTime = ? AND pid = ? AND mid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isisii", $quantity, $note, $cid, $cartTime, $pid, $mid);
$stmt->execute();

echo json_encode(["success" => true]);
?>

