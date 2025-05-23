<?php
session_start();
$cid = $_SESSION['cid'] ?? null;
?>


<!DOCTYPE html>
<html>
<head>
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


    <!-- 引入 jQuery UI CSS（使得排序元素顯示為拖曳狀態） -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="wheel.css">

</head>

<body >
    <!-- Navbar start -->
    <div class="container-fluid fixed-top">
        <div class="container topbar bg-primary d-none d-lg-block" style="padding: 20px;">
            <div class="d-flex justify-content-between">
                <div class="top-info ps-2">
                </div>        
            </div>
        </div>
        <div class="container px-0">
            <nav class="navbar navbar-light bg-white navbar-expand-xl ">
                <a href="../customer/index.php?cid=<?php echo $cid; ?>" class="navbar-brand text-decoration-none"><h1 class="text-primary display-6">Junglebite 命運轉盤</h1></a>
                <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-primary"></span>
                </button>
                <div class="collapse navbar-collapse bg-white" id="navbarCollapse" style="display: flex; flex-direction: row-reverse;">
                    
                    <div class="d-flex m-3 me-0">
                        <!-- <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fas fa-search text-primary"></i></button> -->
                        <!-- <a href="#" class="position-relative me-4 my-auto">
                            <i class="fa fa-shopping-bag fa-2x"></i>
                            <span class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1" style="top: -5px; left: 15px; height: 20px; min-width: 20px;">3</span>
                        </a> -->
                        <?php if (isset($_SESSION['login_success'])): ?>
                        <!-- ✅ 已登入的顯示 -->
                        <div class="dropdown" style="position: relative; display: inline-block;">
                            <a href="javascript:void(0);" class="my-auto" onclick="toggleDropdown()">
                            <img src="  ../login/success.png" alt="Success" style="width: 40px; height: 40px; filter: brightness(0) saturate(100%) invert(42%) sepia(91%) saturate(356%) hue-rotate(71deg) brightness(94%) contrast(92%);">
                            </a>

                            <div id="myDropdown" class="dropdown-content" style="display: none; position: absolute; background-color: white; min-width: 120px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; right: 0; border-radius: 8px;">

                                <?php if ($_SESSION['role'] === 'm'): ?>
                                    <a href="/database/merchant/setting.php" class="dropdown-item">商家設定</a>
                                <?php elseif ($_SESSION['role'] === 'c'): ?>
                                    <a href="../login/login_customer/setting.php?cid=<?php echo $cid; ?>" class="dropdown-item">個人設定</a>
                                    <a href="/database_project/allergy/allergy.php?cid=<?php echo $cid; ?>" class="dropdown-item">過敏設定</a>
                                    <a href="../claw_machine/claw.php?cid=<?php echo $cid; ?>" class="dropdown-item">優惠券活動</a>
                                    <a href="../walletAndrecord/c_wallet.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">錢包</a>
                                    <a href="../walletAndrecord/c_record.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">交易紀錄</a>
                                    <a href="../customer/friends.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">我的好友</a>
                                    <!-- <a href="../wheel/wheel.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item">命運轉盤</a> -->
                                    <a href="../customer/myfavorite.php?cid=<?php echo $cid; ?>&role=c" class="dropdown-item text-decoration-none">我的愛店</a>
                                    <a href="/database_project/customer/reservation.php" class="dropdown-item">我要訂位</a>
                                <?php elseif ($_SESSION['role'] === 'd'): ?>
                                    <a href="/database/customer/setting.php" class="dropdown-item">外送員設定</a>
                                <?php elseif ($_SESSION['role'] === 'platform'): ?>
                                    <a href="/database/customer/setting.php" class="dropdown-item">平台設定</a>
                                <?php endif; ?>
                                    <a href="/database_project/login/login_customer/logout.php" class="dropdown-item">Logout</a>


                            </div>
                        </div>
                        <?php else: ?>
                        <!-- ❌ 未登入的顯示 -->
                        <a href="/database_project/login/before_login.php" class="my-auto">
                            <i class="fas fa-user fa-2x"></i>
                        </a>
                        <?php endif; ?>
                    </div>

                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <div class="header">
        <h1>WINNER</h1>
        <p id="winner">NONE</p>
    </div>
    <div class="wheel">
        <canvas class="" id="canvas" width="500" height="500"></canvas>
        <div class="center-circle" onclick="spin()">
            <div class="triangle"></div>
        </div>
        
    </div>

    <div id="restaurant-list" class="mt-5">
      <div id="tab-0" class="tab-pane fade show p-0 active">
        <div class="row g-4">
          <div class="col-lg-12">
            <div id="restaurant-cards" class="g-4 row">
              <!-- 動態商家卡片會出現在這裡 -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    
  <script>
    function toggleDropdown() {
        const dropdown = document.getElementById("myDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    // 點擊頁面其他地方自動收起下拉選單
    window.onclick = function(event) {
        if (!event.target.matches('.my-auto') && !event.target.closest('.dropdown')) {
            var dropdown = document.getElementById("myDropdown");
            if (dropdown && dropdown.style.display === "block") {
                dropdown.style.display = "none";
            }
        }
    }
  </script>

  <script>
  // 工具函式
  function toRad(deg){ return deg * Math.PI/180; }
  function randomRange(min,max){ return Math.floor(Math.random()*(max-min+1))+min; }
  function easeOutSine(x){ return Math.sin((x*Math.PI)/2); }
  function getPercent(input,min,max){ return (((input-min)*100)/(max-min))/100; }

  // 暖色系定義 + 深淺漸層產生
  const baseColors = [
      "#D0B37A", // 杏仁
      "#E6D193", // 檸檬
      "#BFA15C", // 木堅果
      "#F2E9C3", // 淡奶油
      "#C8B28E", // 沙褐
      "#A78C5F"  // 栗木
      ];
    function shadeColor(color, pct){
      const num = parseInt(color.slice(1),16);
      let r = (num>>16)+Math.round(255*pct),
          g = ((num>>8)&0xFF)+Math.round(255*pct),
          b = (num&0xFF)+Math.round(255*pct);
      r = Math.min(255,Math.max(0,r));
      g = Math.min(255,Math.max(0,g));
      b = Math.min(255,Math.max(0,b));
      return `rgb(${r},${g},${b})`;
    }
    const colors = baseColors.map(c=>({
      light: shadeColor(c,  0.15),
      dark:  shadeColor(c, -0.15)
    }));

    // 轉盤與選項
    const items = [
      "中式","便當","台式","咖哩","咖啡","披薩","日式",
      "歐美","漢堡","炒飯","炸雞","牛排","甜點",
      "義大利麵","蛋糕","飲料","麵食"
    ];
    const canvas = document.getElementById("canvas");
    const ctx    = canvas.getContext("2d");
    const W = canvas.width, H = canvas.height;
    const CX = W/2, CY = H/2, R = W/2;
    const step = 360/items.length;
    let currentDeg = 0, speed = 0, maxRotation = 0, pause = true;

    // 畫盤
    function draw(){
      ctx.clearRect(0,0,W,H);
      ctx.save();
      ctx.translate(CX,CY);
      ctx.rotate(toRad(currentDeg));
      ctx.translate(-CX,-CY);

      let start = 0;
      for(let i=0;i<items.length;i++){
        const end = start+step;
        const col = colors[i % colors.length];

        // 外圈深色
        ctx.beginPath();
        ctx.arc(CX,CY,R-2,toRad(start),toRad(end));
        ctx.fillStyle = col.dark;
        ctx.lineTo(CX,CY);
        ctx.fill();

        // 內圈淺色
        ctx.beginPath();
        ctx.arc(CX,CY,R-30,toRad(start),toRad(end));
        ctx.fillStyle = col.light;
        ctx.lineTo(CX,CY);
        ctx.fill();

        // 文字
        ctx.save();
        ctx.translate(CX,CY);
        ctx.rotate(toRad((start+end)/2));
        ctx.textAlign = "center";
        ctx.fillStyle = (i%2===0 ? "#2B2D42" :"#2B2D42" );
        ctx.font = "bold 24px serif";
        ctx.fillText(items[i], R*0.6, 10);
        ctx.restore();

        start += step;
      }
      ctx.restore();
    }

    // 轉動動畫
    function animate(){
      if(pause) return;
      speed = easeOutSine(getPercent(currentDeg, maxRotation, 0))*20;
      if(speed < 0.01){
        pause = true; updateWinner(); return;
      }
      currentDeg += speed;
      draw();
      requestAnimationFrame(animate);
    }

    

    function updateWinner(){
    const deg = (currentDeg % 360 + 360) % 360;
    const idx = Math.floor((360 - deg) / step) % items.length;
    const winnerText = items[idx].trim();
    document.getElementById("winner").innerText = winnerText;

    fetch(`getRestaurants.php?category=${encodeURIComponent(winnerText)}&cid=<?= $cid ?>`)
      .then(res => {
        if (!res.ok) throw new Error(`伺服器 ${res.status}`);
        return res.json();
      })
      .then(data => {
        console.log("Winner:", winnerText);
        console.log("Fetched merchants:", data);

        const cards = document.getElementById("restaurant-cards");
        if (!Array.isArray(data) || data.length === 0) {
          cards.innerHTML = "<p class='text-center'>抱歉，沒有找到相關店家。</p>";
          return;
        }

    

    cards.innerHTML = data.map(m => {
      const heartClass = m.isFavorited ? 'fa-solid text-danger' : 'fa-regular';
      return `
        <div class="col-md-6 col-lg-4 col-xl-3">
          <div class="rounded position-relative fruite-item" style="cursor:pointer;"
              onclick="location.href='../customer/merchant.php?mid=${encodeURIComponent(m.mid)}'">
            <div class="fruite-img">
              <img src="../${m.mPicture}" class="img-fluid w-100 rounded-top" alt="${m.mName}">
            </div>
            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute" style="top:10px; left:10px;">
              ${m.categoryNames || '未分類'}
            </div>
            <div class="p-4 border border-secondary border-top-0 rounded-bottom"
                style="height:175px; display:flex; flex-direction:column; justify-content:space-between;">
              <div>
                <h5>${m.mName}</h5>
                <p>${m.mAddress}</p>
              </div>
              <div class="d-flex justify-content-between flex-lg-wrap" style="align-items: center;">
                <p class="text-dark fs-5 fw-bold mb-0">
                  <i class="fa-heart favorite-icon ${heartClass}" data-mid="${m.mid}" style="cursor:pointer;" onclick="event.stopPropagation();"></i>
                </p>
                <p class="mb-0" style="text-align:right;">
                  <i class="fas fa-star fs-6 me-1 mb-0" style="color:#ffb524;"></i>
                  ${m.rating ?? '尚無評分'}/5 (${m.ratingCount})
                </p>
              </div>
            </div>
          </div>
        </div>`;
    }).join("");
    })
    .catch(err => {
      console.error("載入錯誤：", err);
      document.getElementById("restaurant-cards")
              .innerHTML = `<p class="text-center">載入失敗：${err.message}</p>`;
    });

    ;
}




    // 啟動轉盤
    function spin(){
      if(!pause) return;
      currentDeg = 0;
      draw();
      maxRotation = 360*randomRange(5,8) + randomRange(0,360);
      pause = false;
      requestAnimationFrame(animate);
    }

    // 初始畫面
    draw();
  </script>

  <script>
  document.addEventListener('click', function (e) {
    const icon = e.target.closest('.favorite-icon');
    if (icon) {
      e.stopPropagation(); // 防止跳轉
      const mid = icon.dataset.mid;

      fetch('toggleFavorite.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `mid=${mid}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          if (data.favorited) {
            icon.classList.remove('fa-regular');
            icon.classList.add('fa-solid', 'text-danger');
          } else {
            icon.classList.remove('fa-solid', 'text-danger');
            icon.classList.add('fa-regular');
          }
        } else {
          alert(data.message || "操作失敗！");
        }
      })
      .catch(err => {
        console.error("收藏操作錯誤：", err);
      });
    }
  });
  </script>

</body>

</html>