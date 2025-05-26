<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include('../dbh.php');
$cid = $_SESSION['cid'] ?? null;
$role = $_SESSION['role'] ?? 'c';

file_put_contents(__DIR__ . '/log.txt', "[PHP] generate_chart.php 啟動\n", FILE_APPEND);

if (!$cid) {
    file_put_contents(__DIR__ . '/log.txt', "[PHP] X 未登入 (cid is null)\n", FILE_APPEND);
    exit;
}

// ✅ 匯出 CSV
file_put_contents(__DIR__ . '/log.txt', "[PHP] 要匯出 CSV 了\n", FILE_APPEND);
$sql = "SELECT DATE(created_at) AS play_date, game_score FROM Coupon WHERE cid = ? AND game=2";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cid);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($play_date, $game_score);

$filename = __DIR__ . "/user_score_data_{$cid}.csv";
$fp = fopen($filename, 'w');
fputcsv($fp, ['play_date', 'game_score']);
while ($stmt->fetch()) {
    fputcsv($fp, [$play_date, $game_score]);
}
fclose($fp);
$stmt->close();
file_put_contents(__DIR__ . '/log.txt', "[PHP] 匯出 CSV 完成\n", FILE_APPEND);

// ✅ 執行 Python 畫圖
$py_script_path = realpath(__DIR__ . '/score_data.py');
echo $py_script_path;
if ($py_script_path && $cid) {
    $python = "C:\\Users\\clair\\AppData\\Local\\Programs\\Python\\Python311\\python.exe";
    $command = $python . " " . escapeshellarg($py_script_path) . " " . escapeshellarg($cid) . " 2>&1";
    echo $command;
    file_put_contents(__DIR__ . '/log.txt', "[CMD] $command\n", FILE_APPEND);
    $output = shell_exec($command);
    file_put_contents(__DIR__ . '/log.txt', "[PY OUTPUT]\n" . $output, FILE_APPEND);
}

$png_path = __DIR__ . "/score_chart_{$cid}.png";
if (!file_exists($png_path)) {
    file_put_contents(__DIR__ . '/log.txt', "[PHP] X score_chart_{$cid}.png 沒產生\n", FILE_APPEND);
} else {
    file_put_contents(__DIR__ . '/log.txt', "[PHP] V score_chart_{$cid}.png 已產出\n", FILE_APPEND);
}

// ✅ 重導回 my_coupons 頁面
header("Location: /database_project/claw_machine/my_coupons.php?cid=$cid&role=$role");
exit;
?>