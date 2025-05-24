<?php
session_start();
include '../dbh.php'; // ⚠️ 修改為你的資料庫連線檔案路徑
file_put_contents('log.txt', var_export($_SESSION, true));


header('Content-Type: application/json');

// 未登入者禁止操作
if (!isset($_SESSION['cid'])) {
    echo json_encode(['success' => false, 'message' => '未登入']);
    exit;
}

$cid = intval($_SESSION['cid']);
$mid = intval($_POST['mid'] ?? 0);

if ($mid === 0) {
    echo json_encode(['success' => false, 'message' => '缺少商家 ID']);
    exit;
}

// 檢查是否已經收藏
$checkStmt = $conn->prepare("SELECT 1 FROM Favorite WHERE cid = ? AND mid = ?");
$checkStmt->bind_param("ii", $cid, $mid);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // 已收藏 → 移除收藏
    $deleteStmt = $conn->prepare("DELETE FROM Favorite WHERE cid = ? AND mid = ?");
    $deleteStmt->bind_param("ii", $cid, $mid);
    $deleteStmt->execute();
    $favorited = false;
} else {
    // 未收藏 → 新增收藏
    $insertStmt = $conn->prepare("INSERT INTO Favorite (cid, mid) VALUES (?, ?)");
    $insertStmt->bind_param("ii", $cid, $mid);
    $insertStmt->execute();
    $favorited = true;
}

// 重新查詢收藏次數
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM Favorite WHERE mid = ?");
$countStmt->bind_param("i", $mid);
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$favoritesCount = $countResult['total'] ?? 0;

// 回傳 JSON 結果
echo json_encode([
    'success' => true,
    'favorited' => $favorited,
    'favoritesCount' => $favoritesCount
]);

if ($checkStmt->num_rows > 0) {
    $deleteStmt = $conn->prepare("DELETE FROM Favorite WHERE cid = ? AND mid = ?");
    $deleteStmt->bind_param("ii", $cid, $mid);
    if (!$deleteStmt->execute()) {
        echo json_encode(['success' => false, 'message' => '移除失敗: ' . $deleteStmt->error]);
        exit;
    }
    $favorited = false;
} else {
    $insertStmt = $conn->prepare("INSERT INTO Favorite (cid, mid) VALUES (?, ?)");
    $insertStmt->bind_param("ii", $cid, $mid);
    if (!$insertStmt->execute()) {
        echo json_encode(['success' => false, 'message' => '新增失敗: ' . $insertStmt->error]);
        exit;
    }
    $favorited = true;
}

