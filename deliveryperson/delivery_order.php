<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include('../dbh.php');
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}
// 假設外送員登入後將 did 存入 session
$did = $_SESSION['did'] ?? null;
if (!$did) {
    die("未登入外送員帳號。");
}

// ✅ 查詢該外送員已接受的訂單數量
$newOrderCount = 0;
$sql = "SELECT COUNT(*) AS count FROM dOrders WHERE did = ? AND orderStatus = 'accept'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $did);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $newOrderCount = $row['count'];
}
$stmt->close()
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
        <div class="container-fluid fruite py-5" style=" position: absolute; top: 5rem;">
            <div class="container-fluid fixed-top" >
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
                        <a href="delivery_index.php?did=<?php echo $did; ?>" class="navbar-brand"><h1 class="text-primary display-6">Junglebite外送員</h1></a>
                        <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                            <span class="fa fa-bars text-primary"></span>
                        </button>
                        <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                            <div class="navbar-nav mx-auto">
                                <a href="delivery_index.php?did=<?php echo $did; ?>" class="nav-item nav-link">現有訂單</a>
                                <a href="delivery_order.php?did=<?php echo $did; ?>" class="nav-item nav-link position-relative">
                                    已接單
                                    <?php if ($newOrderCount > 0): ?>
                                        <span class="position-absolute bg-warning rounded-circle d-flex align-items-center justify-content-center text-dark fw-bold"
                                            style="top: 7px; right: -4px; height: 20px; min-width: 20px; font-size: 0.75rem;">
                                            <?= $newOrderCount ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                                <a href="../walletAndrecord/d_wallet.php?id=<?php echo $did; ?>&role=d" class="nav-item nav-link active">接單紀錄</a>
                                <!-- <a href="menu.php?mid=<?php echo $mid; ?>" class="nav-item nav-link">菜單管理</a>
                                <a href="order.php?mid=<?= $mid; ?>" class="nav-item nav-link position-relative">
                                    訂單
                                    <span class="position-absolute bg-warning rounded-circle d-flex align-items-center justify-content-center text-dark fw-bold"
                                        style="top: 7px; right: -4px; height: 20px; min-width: 20px; font-size: 0.75rem;">
                                        3
                                    </span>
                                </a> -->

                                <!-- <div class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                                    <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                        <a href="cart.html" class="dropdown-item">Cart</a>
                                        <a href="chackout.html" class="dropdown-item">Chackout</a>
                                        <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                                        <a href="404.html" class="dropdown-item">404 Page</a>
                                    </div>
                                </div> -->
                                <!-- <a href="contact.html" class="nav-item nav-link">聯繫平台</a> -->
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
        </div>
        <!-- Navbar End -->
        <div class="container py-5 hero-header">
                <div class="tab-class text-center">
                    <div class="row g-4" style=" display:flex;">
                        <div class="col-lg-4 text-start w-100" style=" display:flex; align-items: center;">
                            <h2>已接單</h2>
                            
                            
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

                        <?php
                        // 撈已接單訂單
                        $sql = "
                            SELECT d.tranId, d.cid, t.mid, t.totalPrice, t.address_text,
                                c.cName AS customerName, m.mAddress, t.paymentMethod
                            FROM dOrders d
                            JOIN Transaction t ON t.tranId = d.tranId
                            JOIN customer c ON t.cid = c.cid
                            JOIN merchant m ON t.mid = m.mid
                            WHERE d.orderStatus != 'arrived'
                            AND d.did = ?
                            AND NOT EXISTS (
                                SELECT 1
                                FROM DeliverySkip ds
                                WHERE ds.did = d.did AND ds.tranId = d.tranId
                            )
                            ORDER BY d.tranId
                        ";


                    

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $did);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $takeOrders = $result->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();
                        

                        ?>

                        <!-- 已接單 -->
                        <div id="currentOrders" class="section container">
                            <?php if (empty($takeOrders)): ?>
                                <p class="text-muted">目前沒有可接的訂單。</p>
                            <?php else: ?>
                                <?php foreach ($takeOrders as $order):
                                    $tranId = $order['tranId'];
                        
                                    $sqlItems = "
                                        SELECT r.pid, r.quantity, r.price, p.pName
                                        FROM record r
                                        JOIN product p ON r.pid = p.pid
                                        WHERE r.tranId = ?
                                    ";
                                    $stmtItems = $conn->prepare($sqlItems);
                                    $stmtItems->bind_param("i", $tranId);
                                    $stmtItems->execute();
                                    $resultItems = $stmtItems->get_result();
                                    $order['items'] = $resultItems->fetch_all(MYSQLI_ASSOC);
                                    $stmtItems->close(); ?>
                                    <div class="card my-2">
                                        <div class="card-body d-flex justify-content-between align-items-center" style="border: 2px solid #45595b; border-radius: 10px; flex-direction:column;">
                                            <div style="display:flex; flex-direction:row; align-items:flex-end;">
                                                <h4>
                                                    
                                                    <span style="text-decoration:underline; cursor:pointer; margin:0 1rem;"
                                                        data-bs-toggle="modal"     
                                                        data-bs-target="#orderModal_<?= htmlspecialchars($order['tranId']) ?>">
                                                        <i class="fa-solid fa-note-sticky"></i>
                                                        #<?= htmlspecialchars($order['tranId']) ?>   
                                                
                                                    </span>
                                                    <?= htmlspecialchars($order['mAddress']) ?> → <?= htmlspecialchars($order['address_text']) ?> 
                                                </h4>
                                                <div class="d-flex gap-2">
                                                    
                                                        <input type="hidden" name="tranId" value="<?= htmlspecialchars($order['tranId']) ?>">
                                                        <button class="btn btn-success ms-4" data-bs-toggle="modal" data-bs-target="#arriveModal_<?= $order['tranId'] ?>">送達</button>
                                                   
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="arriveModal_<?= $order['tranId'] ?>" tabindex="-1" aria-labelledby="arriveModalLabel_<?= $order['tranId'] ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form class="modal-content" action="handle_order.php" method="post" enctype="multipart/form-data">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="arriveModalLabel_<?= $order['tranId'] ?>">上傳送達照片 - 訂單 #<?= $order['tranId'] ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="tranId" value="<?= $order['tranId'] ?>">
                                                                    <div class="mb-3">
                                                                    <label for="arrivePhoto_<?= $order['tranId'] ?>" class="form-label">選擇照片</label>
                                                                    <input type="file" class="form-control" name="arrivePhoto" id="arrivePhoto_<?= $order['tranId'] ?>" required accept="image/*">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" name="action" value="arrived" class="btn btn-success">送出</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                            <!-- 卡片中新增區塊 -->
                                            <span id="duration_<?= $order['tranId'] ?>" class="text-muted">預估時間載入中...</span>
                                            <div id="map_<?= $order['tranId'] ?>" style="height: 300px; width: 100%; margin-top: 1rem;"></div>


                                        </div>
                                        
                                    </div>
                                


                                    <!-- Modal -->
                                    <div class="modal fade" id="orderModal_<?= htmlspecialchars($order['tranId']) ?>" tabindex="-1" aria-labelledby="orderModalLabel_<?= htmlspecialchars($order['tranId']) ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="orderModalLabel_<?= htmlspecialchars($order['tranId']) ?>">訂單詳細內容</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h5 class="text-muted" style="text-align:left;">
                                                        消費者：<?= htmlspecialchars($order['customerName']) ?>       
                                                    </h5>
                                                    
                                                    <ul class="list-group mb-3">
                                                        <?php foreach ($order['items'] as $item): ?>
                                                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                                                <div class="ms-2 me-auto">
                                                                    <div class="fw-bold" style="text-align:start;">
                                                                        <?= htmlspecialchars($item['quantity']) ?> 份
                                                                        <span class="ms-3"><?= htmlspecialchars($item['pName']) ?></span>
                                                                    </div>                                                                                                                                       
                                                                </div>
                                                                <span>
                                                                    $<?= htmlspecialchars($item['price'] * $item['quantity']) ?>
                                                                    <div style="text-align:end;" class="text-muted">
                                                                        備註：<?= htmlspecialchars($item['pComment'] ?? '無') ?>
                                                                    </div>
                                                                </span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <div style="display:flex; justify-content: space-between;">
                                                        <h5 class="text-muted">
                                                            付款方式：
                                                            <?php 
                                                                if ($order['paymentMethod'] === 'cash') {
                                                                    echo '貨到付款';
                                                                } elseif ($order['paymentMethod'] === 'wallet') {
                                                                    echo '錢包';
                                                                } else {
                                                                    echo '卡片';
                                                                }
                                                            ?>
                                                        </h5>
                                                        <h5>總金額：$<?= htmlspecialchars($order['totalPrice']) ?> </h5>
                                                    </div>
                                                    
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="post" action="handle_order.php" class="d-flex gap-2">
                                                        <input type="hidden" name="tranId" value="<?= htmlspecialchars($order['tranId']) ?>">
                                                        <button name="action" value="accept" class="btn btn-success text-white">接受</button>
                                                        <button name="action" value="reject" class="btn btn-danger">拒絕</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>                        
                    </div>
                </div>
                    <!-- Back to Top -->
                <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   
        </div>
         

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
    <!-- 引入 google map API -->
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTVTFQCTTjWiWW9w0OmIE5_OfyfrekW6E"></script> -->
    <script>
    function initMapForOrder(tranId, origin, destination) {
        const mapElement = document.getElementById("map_" + tranId);
        if (!mapElement) return;

        const map = new google.maps.Map(mapElement, {
            zoom: 13,
            center: { lat: 22.6273, lng: 120.3014 }, // 高雄中心點
        });

        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer();
        directionsRenderer.setMap(map);

        // 抓外送員目前位置
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const currentLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                // ✅ 新增：將位置送到後端
                fetch('update_location.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(currentLocation)
                });

                directionsService.route({
                    origin: currentLocation,
                    destination: destination,
                    waypoints: [
                        { location: origin, stopover: true }
                    ],
                    travelMode: google.maps.TravelMode.DRIVING,
                    drivingOptions: {
                        departureTime: new Date()
                    }
                }, function (response, status) {
                    if (status === "OK") {
                        directionsRenderer.setDirections(response);
                        const legs = response.routes[0].legs;
                        const duration = response.routes[0].legs.reduce((total, leg) => total + leg.duration.value, 0);
                        const minutes = Math.round(duration / 60);
                        document.getElementById("duration_" + tranId).innerText = "預估時間：約 " + minutes + " 分鐘";
                        // ✅ 新增：總距離
                        const totalDistance = legs.reduce((sum, leg) => sum + leg.distance.value, 0); // 單位：公尺
                        const km = (totalDistance / 1000).toFixed(1); // 公里格式化為小數點1位

                        // ✅ 顯示時間與距離
                        document.getElementById("duration_" + tranId).innerText =
                            "預估時間：約 " + minutes + " 分鐘，距離：約 " + km + " 公里";
                    } else {
                        console.error("路線規劃失敗:", status);
                        document.getElementById("duration_" + tranId).innerText = "無法取得時間";
                    }
                });
            }, function (error) {
                console.error("無法取得位置：", error.message);
                document.getElementById("duration_" + tranId).innerText = "無法取得目前位置";
            });
        } else {
            console.error("此瀏覽器不支援定位");
            document.getElementById("duration_" + tranId).innerText = "此裝置不支援定位";
        }
    }
    </script>

    <script>
    // 頁面載入後逐一初始化地圖與路線
    document.addEventListener("DOMContentLoaded", function () {
        <?php foreach ($takeOrders as $order): 
            $origin = json_encode($order['mAddress']);
            $dest = json_encode($order['address_text']);
            $tranId = $order['tranId'];
        ?>
            initMapForOrder(<?= $tranId ?>, <?= $origin ?>, <?= $dest ?>);
        <?php endforeach; ?>
    });
    </script>




    </body>
</html>
         
