<?php
// 引入資料庫連線
// include('connect.php');
// db.php
$host = "localhost"; // 資料庫主機
$username = "root";  // 資料庫使用者
$password = "";      // 資料庫密碼
$dbname = "junglebite"; // 資料庫名稱

// 建立與資料庫的連線
$conn = new mysqli($host, $username, $password, $dbname);

// 檢查連線是否成功
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$cardName = $_GET['cardName'] ?? ''; // 從 URL 拿到 cardName
$id = $_GET['id'] ?? ''; // 從 URL 拿到 cardName
$role = $_GET['role'] ?? ''; // 從 URL 拿到 cardName



// 如果有帶 cardName，就撈這張卡的資料
if ($cardName) {
    $sql = "SELECT * FROM card WHERE cardName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cardName);
    $stmt->execute();
    $result = $stmt->get_result();
    $card = $result->fetch_assoc();

    $number = str_split($card['cardNumber'] ?? '', 4);
}

// 檢查是否有表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得表單資料
    $cardHolder = $_POST['card-holder'];
    $cardName = $_POST['card-name']; // 不改
    $cardNumber = $_POST['card-number'] . $_POST['card-number-1'] . $_POST['card-number-2'] . $_POST['card-number-3'];
    $cardType = $_POST['card-type'];
    $cvv = $_POST['card-ccv'];
    $expirationDate = $_POST['card-expiration-year'] . '-' . $_POST['card-expiration-month'] . '-01';
    // $selected_month = isset($card['card-expiration-month']) ? $card['card-expiration-month'] : '';
    // $selected_year = isset($card['card-expiration-year']) ? $card['card-expiration-year'] : '';
    // $date = DateTime::createFromFormat('Y-m-d', $card['expirationDate']);
    // $selected_month = $date->format('m'); // '04'
    // $selected_year = $date->format('Y');  // '2025'

    // UPDATE card
    $sql = "UPDATE card 
            SET cardHolder = ?, cardNumber = ?, cardType = ?, cvv = ?, expirationDate = ?
            WHERE cardName = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssss", $cardHolder, $cardNumber, $cardType, $cvv, $expirationDate, $cardName);

        if ($stmt->execute()) {
            header("Location: ./c_wallet.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
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
    <title>Edit Card</title>
</head>
<body>
<div class="checkout">
  <div id="top_word" style="display: flex;flex-direction: row;">
    <img src="./image/credit-card.png" alt="credit card icon" width="35" height="35">
    <h1 style="margin:0;margin-left:10px">Edit Card</h1>
  </div>
    <!-- <div class="credit-card-box">
      <div class="flip">
        <div class="front">
          <div class="chip"></div>
          <div class="logo">
            <svg version="1.1" id="visa" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                 width="47.834px" height="47.834px" viewBox="0 0 47.834 47.834" style="enable-background:new 0 0 47.834 47.834;">
              <g>
                <g>
                  <path d="M44.688,16.814h-3.004c-0.933,0-1.627,0.254-2.037,1.184l-5.773,13.074h4.083c0,0,0.666-1.758,0.817-2.143
                           c0.447,0,4.414,0.006,4.979,0.006c0.116,0.498,0.474,2.137,0.474,2.137h3.607L44.688,16.814z M39.893,26.01
                           c0.32-0.819,1.549-3.987,1.549-3.987c-0.021,0.039,0.317-0.825,0.518-1.362l0.262,1.23c0,0,0.745,3.406,0.901,4.119H39.893z
                           M34.146,26.404c-0.028,2.963-2.684,4.875-6.771,4.875c-1.743-0.018-3.422-0.361-4.332-0.76l0.547-3.193l0.501,0.228
                           c1.277,0.532,2.104,0.747,3.661,0.747c1.117,0,2.313-0.438,2.325-1.393c0.007-0.625-0.501-1.07-2.016-1.77
                           c-1.476-0.683-3.43-1.827-3.405-3.876c0.021-2.773,2.729-4.708,6.571-4.708c1.506,0,2.713,0.31,3.483,0.599l-0.526,3.092
                           l-0.351-0.165c-0.716-0.288-1.638-0.566-2.91-0.546c-1.522,0-2.228,0.634-2.228,1.227c-0.008,0.668,0.824,1.108,2.184,1.77
                           C33.126,23.546,34.163,24.783,34.146,26.404z M0,16.962l0.05-0.286h6.028c0.813,0.031,1.468,0.29,1.694,1.159l1.311,6.304
                           C7.795,20.842,4.691,18.099,0,16.962z M17.581,16.812l-6.123,14.239l-4.114,0.007L3.862,19.161
                           c2.503,1.602,4.635,4.144,5.386,5.914l0.406,1.469l3.808-9.729L17.581,16.812L17.581,16.812z M19.153,16.8h3.89L20.61,31.066
                           h-3.888L19.153,16.8z"/>
                </g>
              </g>
            </svg>
          </div>
          <div class="number"></div>
          <div class="card-holder">
            <label>Card holder</label>
            <div></div>
          </div>
          <div class="card-expiration-date">
            <label>Expires</label>
            <div></div>
          </div>
        </div>
        <div class="back">
          <div class="strip"></div>
          <div class="logo">
            <svg version="1.1" id="visa" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                 width="47.834px" height="47.834px" viewBox="0 0 47.834 47.834" style="enable-background:new 0 0 47.834 47.834;">
              <g>
                <g>
                  <path d="M44.688,16.814h-3.004c-0.933,0-1.627,0.254-2.037,1.184l-5.773,13.074h4.083c0,0,0.666-1.758,0.817-2.143
                           c0.447,0,4.414,0.006,4.979,0.006c0.116,0.498,0.474,2.137,0.474,2.137h3.607L44.688,16.814z M39.893,26.01
                           c0.32-0.819,1.549-3.987,1.549-3.987c-0.021,0.039,0.317-0.825,0.518-1.362l0.262,1.23c0,0,0.745,3.406,0.901,4.119H39.893z
                           M34.146,26.404c-0.028,2.963-2.684,4.875-6.771,4.875c-1.743-0.018-3.422-0.361-4.332-0.76l0.547-3.193l0.501,0.228
                           c1.277,0.532,2.104,0.747,3.661,0.747c1.117,0,2.313-0.438,2.325-1.393c0.007-0.625-0.501-1.07-2.016-1.77
                           c-1.476-0.683-3.43-1.827-3.405-3.876c0.021-2.773,2.729-4.708,6.571-4.708c1.506,0,2.713,0.31,3.483,0.599l-0.526,3.092
                           l-0.351-0.165c-0.716-0.288-1.638-0.566-2.91-0.546c-1.522,0-2.228,0.634-2.228,1.227c-0.008,0.668,0.824,1.108,2.184,1.77
                           C33.126,23.546,34.163,24.783,34.146,26.404z M0,16.962l0.05-0.286h6.028c0.813,0.031,1.468,0.29,1.694,1.159l1.311,6.304
                           C7.795,20.842,4.691,18.099,0,16.962z M17.581,16.812l-6.123,14.239l-4.114,0.007L3.862,19.161
                           c2.503,1.602,4.635,4.144,5.386,5.914l0.406,1.469l3.808-9.729L17.581,16.812L17.581,16.812z M19.153,16.8h3.89L20.61,31.066
                           h-3.888L19.153,16.8z"/>
                </g>
              </g>
            </svg>
  
          </div>
          <div class="ccv">
            <label>CCV</label>
            <div></div>
          </div>
        </div>
      </div>
    </div> -->
    <style>label{font-size:13px;}</style>
    <form class="form" autocomplete="off" novalidate method="POST">
      <fieldset>
          <label for="card-name">Card Name</label>
          <input type="text" id="card-name" name="card-name" placeholder="<?php echo htmlspecialchars($card['cardName'] ?? '', ENT_QUOTES); ?>"
               value="<?php echo htmlspecialchars($card['cardName'] ?? '', ENT_QUOTES); ?>"
               required />
      </fieldset>
      <fieldset>
          <label for="card-number">Card Number</label>
          <input type="num" id="card-number" class="input-cart-number" name="card-number" class="input-cart-number" maxlength="4" placeholder="<?php echo $number[0] ?? ''; ?>"
          value="<?php echo $number[0] ?? ''; ?>" required />
          <input type="num" id="card-number-1" class="input-cart-number" name="card-number-1" class="input-cart-number" maxlength="4" placeholder="<?php echo $number[1] ?? ''; ?>"
          value="<?php echo $number[1] ?? ''; ?>" required />
          <input type="num" id="card-number-2" class="input-cart-number" name="card-number-2" class="input-cart-number" maxlength="4" placeholder="<?php echo $number[2] ?? ''; ?>"
          value="<?php echo $number[2] ?? ''; ?>" required />
          <input type="num" id="card-number-3" class="input-cart-number" name="card-number-3" class="input-cart-number" maxlength="4" placeholder="<?php echo $number[3] ?? ''; ?>"
          value="<?php echo $number[3] ?? ''; ?>" required />
      </fieldset>
      <fieldset>
          <label for="card-holder">Card Holder</label>
          <input type="text" id="card-holder" name="card-holder" placeholder="holder's name" value="<?php echo htmlspecialchars($card['cardHolder'] ?? ''); ?>" value="<?php echo htmlspecialchars($card['cardHolder'] ?? ''); ?>" required />
      </fieldset>
      <fieldset style="display: flex;align-items: center;">
        <label style="width:67px;display: flex;align-items: center;">Type：</label>
        <label style="width:78px;display: flex; align-items: center;">
          <input type="radio" name="card-type" value="visa"   <?php if (($card['cardType'] ?? '') == 'visa') echo 'checked'; ?> style="height:15px;width:15px;"> Visa
        </label>
        <label style="width: 150px;px;display: flex; align-items: center;">
          <input type="radio" name="card-type" value="MasterCard"   <?php if (($card['cardType'] ?? '') == 'MasterCard') echo 'checked'; ?> style="height:15px;width:15px;"> MasterCard
        </label>
        <label style="width:50px;display: flex;align-items: center;">
        <input type="radio" name="card-type" value="JSB"   <?php if (($card['cardType'] ?? '') == 'JSB') echo 'checked'; ?> style="height:15px;width:15px;"> JSB
      </label>
      </fieldset>
      <fieldset class="fieldset-expiration">
          <label for="card-expiration-month">Expiration date</label>
          <div class="select">
              <select id="card-expiration-month" name="card-expiration-month" required>
              <option>Month</option>
    <?php
    for ($m = 1; $m <= 12; $m++) {
        $month = str_pad($m, 2, '0', STR_PAD_LEFT); // 轉成 01–12
        $selected = ($month == $selected_month) ? 'selected' : '';
        echo "<option value=\"$month\" $selected>$month</option>";
    }
    ?>
              </select>
          </div>
          <div class="select">
              <select id="card-expiration-year" name="card-expiration-year" required>
              <option>Year</option>
    <?php
    for ($y = 2021; $y <= 2032; $y++) {
        $selected = ($y == $selected_year) ? 'selected' : '';
        echo "<option value=\"$y\" $selected>$y</option>";
    }
    ?>
              </select>
          </div>
      </fieldset>
      <fieldset class="fieldset-ccv">
          <label for="card-ccv">CCV</label>
          <input type="text" id="card-ccv" name="card-ccv" maxlength="3" placeholder="cvv" value="<?php echo htmlspecialchars($card['cvv'] ?? ''); ?>"required />
      </fieldset>
      <button class="btn" type="submit"><i class="fa fa-lock"></i> Submit</button>
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

    // 月份選好後自動跳到年份
    const monthSelect = document.getElementById('card-expiration-month');
    const yearSelect = document.getElementById('card-expiration-year');

    if (monthSelect && yearSelect) {
        monthSelect.addEventListener('change', function() {
            if (this.value !== '') {
                yearSelect.focus();
            }
        });
    }
});
</script>



