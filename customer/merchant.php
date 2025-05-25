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
    $userRow = mysqli_fetch_array($result);
}

$storeCount = 0;
$cartDate = $_SESSION['cartTime'] ?? date('Y-m-d'); // 補上這行
if (isset($_SESSION['cid'], $_SESSION['cartTime'])) {
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT mid) AS storeCount FROM CartItem WHERE cid = ? AND DATE(cartTime) = ?");
    $stmt->bind_param("is", $_SESSION['cid'], $cartDate);
    $stmt->execute();
    $stmt->bind_result($storeCount);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_address_id'])) {
    $selected_address_id = $_POST['selected_address_id'];
    $sql = "SELECT address_text FROM caddress WHERE address_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $selected_address_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['current_address'] = $row['address_text'];
        $mid = intval($_POST['mid']);
    }
    $stmt->close();
    header("Location: merchant.php?mid=$mid");
    exit;
}

$defaultAddress = $_SESSION['current_address'] ?? ($userRow['address'] ?? '尚未選擇地址');


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

// 如果從自己按「餐廳訂位」送過來，就 redirect 到 reservation.php
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['mid'])) {
  $mid = intval($_POST['mid']);
  // 將 mid 存到 session（後續 reservation.php 也能用）
  $_SESSION['mid'] = $mid;
  // 或直接用 GET 傳過去 reservation.php
  header("Location: reservation.php?mid={$mid}");
  exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'change_address') {
        // ✅ 更換地址邏輯
    } elseif (isset($_POST['action']) && $_POST['action'] === 'booking') {
        // ✅ 餐廳訂位邏輯
    }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>JungleBite</title>
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
    // 取得網址參數中的 mid
    $mid = isset($_GET['mid']) ? intval($_GET['mid']) : 0;

    // 初始化圖片路徑變數
    $bgImage = '';

    // 查詢該商家的圖片
    if ($mid > 0) {
        $sql = "SELECT mPicture FROM Merchant WHERE mid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $mid);
        $stmt->execute();
        $stmt->bind_result($mPicture);
        if ($stmt->fetch()) {
            $bgImage = "../" . $mPicture;  // 加上相對路徑
        }
        $stmt->close();
    }
    ?>
    
     <!-- Hero Start -->
    <div class="container-fluid mb-5 hero-header" style="padding:18rem 0; background-image: url('<?= htmlspecialchars($bgImage) ?>'); background-size: cover; background-position: center;">   
           
    </div>
        <!-- Hero End -->


    <!-- 顯示商家名稱、住址 -->
    <?php
    if (isset($_GET["mid"])) {
      $mid = $_GET["mid"];
      $sql = "SELECT * FROM Merchant WHERE mid = $mid";
      $result = mysqli_query($conn, $sql);
      $row = mysqli_fetch_array($result);
    }
    ?>
    <section class="py-2 ">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">

          <div class="section-header d-flex mb-5" style="flex-direction: column;">
            <h2><?php echo htmlspecialchars($row['mName']); ?></h2>
            <h4 class="text-muted"><?php echo htmlspecialchars($row['mAddress']); ?></h4>
            </div>
            <form method="post" action="merchant.php">
              <input type="hidden" name="mid" value="<?php echo $row['mid']; ?>">

              <button type="submit" style="background-color: #123524;    color: white;    padding: 10px 15px;    border-radius: 10px;">餐廳訂位</button>
            </form>
          </div>
            
          </div>
        </div>
      </div>
    </section>

    <!-- 顯示評論 -->
    <?php
    if (isset($_GET["mid"])) {
        $mid = $_GET["mid"];

        $sql = "SELECT T.tranId, T.mComment, T.mRating, P.pName, P.pid, P.mid, P.pDescription, P.price, P.pPicture
                FROM Transaction T
                JOIN Record R ON T.tranId = R.tranId
                JOIN Product P ON R.pid = P.pid
                WHERE T.mid = $mid AND T.mComment IS NOT NULL AND T.mRating IS NOT NULL AND T.mComment <> ''";
        $result = mysqli_query($conn, $sql);
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

    // 將資料依照 tranId 分組
    $groupedComments = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $tranId = $row['tranId'];

            if (!isset($groupedComments[$tranId])) {
                $groupedComments[$tranId] = [
                    'mComment' => htmlspecialchars($row['mComment']),
                    'mRating' => intval($row['mRating']),
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
    }
    ?>

    <section class="py-5">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="section-header d-flex mb-5" style="align-items: center;">
              <h2 class="section-title me-3">評論</h2>
              <a href="all_comments.php?mid=<?= $mid ?>" class="text-primary small" style="text-decoration:underline;">查看全部</a>
            </div>
          </div>
        </div>

        <div class="d-flex flex-nowrap overflow-auto gap-4">
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
                  <div class="card shadow-sm flex-shrink-0" style="width: 300px;">
                    <div class="card-body card-scrollable overflow-auto" style="height: 12.5em; display: flex; flex-direction: column; justify-content: space-between;">
                      <h6 class="card-text mb-2">' . $comment . '</h6>
                      <div>
                        <p class="card-text mb-2">購買商品：' . implode(', ', $productLinks) . '</p>
                        <p class="card-text">評分：' . $stars . ' (' . $rating . ' 星)</p>
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
    </section>





    

    <!-- 顯示類別 -->
    <section class="py-5 ">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">

            <div class="section-header d-flex flex-wrap justify-content-between mb-5">
              <h2 class="section-title">商品類別</h2>

              <div class="d-flex align-items-center">
                <!-- <a href="#" class="btn-link text-decoration-none">View All Categories →</a> -->
                <!-- <div class="swiper-buttons">
                  <button class="swiper-prev category-carousel-prev btn btn-primary">❮</button>
                  <button class="swiper-next category-carousel-next btn btn-primary">❯</button>
                </div> -->
                
              </div>
            </div>
            
          </div>
        </div>
        <?php
        if (isset($_GET["mid"])) {
            $mid = $_GET["mid"];

            // 取得店家資訊
            $sql = "SELECT * FROM Merchant WHERE mid = $mid";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($result);

            // 取得類別列表（依照 sort_order 排序）
            $sql = "SELECT productCategoriesId, productCategoryName FROM ProductCategoryList WHERE mid = $mid ORDER BY sort_order ASC";
            $result = mysqli_query($conn, $sql);
        }
        ?>

        
        <div class="row " style=" position:sticky; top: 300px; z-index: 1020; ">
          <div class="col-12">
            <!-- 滑動容器 -->
            <div class="category-scroll-wrapper py-2" style="overflow-x: auto; overflow-y: visible; white-space: nowrap;">

              <?php
              while ($category = mysqli_fetch_assoc($result)) {
                  $catId = $category['productCategoriesId'];
                  $catName = htmlspecialchars($category['productCategoryName']);
                  echo '
                  <div class="d-inline-block mx-2">
                    <a href="#category_' . $catId . '" class="nav-link category-item text-center px-4 py-4 bg-light rounded-3">
                      <h2 class="category-title m-0" style="font-size: 16px;">' . $catName . '</h2>
                    </a>
                  </div>';
              }
              ?>
            </div>
          </div>
        </div>


      </div>
    </section>
    <?php
    if (isset($_GET["mid"])) {
        $mid = $_GET["mid"];

        // 查詢所有分類
        $sql_categories = "SELECT productCategoriesId, productCategoryName FROM ProductCategoryList WHERE mid = $mid ORDER BY sort_order ASC";
        $result_categories = mysqli_query($conn, $sql_categories);

        while ($category = mysqli_fetch_assoc($result_categories)) {
            $catId = $category['productCategoriesId'];
            $catName = htmlspecialchars($category['productCategoryName']);

            echo '
            <!-- 顯示商品 -->
            <section class="py-3" id="category_' . $catId . '">     
              <div class="container-fluid fruite py-3">
                <h2 class=" text-start my-4">' . $catName . '</h2> 
                <div class="container py-0 px-0">
                  <div class="tab-class text-center">
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="g4 row">
                          
                        </div>
                      </div>
                    </div>
                    <div class="tab-content">
                      <div class="tab-pane active show">
                        <div class="row g4">
                          <div class="col-lg-12">
                            <div class="g4" style="display:flex; justify-content: flex-start; flex-wrap: wrap;">';

                // 取得這個分類下的商品
                $sql_products = "SELECT p.* FROM Product p 
                                JOIN ProductCategories pc ON p.pid = pc.pid 
                                WHERE pc.productCategoriesId = $catId AND p.mid = $mid";
                $result_products = mysqli_query($conn, $sql_products);

                if (mysqli_num_rows($result_products) > 0) {
                    while ($product = mysqli_fetch_assoc($result_products)) {
                        $pid = $product['pid'];  // 確保在這裡獲取 pid
                        $mid = $product['mid'];  // 確保在這裡獲取 mid
                        $pName = htmlspecialchars($product['pName']);
                        $pDescription = htmlspecialchars($product['pDescription']);
                        $price = $product['price'];
                        $picture = htmlspecialchars($product['pPicture'] ?? 'default.jpg');
                        $jsPName = addslashes($pName);
                        $jsPDescription = addslashes($pDescription);
                        $jsPrice = $price;
                        $jsPicture = '../' . $picture;
                        $shortDescription = mb_substr($pDescription, 0, 40, 'UTF-8'); // 只取前30字
                        if (mb_strlen($pDescription, 'UTF-8') > 40) {
                            $shortDescription .= '...'; // 加上省略號
                        }

                        
                        echo '
                          <div class="col-md-6 col-lg-6 col-xl-6 my-3" style="height: 200px; display:flex; flex-direction: row;">
                            <div class="h-100 rounded position-relative fruite-item"
                                style="cursor: pointer; display:flex; width: 95%;"
                                onclick="openProductModal(\'' . $jsPName . '\', \'' . $jsPDescription . '\', \'' . $jsPrice . '\', \'' . $jsPicture . '\', ' . $pid . ', ' . $mid . ')"
                                data-bs-toggle="modal" data-bs-target="#productModal">
                                <div class="fruite-img">
                                  <img src="../' . $picture . '" class="img-fluid w-100 rounded-start" alt="' . $pName . '">
                                
                                </div>
                                <div class="p-4 border border-secondary border-top-0 rounded-end d-flex flex-column flex-grow-1 text-start" style="width:279.4px;">
                                  <h5>' . $pName . '</h5>
                                  <h5>$' . $price . '</h5>
                                  <div class="text-muted flex-grow-1 d-flex">
                                    <h6 style="color:#747d88; font-weight:normal;">' . (!empty($shortDescription) ? $shortDescription : '&nbsp;') . '</h6>
                                  </div>
                                </div>
                            </div>
                          </div>
                        ';
                    }
                } else {
                    echo '<p>此分類尚無商品。</p>';
                }

                echo '
                    </div> <!-- brand-carousel -->
                  </div>
                </div>
              </div>
            </div>
          </div>
              
            </section>';
        }
    }
    ?>
    <section class="py-5 " style="display: none;">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">

            <!-- <div class="section-header d-flex justify-content-between"> -->
              
              <h2 class="section-title">Just arrived</h2>

              <div class="d-flex align-items-center">
                <!-- <a href="#" class="btn-link text-decoration-none">View All Categories →</a>
                <div class="swiper-buttons">
                  <button class="swiper-prev products-carousel-prev btn btn-primary">❮</button>
                  <button class="swiper-next products-carousel-next btn btn-primary">❯</button> -->
                </div>  
              </div>
            </div>
            
          </div>
        </div>
        <!-- 不能刪除 -->
        <div class="row" style="display: none;">
          <div class="col-md-12">
            <div class="products-carousel swiper">
              <div class="swiper-wrapper">
                <div class="product-item swiper-slide">
                  <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                  <figure>
                    <a href="index.html" title="Product Title">
                      <img src="../images/thumb-bananas.png"  class="tab-image">
                    </a>
                  </figure>
                  <h3>Sunstar Fresh Melon Juice</h3>
                  <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                  <span class="price">$18.00</span>
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="input-group product-qty">
                        <span class="input-group-btn">
                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                              <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                            </button>
                        </span>
                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                        <span class="input-group-btn">
                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                            </button>
                        </span>
                    </div>
                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                  </div>
                </div>
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

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
            <form method="post" action="merchant.php?mid=<?= htmlspecialchars($_GET['mid']) ?>">
                <input type="hidden" name="action" value="change_address">
                <input type="hidden" name="cartTime" value="<?= htmlspecialchars($cartTime) ?>">
                <input type="hidden" name="cid" value="<?= htmlspecialchars($_SESSION['cid']) ?>">
                <input type="hidden" name="pid" value="<?= htmlspecialchars($_GET['pid'] ?? '') ?>">
                <input type="hidden" name="mid" value="<?= htmlspecialchars($_GET['mid']) ?>">
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