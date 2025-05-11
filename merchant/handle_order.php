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

    // 2. 撈出該筆交易所有商品（從 Orders）
    $sql = "SELECT o.pid, o.cid, o.quantity, p.price
            FROM Orders o
            JOIN Product p ON o.pid = p.pid
            WHERE o.tranId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tranId);
    $stmt->execute();
    $result = $stmt->get_result();

    // 3. 將每筆商品資料插入 Record
    $insertSql = "INSERT INTO Record (tranId, pid, cid, quantity, price) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);

    while ($row = $result->fetch_assoc()) {
        $insertStmt->bind_param("iiiii", $tranId, $row['pid'], $row['cid'], $row['quantity'], $row['price']);
        $insertStmt->execute();
    }

    $stmt->close();
    $insertStmt->close();
    } elseif ($action === 'reject') {
        // 拒絕訂單，刪除 Orders 中該筆交易編號所有商品
        $sql = "DELETE FROM Orders WHERE tranId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $tranId);
        $stmt->execute();

        // 可選：刪除 Transaction 資料（若沒有用處）
        $sql2 = "DELETE FROM Transaction WHERE tranId = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $tranId);
        $stmt2->execute();
    }elseif ($action === 'done') {
        // 完成訂單，更新 Transaction 狀態為 completed
        $sql = "UPDATE Transaction SET orderStatus = 'done' WHERE tranId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $tranId);
        $stmt->execute();
    }elseif ($action === 'take') {
        // 完成訂單，更新 Transaction 狀態為 completed
        $sql = "UPDATE Transaction SET orderStatus = 'take' WHERE tranId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $tranId);
        $stmt->execute();

        // 刪除 Orders 中該筆交易編號所有商品
        $sql2 = "DELETE FROM Orders WHERE tranId = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $tranId);
        $stmt2->execute();
        $stmt2->close();

    }

    header("Location: order.php?mid=$mid"); // 回到原頁面
    exit();
}
?>

