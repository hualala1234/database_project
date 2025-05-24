<?php
include ('../dbh.php');
session_start(); // 必須是第一行，前面不能有空白或 HTML！
$mid = isset($_SESSION["mid"]) ? $_SESSION["mid"] : '';
if ($mid !== '') {
    $sql = "SELECT * FROM Merchant WHERE mid = $mid";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
}

// 商家名稱
$merchant_name = $row['mName'] ?? '';

// 將 JSON 格式的營業時間轉為陣列格式
$store_hours = [];

if (!empty($row['businessHours'])) {
    $hoursArray = json_decode($row['businessHours'], true);

    foreach ($hoursArray as $day => $value) {
        if ($value === '休息') {
            $store_hours[$day] = '休息';
        } else {
            $store_hours[$day] = $value; // e.g. "08:00 - 17:00"
        }
    }
} else {
    // 沒有設定時的預設值
    $store_hours = [
        '星期一' => '休息',
        '星期二' => '休息',
        '星期三' => '休息',
        '星期四' => '休息',
        '星期五' => '休息',
        '星期六' => '休息',
        '星期日' => '休息'
    ];
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
            $sqlNewOrderCount = "SELECT COUNT(DISTINCT t.tranId) AS newOrderCount
                    FROM `Transaction` t 
                    WHERE t.orderStatus = 'new' AND t.mid = $mid" ;
            $resultNewOrderCount = mysqli_query($conn, $sqlNewOrderCount);
            $newOrderCount = 0;
            if ($row = mysqli_fetch_assoc($resultNewOrderCount)) {
            $newOrderCount = $row['newOrderCount'];
            }
        ?>
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
                            <a href="merchant_shop.php?mid=<?php echo $mid; ?>" class="nav-item nav-link" style="color:#ffb524">店面資訊</a>
                            <a href="menu.php?mid=<?php echo $mid; ?>" class="nav-item nav-link">菜單管理</a>
                            <a href="order.php?mid=<?= $mid; ?>" class="nav-item nav-link position-relative">
                                訂單
                                <?php if ($newOrderCount > 0): ?>
                                    <span class="position-absolute bg-warning rounded-circle d-flex align-items-center justify-content-center text-dark fw-bold"
                                        style="top: 7px; right: -4px; height: 20px; min-width: 20px; font-size: 0.75rem;">
                                        <?= $newOrderCount ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <a href="../walletAndrecord/m_wallet.php?id=<?php echo $mid; ?>&role=m" class="nav-item nav-link">接單紀錄</a>

                            <!-- <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                                <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                    <a href="cart.html" class="dropdown-item">Cart</a>
                                    <a href="chackout.html" class="dropdown-item">Chackout</a>
                                    <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                                    <a href="404.html" class="dropdown-item">404 Page</a>
                                </div>
                            </div> -->
                            <a href="merchant_reservations.php?mid=<?php echo $mid; ?>" class="nav-item nav-link" >訂位管理</a>
                            <!-- <a href="contact.html" class="nav-item nav-link">聯繫平台</a> -->
                        </div>
                        
                        <div class="d-flex m-3 me-0"> 
                            <?php if (isset($_SESSION['login_success'])): ?>
                                <!-- ✅ 已登入的顯示 -->
                                <div class="dropdown-custom" style="position: relative; display: inline-block;">
                                    <a href="javascript:void(0);" class="my-auto d-inline-block" onclick="toggleDropdown()" style="cursor: pointer;">
                                        <img src="../login/success.png" alt="Success" style="width: 40px; height: 40px; filter: brightness(0) saturate(100%) invert(42%) sepia(91%) saturate(356%) hue-rotate(71deg) brightness(94%) contrast(92%);">
                                    </a>

                                    <div id="myDropdown" class="d-none" style="
                                        position: absolute;
                                        top: 100%;
                                        right: 0;
                                        background-color: white;
                                        min-width: 120px;
                                        box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
                                        z-index: 1000;
                                        border-radius: 8px;
                                        padding: 8px 0;
                                    ">
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
        <?php
        $sql = "SELECT * FROM Merchant WHERE mid = $mid";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        ?>
        
       
         
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

                                <?php
                                // 抓目前這間餐廳的類別ID（可能多筆）
                                $currentCategories = [];
                                $sqlCurrent = "SELECT categoryId FROM RestaurantCategories WHERE mid = $mid";
                                $resultCurrent = mysqli_query($conn, $sqlCurrent);
                                while ($rowCurrent = mysqli_fetch_assoc($resultCurrent)) {
                                    $currentCategories[] = $rowCurrent['categoryId'];
                                }

                                // 顯示所有選項
                                $sqlCategories = "SELECT categoryId, categoryName FROM RestaurantCategoryList"; // 這裡改成從 RestaurantCategoryList 顯示
                                $resultCategories = mysqli_query($conn, $sqlCategories);
                                ?>

                                

<div id="block" style="display: flex;justify-content: space-evenly;margin-bottom: 60px;">
    <div id="block1" style="width: 600px;padding: 0 60px;">
                                <div class="py-3">
                                    <div style="display:flex; justify-content: center;">
                                        <h3>商店名稱</h3>
                                        <h3 style="color:red; margin:0;">*</h3>
                                    </div>
                                    
                                    <input required style="font-size: 1.5rem; font-weight: bold;" type="text" class="form-control" name="mName" value="<?= $row['mName'] ?>" placeholder="輸入店面名稱">
                                </div>

                                <div class="py-3">
                                    <div style="display:flex; justify-content: center;">
                                        <h3>住址</h3>
                                        <h3 style="color:red; margin:0;">*</h3>
                                    </div>
                                    
                                    <input required style="font-size: 1.5em; font-weight: bold;" type="text" class="form-control" name="mAddress" value="<?= $row['mAddress'] ?>" placeholder="輸入店面地址">
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
              
</div><div id="block2" style="width: 650px;">
                                <div class="form-group py-3">
                                    <div class="text-center mb-2">
                                        <label class="form-label text-dark" style="font-size: 1.75rem; font-weight:bold;">選擇類別</label>
                                        <span style="color: red;">*</span>
                                    </div>

                                    <div style="display:flex; flex-wrap: wrap;font-size: 20px;">
                                    <?php
                                        // 抓所有可用的類別
                                        while ($category = mysqli_fetch_assoc($resultCategories)):
                                            $categoryId = $category['categoryId'];
                                            $categoryName = $category['categoryName'];
                                            $checked = in_array($categoryId, $currentCategories) ? "checked" : "";
                                    ?>
                                        <div class="col-md-2 form-check" style=" display:flex; flex-wrap: wrap; gap:0.3rem; justify-content: center;">
                                            <input class="form-check-input"  type="checkbox" name="restaurantCategories[]" value="<?= $categoryId ?>" id="cat_<?= $categoryId ?>" <?= $checked ?>>
                                            <label class="form-check-label" for="cat_<?= $categoryId ?>"><?= $categoryName ?></label>
                                        </div>
                                    <?php endwhile; ?>
                                    </div>
                                </div>

                               
                                <div class="container mt-5">
                                    <h3>目前營業時間：</h3>
                                    <ul class="list-group mb-3">
                                        <?php foreach ($store_hours as $day => $hours): ?>
                                            <li class="list-group-item d-flex justify-content-between custom-list-group-item text-dark">
                                            <strong><?php echo $day; ?></strong>
                                                <span><?php echo $hours; ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                    <!-- 關鍵：按鈕 type 設為 button，避免觸發 form 提交 -->
                                    <button type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editHoursModal">
                                        編輯營業時間
                                    </button>
                                </div>
                                <!-- Modal 彈出視窗：注意這是在 <form> 外部！ -->
                                <div class="modal fade" id="editHoursModal" tabindex="-1" aria-labelledby="editHoursModalLabel" aria-hidden="true" >
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <!-- Modal 的內部也可以包一個小表單，但建議直接用原本大表單來處理，所以不需要 <form> -->
                                            <div class="modal-body">
                                                <?php foreach ($store_hours as $day => $hours): 
                                                    if ($hours === '休息') {
                                                        $open = '';
                                                        $close = '';
                                                        $closed = true;
                                                    } else {
                                                        list($open, $close) = array_map('trim', explode('-', $hours));
                                                        $closed = false;
                                                    }
                                                ?>
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $day; ?></label>
                                                    <div class="d-flex gap-2">
                                                        <input type="time" class="form-control" name="hours[<?php echo $day; ?>][open]" value="<?php echo $open; ?>" <?php echo $closed ? 'disabled' : ''; ?>>
                                                        <span class="align-self-center">至</span>
                                                        <input type="time" class="form-control" name="hours[<?php echo $day; ?>][close]" value="<?php echo $close; ?>" <?php echo $closed ? 'disabled' : ''; ?>>
                                                        <div class="form-check ms-2">
                                                            <input class="form-check-input" type="checkbox" id="closed_<?php echo $day; ?>" name="hours[<?php echo $day; ?>][closed]" value="1" <?php echo $closed ? 'checked' : ''; ?> onchange="toggleDay('<?php echo $day; ?>')">
                                                            <label class="form-check-label" for="closed_<?php echo $day; ?>">休息</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
</div></div>
                                            <div class="modal-footer">
                                                <!-- 關鍵：這裡不要用 submit，Modal 關閉即可 -->
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                                <button type="button" class="btn btn-primary text-white" onclick="saveBusinessHours()" data-bs-dismiss="modal">儲存</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="hoursJson" id="hoursJson">
                                <div class="form-element button_container">
                                    <input style=" font-weight: bold; font-size: 1.5em;" type="submit" class="btn btn-primary text-white" name="updateMerchant" id="saveButton" value="儲存" disabled>
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
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    <script>
    window.onload = function() {
        var originalName = "<?php echo $row['mName']; ?>";
        var originalAddress = "<?php echo $row['mAddress']; ?>";
        var originalBusinessHours = "<?php echo $row['businessHours']; ?>";
        var originalPicture = "<?php echo $row['mPicture']; ?>";
        var originalCategories = <?php echo json_encode($currentCategories); ?>;

        var saveButton = document.getElementById("saveButton");
        var mNameInput = document.querySelector("[name='mName']");
        var mAddressInput = document.querySelector("[name='mAddress']");
        var businessHoursInput = document.querySelector("[name='businessHours']");
        var imageInput = document.querySelector("[name='ImageUpload']");
        var categoryCheckboxes = document.querySelectorAll("input[name='restaurantCategories[]']");
        var businessHourInputs = document.querySelectorAll("input[name^='hours']");

        function checkIfChanged() {
            var nameChanged = mNameInput.value !== originalName;
            var addressChanged = mAddressInput.value !== originalAddress;
            var imageChanged = imageInput.files.length > 0;

            // 類別變更偵測
            var currentSelected = [];
            categoryCheckboxes.forEach(function(checkbox) {
                if (checkbox.checked) currentSelected.push(checkbox.value);
            });

            var categoriesChanged = originalCategories.length !== currentSelected.length ||
                originalCategories.some(cat => !currentSelected.includes(cat));

            // 檢查營業時間是否變更（比對目前值是否與原值不同）
            let hoursChanged = false;
            businessHourInputs.forEach(function(input) {
                if (input.type === "checkbox") {
                    if (input.checked !== input.defaultChecked) {
                        hoursChanged = true;
                    }
                } else {
                    if (input.value !== input.defaultValue) {
                        hoursChanged = true;
                    }
                }
            });

            if (nameChanged || addressChanged || imageChanged || categoriesChanged || hoursChanged) {
                saveButton.disabled = false;
            } else {
                saveButton.disabled = true;
            }
        }

        // 原欄位監聽
        mNameInput.addEventListener("input", checkIfChanged);
        mAddressInput.addEventListener("input", checkIfChanged);
        imageInput.addEventListener("change", checkIfChanged);
        categoryCheckboxes.forEach(cb => cb.addEventListener("change", checkIfChanged));

        // 新增：監聽營業時間欄位變更
        businessHourInputs.forEach(input => {
            input.addEventListener("input", checkIfChanged);
            input.addEventListener("change", checkIfChanged);
        });
    };
    </script>

    

    <script>
        const checkboxes = document.querySelectorAll('input[name="restaurantCategories[]"]');
        const saveButton = document.getElementById("saveButton");

        function validateForm() {
            let isAnyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
            let requiredInputsFilled = Array.from(document.querySelectorAll("input[required]")).every(input => input.value.trim() !== "");

            if (saveButton) {
                saveButton.disabled = !(isAnyChecked && requiredInputsFilled);
            }
        }

        if (checkboxes.length > 0) {
            checkboxes.forEach(checkbox => checkbox.addEventListener("change", validateForm));
        }

        document.querySelectorAll("input[required]").forEach(input => {
            input.addEventListener("input", validateForm);
        });

        // 初始檢查
        validateForm();
    </script>



    <script>
        function toggleDropdown() {

            const dropdown = document.getElementById("myDropdown");
            dropdown.classList.toggle("d-none");
        }

        window.onclick = function(event) {
            const dropdown = document.getElementById("myDropdown");
            if (!event.target.closest('.dropdown-custom') && dropdown && !dropdown.classList.contains("d-none")) {
                dropdown.classList.add("d-none");

            }
        }
    </script>


    <script>
        function toggleDay(day) {
            const isClosed = document.getElementById('closed_' + day).checked;
            const openInput = document.querySelector(`input[name="hours[${day}][open]"]`);
            const closeInput = document.querySelector(`input[name="hours[${day}][close]"]`);
            openInput.disabled = isClosed;
            closeInput.disabled = isClosed;
        }

        // 按下 Modal 儲存時，自動啟用表單送出按鈕
        document.querySelector('#editHoursModal .btn-primary').addEventListener('click', function () {
            document.getElementById('saveButton').disabled = false;
        });
    </script>

    <script>
    function saveBusinessHours() {
        const days = ["星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"];
        const hoursData = {};

        days.forEach(day => {
            const isClosed = document.getElementById(`closed_${day}`).checked;
            if (isClosed) {
                hoursData[day] = "休息";
            } else {
                const open = document.querySelector(`input[name="hours[${day}][open]"]`).value;
                const close = document.querySelector(`input[name="hours[${day}][close]"]`).value;
                hoursData[day] = `${open} - ${close}`;
            }
        });

        // 更新顯示表格
        const listGroup = document.querySelector(".list-group");
        listGroup.innerHTML = ""; // 清空舊內容
        for (const [day, hours] of Object.entries(hoursData)) {
            listGroup.innerHTML += `
                <li class="list-group-item d-flex justify-content-between">
                    <strong>${day}</strong>
                    <span>${hours}</span>
                </li>
            `;
        }

        // 更新隱藏欄位（表單會帶過去）
        document.getElementById("hoursJson").value = JSON.stringify(hoursData);

        // 啟用儲存按鈕
        document.getElementById("saveButton").disabled = false;
    }
    </script>


    </body>

</html>