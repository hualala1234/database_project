<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include('../dbh.php');
$cid = $_SESSION['cid'] ?? null;
$role = $_SESSION['role'] ;
if (!$cid) return;

try {
    include(__DIR__ . '/export_scores.php');
    file_put_contents(__DIR__ . '/log.txt', "[PHP] V export_scores.php 已執行\n", FILE_APPEND);
} catch (Throwable $e) {
    file_put_contents(__DIR__ . '/log.txt', "[PHP] X export_scores.php 發生錯誤: " . $e->getMessage() . "\n", FILE_APPEND);
}

// 匯出 CSV 到本地，不下載
$sql = "SELECT DATE(created_at) AS play_date, game_score FROM Coupon WHERE cid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cid);
$stmt->execute();
$result = $stmt->get_result();

$filename = __DIR__ . "/user_score_data.csv";
$fp = fopen($filename, 'w');
fputcsv($fp, ['play_date', 'game_score']);
while ($row = $result->fetch_assoc()) {
    fputcsv($fp, $row);
}
fclose($fp);

exit;
?>
