<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include ('../dbh.php');
session_start(); // 必須是第一行，前面不能有空白或 HTML！
$mid = isset($_SESSION["mid"]) ? $_SESSION["mid"] : '';
if ($mid !== '') {
    $sql = "SELECT * FROM Merchant WHERE mid = $mid";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
}


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

        <!-- Spinner Start -->
        <!-- <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" role="status"></div>
        </div> -->
        <!-- Spinner End -->

        
        <!-- Navbar start -->
        <div class="container-fluid fixed-top">
            <div class="container topbar bg-primary d-none d-lg-block" style="padding: 20px;">
                <div class="d-flex justify-content-between">
                    <div class="top-info ps-2">
                    <!-- <i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#" class="text-white">客戶住址</a> -->
                        <!-- <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#" class="text-white">Email@Example.com</a></small> -->
                    </div>
                    <!-- <div class="top-link pe-2">
                        <a href="#" class="text-white"><small class="text-white mx-2">Privacy Policy</small>/</a>
                        <a href="#" class="text-white"><small class="text-white mx-2">Terms of Use</small>/</a>
                        <a href="#" class="text-white"><small class="text-white ms-2">Sales and Refunds</small></a>
                    </div> -->
                </div>
            </div>
            <div class="container px-0">
                <nav class="navbar navbar-light bg-white navbar-expand-xl">
                    <a href="merchant_shop.php?mid=<?php echo $mid; ?>" class="navbar-brand"><h1 class="text-primary display-6">Junglebite商家</h1></a>
                    <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars text-primary"></span>
                    </button>
                    <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                        <div class="navbar-nav mx-auto">
                            <!-- <a href="index.php" class="nav-item nav-link active">Home</a> -->
                            <a href="merchant_shop.php?mid=<?php echo $mid; ?>" class="nav-item nav-link">店面資訊</a>
                            <a href="menu.php?mid=<?php echo $mid; ?>" class="nav-item nav-link">菜單管理</a>
                            <a href="order.php?mid=<?= $mid; ?>" class="nav-item nav-link position-relative">
                                訂單
                                <span class="position-absolute bg-warning rounded-circle d-flex align-items-center justify-content-center text-dark fw-bold"
                                    style="top: 7px; right: -4px; height: 20px; min-width: 20px; font-size: 0.75rem;">
                                    3
                                </span>
                            </a>

                            <!-- <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                                <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                    <a href="cart.html" class="dropdown-item">Cart</a>
                                    <a href="chackout.html" class="dropdown-item">Chackout</a>
                                    <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                                    <a href="404.html" class="dropdown-item">404 Page</a>
                                </div>
                            </div> -->
                            <a href="contact.html" class="nav-item nav-link">聯繫平台</a>
                        </div>
                        <div class="d-flex m-3 me-0">
                            <!-- <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search text-primary"></i></button> -->
                            <!-- <a href="#" class="position-relative me-4 my-auto">
                                <i class="fa fa-shopping-bag fa-2x"></i>
                                <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 15px; height: 20px; min-width: 20px;">3</span>
                            </a> -->
                            <?php if (isset($_SESSION['login_success'])):?>
                            <!-- ✅ 已登入的顯示 -->
                            <div class="dropdown" style="position: relative; display: inline-block;">
                                <a href="javascript:void(0);" class="my-auto" >
                                    <img onclick="toggleDropdown()" src="  ../login/success.png" alt="Success" style="width: 40px; height: 40px; filter: brightness(0) saturate(100%) invert(42%) sepia(91%) saturate(356%) hue-rotate(71deg) brightness(94%) contrast(92%);">
                                </a>

                                <div id="myDropdown" class="dropdown-content" style="display: none; position: absolute; background-color: white; min-width: 120px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; right: 0; border-radius: 8px;">
                                    <?php if ($_SESSION['role'] === 'merchant'): ?>
                                        <a href="/database/merchant/setting.php" class="dropdown-item">商家設定</a>
                                    <?php elseif ($_SESSION['role'] === 'customer'): ?>
                                        <a href="/database/customer/setting.php" class="dropdown-item">個人設定</a>
                                        <a href="/database_project/allergy/allergy.php" class="dropdown-item">過敏設定</a>
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
        <div class="container-fluid fruite py-5" style=" position: absolute; top: 5rem;">
        
            <div class="container py-5">
                <div class="tab-class text-center">
                    <div class="row g-4" style=" display:flex;">
                        <div class="col-lg-4 text-start w-100" style=" display:flex; align-items: center;">
                            <h2>訂單管理</h2>
                            
                            
                            <form action="" method="get" class="mx-5 position-relative" style="display: flex; align-items: center;">
                                <input 
                                    class="form-control border-2 border-secondary py-3 px-4 rounded-pill" 
                                    type="text" 
                                    name="keyword" 
                                    placeholder="Search" 
                                    style="width: 40rem;"
                                    id="myInput"
                                >
                                <button 
                                    type="button" 
                                    class=" py-3 px-4 btn-primary rounded-pill text-white h-100 position-absolute" 
                                    style="margin-left: 0.5rem; right: -5rem; border:0px solid"
                                >
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </form>              
                        </div>
              
                        <div class="gap-1" style="display: flex; align-items: flex-end;">
                            <a style="font-size:1.5rem!important;" 
                                class="btn btn-primary order-toggle" 
                                data-bs-toggle="collapse" 
                                href="#collapse_order"
                                id="order"
                                role="button" 
                                aria-expanded="true" 
                                aria-controls="collapse_order" >
                                    <span style="color:#fff;" class="arrow"></span>
                                    <span style="color:#fff;!important" class="category-name">新訂單</span>
                            </a>
                        </div>
                        <?php
                        $sqlOrders = "SELECT o.*, c.cName, p.pName, t.transactionTime
                                    FROM `Orders` o
                                    JOIN `Product` p ON o.pid = p.pid
                                    JOIN `Customer` c ON o.cid = c.cid
                                    JOIN `Transaction` t ON o.tranId = t.tranId 
                                    WHERE o.mid = $mid
                                    ORDER BY t.transactionTime ASC";
                        $resultOrders = mysqli_query($conn, $sqlOrders);

                        if (!$resultOrders) {
                            die("查詢錯誤：" . mysqli_error($conn));
                        }

                        $orders = [];
                        if ($resultOrders) {
                            while ($row = mysqli_fetch_assoc($resultOrders)) {
                                $orders[$row['tranId']]['customer'] = $row['cName'];
                                $orders[$row['tranId']]['transactionTime'] = $row['transactionTime'];
                                $orders[$row['tranId']]['items'][] = $row;
                            }
                        }
                        ?>

                        <div class="collapse show" id="collapse_order">
                            <div class="card card-body" style="border:2px solid #626263;">

                                <?php foreach ($orders as $tranId => $order): 
                                    $total = 0;
                                    foreach ($order['items'] as $item) {
                                        $total += $item['price'] * $item['quantity'];
                                    }
                                ?>
                                    <!-- 點擊開啟 modal -->
                                    <div class="product-title">
                                        <div style="display:flex; justify-content: space-between;">

                                            <h4 style=" text-align:left; cursor:pointer;" >
                                                <i class="fa-solid fa-note-sticky"></i>
                                                訂單編號：#<?= $tranId ?>     
                                                <span style="text-decoration:underline; cursor:pointer; margin-left:2rem;"
                                                data-bs-toggle="modal"     
                                                data-bs-target="#orderModal_<?= $tranId ?>">
                                                <?= htmlspecialchars($order['customer']) ?> - $<?= $total ?>

                                                </span>
                                            </h4>
                                        </div>
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="orderModal_<?= $tranId ?>" tabindex="-1" aria-labelledby="orderModalLabel_<?= $tranId ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="orderModalLabel_<?= $tranId ?>">訂單詳細內容</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <ul class="list-group mb-3">
                                                        <?php foreach ($order['items'] as $item): ?>
                                                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                                                <div class="ms-2 me-auto">
                                                                    <div class="fw-bold" style="text-align:start;"><?= $item['quantity'] ?>份<span class="ms-3"><?= htmlspecialchars($item['pName']) ?></span></div>                                                                                                                                       
                                                                </div>
                                                                <span>$<?= $item['price'] * $item['quantity'] ?><div style="text-align:end;" class="text-muted">備註：<?= htmlspecialchars($item['specialNote']) ?></div></span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <div class="text-end fw-bold">總金額：$<?= $total ?></div>
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="post" action="handle_order.php" class="d-flex gap-2">
                                                        <input type="hidden" name="tranId" value="<?= $tranId ?>">
                                                        <input type="hidden" name="cid" value="<?= $order['items'][0]['cid'] ?>">
                                                        <button name="action" value="accept" class="btn btn-success">接受</button>
                                                        <button name="action" value="reject" class="btn btn-danger">拒絕</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="gap-1" style="display: flex; align-items: flex-end;">
                            <a style="font-size:1.5rem!important;" 
                                class="btn btn-primary order-toggle" 
                                data-bs-toggle="collapse" 
                                href="#collapse_making"
                                id="making"
                                role="button" 
                                aria-expanded="true" 
                                aria-controls="collapse_making" >
                                    <span style="color:#fff;" class="arrow"></span>
                                    <span style="color:#fff;!important" class="category-name">新訂單</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         

        

        



    <!-- Back to Top -->
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   
    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("myDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
            console.log('yes get');
        }
    </script>

        
    <!-- Jquery 連結 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="search.js" type ="text/javascript"></script>
    <!-- 加入 jQuery UI -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
   
    <script src="menu.js"></script>
    
    <!-- JavaScript Libraries -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    <!-- 引入 jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- 引入 jQuery UI -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="search_order.js"></script>

    </body>

</html>