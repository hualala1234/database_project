<?php
include('connect.php');
$id = $_GET['cid'] ?? null;
if (!$id) die("未提供 cid");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="./c_record.css" />
  <title>Customer Record</title>
</head>
<body>
  <div class="block1">
    <img id="wallet" src="./image/folder.png" alt="wallet icon" width="30" height="30" />
    <h1>Transaction Records</h1>
  </div>

  <div id="container">
    <!-- 左邊 -->
    <div id="menu">
      <div class="block2">
        <div id="add_card" style="margin-top: 10px;">
          <img src="./image/credit-card.png" alt="add button" width="25" height="25" />
          <h2 class="word" style="font-size: 23px; margin: 10px 0;">My cards</h2>
        </div>

        <!-- 錢包餘額 -->
        <div class="balance card c_title" data-card-id="balance">
          <span>Current balance：</span>
          <p id="balance">
            <?php
              $result = $conn->query("SELECT balance FROM wallet WHERE cid = $id");
              if ($result && $row = $result->fetch_assoc()) {
                echo htmlspecialchars($row['balance']) . ' NTD';
            } else {
                echo '0 NTD';
            }
            ?>
          </p>
        </div>

        <!-- 卡片列表 -->
        <?php
        $cardResult = $conn->query("SELECT * FROM card WHERE cid = $id");
        while ($card = $cardResult->fetch_assoc()) {
            $cardId = $card['cardName'];
            echo '<div class="cards">
                <div class="card" data-card-id="' . htmlspecialchars($cardId) . '">
                    <p class="c_title">' . htmlspecialchars($cardId) . '</p>
                    <div class="info">
                        <p>Card Number: ' . htmlspecialchars($card['cardNumber']) . '</p>
                        <p>Expiration Date: ' . htmlspecialchars($card['expirationDate']) . '</p>
                    </div>
                </div>
            </div>';
        }
        ?>
      </div>
    </div>

    <!-- 右邊 -->
    <div id="right_container">
      <h2 class="word" style="text-align:center;">History Transactions</h2>
      <div id="transaction_list">

        <!-- 全部交易 -->
        <div id="transaction_all">
          <table style="width:100%; border-collapse:collapse;">
            <thead style="font-size: 22px;">
              <tr>
                <th>Date</th><th>Merchant</th><th>Spend</th><th>PaymentMethod</th>
              </tr>
            </thead>
            <tbody style="font-size: 20px;">
              <?php
              $sql = "
                SELECT t.transactionTime, t.totalPrice, t.cardName, m.mName
                FROM transaction t
                LEFT JOIN merchant m ON t.mid = m.mid
                WHERE t.cid = ?
                ORDER BY t.transactionTime DESC
              ";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("i", $id);
              $stmt->execute();
              $result = $stmt->get_result();
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['transactionTime']}</td>
                          <td>{$row['mName']}</td>
                          <td>{$row['totalPrice']}</td>
                          <td>{$row['cardName']}</td>
                        </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- 錢包交易 -->
        <div class="transaction_group" id="transaction_balance" style="display:none;">
          <h3>Transactions for Wallet Balance</h3>
          <table>
            <thead style="font-size: 22px;">
              <tr><th>Date</th><th>Merchant</th><th>Spend</th></tr>
            </thead>
            <tbody style="font-size: 20px;">
              <?php
              $walletSql = "SELECT t.transactionTime, t.totalPrice, m.mName
                            FROM transaction t
                            LEFT JOIN merchant m ON t.mid = m.mid
                            WHERE cid = $id AND paymentMethod = 'walletBalance'";
              $walletResult = $conn->query($walletSql);
              while ($row = $walletResult->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['transactionTime']}</td>
                          <td>{$row['mName']}</td>
                          <td>{$row['totalPrice']}</td>
                        </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

        <!-- 每張卡片交易 -->
        <?php
        $cardResult = $conn->query("SELECT DISTINCT cardName FROM card WHERE cid = $id");
        while ($card = $cardResult->fetch_assoc()) {
            $cardId = $card['cardName'];
            echo '<div class="transaction_group" id="transaction_' . $cardId . '" style="display:none;">
                    <h3>Transactions for Card: ' . htmlspecialchars($cardId) . '</h3>
                    <table>
                      <thead style="font-size: 22px;">
                        <tr><th>Date</th><th>Merchant</th><th>Spend</th></tr>
                      </thead>
                      <tbody style="font-size: 20px;">';
            $t_sql = "SELECT t.transactionTime, t.totalPrice, m.mName
                      FROM transaction t
                      INNER JOIN merchant m ON t.mid = m.mid
                      WHERE t.cardName = '$cardId' AND t.cid = $id
                      ORDER BY t.transactionTime DESC";
            $t_result = $conn->query($t_sql);
            while ($t = $t_result->fetch_assoc()) {
                echo "<tr>
                        <td>{$t['transactionTime']}</td>
                        <td>{$t['mName']}</td>
                        <td>{$t['totalPrice']}</td>
                      </tr>";
            }
            echo '</tbody></table></div>';
        }
        ?>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", function () {
      let currentCardId = null;

      document.querySelectorAll('.card').forEach(function(card) {
          card.addEventListener('click', function () {
              const cardId = this.getAttribute('data-card-id');

              if (currentCardId === cardId) {
                  document.getElementById('transaction_all').style.display = 'block';
                  document.querySelectorAll('.transaction_group').forEach(group => group.style.display = 'none');
                  document.querySelectorAll('.card').forEach(c => c.classList.remove('clicked'));
                  currentCardId = null;
              } else {
                  document.getElementById('transaction_all').style.display = 'none';
                  document.querySelectorAll('.transaction_group').forEach(group => group.style.display = 'none');
                  const target = document.getElementById('transaction_' + cardId);
                  if (target) target.style.display = 'block';
                  document.querySelectorAll('.card').forEach(c => c.classList.remove('clicked'));
                  this.classList.add('clicked');
                  currentCardId = cardId;
              }
          });
      });
  });
  </script>
</body>
</html>
