<?php
// 連接資料庫
include('connect.php');

// 取得 cardName
$cardName = $_GET['cardName'] ?? '';

if ($cardName) {
    $sql = "DELETE FROM card WHERE cardName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cardName);

    if ($stmt->execute()) {
        // 刪除成功，回到 c_wallet.php
        header("Location: c_wallet.php");
        exit();
    } else {
        echo "Error deleting card: " . $stmt->error;
    }
} else {
    echo "No card specified!";
}
?>
