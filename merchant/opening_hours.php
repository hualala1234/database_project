<?php
session_start();

// 如果未登入，導向登入頁面
if (!isset($_SESSION['mid'])) {
    header("Location: ../../login.php");
    exit;
}

$merchant_id = $_SESSION['mid'];

// 模擬商家名稱與營業時間資料
$merchant_name = '我的商店';
$store_hours = [
    '星期一' => '10:00 - 20:00',
    '星期二' => '10:00 - 20:00',
    '星期三' => '10:00 - 20:00',
    '星期四' => '10:00 - 20:00',
    '星期五' => '10:00 - 22:00',
    '星期六' => '12:00 - 22:00',
    '星期日' => '休息'
];
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>商家營業時間</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .container {
        max-width: 600px;
        width: 100%;
    }
</style>

</head>
<body class="bg-light">
    <div class="container mt-5">
        <h3>歡迎, <?php echo htmlspecialchars($merchant_name); ?></h3>

        <h5 class="mt-4">目前營業時間：</h5>
        <ul class="list-group mb-3">
            <?php foreach ($store_hours as $day => $hours): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <strong><?php echo $day; ?></strong>
                    <span><?php echo $hours; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editHoursModal">編輯營業時間</button>
    </div>

    <!-- Modal 彈出視窗 -->
    <div class="modal fade" id="editHoursModal" tabindex="-1" aria-labelledby="editHoursModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form action="db_store_hours.php" method="POST">
                <div class="modal-body">
                    <?php foreach ($store_hours as $day => $hours): 
                        // 拆分時段，預設格式為 "HH:MM - HH:MM" 或 "休息"
                        if ($hours === '休息') {
                            $open = '';
                            $close = '';
                            $closed = true;
                        } else {
                            list($open, $close) = array_map('trim', explode('-', $hours));
                            $closed = false;
                        }
                    ?>
                        <div class="mb-3">
                            <label class="form-label"><?php echo $day; ?></label>
                            <div class="d-flex gap-2">
                                <input type="time" class="form-control" name="hours[<?php echo $day; ?>][open]" value="<?php echo $open; ?>" <?php echo $closed ? 'disabled' : ''; ?>>
                                <span class="align-self-center">至</span>
                                <input type="time" class="form-control" name="hours[<?php echo $day; ?>][close]" value="<?php echo $close; ?>" <?php echo $closed ? 'disabled' : ''; ?>>
                                <div class="form-check ms-2">
                                    <input class="form-check-input" type="checkbox" id="closed_<?php echo $day; ?>" name="hours[<?php echo $day; ?>][closed]" value="1" <?php echo $closed ? 'checked' : ''; ?> onchange="toggleDay('<?php echo $day; ?>')">
                                    <label class="form-check-label" for="closed_<?php echo $day; ?>">休息</label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">儲存</button>
                </div>
            </form>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function toggleDay(day) {
        const isClosed = document.getElementById('closed_' + day).checked;
        const openInput = document.querySelector(`input[name="hours[${day}][open]"]`);
        const closeInput = document.querySelector(`input[name="hours[${day}][close]"]`);
        openInput.disabled = isClosed;
        closeInput.disabled = isClosed;
    }
    </script>


</body>
</html>
