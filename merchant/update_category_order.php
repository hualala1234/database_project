<?php
include "../dbh.php";

if (isset($_POST['order']) && is_array($_POST['order'])) {
    $order = $_POST['order'];

    foreach ($order as $index => $categoryId) {
        $sortOrder = $index + 1;
        $categoryId = intval($categoryId);
        $sql = "UPDATE ProductCategoryList SET sort_order = $sortOrder WHERE productCategoriesId = $categoryId";
        mysqli_query($conn, $sql);
    }

    echo "排序更新成功";
} else {
    echo "無效的資料格式";
}
?>
