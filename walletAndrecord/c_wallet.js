document.addEventListener("DOMContentLoaded", function () {
    // 跳轉到 addCard.php
    document.getElementById("add").addEventListener("click", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get("cid");
        const role = urlParams.get("role");
    
        if (id && role) {
            window.location.href = `http://localhost/database_project/walletAndrecord/addCard.php?id=${encodeURIComponent(id)}&role=${encodeURIComponent(role)}`;
        } else {
            alert("缺少 id 或 role 參數！");
        }
    });


    // 1. 先定義：點卡片後，隱藏所有 card_detail
    function hideAllDetails() {
        document.querySelectorAll('#showCard .card_detail').forEach(function(cardDetail) {
            cardDetail.style.display = 'none';
            console.log('隱藏所有 card_detail 1')
        });
    }

    // 2. 綁定左邊所有卡片的點擊事件
    document.querySelectorAll('.card').forEach(function(card) {
        card.addEventListener('click', function (event) {
            event.stopPropagation(); // 不讓點擊冒泡到整個 document

            const cardId = this.getAttribute('data-card-id');
            
            // 全部卡片詳細資訊隱藏
            hideAllDetails();
            console.log('隱藏所有 card_detail 2')

            // 顯示這張被點到的卡片的資訊
            const detailToShow = document.getElementById('detail_card_' + cardId);
            if (detailToShow) {
                detailToShow.style.display = 'block';
                console.log('顯示這張被點到的卡片的資訊3')
            }

            // 顯示整個 showCard 區塊
            document.getElementById('showCard').style.display = 'block';
            console.log('顯示整個 showCard 區塊4')
        });
    });

    // 3. 綁定關閉按鈕，點叉叉後隱藏 showCard
    const closeShowBtn = document.getElementById('closeshow');
    if (closeShowBtn) {
        closeShowBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            document.getElementById('showCard').style.display = 'none';
            console.log('點叉叉後隱藏 showCard5')
        });
    }

    // 4. 加上！點背景空白也能關掉 showCard
    document.addEventListener('click', function(event) {
        const showCard = document.getElementById('showCard');
        
        // if (showCard.style.display === 'block') {
        //     showCard.style.display = 'none';
        //     console.log('點背景空白也能關掉 showCard6')
        // }
    });

    

});
