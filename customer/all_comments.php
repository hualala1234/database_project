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
    $stmt->close();
    // 重定向回 index.php，讓頁面更新
    header("Location: index.php?cid=$cid");
    exit;
}

// 取得目前使用的地址（如果有從 modal 選擇過）
$defaultAddress = $_SESSION['current_address'] ?? ($row['address'] ?? '尚未選擇地址');

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
    <title>FoodMart - Free eCommerce Grocery Store HTML Website Template</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">

    <meta name="author" content="">
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="../css/vendor.css">
    <link rel="stylesheet" type="text/css" href="../css/merchant.css">
    <link href="../css/style.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    
    <!-- Libraries Stylesheet -->
    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/ea478a1bc4.js" crossorigin="anonymous"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 
   

    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&family=Open+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"> -->

  </head>
  <body>

    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
      <defs>
        <symbol xmlns="http://www.w3.org/2000/svg" id="link" viewBox="0 0 24 24">
          <path fill="currentColor" d="M12 19a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm5 0a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm0-4a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm-5 0a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm7-12h-1V2a1 1 0 0 0-2 0v1H8V2a1 1 0 0 0-2 0v1H5a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3Zm1 17a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-9h16Zm0-11H4V6a1 1 0 0 1 1-1h1v1a1 1 0 0 0 2 0V5h8v1a1 1 0 0 0 2 0V5h1a1 1 0 0 1 1 1ZM7 15a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm0 4a1 1 0 1 0-1-1a1 1 0 0 0 1 1Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="arrow-right" viewBox="0 0 24 24">
          <path fill="currentColor" d="M17.92 11.62a1 1 0 0 0-.21-.33l-5-5a1 1 0 0 0-1.42 1.42l3.3 3.29H7a1 1 0 0 0 0 2h7.59l-3.3 3.29a1 1 0 0 0 0 1.42a1 1 0 0 0 1.42 0l5-5a1 1 0 0 0 .21-.33a1 1 0 0 0 0-.76Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="category" viewBox="0 0 24 24">
          <path fill="currentColor" d="M19 5.5h-6.28l-.32-1a3 3 0 0 0-2.84-2H5a3 3 0 0 0-3 3v13a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3Zm1 13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-13a1 1 0 0 1 1-1h4.56a1 1 0 0 1 .95.68l.54 1.64a1 1 0 0 0 .95.68h7a1 1 0 0 1 1 1Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="calendar" viewBox="0 0 24 24">
          <path fill="currentColor" d="M19 4h-2V3a1 1 0 0 0-2 0v1H9V3a1 1 0 0 0-2 0v1H5a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3Zm1 15a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-7h16Zm0-9H4V7a1 1 0 0 1 1-1h2v1a1 1 0 0 0 2 0V6h6v1a1 1 0 0 0 2 0V6h2a1 1 0 0 1 1 1Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="heart" viewBox="0 0 24 24">
          <path fill="currentColor" d="M20.16 4.61A6.27 6.27 0 0 0 12 4a6.27 6.27 0 0 0-8.16 9.48l7.45 7.45a1 1 0 0 0 1.42 0l7.45-7.45a6.27 6.27 0 0 0 0-8.87Zm-1.41 7.46L12 18.81l-6.75-6.74a4.28 4.28 0 0 1 3-7.3a4.25 4.25 0 0 1 3 1.25a1 1 0 0 0 1.42 0a4.27 4.27 0 0 1 6 6.05Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="plus" viewBox="0 0 24 24">
          <path fill="currentColor" d="M19 11h-6V5a1 1 0 0 0-2 0v6H5a1 1 0 0 0 0 2h6v6a1 1 0 0 0 2 0v-6h6a1 1 0 0 0 0-2Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="minus" viewBox="0 0 24 24">
          <path fill="currentColor" d="M19 11H5a1 1 0 0 0 0 2h14a1 1 0 0 0 0-2Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="cart" viewBox="0 0 24 24">
          <path fill="currentColor" d="M8.5 19a1.5 1.5 0 1 0 1.5 1.5A1.5 1.5 0 0 0 8.5 19ZM19 16H7a1 1 0 0 1 0-2h8.491a3.013 3.013 0 0 0 2.885-2.176l1.585-5.55A1 1 0 0 0 19 5H6.74a3.007 3.007 0 0 0-2.82-2H3a1 1 0 0 0 0 2h.921a1.005 1.005 0 0 1 .962.725l.155.545v.005l1.641 5.742A3 3 0 0 0 7 18h12a1 1 0 0 0 0-2Zm-1.326-9l-1.22 4.274a1.005 1.005 0 0 1-.963.726H8.754l-.255-.892L7.326 7ZM16.5 19a1.5 1.5 0 1 0 1.5 1.5a1.5 1.5 0 0 0-1.5-1.5Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="check" viewBox="0 0 24 24">
          <path fill="currentColor" d="M18.71 7.21a1 1 0 0 0-1.42 0l-7.45 7.46l-3.13-3.14A1 1 0 1 0 5.29 13l3.84 3.84a1 1 0 0 0 1.42 0l8.16-8.16a1 1 0 0 0 0-1.47Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="trash" viewBox="0 0 24 24">
          <path fill="currentColor" d="M10 18a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1ZM20 6h-4V5a3 3 0 0 0-3-3h-2a3 3 0 0 0-3 3v1H4a1 1 0 0 0 0 2h1v11a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8h1a1 1 0 0 0 0-2ZM10 5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1h-4Zm7 14a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V8h10Zm-3-1a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="star-outline" viewBox="0 0 15 15">
          <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.5 9.804L5.337 11l.413-2.533L4 6.674l2.418-.37L7.5 4l1.082 2.304l2.418.37l-1.75 1.793L9.663 11L7.5 9.804Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="star-solid" viewBox="0 0 15 15">
          <path fill="currentColor" d="M7.953 3.788a.5.5 0 0 0-.906 0L6.08 5.85l-2.154.33a.5.5 0 0 0-.283.843l1.574 1.613l-.373 2.284a.5.5 0 0 0 .736.518l1.92-1.063l1.921 1.063a.5.5 0 0 0 .736-.519l-.373-2.283l1.574-1.613a.5.5 0 0 0-.283-.844L8.921 5.85l-.968-2.062Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="search" viewBox="0 0 24 24">
          <path fill="currentColor" d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="user" viewBox="0 0 24 24">
          <path fill="currentColor" d="M15.71 12.71a6 6 0 1 0-7.42 0a10 10 0 0 0-6.22 8.18a1 1 0 0 0 2 .22a8 8 0 0 1 15.9 0a1 1 0 0 0 1 .89h.11a1 1 0 0 0 .88-1.1a10 10 0 0 0-6.25-8.19ZM12 12a4 4 0 1 1 4-4a4 4 0 0 1-4 4Z"/>
        </symbol>
        <symbol xmlns="http://www.w3.org/2000/svg" id="close" viewBox="0 0 15 15">
          <path fill="currentColor" d="M7.953 3.788a.5.5 0 0 0-.906 0L6.08 5.85l-2.154.33a.5.5 0 0 0-.283.843l1.574 1.613l-.373 2.284a.5.5 0 0 0 .736.518l1.92-1.063l1.921 1.063a.5.5 0 0 0 .736-.519l-.373-2.283l1.574-1.613a.5.5 0 0 0-.283-.844L8.921 5.85l-.968-2.062Z"/>
        </symbol>
      </defs>
    </svg>

    <div class="preloader-wrapper">
      <div class="preloader">
      </div>
    </div>

    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasCart" aria-labelledby="My Cart">
      <div class="offcanvas-header justify-content-center">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <div class="order-md-last">
          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-primary">Your cart</span>
            <span class="badge bg-primary rounded-pill">3</span>
          </h4>
          <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between lh-sm">
              <div>
                <h6 class="my-0">Growers cider</h6>
                <small class="text-body-secondary">Brief description</small>
              </div>
              <span class="text-body-secondary">$12</span>
            </li>
            <li class="list-group-item d-flex justify-content-between lh-sm">
              <div>
                <h6 class="my-0">Fresh grapes</h6>
                <small class="text-body-secondary">Brief description</small>
              </div>
              <span class="text-body-secondary">$8</span>
            </li>
            <li class="list-group-item d-flex justify-content-between lh-sm">
              <div>
                <h6 class="my-0">Heinz tomato ketchup</h6>
                <small class="text-body-secondary">Brief description</small>
              </div>
              <span class="text-body-secondary">$5</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <span>Total (USD)</span>
              <strong>$20</strong>
            </li>
          </ul>
  
          <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to checkout</button>
        </div>
      </div>
    </div>
    
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasSearch" aria-labelledby="Search">
      <div class="offcanvas-header justify-content-center">
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <div class="order-md-last">
          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-primary">Search</span>
          </h4>
          <form role="search" action="index.html" method="get" class="d-flex mt-3 gap-0">
            <input class="form-control rounded-start rounded-0 bg-light" type="email" placeholder="What are you looking for?" aria-label="What are you looking for?">
            <button class="btn btn-dark rounded-end rounded-0" type="submit">Search</button>
          </form>
        </div>
      </div>
    </div>

    

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
          <a href="index.php?cid=<?php echo $cid; ?>" class="navbar-brand"><h1 class="text-primary display-6">Junglebite</h1></a>
          <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="fa fa-bars text-primary"></span>
          </button>
          <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
            <form method="GET" action="search.php">
              <div class="navbar-nav mx-auto">
                <div class="position-relative mx-auto">
                  <input name="keyword" class="form-control border-2 border-secondary py-3 px-4 rounded-pill" style="width: 30rem;" type="text" placeholder="Search">
                  <button type="submit" class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100" style="top: 0; left: 82.5%;">搜尋</button>
                </div>
              </div>
            </form>
            
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
                            
              <a href="#" class="position-relative me-4 ms-4 my-auto" data-bs-toggle="modal" data-bs-target="#cartModal">
                <i class="fa-solid fa-cart-shopping fa-2x"></i>
                <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 22px; height: 20px; min-width: 20px;">
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
                <?php if ($_SESSION['role'] === 'm'): ?>
                  <a href="/database/merchant/setting.php" class="dropdown-item">商家設定</a>
                <?php elseif ($_SESSION['role'] === 'c'): ?>
                  <a href="../login/login_customer/setting.php?cid=<?php echo $cid; ?>" class="dropdown-item">個人設定</a>
                  <a href="/database_project/allergy/allergy.php?cid=<?php echo $cid; ?>" class="dropdown-item">過敏設定</a>
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
    <?php
    $mid = $_GET['mid'];
    $sort = $_GET['sort'] ?? 'latest';  // 預設為最新
    $filterRating = $_GET['rating'] ?? 'all';

    // 排序 SQL
    $orderBy = match ($sort) {
        'highest' => 'T.mRating DESC',
        'lowest' => 'T.mRating ASC',
        default => 'T.transactionTime DESC',
    };

    // 篩選 SQL
    $ratingFilter = $filterRating !== 'all' ? "AND T.mRating = $filterRating" : '';

    $sql = "SELECT T.tranId, T.mComment, T.mRating, T.transactionTime, 
                P.pName, P.pid, P.mid, P.pDescription, P.price, P.pPicture
            FROM Transaction T
            JOIN Record R ON T.tranId = R.tranId
            JOIN Product P ON R.pid = P.pid
            WHERE T.mid = $mid 
                AND T.mComment IS NOT NULL 
                AND T.mRating IS NOT NULL 
                AND T.mComment <> ''
                $ratingFilter
            ORDER BY $orderBy";

    $result = mysqli_query($conn, $sql);

    // 分組資料
    $groupedComments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tranId = $row['tranId'];
        if (!isset($groupedComments[$tranId])) {
            $groupedComments[$tranId] = [
                'mComment' => htmlspecialchars($row['mComment']),
                'mRating' => intval($row['mRating']),
                'transactionTime' => $row['transactionTime'],
                'products' => [],
            ];
        }
        $groupedComments[$tranId]['products'][] = [
            'pName' => htmlspecialchars($row['pName']),
            'pDescription' => addslashes($row['pDescription']),
            'price' => $row['price'],
            'pPicture' => '../' . $row['pPicture'],
            'pid' => $row['pid'],
            'mid' => $row['mid'],
        ];
    }
    function renderStars($rating) {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '<i class="bi bi-star-fill text-warning"></i>';
            } else {
                $stars .= '<i class="bi bi-star text-warning"></i>';
            }
        }
        return $stars;
    }

    ?>

    <!-- 篩選與排序選單 -->
    <div class="container-fluid fruite py-5  hero-header">
        <form method="get" class="d-flex flex-wrap gap-3 align-items-center">
            <input type="hidden" name="mid" value="<?= $mid ?>">
            <label>排序：
                <select name="sort" class="form-select d-inline-block w-auto" onchange="this.form.submit()">
                    <option value="latest" <?= $sort == 'latest' ? 'selected' : '' ?>>最新</option>
                    <option value="highest" <?= $sort == 'highest' ? 'selected' : '' ?>>評價最高</option>
                    <option value="lowest" <?= $sort == 'lowest' ? 'selected' : '' ?>>評價最低</option>
                </select>
            </label>

            <label>評價：
                <select name="rating" class="form-select d-inline-block w-auto" onchange="this.form.submit()">
                    <option value="all" <?= $filterRating == 'all' ? 'selected' : '' ?>>全部</option>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= $filterRating == $i ? 'selected' : '' ?>><?= $i ?> 星</option>
                    <?php endfor; ?>
                </select>
            </label>
        </form>
    </div>
    <div class="container">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php
            if (!empty($groupedComments)) {
                foreach ($groupedComments as $commentData) {
                    $comment = $commentData['mComment'];
                    $rating = $commentData['mRating'];
                    $stars = renderStars($rating);
                    $productLinks = [];

                    foreach ($commentData['products'] as $product) {
                        $pName = addslashes($product['pName']);
                        $pDescription = $product['pDescription'];
                        $price = $product['price'];
                        $pPicture = $product['pPicture'];
                        $pid = $product['pid'];
                        $mid = $product['mid'];

                        $productLinks[] = '<a href="#"
                            class="text-decoration-underline text-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#productModal"
                            onclick="openProductModal(\'' . $pName . '\', \'' . $pDescription . '\', \'' . $price . '\', \'' . $pPicture . '\', ' . $pid . ', ' . $mid . ')">'
                            . $pName .
                            '</a>';
                    }

                    echo '
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body overflow-auto" style="max-height: 12.5em;">
                                <p class="card-text mb-2">' . $comment . '</p>
                                <div class="mt-auto">
                                    <p class="card-text mb-1">商品：' . implode(', ', $productLinks) . '</p>
                                    <p class="card-text">評分：' . $stars . ' (' . $rating . ' 星)</p>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="col-12"><p class="text-muted">目前尚無評論。</p></div>';
            }
            ?>
        </div>
    </div>

    

    <!-- 加入購物車 -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="productModalLabel">商品名稱</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
          </div>
          <div class="modal-body d-flex flex-column flex-md-row gap-4">
            <img id="modalProductImage" src="" class="img-fluid rounded" style="max-width: 300px;">
            <div class="flex-fill">
              <input type="hidden" id="modalPid">
              <input type="hidden" id="modalMid">
              <p id="modalProductDescription" class="mb-3 text-muted"></p>
              <h4 id="modalProductPrice" class="mb-3"></h4>
              <div class="mb-3">
                <label for="specialNote" class="form-label">特殊指示</label>
                <textarea id="specialNote" class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-3">
                <label for="Quantity" class="form-label">數量</label>
                <input type="number" class="form-control" id="Quantity" value="1" min="1">
              </div>
              <button class="btn btn-primary w-100" onclick="addToCart()">加入購物車</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
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
                                onclick="openEditModal(<?= $item['pid'] ?>, <?= $item['mid'] ?>, <?= $item['quantity'] ?>, decodeURIComponent('<?= rawurlencode($item['specialNote']) ?>'))">
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

    <!-- 🟦 Modal: 更換外送地址 -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="post" action="index.php?cid=<?php echo $cid; ?>">
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
                                $stmt->bind_param("i", $cid); // 假設有 cid session
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
    



    <script src="../js/jquery-1.11.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="../js/plugins.js"></script>
    <script src="../js/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    <script src="cart.js"></script>


    

  </body>
</html>