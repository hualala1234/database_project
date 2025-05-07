<?php
require '../../dbh.php';

// $dId = $_POST['deliveryPerson_id'];
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];
$address = $_POST['address'];
$imageURL = 'default-avatar.png';

// Check if dId or email already exists
$sql = "SELECT * FROM deliveryPerson WHERE  email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$dIdExists = false;
$emailExists = false;

while ($row = $result->fetch_assoc()) {
    // if ($row['dId'] == $dId) {
    //     $dIdExists = true;
    // }
    if ($row['email'] == $email) {
        $emailExists = true;
    }
}

// Handle different cases of duplicate dId and email
if ($dIdExists && $emailExists) {
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
} elseif ($dIdExists) {
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
                }, 4000); // 4000 毫秒＝4 秒
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
    $role = 'd'; // Default role for new users
    $sql = "INSERT INTO deliveryperson (dpName, email, password, dpAddress, dPicture, role)
        VALUES (?, ?, ?, ?, ?,?)";
    $stmt = $conn->prepare($sql);
    
    $stmt->bind_param("ssssss",  $fullname, $email, $password, $address, $imageURL, $role);

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
