<?php
session_start();
include '../dbh.php';

$mid = $_SESSION['mid'] ?? 0;

if (!$mid) {
    echo "<p>請先登入為商家。</p>";
    exit;
}

$sql = "
    SELECT rt.*, c.cName, c.phone, c.email 
    FROM ReserveTrans rt
    JOIN customer c ON rt.Reservationcid = c.cid
    WHERE rt.mid = ?
    ORDER BY rt.reservationDate DESC, rt.reservationTime DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $mid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>商家訂位一覽</title>
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
    <script src="https://kit.fontawesome.com/ea478a1bc4.js" crossorigin="anonymous"></script>

    <!-- Libraries Stylesheet -->
    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">
</head>
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
                        <a href="merchant_shop.php?mid=<?php echo $mid; ?>" class="nav-item nav-link" >店面資訊</a>
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
                        <a href="../walletAndrecord/m_wallet.php?id=<?php echo $mid; ?>&role=m" class="nav-item nav-link active">接單紀錄</a>
                        <a href="merchant_reservations.php?mid=<?php echo $mid; ?>" class="nav-item nav-link" style="color:#ffb524">訂位管理</a>
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

<body class="p-4">
    <h2 class="mb-4" style="margin-top:150px">所有訂位紀錄</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>顧客姓名</th>
                    <th>電話</th>
                    <th>Email</th>
                    <th>日期</th>
                    <th>時間</th>
                    <th>大人</th>
                    <th>小孩</th>
                    <th>訂金</th>
                    <th>建立時間</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['cName']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['reservationDate']) ?></td>
                    <td><?= htmlspecialchars($row['reservationTime']) ?></td>
                    <td><?= intval($row['adult']) ?></td>
                    <td><?= intval($row['children']) ?></td>
                    <td>NT$<?= number_format($row['deposite'] * ($row['adult'] + $row['children']), 0) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>目前沒有任何訂位。</p>
    <?php endif; ?>

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
</body>
</html>
