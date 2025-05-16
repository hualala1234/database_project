<?php
session_start();
include '../../dbh.php';

$cid = $_SESSION['cid'] ?? null;

if (!$cid) {
    echo "請先登入。";
    exit();
}

$sql = "SELECT email, password, cName, cRegistrationTime, birthday, imageURL, phone, address FROM customer WHERE cid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1):
    $customer = $result->fetch_assoc();
else:
    echo "找不到使用者資料。";
    exit();
endif;


// 撈取使用者的所有地址

$address_list = [];
$address_sql = "SELECT  address_id, address_text FROM caddress WHERE cid = ?";
$address_stmt = $conn->prepare($address_sql);

if (!$address_stmt) {
    die("Prepare 失敗：" . $conn->error);  // 印出 SQL 錯誤細節
}

$address_stmt->bind_param("i", $cid);
$address_stmt->execute();
$address_result = $address_stmt->get_result();

while ($row = $address_result->fetch_assoc()) {
    $address_list[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>會員資料設定</title>
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
  <link href="../../lib/lightbox/css/lightbox.min.css" rel="stylesheet">
  <link href="../../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

  <link href="../../css/style.css" rel="stylesheet">
  <!-- Customized Bootstrap Stylesheet -->
  <link href="../../css/bootstrap.min.css" rel="stylesheet">
  <!-- 引入 jQuery UI CSS（使得排序元素顯示為拖曳狀態） -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

  <style>/* 1. 讓固定頂部的 navbar 不會蓋到內容 */
body {
  padding-top: 120px; /* 根據 Navbar 高度微調 */
}

/* 2. List-group-item 額外間距、圓角，看起來更清爽 */
.list-group-item {
  margin-bottom: 0.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

/* 3. 地址列表項目加底色、間距 */
#addressDisplay li {
  background-color: #f8f9fa;
  border-radius: 0.5rem;
  padding: 0.5rem 1rem;
  margin-bottom: 0.5rem;
}

/* 4. 按鈕風格微調 */
.btn-sm {
  padding: 0.25rem 0.5rem;
}
.btn-success {
  background-color: #28a745;
  border-color: #28a745;
}
.btn-success:hover {
  background-color: #218838;
  border-color: #1e7e34;
}

/* 5. Modal 弹窗圆角 */
.modal-content {
  border-radius: 1rem;
}

/* 6. Radio 列表間距 */
.form-check {
  margin-bottom: 0.5rem;
}

/* 7. 在行動裝置上，調整列表文字大小 */
@media (max-width: 576px) {
  .list-group-item {
    font-size: 0.9rem;
  }
  .btn-sm {
    font-size: 0.8rem;
  }
}

/* 8. 如果想讓 Modal Body 更好對齊 */
.modal-body {
  padding: 1.5rem;
}
</style>

</head>
<body class="p-4">
  
  <!-- <h2 class="mb-4">會員資料</h2> -->

  <ul class="list-group" style= 'margin-top:150px'>
    <?php
      $fields = [
        'email' => 'Email',
        'password' => '密碼',
        'cName' => '姓名',
        'birthday' => '生日',
        'phone' => '電話',
        'address' => '地址'
      ];
    ?>
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
            <a href="../../customer/index.php?cid=<?php echo $cid; ?>" class="navbar-brand"><h1 class="text-primary display-6">Junglebite過敏設定</h1></a>

            <!-- <a href="merchant_shop.php?mid=< ?php echo $mid; ?>" class="navbar-brand"><h1 class="text-primary display-6">Junglebite 個人設定</h1></a> -->
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
                        <img src=" ../../login/success.png" alt="Success" style="width: 40px; height: 40px; filter: brightness(0) saturate(100%) invert(42%) sepia(91%) saturate(356%) hue-rotate(71deg) brightness(94%) contrast(92%);">
                        </a>

                        <div id="myDropdown" class="dropdown-content" style="display: none; position: absolute; background-color: white; min-width: 120px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; right: 0; border-radius: 8px;">

                            <?php if ($_SESSION['role'] === 'm'): ?>
                                <a href="/database/merchant/setting.php" class="dropdown-item">商家設定</a>
                            <?php elseif ($_SESSION['role'] === 'c'): ?>
                                <a href="/database_project/allergy/allergy.php?cid=<?php echo $cid; ?>" class="dropdown-item">過敏設定</a>
                                <a href="../../claw_machine/claw.php?cid=<?php echo $cid; ?>" class="dropdown-item">優惠券活動</a>
                                <a href="../walletAndrecord/c_wallet.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">錢包</a>
                                <a href="../walletAndrecord/c_record.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">交易紀錄</a>
                                <a href="../customer/friends.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">我的好友</a>
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


    <div class="container setting-row">
      <div class="row">
        <!-- 左側：會員資料列表 -->
        <div class="col-lg-8">
          <ul class="list-group">
            <?php foreach ($fields as $field => $label): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <strong><?= $label ?>:</strong>
                  <?php if ($field === 'address'): ?>
                    <!-- 先顯示 customer 表的主要地址 -->
                    <p><strong>預設地址：</strong>
                      <span id="addressDisplay">
                        <?= htmlspecialchars($customer['address']) ?>
                      </span>
                    </p>
                    <!-- 再列出所有 caddress 裡的地址 -->
                    <ul class="mb-0 ps-3">
                      <?php foreach ($address_list as $addr): ?>
                        <li class="mb-1"><?= htmlspecialchars($addr['address_text']) ?></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else: ?>
                    <span id="<?= $field ?>Display">
                      <?= htmlspecialchars($customer[$field]) ?>
                    </span>
                  <?php endif; ?>
                </div>
                <div>
                  <?php if ($field === 'address'): ?>
                    <button class="btn btn-sm btn-success me-1" onclick="showAddAddressModal()">
                      ➕ 新增
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="showEditAddressModal()">
                      ✏️ 編輯地址
                    </button>
                  <?php else: ?>
                    <!-- 其他欄位的通用編輯按鈕 -->
                    <button class="btn btn-sm btn-outline-primary" onclick="editField('<?= $field ?>')">
                      ✏️ 編輯
                    </button>
                  <?php endif; ?>
                </div>
              </li>
            <?php endforeach; ?>

            <!-- 註冊時間 -->
            <li class="list-group-item">
              <strong>註冊時間:</strong>
              <span id="registrationDisplay">
                <?= htmlspecialchars($customer['cRegistrationTime']) ?>
              </span>
            </li>
          </ul>
        </div>

        <!-- 右側：大頭貼上傳 -->
        <div class="col-lg-4">
          <div class="card profile-card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">大頭貼</h5>

              <?php if (!empty($customer['imageURL'])): ?>
                <img src="../../<?= htmlspecialchars($customer['imageURL']) ?>"
                    alt="照片"
                    class="mb-3" style='height:240px'>
              <?php else: ?>
                <p class="text-muted">尚未上傳</p>
              <?php endif; ?>

              <form action="update_setting.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="profileImage" class="form-label">選擇新大頭貼：</label>
                  <input type="file" class="form-control" name="profileImage" id="profileImage" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary w-100">
                  儲存圖片
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 新增地址 Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="update_setting.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAddressModalLabel">新增地址</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="cid" value="<?= $cid ?>">
                        <div class="mb-3">
                            <label for="newAddress" class="form-label">地址：</label>
                            <textarea class="form-control" name="newAddress" id="newAddress" rows="1" required></textarea>
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
    
   
    <!-- 編輯地址 Modal -->
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="POST" action="update_setting.php">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editAddressModalLabel">編輯地址</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="cid" value="<?= $cid ?>">

              <!-- 单选框：列出所有地址 -->
              <div class="mb-3">
                <label class="form-label">選擇要修改的地址：</label>
                
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="address_id"
                        id="addr_default" value="default" required>
                  <label class="form-check-label" for="addr_default">
                    <?= htmlspecialchars($customer['address']) ?> （預設地址）
                  </label>
                </div>
                <?php foreach ($address_list as $addr): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="address_id"
                          id="addr<?= $addr['address_id'] ?>" value="<?= $addr['address_id'] ?>">
                    <label class="form-check-label" for="addr<?= $addr['address_id'] ?>">
                      <?= htmlspecialchars($addr['address_text']) ?>
                    </label>
                  </div>
                <?php endforeach; ?>
      
              </div>

              <!-- 輸入新的地址文字 -->
              <div class="mb-3">
                <label for="editedAddressText" class="form-label">新的地址內容：</label>
                <textarea class="form-control" name="editedAddress" id="editedAddressText" rows="2" required></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">儲存修改</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
            </div>
          </div>
        </form>
      </div>
    </div>

  </ul>

  <!-- 編輯 Modal -->
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" method="POST" action="update_setting.php">
        <div class="modal-header">
          <h5 class="modal-title">修改欄位</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="cid" value="<?= $cid ?>">
          <input type="hidden" name="field" id="fieldInput">
          <input type="hidden" name="extraId" id="extraIdInput">
          <div class="mb-3">
            <label for="newValueInput" class="form-label">新值：</label>
            <input type="text" class="form-control" name="newValue" id="newValueInput">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">儲存</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS + Modal Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function editField(field) {
      const fieldMap = {
        email: '編輯 Email',
        password: '編輯密碼',
        cName: '編輯姓名',
        birthday: '編輯生日',
        phone: '編輯電話',
        address: '編輯地址'
      };

      const input = document.getElementById('newValueInput');
      const value = document.getElementById(field + 'Display').innerText.trim();

      document.querySelector('.modal-title').textContent = fieldMap[field] || '修改欄位';
      document.getElementById('fieldInput').value = field;
      document.getElementById('extraIdInput').value = '';
      
      // 切換輸入類型
      if (field === 'birthday') {
        input.type = 'date';
        input.value = value.replace(/\//g, '-');
      } else {
        input.type = 'text';
        input.value = value;
      }

      const modal = new bootstrap.Modal(document.getElementById('editModal'));
      modal.show();
    }

    // 顯示新增地址的彈出視窗
    function showAddAddressModal() {
        const addAddressModal = new bootstrap.Modal(document.getElementById('addAddressModal'));
        addAddressModal.show();
    }

    function showNewAddressInput(cid, text) {
        const section = document.getElementById("editAddressSection");
        const textarea = document.getElementById("editedAddress");

        section.style.display = "block";
        textarea.value = text;
    }

    function showEditAddressModal() {
      const modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
      modal.show();
    }


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
