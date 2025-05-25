<?php
session_start();
$cid = $_SESSION['cid'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="claw.css">
    
      <title>Claw Machine</title>
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
                  <a href="../customer/index.php?cid=<?php echo $cid; ?>" class="navbar-brand text-decoration-none"><h1 class="text-primary display-6">Junglebite 優惠活動</h1></a>
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
                                        <a href="../login/login_customer/setting.php?cid=<?php echo $cid; ?>" class="dropdown-item text-decoration-none">個人設定</a>
                                        <a href="/database_project/allergy/allergy.php?cid=<?php echo $cid; ?>" class="dropdown-item text-decoration-none">過敏設定</a>
                                        <!-- <a href="../claw_machine/claw.php?cid=< ?php echo $cid; ?>" class="dropdown-item text-decoration-none">優惠券活動</a> -->
                                        <a href="../walletAndrecord/c_wallet.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item text-decoration-none">錢包</a>
                                        <a href="../walletAndrecord/c_record.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item text-decoration-none">交易紀錄</a>
                                        <a href="../customer/friends.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item text-decoration-none">我的好友</a>
                                        <a href="../wheel/wheel.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item text-decoration-none">命運轉盤</a>
                                        <a href="../customer/myfavorite.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item text-decoration-none">我的愛店</a>
                                        <a href="/database_project/customer/reservation.php?panel=history" class="dropdown-item">我的訂位</a>
 
                                    <?php elseif ($_SESSION['role'] === 'd'): ?>
                                        <a href="/database/customer/setting.php" class="dropdown-item">外送員設定</a>
                                    <?php elseif ($_SESSION['role'] === 'platform'): ?>
                                        <a href="/database/customer/setting.php" class="dropdown-item">平台設定</a>
                                    <?php endif; ?>
                                        <a href="/database_project/login/login_customer/logout.php" class="dropdown-item text-decoration-none">Logout</a>


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

    <div class="wrapper">
      <div id="boxx">
        <a href="claw.php?cid=<?php echo $cid; ?>&role=c">
          <button class="game1" style="margin-left: 20px;">Game1</button>
        </a>
        <a href="my_coupons.php?cid=<?php echo $cid; ?>&role=c">
            <button class="game2" style="margin-left: 20px;">Game2</button>
        </a>
      </div>
      <div class="collection-box pix"></div>
      <div class="claw-machine">
        <div class="box pix">
          <div class="machine-top pix">
            <div class="arm-joint pix">
              <div class="arm pix">
                <div class="claws pix"></div>
              </div>
            </div>
            <div class="rail hori pix"></div>
            <div class="rail vert pix"></div>
          </div>
          <div class="machine-bottom pix">
            <div class="collection-point pix"></div>
          </div>
        </div>
        <div class="control pix">
          <div class="cover left"></div>
          <button class="hori-btn pix" style= "margin-right:15px; margin-top:5px !important"></button>
          <button class="vert-btn pix" style= "margin-right:15px; margin-top:5px"></button>
          <div class="cover right">
            <div class="instruction pix"></div>
          </div>
          <div class="cover bottom"></div>
          <div class="cover top">
            <div class="collection-arrow pix"></div>
          </div>
          <div class="collection-point pix"></div>
        </div>
      </div>
    </div>

    <div class="popup-message hidden">
      <div class="popup-content">
        <h2>🎉 恭喜夾到娃娃！</h2>
        <p>優惠碼：<strong id="promocode">CLAWWIN20</strong></p>
        <!-- <button class="claim-coupon">領取優惠</button> 新增領取優惠按鈕 -->
      </div>
    </div>
    

    <div class="confetti-container"></div>

    <img id="couponImage" src="getcoupon.png" alt="已領取優惠券" style="
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 10001;
      cursor: pointer;
      max-width: 80%;
      height: 500px;
    ">


  
    <script src="claw.js"></script>
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