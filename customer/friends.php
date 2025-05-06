<?php
session_start();

// 資料庫連線設定
$host = 'localhost';
$dbname = 'database';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 預設登入帳號 cid = 1
$myCid = 1;
$error = "";
$previewFriend = null;

// 處理 POST 請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. 刪除好友
    if (isset($_POST['delete_cid'])) {
        $deleteCid = intval($_POST['delete_cid']);

        $deleteStmt = $pdo->prepare("DELETE FROM friend WHERE (cid = ? AND friend_cid = ?) OR (cid = ? AND friend_cid = ?)");
        $deleteStmt->execute([$myCid, $deleteCid, $deleteCid, $myCid]);

    // 2. 預覽新增好友
    } elseif (isset($_POST['preview_email'])) {
        $email = trim($_POST['preview_email']);
        $stmt = $pdo->prepare("SELECT cid, cName, imageURL, cRegistrationTime FROM customer WHERE email = ?");
        $stmt->execute([$email]);
        $previewFriend = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$previewFriend) {
            $error = "此帳號未註冊";
        }

    // 3. 確定新增好友
    } elseif (isset($_POST['confirm_cid'])) {
        $friendCid = intval($_POST['confirm_cid']);

        if ($friendCid == $myCid) {
            $error = "不能新增自己為好友";
        } else {
            $checkStmt = $pdo->prepare("SELECT * FROM friend WHERE cid = ? AND friend_cid = ?");
            $checkStmt->execute([$myCid, $friendCid]);
            if ($checkStmt->rowCount() == 0) {
                // 雙向新增
                $insertStmt = $pdo->prepare("INSERT INTO friend (cid, friend_cid) VALUES (?, ?), (?, ?)");
                $insertStmt->execute([$myCid, $friendCid, $friendCid, $myCid]);
            } else {
                $error = "此好友已經在列表中";
            }
        }
    }
}

// 取得好友列表
$friendsStmt = $pdo->prepare("
    SELECT c.cid, c.cName, c.imageURL, c.cRegistrationTime
    FROM friend f
    JOIN customer c ON f.friend_cid = c.cid
    WHERE f.cid = ?
");
$friendsStmt->execute([$myCid]);
$friends = $friendsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>好友列表</title>
    <link rel="stylesheet" href="friends.css">
</head>
<body>
    <div class="header_friends">
        <p>MY FRIENDS</p>
        <a href="/database/customer/index.php">
            <img src="home.png" alt="Home" class="home-icon">
        </a>
    </div>
    <h2>我的好友列表</h2>

    <?php if (empty($friends)): ?>
        <p>目前尚無好友。</p>
    <?php else: ?>
        <div class="friend-list">
            <?php foreach ($friends as $f): ?>
                <div class="friend-card">
                    <?php if (!empty($f['imageURL'])): ?>
                        <img src="../<?= htmlspecialchars($f['imageURL']) ?>" alt="照片" class="friend-imageURL">
                    <?php else: ?>
                        <p><em>此用戶尚未上傳照片</em></p>
                    <?php endif; ?>
                    <p><strong>姓名：</strong> <?= htmlspecialchars($f['cName']) ?></p>
                    <p><strong>加入時間：</strong> <?= date('Y-m-d', strtotime($f['cRegistrationTime'])) ?></p>
                
        
                    <!-- 刪除好友表單 -->
                    <form method="POST" action="" onsubmit="return confirm('確定要刪除這位好友嗎？');" style="margin-top: 10px;">
                        <input type="hidden" name="delete_cid" value="<?= $f['cid'] ?>">
                        <button type="submit" class="delete-button">刪除</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <hr>

    <h2>新增好友</h2>

    <!-- 預覽查詢表單 -->
    <form class="add-form" method="POST" action="">
        <input type="email" name="preview_email" placeholder="輸入好友 Email" required>
        <button type="submit" class="add-button">查詢</button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($previewFriend): ?>
        <div style="margin-top: 20px;" class="friend-card">
            <h3>確認新增好友</h3>
            <?php if (!empty($previewFriend['imageURL'])): ?>
                <img src="../<?= htmlspecialchars($previewFriend['imageURL']) ?>" alt="照片" class="friend-imageURL">
            <?php else: ?>
                <p><em>此用戶尚未上傳照片</em></p>
            <?php endif; ?>
            <p><strong>姓名：</strong> <?= htmlspecialchars($previewFriend['cName']) ?></p>
            <p><strong>加入時間：</strong> <?= date('Y-m-d', strtotime($previewFriend['cRegistrationTime'])) ?></p>

            <!-- 確認新增表單 -->
            <form method="POST" action="">
                <input type="hidden" name="confirm_cid" value="<?= $previewFriend['cid'] ?>">
                <button type="submit" class="add-button" style="margin-top: 10px;">確定新增</button>
            </form>
        </div>
    <?php endif; ?>

</body>
</html>
