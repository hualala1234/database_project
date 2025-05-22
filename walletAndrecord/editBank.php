<?php
// connect to DB
include('connect.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? '';
$role = $_GET['role'] ?? '';

// echo  $id,$role;
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

// 查詢資料
$sql = "SELECT * FROM `$table` WHERE `$key` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if (!$row) {
    die("找不到帳號資料，請確認 $table 表中是否有 id = $id 的資料");
}
$number = str_split($row['accountNumber'] ?? '', 4);

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bankCode = $_POST['bankCode'];
    $accountNumber = $_POST['card-number'] . $_POST['card-number-1'] . $_POST['card-number-2'] . $_POST['card-number-3'];

    $updateSql = "UPDATE `$table` SET bankCode = ?, accountNumber = ? WHERE `$key` = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sss", $bankCode, $accountNumber, $id);

    if ($updateStmt->execute()) {
        header("Location: ./{$role}_wallet.php?id={$id}&role={$role}");
        exit();
    } else {
        echo "Error: " . $updateStmt->error;
    }
    $updateStmt->close();
    echo "更新筆數：" . $updateStmt->affected_rows;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./addCard.css">
    <script src="./addCard.js"></script>
    <title>Edit Bank Account</title>
</head>
<body>
<div class="checkout">
    <div id="top_word" style="display: flex; flex-direction: row;">
        <img src="./image/credit-card.png" alt="credit card icon" width="35" height="35">
        <h1 style="margin:0;margin-left:10px">Edit Bank Account</h1>
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



