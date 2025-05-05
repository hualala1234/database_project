<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="claw.css">
   
    <title>Claw Machine</title>
  </head>
  <body>
    <!-- 要改 -->
    <div class="header_claw_machine">
      <p>CLAW MACHINE</p>
      <a href="../customer/index.php">
        <img src="home.png" alt="Home" class="home-icon">
      </a>
    </div>
    <div class="wrapper">
      <div class="collection-box pix"></div>
      <div class="claw-machine">
        <div class="box pix">
          <div class="machine-top pix">
            <div class="arm-joint pix">
              <div class="arm pix">
                <div class="claws pix"></div>
              </div>
            </div>
            <div class="rail hori pix"></div>
            <div class="rail vert pix"></div>
          </div>
          <div class="machine-bottom pix">
            <div class="collection-point pix"></div>
          </div>
        </div>
        <div class="control pix">
          <div class="cover left"></div>
          <button class="hori-btn pix"></button>
          <button class="vert-btn pix"></button>
          <div class="cover right">
            <div class="instruction pix"></div>
          </div>
          <div class="cover bottom"></div>
          <div class="cover top">
            <div class="collection-arrow pix"></div>
          </div>
          <div class="collection-point pix"></div>
        </div>
      </div>
    </div>

    <div class="popup-message hidden">
      <div class="popup-content">
        <h2>🎉 恭喜夾到娃娃！</h2>
        <p>優惠碼：<strong id="promocode">CLAWWIN20</strong></p>
        <!-- <button class="claim-coupon">領取優惠</button> 新增領取優惠按鈕 -->
      </div>
    </div>
    

    <div class="confetti-container"></div>

    <img id="couponImage" src="getcoupon.png" alt="已領取優惠券" style="
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 10001;
      cursor: pointer;
      max-width: 80%;
      height: 500px;
    ">


  
    <script src="claw.js"></script>
  </body>
</html>