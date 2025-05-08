<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./c_wallet.css">
    <script src="./c_wallet.js"></script>
    <title>Customer Wallet</title>
</head>
<body>
    <div id="out_block">
        <div class="block1">
            <img id="wallet" src="./image/wallet.png" alt="wallet icon" width="30" height="30">
            <h1>Wallet</h1><span style="font-size: 22px; margin: 0px; margin-left: 30px;">Welcome to your wallet!</span>
        </div>
        <div class="balance" data-card-id="balance" style="margin-top: 30px;">
            <img id="piggy" src="./image/piggy.png" alt="piggy icon" width="40px" height="40px">
            <p class="word">Current balance：</p>
            <p id="balance">
            <?php
            include('connect.php');
            $id = $_GET['id'] ?? null;
            $role = $_GET['role'] ?? null;

            if ($id) {
                $sql = "SELECT * FROM wallet WHERE cid = $id";
                $result = $conn->query($sql);
            } else {
                echo '未提供用戶 ID';
            }
            if (!$result) {
                die("SQL Error: " . $conn->error);
            }

            if ($row = $result->fetch_assoc()) {
                echo $row['balance'];
            }
            ?>
            NTD</p>
        </div>
        <div class="mycard">
            <div id="add_card" style="margin-left: 10px;">
                <img src="./image/credit-card.png" alt="add button" width="30px"; height='30px'; style="margin-right: 10px;margin-top: 0px;">
                <h2 class="word" >My cards</h2>
                <img id="add" src="./image/add.png" alt="add button" width="25" height="25" style="margin-top: 0px;">
            </div>
            <div id="container">
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
    </div>
    
    <!-- 顯示卡片資訊 -->
    <div id="showCard" style="display: none;">
        <img id="closeshow" src="./image/cross_white.png" alt="close button" width="20" height="20">
        <?php
        include('connect.php');
        
        $sql = "SELECT * FROM card WHERE cid='1'"; // 查詢資料表
        $result = $conn->query($sql);
        
        if (!$result) {
            die("SQL Error: " . $conn->error);
        }
        
        while ($row = $result->fetch_assoc()) {
            // 確保每張卡片擁有唯一的 ID
            $cardId = $row['cardName']; 
            echo '<div class="card_detail" id="detail_card_' . $cardId . '" style="display: none;padding: 15px 0px 0px 120px;color:white;font-weight:900;">
            
                <p class="c_title">' . $row['cardName'] . '</p>
                <div class="info">
                    <p>Card Number: ' . $row['cardNumber'] . '</p>
                    <p>Card Type: ' . $row['cardType'] . '</p>
                    <p>Card Holder: ' . $row['cardHolder'] . '</p>
                    <p>Expiration Date: ' . $row['expirationDate'] . '</p>
                    <p>Card CVV: ' . $row['cvv'] . '</p>
                </div>
                <div class="change">
                <a href="editCard.php?cardName=' . urlencode($cardId) . '&id=' . urlencode($id) . '&role='. urlencode($role).'">
                    <img class="edit" src="./image/pencil_white.png" alt="edit button" width="25" height="25">
                </a>
                <a href="deleteCard.php?cardName=' . urlencode($cardId) . '&id=' . urlencode($id) . '&role='. urlencode($role).'" onclick="return confirm(\'Are you sure you want to delete this card?\');">
                    <img class="delete" src="./image/trash_white.png" alt="delete button" width="25" height="25">
                </a>
            </div>
            </div>';
        }
        ?>
    </div>

    <!-- <div id="editCard" style="display: block;">
        <img id="closeedit" src="./image/cross.png" alt="close button" width="15" height="15">
        <h3><input type="text" id="cardNameInput" placeholder="Card Name" /></h3>
        <p><input type="text" id="bankCodeInput" placeholder="Bank Code" /></p>
        <p><input type="text" id="cardNumberInput" placeholder="Card Number" /></p>
        <p><input type="text" id="cardTypeInput" placeholder="Card Type" /></p>
        <p><input type="text" id="expirationDateInput" placeholder="Expiration Date" /></p>
        <p><input type="text" id="cvvInput" placeholder="Card CVV" /></p>
        <input type="hidden" id="editCardId" value="">
        <button id="submitChanges">Submit</button>
    </div> -->

    <!-- <div class="footer">
        <p>© 2023 Your Company. All rights reserved.</p>
        <p>Privacy Policy | Terms of Service</p>
    </div> -->
</body>
</html>