<?php
include "db_connection.php";
session_start();
$wrong_password = "Enter your password";
$error_email = "Enter your email";

if ($_SERVER["REQUEST_METHOD"]=="POST"){

    $email=mysqli_real_escape_string($conn,$_POST['email']);
    $password=mysqli_real_escape_string($conn,$_POST['password']);

    //fetch database
    $sql="SELECT * FROM merchant WHERE email = '$email'";
    $result=$conn->query($sql);
    //只有找到使用者時才檢查密碼
    if ($result->num_rows > 0) {
        // Check if password is correct
        $user = $result->fetch_assoc();

        if ($password == $user['password']) {
        
            // 設置 session
            $_SESSION['email'] = $user['email'];
            $_SESSION['fullname'] = $user['name'];
            $_SESSION['login_success'] = "登入成功！";
            // 為了分辨是誰登入
            $_SESSION['role'] = 'merchant';


            // 取得 merchant 的 mId
            $mid = $user['mid'];

            // 跳轉到 index.php 並將 mId 傳遞作為查詢參數
            header("Location: /database/customer/index.php?mid=$mid");
            exit();
        } 
        else {
            header("Location: system_blog.php?wrongpw=Wrong password");
            exit();
        }
    }
    else{
        header("Location: system_blog.php?erroremail=No user with that email");
        exit();
    }
}
?>
