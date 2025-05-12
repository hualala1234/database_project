<?php
include '../dbh.php';
session_start(); // 必須是第一行，前面不能有空白或 HTML！
$did = isset($_SESSION["did"]) ? $_SESSION["did"] : '';


// 👉 若點擊「接單」按鈕
if (isset($_GET['accept'])) {
    $tranId = intval($_GET['accept']); // 安全轉型

    $updateSql = "UPDATE dOrders SET orderStatus = 'accept' WHERE tranId = $tranId";
    if (mysqli_query($conn, $updateSql)) {
        // 更新成功後導回主頁
        header("Location: delivery_index.php");
        exit();
    } else {
        echo "接單更新失敗：" . mysqli_error($conn);
    }
}




// 👉 處理「略過」按鈕
if (isset($_GET['skip'])) {
    $tranId = intval($_GET['skip']);

    $check = $conn->prepare("SELECT 1 FROM DeliverySkip WHERE did = ? AND tranId = ?");
    $check->bind_param("ii", $did, $tranId);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO DeliverySkip (did, tranId) VALUES (?, ?)");
        $insert->bind_param("ii", $did, $tranId);
        $insert->execute();
        $insert->close();
    }
    $check->close();

    header("Location: delivery_index.php");
    exit();
}


// 👉 接單功能
if (isset($_GET['tranId'])) {
    $tranId = $_GET['tranId'];
    $stmt = $conn->prepare("UPDATE Transaction SET orderStatus = 'takeaway' WHERE tranId = ?");
    $stmt->bind_param("i", $tranId);
    $stmt->execute();
    $stmt->close();
    header("Location: delivery_index.php");
    exit();
}
?>


<body>

