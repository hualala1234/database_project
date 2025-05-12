<?php
include('connect.php');

$cardName = $_GET['cardName'] ?? '';
$id = $_GET['id'] ?? '';
$role = $_GET['role'] ?? '';

if ($cardName && $id) {
    // 1️⃣ 修改 transaction 的卡片名稱
    $updateSql = "UPDATE transaction SET cardName = 'no_card' WHERE cid = ? AND cardName = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("is", $id, $cardName);

    if (!$updateStmt->execute()) {
        die("更新 transaction 卡片名稱失敗: " . $updateStmt->error);
    }

    $updateStmt->close();

    // 2️⃣ 確認 update 是否真的成功改到資料（選配：debug用）
    // $check = $conn->query("SELECT * FROM transaction WHERE cid = $id AND cardName = '$cardName'");
    // if ($check->num_rows > 0) die("仍有未改到的交易紀錄！");

    // 3️⃣ 刪除卡片
    $deleteSql = "DELETE FROM card WHERE cid = ? AND cardName = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("is", $id, $cardName);

    if ($deleteStmt->execute()) {
        header("Location: ./c_wallet.php?id=" . urlencode($id) . "&role=" . urlencode($role));
        exit();
    } else {
        echo "Error deleting card: " . $deleteStmt->error;
    }

    $deleteStmt->close();
} else {
    echo "參數不完整：需要 cardName 和 id";
}

$conn->close();
?>
