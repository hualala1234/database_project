<?php
require 'db_connection.php';

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
                }, 4000); // 4000 毫秒＝4 秒
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
        $targetDir = 'uploads/';
        $imageURL = $targetDir . $fileName;
        move_uploaded_file($fileTmp, $imageURL);
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
    $sql = "INSERT INTO customer ( cName, email, password, address, introducer, )
            VALUES ( ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssis", $fullname, $email, $password, $address, $introducer, $imageURL);

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
