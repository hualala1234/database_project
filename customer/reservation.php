<?php
// calendar.php
// 顯示指定月份的月曆，並可根據側邊功能選單切換顯示內容

// 載入 DB 連線與啟動 Session
include '../dbh.php';
session_start();

// 取得商家 ID 並撈取商家資訊
$mid = isset($_SESSION['mid']) ? intval($_SESSION['mid']) : 0;
$restaurantName     = '您尚未選擇店家';
$address            = '未知店家地址';
$deposit            = 500;
$businessHours      = '無資料';
if ($mid) {
    // 從 merchant 資料表撈商家名稱、地址、Email、營業時間、訂金
    $stmt = $conn->prepare('SELECT mName, mAddress, businessHours FROM merchant WHERE mid = ?');
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('i', $mid);
    $stmt->execute();
    $stmt->bind_result($mName, $mAddress, $mHours);    if ($stmt->fetch()) {
        $restaurantName = $mName;
        $address        = $mAddress;
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
                            <a href="<?php echo $mid === 0 ? 'index.php' : 'merchant.php?mid=' . $mid; ?>">
                                <img src="shop.png" width="40" height="40" alt="shop" style="margin-right:10px">
                            </a>
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
                                    <a href="../customer/myfavorite.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item text-decoration-none">我的愛店</a>
                                    <!-- <a href="/database_project/customer/reservation.php" class="dropdown-item">我要訂位</a> -->
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
    <div class="calendar-wrapper">
        <div class="sidebar">
            <div class="info">
            <p data-panel="restaurant" class="<?php echo $panel==='restaurant'?'active':''; ?>">餐廳名稱</p>
            <p data-panel="datetime"   class="<?php echo $panel==='datetime'  ?'active':''; ?>">預約日期與時間</p>
            <p data-panel="people"     class="<?php echo $panel==='people'    ?'active':''; ?>">人數</p>
            <p data-panel="confirm"    class="<?php echo $panel==='confirm'   ?'active':''; ?>">確認訂位資訊</p>
            <p data-panel="history" class="<?php echo $panel==='history'?'active':''; ?>">成功訂位記錄</p>
            </div>
        </div>
        <div class="calendar-container">
            
            <!-- 餐廳名稱 面板 -->
            <div id="restaurant" class="content-panel <?php echo $panel==='restaurant'?'active':''; ?>">
                <h2>餐廳名稱：<?php echo htmlspecialchars($restaurantName); ?></h2>
                <p>地址：<?php echo htmlspecialchars($address); ?></p>
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
                    <?php
                    $currentMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n'); // ✅ 有傳參數用它，否則預設本月
                    foreach ($months as $i => $abbr):
                        $m = $i + 1;
                        $cls = ($m === $currentMonth) ? 'active' : '';
                    ?>
                        <a href="?year=<?= $year ?>&month=<?= $m ?>&panel=datetime" class="<?= $cls ?>"><?= $abbr ?></a>
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
                    <label for="timeInput">時間：</label>
                    <input type="time" id="timeInput" />
                    <button id="saveDateTime">儲存</button>
                </div>
            </div>

            <!-- 人數 面板 -->
            <div id="people" class="content-panel <?php echo $panel==='people'?'active':''; ?>">
                <h2 style='text-align:center'>預約人數</h2>
                <p style='text-align:center'>請選擇預約人數</p>
                <div class="people-form">
                    <label>
                        大人：
                        <input type="number" min="1" id="adultCount" value="1"/> 位
                    </label>
                    <label>
                        小孩：
                        <input type="number" min="0" id="childCount" value="0"/> 位
                    </label>
                    <button id="savePeople">儲存</button>
                </div>
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

        <div id="history" class="content-panel <?php echo $panel==='history'?'active':''; ?>">
            <h2>我的訂位紀錄</h2>
            <div class="reservation-history">
                <?php
                if (isset($_SESSION['cid'])) {
                    $cid = $_SESSION['cid'];
                    $sql = "
                        SELECT rt.*, m.mName 
                        FROM ReserveTrans rt
                        JOIN Merchant m ON rt.mid = m.mid
                        WHERE rt.Reservationcid = ?
                        ORDER BY rt.reservationDate DESC, rt.reservationTime DESC
                    ";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $cid);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='history-entry' style='border:1px solid #ccc; border-radius:8px; padding:10px; margin-bottom:10px'>";
                            echo "<p><strong>餐廳：</strong>" . htmlspecialchars($row['mName']) . "</p>";
                            echo "<p><strong>訂位日期：</strong>" . htmlspecialchars($row['reservationDate']) . "</p>";
                            echo "<p><strong>訂位時間：</strong>" . htmlspecialchars($row['reservationTime']) . "</p>";
                            echo "<p><strong>大人：</strong>" . intval($row['adult']) . " 位</p>";
                            echo "<p><strong>小孩：</strong>" . intval($row['children']) . " 位</p>";
                            $total = $row['deposite'] * ($row['adult'] + $row['children']);
                            echo "<p><strong>訂金：</strong>共 NT$" . number_format($total, 0) . " 元（" . number_format($row['deposite'], 0) . " 元/位）</p>";
                            echo "<p><strong>建立時間：</strong>" . htmlspecialchars($row['created_at']) . "</p>";
                            // ✅ 取消表單放這裡
                            echo "<button class='btn btn-danger btn-sm mt-2 cancel-btn' 
                                    data-id='" . $row['ReserveTransid'] . "' 
                                    data-adult='" . $row['adult'] . "' 
                                    data-children='" . $row['children'] . "' 
                                    data-deposite='" . $row['deposite'] . "'>
                                    取消預約
                                </button>";


                            echo "</div>";

                        }
                    } else {
                        echo "<p>尚無任何訂位紀錄。</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p>請先登入以查看預約紀錄。</p>";
                }
                ?>
            </div>
        </div>

    <script>
    // 頁面載入後綁定事件
    document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.sidebar .info p');
    const panels = document.querySelectorAll('.content-panel');
    tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      panels.forEach(p => p.classList.remove('active'));
      document.getElementById(tab.dataset.panel).classList.add('active');
      if (tab.dataset.panel === 'confirm') showConfirmPanel();
    });
  });

    // ✅ 月份按鈕高亮：根據網址上的 month 參數
    const urlParams = new URLSearchParams(window.location.search);
    const selectedMonth = parseInt(urlParams.get('month'), 10);
    document.querySelectorAll('.month-selector a').forEach(a => {
        const linkParams = new URLSearchParams(a.getAttribute('href'));
        const m = parseInt(linkParams.get('month'), 10);
        if (m === selectedMonth) {
        a.classList.add('active');
        } else {
        a.classList.remove('active');
        }
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

    const businessHours = <?php echo json_encode(json_decode($businessHours, true)); ?>;

    document.getElementById('saveDateTime').addEventListener('click', () => {
        const d = sessionStorage.getItem('selectedDate') || '';
        const t = document.getElementById('timeInput').value;
        const date = new Date(`<?php echo "$year-$month"; ?>-${d.padStart(2, '0')}`);
        const weekday = ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'][date.getDay()];

        const time = t;
        const timeHour = parseInt(time.split(':')[0]);
        const timeMin  = parseInt(time.split(':')[1]);

        const slots = (businessHours[weekday] || "").split(',');
        let valid = false;

        for (const slot of slots) {
            const [start, end] = slot.split('-');
            if (!start || !end) continue;
            const [sh, sm] = start.split(':').map(Number);
            const [eh, em] = end.split(':').map(Number);

            const nowMin = timeHour * 60 + timeMin;
            const sMin = sh * 60 + sm;
            const eMin = eh * 60 + em;

            if (nowMin >= sMin && nowMin <= eMin) {
                valid = true;
                break;
            }
        }

        if (!valid) {
            alert(`您選擇的 ${weekday} ${time} 不在營業時間內，請重新選擇`);
            return;
        }

        const full = `<?php echo $year;?>-${String(<?php echo $month;?>).padStart(2,'0')}-${String(d).padStart(2,'0')} ${t}`;
        sessionStorage.setItem('reservationDateTime', full);
        alert('已儲存：' + full);
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

       
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.cancel-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const adult = parseInt(btn.dataset.adult);
                    const children = parseInt(btn.dataset.children);
                    const deposite = parseFloat(btn.dataset.deposite);
                    const refund = (adult + children) * deposite;

                    if (confirm('您確定要取消這筆預約嗎？')) {
                        fetch('cancel_reservation.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                            body: new URLSearchParams({
                                ReserveTransid: id,
                                adult: adult,
                                children: children,
                                deposite: deposite
                            })
                        })
                        .then(res => res.text())
                        .then(text => {
                            alert(text); // 顯示取消成功＋退款金額
                            location.reload(); // 重新整理頁面
                        })
                        .catch(err => alert('取消失敗，請稍後再試'));
                    }
                });
            });
        });
       
    </script>

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