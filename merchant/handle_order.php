<?php
include '../dbh.php';
session_start(); // 必須是第一行，前面不能有空白或 HTML！
$mid = isset($_SESSION["mid"]) ? $_SESSION["mid"] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tranId = $_POST['tranId'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        // 1. 更新 Transaction 狀態為 making
    $sql = "UPDATE Transaction SET orderStatus = 'making' WHERE tranId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tranId);
    $stmt->execute();
    $stmt->close();

    
    } elseif ($action === 'reject') {
        
        // 將 Transaction 的 orderStatus 改為 'reject'
        $sql = "UPDATE Transaction SET orderStatus = 'reject' WHERE tranId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $tranId);
        $stmt->execute();

    }elseif ($action === 'done') {
        // 完成訂單，更新 Transaction 狀態為 completed
        $sql = "UPDATE Transaction SET orderStatus = 'done' WHERE tranId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $tranId);
        $stmt->execute();
    }elseif ($action === 'takeaway') {
        // 完成訂單，更新 Transaction 狀態為 completed
        $sql = "UPDATE Transaction SET orderStatus = 'takeaway' WHERE tranId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $tranId);
        $stmt->execute();

        // 插入 dOrders 的 takeTime
        $sql2 = "UPDATE dOrders SET takeTime = NOW(), orderStatus = 'takeaway' WHERE tranId = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $tranId);
        $stmt2->execute();

    }

    header("Location: order.php?mid=$mid"); // 回到原頁面
    exit();
}
?>

