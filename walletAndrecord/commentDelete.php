<?php
include('connect.php');

$tranId = $_POST['tranId'] ?? null;
$type = $_POST['type'] ?? null;

if (!$tranId || !$type) {
    echo "Missing data";
    exit;
}

$allowed = ['mComment', 'dComment'];
if (!in_array($type, $allowed)) {
    echo "Invalid comment type";
    exit;
}

// 將對應欄位清空
$sql = "UPDATE transaction SET $type = '' WHERE tranId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tranId);
if ($stmt->execute()) {
    echo "Completed delete";
} else {
    echo "Failed: " . $conn->error;
}
?>
