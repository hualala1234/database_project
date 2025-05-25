<?php
include('connect.php'); // 連線資料庫
session_start();
$id = $_SESSION['mid'] ?? NULL;
// $role = $_SESSION['role'] ?? null;
// 抓取 URL 中的 id 和 role
// $id = $_GET['id'] ?? null;
$role = $_GET['role'] ?? null;
$id = $_GET['id'];
$role = $_GET['role'];

$user = null;
$wallet = null;

switch ($role) {
    case 'c':
        $stmt = $conn->prepare("SELECT * FROM customer WHERE cid = ?");
        $bankStmt = $conn->prepare("SELECT * FROM cbank WHERE cid = ?");
        break;
    case 'm':
        $stmt = $conn->prepare("SELECT * FROM merchant WHERE mid = ?");
        $bankStmt = $conn->prepare("SELECT * FROM mbank WHERE mid = ?");
        $_SESSION['mid'] = $id;
        break;
    case 'd':
        $stmt = $conn->prepare("SELECT * FROM deliveryperson WHERE did = ?");
        $bankStmt = $conn->prepare("SELECT * FROM mbank WHERE did = ?");
        break;
    default:
        die("Invalid role.");
}

// $stmt->bind_param("s", $id);
// $bankStmt->bind_param("s", $id);
// $stmt->execute();
// $bankStmt->execute();

// $userResult = $stmt->get_result();
// $walletResult = $bankStmt->get_result();

// if ($userResult->num_rows > 0) {
//     $user = $userResult->fetch_assoc();
// }
// if ($walletResult->num_rows > 0) {
//     $wallet = $walletResult->fetch_assoc();
// }

// $stmt->close();
// $bankStmt->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./m_wallet.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 
    <script src="./m_wallet.js" type="text/javascript"></script>
    <title>Wallet</title>
</head>
<style>
    .logo h1{
    margin-left: 30px;
    text-decoration: none !important;
    color: black !important;
    font-size: calc(1.375rem + 1.5vw);
    font-weight: 800;
    line-height: 1.2;
    font-size: calc(1.375rem + 1.5vw);
    margin-top: 0;
    margin-bottom: .5rem;
    font-family: "Raleway", sans-serif;
    border-bottom: none;
}
</style>
<body>
<div class="block1">
    <a href="../merchant/merchant_shop.php" class="logo" style="text-decoration:none;"><h1>Junglebite</h1></a>
        <img id="wallet" src="./image/wallet.png" alt="wallet icon" width="30" height="30">
        <h1>Marchent Wallet</h1>
        <!-- <span style="font-size: 22px; margin: 0px; margin-left: 30px;">Welcome to your wallet!</span> -->
    </div>
    <div id="container">
        <!-- 左半部 -->
        <div id="menu">
            <div class="block2">
                <div id="add_card" style="margin-top: 10px;">
                    <!-- <h2 class="word" style="font-size: 23px;margin: 10px 0 10px 0">My cards</h2> -->
                    <!-- <img id="add" src="./image/add.png" alt="add button" width="25" height="25"> -->
                </div>

                <div class="block3">
                    <div class="income">
                        <span style="font-size: 22px;">本月銷售：</span> 
                        <p id="salary" style="font-size: 28px;margin: 20px 5px 5px 5px;width: 100%;"> 
                            <?php
                            // include('connect.php');
                            // $id = $_GET['id'] ?? null;

                            if ($id) {
                                $stmt = $conn->prepare("
                                    SELECT SUM(totalPrice) AS monthlyIncome
                                    FROM transaction
                                    WHERE mid = ? AND MONTH(transactionTime) = MONTH(CURDATE()) AND YEAR(transactionTime) = YEAR(CURDATE())
                                ");
                                $stmt->bind_param("i", $id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                $income = 0;
                                if ($row = $result->fetch_assoc()) {
                                    $income = $row['monthlyIncome'] ?? 0;
                                }

                                echo htmlspecialchars($income) . ' NTD';
                                $stmt->close();
                            } else {
                                echo '未提供商家 ID';
                            }
                            ?>
                        </p>
                    </div>
                    <?php
                    // 只撈一筆銀行帳戶資料
                    $sql = "SELECT * FROM `mbank` WHERE mid = $id LIMIT 1";
                    $result = $conn->query($sql);

                    if (!$result) {
                        die("SQL Error: " . $conn->error);
                    }

                    if ($row = $result->fetch_assoc()) {
                        // 只輸出 bankCode 跟 accountNumber
                        echo '<div class="bank-info card">';
                        echo '<p class="c_title" style="font-size: 22px; margin: 0;margin-top:10px; font-weight: bold;">Salary Account</p>';
                        echo '<p style="margin-top:35px;margin-bottom:10px"> Bank Code: ' . htmlspecialchars($row['bankCode']) . '</p>';
                        echo '<p style="margin:0;">Number: ' . htmlspecialchars($row['accountNumber']) . '</p>';
                        echo '</div>';
                    } else {
                        // echo '<p>No bank info found.</p>';
                        echo '<a href="addBank.php?id=' . htmlspecialchars(urlencode($id)) . '&role=' . htmlspecialchars(urlencode($role)) . '" style="text-decoration: none;text-align: center;">
                                <div class="bank-info card" style="background-color: #f0f0f0; color: #333;">
                                    <p class="c_title" style="font-size: 22px; margin: 0;margin-top:10px; font-weight: bold;padding-top:25px;">
                                        Click to Add<br> Salary Account
                                    </p>
                                </div>
                            </a>';
                    }
                    ?>
                </div>
                
                <h3>收入狀況</h3>
                <div class="visualize_graph" style="background-color: #ffffff;">
                    <canvas id="merchantChart" width="120%" height="100%"></canvas>
                </div>
                <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
            </div>  

        <!-- 右半部 -->
        <div id="right_container">
            <h2 class="word" style="font-size:28px; letter-spacing:2px; text-align: center; margin:40px;">
            <?php
                include('connect.php');

                // 查詢 did = 1 的外送員名稱
                $sql = "SELECT mName FROM merchant m
                        WHERE m.mid = $id ";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $merchant = isset($row['mName']) ? htmlspecialchars($row['mName']) : 'Merchant';

                echo "Welcome, " . $merchant . "!";
            ?>
            </h2>
            <div id="transaction_list" style="display:flex; justify-content:flex-start; overflow-y:auto;padding-bottom: 10px;height: 750px;">

            <?php
            include('connect.php');

            // 查詢 transaction + merchant 資料
            $sql = "
                SELECT t.transactionTime, m.mName, t.mRating, t.mComment, t.cid, c.cName
                FROM transaction t
                INNER JOIN merchant m ON t.mid = m.mid
                INNER JOIN customer c ON t.cid = c.cid
                WHERE m.mid=$id
                ORDER BY t.transactionTime DESC
            ";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                echo '<table style="width:100%; border-collapse:collapse;">
                        <thead style="font-size: 22px;">
                            <tr>
                                <th style="padding:10px;width:15%;">Date</th>
                                <th style="padding:10px;">Customer</th>
                                <th style="padding:10px 15px;">Rating</th>
                                <th style="padding:10px;width:50%;">Comments</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 20px;">';

                        while ($row = $result->fetch_assoc()) {
                            // 安全轉成數字
                            $rating = isset($row['mRating']) ? (float)$row['mRating'] : NULL;
                            if (is_null($rating)) {
                                $stars = '<span style="color:gray;">no rating</span>';
                            } else {
                                $rating = max(0, min(5, $rating)); // 👉 限制 rating 一定在 0～5 之間
                        
                                // 計算星星
                                $fullStars = (int)floor($rating);
                                $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                        
                                $stars = str_repeat('⭐', $fullStars);
                                if ($hasHalfStar) {
                                    $stars .= '<img src="./image/half-star.png" alt="half star" style="width:20px; height:20px; margin:0px; padding:0px; vertical-align:middle; padding: 0px 2px 3px 2px;">'; // 半星
                                }
                                // $stars .= str_repeat('☆', $emptyStars);
                                $stars .= str_repeat('<img src="./image/star.png" alt="half star" style="width:20px; height:20px; margin:0px; padding:0px; vertical-align:middle; padding: 0px 2px 3px 2px;">', $emptyStars);
                            }
                            $comment = isset($row['mComment']) ? trim($row['mComment']) : '';
                            if ($comment === '' || strtolower($comment) === 'null') {
                                $comment = 'No comment';
                            }
                    
                            // 安全處理 comment
                            // $comment = isset($row['mComment']) ? trim($row['mComment']) : '';
                            $shortComment = mb_strimwidth($comment, 0, 100, '...');
                            $safeFullComment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'); // ENT_QUOTES 把單雙引號都轉換
                            $safeShortComment = htmlspecialchars($shortComment, ENT_QUOTES, 'UTF-8');

                            echo '<tr style="transition: background-color 0.3s;">
                                    <td>' . htmlspecialchars($row['transactionTime']) . '</td>
                                    <td>' . htmlspecialchars($row['cName']) . '</td>
                                    <td style="width:25%;padding:0px 15px;">' . $stars . '</td>
                                    <td class="comment-cell"  
                                        style="cursor:pointer; text-decoration:;padding:5px 15px;" 
                                        data-full-comment=\'' . $safeFullComment . '\'>' . $safeShortComment . '</td>
                                </tr>';
                                
                        }

                        echo '</tbody></table>';
                    } else {
                        echo '<p style="font-size:22px; color:gray;">No transactions found for m1.</p>';
                    }
                    ?>

                    <!-- Modal -->
                    <div id="commentModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:white; padding:30px; border-radius:12px; box-shadow:0 2px 15px rgba(0,0,0,0.4); z-index:1000; min-width:300px; text-align:center;">
                        <img id="closecomment" src="./image/cross.png" alt="close button" width="15" height="15" style="position:absolute; top:10px; right:10px; cursor:pointer;">
                        <p id="modalCommentText" style="font-size:20px; margin-top:30px; margin-bottom:20px;"></p>
                    </div>
                    <div id="modalOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;"></div>

<script>
// document.addEventListener("DOMContentLoaded", function () {
//     document.querySelectorAll('.comment-cell').forEach(function(cell) {
//         cell.addEventListener('click', function() {
//             var fullComment = this.getAttribute('data-full-comment');
//             document.getElementById('modalCommentText').textContent = fullComment;
//             document.getElementById('modalOverlay').style.display = 'block';
//             document.getElementById('commentModal').style.display = 'block';
//         });
//     });

//     document.getElementById('closecomment').addEventListener('click', function () {
//         document.getElementById('commentModal').style.display = 'none';
//         document.getElementById('modalOverlay').style.display = 'none';
//     });

//     document.getElementById('modalOverlay').addEventListener('click', function () {
//         document.getElementById('commentModal').style.display = 'none';
//         this.style.display = 'none';
//     });
// });
</script>
                <!-- <a href="comment.php?mId=' . htmlspecialchars($row['mId']) . '">View Comment</a> -->
        </div>
        </div>
    </div>
    </div>
    <!-- <div class="footer">
                <p>© 2023 Your Company. All rights reserved.</p>
                <p>Privacy Policy | Terms of Service</p>
    </div> -->
    <!-- 顯示卡片資訊 -->
    <div id="showCard" style="display: none;">
        <img id="closeshow" src="./image/cross_white.png" alt="close button" width="15" height="15">
        <?php
        $sql = "SELECT * FROM `mbank` WHERE mid = $id LIMIT 1";
        $result = $conn->query($sql);

        if (!$result) {
            die("SQL Error: " . $conn->error);
        }
        if ($row = $result->fetch_assoc()) {
            // 只輸出 bankCode 跟 accountNumber
            echo '<div class="bank-info" style="padding: 75px 0px 0px 135px; color: white; font-size: 24px; font-weight: 800;"';
            echo '<p class="c_title" style="font-size: 30px; margin: 0; font-weight: bold;">Salary Account</p>';
            echo '<p>Bank Code: ' . htmlspecialchars($row['bankCode']) . '</p>';
            echo '<p>Number: ' . htmlspecialchars($row['accountNumber']) . '</p>';
            echo '</div>';
        } else {
            echo '<p>No bank info found.</p>';
        }
        // else {
        //     echo '<p>No bank info found.</p>';
        // }
        
        // while ($row = $result->fetch_assoc()) {
        //     // 確保每張卡片擁有唯一的 ID
        //     $cardId = $row['cardName']; 
        //     echo '<div class="card_detail" id="detail_card_' . $cardId . '" style="display: none;padding: 0px 0px 10px 90px;color: white;font-weight: 1000;">
            
        //         <p class="c_title">' . $row['cardName'] . '</p>
        //         <div class="info">
        //             <p>Card Number: ' . $row['cardNumber'] . '</p>
        //             <p>Card Type: ' . $row['cardType'] . '</p>
        //             <p>Card Holder: ' . $row['cardHolder'] . '</p>
        //             <p>Expiration Date: ' . $row['expirationDate'] . '</p>
        //             <p>Card CVV: ' . $row['cvv'] . '</p>
        //         </div>
        //         <div class="change">
        //         <a href="editCard.php?cardName=' . urlencode($cardId) . '">
        //             <img class="edit" src="./image/pencil.png" alt="edit button" width="25" height="25">
        //         </a>
        //     </div>
        //     </div>';
        // }
        ?>
        <div class="change">
            <a href="editBank.php?id=<?= urlencode($id) ?>&role=<?= urlencode($role) ?>">
                <img class="edit" src="./image/pencil_white.png" alt="edit button" width="25" height="25">
            </a>
        </div>
    </div>
    <script src="./d_wallet.js" type="text/javascript"></script>
    <script>// 點擊顯示完整評論
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.comment-cell').forEach(function(cell) {
            cell.addEventListener('click', function() {
                var fullComment = this.getAttribute('data-full-comment');
                document.getElementById('modalCommentText').textContent = fullComment;
                document.getElementById('modalOverlay').style.display = 'block';
                document.getElementById('commentModal').style.display = 'block';
            });
        });
    
        document.getElementById('closecomment').addEventListener('click', function () {
            document.getElementById('commentModal').style.display = 'none';
            document.getElementById('modalOverlay').style.display = 'none';
        });
    
        document.getElementById('modalOverlay').addEventListener('click', function () {
            document.getElementById('commentModal').style.display = 'none';
            this.style.display = 'none';
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        const mid = urlParams.get('id');  // ← 從網址參數抓 mid

        if (!mid) {
            alert("找不到商家 ID");
            return;
        }

        fetch("get_merchant_data.php?mid=" + mid)
            .then(response => response.json())
            .then(data => {
            console.log("回傳資料", data);
            
            if (!Array.isArray(data) || data.length === 0) {
                console.warn("No data returned");
                return;
            }

        const labels = data.map(row => row.date);
        const transactionCounts = data.map(row => parseInt(row.transaction_count));
        const ratings = data.map(row => parseFloat(row.avg_rating));

        const ctx = document.getElementById('merchantChart');
        if (!ctx) {
            console.error("Canvas element not found");
            return;
        }

        new Chart(ctx, {
            type: 'bar',
            data: {
            labels: labels,
            datasets: [
                {
                type: 'bar',
                label: '交易次數',
                data: transactionCounts,
                yAxisID: 'y'
                },
                {
                type: 'line',
                label: '平均評分',
                data: ratings,
                yAxisID: 'y1'
                }
            ]
            },
            options: {
            responsive: true,
            scales: {
                y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: '交易次數'
                }
                },
                y1: {
                beginAtZero: true,
                position: 'right',
                min: 0,
                max: 5,
                title: {
                    display: true,
                    text: '平均評分'
                },
                grid: {
                    drawOnChartArea: false
                }
                }
            }
            }
        });
        });
    });
</script>

</body>
</html>
