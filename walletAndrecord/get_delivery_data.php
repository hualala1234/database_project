<?php
include("connect.php");

$did = $_GET['did'] ?? null;

if (!$did) {
    echo json_encode(['error' => 'No deliveryperson ID provided']);
    exit;
}

$sql = "
    SELECT 
        DATE(transactionTime) AS date,
        COUNT(*) AS delivery_count,
        AVG(dRating) AS avg_rating
    FROM transaction
    WHERE did = ?
    GROUP BY DATE(transactionTime)
    ORDER BY DATE(transactionTime)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $did);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
