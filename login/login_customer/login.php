
<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
// echo "OK - Session Set for cid: " . $_SESSION['cid'];
session_start();
include "../../dbh.php";
$wrong_password = "Enter your password";
$error_email = "Enter your email";


// ✅ Debug 測試用（可刪）
if (isset($_SESSION['cid'])) {
    echo "✅ SESSION 正常：cid = " . $_SESSION['cid'] . "<br>";
}


// ✅ 表單登入
if ($_SERVER["REQUEST_METHOD"]=="POST"){

    $email=mysqli_real_escape_string($conn,$_POST['email']);
    $password=mysqli_real_escape_string($conn,$_POST['password']);

    //fetch database
    $sql="SELECT * FROM customer WHERE email = '$email'";
    $result=$conn->query($sql);
    //只有找到使用者時才檢查密碼
    if ($result->num_rows > 0) {
        // Check if password is correct
        $user = $result->fetch_assoc();

        if ($password == $user['password']) {
            $cid = $user['cid']; // ← 加上這行
            $_SESSION['cid'] = $user['cid'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['fullname'] = $user['cName'];
            $_SESSION['login_success'] = "登入成功！";
            $_SESSION['role'] = 'c';

            // 檢查該使用者是否已有錢包
            $walletCheckSql = "SELECT * FROM wallet WHERE cid = '$cid' AND role = 'c'";
            $walletCheckResult = $conn->query($walletCheckSql);

            if ($walletCheckResult->num_rows == 0) {
                // 若不存在，則建立 wallet 資料（初始餘額為 0）
                $createWalletSql = "INSERT INTO wallet (cid,role, balance) VALUES ('$cid','c', 0)";
                $conn->query($createWalletSql);
            }

            // 跳回原本頁面
            header("Location: ../../customer/index.php?cid=$cid");
            exit();
        } 
        else {
            header("Location: system_blog.php?wrongpw=Wrong password&show=login");
            exit();
        }
    }
    else{
        header("Location: system_blog.php?erroremail=No user with that email&show=login");
        exit();
    }
}
// 新加的
// ✅ 人臉登入 POST（由 JS 傳入）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['face_login']) && isset($_POST['cid'])) {
    
    $cid = (int)$_POST['cid'];
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
        $cid = $user['cid']; // ← 加上這行
        
        // 檢查該使用者是否已有錢包
        $walletCheckSql = "SELECT * FROM wallet WHERE cid = '$cid' AND role = 'c'";
        $walletCheckResult = $conn->query($walletCheckSql);

        if ($walletCheckResult->num_rows == 0) {
            // 若不存在，則建立 wallet 資料（初始餘額為 0）
            $createWalletSql = "INSERT INTO wallet (cid,role, balance) VALUES ('$cid','c', 0)";
            $conn->query($createWalletSql);
            echo "create wallet";
        }

        echo "face login success";
        
        exit();
    } else {
        http_response_code(401);
        echo "登入失敗：查無此帳戶";
        exit();
    }
}
?>
