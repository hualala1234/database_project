<?php
// list_delivery_cards.php

// 1. 資料庫連線參數，請依實際修改
$host     = 'localhost';
$db_user  = 'root';
$db_pw    = '';
$db_name  = 'login_deliveryperson';
$charset  = 'utf8mb4';

// 2. 建立 MySQLi 連線
$mysqli = new mysqli($host, $db_user, $db_pw, $db_name);
if ($mysqli->connect_errno) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
$mysqli->set_charset($charset);

// 3. 執行查詢：抓出所有外送員資料
$sql = "SELECT did, password, address, name, email, imageURL, DateCreate
        FROM deliveryperson
        ORDER BY DateCreate DESC";
if (!$result = $mysqli->query($sql)) {
    die('Query Error: ' . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>外送員列表</title>
    <style>
      body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 20px; }
      .container-list { display: flex; flex-wrap: wrap; gap: 20px; }
      .card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 16px;
        width: 280px;
        box-sizing: border-box;
      }
      .card-header { margin-bottom: 12px; }
      .card-header h2 { margin: 0 0 4px; font-size: 1.2em; }
      .card-header .date { color: #666; font-size: 0.9em; }
      .avatar { text-align: center; margin-bottom: 12px; }
      .avatar img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; }
      .card-body p { margin: 4px 0; font-size: 0.95em; }
      .card-body p span { font-weight: bold; }
    </style>
</head>
<body>
    <h1>外送員清單</h1>
    <div class="container-list">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
          <div class="card-header">
            <h2><?php echo htmlspecialchars($row['name']); ?> (ID: <?php echo htmlspecialchars($row['did']); ?>)</h2>
            <div class="date">註冊於：<?php echo htmlspecialchars($row['DateCreate']); ?></div>
          </div>
          <div class="avatar">
            <?php 
              // 若 imageURL 為圖片檔名，此處預設放在 uploads 資料夾
              $imgPath = !empty($row['imageURL']) ? 'uploads/' . $row['imageURL'] : 'default-avatar.png';
            ?>
            <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Avatar">
          </div>
          <div class="card-body">
            <p><span>Email:</span> <?php echo htmlspecialchars($row['email']); ?></p>
            <p><span>Address:</span> <?php echo htmlspecialchars($row['address']); ?></p>
            <p><span>Password:</span> <?php echo htmlspecialchars($row['password']); ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
</body>
</html>

<?php
// 4. 釋放資源並關閉連線
$result->free();
$mysqli->close();
?>
