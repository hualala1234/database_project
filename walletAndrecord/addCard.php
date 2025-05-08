<?php
// 引入資料庫連線
include('connect.php');
$id = $_GET['id']; // 或是從 $_SESSION['cid'];
$role = $_GET['role']; 
if (!isset($id)) die("Error: cid 未設定");

// 檢查是否有表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得表單資料
    $cardHolder = $_POST['card-holder'];
    $cardName = $_POST['card-name'];
    $cardNumber = $_POST['card-number'] . $_POST['card-number-1'] . $_POST['card-number-2'] . $_POST['card-number-3']; // 合併卡號
    $cardType = $_POST['card-type'];
    $cvv = $_POST['card-ccv'];
    $expirationDate = $_POST['card-expiration-year'] . '-' . $_POST['card-expiration-month']; // 以 yyyy-mm 格式儲存

    echo $expirationDate;

    // 準備 SQL 查詢語句來插入資料
    $sql = "INSERT INTO card (cid, cardHolder, cardName, cardNumber, cardType, cvv, expirationDate) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

    // 準備語句並綁定參數
    if ($stmt = $conn->prepare($sql)) {
      $stmt->bind_param("issssss", $id, $cardHolder, $cardName, $cardNumber, $cardType, $cvv, $expirationDate);

        // 執行插入操作
        if ($stmt->execute()) {
            // echo "Card added successfully!";
            // 資料新增成功後導向回 c_wallet.php
            header("Location: ./c_wallet.php?id=" . urlencode($id) . "&role=" . urlencode($role));
            exit(); // 確保停止執行後續程式碼
        } else {
            echo "Error: " . $stmt->error;
        }

        // 關閉語句
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // 關閉資料庫連線
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./addCard.css">
    <script src="./addCard.js"></script>
    <title>Add Card</title>
</head>
<body>
<div class="checkout">
  <div id="top_word" style="display: flex;flex-direction: row;">
    <img src="./image/credit-card.png" alt="credit card icon" width="35" height="35">
    <h1 style="margin:0;margin-left:10px">Add Card</h1>
  </div>
    <style>label{font-size:13px;}</style>
    <form class="form" autocomplete="off" novalidate method="POST" >
      <fieldset>
          <label for="card-name">Card Name</label>
          <input type="text" id="card-name" name="card-name" placeholder="card's name" required />
      </fieldset>
      <fieldset>
          <label for="card-number">Card Number</label>
          <input type="num" id="card-number" class="input-cart-number" name="card-number" class="input-cart-number" maxlength="4" placeholder="0000" required />
          <input type="num" id="card-number-1" class="input-cart-number" name="card-number-1" class="input-cart-number" maxlength="4" placeholder="0000" required />
          <input type="num" id="card-number-2" class="input-cart-number" name="card-number-2" class="input-cart-number" maxlength="4" placeholder="0000" required />
          <input type="num" id="card-number-3" class="input-cart-number" name="card-number-3" class="input-cart-number" maxlength="4" placeholder="0000" required />
      </fieldset>
      <fieldset>
          <label for="card-holder">Card Holder</label>
          <input type="text" id="card-holder" name="card-holder" placeholder="holder's name" required />
      </fieldset>
      <fieldset style="display: flex;align-items: center;">
        <label style="width:67px;display: flex;align-items: center;">Type：</label>
        <label style="width:78px;display: flex; align-items: center;">
          <input type="radio" name="card-type" value="visa" style="height:15px;width:15px;"> Visa
        </label>
        <label style="width: 150px;px;display: flex; align-items: center;">
          <input type="radio" name="card-type" value="MasterCard" style="height:15px;width:15px;"> MasterCard
        </label>
        <label style="width:50px;display: flex;align-items: center;">
        <input type="radio" name="card-type" value="JSB" style="height:15px;width:15px;"> JSB
      </label>
      </fieldset>
      <fieldset class="fieldset-expiration">
          <label for="card-expiration-month">Expiration date</label>
          <div class="select">
              <select id="card-expiration-month" name="card-expiration-month" required>
              <option value="">Month</option>
              <?php
              for ($i = 1; $i <= 12; $i++) {
                  $val = str_pad($i, 2, "0", STR_PAD_LEFT);
                  $selected = ($val === $selected_month) ? 'selected' : '';
                  echo "<option value=\"$val\" $selected>$val</option>";
              }
              ?>
              </select>
          </div>
          <div class="select">
              <select id="card-expiration-year" name="card-expiration-year" required>
              <option value="">Year</option>
              <?php
              $y=date('Y');
              $selected_year = date('Y'); // 預設選擇當前年份
              for ($i = 0; $i <= 20; $i++) {
                $y++;
                  $selected = ($y == $selected_year) ? 'selected' : '';
                  echo "<option value=\"$y\" $selected>$y</option>";
              }
              ?>
              </select>
          </div>
      </fieldset>
      <fieldset class="fieldset-ccv">
          <label for="card-ccv">CCV</label>
          <input type="text" id="card-ccv" name="card-ccv" maxlength="3" placeholder="000" required />
      </fieldset>
      <button class="btn" type="submit"><i class="fa fa-lock"></i> Submit</button>
  </form>
  </div>
</body>
</html>

<script>
// 當畫面載入後，設定卡號輸入欄的自動跳轉
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.input-cart-number'); // 選全部卡號小格子
    inputs.forEach((input, idx) => {
        input.addEventListener('input', function() {
            // 如果輸入到4個字，就跳到下一個 input
            if (this.value.length === 4 && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }
        });

        input.addEventListener('keydown', function(e) {
            // 如果按 backspace 且目前是空的，跳回上一格
            if (e.key === 'Backspace' && this.value.length === 0 && idx > 0) {
                inputs[idx - 1].focus();
            }
        });
    });
});
</script>
