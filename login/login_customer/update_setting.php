<?php
// 資料庫連線設定
$host = 'localhost';
$dbname = 'database';
$username = 'root';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid = $_POST['cid'];
    $field = $_POST['field'];
    $newValue = $_POST['newValue'];
    $extraId = $_POST['extraId'] ?? null; // 用於地址編輯

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 👉 地址新增
        if ($field === 'add_address') {
            $stmt = $pdo->prepare("INSERT INTO cAddress (cid, address_text) VALUES (:cid, :address_text)");
            $stmt->execute(['cid' => $cid, 'address_text' => $newValue]);

        // 👉 地址編輯
        } elseif ($field === 'edit_address') {
            if (!$extraId) {
                die("缺少地址 ID");
            }
            $stmt = $pdo->prepare("UPDATE cAddress SET address_text = :address_text WHERE id = :id AND cid = :cid");
            $stmt->execute([
                'address_text' => $newValue,
                'id' => $extraId,
                'cid' => $cid
            ]);

        // 👉 一般 customer 欄位編輯
        } elseif (in_array($field, ['cName', 'email', 'phone', 'password', 'birthday'])) {
            $stmt = $pdo->prepare("UPDATE customer SET $field = :value WHERE cid = :cid");
            $stmt->execute(['value' => $newValue, 'cid' => $cid]);

        // ❌ 不允許的欄位
        } else {
            die("不允許的欄位");
        }

        // ✅ 成功後導回設定頁
        header("Location: setting.php");
        exit;

    } catch (PDOException $e) {
        die("更新失敗: " . $e->getMessage());
    }
}
?>
