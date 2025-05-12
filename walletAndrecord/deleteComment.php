<?php
// 引入資料庫連線
include('connect.php');

// 安全取得 POST 傳來的參數
$tranId = $_POST['tranId'] ?? '';
$type = $_POST['type'] ?? '';

// 僅允許刪除 mComment 或 dComment 這兩個欄位
if ($tranId && in_array($type, ['mComment', 'dComment'])) {
    // 使用 prepared statement 防止 SQL injection
    $sql = "UPDATE transaction SET $type = NULL WHERE tranId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tranId);

    if ($stmt->execute()) {
        echo "success";
        // 刪除成功，回到 c_wallet.php
        header("Location: all_cord.php");
        exit();
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "invalid_input";
    file_put_contents('log.txt', "tranId: $tranId, type: $type\n", FILE_APPEND);
}

$conn->close();
?>
