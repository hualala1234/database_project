<?php
session_start();
include('../dbh.php');
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}
// 假設外送員登入後將 did 存入 session
$did = $_SESSION['did'] ?? null;
if (!$did) {
    die("未登入外送員帳號。");
}


// 👉 若點擊「接單」按鈕
if (isset($_GET['accept'])) {
    $tranId = intval($_GET['accept']);
    $stmt = $conn->prepare("UPDATE Transaction SET orderStatus = 'takeaway' WHERE tranId = ?");
    $stmt->bind_param("i", $tranId);
    $stmt->execute();
    $stmt->close();

    header("Location: delivery_index.php");
    exit();
}


// 👉 處理「略過」按鈕
if (isset($_GET['skip'])) {
    $tranId = intval($_GET['skip']);

    $check = $conn->prepare("SELECT 1 FROM DeliverySkip WHERE did = ? AND tranId = ?");
    $check->bind_param("ii", $did, $tranId);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO DeliverySkip (did, tranId) VALUES (?, ?)");
        $insert->bind_param("ii", $did, $tranId);
        $insert->execute();
        $insert->close();
    }
    $check->close();

    header("Location: delivery_index.php");
    exit();
}


// 👉 接單功能
if (isset($_GET['tranId'])) {
    $tranId = $_GET['tranId'];
    $stmt = $conn->prepare("UPDATE Transaction SET orderStatus = 'takeaway' WHERE tranId = ?");
    $stmt->bind_param("i", $tranId);
    $stmt->execute();
    $stmt->close();
    header("Location: delivery_index.php");
    exit();
}

// 撈尚未被略過、可接的訂單
$sql = "
    SELECT t.tranId, t.cid, t.mid, t.totalPrice, t.address_text, t.tNote,
           c.cName AS customerName,
           m.mName AS merchantName
    FROM Transaction t
    JOIN customer c ON t.cid = c.cid
    JOIN merchant m ON t.mid = m.mid
    WHERE t.orderStatus = 'take'
      AND t.tranId NOT IN (
          SELECT tranId FROM DeliverySkip WHERE did = ?
      )
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $did);
$stmt->execute();
$result = $stmt->get_result();
$takeOrders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 撈已接單訂單（不過濾 did，是所有外送員的接單）
$sqlMaking = "
    SELECT t.tranId, t.cid, t.mid, t.totalPrice, t.address_text, t.tNote,
           c.cName AS customerName,
           m.mName AS merchantName
    FROM Transaction t
    JOIN customer c ON t.cid = c.cid
    JOIN merchant m ON t.mid = m.mid
    WHERE t.orderStatus = 'takeaway'
";
$resultMaking = $conn->query($sqlMaking);
$makingOrders = $resultMaking->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>外送員介面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .section { margin-top: 100px; }
    </style>
</head>
<body>

<!-- 導覽列 -->
<div class="d-flex justify-content-center gap-3 sticky-top bg-white py-3 border-bottom">
    <a href="#currentOrders" class="btn btn-primary">現有訂單</a>
    <a href="#acceptedOrders" class="btn btn-secondary">已接單</a>
    <a href="#statusToggle" class="btn btn-success">ON/OFFLINE</a>
</div>

<input type="hidden" id="statusState" value="OFFLINE">

<!-- 現有訂單 -->
<div id="currentOrders" class="section container">
    <h3>現有訂單</h3>
    <?php if (empty($takeOrders)): ?>
        <p class="text-muted">目前沒有可接的訂單。</p>
    <?php else: ?>
        <?php foreach ($takeOrders as $order): ?>
            <div class="card my-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <?= htmlspecialchars($order['merchantName']) ?> → <?= htmlspecialchars($order['customerName']) ?> 
                        總金額：$<?= htmlspecialchars($order['totalPrice']) ?>，送至 <?= htmlspecialchars($order['address_text']) ?>
                        <?php if (!empty($order['tNote'])): ?>
                            （備註：<?= htmlspecialchars($order['tNote']) ?>）
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success btn-sm" onclick="acceptOrder(<?= $order['tranId'] ?>)">接單</button>
                        <a href="delivery_index.php?skipId=<?= $order['tranId'] ?>" 
                           class="btn btn-outline-danger btn-sm" 
                           onclick="return confirm('確定要跳過這筆訂單嗎？');">跳過</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- 已接單 -->
<div id="acceptedOrders" class="section container">
    <h3>已接單</h3>
    <?php if (empty($makingOrders)): ?>
        <p class="text-muted">尚無接單紀錄。</p>
    <?php else: ?>
        <?php foreach ($makingOrders as $order): ?>
            <div class="card my-2">
                <div class="card-body">
                    <?= htmlspecialchars($order['merchantName']) ?> → <?= htmlspecialchars($order['customerName']) ?> 
                    總金額：$<?= htmlspecialchars($order['totalPrice']) ?>，送至 <?= htmlspecialchars($order['address_text']) ?>
                    <?php if (!empty($order['tNote'])): ?>
                        （備註：<?= htmlspecialchars($order['tNote']) ?>）
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ON/OFFLINE -->
<div id="statusToggle" class="section container">
    <h3>狀態切換</h3>
    <button class="btn btn-outline-primary" onclick="toggleStatus(this)">目前狀態：OFFLINE</button>
</div>

<script>
function toggleStatus(button) {
    const statusInput = document.getElementById('statusState');
    if (button.textContent.includes('OFFLINE')) {
        button.textContent = '目前狀態：ONLINE';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-outline-success');
        statusInput.value = 'ONLINE';
    } else {
        button.textContent = '目前狀態：OFFLINE';
        button.classList.remove('btn-outline-success');
        button.classList.add('btn-outline-primary');
        statusInput.value = 'OFFLINE';
    }
}

function acceptOrder(tranId) {
    const status = document.getElementById('statusState').value;
    if (status !== 'ONLINE') {
        alert('請先切換為 ONLINE 才能接單！');
        return;
    }
    window.location.href = `delivery_index.php?tranId=${tranId}`;
}

function skipOrder(tranId) {
    if (confirm('確定略過這筆訂單？')) {
        window.location.href = `delivery_index.php?skip=${tranId}`;
    }
}
</script>

</body>
</html>