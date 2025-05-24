<?php
session_start();
include ('../dbh.php');  
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cid = isset($_SESSION["cid"]) ? $_SESSION["cid"] : '';
if ($cid !== '') {
    $sql = "SELECT * FROM Customer WHERE cid = $cid";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    
}
// ✅ 預設不是 VIP
$isVIP = false;
$vipImage = './vip.png';

if (!empty($cid)) {
    $sql = "SELECT vipTime FROM customer WHERE cid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (!is_null($row['vipTime'])) {
            $isVIP = true;
            $vipImage = './is_vip.png';
        }
    }
}

$storeCount = 0;
if (isset($_SESSION['cid'], $_SESSION['cartTime'])) {
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT mid) AS storeCount FROM CartItem WHERE cid = ? AND DATE(cartTime) = ?");
    $stmt->bind_param("is", $_SESSION['cid'], $cartDate);  // $cartDate 是 '2025-05-06' 或與資料庫日期匹配的日期

    $stmt->execute();
    $stmt->bind_result($storeCount);
    $stmt->fetch();
    $stmt->close();
}

// 處理表單提交更新地址
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_address_id'])) {
    $selected_address_id = $_POST['selected_address_id'];
    // 根據選擇的地址 ID 更新 session 中的地址
    $sql = "SELECT address_text FROM caddress WHERE address_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $selected_address_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['current_address'] = $row['address_text']; // 更新 session 地址
    }
    // 重定向回 index.php，讓頁面更新
    header("Location: index.php?cid=$cid");
    exit;
}

// 取得目前使用的地址（如果有從 modal 選擇過）
$defaultAddress = $_SESSION['current_address'] ?? ($row['address'] ?? '尚未選擇地址');

//訂單進度
$sql = "
SELECT t.*, d.dpName, d.latitude AS dLatitude, d.longitude AS dLongitude, o.orderStatus AS deliveryStatus, o.arrivePicture
FROM Transaction t
LEFT JOIN deliveryperson d ON t.did = d.did
LEFT JOIN dOrders o ON o.tranId = t.tranId
WHERE t.cid = ? 
  AND t.orderStatus != 'complete'
  AND t.orderStatus != 'rejectConfirm'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cid);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);




?>


<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Junglebite</title>
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
        <script src="https://kit.fontawesome.com/ea478a1bc4.js" crossorigin="anonymous"></script>
        

        <!-- Libraries Stylesheet -->
        <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="../css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="../css/style.css" rel="stylesheet">



    </head>

    <body>

        <!-- Spinner Start -->
        <!-- <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" role="status"></div>
        </div> -->
        <!-- Spinner End -->

        
        <!-- Navbar start -->
        <div class="container-fluid fixed-top">
            <div class="container topbar bg-primary d-none d-lg-block">
                <div class="top-info ps-2">
                    <span class="address-label text-white"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> 目前住址</span>
                    <span class="address-text" id="current-address" class="text-white">
                        <?= htmlspecialchars($defaultAddress) ?> <!-- PHP 顯示預設地址 -->
                    </span>
                    <button class="btn btn-sm btn-outline-light ms-2 change-address-btn" data-bs-toggle="modal" data-bs-target="#addressModal">
                        更換外送地點
                    </button>
                </div>
            </div>
            <div class="container px-0">
                <nav class="navbar navbar-light bg-white navbar-expand-xl">
                    <a href="index.php?cid=<?php echo $cid; ?>" class="navbar-brand"><img class="logo" src="../image/logo.png" alt="logo"  height="100"></a>
                    <!-- <a href="index.php?cid=<?php echo $cid; ?>" class="navbar-brand"><h1 class="text-primary display-6">Junglebite</h1></a> -->
                    <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars text-primary"></span>
                    </button>
                    <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                        
                    <form method="GET" action="search.php">
                        <div class="navbar-nav mx-auto">
                            <div class="position-relative mx-auto">
                                <input name="keyword" class="form-control border-2 border-secondary py-3 px-4 rounded-pill" style="width: 30rem;margin-left:200px;" type="text" placeholder="Search">
                                <button type="submit" class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100" style="top: 0; left: 87.64%;">搜尋</button>
                            </div>
                        </div>
                    </form>
                        <!-- <a href="../walletAndrecord/c_wallet.php?cid=<?php echo $cid; ?>&role=c">
                            <img class="wallet" src="./wallet.png" alt="wallet icon" width="40" height="40"
                                onmouseover="this.src='./wallet_hover.png'" 
                                onmouseout="this.src='./wallet.png'">
                        </a>
                        <a href="../walletAndrecord/c_record.php?cid=<?php echo $cid; ?>&role=c">
                            <img class="trans" src="./trans.png" alt="trans icon" width="40" height="40" style="margin-left: 20px;"
                                onmouseover="this.src='./trans_hover.png'" 
                                onmouseout="this.src='./trans.png'">
                        </a> -->
                        


                        <div class="d-flex m-3 me-0" style="align-items: center;">
                            <a href="../image_search/phpfrontend/index.php?cid=<?php echo $cid; ?>&role=c">
                                <img class="camara" src="./camara.png" alt="camara icon" width="50" height="50" 
                                    onmouseover="this.src='./camara_hover.png'" 
                                    onmouseout="this.src='./camara.png'">
                            </a>
                           
                            <!-- Crown Icon -->
                            <img class="crown" src="<?= $vipImage ?>" alt="VIP icon" width="35" height="35"
                            style="margin-bottom:0.5rem; margin-left:1rem; <?= $isVIP ? '' : 'cursor: pointer;' ?>"
                                <?php if (!$isVIP): ?>
                                onmouseover="this.src='./vip_hover.png'" 
                                onmouseout="this.src='./vip.png'"
                                onclick="toggleVIP(event)"
                            <?php endif; ?>
                            >
                            <!-- ✅ VIP 彈出視窗 -->
                            <div class="vip" id="vip-popup" style="display: none;">
                                <img id="closecomment" src="../walletAndrecord/image/cross.png" alt="close button" width="15" height="15" 
                                    style="position:absolute; top:10px; right:10px;" 
                                    onclick="closeVIP()">
                                
                                <img id="vip-image" src="./join_vip.png" alt="vip" style="cursor: pointer;" onclick="addVIPToCart()">
                                <p style="cursor: pointer;" onclick="confirmJoinVIP()">我要加入 VIP</p>
                            </div>

                            <!-- ✅ 飛行動畫圖像容器 -->
                            <div id="fly-container"></div>

                            <!-- ✅ 訊息提示 -->
                            <div id="vip-message" style="display:none; position: fixed; top: 80px; left: 50%; transform: translateX(-50%);
                                background: #4CAF50; color: white; padding: 10px 20px; border-radius: 8px; z-index: 3000;">
                                已成功加入 VIP 到購物車！
                            </div>
                            <?php if (count($orders) > 0): ?>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#multiOrderModal" class="ms-3 position-relative">
                                <i class="fa-solid fa-motorcycle fa-2x"></i>
                                <span id="order-count" class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 22px; height: 20px; min-width: 20px;">
                                    <?= count($orders) ?>
                                </span>
                            </a>
                            <?php endif; ?>

                            <a href="#" class="position-relative me-3 ms-3 my-auto" data-bs-toggle="modal" data-bs-target="#cartModal">
                                <i class="fa-solid fa-cart-shopping fa-2x"></i>
                                <span id="cart-count" class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 22px; height: 20px; min-width: 20px;">
                                    <?= isset($cartCount) ? $cartCount : '0' ?>
                                </span>
                            </a>
                            <?php if (isset($_SESSION['login_success'])): ?>
                            <!-- ✅ 已登入的顯示 -->
                            <div class="dropdown" style="position: relative; display: inline-block;">
                                <a href="javascript:void(0);" class="my-auto" onclick="toggleDropdown()">
                                <img src="  ../login/success.png" alt="Success" style="width: 40px; height: 40px; filter: brightness(0) saturate(100%) invert(42%) sepia(91%) saturate(356%) hue-rotate(71deg) brightness(94%) contrast(92%);">
                                </a>

                                <div id="myDropdown" class="dropdown-content" style="display: none; position: absolute; background-color: white; min-width: 120px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; right: 0; border-radius: 8px;">

                                    <?php if ($_SESSION['role'] === 'merchant'): ?>
                                        <a href="/database/merchant/setting.php" class="dropdown-item">商家設定</a>
                                    <?php elseif ($_SESSION['role'] === 'c'): ?>
                                        <a href="/database/customer/setting.php" class="dropdown-item">個人設定</a>
                                        <a href="/database_project/allergy/allergy.php" class="dropdown-item">過敏設定</a>
                                        <a href="../claw_machine/claw.php" class="dropdown-item">優惠券活動</a>
                                        <a href="../walletAndrecord/c_wallet.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">錢包</a>
                                        <a href="../walletAndrecord/c_record.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">交易紀錄</a>
                                        <a href="friends.php" class="dropdown-item">我的好友</a>
                                    <?php elseif ($_SESSION['role'] === 'delivery_person'): ?>
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




        <!-- Fruits Shop Start-->
        <?php
        // 頁面頂端或適當位置先宣告
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        ?>

        

        <div class="container-fluid fruite py-5  hero-header">
        
            <div class="container py-5">
                <div class="tab-class text-center">
                    <div class="g-4">
                        <div class="mb-4" >
                            <?php
                            $activeTab = $_GET['activeTab'] ?? 'tab-0';
                            ?>
                            <form method="GET" id="filterForm" style="display:flex;" class="col-md-4">
                                <select name="sortRating" id="sortRating" onchange="document.getElementById('filterForm').submit()" class="form-select bg-light rounded-pill text-dark me-3">
                                    <option value="">評分</option>
                                    <option value="desc" <?= ($_GET['sortRating'] ?? '')=='desc' ? 'selected' : '' ?>>由高到低</option>
                                    <option value="asc" <?= ($_GET['sortRating'] ?? '')=='asc' ? 'selected' : '' ?>>由低到高</option>
                                </select>

                                <select name="priceRange" id="priceRange" onchange="document.getElementById('filterForm').submit()" class="form-select bg-light rounded-pill text-dark ">
                                    <option value="">價格</option>
                                    <option value="1" <?= ($_GET['priceRange'] ?? '')=='1' ? 'selected' : '' ?>>200 元以下</option>
                                    <option value="2" <?= ($_GET['priceRange'] ?? '')=='2' ? 'selected' : '' ?>>201 ~ 500 元</option>
                                    <option value="3" <?= ($_GET['priceRange'] ?? '')=='3' ? 'selected' : '' ?>>501 ~ 800 元</option>
                                    <option value="4" <?= ($_GET['priceRange'] ?? '')=='4' ? 'selected' : '' ?>>800 元以上</option>
                                </select>
                                <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                            </form>
                        </div>
                        


                    <div class="tab-content">
                        <!-- 所有商品 -->
                        <div id="tab-0" class="tab-pane fade show p-0 active">
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="g-4 row" >
                                        <?php
                                       

                                        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
                                        $sortRating = $_GET['sortRating'] ?? '';
                                        $priceRange = $_GET['priceRange'] ?? '';
                                
                                        $matchedMids = [];
                                
                                        if ($keyword !== '') {
                                            $keywordLike = '%' . $conn->real_escape_string($keyword) . '%';
                                
                                            // 1. Product 中 pName 或 pDescription
                                            $sql = "SELECT DISTINCT mid FROM Product WHERE pName LIKE ? OR pDescription LIKE ?";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bind_param('ss', $keywordLike, $keywordLike);
                                            $stmt->execute();
                                            $stmt->bind_result($mid);
                                            while ($stmt->fetch()) $matchedMids[] = $mid;
                                            $stmt->close();
                                
                                            // 2. Merchant 中 mName
                                            $sql = "SELECT DISTINCT mid FROM Merchant WHERE mName LIKE ?";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bind_param('s', $keywordLike);
                                            $stmt->execute();
                                            $stmt->bind_result($mid);
                                            while ($stmt->fetch()) $matchedMids[] = $mid;
                                            $stmt->close();
                                
                                            // 3. 餐廳類別 categoryName
                                            $sql = "
                                                SELECT DISTINCT rc.mid
                                                FROM RestaurantCategoryList rcl
                                                JOIN RestaurantCategories rc ON rcl.categoryId = rc.categoryId
                                                WHERE rcl.categoryName LIKE ?
                                            ";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bind_param('s', $keywordLike);
                                            $stmt->execute();
                                            $stmt->bind_result($mid);
                                            while ($stmt->fetch()) $matchedMids[] = $mid;
                                            $stmt->close();
                                
                                            // 4. 商品類別 productCategoryName
                                            $sql = "
                                                SELECT DISTINCT p.mid
                                                FROM ProductCategoryList pcl
                                                JOIN ProductCategories pc ON pcl.productCategoriesId = pc.productCategoriesId
                                                JOIN Product p ON pc.pid = p.pid
                                                WHERE pcl.productCategoryName LIKE ?
                                            ";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bind_param('s', $keywordLike);
                                            $stmt->execute();
                                            $stmt->bind_result($mid);
                                            while ($stmt->fetch()) $matchedMids[] = $mid;
                                            $stmt->close();
                                
                                            // 移除重複
                                            $matchedMids = array_unique($matchedMids);
                                        }
                                
                                        $placeholder = implode(',', array_fill(0, count($matchedMids), '?'));
                                        $sqlAll = "
                                            SELECT 
                                                m.*, 
                                                GROUP_CONCAT(rcl.categoryName SEPARATOR ', ') AS categoryNames,
                                                COUNT(DISTINCT t.tranId) AS additionalRatingCount,
                                                COALESCE(AVG(t.mRating), 0) AS additionalRatingAvg,
                                                (
                                                    (
                                                        m.rating * m.ratingCount + 
                                                        COALESCE(SUM(t.mRating), 0)
                                                    ) / NULLIF((m.ratingCount + COUNT(t.mRating)), 0)
                                                ) AS combinedRating
                                            FROM Merchant m
                                            LEFT JOIN RestaurantCategories rc ON m.mid = rc.mid
                                            LEFT JOIN RestaurantCategoryList rcl ON rc.categoryId = rcl.categoryId
                                            LEFT JOIN Transaction t ON m.mid = t.mid AND t.mRating IS NOT NULL
                                        ";
                                
                                        if (!empty($matchedMids)) {
                                            $sqlAll .= " WHERE m.mid IN ($placeholder)";

                                            // 價格範圍處理（抓該商家產品的平均價格）
                                            switch ($priceRange) {
                                                case '1': // 200 以下
                                                    $sqlAll .= " AND (
                                                        SELECT AVG(price) FROM Product WHERE mid = m.mid
                                                    ) < 200";
                                                    break;
                                                case '2': // 201 ~ 500
                                                    $sqlAll .= " AND (
                                                        SELECT AVG(price) FROM Product WHERE mid = m.mid
                                                    ) BETWEEN 201 AND 500";
                                                    break;
                                                case '3': // 501 ~ 800
                                                    $sqlAll .= " AND (
                                                        SELECT AVG(price) FROM Product WHERE mid = m.mid
                                                    ) BETWEEN 501 AND 800";
                                                    break;
                                                case '4': // 800 以上
                                                    $sqlAll .= " AND (
                                                        SELECT AVG(price) FROM Product WHERE mid = m.mid
                                                    ) > 800";
                                                    break;
                                            }

                                            // 排序處理
                                            $sqlAll .= " GROUP BY m.mid";

                                            if ($sortRating == 'asc') {
                                                $sqlAll .= " ORDER BY combinedRating ASC";
                                            } elseif ($sortRating == 'desc') {
                                                $sqlAll .= " ORDER BY combinedRating DESC";
                                            } else {
                                                $sqlAll .= " ORDER BY RAND()";
                                            }                   
                                            $stmt = $conn->prepare($sqlAll);
                                            $stmt->bind_param(str_repeat('i', count($matchedMids)), ...$matchedMids);
                                            $stmt->execute();
                                            $resultAll = $stmt->get_result();
                                        } else {
                                            $resultAll = false;
                                        }
            

                                        if ($resultAll && $resultAll->num_rows > 0) {
                                            while ($row = $resultAll->fetch_assoc()) {
                                                $isFavorited = false;
                                            if ($cid) {
                                                $checkFav = $conn->prepare("SELECT 1 FROM Favorite WHERE cid = ? AND mid = ?");
                                                $checkFav->bind_param("ii", $cid, $row["mid"]);
                                                $checkFav->execute();
                                                $checkFav->store_result();
                                                $isFavorited = $checkFav->num_rows > 0;
                                                $checkFav->close();
                                            }

                                            $heartClass = $isFavorited ? 'fa-solid text-danger' : 'fa-regular';


                                            echo '
                                            <div class="col-md-6 col-lg-4 col-xl-3">
                                                <div class="rounded position-relative fruite-item" style="cursor: pointer;" onclick="location.href=\'merchant.php?mid=' . urlencode($row["mid"]) . '\'">
                                                    <div class="fruite-img">
                                                        <img src="../' . $row["mPicture"] . '" class="img-fluid w-100 rounded-top" alt="">
                                                    </div>
                                                    <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top: 10px; left: 10px;">' . htmlspecialchars($row["categoryNames"] ?? '未分類') . '</div>
                                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom" style="height:175px; display:flex;flex-direction: column; justify-content: space-between;">
                                                        <div>
                                                            <h5>' . htmlspecialchars($row["mName"]) . '</h5>
                                                            <p>' . htmlspecialchars($row["mAddress"]) . '</p>
                                                        </div>
                                                        <div class="d-flex justify-content-between flex-lg-wrap" 每個商家的「加總後評價星等」與「加總後評價人數」，和前面你使用 combinedRating 一樣的方式。>
                                                            <p class="text-dark fs-5 fw-bold mb-0" onclick="event.stopPropagation();">
                                                                <i class="fa-heart favorite-icon ' . $heartClass . '" data-mid="' . $row["mid"] . '"></i>
                                                                
                                                            </p>
                                                            <p class="mb-0" style="text-align:right;">
                                                                <i class="fas fa-star fs-6 me-1 mb-0" style="color:#ffb524;"></i>' . 
                                                                number_format($row["combinedRating"], 1) . 
                                                                '/5 (' . 
                                                                ($row["ratingCount"] + $row["additionalRatingCount"]) . 
                                                                ')
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
                                        }
                                    } else {
                                        echo "<p class='text-center'>尚無商家資料</p>";
                                    }
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div> 
            </div> 
        </div>               
                         
        <!-- Fruits Shop End-->
        

        


        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
            <div class="container py-5">
                <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(226, 175, 24, 0.5) ;">
                    <div class="row g-4">
                        <div class="col-lg-3">
                            <a href="#">
                                <h1 class="text-primary mb-0">Junglebite</h1>
                                <p class="text-secondary mb-0">Fresh products</p>
                            </a>
                        </div>
                        <div class="col-lg-6">
                            <div class="position-relative mx-auto">
                                <input class="form-control border-0 w-100 py-3 px-4 rounded-pill" type="number" placeholder="Your Email">
                                <button type="submit" class="btn btn-primary border-0 border-secondary py-3 px-4 position-absolute rounded-pill text-white" style="top: 0; right: 0;">Subscribe Now</button>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="d-flex justify-content-end pt-3">
                                <a class="btn  btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-outline-secondary me-2 btn-md-square rounded-circle" href=""><i class="fab fa-youtube"></i></a>
                                <a class="btn btn-outline-secondary btn-md-square rounded-circle" href=""><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Why People Like us!</h4>
                            <p class="mb-4">typesetting, remaining essentially unchanged. It was 
                                popularised in the 1960s with the like Aldus PageMaker including of Lorem Ipsum.</p>
                            <a href="" class="btn border-secondary py-2 px-4 rounded-pill text-primary">Read More</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Shop Info</h4>
                            <a class="btn-link" href="">About Us</a>
                            <a class="btn-link" href="">Contact Us</a>
                            <a class="btn-link" href="">Privacy Policy</a>
                            <a class="btn-link" href="">Terms & Condition</a>
                            <a class="btn-link" href="">Return Policy</a>
                            <a class="btn-link" href="">FAQs & Help</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex flex-column text-start footer-item">
                            <h4 class="text-light mb-3">Account</h4>
                            <a class="btn-link" href="">My Account</a>
                            <a class="btn-link" href="">Shop details</a>
                            <a class="btn-link" href="">Shopping Cart</a>
                            <a class="btn-link" href="">Wishlist</a>
                            <a class="btn-link" href="">Order History</a>
                            <a class="btn-link" href="">International Orders</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-item">
                            <h4 class="text-light mb-3">Contact</h4>
                            <p>Address: 1429 Netus Rd, NY 48247</p>
                            <p>Email: Example@gmail.com</p>
                            <p>Phone: +0123 4567 8910</p>
                            <p>Payment Accepted</p>
                            <img src="../img/payment.png" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->
        <!-- 購物車 Modal -->
        <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cartModalLabel">購物車</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                    </div>
                    <div class="modal-body">

                    <?php
                    $cid = $_SESSION['cid'];
                    $cartTime = isset($_SESSION['cartTime']) ? $_SESSION['cartTime'] : '';

                    $sql = "SELECT c.*, p.pName, p.price, p.pPicture, m.mName
                            FROM CartItem c
                            JOIN Product p ON c.pid = p.pid
                            JOIN Merchant m ON c.mid = m.mid
                            WHERE c.cid = ? AND c.cartTime = ?
                            ORDER BY c.mid";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $cid, $cartTime);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $groupedItems = [];
                    while ($row = $result->fetch_assoc()) {
                        $groupedItems[$row['mid']]['mName'] = $row['mName'];
                        $groupedItems[$row['mid']]['items'][] = $row;
                    }
                    ?>

                    <?php foreach ($groupedItems as $mid => $group): ?>
                        <?php
                        $subtotal = 0;
                        foreach ($group['items'] as $item) {
                            $subtotal += $item['price'] * $item['quantity'];
                        }
                    ?>
                        <div class="mb-4">
                            <h5>
                                <a class="text-primary text-decoration-none" href="merchant.php?mid=<?= urlencode($group['items'][0]['mid']) ?>">
                                <?= htmlspecialchars($group['mName']) ?>
                                </a>
                            </h5>

                            <?php foreach ($group['items'] as $item): ?>
                            <div style="display: flex; align-items: flex-end; justify-content: space-between;">
                                <div class="d-flex align-items-center mb-3"
                                    id="cart-item-<?= $item['pid'] ?>-<?= $item['mid'] ?>"
                                    data-price="<?= $item['price'] ?>">
                                    <img src="../<?= htmlspecialchars($item['pPicture']) ?>" alt="<?= $item['pName'] ?>" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="ms-3 flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <strong><?= htmlspecialchars($item['pName']) ?> - NT$<?= htmlspecialchars($item['price']) ?></strong>
                                            </div>

                                            <?php if (!empty($item['specialNote'])): ?>
                                            <div class="text-muted small  mx-3">
                                                備註:
                                                <?= nl2br(htmlspecialchars($item['specialNote'])) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                

                                        <div class="d-flex align-items-center mt-2">
                                            <div class="input-group input-group-sm" style="max-width: 140px;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="handleDecrease(<?= $item['pid'] ?>, <?= $item['mid'] ?>)">-</button>
                                                <input type="text" id="qty-<?= $item['pid'] ?>-<?= $item['mid'] ?>" class="form-control text-center" value="<?= $item['quantity'] ?>" readonly>
                                                <button class="btn btn-outline-secondary" type="button" onclick="handleIncrease(<?= $item['pid'] ?>, <?= $item['mid'] ?>)">+</button>
                                            </div>
                                            <button class="btn btn-sm btn-outline-danger ms-3" onclick="removeItem(<?= $item['pid'] ?>, <?= $item['mid'] ?>)">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                            <!-- 新增編輯按鈕 -->
                                            <button class="btn btn-sm btn-outline-secondary ms-2"
                                                    onclick='openEditModal(<?= $item["pid"] ?>, <?= $item["mid"] ?>, <?= $item["quantity"] ?>, <?= json_encode($item["specialNote"] ?? "") ?>)'>
                                                <i class="fa-solid fa-pen"></i>
                                            </button>

                                            
                                        </div> 
                                    </div> 
                                </div>
                                <div>
                                    <span class="fw-bold" id="subtotal-<?= $item['pid'] ?>-<?= $item['mid'] ?>">
                                    NT$<?= $item['price'] * $item['quantity'] ?>
                                    </span>
                                </div>
                            
                            </div>
                            
                            
                            <?php endforeach; ?>
                            <!-- 小計與結帳按鈕 -->
                            <hr>
                            <div style="display: flex; flex-direction: column; align-items: flex-end;" class="mt-2">
                                <div class="fw-bold text-end">
                                小計：<span id="store-subtotal-<?= $mid ?>">NT$<?= $subtotal ?></span>
                                </div>
                                <a href="checkout.php?mid=<?= $mid ?>" class="btn btn-sm btn-primary mt-2 fw-bold py-2 text-white" >
                                前往結帳
                                </a>
                            </div>
                        </div>
                    
                        <?php endforeach; ?>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 修改購物車 -->
        <div class="modal fade" id="editCartModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">編輯購物車項目</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editPid">
                        <input type="hidden" id="editMid">

                        <div class="mb-3">
                            <label for="editQuantity" class="form-label">數量</label>
                            <input type="number" id="editQuantity" class="form-control" min="1">
                        </div>
                        <div class="mb-3">
                            <label for="editNote" class="form-label">備註</label>
                            <textarea id="editNote" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button class="btn btn-primary text-white" onclick="saveEdit()">儲存變更</button>
                    </div>
                </div>
            </div>
        </div>



        <!-- Back to Top -->
        <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   

    
    <!-- 訂單進度 -->
    <div class="modal fade" id="multiOrderModal" tabindex="-1" aria-labelledby="multiOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="multiOrderModalLabel">目前外送中訂單</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
            </div>

            <div class="modal-body">
                <?php foreach ($orders as $order): ?>
                <div class="border rounded p-3 mb-4 shadow-sm">
                    <h6>#<?= $order['tranId'] ?>：<?= htmlspecialchars($order['address_text']) ?></h6>
                    <?php
                    // 原始狀態
                    $status = $order['orderStatus'];
                    $deliveryStatus = $order['deliveryStatus'];

                    // 根據複合條件調整顯示用的狀態
                    if ($status === 'takeaway' && $deliveryStatus === 'arrived') {
                        $displayStatus = 'arrived'; // 已送達（待確認）
                    } elseif ($status === 'done' && $deliveryStatus === 'accept') {
                        $displayStatus = 'done'; // 等待外送員取餐
                    } elseif ($status === 'making' && $deliveryStatus === 'accept') {
                        $displayStatus = 'making'; // 商家製作中
                    } else {
                        $displayStatus = $status; // 預設使用 Transaction 的狀態
                    }

                    // 顯示文字對照
                    $statusMap = [
                        'new' => '等待商家接單',
                        'making' => '商家製作中',
                        'done' => '等待外送員取餐',
                        'takeaway' => '配送中',
                        'arrived' => '已送達（待確認）',
                        'reject' => '已被拒單'
                    ];
                    
                    ?>
                    <p><strong>目前狀態：</strong>
                        <?= $statusMap[$displayStatus] ?? '未知狀態' ?>
                    </p>

                    
                    <?php
                    $tranId = $order['tranId'];
                    // 資料從 Record 表撈
                    $detailsSql = "
                    SELECT r.*, p.pName 
                    FROM Record r
                    JOIN Product p ON r.pid = p.pid
                    WHERE r.tranId = ?
                    ";
                    
                    
                    $detailsStmt = $conn->prepare($detailsSql);
                    $detailsStmt->bind_param("i", $tranId);
                    $detailsStmt->execute();
                    $detailsResult = $detailsStmt->get_result();
                    $items = $detailsResult->fetch_all(MYSQLI_ASSOC);

                    // 計算小計
                    $subtotal = 0;
                    foreach ($items as $item) {
                        $subtotal += $item['salePrice'];
                    }

                    // 外送費與優惠邏輯
                    $deliveryFee = 30;
                    $serviceFee = $subtotal * 0.05;
                    $discountRate = 1.0;
                    // 預設優惠說明
                    $couponDescription = '沒有使用';

                    // 計算優惠折扣與免運
                    switch ($order['couponCode']) {
                        case 'CLAWWIN15':
                            $discountRate = 0.85;
                            $couponDescription = 'CLAWWIN15（15%折扣）';
                            break;
                        case 'CLAWWIN20':
                            $discountRate = 0.80;
                            $couponDescription = 'CLAWWIN20（20%折扣）';
                            break;
                        case 'CLAWWIN30':
                            $discountRate = 0.70;
                            $couponDescription = 'CLAWWIN30（30%折扣）';
                            break;
                        default:
                            $discountRate = 1.0;
                            break;
                    }

                    if ($order['couponCode'] === 'CLAWSHIP') {
                        $deliveryFee = 0;
                        $couponDescription = 'CLAWSHIP（免運費）';
                    }

                    $total = $subtotal * $discountRate + $deliveryFee + $serviceFee;
                    ?>



                    <?php if (
                        ($deliveryStatus === 'accept') 
                        // 配送中或已送達時顯示外送員名稱
                    ): ?>
                        <?php if (!empty($order['dpName'])): ?>
                            <p><strong>外送員：</strong><?= htmlspecialchars($order['dpName']) ?></p>
                        <?php else: ?>
                            <p><strong>外送員：</strong>尚未指派</p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <button class="btn btn-outline-primary my-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrder<?= $order['tranId'] ?>">
                        顯示訂單明細
                    </button>

                    <div class="collapse" id="collapseOrder<?= $order['tranId'] ?>">
                        <div class="card card-body">
                            <h6>訂購明細：</h6>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>商品名稱</th>
                                        <th>數量</th>
                                        <th>單價</th>
                                        <th>總價</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['pName']) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>$<?= $item['price'] ?></td>
                                        <td>$<?= $item['salePrice'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <p><strong>優惠券：</strong> <?= $couponDescription ?></p>
                            <!-- 小計顯示（含優惠） -->
                            <p><strong>小計：</strong>
                                <?php if ($discountRate < 1.0): ?>
                                    <del>$<?= number_format($subtotal, 0) ?></del>
                                    <span class="text-success">$<?= number_format($subtotal * $discountRate, 0) ?></span>
                                    
                                <?php else: ?>
                                    $<?= number_format($subtotal, 0) ?>
                                <?php endif; ?>
                            </p>

                            <!-- 運費顯示（含 CLAWSHIP） -->
                            <p><strong>運費：</strong>
                                <?php if ($order['couponCode'] === 'CLAWSHIP'): ?>
                                    <del>$30</del>
                                    <span class="text-success">$0</span>
                                    
                                <?php else: ?>
                                    $<?= number_format($deliveryFee, 0) ?>
                                <?php endif; ?>
                            </p>
                            <p><strong>平台服務費（5%）：</strong> $<?= number_format($serviceFee, 0) ?></p>
                            <p><strong>總金額：</strong> $<?= number_format($total, 0) ?></p>
                            <?php
                            $paymentDisplay = '';
                            if ($order['paymentMethod'] === 'cash') {
                                $paymentDisplay = '貨到付款';
                            } elseif ($order['paymentMethod'] === 'wallet') {
                                $paymentDisplay = '錢包付款';
                            } else {
                                $paymentDisplay = htmlspecialchars($order['paymentMethod']);
                            }
                            ?>
                            <p><strong>付款方式：</strong> <?= $paymentDisplay ?></p>

                        </div>
                    </div>

                    <?php if ($displayStatus === 'takeaway' && !empty($order['dLatitude']) && !empty($order['dLongitude']) && !empty($order['address_text'])): ?>
                        <div class="my-3">
                            <h6>配送路線：</h6>
                            <div id="map<?= $order['tranId'] ?>" style="height: 300px;" class="rounded shadow mb-2"></div>
                            <!-- 顯示預估抵達時間與距離 -->
                            <div id="infoBox<?= $order['tranId'] ?>" class="text-muted small ps-2"></div>
                        </div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                initMapAndRouteByAddress(
                                    <?= $order['tranId'] ?>,
                                    <?= $order['dLatitude'] ?>,
                                    <?= $order['dLongitude'] ?>,
                                    <?= json_encode($order['address_text']) ?>
                                );
                            });
                        </script>
                    <?php endif; ?>


                    <?php if ($displayStatus === 'arrived'): ?>
                        <button class="btn btn-success my-2" type="button" data-bs-toggle="collapse" data-bs-target="#ratingSection<?= $order['tranId'] ?>">
                            確認訂單
                        </button>

                        <div class="collapse mb-2" id="ratingSection<?= $order['tranId'] ?>">
                            <div class="card card-body">
                                <h6>訂單確認與評價</h6>
                                <!-- 送達照片 -->
                                <?php if (!empty($order['arrivePicture'])): ?>
                                    <div class="text-center my-3">
                                        <img src="../<?= htmlspecialchars($order['arrivePicture']) ?>" 
                                            alt="到達照片" 
                                            class="img-fluid rounded shadow"
                                            style="max-width: 300px;">
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="submit_review.php">
                                    <input type="hidden" name="tranId" value="<?= $order['tranId'] ?>">

                                    <!-- 商家評分 -->
                                    <label>商家評分：</label>
                                    <div class="star-rating" data-name="mRating">
                                        <i class="bi bi-star" data-value="1"></i>
                                        <i class="bi bi-star" data-value="2"></i>
                                        <i class="bi bi-star" data-value="3"></i>
                                        <i class="bi bi-star" data-value="4"></i>
                                        <i class="bi bi-star" data-value="5"></i>
                                        <input type="hidden" name="mRating" value="">
                                    </div>
                                    <textarea name="mComment" class="form-control mb-3" placeholder="商家評論"></textarea>

                                    <!-- 外送員評分 -->
                                    <label>外送員評分：</label>
                                    <div class="star-rating" data-name="dRating">
                                        <i class="bi bi-star" data-value="1"></i>
                                        <i class="bi bi-star" data-value="2"></i>
                                        <i class="bi bi-star" data-value="3"></i>
                                        <i class="bi bi-star" data-value="4"></i>
                                        <i class="bi bi-star" data-value="5"></i>
                                        <input type="hidden" name="dRating" value="">
                                    </div>
                                    <textarea name="dComment" class="form-control mb-3" placeholder="外送員評論"></textarea>

                                    <!-- 商品評分（每一項都要） -->
                                    <?php foreach ($items as $item): ?>
                                        <div class="mb-3 border rounded p-2">
                                            <p><strong><?= htmlspecialchars($item['pName']) ?></strong></p>
                                            <input type="hidden" name="pids[]" value="<?= $item['pid'] ?>">
                                            <label>商品評分：</label>
                                            <div class="star-rating" data-name="pRating[<?= $item['pid'] ?>]">
                                                <i class="bi bi-star" data-value="1"></i>
                                                <i class="bi bi-star" data-value="2"></i>
                                                <i class="bi bi-star" data-value="3"></i>
                                                <i class="bi bi-star" data-value="4"></i>
                                                <i class="bi bi-star" data-value="5"></i>
                                                <input type="hidden" name="pRating[<?= $item['pid'] ?>]" value="">
                                            </div>

                                            <textarea name="pComment[<?= $item['pid'] ?>]" class="form-control" placeholder="商品評論"></textarea>
                                        </div>
                                    <?php endforeach; ?>

                                    <button type="submit" class="btn btn-primary text-white">送出評價</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>


                    <!-- 外送進度條 -->
                    <div class="progress mb-2" style="height: 25px;">
                    <?php
                    $progressValues = [
                        'new' => 10,
                        'making' => 30,
                        'done' => 50,
                        'takeaway' => 80,
                        'arrived' => 100,
                        'reject' => 0
                    ];
                    $progress = $progressValues[$status] ?? 0;

                    // 當 orderStatus 為 'takeaway' 且 deliveryStatus 為 'arrived' 時，設定進度為 100%
                    if ($order['orderStatus'] === 'takeaway' && $order['deliveryStatus'] === 'arrived') {
                        $progress = 100;
                    }

                    $progressColor = $status === 'reject' ? 'bg-danger' : 'bg-success';
                    ?>
                        <div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= $progress ?>%;">
                            <?= $progress ?>%
                        </div>
                    </div>
                    <?php if ($status === 'reject'): ?>
                    <form method="post" action="confirm_reject.php" class="mt-3">
                        <input type="hidden" name="tranId" value="<?= $order['tranId'] ?>">
                        <button type="submit" class="btn btn-outline-danger">
                        確認拒單
                        </button>
                    </form>
                    <?php endif; ?>

                    <!-- 如果要加入詳細資訊或圖片可以加在這裡 -->

                </div>
                <?php endforeach; ?>
            </div>

            
        </div>
    </div>
    <script>
    function toggleDropdown() {
        var dropdown = document.getElementById("myDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }
    window.onclick = function(event) {
        var dropdown = document.getElementById("myDropdown");
        if (!event.target.closest('.dropdown') && dropdown && dropdown.style.display === "block") {
            dropdown.style.display = "none";
        }
    }
    </script>
        
        
    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    <script src="cart.js"></script>
    <!-- google map api -->
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTVTFQCTTjWiWW9w0OmIE5_OfyfrekW6E"></script> -->
    <script>
    function geocodeAddress(address, callback) {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address: address }, function(results, status) {
            if (status === "OK") {
                const location = results[0].geometry.location;
                callback(location.lat(), location.lng());
            } else {
                console.error("Geocode failed: " + status);
            }
        });
    }
    </script>
    <script>
    function initMapAndRouteByAddress(tranId, dLat, dLng, customerAddress) {
        geocodeAddress(customerAddress, function(cLat, cLng) {
            const map = new google.maps.Map(document.getElementById("map" + tranId), {
                zoom: 14,
                center: { lat: parseFloat(dLat), lng: parseFloat(dLng) },
            });

            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer({ map: map });

            directionsService.route(
                {
                    origin: { lat: parseFloat(dLat), lng: parseFloat(dLng) },
                    destination: { lat: parseFloat(cLat), lng: parseFloat(cLng) },
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                (response, status) => {
                    if (status === "OK") {
                        directionsRenderer.setDirections(response);
                        const leg = response.routes[0].legs[0];
                        const estimate = leg.duration.text;
                        const distance = leg.distance.text;

                        // 將預估時間與距離顯示在下方區塊
                        const infoDiv = document.getElementById("infoBox" + tranId);
                        infoDiv.innerHTML = `<strong>預估抵達時間：</strong> ${estimate}，距離：約 ${distance}`;
                    } else {
                        console.error("Directions request failed: " + status);
                    }
                }
            );
        });
    }

    </script>


    <!-- 星星 -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.star-rating').forEach(function (ratingDiv) {
            const stars = ratingDiv.querySelectorAll('i');
            const input = ratingDiv.querySelector('input[type="hidden"]');

            stars.forEach(function (star, idx) {
                star.addEventListener('click', function () {
                    const value = star.getAttribute('data-value');
                    input.value = value;

                    stars.forEach((s, i) => {
                        s.classList.toggle('bi-star-fill', i < value);
                        s.classList.toggle('bi-star', i >= value);
                    });
                });
            });
        });
    });
    </script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.favorite-icon').forEach(icon => {
        icon.addEventListener('click', function (e) {
        const mid = this.dataset.mid;
        const icon = this;
        const countSpan = this.nextElementSibling;

        fetch('toggle_favorite.php', {
            method: 'POST',
            headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `mid=${mid}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
            if (data.favorited) {
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid', 'text-danger');
            } else {
                icon.classList.remove('fa-solid', 'text-danger');
                icon.classList.add('fa-regular');
            }
            countSpan.textContent = data.favoritesCount;
            } else {
            alert("請先登入才能收藏！");
            }
        });
        });
    });
    });
    </script>


    <!-- 🟦 Modal: 更換外送地址 -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="index.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addressModalLabel">選擇外送地址</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <select class="form-select" name="selected_address_id" id="addressSelect">
                            <?php
                            $sql = "SELECT address_id, address_text FROM caddress WHERE cid = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $_SESSION['cid']); // 假設有 cid session
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row['address_id'] . '">' . htmlspecialchars($row['address_text']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">使用此地址</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    




    </body>

</html>