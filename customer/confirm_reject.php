<?php
session_start();
include('../dbh.php');

// ✅ 檢查是否登入
if (!isset($_SESSION['cid'])) {
    echo json_encode(['success' => false, 'error' => '未登入']);
    exit;
}

$cid = $_SESSION['cid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tranId'])) {
    $tranId = intval($_POST['tranId']);

    // ✅ 加入 cid 驗證，防止竄改他人資料
    $stmt = $conn->prepare("UPDATE Transaction SET orderStatus = 'rejectConfirm' WHERE tranId = ? AND cid = ?");
    $stmt->bind_param("ii", $tranId, $cid);

    if ($stmt->execute()) {
        header("Location: index.php?cid=$cid");
        exit();
    } else {
        echo "更新失敗：" . $conn->error;
    }
} else {
    echo "無效的請求。";
}
?>
