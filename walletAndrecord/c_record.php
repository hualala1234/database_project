<?php
include('connect.php');
session_start();
// $id = $_GET['cid'] ?? null;
$id = $_SESSION["cid"] ?? null;
$role = $_SESSION["role"] ?? null;
if (!$id) die("未提供 cid");
?>
<script>
    console.log("cid: <?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>");
    console.log("role: <?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>");
</script>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="./c_record.css" />
  <script src="./c_record.js" type="text/javascript"></script>
  <link href="https://fonts.googleapis.com/css2?family=Chivo:ital,wght@0,100..900;1,100..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <title>Customer Record</title>
</head>
<body>
  <div class="block1">
    <a href="../customer/index.php" class="logo" style="text-decoration:none; margin-left: 30px;
    text-decoration: none !important;
    color: black !important;
    font-size: 47px;
    font-weight: 800;
    line-height: 1.2;
    font-size: calc(1.375rem + 1.5vw);
    margin: 0 0 8px 30px;
    margin-bottom: .5rem;font-family: 'Raleway', sans-serif;"><p>Junglebite</p></a>
    <img id="wallet" src="./image/folder.png" alt="wallet icon" width="30" height="30" />
    <h1>Transaction Records</h1>
  </div>

  <div id="container">
    <div id="add_card" style="margin-top: 10px;">
          <img src="./image/credit-card.png" alt="add button" width="28" height="28" />
          <h2 class="word" style="font-size: 28px; margin: 10px 0;">My cards</h2>
        </div>
    <!-- 左邊 -->
    <div id="menu">
        
      <div class="block2">
        

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
                        <p>' . htmlspecialchars($card['cardNumber']) . '</p>
                        <p>ExpDate: ' . htmlspecialchars($card['expirationDate']) . '</p>
                    </div>
                </div>
            </div>';
        }
        ?>
      </div>
    </div>

    <!-- 右邊 -->
    <div id="right_container">
      <!-- <h2 class="word" style="text-align:center;">History Transactions</h2> -->
      <div id="transaction_list">

        <!-- 全部交易 -->
        <div id="transaction_all" style="display:block; overflow-y:auto;padding-bottom: 10px;height: 500px;">
            <h2 class="word" style="text-align:center;">History Transactions ( <span>All Transactions</span> )</h2>
            <table>
                <thead style="font-size: 22px;">
                <tr>
                    <th style="padding:10px;">TransID</th>
                    <th style="padding:10px;width:10%;">Date</th>
                    <th style="padding:10px;width:10%;">Merchant</th>
                    <th style="padding:10px;width:13%;">Rating</th>
                    <th style="padding:10px;">Comment</th>
                    <th style="padding:10px;width:6%;">Delivery Man</th>
                    <th style="padding:10px;width:13%;">Rating</th>
                    <th style="padding:10px;">Comment</th>
                    <th style="padding:10px;">Spend</th>
                </tr>
                </thead>
                <tbody style="font-size: 20px;">
                <?php
                    $sql = "
                    SELECT t.transactionTime, t.cid, m.mName, t.mRating, t.mComment, d.dpName, t.dRating, t.dComment, t.tranId, t.totalPrice
                    FROM transaction t
                    LEFT JOIN merchant m ON t.mid = m.mid
                    LEFT JOIN deliveryperson d ON t.did = d.did
                    WHERE t.cid = $id
                    ORDER BY t.transactionTime DESC
                    ";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $mrating = isset($row['mRating']) ? (float)$row['mRating'] : null;
                        $drating = isset($row['dRating']) ? (float)$row['dRating'] : null;
                        if($mrating === null) {
                            $mStars = '<span style="color:gray;">no rating</span>';
                        }else{
                            $mfullStars = floor($mrating);
                            $mhasHalfStar = ($mrating - $mfullStars) >= 0.5;
                            $memptyStars = 5 - $mfullStars - ($mhasHalfStar ? 1 : 0);
                            $mStars = str_repeat('⭐', $mfullStars);
                            if ($mhasHalfStar) $mStars .= '<img src="./image/half-star.png" style="width:20px;height:20px;vertical-align:middle;">';
                            $mStars .= str_repeat('<img src="./image/star.png" style="width:20px;height:20px;vertical-align:middle;">', $memptyStars);
                        }

                        if ($drating === null) {
                            $dStars = '<span style="color:gray;">no rating</span>';
                        } else {
                            $dfullStars = floor($drating);
                            $dhasHalfStar = ($drating - $dfullStars) >= 0.5;
                            $demptyStars = 5 - $dfullStars - ($dhasHalfStar ? 1 : 0);
                            $dStars = str_repeat('⭐', $dfullStars);
                            if ($dhasHalfStar) $dStars .= '<img src="./image/half-star.png" style="width:20px;height:20px;vertical-align:middle;">';
                            $dStars .= str_repeat('<img src="./image/star.png" style="width:20px;height:20px;vertical-align:middle;">', $demptyStars);
                        }

                        $mcomment = isset($row['mComment']) ? trim($row['mComment']) : '';
                        if ($mcomment === '' || strtolower($mcomment) === 'null') {
                            $mcomment = 'No comment';
                        }
                        $dcomment = isset($row['dComment']) ? trim($row['dComment']) : '';
                        if ($dcomment === '' || strtolower($dcomment) === 'null') {
                            $dcomment = 'No comment';
                        }


                        // $msafeComment = htmlspecialchars(trim($row['mComment']), ENT_QUOTES, 'UTF-8');
                        // $dsafeComment = htmlspecialchars(trim($row['dComment']), ENT_QUOTES, 'UTF-8');
                        $msafeComment = htmlspecialchars($mcomment, ENT_QUOTES, 'UTF-8'); // ✅ 用處理後的值
                        $dsafeComment = htmlspecialchars($dcomment, ENT_QUOTES, 'UTF-8');
                        $msafeShort = mb_strimwidth($msafeComment, 0, 100, '...');
                        $dsafeShort = mb_strimwidth($dsafeComment, 0, 100, '...');

                        echo '<tr>
                                <td>' . htmlspecialchars($row['tranId']) . '</td>
                                <td>' . htmlspecialchars($row['transactionTime']) . '</td>
                                <td style="padding: 10px 5px;">' . htmlspecialchars($row['mName']) . '</td>
                                <td>' . $mStars . '</td>
                                <td class="comment-cell" style="padding:10px 0px;"
                                    data-full-comment="' . $msafeComment . '" 
                                    data-tid="' . $row['tranId'] . '" 
                                    data-type="mComment">' . $msafeShort . '</td>
                                <td>' . htmlspecialchars($row['dpName']) . '</td>
                                <td>' . $dStars . '</td>
                                <td class="comment-cell"  style="padding:10px 0px;"
                                    data-full-comment="' . $dsafeComment . '"
                                    data-tid="' . $row['tranId'] . '"  
                                    data-type="dComment">' . $dsafeShort . '</td>
                                <td>' . htmlspecialchars($row['totalPrice']) . ' NTD</td>
                            </tr>';
                    }
                    } else {
                    $cName = 'this customer';
                    $custResult = $conn->query("SELECT cName FROM customer WHERE cid = $id");
                    if ($custResult && $row = $custResult->fetch_assoc()) {
                        $cName = $row['cName'];
                    }
                    echo '<tr><td colspan="8" style="text-align:center; color:gray; font-size:22px; padding:20px;">
                            No transactions found for ' . htmlspecialchars($cName) . '.<br>
                            <span style="font-size:18px;">Go place your order now! 😽 
                                <a href="../customer/index.php">click to order</a>
                            </span>
                            </td></tr>';
                    }
                ?>
                </tbody>
            </table>
        </div>  


        <!-- 錢包交易 -->
        <div class="transaction_group" id="transaction_balance" style="display:none;overflow-y:auto;padding-bottom: 10px;height: 500px;">
            <h2 class="word" style="text-align:center;">History Transactions ( <span>Wallet Balance</span> )</h2>
          <table>
            <thead style="font-size: 22px;">
              <tr>
                <th style="padding:10px;">TransID</th>
                <th style="padding:10px;width:10%;">Date</th>
                <th style="padding:10px;width:10%;">Merchant</th>
                <th style="padding:10px 0px;width:13%;">Rating</th>
                <th style="padding:10px;">Comment</th>
                <th style="padding:10px;width:6%;">Delivery Man</th>
                <th style="padding:10px 0px;width:13%;">Rating</th>
                <th style="padding:10px;">Comment</th>
                    <th style="padding:10px;">Spend</th>
              </tr>
            </thead>
            <tbody style="font-size: 20px;">
              <?php
              $walletSql = "SELECT t.transactionTime, t.cid, m.mName, t.mRating, t.mComment, d.dpName, t.dRating, t.dComment, t.tranId, t.totalPrice
                            FROM transaction t
                            LEFT JOIN merchant m ON t.mId = m.mId
                            LEFT JOIN deliveryperson d ON t.did = d.did
                            WHERE t.cid = $id AND paymentMethod = 'walletBalance'
                            ORDER BY t.transactionTime DESC";
              $walletResult = $conn->query($walletSql);
              if (!$walletResult) {
                            die("SQL Error: " . $conn->error);
                        }
              if ($result && $result->num_rows > 0) {
                while ($row = $walletResult->fetch_assoc()) {
                    // 安全轉成數字
                    $mrating = isset($row['mRating']) ? (float)$row['mRating'] : NULL;
                    if (is_null($mrating)) {
                        $mStars = '<span style="color:gray;">no rating</span>';
                    } else {
                    $mrating = max(0, min(5, $mrating));
                    // ⭐ 商家星星計算
                    $mfullStars = (int)floor($mrating);
                    $mhasHalfStar = ($mrating - $mfullStars) >= 0.5;
                    $memptyStars = 5 - $mfullStars - ($mhasHalfStar ? 1 : 0);
                
                    $mStars = str_repeat('⭐', $mfullStars);
                    if ($mhasHalfStar) {
                        $mStars .= '<img src="./image/half-star.png" alt="half star" style="width:20px; height:20px; vertical-align:middle;">';
                    }
                    $mStars .= str_repeat('<img src="./image/star.png" alt="empty star" style="width:20px; height:20px; vertical-align:middle;">', $memptyStars);
                    }
                
                    $drating = isset($row['dRating']) ? (float)$row['dRating'] : NULL;
                    if (is_null($drating)) {
                        $dStars = '<span style="color:gray;">no rating</span>';
                    } else {
                    $drating = max(0, min(5, $drating)); // ⚠️ 原本這邊錯誤寫成使用 $mrating
                    // ⭐ 外送員星星計算（如有需要顯示）
                    $dfullStars = (int)floor($drating);
                    $dhasHalfStar = ($drating - $dfullStars) >= 0.5;
                    $demptyStars = 5 - $dfullStars - ($dhasHalfStar ? 1 : 0);
                
                    $dStars = str_repeat('⭐', $dfullStars);
                    if ($dhasHalfStar) {
                        $dStars .= '<img src="./image/half-star.png" alt="half star" style="width:20px; height:20px; vertical-align:middle;">';
                    }
                    $dStars .= str_repeat('<img src="./image/star.png" alt="empty star" style="width:20px; height:20px; vertical-align:middle;">', $demptyStars);
                    }
                
                    $mcomment = isset($row['mComment']) ? trim($row['mComment']) : '';
                    if ($mcomment === '' || strtolower($mcomment) === 'null') {
                        $mcomment = 'No comment';
                    }
                    $dcomment = isset($row['dComment']) ? trim($row['dComment']) : '';
                    if ($dcomment === '' || strtolower($dcomment) === 'null') {
                        $dcomment = 'No comment';
                    }

                    // 處理評論
                    // $mcomment = isset($row['mComment']) ? trim($row['mComment']) : '';
                    $mshortComment = mb_strimwidth($mcomment, 0, 100, '...');
                    $msafeFullComment = htmlspecialchars($mcomment, ENT_QUOTES, 'UTF-8');
                    $msafeShortComment = htmlspecialchars($mshortComment, ENT_QUOTES, 'UTF-8');

                    // $dcomment = isset($row['dComment']) ? trim($row['dComment']) : '';
                    $dshortComment = mb_strimwidth($dcomment, 0, 100, '...');
                    $dsafeFullComment = htmlspecialchars($dcomment, ENT_QUOTES, 'UTF-8');
                    $dsafeShortComment = htmlspecialchars($dshortComment, ENT_QUOTES, 'UTF-8');
                
                    echo '<tr style="transition: background-color 0.3s;">
                            <td>' . htmlspecialchars($row['tranId']) . '</td>
                            <td>' . htmlspecialchars($row['transactionTime']) . '</td>
                            <td style="padding: 10px 5px;">' . htmlspecialchars($row['mName']) . '</td>
                            <td>' . $mStars . '</td>
                            <td class="comment-cell" 
                                style="cursor:pointer; text-decoration:;padding:20px;" 
                                data-full-comment="' . $msafeFullComment . '" 
                                data-tid="' . $row['tranId'] . '" 
                                data-type="mComment">' . $msafeShortComment . '</td>

                            <td>' . htmlspecialchars($row['dpName']) . '</td>
                            <td>' . $dStars . '</td>
                            <td class="comment-cell" 
                                style="cursor:pointer; text-decoration:;padding:20px;" 
                                data-full-comment="' . $dsafeFullComment . '" 
                                data-tid="' . $row['tranId'] . '" 
                                data-type="dComment">' . $dsafeShortComment . '</td>
                                <td>' . htmlspecialchars($row['totalPrice']) . ' NTD</td>
                        </tr>';
                    echo '<script>console.log("mComment: ' . $msafeFullComment . '");</script>';
                    }
                }
                else {
                    $cName = 'this customer'; // 預設值（避免 null）
                    $custResult = $conn->query("SELECT cName FROM customer WHERE cid = $id");
                    if ($custResult && $row = $custResult->fetch_assoc()) {
                        $cName = $row['cName'];
                    }
                    // ✅ 顯示在表格上方
                    echo '<tr><td colspan="8" style="text-align:center; color:gray; font-size:22px; padding:20px;">
                    No transactions found for ' . htmlspecialchars($cName) . '.<br>
                    <span style="font-size:18px;">Go place your order now! 😽 <a href="../customer/index.php">click to order</a></span>
                </td></tr>';
                }echo '</tbody></table>';
              ?>
            </tbody>
          </table>
        </div>

        <!-- 每張卡片交易 -->
        <?php
        $cardResult = $conn->query("SELECT DISTINCT cardName FROM card WHERE cid = $id");
        while ($card = $cardResult->fetch_assoc()) {
            $cardId = $card['cardName'];
            echo '<div class="transaction_group" id="transaction_' . $cardId . '" style="display:none;overflow-y:auto;padding-bottom: 10px;height: 500px;">
                    <h2 class="word" style="text-align:center;">History Transactions ( <span> ' . htmlspecialchars($cardId) . '</span> )</h2>
                    <table>
                      <thead style="font-size: 22px;">
                        <tr>
                            <th style="padding:10px;">TransID</th>
                            <th style="padding:10px;width:10%;">Date</th>
                            <th style="padding:10px;width:10%;">Merchant</th>
                            <th style="padding:10px 0px;width:13%;">Rating</th>
                            <th style="padding:10px;">Comment</th>
                            <th style="padding:10px;width:6%;">Delivery Man</th>
                            <th style="padding:10px 0px;width:13%;">Rating</th>
                            <th style="padding:10px;">Comment</th>
                            <th style="padding:10px;">Spend</th>
                        </tr>
                      </thead>
                      <tbody style="font-size: 20px;">';
            $t_sql = "SELECT t.transactionTime, t.cid, m.mName, t.mRating, t.mComment, d.dpName, t.dRating, t.dComment, t.tranId, t.totalPrice
                        FROM transaction t
                        LEFT JOIN merchant m ON t.mId = m.mId
                        LEFT JOIN deliveryperson d ON t.did = d.did
                        WHERE t.cid=$id AND t.cardName = '$cardId'
                        ORDER BY t.transactionTime DESC";
            $t_result = $conn->query($t_sql);
            if ($t_result && $t_result->num_rows > 0) {
                while ($t = $t_result->fetch_assoc()) {
                    // 安全轉成數字
                    $mrating = isset($t['mRating']) ? (float)$t['mRating'] : NULL;
                    if (is_null($mrating)) {
                        $mStars = '<span style="color:gray;">no rating</span>';
                    } else {
                        $mrating = max(0, min(5, $mrating));
                    
                        // ⭐ 商家星星計算
                        $mfullStars = (int)floor($mrating);
                        $mhasHalfStar = ($mrating - $mfullStars) >= 0.5;
                        $memptyStars = 5 - $mfullStars - ($mhasHalfStar ? 1 : 0);
                    
                        $mStars = str_repeat('⭐', $mfullStars);
                        if ($mhasHalfStar) {
                            $mStars .= '<img src="./image/half-star.png" alt="half star" style="width:20px; height:20px; vertical-align:middle;">';
                        }
                        $mStars .= str_repeat('<img src="./image/star.png" alt="empty star" style="width:20px; height:20px; vertical-align:middle;">', $memptyStars);
                    }
                
                
                    $drating = isset($t['dRating']) ? (float)$t['dRating'] : NULL;
                    if (is_null($drating)) {
                        $dStars = '<span style="color:gray;">no rating</span>';
                    } else {
                        $drating = max(0, min(5, $drating)); // ⚠️ 原本這邊錯誤寫成使用 $mrating
                        // ⭐ 外送員星星計算（如有需要顯示）
                        $dfullStars = (int)floor($drating);
                        $dhasHalfStar = ($drating - $dfullStars) >= 0.5;
                        $demptyStars = 5 - $dfullStars - ($dhasHalfStar ? 1 : 0);
                    
                        $dStars = str_repeat('⭐', $dfullStars);
                        if ($dhasHalfStar) {
                            $dStars .= '<img src="./image/half-star.png" alt="half star" style="width:20px; height:20px; vertical-align:middle;">';
                        }
                        $dStars .= str_repeat('<img src="./image/star.png" alt="empty star" style="width:20px; height:20px; vertical-align:middle;">', $demptyStars);
                    }
                    $mcomment = isset($t['mComment']) ? trim($t['mComment']) : '';
                    if ($mcomment === '' || strtolower($mcomment) === 'null') {
                        $mcomment = 'No comment';
                    }
                    $dcomment = isset($t['dComment']) ? trim($t['dComment']) : '';
                    if ($dcomment === '' || strtolower($dcomment) === 'null') {
                        $dcomment = 'No comment';
                    }

                
                    // 處理評論
                    // $mcomment = isset($t['mComment']) ? trim($t['mComment']) : '';
                    $mshortComment = mb_strimwidth($mcomment, 0, 100, '...');
                    $msafeFullComment = htmlspecialchars($mcomment, ENT_QUOTES, 'UTF-8');
                    $msafeShortComment = htmlspecialchars($mshortComment, ENT_QUOTES, 'UTF-8');

                    // $dcomment = isset($t['dComment']) ? trim($t['dComment']) : '';
                    $dshortComment = mb_strimwidth($dcomment, 0, 100, '...');
                    $dsafeFullComment = htmlspecialchars($dcomment, ENT_QUOTES, 'UTF-8');
                    $dsafeShortComment = htmlspecialchars($dshortComment, ENT_QUOTES, 'UTF-8');
                
                    echo '<tr style="transition: background-color 0.3s;">
                            <td>' . htmlspecialchars($t['tranId']) . '</td>
                            <td>' . htmlspecialchars($t['transactionTime']) . '</td>
                            <td style="padding: 10px 5px;">' . htmlspecialchars($t['mName']) . '</td>
                            <td>' . $mStars . '</td>
                            <td class="comment-cell" 
                                style="cursor:pointer; text-decoration:;padding:20px;" 
                                data-full-comment="' . $msafeFullComment . '" 
                                data-tid="' . $t['tranId'] . '" 
                                data-type="mComment">' . $msafeShortComment . '</td>

                            <td>' . htmlspecialchars($t['dpName']) . '</td>
                            <td>' . $dStars . '</td>
                            <td class="comment-cell" 
                                style="cursor:pointer; text-decoration:;padding:20px;" 
                                data-full-comment="' . $dsafeFullComment . '" 
                                data-tid="' . $t['tranId'] . '" 
                                data-type="dComment">' . $dsafeShortComment . '</td>
                            <td>' . htmlspecialchars($t['totalPrice']) . ' NTD</td>
                        </tr>';
                    echo '<script>console.log("mComment: ' . $msafeFullComment . '");</script>';
                }
            } else {
                // ❗顯示提示訊息在 <tbody> 裡的 <tr>
                $cName = 'this customer';
                $custResult = $conn->query("SELECT cName FROM customer WHERE cid = $id");
                if ($custResult && $row = $custResult->fetch_assoc()) {
                    $cName = $row['cName'];
                }
                echo '<tr><td colspan="8" style="text-align:center; color:gray; font-size:22px; padding:20px;">
                        No transactions found for ' . htmlspecialchars($cName) . '.<br>
                        <span style="font-size:18px;">Go place your order now! 😽 <a href="../customer/index.php">click to order</a></span>
                    </td></tr>';
            }

            echo '</tbody></table></div>';
        }
        ?>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div id="commentModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:white; padding:30px; border-radius:12px; box-shadow:0 2px 15px rgba(0,0,0,0.4); z-index:1000; min-width:300px; text-align:center;">
        <img id="closecomment" src="./image/cross.png" alt="close button" width="15" height="15" style="position:absolute; top:10px; right:10px; cursor:pointer;">
        <p id="modalCommentText" style="font-size:20px; margin-top:30px; margin-bottom:20px;"></p>
        <!-- 原本文字按鈕的替代版 -->
        <textarea id="editCommentInput" style="display:none; width:100%; height:80px; margin-top:10px;"></textarea>
        <div class="change">
            <img id="editBtn" class="btn-comment"
                src="./image/pencil.png" alt="edit" width="25" height="25"
                style="cursor:pointer;margin:0; margin-top:10px; display:inline-block;">
            
            <button id="saveEditBtn" class="btn-comment" style="display:none;margin-top: 10px;font-size: 16px;">💾 Save</button>
            <button id="cancelEditBtn" class="btn-nocomment" style="display:none;margin-top: 10px;margin-left:35px;font-size: 16px;">❌ Cancel</button>
            
            <!-- 🗑️ Trash button -->
            <img id="deleteCommentBtn"
                src="./image/trash.png"
                alt="delete"
                width="25"
                height="25"
                style="cursor:pointer;margin:0; margin-top:10px; display:none;"
                data-tid=""
                data-type="">
        </div>

    </div>
    <div id="modalOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;"></div>

    <!-- <a href="comment.php?mId=' . htmlspecialchars($row['mId']) . '">View Comment</a> -->
    </div>
            </div>
    <!-- <div class="footer">
                <p>© 2023 Your Company. All rights reserved.</p>
                <p>Privacy Policy | Terms of Service</p>
    </div> -->

    <!-- <script src="./d_wallet.js" type="text/javascript"></script> -->
<script>// 點擊顯示完整評論
    document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById('commentModal');
    const overlay = document.getElementById('modalOverlay');
    const modalText = document.getElementById('modalCommentText');
    const editInput = document.getElementById('editCommentInput');
    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveEditBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    const deleteBtn = document.getElementById('deleteCommentBtn');

    function resetEditor() {
        editInput.style.display = 'none';
        saveBtn.style.display = 'none';
        cancelBtn.style.display = 'none';
        editBtn.style.display = 'inline-block';
        deleteBtn.style.display = 'inline-block';
        editInput.value = '';
    }

    function openModal(fullComment, tId, type) {
        modalText.textContent = fullComment;
        resetEditor();
        deleteBtn.setAttribute('data-tid', tId);
        deleteBtn.setAttribute('data-type', type);
        modal.style.display = 'block';
        overlay.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
        overlay.style.display = 'none';
        resetEditor();
    }

    document.querySelectorAll('.comment-cell').forEach(function (cell) {
        cell.addEventListener('click', function () {
            const fullComment = this.getAttribute('data-full-comment');
            const tId = this.getAttribute('data-tid');
            const type = this.getAttribute('data-type');
            openModal(fullComment, tId, type);
        });
    });

    document.getElementById('closecomment').addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);

    editBtn.addEventListener('click', function () {
        editBtn.style.display = 'none';
        deleteBtn.style.display = 'none';
        editInput.style.display = 'block';
        saveBtn.style.display = 'inline-block';
        cancelBtn.style.display = 'inline-block';
        editInput.value = modalText.textContent.trim();
    });

    editInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault(); // 防止換行
            saveBtn.click(); // 觸發儲存
        }
    });

    cancelBtn.addEventListener('click', function () {
        resetEditor();
    });

    saveBtn.addEventListener('click', function () {
        const newComment = editInput.value;
        const tid = deleteBtn.getAttribute('data-tid');
        const type = deleteBtn.getAttribute('data-type');

        fetch('./commentEdit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `tranId=${tid}&type=${type}&comment=${encodeURIComponent(newComment)}`
        })
        .then(res => res.text())
        .then(res => {
            alert("Comment updated!");
            location.reload();
        });
    });

    deleteBtn.addEventListener('click', function () {
        const tid = deleteBtn.getAttribute('data-tid');
        const type = deleteBtn.getAttribute('data-type');

        if (confirm("Are you sure you want to delete this comment?")) {
            fetch('./commentDelete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `tranId=${tid}&type=${type}`
            })
            .then(response => response.text())
            .then(result => {
                alert('Comment deleted.');
                location.reload();
            });
        }
    });
});

</script>

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
