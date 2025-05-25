<?php
session_start();
include("connect.php"); // 請確認這是你連資料庫的檔案

// $mid = $_SESSION['id'] ?? null;
$mid = $_GET['mid'] ?? null; // 從 GET 參數中獲取 merchant ID

if (!$mid) {
    echo json_encode(['error' => 'No merchant ID provided']);
    exit;
}

$sql = "
    SELECT 
        DATE(transactionTime) AS date,
        COUNT(*) AS transaction_count,
        AVG(mRating) AS avg_rating
    FROM transaction
    WHERE mid = ?
    GROUP BY DATE(transactionTime)
    ORDER BY DATE(transactionTime)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $mid);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
