<?php
session_start();

// 清除所有 session 資料
session_unset();
session_destroy();

// 跳轉回登入頁面

header("Location: /database_project/login/before_login.php");
exit();
?>
