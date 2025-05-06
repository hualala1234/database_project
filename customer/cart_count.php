<?php
session_start();
require_once("../dbh.php");

if (!isset($_SESSION['cid']) || empty($_SESSION['cid'])) {
    echo json_encode(["count"] => 0);
    exit;
}

$cid = $_SESSION['cid'];
$cartTimes = $_SESSION['cartTime'] ?? [];

$storeCount = 0;

if (is_array($cartTimes)) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS itemCount FROM CartItem WHERE cid = ? AND cartTime = ?");

    foreach ($cartTimes as $mid => $cartTime) {
        $stmt->bind_param("is", $cid, $cartTime);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if (isset($result['itemCount']) && $result['itemCount'] > 0) {
            $storeCount++;
        }
    }
}

echo json_encode(["count" => $storeCount]);
?>
