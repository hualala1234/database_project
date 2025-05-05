

document.addEventListener("DOMContentLoaded", function () {
    function openEditModal(pid, mid, quantity, note) {
                
        // 設定 modal 裡的值
            document.getElementById('editPid').value = pid;
            document.getElementById('editMid').value = mid;
            document.getElementById('editQuantity').value = quantity;
            document.getElementById('editNote').value = note;

        // 顯示 modal
            const modalEl = document.getElementById('editCartModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        function saveEdit() {
            const pid = document.getElementById('editPid').value;
            const mid = document.getElementById('editMid').value;
            const quantity = document.getElementById('editQuantity').value;
            const note = document.getElementById('editNote').value;

            fetch('update_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ pid, mid, quantity, note })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                location.reload(); // 重新載入購物車
                }
            });
    }

    function updateQuantity(pid, mid, newQty) {
        if (newQty < 1) return;

        fetch('update_cart_item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ pid, mid, quantity: newQty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // 更新數量欄位
                document.getElementById(`qty-${pid}-${mid}`).value = newQty;

                // 更新小計
                const itemDiv = document.getElementById(`cart-item-${pid}-${mid}`);
                const price = parseFloat(itemDiv.dataset.price);
                const subtotal = price * newQty;
                document.getElementById(`subtotal-${pid}-${mid}`).textContent = `NT$${subtotal}`;

                // 更新購物車 icon 上的數量
                updateCartCount();
            }
        });
    }

    function removeItem(pid, mid) {
        if (!confirm('確定要移除這項商品嗎？')) return;

        fetch('remove_cart_item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ pid, mid })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // 移除 DOM 元素
                const item = document.getElementById(`cart-item-${pid}-${mid}`);
                if (item) item.remove();

                updateCartCount();
            }
        });
    }
    
});