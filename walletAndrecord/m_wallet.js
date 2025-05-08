document.addEventListener("DOMContentLoaded", function () {
    console.log('DOMContentLoaded event fired!');
    // click card to view the card
    // 取得 DOM 元素
    const card = document.getElementById("card1"); // !!!要改成點哪張卡片就出現哪張卡片的訊息
    const showCard = document.getElementById("showCard");
    const closeshowBtn = document.getElementById("closeshow");

    showCard.style.display = "none";
    // 點 card 顯示 showCard 區塊
    card.addEventListener("click", function () {
        showCard.style.display = "block";
    });

    // 點 close 關閉 showCard
    closeshowBtn.addEventListener("click", function () {
        showCard.style.display = "none";
    });

    document.querySelectorAll('.comment-cell').forEach(function(cell) {
        cell.addEventListener('click', function() {
            var fullComment = this.getAttribute('data-full-comment');
            document.getElementById('modalCommentText').textContent = fullComment;
            document.getElementById('modalOverlay').style.display = 'block';
            document.getElementById('commentModal').style.display = 'block';
        });
    });

    document.getElementById('closecomment').addEventListener('click', function () {
        document.getElementById('commentModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    });

    document.getElementById('modalOverlay').addEventListener('click', function () {
        document.getElementById('commentModal').style.display = 'none';
        this.style.display = 'none';
    });

    // document.addEventListener("DOMContentLoaded", function () {
    //     document.querySelectorAll('.comment-cell').forEach(function(cell) {
    //         cell.addEventListener('click', function() {
    //             console.log('get full comment');
    //             const fullComment = this.getAttribute('data-full-comment');
    //             document.getElementById('modalCommentText').innerText = fullComment;
    //             document.getElementById('modalOverlay').style.display = 'block';
    //             document.getElementById('commentModal').style.display = 'block';
    //         });
    //     });
    // });


    // 點擊顯示完整評論
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.comment-cell').forEach(function(cell) {
            cell.addEventListener('click', function() {
                var fullComment = this.getAttribute('data-full-comment');
                document.getElementById('modalCommentText').textContent = fullComment;
                document.getElementById('modalOverlay').style.display = 'block';
                document.getElementById('commentModal').style.display = 'block';
            });
        });
    
        document.getElementById('closecomment').addEventListener('click', function () {
            document.getElementById('commentModal').style.display = 'none';
            document.getElementById('modalOverlay').style.display = 'none';
        });
    
        document.getElementById('modalOverlay').addEventListener('click', function () {
            document.getElementById('commentModal').style.display = 'none';
            this.style.display = 'none';
        });
    });
    
});

