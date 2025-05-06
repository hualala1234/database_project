document.addEventListener("DOMContentLoaded", function () {
    // document.getElementById("add").addEventListener("click", function() {
    //     // 跳轉到 addCard.php
    //     window.location.href = "http://localhost/jb_project/image/testing.php";
    // });

    // let currentCardId = null;

    // document.querySelectorAll('.card').forEach(function(card) {
    //     card.addEventListener('click', function () {
    //         const cardId = this.getAttribute('data-card-id');

    //         // 如果是同一張卡片被重複點擊 → 顯示 transaction_all
    //         if (currentCardId === cardId) {
    //             // 顯示全部交易
    //             document.getElementById('transaction_all').style.display = 'block';

    //             // 隱藏其他交易區塊
    //             document.querySelectorAll('.transaction_group').forEach(group => {
    //                 if (group.id !== 'transaction_all') {
    //                     group.style.display = 'none';
    //                 }
    //             });

    //             // 清除選取狀態
    //             document.querySelectorAll('.card').forEach(c => c.classList.remove('clicked'));
    //             currentCardId = null;
    //         } else {
    //             // 顯示對應卡片交易區塊
    //             document.getElementById('transaction_all').style.display = 'none';
    //             document.querySelectorAll('.transaction_group').forEach(group => {
    //                 group.style.display = 'none';
    //             });

    //             const target = document.getElementById('transaction_' + cardId);
    //             if (target) target.style.display = 'block';

    //             // 更新樣式
    //             document.querySelectorAll('.card').forEach(c => c.classList.remove('clicked'));
    //             this.classList.add('clicked');

    //             currentCardId = cardId;
    //         }
    //     });
    // });


    // // click card to view the card
    // // 取得 DOM 元素
    // const card = document.getElementById("card1"); // !!!要改成點哪張卡片就出現哪張卡片的訊息
    // const showCard = document.getElementById("showCard");
    // const closeshowBtn = document.getElementById("closeshow");

    // showCard.style.display = "none";
    // // 點 card 顯示 showCard 區塊
    // card.addEventListener("click", function () {
    //     showCard.style.display = "block";
    // });

    // // 點 close 關閉 showCard
    // closeshowBtn.addEventListener("click", function () {
    //     showCard.style.display = "none";
    // });
    // document.getElementById("submit").addEventListener("click", function () {
    //     const cardName = document.getElementById("cardName").value.trim();
    //     const bankCode = document.getElementById("bankCode").value.trim();
    //     const cardType = document.querySelector('input[name="c_type"]:checked');
    //     const cardNumber = document.getElementById("cardNumber").value.trim;
    //     const expirationDate = document.getElementById("expirationDate").value.trim();
    //     const cardCVV = document.getElementById("cardCVV").value.trim();
        
    //     if(!cardName || !bankCode || !cardType || !cardNumber || !expirationDate || !cardCVV) {
    //         alert("Please fill in all fields.");
    //         return;
    //     }

    //     const formData = new FormData();
    //     formData.append("cardName", cardName);
    //     formData.append("bankCode", bankCode);
    //     formData.append("cardType", cardType.value);
    //     formData.append("cardNumber", cardNumber);
    //     formData.append("expirationDate", expirationDate+"-01");
    //     formData.append("cardCVV", cardCVV);

    //     // 看不懂QQ
    //     fetch("/save_card/", {
    //         method: "POST",
    //         headers: {
    //             "X-CSRFToken": getCookie("csrftoken")  // Django 需要 CSRF token 驗證
    //         },
    //         body: formData
    //     })
    //     .then(response => {
    //         if (response.ok) {
    //             window.location.href = "wallet.html";  // 儲存成功，跳轉
    //         } else {
    //             alert("儲存失敗！");
    //         }
    //     })
    //     .catch(error => {
    //         console.error("發生錯誤：", error);
    //         alert("發送資料時發生錯誤！");
    //     });
        
    // });

    // // 編輯卡片功能
    // // Get references to elements
    // const editButton = document.getElementById('edit');
    // // const showCard = document.getElementById('showCard');
    // const editCard = document.getElementById('editCard');
    // const submitChanges = document.getElementById('submitChanges');

    // // Current card data
    // let cardData = {
    //     cardName: "Card Name",
    //     bankCode: "1234",
    //     cardNumber: "5678 9876 5432 1234",
    //     cardType: "VISA",
    //     expirationDate: "12/23",
    //     cvv: "123"
    // };

    // // When the "edit" button is clicked, show the editable form
    // editButton.addEventListener('click', function() {
    //     // Hide the original card info
    //     showCard.style.display = "none";

    //     // Show the editable form
    //     editCard.style.display = "block";

    //     // Populate the form with current card values
    //     document.getElementById('cardNameInput').value = cardData.cardName;
    //     document.getElementById('bankCodeInput').value = cardData.bankCode;
    //     document.getElementById('cardNumberInput').value = cardData.cardNumber;
    //     document.getElementById('cardTypeInput').value = cardData.cardType;
    //     document.getElementById('expirationDateInput').value = cardData.expirationDate;
    //     document.getElementById('cvvInput').value = cardData.cvv;
    // });

    // // When the submit button is clicked, update the card data and return to the original view
    // submitChanges.addEventListener('click', function() {
    //     // Get the new values from the form
    //     cardData.cardName = document.getElementById('cardNameInput').value;
    //     cardData.bankCode = document.getElementById('bankCodeInput').value;
    //     cardData.cardNumber = document.getElementById('cardNumberInput').value;
    //     cardData.cardType = document.getElementById('cardTypeInput').value;
    //     cardData.expirationDate = document.getElementById('expirationDateInput').value;
    //     cardData.cvv = document.getElementById('cvvInput').value;

    //     // Update the display with the new values
    //     document.getElementById('cardNameDisplay').textContent = cardData.cardName;
    //     document.getElementById('bankCodeDisplay').textContent = `Bank Code：${cardData.bankCode}`;
    //     document.getElementById('cardNumberDisplay').textContent = `Card Number：${cardData.cardNumber}`;
    //     document.getElementById('cardTypeDisplay').textContent = `Card Type：${cardData.cardType}`;
    //     document.getElementById('expirationDateDisplay').textContent = `Expiration Date：${cardData.expirationDate}`;
    //     document.getElementById('cvvDisplay').textContent = `Card CVV：${cardData.cvv}`;

    //     // Hide the editable form and show the original display again
    //     editCard.style.display = "none";
    //     showCard.style.display = "block";
    // });

   
    // // // 新增卡片功能
    // const addImage = document.getElementById('add');  // The Add image
    // const addCardPage = document.getElementById('add_card_page');  // The div with class "add_card_page"
    // const closeaddBtn = document.getElementById('closeadd');  // The Close button

    // addCardPage.style.display = "none";
    // // Function to show the add_card_page div
    // addImage.addEventListener('click', function() {
    //     addCardPage.style.display = "block";
    // });

    // // Function to hide the add_card_page div when close button is clicked
    // closeaddBtn.addEventListener('click', function() {
    //     addCardPage.style.display = "none";
    // });

    // // Close the add_card_page div if the user clicks anywhere outside the modal content
    // window.onclick = function(event) {
    //     if (event.target == addCardPage) {
    //         addCardPage.style.display = "none";
    //     }
    // };
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

    
    
});
