<?php
session_start();


if (!isset($_SESSION['cid'])) {
    echo "請先登入再提交資料。";
    exit();
}


// 1. 資料庫連線設定
$host = 'localhost';
$dbname = 'database';
$username = 'root';
$password = '';


try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // 2. 取得登入中的客戶 ID
    $customer_id = $_SESSION['cid'];


    // 3. 接收過敏原資料
    $allergens = isset($_POST['allergens']) ? $_POST['allergens'] : [];
    $other_allergen = isset($_POST['other_allergen']) ? trim($_POST['other_allergen']) : '';


    // 4. 合併過敏原清單
    $allergy_data = implode(',', $allergens);
    // if (!empty($other_allergen)) {
    //     $allergy_data .= ',' . $other_allergen;
    // }


    // 5. 寫入資料表（若已存在就更新）
    $stmt = $conn->prepare("
        INSERT INTO allergy (cid, allergens, other_allergen)
        VALUES (:cid, :allergens, :other_allergen)
        ON DUPLICATE KEY UPDATE 
            allergens = :allergens_update,
            other_allergen = :other_allergens_update

    ");


    $stmt->execute([
        ':cid' => $customer_id,
        ':allergens' => implode(',', $allergens), // 只放勾選的
        ':other_allergen' => $other_allergen,
        ':allergens_update' => implode(',', $allergens),
        ':other_allergens_update' => $other_allergen
    ]);


    echo "過敏資訊已成功儲存！";
    echo "<script>
        setTimeout(function() {
            window.location.href = 'allergy.php'; // 請將 index.php 改成你的首頁檔案名稱
        }, 1500);
    </script>";



} catch (PDOException $e) {
    echo "儲存失敗：" . $e->getMessage();
}
?>
