<?php
session_start();
include ('../../walletAndrecord/connect.php');  
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cid = isset($_SESSION["cid"]) ? $_SESSION["cid"] : '';
?>
<script>
  const cid = "<?php echo htmlspecialchars($cid, ENT_QUOTES, 'UTF-8'); ?>";
window.addEventListener('DOMContentLoaded', () => {
  const cid = "<?php echo $cid; ?>";
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('cid') && urlParams.get('role') === 'c') {
        // 自動觸發圖片選擇器
        document.getElementById('imageInput').click();
    }
});
</script>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>JungleBite - 以圖搜圖</title>
  <style>
    body {
      font-family: "Segoe UI", sans-serif;
      background: #f9f9f9;
      padding: 40px;
      text-align: center;
      background-image: url('../../walletAndrecord/image/forest.png');
      background-repeat: no-repeat;
      background-size: cover;
    }

    h1 {
      color: #333;
      margin-bottom: 20px;
    }

    .upload-section {
      background: #fff;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      display: inline-block;
    }

    #preview {
      margin-top: 20px;
      max-width: 250px;
      border: 2px dashed #ccc;
      border-radius: 8px;
      padding: 10px;
    }

    button {
      padding: 10px 20px;
      font-size: 16px;
      margin-top: 15px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0056b3;
    }

    .result-container {
      margin-top: 40px;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .card {
      background: #fff;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      width: 180px;
      transition: transform 0.2s ease;
    }

    .card img {
      width: 100%;
      border-radius: 8px;
    }

    .card p {
      margin-top: 8px;
      font-weight: bold;
    }

    .card:hover {
      transform: scale(1.05);
    }
    #block1{
      display: flex;
      flex-direction:column;
      justify-content:flex-start;
      margin:0px 60px;
    }
    #content{
      display: flex;
      /* flex-wrap:wrap; */
      flex-direction:row;
    }
  </style>
</head>
<body>
  <div id="content">
    <div id="block1">
      <h1>🍽️ 圖搜圖系統</h1>

      <div class="upload-section">
        <form id="uploadForm" enctype="multipart/form-data">
          <input type="file" id="imageInput" name="image" accept="image/*" required><br>
          <img id="preview" alt="預覽圖會顯示在這裡"><br>
          <button type="submit">🔍 開始搜尋</button>
        </form>
      </div>
    </div>
    <div id="results" class="result-container"></div>
  </div>
  <script>
    document.getElementById('imageInput').onchange = function(event) {
      const reader = new FileReader();
      reader.onload = function(){
        document.getElementById('preview').src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    };

    document.getElementById('uploadForm').onsubmit = function(event) {
      event.preventDefault();
      const file = document.getElementById('imageInput').files[0];
      const formData = new FormData();
      formData.append('image', file);
      formData.append('cid', cid); // 👈 傳給 Flask

      fetch('http://localhost:5000/search', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        let html = '';
        data.results.forEach(item => {
          html += `
            <div class="card">
              <a href="${item.merchant_url}">
                <img src="${item.image_url}" alt="${item.name}">
              </a>
              <p>${item.name}</p>
            </div>
          `;
        });
        document.getElementById('results').innerHTML = html;
      })
      .catch(err => {
        alert("搜尋失敗，請確認 Flask 是否啟動");
        console.error(err);
      });
    };
  </script>

</body>
</html>
