// ✅ 點擊 crown 顯示 .vip 彈窗
function toggleVIP(event) {
    event.stopPropagation();
    const vip = document.getElementById('vip-popup');
    vip.style.display = (vip.style.display === 'none') ? 'block' : 'none';
}

// ✅ 關閉 .vip 彈窗
function closeVIP() {
    document.getElementById('vip-popup').style.display = 'none';
}

// ✅ 點擊「我要加入VIP」後出現確認視窗
function confirmJoinVIP() {
    closeVIP();
    if (confirm("確認支付 499 元加入 VIP 嗎？\n付款後無法退款。")) {
        joinVIP();
    }
}

// ✅ 執行加入 VIP 的後端請求
function joinVIP() {
    fetch('./join_vip.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'join' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const crown = document.querySelector('.crown');
            crown.src = './is_vip.png';
            crown.style.cursor = 'default';
            crown.onclick = null;
            // 隱藏 crown，顯示成功訊息
            closeVIP();
            document.querySelector('.crown').style.display = 'none';
            showVIPMessage("成功加入 VIP！");
            setTimeout(() => location.reload(), 1500);
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error("❌ 錯誤：", err);
        alert("伺服器錯誤，請稍後再試。");
    });
}

// ✅ 顯示加入成功訊息
function showVIPMessage(message = "已成功加入 VIP！") {
    const msg = document.getElementById('vip-message');
    msg.textContent = message;
    msg.style.display = 'block';
    setTimeout(() => {
        msg.style.display = 'none';
    }, 2000);
}

// ✅ 點擊外部自動關閉 VIP 彈窗
document.addEventListener('click', function(event) {
    const vip = document.getElementById('vip-popup');
    const crown = document.querySelector('.crown');
    if (vip.style.display === 'block' && !vip.contains(event.target) && !crown.contains(event.target)) {
        closeVIP();
    }
});