<?php
header('Content-Type: application/json');

// 確保有收到圖片
if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
    echo json_encode(["login" => false, "error" => "No image uploaded"]);
    exit;
}

// 儲存圖片到暫存檔
$tmp_name = $_FILES['image']['tmp_name'];

// 呼叫 Python Flask API
$python_api_url = "http://localhost:5000/face_login";
$cmd = "curl -s -X POST -F image=@$tmp_name $python_api_url";
$response = shell_exec($cmd);

// 回傳 Python 的結果
if ($response) {
    echo $response;
} else {
    echo json_encode(["login" => false, "error" => "No response from Python API"]);
}
?>
