<?php
session_start();

if (isset($_SESSION['cid'])) {
    $customer_id = $_SESSION['cid'];
    
    // 連接資料庫
    $conn = new mysqli('localhost', 'root', '', 'junglebite');
    if ($conn->connect_error) {
        die("連接失敗：" . $conn->connect_error);
    }

    // 查詢 customer 資料表以獲取 cName
    $sql = "SELECT cName FROM customer WHERE cid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL 語句錯誤：" . $conn->error);
    }
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $customer_name = '使用者';  // 預設名稱
    if ($row = $result->fetch_assoc()) {
        $customer_name = $row['cName'];  // 取得名稱
    }

    // 查詢過敏原資料
    $sql = "SELECT allergens, other_allergen FROM allergy WHERE cid = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL 語句錯誤：" . $conn->error);
    }
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $allergens = [];
    $other_allergen = '';
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['allergens'])) {
            // 假設 allergens 是用逗號分隔的多個值
            $allergen_items = explode(',', $row['allergens']);
            $allergens = array_merge($allergens, array_map('trim', $allergen_items));
        }
        if (!empty($row['other_allergen'])) {
            $other_allergen = $row['other_allergen']; // 如果有多筆資料，你可以選擇是否要合併
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo "請先登入，才能填寫過敏原資料。";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>allergy</title>
    <link rel="stylesheet" href="allergy.css">
    <!-- 引入 Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Noto Sans TC', sans-serif;
        background-color: #e6f2e6; /* 淺綠背景 */
        color: #2e3d27; /* 深綠褐色文字 */
    }

    h3 {
        margin-bottom: 20px;
        color: #355e3b; /* 森林綠 */
    }

    .container {
        background-color: #f5fbef; /* 很淡的草綠色 */
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(34, 51, 34, 0.1);
        max-width: 800px;
    }

    .allergen-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .allergen-item {
        background-color: #cce3cc; /* 草地綠 */
        padding: 10px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(50, 75, 50, 0.05);
        transition: transform 0.2s;
    }

    .allergen-item:hover {
        transform: scale(1.03);
        background-color: #b5d6b5; /* hover時變深綠 */
    }

    .allergen-item img {
        margin-bottom: 10px;
        width: 180px;
    }

    .modal-content {
        border-radius: 12px;
        background-color: #f0f5f0;
    }

    #other-allergen {
        width: 80%;
        margin: 10px auto;
        display: block;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #8fb88f; /* 淡綠邊框 */
        background-color: #f9fff9;
    }

    ul {
        padding-left: 20px;
    }

    ul li {
        margin-bottom: 5px;
    }

    .modal-title {
        font-weight: bold;
        color: #2e4b2e;
    }

    button.btn-primary {
        background-color: #3b7a3b; /* 深叢林綠 */
        border-color: #3b7a3b;
    }

    button.btn-primary:hover {
        background-color: #2f612f; /* hover 更深 */
    }

    button.btn-secondary {
        background-color: #8a9f8a;
        border-color: #8a9f8a;
    }

    button.btn-secondary:hover {
        background-color: #768976;
    }

    .container.mt-5 {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        top: 200px;
    }
</style>

</head>
<body>

    <div class="header_allergy">
        <p>allergens you have</p>
        <a href="customer\index.php">
            <img src="home.png" alt="Home" class="home-icon">
        </a>    
    </div>

    <div class="container mt-5">
        <h3>歡迎, <?php echo htmlspecialchars($customer_name); ?></h3>
        <p>您的客戶編號 (CID): <?php echo htmlspecialchars($customer_id); ?></p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#allergyModal">編輯過敏原</button>

        <!-- 顯示已存入的過敏原資料 -->
        <div class="mt-4">
            <h5>您的過敏原資料：</h5>
            <?php if (!empty($allergens) || !empty($other_allergen)): ?>
                <ul>
                    <?php foreach ($allergens as $allergen): ?>
                        <li><?php echo htmlspecialchars($allergen); ?></li>
                    <?php endforeach; ?>
                    <?php if (!empty($other_allergen)): ?>
                        <li>其他過敏原：<?php echo htmlspecialchars($other_allergen); ?></li>
                    <?php endif; ?>
                </ul>
            <?php else: ?>
                <p>尚未填寫過敏原資料。</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- 過敏原彈出視窗 -->
    <div class="modal fade" id="allergyModal" tabindex="-1" aria-labelledby="allergyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allergyModalLabel">選擇過敏原</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="allergyForm" action="db_allergy.php" method="POST">
                        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
                        <fieldset>
                            <legend>請勾選您的食物過敏原：</legend>
                            <div class="allergen-grid">
                            <div class="allergen-item">
                                <label>
                                    <img src="crab.png" alt="甲殼類">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="shellfish"
                                        <?php echo in_array('shellfish', $allergens) ? 'checked' : ''; ?>>
                                    甲殼類（蝦、蟹）
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="魚.png" alt="魚類">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="fish"
                                        <?php echo in_array('fish', $allergens) ? 'checked' : ''; ?>>
                                    魚類
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="花生.png" alt="花生">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="peanuts"
                                        <?php echo in_array('peanuts', $allergens) ? 'checked' : ''; ?>>
                                    花生
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="nuts.png" alt="堅果類">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="nuts"
                                        <?php echo in_array('nuts', $allergens) ? 'checked' : ''; ?>>
                                    堅果類（核桃、杏仁、腰果）
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="牛奶.png" alt="牛奶">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="milk"
                                        <?php echo in_array('milk', $allergens) ? 'checked' : ''; ?>>
                                    牛奶
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="egg.png" alt="雞蛋">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="eggs"
                                        <?php echo in_array('eggs', $allergens) ? 'checked' : ''; ?>>
                                    雞蛋
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="小麥.png" alt="小麥">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="wheat"
                                        <?php echo in_array('wheat', $allergens) ? 'checked' : ''; ?>>
                                    小麥（含麩質）
                                </label>
                            </div>
                            <div class="allergen-item">
                                <label>
                                    <img src="蚵仔.png" alt="螺貝類">
                                    <br>
                                    <input type="checkbox" name="allergens[]" value="mollusks"
                                        <?php echo in_array('mollusks', $allergens) ? 'checked' : ''; ?>>
                                    螺貝類（蚵仔、淡菜）
                                </label>
                            </div>
                            </div>
                            <div style="text-align: center; margin-top: 30px;">
                                <label for="other-allergen">其他過敏原：</label><br>
                                <input type="text" id="other-allergen" name="other_allergen" 
                                    value="<?php echo htmlspecialchars($other_allergen); ?>" 
                                    placeholder="請輸入其他過敏原" 
                                    style="width: 60%; padding: 8px; border-radius: 6px; border: 1px solid #ccc; margin-top: 20px;">
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                    <button type="submit" form="allergyForm" class="btn btn-primary">提交</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 引入 Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>