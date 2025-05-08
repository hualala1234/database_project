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
  </style>
</head>
<body>

  <h1>🍽️ JungleBite 圖搜圖系統</h1>

  <div class="upload-section">
    <form id="uploadForm" enctype="multipart/form-data">
      <input type="file" id="imageInput" name="image" accept="image/*" required><br>
      <img id="preview" alt="預覽圖會顯示在這裡"><br>
      <button type="submit">🔍 開始搜尋</button>
    </form>
  </div>

  <div id="results" class="result-container"></div>

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
              <img src="${item.image_url}" alt="${item.name}">
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
