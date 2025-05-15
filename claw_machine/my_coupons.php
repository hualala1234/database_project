<?php
date_default_timezone_set('Asia/Taipei');
$tz = new DateTimeZone('Asia/Taipei');
$now = new DateTime('now', $tz);
$today = $now->format('Y-m-d');
// 📄 my_coupons.php
session_start();
include('../dbh.php');
$cid = $_SESSION['cid'] ?? null;
if (!$cid) {
    echo "<p>請先登入。</p>";
    exit;
}

// if (function_exists('shell_exec')) {
//     echo "✅ shell_exec 可使用";
// } else {
//     echo "❌ shell_exec 被禁用";
// }
// 1️⃣ 執行 export_scores.php 以產出 CSV
// file_get_contents("export_scores.php");

// // 2️⃣ 執行 score_data.py（請確認你的 Python 安裝與路徑）
// $script_path = realpath("claw_machine/score_data.py");
// shell_exec("python3 " . escapeshellarg($script_path));
// // echo "<p>CSV 檔案已產出。</p>";
// echo $script_path;


// 計算今天還能玩幾次（qualified 為 true 才算）
$sql = "SELECT COUNT(*) AS play_count FROM Coupon WHERE cid = ? AND DATE(created_at) = CURDATE() AND qualified = TRUE AND game=2";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cid);
$stmt->execute();
$stmt->bind_result($play_count);
$stmt->fetch();
$stmt->close();
$remaining = max(0, 3 - $play_count);

// 🎁 查詢前三次有優惠券的紀錄
$sql = "SELECT discount, game_score, created_at, used FROM Coupon WHERE cid = ? AND qualified = TRUE AND game=2 ORDER BY created_at DESC ";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cid);
$stmt->execute();
$result = $stmt->get_result();

// 📜 查詢所有遊戲紀錄
$sql_log = "SELECT game_score, created_at, discount, qualified FROM Coupon WHERE cid = ? AND game=2 ORDER BY created_at DESC";
$stmt_log = $conn->prepare($sql_log);
$stmt_log->bind_param("i", $cid);
$stmt_log->execute();
$logs = $stmt_log->get_result();

$log_entries = [];
while ($row = $logs->fetch_assoc()) {
    if ($row['qualified']&& $row['game_score'] > 0) {
        $desc = "✅ 成績 {$row['game_score']} 已儲存，獲得 {$row['discount']}% off 優惠券";
    } elseif ($row['qualified'] && $row['game_score'] == 0) {
        $desc = "❌ 成績 {$row['game_score']} 已儲存，但無優惠券";
    } else {
        $desc = "❌ 成績 {$row['game_score']} 已儲存，但已達上限，無優惠券";
    }
    $log_entries[] = [
        'time' => $row['created_at'],
        'score' => $row['game_score'],
        'desc' => $desc
    ];
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>我的優惠券 - JungleBite</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif;background-image: url('../walletAndrecord/image/forest.png');
    background-repeat: no-repeat;
    background-size: cover;; padding: 40px; display: flex; }
    .main-content { flex: 2; margin-left:5%}
    .log-panel { flex: 1; margin-right: 30px; padding: 20px 40px 30px 40px; background: #fff; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); height: 800px; overflow-y: auto; position: relative; right:25%;}
    .log-entry { margin-bottom: 12px; border-bottom: 1px dashed #ccc; padding-bottom: 8px; }
    h2, h3 { color: #333; }
    table {  border-collapse: collapse; width: 70%; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); border-radius: 15px; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 12px; }
    th { background-color:rgb(76, 114, 154); color: white; }
    td { font-weight: 500; }
    .back-link { display: inline-block; margin-top: 30px; padding: 10px 20px; background:rgb(115, 173, 235); color: white; border-radius: 6px; text-decoration: none; }
    .back-link:hover { background: #0056b3; }
    .score-chart { margin-top: 40px; max-width: 100%; }
    .play-button {
        margin-top: 20px;
        padding: 10px 20px;
        background: rgb(221, 72, 8);
        color: white;
        border-radius: 6px;
        cursor: pointer;
        display: inline-block;
        transition: transform 0.2s ease;
        margin-right: 20px;
    }
    .play-button:hover {
    transform: scale(1.2);
    }
  </style>
</head>
<!-- <script>
window.addEventListener('load', () => {
  fetch('/database_project/claw_machine/generate_chart.php')
    .then(res => res.text())
    .then(console.log) // 或顯示成 loading message
    .catch(console.error);
});
</script> -->

<body>
  <div class="main-content">
    <h1>🎮 今日剩餘可玩次數：<?= $remaining ?> / 3</h1>
    <a onclick="location.href='/database_project/flappyBirds/flappy_bird/run_flappy.php?cid=<?= $cid ?>&role=c'" class="play-button" style="margin-top: 20px; padding: 10px 20px; background:rgb(221, 72, 8); color: white; border-radius: 6px; cursor: pointer;">開始遊戲</a>
    <a class="back-link" href="../customer/index.php">⬅ 返回主頁</a>
    <h2>🎁 我的優惠券清單</h2>
    <table>
      <tr>
        <th>折扣 (%)</th>
        <th>遊戲得分</th>
        <th>獲得時間</th>
        <th>狀態</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <?php
          $created_date = (new DateTime($row['created_at'], $tz))->format('Y-m-d');
          $status = $row['used'] ? '✔ 已使用' : ($created_date === date('Y-m-d') ? '✅ 有效' : '❌ 已過期');
        ?>
        <tr>
          <td><?= $row['discount'] ?>%</td>
          <td><?= $row['game_score'] ?></td>
          <td><?= $row['created_at'] ?></td>
          <td><?= $status ?></td>
        </tr>
      <?php endwhile; ?>
    </table>

    <div class="score-chart">
      <h2>📈 得分趨勢圖</h2><pre><a onclick="location.href='/database_project/claw_machine/generate_chart.php?cid=<?= $cid ?>&role=c'" class="play-button" style="margin-top: 20px; padding: 10px 20px; background:rgb(221, 72, 8); color: white; border-radius: 6px; cursor: pointer;">更新圖片</a></pre>
      <!-- <img src="score_chart.png?ts=<?= time() ?>" alt="得分圖表" style="max-width: 70%;"> -->
      <img src="score_chart_<?= $cid ?>.png?ts=<?= time() ?>" alt="得分圖表" style="max-width: 70%;">

    </div>

    
  </div>

    <div class="sidebar">
    <div class="log-panel">
    <h2>📜 遊戲紀錄</h2>
    <?php foreach ($log_entries as $entry): ?>
        <div class="log-entry">
        <div><strong>時間：</strong><?= $entry['time'] ?></div>
        <div><strong>成績：</strong><?= $entry['score'] ?></div>
        <div><?= $entry['desc'] ?></div>
        </div>
    <?php endforeach; ?>
    </div>
    
    </div>
</body>
</html>
<?php
// ✅ 自動產出圖表
// include(__DIR__ . '/generate_chart.php');
?>