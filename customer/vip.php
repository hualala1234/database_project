<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="vip">
        <img src="./join_vip.png" alt="vip">
        <a href="https://junglebite.com.tw/vip" target="_blank"> 加入 VIP </a>
        <!-- <p>成為 VIP 會員，享有更多優惠！</p> -->
    </div>
    <!-- Crown Icon -->
    <img class="crown" src="./vip.png" alt="crown icon" width="40" height="40"
        style="margin-left: 20px; margin-top: 20px; cursor: pointer;"
        onmouseover="this.src='./vip_hover.png'" 
        onmouseout="this.src='./vip.png'"
        onclick="toggleVIP(event)">

    <!-- VIP 彈出視窗 -->
    <div class="vip" id="vip-popup" style="display: none;">
        <img id="closecomment" src="./image/cross.png" alt="close button" width="15" height="15" 
            style="position:absolute; top:10px; right:10px; cursor:pointer;" 
            onclick="closeVIP()">
        
        <img id="vip-image" src="./join_vip.png" alt="vip" style="cursor: pointer;" onclick="addVIPToCart()">
        <p style="cursor: pointer;" onclick="addVIPToCart()">加入 VIP</p>
    </div>

    <!-- 購物車圖示 -->
    <div class="cart-container" id="cart">
        <img src="./image/cart.png" width="30" height="30">
        <span class="cart-count" id="cart-count">0</span>
    </div>

    <!-- 飛入動畫圖片容器 -->
    <div id="fly-container"></div>
</body>
</html>
