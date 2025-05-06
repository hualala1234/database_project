<?php
session_start();
include('../dbh.php');

$data = json_decode(file_get_contents("php://input"), true);
$pid = $data['pid'];
$specialNote = $data['specialNote'];
$cid = $_SESSION['cid'];
$cartTime = $_SESSION['cartTime'];

if (!isset($pid, $specialNote, $cid, $cartTime)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

$sql = "UPDATE CartItem SET specialNote = ? WHERE cid = ? AND cartTime = ? AND pid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisi", $specialNote, $cid, $cartTime, $pid);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}
?>
