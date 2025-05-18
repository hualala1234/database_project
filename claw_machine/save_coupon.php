<?php
// 資料庫連線設定
// $host = 'localhost';
// $dbname = 'junglebite';
// $user = 'root';
// $pass = '';

// // 設定回應格式為 JSON
// header('Content-Type: application/json');

// // 取得並解析 JSON 輸入
// $input = json_decode(file_get_contents('php://input'), true);

// // 檢查必要欄位是否存在
// if (!isset($input['prizeMessage'], $input['prizeCode'], $input['time'])) {
//     echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
//     exit;
// }

// // 可改成從 session 抓 customer ID（如果你有登入機制）
// session_start();
// $cid = $_SESSION['cid'] ?? null;


// try {
//     // ✅ 建立資料庫連線
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//     // ✅ 檢查今天是否已玩過
//     $stmt = $pdo->prepare("
//         SELECT COUNT(*) FROM coupons
//         WHERE cid = :cid
//           AND DATE(created_at) = CURDATE()
//     ");
//     $stmt->execute([':cid' => $cid]);
//     $playedToday = $stmt->fetchColumn();

//     // 每次讀取優惠券時，刪除超過一天的
//     $stmt = $pdo->prepare("DELETE FROM coupons WHERE created_at < NOW() - INTERVAL 1 DAY");
//     $stmt->execute();


//     if ($playedToday > 2) {
//         echo json_encode(['status' => 'error', 'message' => '今天已經玩過三次了']);
//         exit;
//     }

//     // ✅ 提取關鍵字（例如："15% 折扣優惠"、"免運費1次"）
//     $fullMessage = $input['prizeMessage'];
//     $matchedKeyword = '';
//     if (preg_match('/\d+% 折扣優惠|免運費\d*次/', $fullMessage, $matches)) {
//         $matchedKeyword = $matches[0];
//     } else {
//         $matchedKeyword = $fullMessage; // 若無法匹配就儲存原始訊息
//     }

//     // ✅ 沒玩過就儲存資料
//     $stmt = $pdo->prepare("INSERT INTO coupons (message, code, created_at,cid) VALUES (:message, :code, :created_at, :cid)");
//     $stmt->execute([
//         ':message' => $matchedKeyword,
//         ':code' => $input['prizeCode'],
//         ':created_at' => date('Y-m-d H:i:s'),  // ✅ 安全又簡單
//         ':cid' => $cid,
//     ]);

//     echo json_encode(['status' => 'success', 'message' => 'Coupon saved']);
// } catch (PDOException $e) {
//     echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
// }
?>
<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Taipei');


$host = 'localhost';
$dbname = 'junglebite';
$user = 'root';
$pass = '';

$input = json_decode(file_get_contents('php://input'), true);
$cid = $_SESSION['cid'] ?? null;

if (!$cid || !isset($input['prizeMessage'], $input['prizeCode'], $input['time'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 每次新增前，刪除超過一天的資料（讓 is_expired 有效）
    $pdo->exec("UPDATE coupon SET is_expired = TRUE WHERE created_at < CURDATE() AND game=1");

    // 查詢今天的紀錄數量
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM coupon WHERE cid = :cid AND DATE(created_at) = CURDATE() AND game=1");
    $stmt->execute([':cid' => $cid]);
    $playedToday = (int)$stmt->fetchColumn();

    // 判斷 qualified 與 discount
    $qualified = $playedToday < 3;
    $message = $input['prizeMessage'];
    // $discount = 0;

    // ✅ 擷取關鍵字訊息（用於顯示）
    $fullMessage = $input['prizeMessage'];
    if (preg_match('/\d+% 折扣優惠|免運費\d*次/', $fullMessage, $matches)) {
        $message = $matches[0];
    } else {
        $message = $fullMessage; // 若無法匹配就用原始訊息
    }

    // ✅ 預設折扣為 0，若是折扣類型就解析折扣數值
    $discount = 0;
    if (preg_match('/(\d+)% 折扣優惠/', $message, $match)) {
        $discount = (int)$match[1];
    }
    if (!$qualified) {
        $discount = 0; // 若超過每日三次就不能有折扣
        echo json_encode([
        'status' => 'error',
        'message' => '今天已經玩過三次囉～請明天再來',]);
    }


    // 若超過三次，雖然紀錄仍存，但折扣歸 0、不 qualified
    // if (!$qualified) {
    //     $discount = 0;
    // }

    else{
        // 寫入資料庫
        $stmt = $pdo->prepare("
            INSERT INTO coupon (message, code, created_at, cid, game, discount, qualified, used, is_expired)
            VALUES (:message, :code, :created_at, :cid, :game, :discount, :qualified, FALSE, FALSE)
        ");
        $stmt->execute([
            ':message' => $message,
            ':code' => $input['prizeCode'],
            ':created_at' => date('Y-m-d H:i:s'),
            ':cid' => $cid,
            ':game' => 1,
            ':discount' => $discount,
            ':qualified' => $qualified,
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Coupon saved']);
    }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

