<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./c_record.css">
    <script src="./c_record.js"></script>
    <title>Customer Record</title>
</head>
<body>
    <div class="block1">
        <img id="wallet" src="./image/folder.png" alt="wallet icon" width="30" height="30">
        <h1>Transaction Records</h1><span style="font-size: 22px; margin: 0px; margin-left: 30px;"></span>
    </div>
    <div id="container">
        <!-- 左半部 -->
        <div id="menu">
            <div class="block2">
                <div id="add_card" style="margin-top: 10px;">
                    <img src="./image/credit-card.png" alt="add button" width="25" height="25">
                    <h2 class="word" style="font-size: 23px;margin: 10px 0 10px 0">My cards</h2>
                    <!-- <img id="add" src="./image/add.png" alt="add button" width="25" height="25"> -->
                </div>

                <div class="balance card c_title" data-card-id="balance">
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
                    ?></p>
                </div>

                <?php

            $sql = "SELECT * FROM card WHERE cid = $id";
            $result = $conn->query($sql);
            
            
            while ($row = $result->fetch_assoc()) {
                // 動態生成每張卡片的唯一 ID
                $cardId = $row['cardName']; // 使用 'id' 作為資料庫中每張卡片的唯一識別符
                echo '<div class="cards">
                <div id="card_' . $cardId . '" class="card" data-card-id="' . $cardId . '">
                        <p class="c_title">' . $row['cardName'] . '</p>
                        <div class="info">
                            <p>Card Number: ' . $row['cardNumber'] . '</p>
                            <p>Expiration Date: ' . $row['expirationDate'] . '</p>
                        </div>
                    </div>
                </div>';
            }                
            ?>
            </div>
        </div>    

        <!-- 右半部 -->
        <div id="right_container">
            <!-- <div id="right_card_details"></div> -->
            <h2 class="word" style="text-align:center;">History Transactions</h2>
            <div id="transaction_list">
                <?php
                    // include('connect.php');
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
                <?php


                // 查詢所有用wallet交易的紀錄
                $walletBalance = "SELECT * FROM transaction t
                LEFT JOIN merchant m ON t.mid = m.mid
                WHERE cid = $id and paymentMethod = 'walletBalance'";
                $result = $conn->query($walletBalance);
                if ($result->num_rows > 0) {
                    echo '<div class="transaction_group" id="transaction_balance" style="display: none;">
                        <h3>Transactions for Wallet Balance</h3>';
                    echo '<table>
                            <thead style="font-size: 22px;">
                                <tr>
                                    <th>Date</th>
                                    <th>Merchant</th>
                                    <th>Spend</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 20px;">';
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                                <td>' . $row['transactionTime'] . '</td>
                                <td>' . $row['mName']. '</td>
                                <td>' . $row['totalPrice'] . '</td>
                            </tr>';
                    }
                    echo '</tbody></table>';
                    echo '</div>'; // end .transaction_group
                } else {
                    echo '<p>No transactions found for wallet balance.</p>';
                }

                // 查詢所有用卡片交易的紀錄
                $sql = "SELECT * FROM `card` NATURAL JOIN `transaction` WHERE cid = $id";
                $cardResult = $conn->query($sql);

                while ($card = $cardResult->fetch_assoc()) {
                    $cardId = $card['cardName'];
                    // echo "<p>".$cardId."</p>";
                    echo '<div class="transaction_group" id="transaction_' . $cardId . '" style="display: none;">
                        <h3>Transactions for Card: ' . $card['cardNumber'] . '</h3>';

                    $t_sql = "SELECT t.transactionTime, t.totalPrice m.mName
                    FROM `transaction` t 
                    INNER JOIN `card` c ON t.cardName = c.cardName 
                    INNER JOIN `merchant` m 
                    WHERE c.cardName = '$cardId' 
                    ORDER BY t.transactionTIme DESC";

                    $t_result = $conn->query($t_sql) or die("SQL Error: " . $conn->error . " | SQL: " . $t_sql);

                    if ($t_result->num_rows > 0) {
                        echo '<table>
                                <thead style="font-size: 22px;">
                                    <tr>
                                        <th>Date</th>
                                        <th>Merchant</th>
                                        <th>Spend</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 20px;">';
                        while ($t = $t_result->fetch_assoc()) {
                            echo '<tr>
                                    <td>' . $t['transactionTime'] . '</td>
                                    <td>' . $row['mName']. '</td>
                                    <td>' . $t['totalPrice'] . '</td>
                                </tr>';
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<p>No transactions found.</p>';
                    }

                    echo '</div>'; // end .transaction_group
                }
                ?>
            </div>
        </div>
    </div>
<!-- 
    <div class="footer">
        <p>© 2023 Your Company. All rights reserved.</p>
        <p>Privacy Policy | Terms of Service</p>
    </div> -->
</body>
</html>