<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$cid = $_SESSION['cid'] ?? null;
$role = $_SESSION['role'];

echo "<script>console.log('PHP: $cid');</script>";
file_put_contents(__DIR__ . '/log.txt', "[PHP] generate_chart.php 啟動\n", FILE_APPEND);

if (!$cid) {
    file_put_contents(__DIR__ . '/log.txt', "[PHP] X 未登入 (cid is null)\n", FILE_APPEND);
    return;
}

echo "匯出 CSV";
// 匯出 CSV
include(__DIR__ . '/export_scores.php');
file_put_contents(__DIR__ . '/log.txt', "[PHP] V export_scores.php 已執行\n", FILE_APPEND);

echo "執行 Python 產圖";
// 執行 Python 產圖
$py_script_path = realpath(__DIR__ . '/score_data.py');
if (!$py_script_path) {
    file_put_contents(__DIR__ . '/log.txt', "[PHP] X 找不到 score_data.py\n", FILE_APPEND);
    return;
}

echo $py_script_path;
$command = "C:\\xampp\\htdocs\\jb_project\\.venv\\Scripts\\python.exe " . escapeshellarg($py_script_path) . " 2>&1";
$output = shell_exec($command);
file_put_contents(__DIR__ . '/log.txt', "[PY OUTPUT]\n$output\n", FILE_APPEND);

if (!file_exists(__DIR__ . '/score_chart.png')) {
    file_put_contents(__DIR__ . '/log.txt', "[PHP] X score_chart.png 沒產生\n", FILE_APPEND);
} else {
    file_put_contents(__DIR__ . '/log.txt', "[PHP] V score_chart.png 已產出\n", FILE_APPEND);
}
// header("Refresh: $sec; url=$page");
header("./my_coupons.php?cid=$cid&role=c");
?>
