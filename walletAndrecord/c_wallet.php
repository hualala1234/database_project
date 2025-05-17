<?php
include('../dbh.php');
session_start();
$id = $_SESSION['cid'] ?? 0;
$role = $_GET['role'] ?? null;

// 查詢餘額
$balance = 0;
if ($id) {
    $stmt = $conn->prepare("SELECT balance FROM wallet WHERE cid = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $balance = $res->fetch_assoc()['balance'] ?? 0;
}
// $query = "SELECT card_number FROM cbank WHERE cid = ?";
// $cardStmt = $conn->prepare($query);

// if (!$cardStmt) {
//     die("Prepare failed: " . $conn->error);  // 顯示錯誤細節
// }

$cards = [];
$cardStmt = $conn->prepare("SELECT bankCode, accountNumber FROM cbank WHERE cid = ?");
$cardStmt->bind_param("i", $id);
$cardStmt->execute();
$cardRes = $cardStmt->get_result();
while ($c = $cardRes->fetch_assoc()) {
    $cards[] = [
        'bankCode' => $c['bankCode'],
        'accountNumber' => $c['accountNumber']
    ];
}

// 查詢交易紀錄
$records = [];
$stmt = $conn->prepare("SELECT * FROM wallet_record WHERE cid = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $records[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./c_wallet.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 
    <script src="./c_wallet.js" defer></script>
    <title>Customer Wallet</title>
</head>
<body>
    <div id="out_block">
        <div class="block1">
            <a href="../customer/index.php" class="logo" style="text-decoration:none;"><h1>Junglebite</h1></a>
            <img id="wallet" src="./image/wallet.png" alt="wallet icon" width="30" height="30">
            <h1>Wallet</h1><span style="font-size: 22px; margin: 0px; margin-left: 30px;">Welcome to your wallet!</span>
        </div>
        <div class="balance" data-card-id="balance" style="margin-top: 30px;">
            <img id="piggy" src="./image/piggy.png" alt="piggy icon" width="40px" height="40px">
            <p class="word">Current balance：</p>
            <p id="balance"><?= $balance ?>NTD</p>
            <p id="toggle-transfer" style="cursor: pointer; color: blue; font-weight: bold;margin-left: 30px;margin-top: 20px;">➕ 儲值 / 提款</p>
            <div id="transfer-form-section" style="display: none; margin-top: 1rem;">
                <h2 style="text-align: center; color: #333; margin-bottom: 20px;">儲值</h2>
                <form id="deposit-form" style="margin-bottom: 30px;">
                    <label for="saved-account-deposit">選擇帳號：</label>
                    <select id="saved-account-deposit" style="width: 100%; padding: 5px;">
                        <option value="">➕ 新增帳戶</option>
                        <?php foreach ($cards as $card): ?>
                            <option value="<?= $card['bankCode'] ?>-<?= $card['accountNumber'] ?>">
                            <?= $card['bankCode'] ?> - <?= $card['accountNumber'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="new-deposit-account" style="display: none; margin-top: 10px;">
                        <input type="text" id="deposit-bankCode" placeholder="銀行代碼" style="width: 80px; padding: 5px;">
                        <input type="text" id="deposit-accountNumber" placeholder="帳號" style="padding: 5px; width: 180px;">
                        <p id="formatted-deposit-account" style="font-weight: bold; color: #146E57; margin-top: 5px;"></p>
                    </div>
                    <input type="number" id="deposit-amount" placeholder="金額" style="width: 95%; margin-top: 10px; padding: 5px;">
                    <button type="submit" style="margin-top: 30px; padding: 8px 16px; background-color: #146E57; color: white; border: none; border-radius: 5px; cursor: pointer;">儲值</button>
                </form>

                <h2 class="mt-4" style="text-align: center; color: #333; margin-bottom: 20px;">提款</h2>
                <form id="withdraw-form">
                    <label for="saved-account-withdraw">選擇帳號：</label>
                    <select id="saved-account-withdraw" style="width: 100%; padding: 5px;">
                        <option value="">➕ 新增帳戶</option>
                        <?php foreach ($cards as $card): ?>
                            <option value="<?= $card['bankCode'] ?>-<?= $card['accountNumber'] ?>">
                            <?= $card['bankCode'] ?> - <?= $card['accountNumber'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="new-withdraw-account" style="display: none; margin-top: 10px;">
                        <input type="text" id="withdraw-bankCode" placeholder="銀行代碼" style="width: 80px; padding: 5px;">
                        <input type="text" id="withdraw-accountNumber" placeholder="帳號" style="padding: 5px; width: 180px;">
                        <p id="formatted-withdraw-account" style="font-weight: bold; color: #146E57; margin-top: 5px;"></p>
                    </div>
                    <input type="number" id="withdraw-amount" placeholder="金額" style="width: 95%; margin-top: 10px; padding: 5px;">
                    <button type="submit" style="margin-top: 30px; padding: 8px 16px; background-color: #ad311f; color: white; border: none; border-radius: 5px; cursor: pointer;">提款</button>
                </form>
            </div>
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
        
        $sql = "SELECT * FROM card WHERE cid=$id"; // 查詢資料表
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
    <div id="luggage-container" style="display: none;">
        <img id="luggage" src="./image/luggage.png" alt="luggage" />
    </div>

    <div id="money-rain-container"></div>
    <div id="money-fly-container"></div>
    

<script>
function limitInputDigits(inputElem, maxDigits) {
    inputElem.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, maxDigits);
    });
}
function updateFormattedAccount(bankInput, accInput, outputElem) {
    function update() {
        const bank = bankInput.value;
        const acc = accInput.value;
        outputElem.textContent = (bank && acc) ? `帳號格式：${bank}-${acc}` : '';
    }
    bankInput.addEventListener('input', update);
    accInput.addEventListener('input', update);
}
function triggerMoneyRain() {
    const container = document.getElementById("money-rain-container");
    for (let i = 0; i < 20; i++) {
        const money = document.createElement("img");
        money.src = "./image/dollar.png";
        money.className = "money";
        money.style.left = `${Math.random() * 100}%`;
        money.style.animationDelay = `${Math.random()}s`;
        container.appendChild(money);
        setTimeout(() => money.remove(), 2000);
    }
}
function triggerMoneyFlyUp() {
    const container = document.getElementById("money-fly-container");

    for (let i = 0; i < 20; i++) {
        const money = document.createElement("img");
        money.src = "./image/flying-money.png";
        money.className = "money-up";

        // 隨機位置與延遲
        money.style.left = `${Math.random() * 100}%`;
        money.style.animationDelay = `${Math.random() * 0.5}s`;

        container.appendChild(money);

        // 2 秒後自動移除
        setTimeout(() => money.remove(), 2000);
    }
}



const toggleBtn = document.getElementById("toggle-transfer");
const transferSection = document.getElementById("transfer-form-section");
toggleBtn.addEventListener("click", () => {
    const isHidden = transferSection.style.display === "none" || transferSection.style.display === "";
    transferSection.style.display = isHidden ? "block" : "none";
    toggleBtn.textContent = isHidden ? "➖ 收起表單" : "➕ 儲值 / 提款";
});

const savedDepositSelect = document.getElementById('saved-account-deposit');
const newDepositFields = document.getElementById('new-deposit-account');
savedDepositSelect.addEventListener('change', () => {
    newDepositFields.style.display = savedDepositSelect.value === '' ? 'block' : 'none';
});

const savedWithdrawSelect = document.getElementById('saved-account-withdraw');
const newWithdrawFields = document.getElementById('new-withdraw-account');
savedWithdrawSelect.addEventListener('change', () => {
    newWithdrawFields.style.display = savedWithdrawSelect.value === '' ? 'block' : 'none';
});

if (savedDepositSelect.value === '') newDepositFields.style.display = 'block';
if (savedWithdrawSelect.value === '') newWithdrawFields.style.display = 'block';

limitInputDigits(document.getElementById('deposit-bankCode'), 3);
limitInputDigits(document.getElementById('deposit-accountNumber'), 12);
limitInputDigits(document.getElementById('withdraw-bankCode'), 3);
limitInputDigits(document.getElementById('withdraw-accountNumber'), 12);

updateFormattedAccount(
    document.getElementById('deposit-bankCode'),
    document.getElementById('deposit-accountNumber'),
    document.getElementById('formatted-deposit-account')
);
updateFormattedAccount(
    document.getElementById('withdraw-bankCode'),
    document.getElementById('withdraw-accountNumber'),
    document.getElementById('formatted-withdraw-account')
);

// 資料送出後觸發下錢雨
const depositForm = document.getElementById('deposit-form');
depositForm.addEventListener('submit', function (e) {
    e.preventDefault();
    let bankCode, accountNumber;
    if (savedDepositSelect.value === '') {
        bankCode = document.getElementById('deposit-bankCode').value;
        accountNumber = document.getElementById('deposit-accountNumber').value;
    } else {
        [bankCode, accountNumber] = savedDepositSelect.value.split('-');
    }
    const amount = parseInt(document.getElementById('deposit-amount').value);
    fetch('../walletAndrecord/deposit.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ bankCode, accountNumber, amount })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            triggerMoneyRain();
            setTimeout(() => location.reload(), 1500);
        }
    });
});
// ✅ 提款後觸發「錢飛走」動畫
const withdrawForm = document.getElementById('withdraw-form');
withdrawForm.addEventListener('submit', function (e) {
    e.preventDefault();

    let bankCode, accountNumber;
    const savedWithdrawSelect = document.getElementById('saved-account-withdraw');  // ✅ 正確抓提款下拉

    if (savedWithdrawSelect.value === '') {
        bankCode = document.getElementById('withdraw-bankCode').value;
        accountNumber = document.getElementById('withdraw-accountNumber').value;
    } else {
        [bankCode, accountNumber] = savedWithdrawSelect.value.split('-');
    }

    const amount = parseInt(document.getElementById('withdraw-amount').value);

    fetch('../walletAndrecord/withdraw.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ bankCode, accountNumber, amount })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            // triggerMoneyFlyUp();  // ✅ 呼叫錢飛走動畫
            // setTimeout(() => location.reload(), 1500);
            
            triggerMoneyFlyToLuggage();
            setTimeout(() => location.reload(), 1500);
        }
    });
});
function triggerMoneyFlyToLuggage() {
    const luggage = document.getElementById("luggage");
    const luggageBox = document.getElementById("luggage-container");
    luggageBox.style.display = 'block';

    const luggageRect = luggage.getBoundingClientRect();

    for (let i = 0; i < 15; i++) {
        const money = document.createElement("img");
        money.src = "./image/flying-money.png";
        money.className = "money-fly-up";

        const startX = Math.random() * window.innerWidth;
        const startY = window.innerHeight - 100;
        money.style.left = `${startX}px`;
        money.style.top = `${startY}px`;

        const targetX = luggageRect.left + luggageRect.width / 2 - 50; // 調整 X 軸位置
        const targetY = luggageRect.top + luggageRect.height / 2 - 50; // 調整 Y 軸位置

        const deltaX = targetX - startX;
        const deltaY = targetY - startY;

        // 播放動畫
        money.animate([
            { transform: `translate(0, 0) scale(1)`, opacity: 1 },
            { transform: `translate(${deltaX}px, ${deltaY}px) scale(0.3)`, opacity: 0 }
        ], {
            duration: 1500,
            easing: "ease-in"
        });

        document.body.appendChild(money);

        setTimeout(() => money.remove(), 1500);
    }

    // 行李箱隱藏
    setTimeout(() => {
        luggageBox.style.display = 'none';
    }, 1600);
}
function animateBalance(from, to) {
    const duration = 1000;
    const startTime = performance.now();
    const balanceElem = document.getElementById("balance");

    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const value = Math.floor(from + (to - from) * progress);
        balanceElem.textContent = `${value} NTD`;
        if (progress < 1) requestAnimationFrame(update);
    }

    requestAnimationFrame(update);
}
function showSparkle(x, y) {
    const sparkle = document.createElement("div");
    sparkle.className = "sparkle";
    sparkle.style.left = `${x}px`;
    sparkle.style.top = `${y}px`;
    document.body.appendChild(sparkle);
    setTimeout(() => sparkle.remove(), 600);
}





</script>
</body>
</html>