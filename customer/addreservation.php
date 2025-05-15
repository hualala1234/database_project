<?php
include '../dbh.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 拆出日期時間
  list($resDate, $resTime) = explode(' ', $_POST['reservationDateTime']);
  $adult    = intval($_POST['adult']);
  $children = intval($_POST['children']);
  $deposite = floatval($_POST['deposite']);
  $cid = intval($_SESSION['cid']);
  $mid = intval($_SESSION['mid']);

  // **把取消 & 遲到時間預設在後端**  
  $cancel = '03:00:00';  // 預約前 3 小時可取消
  $late   = '00:15:00';  // 最晚可遲到 15 分鐘

  $stmt = $conn->prepare("
    INSERT INTO reservation
      (reservationDate, reservationTime,
       adult, children,
       acceptableCancellationTime,
       acceptableLateTime,
       deposite, Reservationcid, mid)
    VALUES (?,?,?,?,?,?,?,?,?)
  ");
  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }
  $stmt->bind_param(
    'ssiissdii',
    $resDate, $resTime,
    $adult,   $children,
    $cancel,  $late,
    $deposite, $cid, $mid
  );
  if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
  }
  $stmt->close();

  // 插入成功，回到確認面板
  header('Location: calendar.php?panel=confirm');
  exit;
}
