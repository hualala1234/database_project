function toggleVIP(event) {
    event.stopPropagation();
    const vip = document.getElementById('vip-popup');
    vip.style.display = (vip.style.display === 'none') ? 'block' : 'none';
}

function closeVIP() {
    document.getElementById('vip-popup').style.display = 'none';
}

function addVIPToCart() {
    closeVIP();
    incrementCartCount();
    animateFlyToCart(document.getElementById('vip-image'));
    showVIPMessage();

    // 發送資料給後端 PHP 寫入購物車
    fetch('../checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            item: 'VIP會員',
            quantity: 1,
            price: 500  // 根據實際定價調整
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log('後端回應：', data);
    });
}

function incrementCartCount() {
    const countElem = document.getElementById('cart-count');
    let count = parseInt(countElem.textContent);
    countElem.textContent = count + 1;
}

function animateFlyToCart(sourceElem) {
    const flyImg = sourceElem.cloneNode(true);
    const cart = document.querySelector('.fa-cart-shopping');
    const flyContainer = document.getElementById('fly-container');

    const sourceRect = sourceElem.getBoundingClientRect();
    const cartRect = cart.getBoundingClientRect();

    flyImg.className = 'flying-img';
    flyImg.style.left = sourceRect.left + 'px';
    flyImg.style.top = sourceRect.top + 'px';

    flyContainer.appendChild(flyImg);

    requestAnimationFrame(() => {
        flyImg.style.transform = `translate(${cartRect.left - sourceRect.left}px, ${cartRect.top - sourceRect.top}px) scale(0.2)`;
        flyImg.style.opacity = 0;
    });

    setTimeout(() => {
        flyImg.remove();
    }, 1000);
}

function showVIPMessage() {
    const msg = document.getElementById('vip-message');
    msg.style.display = 'block';
    setTimeout(() => {
        msg.style.display = 'none';
    }, 2000);
}

// 點外部自動關閉 VIP 彈窗
document.addEventListener('click', function(event) {
    const vip = document.getElementById('vip-popup');
    const crown = document.querySelector('.crown');
    if (vip.style.display === 'block' && !vip.contains(event.target) && !crown.contains(event.target)) {
        closeVIP();
    }
});
