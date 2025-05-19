<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['face_login']) && $_POST['face_login'] == 1) {
    if (isset($_POST['cid'])) {
        $_SESSION['cid'] = $_POST['cid'];
        echo "OK";
    } else {
        echo "缺少 cid";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>人臉登入</title>
</head>
<body>
  <h2>請輸入 Email 並看鏡頭鏡頭</h2>Enter Email：
  <input type="email" id="email" placeholder="請輸入 Email" style="width:65%"><br><br>
  <video id="video" width="320" height="240" autoplay></video><br>
  <button id="loginBtn" style="margin-top: 20px;">🔒 拍照登入</button>
  <canvas id="canvas" width="320" height="240" style="display: none;"></canvas>

  <script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');

    navigator.mediaDevices.getUserMedia({ video: true })
      .then(stream => video.srcObject = stream)
      .catch(err => alert("無法啟用鏡頭：" + err));

    document.getElementById('loginBtn').addEventListener('click', () => {
      const email = document.getElementById('email').value.trim();
      if (!email) {
        alert("請輸入 Email");
        return;
      }

      context.drawImage(video, 0, 0, canvas.width, canvas.height);
      const imageData = canvas.toDataURL('image/jpeg');

      fetch('http://localhost:5000/face-login-image', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ image: imageData, email: email })
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          const cid = data.user_id;

          fetch("login.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            credentials: "include", // ✅ 加這行！
            body: `face_login=1&cid=${cid}`
          }).then(() => {
            window.location.href = "../customer/index.php";
          });
        } else {
          alert("登入失敗：" + data.message);
        }
      });
    });
  </script>
</body>
</html>
<style>
    html{
    display: flex;
    justify-content: center;
    text-align: center;
    padding-top: 100px;
    }
    body{
        background-image: url('../walletAndrecord/image/hide.png');
        background-repeat: no-repeat;
        background-size: cover;
    }
</style>
