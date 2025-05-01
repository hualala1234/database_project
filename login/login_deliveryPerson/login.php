<?php
include "../../dbh.php";
session_start();
$wrong_password = "Enter your password";
$error_email = "Enter your email";

if ($_SERVER["REQUEST_METHOD"]=="POST"){

    $email=mysqli_real_escape_string($conn,$_POST['email']);
    $password=mysqli_real_escape_string($conn,$_POST['password']);

    //fetch database
    $sql="SELECT * FROM deliveryPerson WHERE email = '$email'";
    $result=$conn->query($sql);
    //只有找到使用者時才檢查密碼
    if ($result->num_rows > 0) {
        // Check if password is correct
        $user = $result->fetch_assoc();

        if ($password == $user['password']) {
            $did = $user['did'];
            $_SESSION['fullname'] = $user['name'];
            $_SESSION['login_success'] = "登入成功！";

            $_SESSION['role'] = 'delivery_person';
            // 跳回原本頁面
            header("Location: /database/customer/index.php?did=$did");
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
