<?php
    include ('../dbh.php');
    
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Junglebite</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://kit.fontawesome.com/ea478a1bc4.js" crossorigin="anonymous"></script>

        <!-- Libraries Stylesheet -->
        <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

        <link href="../css/style.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="../css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="../css/style.css" rel="stylesheet">

        <!-- Jquery 連結 -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="search.js" type ="text/javascript"></script>
    </head>

    <body>

        <!-- Spinner Start -->
        <!-- <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" role="status"></div>
        </div> -->
        <!-- Spinner End -->

        <?php
        if(isset($_GET["mid"])){
            $mid = $_GET["mid"];
            $sql = "SELECT * FROM Merchant WHERE mid = $mid";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($result);
        }
        
        ?>
        <!-- Navbar start -->
        <div class="container-fluid fixed-top">
            <div class="container topbar bg-primary d-none d-lg-block">
                <div class="d-flex justify-content-between">
                    <div class="top-info ps-2">
                    <!-- <i class="fas fa-map-marker-alt me-2 text-secondary"></i> <a href="#" class="text-white">客戶住址</a> -->
                        <!-- <small class="me-3"><i class="fas fa-envelope me-2 text-secondary"></i><a href="#" class="text-white">Email@Example.com</a></small> -->
                    </div>
                    <!-- <div class="top-link pe-2">
                        <a href="#" class="text-white"><small class="text-white mx-2">Privacy Policy</small>/</a>
                        <a href="#" class="text-white"><small class="text-white mx-2">Terms of Use</small>/</a>
                        <a href="#" class="text-white"><small class="text-white ms-2">Sales and Refunds</small></a>
                    </div> -->
                </div>
            </div>
            <div class="container px-0">
                <nav class="navbar navbar-light bg-white navbar-expand-xl">
                    <a href="index.html" class="navbar-brand"><h1 class="text-primary display-6">Junglebite商家</h1></a>
                    <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars text-primary"></span>
                    </button>
                    <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                        <div class="navbar-nav mx-auto">
                            <!-- <a href="index.php" class="nav-item nav-link active">Home</a> -->
                            <a href="merchant_shop.php?mid=<?php echo $mid; ?>" class="nav-item nav-link">店面資訊</a>
                            <a href="menu.php?mid=<?php echo $mid; ?>" class="nav-item nav-link">菜單管理</a>
                            <a href="shop-detail.html" class="nav-item nav-link">訂單</a>
                            <!-- <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                                <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                    <a href="cart.html" class="dropdown-item">Cart</a>
                                    <a href="chackout.html" class="dropdown-item">Chackout</a>
                                    <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                                    <a href="404.html" class="dropdown-item">404 Page</a>
                                </div>
                            </div> -->
                            <a href="contact.html" class="nav-item nav-link">聯繫平台</a>
                        </div>
                        <div class="d-flex m-3 me-0">
                            <!-- <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search text-primary"></i></button> -->
                            <!-- <a href="#" class="position-relative me-4 my-auto">
                                <i class="fa fa-shopping-bag fa-2x"></i>
                                <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 15px; height: 20px; min-width: 20px;">3</span>
                            </a> -->
                            <a href="#" class="my-auto">
                                <i class="fas fa-user fa-2x"></i>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Navbar End -->
        <div class="container-fluid fruite py-5" style=" position: absolute; top: 5rem;">
        
            <div class="container py-5">
                <div class="tab-class text-center">
                    <div class="row g-4" style=" display:flex;">
                        <div class="col-lg-4 text-start w-100" style=" display:flex; align-items: center;">
                            <h2>菜單管理</h2>
                            
                            
                            <form action="" method="get" class="mx-5 position-relative" style="display: flex; align-items: center;">
                                <input 
                                    class="form-control border-2 border-secondary py-3 px-4 rounded-pill" 
                                    type="text" 
                                    name="keyword" 
                                    placeholder="Search" 
                                    style="width: 40rem;"
                                    id="myInput"
                                >
                                <button 
                                    type="button" 
                                    class=" py-3 px-4 rounded-pill text-white h-100 position-absolute" 
                                    style="margin-left: 0.5rem; right: -5rem; background-color: #81C408; border:#81C408;"
                                >
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </form>              
                        </div>
                        <div style="display:flex;">
                            <button style="margin-right: 0.5rem;" type="button" class="btn btn-secondary border-2 border-secondary py-3 px-3  text-white h-100" data-bs-toggle="modal" data-bs-target="#addCategoryModal"><i class="fa-solid fa-plus"></i> 新增類別</button>
                            <button style="margin-left:0.7rem; background-color: #ff5d6d;" type="button" class="btn border-2 border-secondary py-3 px-3  text-white h-100" data-bs-toggle="modal" data-bs-target="#addProductModal"><i style="margin-right:0.2rem;" class="fa-solid fa-plus"></i>新增商品</button>

                        </div>
                        <div id="addp" style="display:none;"></div>

                        

                        <!-- 新增類別表單 -->
                        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addCategoryModalLabel">新增類別</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="../process.php" method="POST">
                                            <input type="hidden" name="mid" value="<?php echo $mid; ?>">

                                            <div class="form-group">
                                                <label for="productCategoryName">類別名稱</label>
                                                <input type="text" class="form-control" id="productCategoryName" name="productCategoryName" required placeholder="輸入類別名稱">
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary mt-3">儲存</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 新增商品表單 -->
                        <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                            <div class="modal-dialog" style="max-width: 50rem;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addProductModalLabel">新增商品</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="../process.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="mid" value="<?php echo $mid; ?>">

                                            <!-- 類別名稱下拉選單 -->
                                            <div class="form-group py-3">
                                                <div style="display:flex; justify-content: center;">
                                                    <label for="pName">選擇類別</label>
                                                    <p style="color:red; margin:0;">*</p>

                                                </div>
                                                
                                                <select class="form-control" id="productCategoryName" name="productCategoryId" required>
                                                    <option value="">-- 請選擇類別 --</option>
                                                    <?php
                                                        // 查詢所有的類別名稱
                                                        $sqlCategories = "SELECT productCategoriesId, productCategoryName FROM ProductCategoryList WHERE mid = $mid";
                                                        $resultCategories = mysqli_query($conn, $sqlCategories);
                                                        while ($category = mysqli_fetch_assoc($resultCategories)) {
                                                            echo "<option value='{$category['productCategoriesId']}'>{$category['productCategoryName']}</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <!-- 商品名稱 -->
                                            <div class="form-group py-3">
                                                <div style="display:flex; justify-content: center;">
                                                    <label for="pName">商品名稱</label>
                                                    <p style="color:red; margin:0;">*</p>

                                                </div>

                                                
                                                <input type="text" class="form-control" id="pName" name="pName" required placeholder="輸入商品名稱">
                                            </div>

                                            <!-- 商品敘述 -->
                                            <div class="form-group py-3">
                                                
                                                <label for="pDescription">商品敘述</label>
                                                <textarea class="form-control" id="pDescription" name="pDescription" rows="3" placeholder="輸入商品敘述"></textarea>
                                            </div>

                                            <!-- 商品價格 -->
                                            <div class="form-group py-3">
                                                <div style="display:flex; justify-content: center;">
                                                    <label for="pName">價錢</label>
                                                    <p style="color:red; margin:0;">*</p>

                                                </div>
                                                
                                                <input type="number" class="form-control" id="price" name="price" required placeholder="輸入價錢">
                                            </div>

                                            <!-- 上傳商品照片 -->
                                            <div class="form-group py-3">
                                                <label for="productImage">商品圖片</label>
                                                <input type="file" class="form-control" id="productImage" name="productImage">
                                            </div>

                                            <button type="submit" class="btn btn-primary mt-3">儲存</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                    
                    <?php
                        if(isset($_GET["mid"])){
                            $mid = $_GET["mid"];
                            
                            // 查詢 ProductCategoryList 表中符合 mid 的所有 productCategoryName 和 productCategoriesId
                            $sqlCategoryList = "SELECT productCategoryName, productCategoriesId 
                                                FROM ProductCategoryList 
                                                WHERE mid = $mid";
                            $resultCategoryList = mysqli_query($conn, $sqlCategoryList);

                            if ($resultCategoryList && mysqli_num_rows($resultCategoryList) > 0) {
                                while ($category = mysqli_fetch_assoc($resultCategoryList)) {
                                    $categoryName = $category['productCategoryName'];
                                    $productCategoriesId = $category['productCategoriesId'];

                                    echo '
                                    <div class="gap-1" style="display: flex; align-items: flex-end;">
                                        <a style="font-size:1.5rem!important;" 
                                        class="btn btn-primary category-toggle" 
                                        data-bs-toggle="collapse" 
                                        href="#collapse_' . $productCategoriesId . '" 
                                        id="category'. $productCategoriesId . '"
                                        role="button" 
                                        aria-expanded="true" 
                                        aria-controls="collapse_' . $productCategoriesId . '" 
                                        data-category="' . $categoryName . '">
                                            <span class="arrow"></span>
                                            <span class="category-name">' . htmlspecialchars($categoryName) . '</span>
                                        </a>
                                        <h3 style="margin-left:0.5rem; cursor: pointer;" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCategoryModal_' . $productCategoriesId . '">
                                            <i class="fa-solid fa-pen"></i>
                                        </h3>
                                        <h3 style="margin-left:0.5rem;">
                                            <i class="fa-solid fa-trash" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal_' . $productCategoriesId . '"></i>
                                        </h3>
                                    </div>
                                    
                                    <!-- 編輯分類 Modal -->
                                    <div class="modal fade" id="editCategoryModal_' . $productCategoriesId . '" tabindex="-1" aria-labelledby="editCategoryModalLabel_' . $productCategoriesId . '" aria-hidden="true">
                                        <div class="modal-dialog" >
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="editCategoryModalLabel_' . $productCategoriesId . '">編輯分類名稱</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="../process.php" method="post">
                                                        <input type="hidden" name="mid" value="' . $mid . '">
                                                        <input type="hidden" name="productCategoriesId" value="' . $productCategoriesId . '">
                                                        <div class="py-3">
                                                            <label for="productCategoryName">分類名稱</label>
                                                            
                                                            <input style=" font-weight: bold;" type="text" class="form-control" name="productCategoryName" value="' . htmlspecialchars($categoryName) . '" placeholder="輸入分類名稱">
                                                        </div>
                                                        <div class="form-element button_container mt-3">
                                                            <input style=" font-weight: bold;" type="submit" class="btn btn-primary" name="updateCategory" value="儲存">
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="deleteCategoryModal_' . $productCategoriesId . '" tabindex="-1" aria-labelledby="deleteCategoryModalLabel_' . $productCategoriesId . '" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="deleteCategoryModalLabel_' . $productCategoriesId . '">刪除類別</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                                                </div>
                                                <div class="modal-body">
                                                    你確定要刪除「' . htmlspecialchars($categoryName) . '」這個類別嗎？刪除後該類別下的商品也會一起刪除，無法復原。
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="../process.php" method="post">
                                                        <input type="hidden" name="productCategoriesId" value="' . $productCategoriesId . '">
                                                        <input type="hidden" name="mid" value="' . $mid . '">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                                        <button type="submit" name="deleteCategory" class="btn btn-danger">確定刪除</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

                                    // 查詢 Product 表中對應的產品名稱
                                    $sqlProducts = "SELECT *
                                                    FROM Product
                                                    WHERE pid IN (SELECT pid FROM ProductCategories WHERE productCategoriesId = $productCategoriesId)";

                                    $resultProducts = mysqli_query($conn, $sqlProducts);

                                    
                                    
                                    // 顯示產品
                                    if ($resultProducts && mysqli_num_rows($resultProducts) > 0) {
                                        echo '<div class="collapse show" id="collapse_' . $productCategoriesId . '">
                                                <div class="card card-body" style="border:2px solid #626263;" >';
                                        
                                        while ($productDetail = mysqli_fetch_assoc($resultProducts)) {

                                            echo '<div class="product-title">
                                                    <div style="display:flex; justify-content: space-between;" >
                                                        <h4 style="margin:0; text-align:left; text-decoration:underline; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#editProductModal_' . $productDetail['pid'] . '"><i style="margin-right:0.4rem" class="fa-solid fa-bag-shopping"></i>'
                                                            . htmlspecialchars($productDetail['pName']) .  ' - $' . htmlspecialchars($productDetail['price']) . 
                                                        '</h4>
                                                        <h4>
                                                            <i class="fa-solid fa-trash" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#deleteProductModal_'. $productDetail['pid'] .'"></i>
                                                        </h4>
                                                    </div>

                                                </div>';


                                            // 下面是每個產品自己的 Modal
                                            echo '
                                            <div class="modal fade" id="editProductModal_' . $productDetail['pid'] . '" tabindex="-1" aria-labelledby="editProductModalLabel_' . $productDetail['pid'] . '" aria-hidden="true">
                                                <div class="modal-dialog" style="max-width: 50rem;">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="editProductModalLabel_' . $productDetail['pid'] . '">編輯產品</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                                                        </div>
                                                        
                                                        <div class="modal-body">
                                                            <form action="../process.php" method="post" enctype="multipart/form-data">
                                                                <input type="hidden" name="mid" value="' . $mid . '">
                                                                <input type="hidden" name="pid" value="' . $productDetail['pid'] . '">
                                                                <input type="hidden" name="deleteImage" id="deleteImage_' . $productDetail['pid'] . '" value="no">



                                                                <!-- 類別名稱下拉選單 -->
                                                                <div class="form-group py-3">
                                                                    <div style="display:flex; justify-content: center;">
                                                                        <label for="productCategoryName">選擇類別</label>
                                                                        <p style="color:red; margin:0;">*</p>

                                                                    </div>
                                                                    
                                                                    <select class="form-control" id="productCategoryName" name="productCategoryId" required>
                                                                        <option value="">-- 請選擇類別 --</option>';

                                                                        // 查詢當前產品的類別
                                                                        $sqlProductCategory = "SELECT productCategoriesId FROM ProductCategories WHERE pid = " . $productDetail['pid'];
                                                                        $resultProductCategory = mysqli_query($conn, $sqlProductCategory);
                                                                        $currentCategory = mysqli_fetch_assoc($resultProductCategory)['productCategoriesId'];


                                                                        // 查詢所有的類別名稱
                                                                        $sqlCategories = "SELECT productCategoriesId, productCategoryName FROM ProductCategoryList WHERE mid = $mid";
                                                                        $resultCategories = mysqli_query($conn, $sqlCategories);
                                                                        while ($category = mysqli_fetch_assoc($resultCategories)) {
                                                                            $selected = ($category['productCategoriesId'] == $currentCategory) ? 'selected' : '';
                                                                            echo '<option value="' . $category['productCategoriesId'] . '" ' . $selected . '>' . htmlspecialchars($category['productCategoryName']) . '</option>';
                                                                        }


                                                                echo '</select>
                                                                </div>

                                                                <div class="py-3">
                                                                    <div style="display:flex; justify-content: center;">
                                                                        <label for="pName">商品名稱</label>
                                                                        <p style="color:red; margin:0;">*</p>

                                                                    </div>
                                                                    
                                                                    <input style= "font-weight: bold;" type="text" class="form-control" name="pName" required value="' . htmlspecialchars($productDetail['pName']) . '" placeholder="輸入產品名稱">
                                                                </div>
                                                                <div class="py-3">
                                                                    <label for="pDescription">商品敘述</label>
                                                                    <textarea style="font-weight: bold;" class="form-control" name="pDescription"  placeholder="輸入商品敘述" rows="4">' . htmlspecialchars($productDetail['pDescription']) . '</textarea>
                                                                </div>

                                                                <div class="py-3">
                                                                    <div style="display:flex; justify-content: center;">
                                                                        <label for="price">價錢</label>
                                                                        <p style="color:red; margin:0;">*</p>

                                                                    </div>
                                                                    
                                                                    <input style=" font-weight: bold;" type="text" class="form-control" name="price" required value="' . htmlspecialchars($productDetail['price']) . '" placeholder="輸入價錢">
                                                                </div>

                                                                <div class="py-3">
                                                                    <p>目前圖片</p>';
                                                                    
                                                                    if (!empty($productDetail['pPicture'])) {
                                                                        echo '
                                                                        <div style="position: relative; display: inline-block;">
                                                                            <img src="../' . htmlspecialchars($productDetail['pPicture']) . '" alt="Product Image" style="max-width: 20rem; max-height: 20rem; ">
                                                                            <!-- Delete Button -->
                                                                            <i class="fa-solid fa-x" style="position: absolute; color: white; cursor: pointer; background-color:#dc3545; border-radius:50%; padding: 0.5em; top:-0.5em; left:95%" onclick="removeImage_' . $productDetail['pid'] . '()"></i>
                                                                            
                                                                        </div>
                                                                        ';
                                                                    } else {
                                                                        echo '<p>No image available</p>';
                                                                    }
                                                        echo '  </div>

                                                                <div class="py-3">
                                                                    <label for="ImageUpload"">上傳新照片</label>
                                                                    <input style=" font-weight: bold;" type="file" class="form-control" name="ImageUpload">
                                                                </div>

                                                                <div class="form-element button_container mt-3">
                                                                    
                                                                    <input style=" font-weight: bold;" type="submit" class="btn btn-primary" name="updateProduct" id="saveButton_' . $productDetail['pid'] . '" value="儲存" disabled>

                                                                </div>

                                                            </form>
                                                            

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 刪除產品的 Modal -->
                                            <div class="modal fade" id="deleteProductModal_' . $productDetail['pid'] . '" tabindex="-1" aria-labelledby="deleteProductModalLabel_' . $productDetail['pid'] . '" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="deleteProductModalLabel_' . $productDetail['pid'] . '">刪除產品</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            你確定要刪除「' . htmlspecialchars($productDetail['pName']) . '」這個商品嗎？刪除後無法復原。
                                                        </div>
                                                        <div class="modal-footer">
                                                            <form action="../process.php" method="post">
                                                                <input type="hidden" name="pid" value="' . $productDetail['pid'] . '">
                                                                <input type="hidden" name="mid" value="' . $mid . '">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                                                <button type="submit" name="deleteProduct" class="btn btn-danger">確定刪除</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';

                                            echo '
                                            <script>
                                                document.addEventListener("DOMContentLoaded", function () {
                                                    // 設定初始值
                                                    var originalPName_' . $productDetail['pid'] . ' = "' . addslashes($productDetail['productName']) . '";
                                                    var originalPDescription_' . $productDetail['pid'] . ' = "' . addslashes($productDetail['productDescription']) . '";
                                                    var originalPrice_' . $productDetail['pid'] . ' = "' . addslashes($productDetail['price']) . '";
                                                    var originalProductCategory_' . $productDetail['pid'] . ' = "' . addslashes($productDetail['category']) . '";

                                                    var saveButton_' . $productDetail['pid'] . ' = document.getElementById("saveButton_' . $productDetail['pid'] . '");
                                                    var pNameInput_' . $productDetail['pid'] . ' = document.querySelector("#editProductModal_' . $productDetail['pid'] . ' [name=pName]");
                                                    var pDescriptionInput_' . $productDetail['pid'] . ' = document.querySelector("#editProductModal_' . $productDetail['pid'] . ' [name=pDescription]");
                                                    var priceInput_' . $productDetail['pid'] . ' = document.querySelector("#editProductModal_' . $productDetail['pid'] . ' [name=price]");
                                                    var imageInput_' . $productDetail['pid'] . ' = document.querySelector("#editProductModal_' . $productDetail['pid'] . ' [name=ImageUpload]");
                                                    var productCategoryInput_' . $productDetail['pid'] . ' = document.querySelector("#editProductModal_' . $productDetail['pid'] . ' [name=productCategoryId]");
                                                    var deleteImageInput_' . $productDetail['pid'] . ' = document.getElementById("deleteImage_' . $productDetail['pid'] . '");

                                                    function checkIfChanged_' . $productDetail['pid'] . '() {
                                                        var nameChanged = pNameInput_' . $productDetail['pid'] . '.value !== originalPName_' . $productDetail['pid'] . ';
                                                        var descriptionChanged = pDescriptionInput_' . $productDetail['pid'] . '.value !== originalPDescription_' . $productDetail['pid'] . ';
                                                        var priceChanged = priceInput_' . $productDetail['pid'] . '.value !== originalPrice_' . $productDetail['pid'] . ';
                                                        var imageChanged = imageInput_' . $productDetail['pid'] . '.files.length > 0 || deleteImageInput_' . $productDetail['pid'] . '.value === "yes";
                                                        var categoryChanged = productCategoryInput_' . $productDetail['pid'] . '.value !== originalProductCategory_' . $productDetail['pid'] . ';

                                                        saveButton_' . $productDetail['pid'] . '.disabled = !(nameChanged || priceChanged || imageChanged || descriptionChanged || categoryChanged);
                                                    }

                                                    pNameInput_' . $productDetail['pid'] . '.addEventListener("input", checkIfChanged_' . $productDetail['pid'] . ');
                                                    pDescriptionInput_' . $productDetail['pid'] . '.addEventListener("input", checkIfChanged_' . $productDetail['pid'] . ');
                                                    priceInput_' . $productDetail['pid'] . '.addEventListener("input", checkIfChanged_' . $productDetail['pid'] . ');
                                                    imageInput_' . $productDetail['pid'] . '.addEventListener("change", checkIfChanged_' . $productDetail['pid'] . ');
                                                    productCategoryInput_' . $productDetail['pid'] . '.addEventListener("change", checkIfChanged_' . $productDetail['pid'] . ');

                                                    window.removeImage_' . $productDetail['pid'] . ' = function () {
                                                        var imageElement = document.querySelector("#editProductModal_' . $productDetail['pid'] . ' img");
                                                        var deleteIcon = document.querySelector("#editProductModal_' . $productDetail['pid'] . ' i.fa-x");

                                                        if (imageElement) imageElement.style.display = "none";
                                                        if (deleteIcon) deleteIcon.style.display = "none";

                                                        deleteImageInput_' . $productDetail['pid'] . '.value = "yes";
                                                        checkIfChanged_' . $productDetail['pid'] . '();
                                                    };
                                                });
                                            </script>';





                                            
                                                    
                                        }
                                        
                                        
                                        echo '</div>
                                            </div>';
                                    }
                                }
                            } else {
                                echo "<p>該商店沒有任何分類。</p>";
                            }
                        }
                    ?>

                    
                    </div>
                </div>
            </div>
        </div>
         

        

        



        <!-- Back to Top -->
        <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   

        
    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>



    </body>

</html>