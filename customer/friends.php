<?php
session_start();
$cid = $_SESSION['cid'] ?? '';

// 資料庫連線設定
$host = 'localhost';
$dbname = 'junglebite';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$myCid = $_SESSION['cid'] ?? null;

if (!$myCid) {
    // 若未登入，導向回登入頁或顯示錯誤
    header('Location: /database_project/login/before_login.php');
    exit();
}

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
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/ea478a1bc4.js" crossorigin="anonymous"></script>

    <!-- Libraries Stylesheet -->
    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <link href="../css/style.css" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">


    <!-- 引入 jQuery UI CSS（使得排序元素顯示為拖曳狀態） -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

</head>
<body>
    <body class="p-4">
        <!-- Navbar start -->
        <div class="container-fluid fixed-top">
                <div class="container topbar bg-primary d-none d-lg-block" style="padding: 20px;">
                    <div class="d-flex justify-content-between">
                        <div class="top-info ps-2">
                        </div>        
                    </div>
                </div>
                <div class="container px-0">
                    <nav class="navbar navbar-light bg-white navbar-expand-xl">
                        <a href="../customer/index.php?cid=<?php echo $cid; ?>" class="navbar-brand"><h1 class="text-primary display-6">Junglebite 好友列表</h1></a>
                        <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                            <span class="fa fa-bars text-primary"></span>
                        </button>
                        <div class="collapse navbar-collapse bg-white" id="navbarCollapse" style="display: flex; flex-direction: row-reverse;">
                            
                            <div class="d-flex m-3 me-0">
                                <!-- <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search text-primary"></i></button> -->
                                <!-- <a href="#" class="position-relative me-4 my-auto">
                                    <i class="fa fa-shopping-bag fa-2x"></i>
                                    <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 15px; height: 20px; min-width: 20px;">3</span>
                                </a> -->
                                <?php if (isset($_SESSION['login_success'])): ?>
                                <!-- ✅ 已登入的顯示 -->
                                <div class="dropdown" style="position: relative; display: inline-block;">
                                    <a href="javascript:void(0);" class="my-auto" onclick="toggleDropdown()">
                                    <img src="  ../login/success.png" alt="Success" style="width: 40px; height: 40px; filter: brightness(0) saturate(100%) invert(42%) sepia(91%) saturate(356%) hue-rotate(71deg) brightness(94%) contrast(92%);">
                                    </a>

                                    <div id="myDropdown" class="dropdown-content" style="display: none; position: absolute; background-color: white; min-width: 120px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; right: 0; border-radius: 8px;">

                                        <?php if ($_SESSION['role'] === 'm'): ?>
                                            <a href="/database/merchant/setting.php" class="dropdown-item">商家設定</a>
                                        <?php elseif ($_SESSION['role'] === 'c'): ?>
                                            <a href="../login/login_customer/setting.php?cid=<?php echo $cid; ?>" class="dropdown-item">個人設定</a>
                                            <a href="/database_project/allergy/allergy.php?cid=<?php echo $cid; ?>" class="dropdown-item">過敏設定</a>
                                            <a href="../claw_machine/claw.php?cid=<?php echo $cid; ?>" class="dropdown-item">優惠券活動</a>
                                            <a href="../walletAndrecord/c_wallet.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">錢包</a>
                                            <a href="../walletAndrecord/c_record.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">交易紀錄</a>
                                            <!-- <a href="../customer/friends.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">我的好友</a> -->
                                            <a href="../wheel/wheel.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">命運轉盤</a>
                                            <a href="../customer/myfavorite.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item text-decoration-none">我的愛店</a>
                                            <a href="/database_project/customer/reservation.php" class="dropdown-item">我要訂位</a>
                                        <?php elseif ($_SESSION['role'] === 'd'): ?>
                                            <a href="/database/customer/setting.php" class="dropdown-item">外送員設定</a>
                                        <?php elseif ($_SESSION['role'] === 'platform'): ?>
                                            <a href="/database/customer/setting.php" class="dropdown-item">平台設定</a>
                                        <?php endif; ?>
                                            <a href="/database_project/login/login_customer/logout.php" class="dropdown-item">Logout</a>


                                    </div>
                                </div>
                                <?php else: ?>
                                <!-- ❌ 未登入的顯示 -->
                                <a href="/database_project/login/before_login.php" class="my-auto">
                                    <i class="fas fa-user fa-2x"></i>
                                </a>
                                <?php endif; ?>
                            </div>

                        </div>
                    </nav>
                </div>
            </div>
            <!-- Navbar End -->
    

    <div class="allfriendslist" style="margin-top: 100px;">
        <h2>我的好友列表</h2>
    
        <?php if (empty($friends)): ?>
            <p>目前尚無好友。</p>
        <?php else: ?>
            <div class="friend-list" >
                <?php foreach ($friends as $f): ?>
                    <div class="friend-card">
                        <?php if (!empty($f['imageURL'])): ?>
                            <img src="../<?= htmlspecialchars($f['imageURL']) ?>" alt="照片" class="friend-imageURL" >
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
    </div>


    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById("myDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        // 點擊頁面其他地方自動收起下拉選單
        window.onclick = function(event) {
            if (!event.target.matches('.my-auto') && !event.target.closest('.dropdown')) {
                var dropdown = document.getElementById("myDropdown");
                if (dropdown && dropdown.style.display === "block") {
                    dropdown.style.display = "none";
                }
            }
        }
    </script>

</body>
</html>
