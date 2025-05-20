<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../dbh.php';

// $cId = $_POST['customer_id'];
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];
$address = $_POST['address'];
$introducer = $_POST['introducer'];
$imageURL = 'default-avatar.png';

// Check if cId or email already exists
$sql = "SELECT * FROM customer WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$cIdExists = false;
$emailExists = false;

while ($row = $result->fetch_assoc()) {
    // if ($row['cId'] == $cId) {
    //     $cIdExists = true;
    // }
    if ($row['email'] == $email) {
        $emailExists = true;
    }
}

// Handle different cases of duplicate cId and email
if ($cIdExists && $emailExists) {
    echo "
        <div style='border: 2px solid red; padding: 10px; color: red;'>
            <strong>此email已被註冊</strong>
            <strong>4秒後返回登入頁面</strong>
        </div>
    ";
    echo "
            <script>
                setTimeout(function() {
                    window.history.back();
                }, 2000); // 2000 毫秒＝2 秒
            </script>
        ";
} elseif ($cIdExists) {
    echo "
        <div style='border: 2px solid red; padding: 10px; color: red;'>
            <strong>此使用者id已被註冊</strong>
            <strong>4秒後返回登入頁面</strong>
        </div>
    ";
    echo "
            <script>
                setTimeout(function() {
                    window.history.back();
                }, 2000); // 2000 毫秒＝2 秒
            </script>
        ";
} elseif ($emailExists) {
    echo "
        <div style='border: 2px solid red; padding: 10px; color: red;'>
            <strong>此email已被註冊</strong>
            <strong>4秒後返回登入頁面</strong>
        </div>
    ";
    echo "
            <script>
                setTimeout(function() {
                    window.history.back();
                }, 2000); // 2000 毫秒＝2 秒
            </script>
        ";
} else {
    // If no duplicates, proceed with the insert

    $imageURL = ''; // Default image if none uploaded

    // Image upload (if any)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = uniqid() . '-' . $_FILES['image']['name'];
    
        $uploadDir = realpath(__DIR__ . '/../../upload_images'); // 實體伺服器路徑
        $savePath = $uploadDir . '/' . $fileName;                 // 真正儲存位置
        $imageURL = 'upload_images/' . $fileName;                 // 儲存在資料庫中 → 用於網頁顯示
    
        // 確保資料夾存在
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    
        if (!move_uploaded_file($fileTmp, $savePath)) {
            echo "<h3 style='color:red;'>圖片移動失敗</h3>";
            $imageURL = 'default-avatar.png';
        }
    }
    
    

    $introducer = isset($_POST['introducer']) && trim($_POST['introducer']) !== '' ? (int)$_POST['introducer'] : null;

    // 若不是 NULL，檢查是否存在於 customer 表
    if (!is_null($introducer)) {
        $checkIntro = $conn->prepare("SELECT cid FROM customer WHERE cid = ?");
        $checkIntro->bind_param("i", $introducer);
        $checkIntro->execute();
        $introResult = $checkIntro->get_result();
        if ($introResult->num_rows === 0) {
            $introducer = null; // introducer 不存在 → 設為 NULL
        }
        $checkIntro->close();
    }

    // Insert the new user into the database
    $role = 'c';
    $sql = "INSERT INTO customer ( cName, email, password, address, introducer, imageURL, role)
            VALUES ( ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiss", $fullname, $email, $password, $address, $introducer, $imageURL, $role);

    if ($stmt->execute()) {
        $new_user_id = $conn->insert_id; // 自動取得新註冊的使用者 cid
        echo "
            <h2 style='color: green;'>註冊成功！將開始拍攝人臉以進行辨識。</h2>
            <script>
                setTimeout(function() {
                    window.location.href = '../../face_login_project/register_face.html?cid={$new_user_id}';
                }, 1500);
            </script>
        ";
        exit;
    } else {
        echo "<h2 style='color: red;'>註冊失敗：" . $stmt->error . "</h2>";
    }
}

$stmt->close();
?>
<!-- <h2 style='color: green;'>註冊成功！2 秒後將自動跳轉到登入頁面。</h2>
<script>
    setTimeout(function() {
        window.location.href = 'system_blog.php';
    }, 2000); // 2000 毫秒＝2 秒
</script> -->