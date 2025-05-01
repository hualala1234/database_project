<?php
session_start();

if (isset(($_SESSION['email']))) {
    echo "<p>Welcome, " . $_SESSION['fullname'] . "!</p>";
    echo "<a href='logout.php'>登出</a>";
} else {
    echo "<p>請先註冊。</p>";
}
?>
