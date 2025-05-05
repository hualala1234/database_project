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
$cartCount = 0;
if (isset($_SESSION['cid'], $_SESSION['cartTime'])) {
    $stmt = $conn->prepare("SELECT SUM(quantity) AS total FROM CartItem WHERE cid = ? AND cartTime = ?");
    $stmt->bind_param("is", $_SESSION['cid'], $_SESSION['cartTime']);
    $stmt->execute();
    $stmt->bind_result($cartCount);
    $stmt->fetch();
    $stmt->close();
}

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
        <!-- Navbar start -->
        <div class="container-fluid fixed-top">
        <div class="container topbar bg-primary d-none d-lg-block">
            <div class="d-flex justify-content-between">
            <div class="top-info ps-2">
                <i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#" class="text-white">客戶住址</a>
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
            <a href="index.php?cid=<?php echo $cid; ?>" class="navbar-brand"><h1 class="text-primary display-6">Junglebite</h1></a>
            <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                <div class="position-relative mx-auto">
                    <input class="form-control border-2 border-secondary  py-3 px-4 rounded-pill" style="width: 30rem;" type="number" placeholder="Search">
                    <button type="submit" class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100" style="top: 0; left: 82.5%;">搜尋</button>
                </div>
                                
                </div>
                <div class="d-flex m-3 me-0">
                                
                
                <?php if (isset($_SESSION['login_success'])): ?>
                <!-- ✅ 已登入的顯示 -->
                <div class="dropdown" style="position: relative; display: inline-block;">
                    <a href="javascript:void(0);" class="my-auto" onclick="toggleDropdown()">
                    <img src="  ../login/success.png" alt="Success" style="width: 40px; height: 40px; filter: brightness(0) saturate(100%) invert(42%) sepia(91%) saturate(356%) hue-rotate(71deg) brightness(94%) contrast(92%);">
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
        
        

        <!-- Cart Page Start -->
        <div class="container-fluid py-5 hero-header">
            <div class="container py-5">
            <?php
            // 確保有 mid 參數
            $mid = isset($_GET['mid']) ? intval($_GET['mid']) : 0;

            // 如果 mid 存在，從 Merchant 表查詢店家名稱
            if ($mid > 0) {
                $sql = "SELECT mName FROM Merchant WHERE mid = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $mid);
                $stmt->execute();
                $stmt->bind_result($mName);
                $stmt->fetch();
                $stmt->close();

                // 顯示店家名稱
                if ($mName) {
                    echo "<h1 class='check-title'><a href='merchant.php?mid=$mid'>$mName</a></h1>";
                } else {
                    echo '<h1>未找到店家名稱</h1>';
                }
            } else {
                echo "<h1>未選擇店家</h1>";
            }
            ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col"></th>
                            <th scope="col">商品名稱</th>
                            <th scope="col">價錢</th>
                            <th scope="col">數量</th>
                            <th scope="col">商品總金額</th>
                            <th scope="col">特殊指示</th>
                            <th scope="col"></th>
                          </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
                <div>
                    <h5 class="mb-0 text-dark py-4">運送住址</h5>
                </div>

                <div style="display:flex; justify-content: space-between; align-items: stretch;" >
                    <div class="mt-5 col-6" style="display:flex; flex-direction: column;">
                        <div style="display:flex;">
                            <input type="text" class="border-secondary  rounded me-5 py-2 mb-4" placeholder="優惠卷號碼">
                            <button class="btn btn-primary border-secondary rounded-pill px-4 py-2 text-white mb-4" type="button">使用優惠卷</button>
                        </div>
                        

                        <?php
                        // SQL 查詢
                        $sql = "SELECT cardName FROM Card WHERE cid = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $cid);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // 儲存卡片名稱
                        $cardNames = [];
                        while ($row = $result->fetch_assoc()) {
                            $cardNames[] = $row['cardName'];
                        }
                        ?>

                        <div>
                            <div>                
                                <h5 class="mb-0 text-dark py-3">付款方式</h5>
                                
                                <div style="display:flex; flex-direction:row; justify-content:flex-start; flex-wrap: wrap;">
                                    <div class="form-check text-start me-4">
                                        <input type="radio" class="form-check-input bg-primary border-0" id="Shipping-1" name="paymentMethod" value="Shipping">
                                        <label class="form-check-label text-dark" for="Shipping-1">錢包</label>
                                    </div>

                                    
                                    
                                    <?php
                                    if (!empty($cardNames)) {
                                        foreach ($cardNames as $index => $cardName) {
                                            $inputId = "cardOption_" . $index;
                                            echo "
                                            <div class='form-check text-start me-4'>                              
                                                <input type='radio' class='form-check-input bg-primary border-0' id='$inputId' name='paymentMethod' value='$cardName'>
                                                <label class='form-check-label text-dark' for='$inputId'>$cardName</label>                            
                                            </div>";
                                        }
                                    }
                                    ?>


                                    <div class="form-check text-start me-4">
                                        <input type="radio" class="form-check-input bg-primary border-0" id="newCardOption" name="paymentMethod" data-bs-toggle="modal" data-bs-target="#addCardModal">
                                        <label class="form-check-label text-dark" for="Shipping-1">新增信用卡</label>
                                    </div>
                                    <div class="form-check text-start me-4">
                                        <input type="radio" class="form-check-input bg-primary border-0" id="Shipping-cash" name="paymentMethod" value="Shipping">
                                        <label class="form-check-label text-dark" for="Shipping-3">貨到付款</label>
                                    </div>
                                </div>
                            
                                

                            </div>
                            <div class="my-4 w-100">
                                <h5><label for="specialNote" class="form-label">備註</label></h5>
                                <textarea id="specialNote" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                            
                    </div>
                    
                    <div class="col-5  mt-5">
                        <div class="bg-light rounded">
                            <div class="p-4">
                                <!-- <h1 class="display-6 mb-4">Cart <span class="fw-normal">Total</span></h1> -->
                                <div class="d-flex justify-content-between mb-4">
                                    <h5 class="mb-0 me-4 text-dark">小計</h5>
                                    <p class="mb-0 subtotal"></p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-0 me-4 text-dark">外送費</h5>
                                    <div class="">
                                        <p class="mb-0">$30</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between my-2">
                                    <h5 class="mb-0 me-4 text-dark">平台服務費</h5>
                                    <div class="">
                                        <p class="mb-0 platform-fee"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                                <h5 class="mb-0 ps-4 me-4 text-dark">總金額</h5>
                                <p class="mb-0 pe-4 grand-total"></p>
                            </div>
                            <div class="text-end px-2">
                                <button id="submitOrderBtn" class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4 " type="button">送出訂單</button>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>


        <!-- 新增信用卡 Modal -->
        <div class="modal fade" id="addCardModal" tabindex="-1" aria-labelledby="addCardModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="insert_card.php" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCardModalLabel">新增信用卡資訊</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="cid" value="<?php echo $_SESSION['cid']; ?>"> <!-- 用 session 傳入 cid -->

                            <div class="mb-3">
                                <label for="cardName" class="form-label">卡片暱稱</label>
                                <input type="text" class="form-control" id="cardName" name="cardName" required>
                            </div>

                            <div class="mb-3">
                                <label for="cardHolder" class="form-label">持卡人姓名</label>
                                <input type="text" class="form-control" id="cardHolder" name="cardHolder" maxlength="10" required>
                            </div>

                            <div class="mb-3">
                                <label for="cardNumber" class="form-label">卡號</label>
                                <input type="text" class="form-control" id="cardNumber" name="cardNumber" maxlength="16" required>
                            </div>

                            <div class="mb-3">
                                <label for="cardType" class="form-label">卡別</label>
                                <select class="form-select" id="cardType" name="cardType" required>
                                <option value="Visa">Visa</option>
                                <option value="MasterCard">MasterCard</option>
                                <option value="JCB">JCB</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="cvv" name="cvv" maxlength="3" required>
                            </div>

                            <div class="mb-3">
                                <label for="expirationDate" class="form-label">到期日</label>
                                <input type="date" class="form-control" id="expirationDate" name="expirationDate" required>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">儲存</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
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
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const newCardOption = document.getElementById("newCardOption");
                const submitBtn = document.getElementById("submitOrderBtn");

                // 當選擇新增信用卡
                newCardOption.addEventListener("change", function () {
                    if (this.checked) {
                        submitBtn.disabled = true;
                        // 顯示 modal（視你用的框架可能不同）
                        const newCardModal = new bootstrap.Modal(document.getElementById("newCardModal"));
                        newCardModal.show();
                    }
                });

                // 當卡片成功新增後（你可以在新增成功的 callback 裡觸發）
                window.enableOrderButton = function () {
                    submitBtn.disabled = false;
                };
            });
        </script>

        


        <script src="../js/jquery-1.11.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../js/plugins.js"></script>
        <script src="../js/script.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../lib/easing/easing.min.js"></script>
        <script src="../lib/waypoints/waypoints.min.js"></script>
        <script src="../lib/lightbox/js/lightbox.min.js"></script>
        <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Template Javascript -->
        <script src="../js/main.js"></script>
        <script src="checkout.js"></script> 


    </body>
</html>