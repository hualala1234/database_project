<?php
include '../dbh.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $tranId = intval($_POST['tranId']);
    $did = isset($_SESSION["did"]) ? $_SESSION["did"] : '';

    if ($action === 'accept') {
        // 取得該筆訂單的 cid
        $cidResult = $conn->prepare("SELECT cid FROM Transaction WHERE tranId = ?");
        $cidResult->bind_param("i", $tranId);
        $cidResult->execute();
        $cidResult->bind_result($cid);
        $cidResult->fetch();
        $cidResult->close();

        // 新增 dOrders 記錄
        $insert = $conn->prepare("INSERT INTO dOrders (tranId, did, cid, dAcceptTime, orderStatus) VALUES (?, ?, ?, NOW(), 'accept')");
        $insert->bind_param("iii", $tranId, $did, $cid);
        $insertSuccess = $insert->execute();
        $insert->close();

        if ($insertSuccess) {
            // 更新 Transaction 表：orderStatus = 'takeaway'、did = ?
            $update = $conn->prepare("UPDATE Transaction SET  did = ? WHERE tranId = ?");
            $update->bind_param("ii", $did, $tranId);
            $update->execute();
            $update->close();
        }

        header("Location: delivery_index.php?did=$did");
        exit();
    } elseif ($action === 'reject') {
        // 拒絕訂單：插入 DeliverySkip 避免重複推薦
        $skip = $conn->prepare("INSERT IGNORE INTO DeliverySkip (did, tranId) VALUES (?, ?)");
        $skip->bind_param("ii", $did, $tranId);
        $skip->execute();
        $skip->close();

        header("Location: delivery_index.php?did=$did");
        exit();
    }
}
?>

<?php
session_start();
include('../dbh.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'arrived') {
    $tranId = $_POST['tranId'] ?? null;
    $did = $_SESSION['did'] ?? null;

    if (!$tranId || !$did) {
        die("缺少參數");
    }

    // 檢查是否有檔案上傳
    if (isset($_FILES['arrivePhoto']) && $_FILES['arrivePhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../upload_images/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmp = $_FILES['arrivePhoto']['tmp_name'];
        $fileName = basename($_FILES['arrivePhoto']['name']);
        $targetFile = $uploadDir . time() . '_' . $fileName;

        if (move_uploaded_file($fileTmp, $targetFile)) {
            $relativePath = str_replace('../', '', $targetFile); // 儲存相對路徑
            $now = date("Y-m-d H:i:s");

            // 更新資料庫
            $stmt = $conn->prepare("UPDATE dOrders SET arriveTime = ?, arrivePicture = ?, orderStatus = 'arrived' WHERE tranId = ? AND did = ?");
            $stmt->bind_param("ssii", $now, $relativePath, $tranId, $did);

            if ($stmt->execute()) {
                header("Location: delivery_index.php?did=$did");
                exit();
            } else {
                die("更新失敗：" . $conn->error);
            }
        } else {
            die("檔案上傳失敗");
        }
    } else {
        die("請選擇要上傳的圖片");
    }
}
?>

