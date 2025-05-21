<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
session_start();
include "../../dbh.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['cid'] = $user['cid'];
        $_SESSION['cName'] = $user['cName'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['login_success'] = "人臉註冊成功並登入！";
        $_SESSION['role'] = "c";

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "fail", "message" => "找不到該 Email 對應的帳戶"]);
    }
}
?>
