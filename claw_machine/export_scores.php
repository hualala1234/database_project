<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include('../dbh.php');
$cid = $_SESSION['cid'] ?? null;
$role = $_SESSION['role'] ;
if (!$cid) return;

// try {
//     include(__DIR__ . '/export_scores.php');
//     file_put_contents(__DIR__ . '/log.txt', "[PHP] V export_scores.php 已執行\n", FILE_APPEND);
// } catch (Throwable $e) {
//     file_put_contents(__DIR__ . '/log.txt', "[PHP] X export_scores.php 發生錯誤: " . $e->getMessage() . "\n", FILE_APPEND);
// }


file_put_contents(__DIR__ . '/log.txt', "要匯出CSV了\n", FILE_APPEND);
// 匯出 CSV 到本地，不下載
$sql = "SELECT DATE(created_at) AS play_date, game_score FROM Coupon WHERE cid = ? AND game=2";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cid);
$stmt->execute();

file_put_contents(__DIR__ . '/log.txt', "使用 store_result + bind_result + fetch 避免記憶體爆掉\n", FILE_APPEND);
// ✅ 使用 store_result + bind_result + fetch 避免記憶體爆掉
$stmt->store_result();
$stmt->bind_result($play_date, $game_score);

// $filename = __DIR__ . "/user_score_data_{$cid}.csv";
$filename = __DIR__ . "/user_score_data_{$cid}.csv";
$fp = fopen($filename, 'w');
fputcsv($fp, ['play_date', 'game_score']);

while ($stmt->fetch()) {
    fputcsv($fp, [$play_date, $game_score]);
}

file_put_contents(__DIR__ . '/log.txt', "結束!!!\n", FILE_APPEND);
fclose($fp);
$stmt->close();

exit;
?>