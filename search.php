<?php
include('dbh.php'); // 資料庫連接

// 檢查是否有傳遞關鍵字
if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']); // 防止 SQL 注入

    // 查詢符合關鍵字的類別
    $categoryQuery = "SELECT * FROM Categories WHERE categoryName LIKE '%$keyword%'";
    $categoryResult = mysqli_query($conn, $categoryQuery);

    // 查詢符合關鍵字的商品
    $productQuery = "SELECT * FROM Products WHERE productName LIKE '%$keyword%'";
    $productResult = mysqli_query($conn, $productQuery);

    // 顯示類別結果
    echo "<h3>相關類別</h3>";
    echo "<div class='row'>";
    while ($category = mysqli_fetch_assoc($categoryResult)) {
        echo "<div class='col-md-4'>
                <div class='category-card'>
                    <h4>" . htmlspecialchars($category['categoryName']) . "</h4>
                </div>
              </div>";
    }
    echo "</div>";

    // 顯示商品結果
    echo "<h3>相關商品</h3>";
    echo "<div class='row'>";
    while ($product = mysqli_fetch_assoc($productResult)) {
        echo "<div class='col-md-4'>
                <div class='product-card'>
                    <h4>" . htmlspecialchars($product['productName']) . "</h4>
                    <p>價格：" . htmlspecialchars($product['price']) . "</p>
                </div>
              </div>";
    }
    echo "</div>";
}
?>
