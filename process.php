<!-- 處理商店資訊編輯 -->
<?php
if (isset($_POST["updateMerchant"])) {
    include("dbh.php");

    $mid = mysqli_real_escape_string($conn, $_POST["mid"]);
    $mName = mysqli_real_escape_string($conn, $_POST["mName"]);
    $mAddress = mysqli_real_escape_string($conn, $_POST["mAddress"]);
    $businessHours = mysqli_real_escape_string($conn, $_POST["businessHours"]);
    $mPicture = ''; // 預設為空

    // 圖片處理
    $file = $_FILES['ImageUpload']['name'];
    $tempname = $_FILES['ImageUpload']['tmp_name'];
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if (!empty($file)) {
        $new_filename = time() . '-' . uniqid() . '.' . $file_extension;
        $upload_folder = "upload_images/" . $new_filename;

        if (in_array($file_extension, $allowed_extensions)) {
            if (move_uploaded_file($tempname, $upload_folder)) {
                $mPicture = $upload_folder;
            } else {
                echo "圖片上傳失敗，可能是權限問題或檔案過大。";
                exit;
            }
        } else {
            echo "檔案格式錯誤，只允許 JPG、JPEG、PNG。";
            exit;
        }
    }

    // 更新餐廳分類
    if (isset($_POST['restaurantCategories'])) {
        $newCategories = $_POST['restaurantCategories'];

        // 先清除餐廳的所有類別
        mysqli_query($conn, "DELETE FROM RestaurantCategories WHERE mid = $mid");

        // 再新增新的類別
        foreach ($newCategories as $categoryId) {
            $categoryId = mysqli_real_escape_string($conn, $categoryId);
            $sqlInsert = "INSERT INTO RestaurantCategories (mid, categoryId) 
                          VALUES ('$mid', '$categoryId')";
            mysqli_query($conn, $sqlInsert);
        }
    }

    // 成功跳轉
    header("Location: merchant/merchant_shop.php?mid=$mid");
    exit();
}
?>


<!-- 商品編輯 -->

<?php
// 顯示錯誤訊息（避免 HTTP 500 問題時無法顯示原因）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['updateProduct'])) {
    include("dbh.php");

    $pid = $_POST['pid'];
    $mid = $_POST['mid'];
    $pName = mysqli_real_escape_string($conn, $_POST['pName']);
    $pDescription = mysqli_real_escape_string($conn, $_POST['pDescription']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $productCategoriesId = $_POST['productCategoryId'];  // 這是來自 ProductCategoryList 的 foreign key
    $pPicture = '';
    $deleteMessage = ''; // 用來儲存刪除圖片的訊息

    // 檢查選擇的 category 是否有效
    $checkCategorySql = "SELECT productCategoriesId FROM ProductCategoryList WHERE productCategoriesId = '$productCategoriesId' AND mid = '$mid'";
    $categoryResult = mysqli_query($conn, $checkCategorySql);

    if (mysqli_num_rows($categoryResult) == 0) {
        echo "選擇的類別無效。";
        exit;
    }

    // 處理圖片上傳
    if (!empty($_FILES['ImageUpload']['name'])) {
        $file = $_FILES['ImageUpload']['name'];
        $tempname = $_FILES['ImageUpload']['tmp_name'];
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = time() . '-' . uniqid() . '.' . $file_extension;
            $upload_folder = "upload_images/" . $new_filename;

            if (move_uploaded_file($tempname, $upload_folder)) {
                $pPicture = $upload_folder;
            } else {
                echo "圖片上傳失敗，可能是權限問題或檔案過大。";
                exit;
            }
        } else {
            echo "檔案格式錯誤，只允許 JPG、JPEG、PNG。";
            exit;
        }
    } elseif ($_POST['deleteImage'] === 'yes') {
        // 如果刪除圖片，將圖片欄位設為空並刪除原圖片
        $sqlCurrentImage = "SELECT pPicture FROM Product WHERE pid = $pid";
        $resultCurrentImage = mysqli_query($conn, $sqlCurrentImage);
        $currentImage = mysqli_fetch_assoc($resultCurrentImage)['pPicture'];

        // 刪除伺服器上的圖片文件
        if (!empty($currentImage) && file_exists($currentImage)) {
            if (unlink($currentImage)) {
                $deleteMessage = "圖片已成功刪除。"; // 儲存刪除訊息
            } else {
                $deleteMessage = "圖片刪除失敗，可能是權限問題。"; // 儲存刪除失敗訊息
            }
        }

        // 清除資料庫中的圖片欄位
        $pPicture = ''; // 設為空字符串
    }

    // 更新 Product 資料
    $sql = "UPDATE Product SET pName = '$pName', price = '$price', pDescription = '$pDescription'";
    if (!empty($pPicture)) {
        $sql .= ", pPicture = '$pPicture'"; // 更新圖片欄位
    } else {
        $sql .= ", pPicture = NULL"; // 如果刪除圖片，設為 NULL
    }
    $sql .= " WHERE pid = $pid AND mid = $mid";

    // 執行更新資料庫
    if (!mysqli_query($conn, $sql)) {
        echo "更新商品錯誤：" . mysqli_error($conn);
        exit;
    }

    // 更新或新增 ProductCategories 關聯
    $checkSql = "SELECT * FROM ProductCategories WHERE pid = $pid";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        // 更新現有的類別
        $sqlCategory = "UPDATE ProductCategories SET productCategoriesId = '$productCategoriesId' WHERE pid = $pid";
    } else {
        // 插入新類別
        $sqlCategory = "INSERT INTO ProductCategories (productCategoriesId, pid, mid) VALUES ('$productCategoriesId', '$pid', '$mid')";
    }

    // 執行類別更新
    if (!mysqli_query($conn, $sqlCategory)) {
        echo "更新類別錯誤：" . mysqli_error($conn);
        exit;
    }

    // 顯示刪除訊息
    if (!empty($deleteMessage)) {
        echo $deleteMessage; // 顯示刪除訊息
    }

    // 如果成功，重新導向到菜單頁面
    header("Location: merchant/menu.php?mid=$mid");
    exit();
}
?>








<!-- 更改商品類別 -->
<?php
if (isset($_POST['updateCategory'])) {
    include("dbh.php");

    $mid = mysqli_real_escape_string($conn, $_POST['mid']);
    $productCategoriesId = mysqli_real_escape_string($conn, $_POST['productCategoriesId']);
    $productCategoryName = mysqli_real_escape_string($conn, $_POST['productCategoryName']);

    // 更新 ProductCategoryList 表中的分類名稱
    $sql = "UPDATE ProductCategoryList 
            SET productCategoryName = '$productCategoryName' 
            WHERE productCategoriesId = $productCategoriesId AND mid = $mid";

    if (mysqli_query($conn, $sql)) {
        // 更新成功後重新導向到菜單頁面
        header("Location: merchant/menu.php?mid=$mid");
        exit();
    } else {
        echo "更新錯誤：" . mysqli_error($conn);
    }
}
?>

<!-- 新增商品類別 -->
<?php
include('dbh.php'); // 連接資料庫

if (isset($_POST['productCategoryName']) && isset($_POST['mid'])) {
    $mid = mysqli_real_escape_string($conn, $_POST['mid']);
    $productCategoryName = mysqli_real_escape_string($conn, $_POST['productCategoryName']);

    // 查詢該 mid 下目前最大 sort_order
    $query = "SELECT IFNULL(MAX(sort_order), 0) AS max_order 
              FROM ProductCategoryList 
              WHERE mid = '$mid'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $newSortOrder = $row['max_order'] + 1;

    // 插入新的分類名稱，同時加入新的 sort_order
    $sql = "INSERT INTO ProductCategoryList (productCategoryName, mid, sort_order) 
            VALUES ('$productCategoryName', '$mid', $newSortOrder)";

    if (mysqli_query($conn, $sql)) {
        // 插入成功後返回商店頁面
        header("Location: merchant/menu.php?mid=$mid");
        exit();
    } else {
        echo "錯誤: " . mysqli_error($conn);
    }
}
?>



<!-- 新增商品 -->
<?php
include('dbh.php'); // 連接資料庫

if (isset($_POST['pName'], $_POST['price'], $_POST['productCategoryId'], $_POST['pDescription'])) {
    $mid = mysqli_real_escape_string($conn, $_POST['mid']);
    $pName = mysqli_real_escape_string($conn, $_POST['pName']);
    $pDescription = mysqli_real_escape_string($conn, $_POST['pDescription']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $productCategoryId = mysqli_real_escape_string($conn, $_POST['productCategoryId']);
    $pPicture = '';

    // 有上傳圖片才處理
    if (!empty($_FILES["productImage"]["name"])) {
        $targetDir = "upload_images/";
        $fileName = basename($_FILES["productImage"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // 只允許圖片格式
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFile)) {
                $pPicture = $targetFile;
            } else {
                echo "圖片上傳失敗";
                exit();
            }
        } else {
            echo "只允許上傳圖片文件（JPG, JPEG, PNG, GIF）";
            exit();
        }
    }

    // 插入產品資料到 Product 表
    $sqlInsertProduct = "INSERT INTO Product (mid, pName, pDescription, price, pPicture) 
                         VALUES ('$mid', '$pName', '$pDescription', '$price', '$pPicture')";

    if (mysqli_query($conn, $sqlInsertProduct)) {
        // 獲取剛插入的 pid
        $pid = mysqli_insert_id($conn);

        // 插入產品分類資料到 ProductCategories 表
        $sqlInsertCategory = "INSERT INTO ProductCategories (pid, productCategoriesId) 
                              VALUES ('$pid', '$productCategoryId')";
        if (mysqli_query($conn, $sqlInsertCategory)) {
            // 成功後，重定向回商店頁面
            header("Location: merchant/menu.php?mid=$mid");
            exit();
        } else {
            echo "錯誤: " . mysqli_error($conn);
        }
    } else {
        echo "錯誤: " . mysqli_error($conn);
    }
}
?>


<!-- 刪除商品 -->
<?php
if (isset($_POST['deleteProduct'])) {
    include("dbh.php");
    $pid = $_POST['pid'];
    $mid = $_POST['mid'];

    // 先刪除 ProductCategories 對應的資料（避免 FK 錯誤）
    $sqlDeleteCategory = "DELETE FROM ProductCategories WHERE pid = $pid";
    mysqli_query($conn, $sqlDeleteCategory);

    // 再刪除 Product 資料
    $sqlDeleteProduct = "DELETE FROM Product WHERE pid = $pid AND mid = $mid";

    if (mysqli_query($conn, $sqlDeleteProduct)) {
        header("Location: merchant/menu.php?mid=$mid");
        exit();
    } else {
        echo "刪除失敗：" . mysqli_error($conn);
    }
}
?>

<!-- 刪除商品類別 -->
<?php
if (isset($_POST['deleteCategory'])) {
    include("dbh.php");

    $productCategoriesId = intval($_POST['productCategoriesId']); // 保險轉型
    $mid = intval($_POST['mid']);

    // 第一步：找出這個類別底下所有產品的 pid
    $sqlGetPids = "SELECT pid FROM ProductCategories WHERE productCategoriesId = $productCategoriesId";
    $resultPids = mysqli_query($conn, $sqlGetPids);

    if ($resultPids && mysqli_num_rows($resultPids) > 0) {
        while ($row = mysqli_fetch_assoc($resultPids)) {
            $pid = $row['pid'];

            // 第二步：刪掉 Product 表中這些 pid 的商品
            $sqlDeleteProduct = "DELETE FROM Product WHERE pid = $pid";
            mysqli_query($conn, $sqlDeleteProduct);
        }
    }

    // 第三步：刪掉 ProductCategories 表中這個類別底下的所有連結
    $sqlDeleteProductCategories = "DELETE FROM ProductCategories WHERE productCategoriesId = $productCategoriesId";
    mysqli_query($conn, $sqlDeleteProductCategories);

    // 第四步：刪掉 ProductCategoryList 表中的類別本身
    $sqlDeleteCategoryList = "DELETE FROM ProductCategoryList WHERE productCategoriesId = $productCategoriesId";
    mysqli_query($conn, $sqlDeleteCategoryList);

    // 最後回到原本的頁面
    header("Location: merchant/menu.php?mid=$mid");
    exit();
}
?>

<!-- 更改商品類別排序 -->
<?php
include "dbh.php"; // 你的資料庫連線

if (isset($_POST['productCategoryName']) && isset($_POST['mid'])) {
    $name = mysqli_real_escape_string($conn, $_POST['productCategoryName']);
    $mid = intval($_POST['mid']);

    // 查詢該 mid 下的最大 sort_order
    $query = "SELECT IFNULL(MAX(sort_order), 0) AS max_order FROM ProductCategoryList WHERE mid = $mid";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $newSortOrder = $row['max_order'] + 1;

    // 新增資料
    $insertSql = "INSERT INTO ProductCategoryList (productCategoryName, mid, sort_order) 
                  VALUES ('$name', $mid, $newSortOrder)";
    
    if (mysqli_query($conn, $insertSql)) {
        echo "新增成功，sort_order = $newSortOrder";
    } else {
        echo "錯誤：" . mysqli_error($conn);
    }
} else {
    echo "缺少必要參數。";
}
?>
