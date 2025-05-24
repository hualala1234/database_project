<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./all_record.css">
    <!-- <script src="./m_wallet.js" type="text/javascript"></script> -->
    <title>All record</title>
</head>
<body>
    <div class="block1" >
        <img id="order" src="./image/comment.png" alt="wallet icon" width="30" height="30" style="margin-left: 20px;margin-right: 20px;">
        <h1>Comment Management</h1><span>
        <!-- <span style="font-size: 22px; margin: 0px; margin-left: 30px;">Welcome to your wallet!</span> -->
    </div>
    <!-- 右半部 -->
    <div id="right_container">
        <h2 class="word" style="font-size:28px; letter-spacing:2px; text-align: center; ">All Order</h2>
        <div id="transaction_list" style="display:flex; justify-content:flex-start;">

        <?php
        include('connect.php');

        // 查詢 transaction + merchant 資料
        $sql = "
            SELECT t.transactionTime, m.mName, t.mRating, t.mComment, d.dpName, t.dRating, t.dComment, t.tranId, t.orderStatus
            FROM transaction t
            LEFT JOIN merchant m ON t.mId = m.mId
            LEFT JOIN deliveryperson d ON t.did = d.did
            ORDER BY t.transactionTime DESC
        ";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo '<table style="width:100%; border-collapse:collapse;">
                    <thead style="font-size: 22px;">
                        <tr>
                            <th style="padding:10px;">TransID</th>
                            <th style="padding:10px;width:10%;">Date</th>
                            <th style="padding:10px;width:10%;">Merchant</th>
                            <th style="padding:10px;width:12%;">Rating</th>
                            <th style="padding:10px;">Comment</th>
                            <th style="padding:10px;width:6%;">Delivery Man</th>
                            <th style="padding:10px;width:12%;">Rating</th>
                            <th style="padding:10px;">Comment</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 20px;">';

        while ($row = $result->fetch_assoc()) {
            // 安全轉成數字
            $mrating = isset($row['mRating']) ? (float)$row['mRating'] : NULL;
            
            if($mrating === null) {
                $mStars = '<span style="color:gray;">no rating</span>';
            }else{
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
            if ($drating === null) {
                $dStars = '<span style="color:gray;">no rating</span>';
            } else {
                $drating = max(0, min(5, $drating)); 
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

            // ----- 評論處理 -----
            $mcomment = trim($row['mComment'] ?? '');
            if ($mcomment === '' || strtolower($mcomment) === 'null') {
                $mcomment = 'No comment';
            }

            $dcomment = trim($row['dComment'] ?? '');
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
                    <td>' . htmlspecialchars($row['mName']) . '</td>
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
                </tr>';
            echo '<script>console.log("mComment: ' . $msafeFullComment . '");</script>';
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
            <!-- 🗑️ Trash button -->
            <img id="deleteCommentBtn"
                src="./image/trash.png"
                alt="delete"
                width="25"
                height="25"
                style="cursor:pointer; margin-top:10px; display:none;"
                data-tid=""
                data-type="">

        </div>
        <div id="modalOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;"></div>

    <!-- <a href="comment.php?mId=' . htmlspecialchars($row['mId']) . '">View Comment</a> -->
    </div>
            </div>
    <!-- <div class="footer">
                <p>© 2023 Your Company. All rights reserved.</p>
                <p>Privacy Policy | Terms of Service</p>
    </div> -->

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

        
        document.querySelectorAll('.comment-cell').forEach(function(cell) {
            cell.addEventListener('click', function () {
                const fullComment = this.getAttribute('data-full-comment');
                const tId = this.getAttribute('data-tid'); // 你需要在原本 td 加上這個屬性
                const type = this.getAttribute('data-type'); // mComment 或 dComment

                document.getElementById('modalCommentText').textContent = fullComment;

                const deleteBtn = document.getElementById('deleteCommentBtn');
                deleteBtn.style.display = 'inline-block'; // 顯示 trash icon
                deleteBtn.setAttribute('data-tid', tId);
                deleteBtn.setAttribute('data-type', type);

                document.getElementById('modalOverlay').style.display = 'block';
                document.getElementById('commentModal').style.display = 'block';
            });
        });

        // 刪除評論
        document.getElementById('deleteCommentBtn').addEventListener('click', function () {
            const tid = this.getAttribute('data-tid');
            const type = this.getAttribute('data-type');

            if (confirm("Are you sure you want to delete this comment?")) {
                console.log("Sending:", `tranId=${tid}&type=${type}`);
                fetch('./deleteComment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `tranId=${tid}&type=${type}`
                })
                .then(response => response.text())
                .then(result => {
                    // alert(result);
                    alert('Comment deleted.');
                    location.reload(); // 或改成隱藏該行、更新內容
                });
            }
        });

        // 關閉 modal
        document.getElementById('closecomment').addEventListener('click', function () {
            document.getElementById('commentModal').style.display = 'none';
            document.getElementById('modalOverlay').style.display = 'none';
        });


    });
    </script>
</body>
</html>
