<?php
session_start();
include ('../../walletAndrecord/connect.php');  
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cid = isset($_SESSION["cid"]) ? $_SESSION["cid"] : '';

// 呼叫 Python 並傳 cid
// $command = "python ../flappyBirds/flappy_bird/flappy_bird_ans.py $cid";
$python = "/Users/ba/anaconda3/bin/python3";
$script = "/Applications/XAMPP/xamppfiles/htdocs/database_project/flappyBirds/flappy_bird/flappy_bird_ans.py
";

$command = "\"$python\" \"$script\" $cid";
$output = shell_exec($command . " 2>&1");
$_SESSION['flappy_log'] = "執行命令：$command\n回傳：\n$output";
echo "<pre>$output</pre>";

// 將輸出訊息存入 session
$_SESSION['flappy_log'] = $output;

// 導回 my_coupons.php 顯示結果
// header("Location: my_coupons.php");
header("Location: /database_project/claw_machine/my_coupons.php");
exit;

// 呼叫 Python 程式
// exec("python ../flappyBirds/flappy_bird/flappy_bird_ans.py");
?>