<?php
// calendar.php
// 顯示指定月份的月曆，並可根據側邊功能選單切換顯示內容

// 載入 DB 連線與啟動 Session
include '../dbh.php';
session_start();

// 取得商家 ID 並撈取商家資訊
$mid = isset($_SESSION['mid']) ? intval($_SESSION['mid']) : 0;
$restaurantName     = '示例飯店';
$address            = '臺北市中正區示例路123號';
$phone              = '(02)1234-5678';
$deposit            = 500;
$businessHours      = '無資料';
if ($mid) {
    // 從 merchant 資料表撈商家名稱、地址、Email、營業時間、訂金
    $stmt = $conn->prepare('SELECT mName, mAddress, mEmail, businessHours FROM merchant WHERE mid = ?');
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('i', $mid);
    $stmt->execute();
    $stmt->bind_result($mName, $mAddress, $mEmail, $mHours);    if ($stmt->fetch()) {
        $restaurantName = $mName;
        $address        = $mAddress;
        $phone          = $mEmail;
        $businessHours  = $mHours;
    }
    $stmt->close();
}

// 取得顧客ID並撈取顧客資訊
$cid = isset($_SESSION['cid']) ? intval($_SESSION['cid']) : 0;
$custName  = '';
$custPhone = '';
$custEmail = '';
if ($cid) {
    // 注意：表名改為小寫 customer
    $stmt2 = $conn->prepare('SELECT cName, phone, email FROM customer WHERE cid = ?');
    if (!$stmt2) {
        die('Prepare failed (customer): ' . $conn->error);
    }
    $stmt2->bind_param('i', $cid);
    $stmt2->execute();
    $stmt2->bind_result($cName, $cPhone, $cEmail);
    if ($stmt2->fetch()) {
        $custName  = $cName;
        $custPhone = $cPhone;
        $custEmail = $cEmail;
    }
    $stmt2->close();
}

// 時區與面板、年月設定
date_default_timezone_set('Asia/Taipei');
$panel = isset($_GET['panel']) ? $_GET['panel'] : 'restaurant';
$year  = isset($_GET['year'])  ? intval($_GET['year'])  : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');

// 月曆計算
$firstDayOfMonth = mktime(0,0,0,$month,1,$year);
$totalDays = date('t',$firstDayOfMonth);
$startWeekday = date('N',$firstDayOfMonth) - 1; // ISO 月曜為0
$weekDays = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
$months   = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title><?php echo "{$year} 年 {$month} 月 月曆"; ?></title>    
    <link rel="stylesheet" href="reservation.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <nav class="navbar navbar-light bg-white navbar-expand-xl">
                <a href="index.php?mid=<?php echo $mid; ?>" class="navbar-brand"><h1 class="text-primary display-6">Junglebite 餐廳訂位</h1></a>
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
                            <a href="merchant.php?mid=<?php echo $mid; ?>">
                                <img src="shop.png" width="40" height="40" alt="shop">
                            </a>
                            <a href="javascript:void(0);" class="my-auto" onclick="toggleDropdown()">
                                <img src="  ../login/success.png" alt="Success" style="width: 40px; height: 40px; filter: brightness(0) saturate(100%) invert(42%) sepia(91%) saturate(356%) hue-rotate(71deg) brightness(94%) contrast(92%);">
                            </a>

                            <div id="myDropdown" class="dropdown-content" style="display: none; position: absolute; background-color: white; min-width: 120px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; right: 0; border-radius: 8px;">

                                <?php if ($_SESSION['role'] === 'merchant'): ?>
                                    <a href="/database/merchant/setting.php" class="dropdown-item">商家設定</a>
                                <?php elseif ($_SESSION['role'] === 'customer'): ?>
                                    <a href="../login/login_customer/setting.php" class="dropdown-item">個人設定</a>
                                    <a href="/database_project/allergy/allergy.php" class="dropdown-item">過敏設定</a>
                                    <a href="../claw_machine/claw.php" class="dropdown-item">優惠券活動</a>
                                    <!-- <a href="friends.php" class="dropdown-item">我的好友</a> -->
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
    <div class="calendar-wrapper">
        <div class="sidebar">
            <div class="info">
            <p data-panel="restaurant" class="<?php echo $panel==='restaurant'?'active':''; ?>">餐廳名稱</p>
            <p data-panel="datetime"   class="<?php echo $panel==='datetime'  ?'active':''; ?>">預約日期與時間</p>
            <p data-panel="people"     class="<?php echo $panel==='people'    ?'active':''; ?>">人數</p>
            <p data-panel="confirm"    class="<?php echo $panel==='confirm'   ?'active':''; ?>">確認訂位資訊</p>
            </div>
        </div>
        <div class="calendar-container">
            
            <!-- 餐廳名稱 面板 -->
            <div id="restaurant" class="content-panel <?php echo $panel==='restaurant'?'active':''; ?>">
                <h2>餐廳名稱：<?php echo htmlspecialchars($restaurantName); ?></h2>
                <p>地址：<?php echo htmlspecialchars($address); ?></p>
                <p>email：<?php echo htmlspecialchars($phone); ?></p>
                <p>訂金：NT$<?php echo htmlspecialchars($deposit); ?> / 位</p>
                <div class="business-hours">
                <?php 
                    $hours = json_decode($businessHours, true);
                    if (is_array($hours)):
                    foreach ($hours as $day => $interval): ?>
                        <div class="day">
                        <span><?php echo htmlspecialchars($day); ?></span>
                        <span><?php echo htmlspecialchars($interval); ?></span>
                        </div>
                <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- 預約日期與時間 面板 -->
        
            <div id="datetime" class="content-panel <?php echo $panel==='datetime'?'active':''; ?>">
                <h2 style='text-align:center'>預約日期與時間</h2>
                <p style='text-align:center'>請選擇日期與時間</p>
            <div class="month-selector">
                <?php foreach($months as $i=>$abbr): $m=$i+1; ?>
                <a href="?year=<?php echo $year;?>&month=<?php echo $m;?>&panel=datetime" class="<?php echo $m===$month?'active':'';?>"><?php echo $abbr; ?></a>
                <?php endforeach; ?>
            </div>
            <table>
                <tr><?php foreach($weekDays as $d): ?><th><?php echo $d;?></th><?php endforeach;?></tr>
                <tr>
                <?php for($i=0; $i<$startWeekday; $i++) echo '<td></td>'; ?>
                <?php for($d=1; $d<=$totalDays; $d++):
                $wd = ($startWeekday+$d-1)%7;
                $isToday = ($year==date('Y') && $month==date('n') && $d==date('j'));
                ?>
                <td data-day="<?php echo $d;?>" class="<?php echo $isToday?'today':''; ?>"><?php echo $d; ?></td>
                <?php if($wd==6 && $d!=$totalDays) echo '</tr><tr>'; ?>
                <?php endfor; ?>
                <?php for($i=0; $i<(7-(($startWeekday+$totalDays)%7))%7; $i++) echo '<td></td>'; ?>
                </tr>
            </table>
            <div class="time-select">
                <label>時間：<input type="time" id="timeInput"/></label>
            </div>
            <button id="saveDateTime">儲存</button>
            </div>

            <!-- 人數 面板 -->
            <div id="people" class="content-panel <?php echo $panel==='people'?'active':''; ?>">
            <label>大人：<input type="number" min="1" id="adultCount" value="1"/> 位</label><br><br>
            <label>小孩：<input type="number" min="0" id="childCount" value="0"/> 位</label><br><br>
            <button id="savePeople">儲存</button>
            </div>


            <!-- reservation.php 中的 confirm 區段 -->
            <div id="confirm" class="content-panel <?php echo $panel==='confirm'?'active':''; ?>">
            <h2>訂位確認資訊</h2>

            <!-- 1. 包一個 form，指向 addreservation.php -->
            <form id="reservationForm" method="POST" action="addreservation.php">
                <!-- 2. 這些 p 只是顯示用 -->
                <p id="confirmRestaurant"></p>
                <p id="confirmAddress"></p>
                <p id="confirmCustomerName"></p>
                <p id="confirmCustomerPhone"></p>
                <p id="confirmCustomerEmail"></p>
                <p id="confirmDateTime"></p>
                <p id="confirmPeople"></p>
                <p id="confirmCancel"></p>
                <p id="confirmDeposit"></p>
                <p id="confirmPhone"></p>

                <!-- 3. 隱藏欄位：跟 addreservation.php 接受的欄位名稱要對應 -->
                <input type="hidden" name="reservationDateTime" id="reservationDateTimeInput">
                <input type="hidden" name="adult"                     id="adultInput">
                <input type="hidden" name="children"                  id="childrenInput">
                <input type="hidden" name="acceptableCancellationTime" id="cancelInput">
                <input type="hidden" name="acceptableLateTime"      id="lateInput">
                <input type="hidden" name="deposite"                 id="depositInput">

                <!-- 4. 按鈕改成 submit -->
                <button type="submit" id="finalConfirm" class="confirm-btn">確認訂位</button>
            </form>
            </div>
    <script>
    // 頁面載入後綁定事件
    document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.sidebar .info p');
    const panels = document.querySelectorAll('.content-panel');
    tabs.forEach(tab => tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        panels.forEach(p => p.classList.remove('active'));
        document.getElementById(tab.dataset.panel).classList.add('active');
        if (tab.dataset.panel === 'confirm') showConfirmPanel();
    }));
    document.addEventListener('DOMContentLoaded', ()=>{
        const form = document.getElementById('reservationForm');
        form.addEventListener('submit', ()=>{
            showConfirmPanel();  // 把 sessionStorage 裡的時間、人數、取消、遲到、訂金……都寫到隱藏 input
            // 注意：這裡不呼叫 e.preventDefault()，直接往下送出
        });
        });

    // highlight today
    const today = new Date();
    if (today.getFullYear()===<?php echo $year;?> && today.getMonth()+1===<?php echo $month;?>) {
        const cell = document.querySelector(`#datetime td[data-day='${today.getDate()}']`);
        if (cell) cell.classList.add('today');
    }

    // date click
    document.querySelectorAll('#datetime td[data-day]').forEach(cell=>{
        cell.addEventListener('click', ()=>{
        document.querySelectorAll('#datetime td.selected').forEach(s=>s.classList.remove('selected'));
        cell.classList.add('selected');
        sessionStorage.setItem('selectedDate', cell.dataset.day);
        });
    });

    // save date/time
    document.getElementById('saveDateTime').addEventListener('click', ()=>{
        const d = sessionStorage.getItem('selectedDate')||'';
        const t = document.getElementById('timeInput').value;
        const full=`<?php echo $year;?>-${String(<?php echo $month;?>).padStart(2,'0')}-${String(d).padStart(2,'0')} ${t}`;
        sessionStorage.setItem('reservationDateTime',full);
        alert('已儲存：'+full);
    });

    // save people
    document.getElementById('savePeople').addEventListener('click', ()=>{
        const a=document.getElementById('adultCount').value;
        const c=document.getElementById('childCount').value;
        sessionStorage.setItem('reservationPeople',JSON.stringify({adults:a,children:c}));
        alert(`已儲存：大人 ${a} 位，小孩 ${c} 位`);
    });

    // confirm button
    document.getElementById('finalConfirm').addEventListener('click',showConfirmPanel);
    });

    // show confirm
    function showConfirmPanel(){
        // 顯示文字
        document.getElementById('confirmRestaurant').textContent = '餐廳名稱：'+<?php echo json_encode($restaurantName);?>;
        document.getElementById('confirmAddress').textContent    = '地址：'+<?php echo json_encode($address);?>;
        document.getElementById('confirmCustomerName').textContent  = '顧客姓名：'+<?php echo json_encode($custName);?>;
        document.getElementById('confirmCustomerPhone').textContent = '顧客電話：'+<?php echo json_encode($custPhone);?>;
        document.getElementById('confirmCustomerEmail').textContent = '顧客信箱：'+<?php echo json_encode($custEmail);?>;

        // 取出時間、人數
        const dt = sessionStorage.getItem('reservationDateTime') || '';
        document.getElementById('confirmDateTime').textContent = '預約時間：'+ dt;
        document.getElementById('reservationDateTimeInput').value = dt;

        const ppl = JSON.parse(sessionStorage.getItem('reservationPeople') || '{}');
        const adults   = ppl.adults   || 0;
        const children = ppl.children || 0;
        document.getElementById('confirmPeople').textContent = `人數：大人 ${adults} 位，小孩 ${children} 位`;
        document.getElementById('adultInput').value    = adults;
        document.getElementById('childrenInput').value = children;

        // 取消跟遲到時間（依你生意需求改格式）
        const cancelTime = '03:00:00';   // 範例：預約前 3 小時可取消
        const lateTime   = '00:15:00';   // 範例：最晚可遲到 15 分
        document.getElementById('cancelInput').value = '03:00:00';
        document.getElementById('lateInput') .value = '00:15:00';
        document.getElementById('lateInput').value   = lateTime;

        // 訂金（PHP 端從 DB 撈到的變數）
        const deposit = <?php echo json_encode($deposit);?>;
        document.getElementById('confirmDeposit').textContent = '訂金：NT$'+deposit+' / 位';
        document.getElementById('depositInput').value = deposit;

        // 餐廳電話
        document.getElementById('confirmPhone').textContent = '電話：'+<?php echo json_encode($phone);?>;
        }
    </script>
</body>
</html>