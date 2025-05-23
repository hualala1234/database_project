<?php
// connect to DB
include('connect.php');
session_start();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? '';
$role = $_GET['role'] ?? '';

// 根據角色選擇不同表格
$table = '';
$key = '';
if ($role === 'm') {
    $table = 'mbank';
    $key = 'mid';
} elseif ($role === 'd') {
    $table = 'dbank';
    $key = 'did';
} else {
    die("Invalid role");
}

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bankCode = $_POST['bankCode'];
    $accountNumber = $_POST['card-number'] . $_POST['card-number-1'] . $_POST['card-number-2'] . $_POST['card-number-3'];

    // 檢查是否已存在該帳戶資料
    $checkSql = "SELECT * FROM `$table` WHERE `$key` = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    if ($checkResult->num_rows > 0) {
        die("此帳戶已存在，無法重複新增！");
    }
    $checkStmt->close();

    // 執行 INSERT
    $insertSql = "INSERT INTO `$table` (`$key`, bankCode, accountNumber) VALUES (?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("sss", $id, $bankCode, $accountNumber);

    if ($insertStmt->execute()) {
        header("Location: ./{$role}_wallet.php?id={$id}&role={$role}");
        exit();
    } else {
        echo "Error: " . $insertStmt->error;
    }
    $insertStmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./addCard.css">
    <script src="./addCard.js"></script>
    <title>Add Bank Account</title>
</head>
<body>
<div class="checkout">
    <div id="top_word" style="display: flex; flex-direction: row;">
        <img src="./image/credit-card.png" alt="credit card icon" width="35" height="35">
        <h1 style="margin:0;margin-left:10px">Add Bank Account</h1>
    </div>
    <form class="form" autocomplete="off" method="POST">
        <fieldset>
            <label for="bankCode">Bank Code</label>
            <input type="text" id="bankCode" name="bankCode" placeholder="<?php echo htmlspecialchars($row['bankCode'] ?? '', ENT_QUOTES); ?>" value="<?php echo htmlspecialchars($row['bankCode'] ?? '', ENT_QUOTES); ?>" required />
        </fieldset>
        <fieldset>
            <label for="card-number">Account Number</label>
            <input type="text" name="card-number" maxlength="4" class="input-cart-number" value="<?= $number[0] ?? '' ?>" required />
            <input type="text" name="card-number-1" maxlength="4" class="input-cart-number" value="<?= $number[1] ?? '' ?>" required />
            <input type="text" name="card-number-2" maxlength="4" class="input-cart-number" value="<?= $number[2] ?? '' ?>" required />
            <input type="text" name="card-number-3" maxlength="4" class="input-cart-number" value="<?= $number[3] ?? '' ?>" required />
        </fieldset>
        <button class="btn" type="submit">Submit</button>
    </form>
</div>
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.input-cart-number');

    inputs.forEach((input, idx) => {
        // 當輸入時
        input.addEventListener('input', function() {
            if (this.value.length === 4 && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }
        });

        // 當按下鍵盤時
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value.length === 0 && idx > 0) {
                const prevInput = inputs[idx - 1];
                prevInput.focus();
                // 讓游標跳到上格的最後一個字
                const length = prevInput.value.length;
                prevInput.setSelectionRange(length, length);
            }
        });
    });
});
</script>



