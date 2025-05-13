<?php
session_start();
include('../dbh.php');
$cid = $_SESSION['cid'] ?? null;

if (!$cid) {
    echo "<p>請先登入。</p>";
    exit;
}

// 計算今天玩了幾次
$sql = "SELECT COUNT(*) AS play_count FROM Coupon WHERE cid = ? AND DATE(created_at) = CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cid);
$stmt->execute();
$stmt->bind_result($play_count);
$stmt->fetch();
$stmt->close();

$remaining = max(0, 3 - $play_count);

// 查詢所有優惠券
$sql = "SELECT discount, game_score, created_at FROM Coupon WHERE cid = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cid);
$stmt->execute();
$result = $stmt->get_result();

// 查詢所有今日遊玩記錄（含是否有優惠券）
$sql_log = "SELECT game_score, created_at FROM Coupon WHERE cid = ? ORDER BY created_at DESC";
$stmt_log = $conn->prepare($sql_log);
$stmt_log->bind_param("i", $cid);
$stmt_log->execute();
$logs = $stmt_log->get_result();
$log_entries = [];
$log_counter = 0;
while ($row = $logs->fetch_assoc()) {
    $log_counter++;
    $created_date = date('Y-m-d', strtotime($row['created_at']));
    $desc = ($created_date === date('Y-m-d') && $log_counter <= 3)
        ? "✅ 成績 {$row['game_score']} 已儲存，獲得 {$row['game_score']}% off 優惠券"
        : "❌ 成績 {$row['game_score']} 已儲存，但已達上限，無優惠券";
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
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f8ff;
      padding: 40px;
      display: flex;
    }
    .main-content {
      flex: 2;
    }
    .log-panel {
      flex: 1;
      margin-left: 30px;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
      height: 500px;
      overflow-y: auto;
    }
    .log-entry {
      margin-bottom: 12px;
      border-bottom: 1px dashed #ccc;
      padding-bottom: 8px;
    }
    h2, h3 {
      color: #333;
    }
    table {
      margin: 0 auto;
      border-collapse: collapse;
      width: 80%;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    th, td {
      border: 1px solid #ccc;
      padding: 12px;
    }
    th {
      background-color: #007bff;
      color: white;
    }
    td {
      font-weight: 500;
    }
    .back-link {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 20px;
      background: #007bff;
      color: white;
      border-radius: 6px;
      text-decoration: none;
    }
    .back-link:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <div class="main-content">
    <h2>🎮 今日剩餘可玩次數：<?= $remaining ?> / 3</h2>

    <?php if (isset($_SESSION['flappy_log'])): ?>
      <div style="color: green; margin-top: 20px; font-weight: bold; white-space: pre-line;">
        <?= htmlspecialchars($_SESSION['flappy_log']) ?>
      </div>
      <?php unset($_SESSION['flappy_log']); ?>
    <?php endif; ?>

    <h3>🎁 我的優惠券清單</h3>
    <table>
      <tr>
        <th>折扣 (%)</th>
        <th>遊戲得分</th>
        <th>獲得時間</th>
        <th>狀態</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['discount'] ?>%</td>
          <td><?= $row['game_score'] ?></td>
          <td><?= $row['created_at'] ?></td>
          <td>
            <?php
              $created_date = date('Y-m-d', strtotime($row['created_at']));
              echo $created_date === date('Y-m-d') ? '✅ 有效' : '❌ 已過期';
            ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>

    <button onclick="location.href='/database_project/flappyBirds/flappy_bird/run_flappy.php?cid=<?= $cid ?>&role=c'" style="margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; border-radius: 6px; cursor: pointer;">開始遊戲</button>   
    <a class="back-link" href="../customer/index.php">⬅ 返回主頁</a>
  </div>

  <div class="log-panel">
    <h3>📜 遊戲紀錄</h3>
    <?php foreach ($log_entries as $entry): ?>
      <div class="log-entry">
        <div><strong>時間：</strong><?= $entry['time'] ?></div>
        <div><strong>成績：</strong><?= $entry['score'] ?></div>
        <div><?= $entry['desc'] ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>
