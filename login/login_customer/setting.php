<?php
// 資料庫連線設定
$host = 'localhost';
$dbname = 'database';
$username = 'root';
$password = '';

$cid = 15;

$uploadDir = __DIR__ . '/../upload_images/';  // 伺服器儲存實體路徑
$webPathPrefix = '../../upload_images/';      // 給 HTML 用的相對路徑

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 根據新增或修改地址的結果，決定要顯示的訊息
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cid = $_POST['cid'];
        $newAddressText = $_POST['new_address_text'];

        if (isset($_POST['selectedAddress'])) {
            $selectedAddressId = $_POST['selectedAddress'];
            // 更新地址處理
            try {
                $stmt = $pdo->prepare("UPDATE cAddress SET address_text = :address_text WHERE address_id = :address_id AND cid = :cid");
                $stmt->execute(['address_text' => $newAddressText, 'address_id' => $selectedAddressId, 'cid' => $cid]);
                $message = '地址已修改成功';
            } catch (PDOException $e) {
                die("更新失敗: " . $e->getMessage());
            }
        } else {
            // 新增地址處理
            try {
                $stmt = $pdo->prepare("INSERT INTO cAddress (cid, address_text) VALUES (:cid, :address_text)");
                $stmt->execute(['cid' => $cid, 'address_text' => $newAddressText]);
                $message = '地址已新增成功';
            } catch (PDOException $e) {
                die("新增失敗: " . $e->getMessage());
            }
        }
    }

    if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] === UPLOAD_ERR_OK) {
        $tmpFile = $_FILES['newImage']['tmp_name'];
        $filename = basename($_FILES['newImage']['name']);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $newFilename = uniqid('img_') . '.' . $ext;
    
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
    
        $targetPath = $uploadDir . $newFilename;
        $imagePathForDB = $webPathPrefix . $newFilename;
    
        if (move_uploaded_file($tmpFile, $targetPath)) {
            // Update DB
            $pdo = new PDO("mysql:host=localhost;dbname=database;charset=utf8", 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("UPDATE customer SET imageURL = :imageURL WHERE cid = :cid");
            $stmt->execute(['imageURL' => $imagePathForDB, 'cid' => $cid]);
    
            header("Location: setting.php?upload=success");
            exit;
        } else {
            die("圖片移動失敗");
        }
    } else {
        die("圖片上傳失敗，錯誤碼：" . $_FILES['newImage']['error']);
    }

    // 取得客戶資料
    $stmt = $pdo->prepare("SELECT * FROM customer WHERE cid = :cid");
    $stmt->execute(['cid' => $cid]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    // 撈出地址（多筆）
    $stmtAddr = $pdo->prepare("SELECT * FROM cAddress WHERE cid = :cid");
    $stmtAddr->execute(['cid' => $cid]);
    $addresses = $stmtAddr->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>新增失敗: " . $e->getMessage() . "</div>";
    die("連線失敗: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>設定頁面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Popup message styling */
        .popup-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #28a745;
            color: white;
            padding: 20px;
            border-radius: 5px;
            display: none;
            z-index: 9999;
        }
    </style>
</head>
<body class="container mt-5">
    <!-- 顯示 Popup 消息 -->
    <div id="popupMessage" class="popup-message"></div>

    <h2>個人資料設定</h2>
    

    <?php if ($customer): ?>
        <ul class="list-group">
            <!-- 可編輯欄位 -->
            <li class="list-group-item">
                <strong>姓名:</strong> 
                <span id="cNameDisplay"><?= htmlspecialchars($customer['cName']) ?></span>
                <button class="btn btn-sm btn-outline-primary ms-2" onclick="editField('cName')">✏️ 修改</button>
            </li>
            <li class="list-group-item">
                <strong>Email:</strong> 
                <span id="emailDisplay"><?= htmlspecialchars($customer['email']) ?></span>
                <button class="btn btn-sm btn-outline-primary ms-2" onclick="editField('email')">✏️ 修改</button>
            </li>
            <li class="list-group-item">
                <strong>密碼:</strong> 
                <span id="passwordDisplay"><?= htmlspecialchars($customer['password']) ?></span>
                <button class="btn btn-sm btn-outline-primary ms-2" onclick="editField('password')">✏️ 修改</button>
            </li>
            <li class="list-group-item">
                <strong>連絡電話:</strong> 
                <span id="phoneDisplay"><?= htmlspecialchars($customer['phone']) ?></span>
                <button class="btn btn-sm btn-outline-primary ms-2" onclick="editField('phone')">✏️ 修改</button>
            </li>
            <li class="list-group-item">
                <strong>生日:</strong> 
                <span id="birthdayDisplay"><?= htmlspecialchars($customer['birthday']) ?></span>
                <button class="btn btn-sm btn-outline-primary ms-2" onclick="editField('birthday')">✏️ 修改</button>
            </li>

            <!-- 僅顯示圖片 -->
            <li class="list-group-item">
                <strong>頭像:</strong><br>
                <?php if (!empty($customer['imageURL'])): ?>
                    <img src="../../<?= htmlspecialchars($customer['imageURL']) ?>" alt="照片" class="friend-imageURL mb-2" style="max-width: 150px;">
                <?php else: ?>
                    <span>未上傳圖片</span>
                <?php endif; ?>
                <br>
                <!-- 新增：修改照片按鈕 -->
                <button class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#editPhotoModal">
                    修改照片
                </button>
            </li>


            <h4>目前地址</h4>
            <ul class="list-group mb-3">
                <?php foreach ($addresses as $addr): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($addr['address_text']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- 編輯地址按鈕，觸發編輯地址的彈出視窗 -->
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#editAddressModal">
                編輯地址
            </button>

            <!-- 編輯地址 Modal -->
            <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editAddressModalLabel">編輯地址</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="cid" value="<?= $cid ?>">

                            <h6>選擇要修改的地址</h6>
                            <div class="mb-3">
                                <?php foreach ($addresses as $addr): ?>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name="selectedAddress" value="<?= $addr['address_id'] ?>" id="address_<?= $addr['address_id'] ?>" onchange="showNewAddressInput(<?= $addr['address_id'] ?>, '<?= htmlspecialchars($addr['address_text']) ?>')">
                                        <label class="form-check-label" for="address_<?= $addr['address_id'] ?>">
                                            <?= htmlspecialchars($addr['address_text']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- 顯示新地址輸入框 -->
                            <div class="mb-3" id="newAddressInput" style="display: none;">
                                <label for="editAddressText" class="form-label">新的地址：</label>
                                <input type="text" class="form-control" name="new_address_text" id="editAddressText" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">儲存地址</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 新增地址按鈕，觸發新增地址的彈出視窗 -->
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addNewAddressModal">
                新增地址
            </button>

            <!-- 新增地址 Modal -->
            <div class="modal fade" id="addNewAddressModal" tabindex="-1" aria-labelledby="addNewAddressModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addNewAddressModalLabel">新增地址</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="cid" value="<?= $cid ?>">
                            <div class="mb-3">
                                <label for="editAddressText" class="form-label">地址內容：</label>
                                <input type="text" class="form-control" name="new_address_text" id="addAddressText" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">儲存新地址</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        </div>
                    </form>
                </div>
            </div>



            <!-- 純顯示欄位 -->
            <li class="list-group-item"><strong>介紹人:</strong> <?= htmlspecialchars($customer['introducer']) ?></li>
            <li class="list-group-item"><strong>註冊時間:</strong> <?= htmlspecialchars($customer['cRegistrationTime']) ?></li>
        </ul>
    <?php else: ?>
        <div class="alert alert-warning">找不到該客戶資料。</div>
    <?php endif; ?>

    <!-- 修改表單 Modal -->
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
              <label for="newValue" class="form-label">新值：</label>
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

    <!-- 修改照片 Modal -->
    <div class="modal fade" id="editPhotoModal" tabindex="-1" aria-labelledby="editPhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="upload_image.php" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPhotoModalLabel">修改頭像</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="cid" value="<?= $cid ?>">
                    <div class="mb-3">
                        <label for="newImage" class="form-label">選擇新照片：</label>
                        <input type="file" class="form-control" name="newImage" id="newImage" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">上傳</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 顯示popup訊息，並自動在2秒後隱藏
        function showPopupMessage(message) {
            var popup = document.getElementById("popupMessage");
            popup.innerHTML = message;
            popup.style.display = "block";

            // 隱藏popup訊息2秒後
            setTimeout(function() {
                popup.style.display = "none";
            }, 2000);
        }

        // 根據PHP結果顯示成功訊息
        <?php if (isset($message)): ?>
            showPopupMessage("<?= $message ?>");
        <?php endif; ?>

        function showMessage(message) {
            const messageElement = document.getElementById('message');
            messageElement.innerHTML = message;
            messageElement.style.display = 'block';
            
            setTimeout(function() {
                messageElement.style.display = 'none';
            }, 2000);  // 2秒後自動消失
        }

        function editField(field) {
            const value = document.getElementById(field + 'Display').innerText.trim();
            const input = document.getElementById('newValueInput');
            document.getElementById('fieldInput').value = field;

            // 顯示中文欄位說明
            const fieldTitleMap = {
                cName: '編輯姓名',
                email: '編輯 Email',
                password: '編輯密碼',
                birthday: '編輯生日',
                phone: '編輯電話',
            };
            document.querySelector('.modal-title').textContent = fieldTitleMap[field] || '編輯欄位';

            // 根據欄位切換輸入類型
            if (field === 'birthday') {
                input.type = 'date';
                const formatted = value.replace(/\//g, '-');
                input.value = formatted;
            } else {
                input.type = 'text';
                input.value = value;
            }

            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }

         // 顯示新地址輸入框的函式，並且填入選擇的地址內容
        function showNewAddressInput(addressId, addressText) {
            document.getElementById('newAddressInput').style.display = 'block';
            document.getElementById('editAddressText').value = addressText; // 顯示已選擇的地址
        }

    </script>

</body>
</html>
