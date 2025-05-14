<?php
session_start();
require '../dbh.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cid = $_SESSION['cid'] ?? '';

$tranId = $_POST['tranId'];
$mRating = isset($_POST['mRating']) && $_POST['mRating'] !== '' ? (float)$_POST['mRating'] : null;
$mComment = $_POST['mComment'] ?? null;
$dRating = isset($_POST['dRating']) && $_POST['dRating'] !== '' ? (float)$_POST['dRating'] : null;
$dComment = $_POST['dComment'] ?? null;

$pids = $_POST['pids'] ?? [];
$pRatings = $_POST['pRating'] ?? [];
$pComments = $_POST['pComment'] ?? [];

// 更新 Transaction 表，並順便把 orderStatus 設為 'complete'
$tranSql = "UPDATE Transaction SET mRating=?, mComment=?, dRating=?, dComment=?, orderStatus='complete' WHERE tranId=?";
$tranStmt = $conn->prepare($tranSql);

$mr = $mRating;
$mc = $mComment;
$dr = $dRating;
$dc = $dComment;
$tid = $tranId;

$tranStmt->bind_param("dsssi", $mr, $mc, $dr, $dc, $tid);
$tranStmt->execute();

// 更新 Record 表
$recordSql = "UPDATE Record SET pRating=?, pComment=? WHERE tranId=? AND pid=?";
$recordStmt = $conn->prepare($recordSql);

for ($i = 0; $i < count($pids); $i++) {
    $pid = $pids[$i];
    $rating = isset($pRatings[$pid]) && $pRatings[$pid] !== '' ? (float)$pRatings[$pid] : null;
    $comment = $pComments[$pid] ?? null;

    // 用變數承接以避免 bind_param 出錯
    $pr = $rating;
    $pc = $comment;
    $tid = $tranId;
    $pd = $pid;

    $recordStmt->bind_param("dsii", $pr, $pc, $tid, $pd);
    $recordStmt->execute();
}

header("Location: index.php?cid=$cid");
exit;
?>
