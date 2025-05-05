<?php
include '../dbh.php'; // 資料庫連線

$cid = $_POST['cid'];
$cardName = $_POST['cardName'];
$cardHolder = $_POST['cardHolder'];
$cardNumber = $_POST['cardNumber'];
$cardType = $_POST['cardType'];
$cvv = $_POST['cvv'];
$expirationDate = $_POST['expirationDate'];

// 先檢查是否有重複的卡號 for 同一使用者
$check_sql = "SELECT * FROM card WHERE cid = ? AND cardNumber = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("is", $cid, $cardNumber);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // 卡片已存在，不插入
    echo "<script>
        alert('這張卡片已經存在！請勿重複新增。');
        window.location.href = 'checkout.php';
    </script>";
    exit();
}

// 若無重複，插入新卡片
$sql = "INSERT INTO card (cid, cardName, cardHolder, cardNumber, cardType, cvv, expirationDate)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssss", $cid, $cardName, $cardHolder, $cardNumber, $cardType, $cvv, $expirationDate);

if ($stmt->execute()) {
    header("Location: checkout.php");
    exit();
} else {
    echo "新增失敗：" . $stmt->error;
}
?>
