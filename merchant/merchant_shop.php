<?php
    include ('../dbh.php');
    
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

        <?php
        if(isset($_GET["mid"])){
            $mid = $_GET["mid"];
            $sql = "SELECT * FROM Merchant WHERE mid = $mid";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($result);
        }
        
        ?>
        <!-- Navbar start -->
        <div class="container-fluid fixed-top">
            <div class="container topbar bg-primary d-none d-lg-block">
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
                    <a href="index.html" class="navbar-brand"><h1 class="text-primary display-6">Junglebite商家</h1></a>
                    <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars text-primary"></span>
                    </button>
                    <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                        <div class="navbar-nav mx-auto">
                            <!-- <a href="index.php" class="nav-item nav-link active">Home</a> -->
                            <a href="merchant_shop.php?mid=<?php echo $mid; ?>" class="nav-item nav-link">店面資訊</a>
                            <a href="menu.php?mid=<?php echo $mid; ?>" class="nav-item nav-link">菜單管理</a>
                            <a href="shop-detail.html" class="nav-item nav-link">訂單</a>
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
                            <a href="#" class="my-auto">
                                <i class="fas fa-user fa-2x"></i>
                            </a>
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
                        <div class="col-lg-4 text-start">
                            <h2>店面資訊</h2>
                        </div>
                        <div class="modal-body" style="margin-top:0;">
                            <form action="../process.php" method="post" enctype="multipart/form-data">
                                <input  type="hidden" name="mid" value="<?= $row['mid'] ?>">

                                <div class="py-3">
                                    <h3>店面名稱</h3>
                                    <input style="font-size: 1.5rem; font-weight: bold;" type="text" class="form-control" name="mName" value="<?= $row['mName'] ?>" placeholder="輸入店面名稱">
                                </div>

                                <div class="py-3">
                                    <h3>住址</h3>
                                    <input style="font-size: 1.5em; font-weight: bold;" type="text" class="form-control" name="mAddress" value="<?= $row['mAddress'] ?>" placeholder="輸入店面地址">
                                </div>

                                <div class="py-3">
                                    <h3>目前圖片</h3>
                                    <?php if (!empty($row['mPicture'])): ?>
                                        <!-- 修改圖片路徑 -->
                                        <img src="../<?= $row['mPicture'] ?>" alt="Merchant Image" style="max-width: 20rem; max-height: 20rem; margin-top: 1.5rem">
                                    <?php else: ?>
                                        <p>No image available</p>
                                    <?php endif; ?>
                                </div>


                                <div class="py-3">
                                    <h3>上傳新照片</h3>
                                    <input style="font-size: 1.5em; font-weight: bold;" type="file" class="form-control" name="ImageUpload">
                                </div>

                                <div class="py-3">
                                    <h3>營業時間</h3>
                                    <input style="font-size: 1.5em; font-weight: bold;" type="text" class="form-control" name="businessHours" value="<?= $row['businessHours'] ?>" placeholder="輸入營業時間">
                                </div>

                                
                                <div class="form-element button_container">
                                    <input style=" font-weight: bold; font-size: 1.5em;" type="submit" class="btn btn-primary" name="updateMerchant" id="saveButton" value="儲存" disabled>
                                </div>

                            </form>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    
        

        



        <!-- Back to Top -->
        <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   

        
    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    <script>
        window.onload = function() {
            // 取得原始的資料
            var originalName = "<?php echo $row['mName']; ?>";
            var originalAddress = "<?php echo $row['mAddress']; ?>";
            var originalBusinessHours = "<?php echo $row['businessHours']; ?>";
            var originalPicture = "<?php echo $row['mPicture']; ?>";  // 原始圖片路徑

            // 取得元素
            var saveButton = document.getElementById("saveButton");
            var mNameInput = document.querySelector("[name='mName']");
            var mAddressInput = document.querySelector("[name='mAddress']");
            var businessHoursInput = document.querySelector("[name='businessHours']");
            var imageInput = document.querySelector("[name='ImageUpload']"); // 圖片欄位

            // 比對輸入框是否有更動
            function checkIfChanged() {
                var nameChanged = mNameInput.value !== originalName;
                var addressChanged = mAddressInput.value !== originalAddress;
                var businessHoursChanged = businessHoursInput.value !== originalBusinessHours;
                var imageChanged = imageInput.files.length > 0; // 如果有選擇新圖片，視為圖片有改變

                // 如果有任何欄位更動，啟用 "Save" 按鈕；否則禁用
                if (nameChanged || addressChanged || businessHoursChanged || imageChanged) {
                    saveButton.disabled = false;
                } else {
                    saveButton.disabled = true;
                }
            }

            // 監聽輸入框的變動
            mNameInput.addEventListener("input", checkIfChanged);
            mAddressInput.addEventListener("input", checkIfChanged);
            businessHoursInput.addEventListener("input", checkIfChanged);
            imageInput.addEventListener("change", checkIfChanged);  // 監聽圖片選擇的變動
        }
    </script>


    </body>

</html>