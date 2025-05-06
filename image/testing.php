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
// 檢查是否有表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得表單資料
    $cardHolder = $_POST['card-holder'];
    $cardName = $_POST['card-name'];
    $cardNumber = $_POST['card-number'] . $_POST['card-number-1'] . $_POST['card-number-2'] . $_POST['card-number-3']; // 合併卡號
    $cardType = $_POST['card-type'];
    $cvv = $_POST['card-ccv'];
    $expirationDate = $_POST['card-expiration-month'] . '-' . $_POST['card-expiration-year']; // 以 yyyy-mm 格式儲存

    // 準備 SQL 查詢語句來插入資料
    $sql = "INSERT INTO card (cardHolder, cardName, cardNumber, cardType, cvv, expirationDate) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // 準備語句並綁定參數
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssss", $cardHolder, $cardName, $cardNumber, $cardType, $cvv, $expirationDate);

        // 執行插入操作
        if ($stmt->execute()) {
            // echo "Card added successfully!";
            // 資料新增成功後導向回 c_wallet.php
            header("Location: ../c_wallet.php");
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
    <link rel="stylesheet" href="./testing.css">
    <script src="./testing.js"></script>
    <title>Add Card</title>
</head>
<body>
<div class="checkout">
  <div id="top_word" style="display: flex;flex-direction: row;">
    <img src="./credit-card.png" alt="credit card icon" width="35" height="35">
    <h1 style="margin:0;margin-left:10px">Add Card</h1>
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
        <input type="text" id="card-name" name="card-name" required />
    </fieldset>
    <fieldset>
        <label for="card-number">Card Number</label>
        <input type="num" id="card-number" name="card-number" class="input-cart-number" maxlength="4" required />
        <input type="num" id="card-number-1" name="card-number-1" class="input-cart-number" maxlength="4" required />
        <input type="num" id="card-number-2" name="card-number-2" class="input-cart-number" maxlength="4" required />
        <input type="num" id="card-number-3" name="card-number-3" class="input-cart-number" maxlength="4" required />
    </fieldset>
    <fieldset>
        <label for="card-holder">Card Holder</label>
        <input type="text" id="card-holder" name="card-holder" required />
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
                <option></option>
                <option>01</option>
                <option>02</option>
                <option>03</option>
                <option>04</option>
                <option>05</option>
                <option>06</option>
                <option>07</option>
                <option>08</option>
                <option>09</option>
                <option>10</option>
                <option>11</option>
                <option>12</option>
            </select>
        </div>
        <div class="select">
            <select id="card-expiration-year" name="card-expiration-year" required>
                <option></option>
                <option>2021</option>
                <option>2022</option>
                <option>2023</option>
                <option>2024</option>
                <option>2025</option>
                <option>2026</option>
                <option>2029</option>
                <option>2030</option>
                <option>2031</option>
                <option>2032</option>
            </select>
        </div>
    </fieldset>
    <fieldset class="fieldset-ccv">
        <label for="card-ccv">CCV</label>
        <input type="text" id="card-ccv" name="card-ccv" maxlength="3" required />
    </fieldset>
    <button class="btn" type="submit"><i class="fa fa-lock"></i> Submit</button>
</form>
  </div>
</body>
</html>
<body>
    <div class="block1">
        <img id="wallet" src="./image/folder.png" alt="wallet icon" width="30" height="30">
        <h1>Transaction Records</h1>
    </div>
    <div id="container">
        <!-- 左半部 -->
        <div id="menu">
            <div class="block2">
                <div id="add_card" style="margin-top: 10px;">
                    <img src="./image/credit-card.png" alt="add button" width="25" height="25">
                    <h2 class="word" style="font-size: 23px;margin: 10px 0 10px 0">My cards</h2>
                </div>

                <div class="balance card c_title" data-card-id="walletBalance">
                    <span>Current balance：</span>
                    <p id="balance">
                    <?php
                    include('connect.php');
                    $id = $_GET['id'] ?? null;

                    if ($id) {
                        $sql = "SELECT * FROM wallet WHERE cid = $id";
                        $result = $conn->query($sql);
                        if (!$result) die("SQL Error: " . $conn->error);
                        if ($row = $result->fetch_assoc()) {
                            echo htmlspecialchars($row['balance']) . ' NTD';
                        }
                    } else {
                        echo '未提供 ID';
                    }
                    ?>
                    </p>
                </div>

                <?php
                $sql = "SELECT * FROM card WHERE cid = $id";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    $cardId = $row['cardName'];
                    echo '<div class="cards">
                            <div class="card" data-card-id="' . htmlspecialchars($cardId) . '">
                                <p class="c_title">' . htmlspecialchars($row['cardName']) . '</p>
                                <div class="info">
                                    <p>Card Number: ' . htmlspecialchars($row['cardNumber']) . '</p>
                                    <p>Expiration Date: ' . htmlspecialchars($row['expirationDate']) . '</p>
                                </div>
                            </div>
                          </div>';
                }
                ?>
            </div>
        </div>

        <!-- 右半部 -->
        <div id="right_container">
            <h2 class="word" style="text-align:center;">History Transactions</h2>
            <div id="transaction_list">
                <?php
                $sql = "
                    SELECT t.transactionTime, t.totalPrice, t.paymentMethod, m.mName
                    FROM transaction t
                    LEFT JOIN merchant m ON t.mid = m.mid
                    WHERE t.cid = ?
                    ORDER BY t.transactionTime DESC
                ";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();

                echo '<div id="transaction_all"><table id="transaction_table" style="width:100%; border-collapse:collapse;">
                        <thead style="font-size: 22px;">
                            <tr>
                                <th>Date</th>
                                <th>Merchant</th>
                                <th>Spend</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>';

                while ($row = $result->fetch_assoc()) {
                    $method = htmlspecialchars($row['paymentMethod']);
                    echo '<tr data-method="' . $method . '">
                            <td>' . htmlspecialchars($row['transactionTime']) . '</td>
                            <td>' . htmlspecialchars($row['mName']) . '</td>
                            <td>' . htmlspecialchars($row['totalPrice']) . '</td>
                            <td>' . $method . '</td>
                        </tr>';
                }

                echo '</tbody></table>';
                ?>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('#transaction_table tbody tr');
        const cardElements = document.querySelectorAll('.card');

        function showAllRows() {
            rows.forEach(row => row.style.display = '');
        }

        cardElements.forEach(card => {
            card.addEventListener('click', function () {
                const cardId = this.getAttribute('data-card-id');
                rows.forEach(row => {
                    const method = row.getAttribute('data-method');
                    row.style.display = (method === cardId) ? '' : 'none';
                });
            });
        });

        showAllRows(); // 預設顯示所有交易
    });
    </script>
</body>
