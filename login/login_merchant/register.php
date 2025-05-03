<?php
session_start();
require '../../dbh.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);


// $mId = $_POST['merchant_id'];
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];
$address = $_POST['address'];
$imageURL = 'default-avatar.png';

// Check if mId or email already exists
$sql = "SELECT * FROM merchant WHERE mEmail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// $mIdExists = false;
$emailExists = false;

while ($row = $result->fetch_assoc()) {
    // if ($row['mId'] == $mId) {
    //     $mIdExists = true;
    // }
    if ($row['mEmail'] == $email) {
        $emailExists = true;
    }
}

// Handle different cases of duplicate mId and email
if ( $emailExists) {
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
                }, 4000); // 4000 毫秒＝4 秒
            </script>
        ";
} else {
    // If no duplicates, proceed with the insert

    $imageURL = 'default-avatar.png'; // Default image if none uploaded

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

    // Insert the new user into the database
    $sql = "INSERT INTO merchant ( mName, mEmail, password, mAddress, mPicture)
            VALUES ( ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss",  $fullname, $email, $password, $address, $imageURL);

    if ($stmt->execute()) {
        echo "
            <h2 style='color: green;'>註冊成功！2 秒後將自動跳轉到登入頁面。</h2>
            <script>
                setTimeout(function() {
                    window.location.href = 'system_blog.php';
                }, 2000); // 2000 毫秒＝2 秒
            </script>
        ";
        exit;
    } else {
        echo "<h2 style='color: red;'>註冊失敗：" . $stmt->error . "</h2>";
    }
}

$stmt->close();
?>
