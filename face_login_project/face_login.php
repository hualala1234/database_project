<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
session_start();
require '../dbh.php'; // 根據實際路徑修改

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['face_login']) && isset($_POST['cid'])) {
    $cid = (int)$_POST['cid'];

    // 查詢使用者資料
    $stmt = $conn->prepare("SELECT * FROM customer WHERE cid = ?");
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['cid'] = $user['cid'];
        $_SESSION['cName'] = $user['cName'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['login_success'] = "登入成功！";
        $_SESSION['role'] = 'c';
        echo "OK - Session Set: " . session_id();

        // 登入成功，什麼都不用輸出（由 JS 導向）
        exit();
    } else {
        http_response_code(401);
        echo "登入失敗：查無此帳戶";
        exit();
    }
} else {
    http_response_code(400);
    echo "請以 POST 傳入 face_login 和 cid";
}
?>
