<?php
    session_start();
    include '../../dbh.php';

    $cid = $_SESSION['cid'] ?? $_POST['cid'] ?? null;

    if (!$cid) {
        echo "未登入或缺少 cid。";
        exit();
    }

    // ✅ 先處理圖片上傳（獨立於其他欄位）
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
        $file = $_FILES['profileImage'];
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = 'upload_images/profile_' . $cid . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../../upload_images/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            $destPath = $uploadFileDir . basename($newFileName);

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $relativePath = 'upload_images/' . basename($newFileName);
                $updateSql = "UPDATE customer SET imageURL = ? WHERE cid = ?";
                $stmt = $conn->prepare($updateSql);
                $stmt->bind_param("si", $relativePath, $cid);
                if ($stmt->execute()) {
                    header("Location: setting.php");
                } else {
                    echo "圖片資料庫更新失敗。";
                }
            } else {
                echo "檔案搬移失敗。";
            }
        } else {
            echo "不支援的檔案格式。";
        }

        exit; // ⛔️ 加這個 exit()，避免往下跑到欄位更新邏輯
    }


// 2) 新增地址
if (isset($_POST['newAddress'])) {
    $newAddress = trim($_POST['newAddress']);
    if ($newAddress !== '') {
        $stmt = $conn->prepare("INSERT INTO caddress (cid, address_text) VALUES (?, ?)");
        $stmt->bind_param("is", $cid, $newAddress);
        if ($stmt->execute()) {
            

            header("Location: setting.php");
            exit;
        } else {
            exit("新增地址失敗：" . $stmt->error);
        }
    } else {
        exit("地址不得為空");
    }
}

// 3) 修改子地址
// 3) 編輯預設/子地址
if (isset($_POST['address_id'], $_POST['editedAddress'])) {
    $addressId     = $_POST['address_id'];       // 可能是 'default' 或 數字
    $editedAddress = trim($_POST['editedAddress']);
    if ($editedAddress === '') {
        exit("新的地址不得為空");
    }

    if ($addressId === 'default') {
        // 更新 customer 表的預設地址
        $stmt = $conn->prepare("UPDATE customer SET address = ? WHERE cid = ?");
        $stmt->bind_param("si", $editedAddress, $cid);
    } else {
        // 更新 caddress 表的子地址
        $aid = intval($addressId);
        $stmt = $conn->prepare(
          "UPDATE caddress 
             SET address_text = ? 
           WHERE address_id = ? AND cid = ?"
        );
        $stmt->bind_param("sii", $editedAddress, $aid, $cid);
    }

    if ($stmt->execute()) {
        header("Location: setting.php");
        exit;
    } else {
        exit("修改地址失敗：" . $stmt->error);
    }
}

    // 以下是欄位修改的邏輯
    // $field = $_POST['field'] ?? null;
    // $newValue = $_POST['newValue'] ?? null;
if (isset($_POST['field'], $_POST['newValue'])) {
    $field    = $_POST['field'];
    $newValue = trim($_POST['newValue']);

    $allowedFields = ['email', 'password', 'cName', 'birthday', 'phone','address'];
    if (!in_array($field, $allowedFields, true)) {
        exit("不允許修改該欄位。"); 
    }

    // 根據欄位更新資料庫
    // $updateSql = "UPDATE customer SET $field = ? WHERE cid = ?";
    // $stmt = $conn->prepare($updateSql);
    // $stmt->bind_param("si", $newValue, $cid);

    // if ($stmt->execute()) {
    //     echo "修改成功！";
    // } else {
    //     echo "修改失敗。";
    // }
    // 寫入 customer
    $sql = "UPDATE customer SET {$field} = ? WHERE cid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newValue, $cid);
    if ($stmt->execute()) {
        header("Location: setting.php");
        exit;
    } else {
        exit("更新失敗：" . $stmt->error);
    }
}

// 如果以上都沒 match，視為非法請求
exit('非法請求');


?>
