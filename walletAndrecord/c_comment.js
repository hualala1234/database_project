document.addEventListener("DOMContentLoaded", function () {
    // document.getElementById("add").addEventListener("click", function() {
    //     // 跳轉到 addCard.php
    //     window.location.href = "http://localhost/jb_project/image/testing.php";
    // });

    let currentCardId = null;

    document.querySelectorAll('.card').forEach(function(card) {
        card.addEventListener('click', function () {
            const cardId = this.getAttribute('data-card-id');
            console.log('cardid=',cardId);

            // 如果是同一張卡片被重複點擊 → 顯示 transaction_all
            if (currentCardId === cardId) {
                // 顯示全部交易
                document.getElementById('transaction_all').style.display = 'block';
                console.log('顯示全部交易');

                // 隱藏其他交易區塊
                document.querySelectorAll('.transaction_group').forEach(group => {
                    if (group.id !== 'transaction_all') {
                        group.style.display = 'none';
                        console.log('隱藏其他交易區塊');
                    }
                });

                // 清除選取狀態
                document.querySelectorAll('.card').forEach(c => c.classList.remove('clicked'));
                currentCardId = null;
                console.log('清除選取狀態');
            } else {
                // 顯示對應卡片交易區塊
                document.getElementById('transaction_all').style.display = 'none';
                document.querySelectorAll('.transaction_group').forEach(group => {
                    group.style.display = 'none';
                    console.log('顯示對應卡片交易區塊-other display none');
                });

                const target = document.getElementById('transaction_' + cardId);
                if (target) target.style.display = 'block';console.log('顯示對應卡片交易區塊');

                // 更新樣式
                document.querySelectorAll('.card').forEach(c => c.classList.remove('clicked'));
                this.classList.add('clicked');
                console.log('更新樣式');

                currentCardId = cardId;
                console.log('currentid=',currentCardId)
            }
        });
    });

    const rows = document.querySelectorAll('#transaction_table tbody tr');
    const walletBtn = document.getElementById('showWallet');
    const cardBtns = document.querySelectorAll('[data-card]');

    function showAll() {
        rows.forEach(row => row.style.display = '');
    }

    function filterByMethod(method) {
        rows.forEach(row => {
            row.style.display = (row.getAttribute('data-method') === method) ? '' : 'none';
        });
    }

    walletBtn?.addEventListener('click', () => filterByMethod('walletBalance'));

    cardBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const card = btn.getAttribute('data-card');
            filterByMethod(card);
        });
    });

    // 預設顯示全部
    showAll();

    // let currentCardId = null;

    // document.querySelectorAll('.card').forEach(function(card) {
    //     card.addEventListener('click', function () {
    //         const cardId = this.getAttribute('data-card-id');

    //         if (currentCardId === cardId) {
    //             document.getElementById('transaction_all').style.display = 'block';
    //             document.querySelectorAll('.transaction_group').forEach(group => {
    //                 group.style.display = 'none';
    //             });
    //             document.querySelectorAll('.card').forEach(c => c.classList.remove('clicked'));
    //             currentCardId = null;
    //         } else {
    //             document.getElementById('transaction_all').style.display = 'none';
    //             document.querySelectorAll('.transaction_group').forEach(group => {
    //                 group.style.display = 'none';
    //             });
    //             const target = document.getElementById('transaction_' + cardId);
    //             if (target) target.style.display = 'block';
    //             document.querySelectorAll('.card').forEach(c => c.classList.remove('clicked'));
    //             this.classList.add('clicked');
    //             currentCardId = cardId;
    //         }
    //     });
    // });

    
    
});
