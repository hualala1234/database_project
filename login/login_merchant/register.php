<?php
require 'db_connection.php';

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
    if ($row['email'] == $email) {
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
        $fileTmp  = $_FILES['image']['tmp_name'];
        $fileName = uniqid() . '-' . basename($_FILES['image']['name']);  // 只留檔名
        $targetDir = 'uploads/';
        // 把檔案移到 uploads/ 下
        if (move_uploaded_file($fileTmp, $targetDir . $fileName)) {
            $imageURL = $fileName;  // 存檔名到 DB
        } else {
            // 移動失敗，仍維持 default
            $imageURL = 'default-avatar.png';
        }
    } else {
        // 沒有上傳檔案，使用預設
        $imageURL = 'default-avatar.png';
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
