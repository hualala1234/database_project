<?php
session_start();
include('../dbh.php');

$cid = $_SESSION['cid'] ?? null;
$mid = $_SESSION['mid'] ?? null;

if (!$cid || !$mid) {
    die("未登入或店家資料錯誤");
}

$reservationDateTime = $_POST['reservationDateTime'] ?? '';
$adult    = (int)($_POST['adult'] ?? 0);
$children = (int)($_POST['children'] ?? 0);
$cancelTime = $_POST['acceptableCancellationTime'] ?? '03:00:00';
$lateTime   = $_POST['acceptableLateTime'] ?? '00:15:00';
$deposite   = floatval($_POST['deposite'] ?? 0);

if (!$cid || !$mid || !$reservationDateTime) {
    exit("參數錯誤");
}

// 拆分 datetime
list($date, $time) = explode(' ', $reservationDateTime);

// 計算總訂金（以每位大人收費）
$totalDeposit = $deposite * ($adult + $children);

// ✅ 先檢查是否重複預約
$stmt = $conn->prepare("SELECT COUNT(*) FROM ReserveTrans WHERE Reservationcid = ? AND mid = ? AND reservationDate = ? AND reservationTime = ?");
$stmt->bind_param("iiss", $cid, $mid, $date, $time);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

// ✅ 如果有重複，先跳 alert 並讓使用者決定
if ($count > 0 && !isset($_POST['confirm_duplicate'])) {
    echo "<script>
        if (confirm('您已在相同時段預約過此餐廳，確定要重複訂位嗎？')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'addreservation.php';

            const fields = " . json_encode($_POST) . ";

            for (let key in fields) {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            }

            let confirmInput = document.createElement('input');
            confirmInput.type = 'hidden';
            confirmInput.name = 'confirm_duplicate';
            confirmInput.value = '1';
            form.appendChild(confirmInput);

            document.body.appendChild(form);
            form.submit();
        } else {
            alert('已取消訂位');
            window.history.back();
        }
    </script>";
    exit;
}

$conn->begin_transaction();

try {
    // 1. 檢查錢夠不夠
    $stmt = $conn->prepare("SELECT balance FROM wallet WHERE cid = ?");
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $stmt->bind_result($balance);
    $stmt->fetch();
    $stmt->close();

    if ($balance < $totalDeposit) {
        throw new Exception("餘額不足，無法完成訂位。");
    }

    // 2. 扣除訂金
    $stmt = $conn->prepare("UPDATE wallet SET balance = balance - ? WHERE cid = ?");
    $stmt->bind_param("di", $totalDeposit, $cid);
    $stmt->execute();
    if ($stmt->affected_rows <= 0) throw new Exception("扣款失敗");
    $stmt->close();

    // 3. 新增預約紀錄
    $stmt = $conn->prepare("
        INSERT INTO ReserveTrans
        (Reservationcid, mid, reservationDate, reservationTime, adult, children, acceptableCancellationTime, acceptableLateTime, deposite)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissiiisd", $cid, $mid, $date, $time, $adult, $children, $cancelTime, $lateTime, $deposite);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    echo "<script>alert('訂位成功，已扣款 NT$$totalDeposit'); window.location.href='reservation.php';</script>";
} catch (Exception $e) {
    $conn->rollback();
    echo "<script>alert('訂位失敗：" . $e->getMessage() . "'); window.history.back();</script>";
}
?>
