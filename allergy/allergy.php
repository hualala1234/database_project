<?php
session_start();

if (isset($_SESSION['cid'])) {
    $cid = $_SESSION['cid'];
    
    // 連接資料庫
    $conn = new mysqli('localhost', 'root', '', 'junglebite');
    if ($conn->connect_error) {
        die("連接失敗：" . $conn->connect_error);
    }

    // 查詢 customer 資料表以獲取 cName
    $sql = "SELECT cName FROM customer WHERE cid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL 語句錯誤：" . $conn->error);
    }
    $stmt->bind_param("s", $cid);
    $stmt->execute();
    $result = $stmt->get_result();

    $customer_name = '使用者';  // 預設名稱
    if ($row = $result->fetch_assoc()) {
        $customer_name = $row['cName'];  // 取得名稱
    }

    // 查詢過敏原資料
    $sql = "SELECT allergens, other_allergen FROM allergy WHERE cid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL 語句錯誤：" . $conn->error);
    }
    $stmt->bind_param("s",$cid);
    $stmt->execute();
    $result = $stmt->get_result();

    $allergens = [];
    $other_allergen = '';
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['allergens'])) {
            // 假設 allergens 是用逗號分隔的多個值
            $allergen_items = explode(',', $row['allergens']);
            $allergens = array_merge($allergens, array_map('trim', $allergen_items));
        }
        if (!empty($row['other_allergen'])) {
            $other_allergen = $row['other_allergen']; // 如果有多筆資料，你可以選擇是否要合併
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo "請先登入，才能填寫過敏原資料。";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>allergy</title>
    <link rel="stylesheet" href="allergy.css">
    <!-- 引入 Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <!-- 引入 jQuery UI CSS（使得排序元素顯示為拖曳狀態） -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

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
            <nav class="navbar navbar-light bg-white navbar-expand-xl">
                <a href="../customer/index.php?cid=<?php echo $cid; ?>" class="navbar-brand"><h1 class="text-primary display-6">Junglebite  過敏設定</h1></a>
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
                                    <!-- <a href="/database_project/allergy/allergy.php?cid=<?php echo $cid; ?>" class="dropdown-item">過敏設定</a> -->
                                    <a href="../claw_machine/claw.php?cid=<?php echo $cid; ?>" class="dropdown-item">優惠券活動</a>
                                    <a href="../walletAndrecord/c_wallet.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">錢包</a>
                                    <a href="../walletAndrecord/c_record.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">交易紀錄</a>
                                    <a href="../customer/friends.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">我的好友</a>
                                    <a href="../wheel/wheel.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">命運轉盤</a>
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
    

    <div class="allergy-container mt-5">
        <h3>歡迎, <?php echo htmlspecialchars($customer_name); ?></h3>
        <p>您的客戶編號 (CID): <?php echo htmlspecialchars($cid); ?></p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#allergyModal">編輯過敏原</button>

        <!-- 顯示已存入的過敏原資料 -->
        <div class="mt-4">
            <h5>您的過敏原資料：</h5>
            <?php if (!empty($allergens) || !empty($other_allergen)): ?>
                <ul>
                    <?php foreach ($allergens as $allergen): ?>
                        <li><?php echo htmlspecialchars($allergen); ?></li>
                    <?php endforeach; ?>
                    <?php if (!empty($other_allergen)): ?>
                        <li>其他過敏原：<?php echo htmlspecialchars($other_allergen); ?></li>
                    <?php endif; ?>
                </ul>
            <?php else: ?>
                <p>尚未填寫過敏原資料。</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- 過敏原彈出視窗 -->
    <div class="modal fade" id="allergyModal" tabindex="-1" aria-labelledby="allergyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allergyModalLabel">選擇過敏原</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="allergyForm" action="db_allergy.php" method="POST">
                        <input type="hidden" name="cid" value="<?php echo $cid; ?>">
                        <fieldset>
                            <legend>請勾選您的食物過敏原：</legend>
                            <div class="allergen-grid">
                            <div class="allergen-item">
                                <label>
                                    <img src="crab.png" alt="甲殼類">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="shellfish"
                                        <?php echo in_array('shellfish', $allergens) ? 'checked' : ''; ?>>
                                    甲殼類（蝦、蟹）
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="魚.png" alt="魚類">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="fish"
                                        <?php echo in_array('fish', $allergens) ? 'checked' : ''; ?>>
                                    魚類
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="花生.png" alt="花生">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="peanuts"
                                        <?php echo in_array('peanuts', $allergens) ? 'checked' : ''; ?>>
                                    花生
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="nuts.png" alt="堅果類">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="nuts"
                                        <?php echo in_array('nuts', $allergens) ? 'checked' : ''; ?>>
                                    堅果類（核桃、杏仁、腰果）
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="牛奶.png" alt="牛奶">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="milk"
                                        <?php echo in_array('milk', $allergens) ? 'checked' : ''; ?>>
                                    牛奶
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="egg.png" alt="雞蛋">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="eggs"
                                        <?php echo in_array('eggs', $allergens) ? 'checked' : ''; ?>>
                                    雞蛋
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="小麥.png" alt="小麥">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="wheat"
                                        <?php echo in_array('wheat', $allergens) ? 'checked' : ''; ?>>
                                    小麥（含麩質）
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="蚵仔.png" alt="螺貝類">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="mollusks"
                                        <?php echo in_array('mollusks', $allergens) ? 'checked' : ''; ?>>
                                    螺貝類（蚵仔、淡菜）
                                </label>
                            </div>
                            </div>
                            <div style="text-align: center; margin-top: 30px;">
                                <label for="other-allergen">其他過敏原：</label><br>
                                <input type="text" id="other-allergen" name="other_allergen" 
                                    value="<?php echo htmlspecialchars($other_allergen); ?>" 
                                    placeholder="請輸入其他過敏原" 
                                    style="width: 60%; padding: 8px; border-radius: 6px; border: 1px solid #ccc; margin-top: 20px;">
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                    <button type="submit" form="allergyForm" class="btn btn-primary">提交</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 引入 Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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