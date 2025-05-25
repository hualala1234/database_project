<?php
session_start();
include '../dbh.php';

if (!isset($_SESSION['cid'])) {
    echo "<script>alert('請先登入才能查看收藏'); location.href='../login/before_login.php';</script>";
    exit;
}

$cid = $_SESSION['cid'];

$sql = "
    SELECT m.*, 
           GROUP_CONCAT(rcl.categoryName SEPARATOR ', ') AS categoryNames,
           m.rating,
           IFNULL(m.ratingCount, 0) AS ratingCount
    FROM Favorite f
    JOIN Merchant m ON f.mid = m.mid
    LEFT JOIN RestaurantCategories rc ON m.mid = rc.mid
    LEFT JOIN RestaurantCategoryList rcl ON rc.categoryId = rcl.categoryId
    WHERE f.cid = ?
    GROUP BY m.mid
    ORDER BY m.mName ASC
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>我的收藏</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
    <style>
        div#favorite-list {
            margin-top: 150px;
            margin-left: 0px;
        }
        .fruite-item {
            border: 1px solid #ccc;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s;
            background: #fff;
            position: relative;
        }
        .fruite-item:hover {
            transform: scale(1.02);
        }
        .fruite-img img {
            height: 180px;
            object-fit: cover;
        }
        .favorite-icon {
            color: #e74c3c;
            font-size: 1.3rem;
            cursor: pointer;
        }

        /* 動畫淡出 */
        .fade-out {
            opacity: 0;
            transition: opacity 1s ease;
        }
    </style>
</head>
<body>

    <!-- Navbar start -->
    <div class="container-fluid fixed-top">
        <div class="container topbar bg-primary d-none d-lg-block" style="padding: 20px;">
            <div class="d-flex justify-content-between">
                <div class="top-info ps-2">
                </div>        
            </div>
        </div>
        <div class="container px-0">
            <nav class="navbar navbar-light bg-white navbar-expand-xl ">
                <a href="../customer/index.php?cid=<?php echo $cid; ?>" class="navbar-brand text-decoration-none"><h1 class="text-primary display-6">Junglebite 我的收藏</h1></a>
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
                                    <a href="../login/login_customer/setting.php" class="dropdown-item">個人設定</a>
                                    <a href="/database_project/allergy/allergy.php" class="dropdown-item">過敏設定</a>
                                    <!-- <a href="../claw_machine/claw.php" class="dropdown-item">優惠券活動</a> -->
                                    <a href="../walletAndrecord/c_wallet.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">錢包</a>
                                    <a href="../walletAndrecord/c_record.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">交易紀錄</a>
                                    <a href="friends.php" class="dropdown-item">我的好友</a>
                                    <a href="../wheel/wheel.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item text-decoration-none">命運轉盤</a>
                                    <a href="/database_project/customer/reservation.php?panel=history" class="dropdown-item">我的訂位</a>

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


        <div class="row g-4" id="favorite-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($m = $result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 favorite-card" id="card-<?= $m['mid'] ?>">
                        <div class="rounded position-relative fruite-item">
                            <div class="fruite-img" onclick="location.href='merchant.php?mid=<?= urlencode($m['mid']) ?>'">
                                <img src="../<?= htmlspecialchars($m['mPicture']) ?>" class="img-fluid w-100 rounded-top" alt="<?= htmlspecialchars($m['mName']) ?>">
                            </div>
                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top:10px; left:10px;">
                                <?= htmlspecialchars($m['categoryNames'] ?? '未分類') ?>
                            </div>
                            <div class="p-4 border border-secondary border-top-0 rounded-bottom"
                                style="height:175px; display:flex; flex-direction:column; justify-content:space-between;">
                                <div>
                                    <h5><?= htmlspecialchars($m['mName']) ?></h5>
                                    <p><?= htmlspecialchars($m['mAddress']) ?></p>
                                </div>
                                <div class="d-flex justify-content-between flex-lg-wrap">
                                    <p class="text-dark fs-5 fw-bold mb-0">
                                        <i class="fa-solid fa-heart favorite-icon" data-mid="<?= $m['mid'] ?>"></i>
                                    </p>
                                    <p class="mb-0" style="text-align:right;">
                                        <i class="fas fa-star fs-6 me-1 mb-0" style="color:#ffb524;"></i>
                                        <?= htmlspecialchars($m['rating'] ?? '尚無評分') ?>/5 (<?= $m['ratingCount'] ?>)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">你還沒有收藏任何店家。</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- 加在頁面中（推薦放 container 外面） -->
    <!-- 顯示取消提示 -->
    <div id="remove-alert" class="text-center text-danger mt-4 fw-bold" style="display:none;"></div>
   
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

    <script>
    document.addEventListener('DOMContentLoaded', () => {
    let pending = {}; // 用 mid 作為 key 儲存倒數中的卡片資訊

    document.querySelectorAll('.favorite-icon').forEach(icon => {
        icon.addEventListener('click', function (e) {
        e.stopPropagation();

        const mid = this.dataset.mid;
        const card = document.getElementById('card-' + mid);
        const storeName = card.querySelector('h5').innerText;
        const iconEl = this;

        // 如果已經在倒數中 → 代表是復原
        if (pending[mid]) {
            clearTimeout(pending[mid].timer);
            delete pending[mid];

            // 將愛心變回紅色實心
            iconEl.classList.remove('fa-regular');
            iconEl.classList.add('fa-solid', 'text-danger');

            // 呼叫 toggle_favorite.php 加回收藏
            fetch('toggle_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `mid=${encodeURIComponent(mid)}`
            });

            // 清除提示
            document.getElementById('remove-alert').style.display = 'none';
            return;
        }

        // 🔁 第一次點擊 → 開始取消收藏
        iconEl.classList.remove('fa-solid', 'text-danger');
        iconEl.classList.add('fa-regular');

        fetch('toggle_favorite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `mid=${encodeURIComponent(mid)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && !data.favorited) {
            // 開始倒數
            const timer = setTimeout(() => {
                card.classList.add('fade-out');
                setTimeout(() => {
                card.remove();

                // 顯示提示
                const alertBox = document.getElementById('remove-alert');
                alertBox.innerText = `已取消收藏：${storeName}`;
                alertBox.style.display = 'block';

                // 若卡片全部刪光
                if (document.querySelectorAll('.favorite-card').length === 0) {
                    document.getElementById('favorite-list').innerHTML =
                    "<p class='text-center'>你還沒有收藏任何店家。</p>";
                }
                }, 1000);
                delete pending[mid];
            }, 3000);

            // 儲存倒數資訊
            pending[mid] = { timer };
            }
        });
        });
    });
    });
    </script>

</body>
</html>
