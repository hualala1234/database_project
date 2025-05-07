// Smooth Scroll for Category Links
document.querySelectorAll('a.category-item').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const targetId = this.getAttribute('href').substring(1); // 去掉 #
      const target = document.getElementById(targetId);
      if (target) {
        const offset = -5; // navbar 高度
        const bodyRect = document.body.getBoundingClientRect().top;
        const elementRect = target.getBoundingClientRect().top;
        const elementPosition = elementRect - bodyRect;
        const offsetPosition = elementPosition - offset;
  
        window.scrollTo({
          top: offsetPosition,
          behavior: "smooth"
        });
      }
    });
  });
  
  // Open Product Modal
    function openProductModal(name, description, price, imageUrl, pid, mid) {
        document.getElementById('productModalLabel').textContent = name;
        document.getElementById('modalProductDescription').textContent = description;
        document.getElementById('modalProductPrice').textContent = 'NT$ ' + price;
        document.getElementById('modalProductImage').src = imageUrl;
        document.getElementById('specialNote').value = '';
        document.getElementById('quantity').value = 1;
        document.getElementById('modalPid').value = pid;
        document.getElementById('modalMid').value = mid;
    }
  

  // Add to Cart
    function addToCart() {
        const pid = document.getElementById('modalPid').value;
        const mid = document.getElementById('modalMid').value;
        const quantity = parseInt(document.getElementById('Quantity').value); // 確保是數字
        console.log('Quantity:', quantity);
        const note = document.getElementById('specialNote').value;
    
        console.log(`Adding product to cart - PID: ${pid}, MID: ${mid}, Quantity: ${quantity}, Special Note: ${note}`); // 調試，確保數量正確
    
        fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pid, mid, quantity, note })
        })
        .then(res => res.json())
        .then(data => {
        if (data.success) {
            document.querySelector('#productModal .btn-close').click();
            updateCartCount();
            window.location.reload(); // 重新加載頁面來反映更改
        }
        });
    }
  
  
    // Update Cart Count
    function updateCartCount() {
        fetch('cart_count.php')
        .then(res => res.json())
        .then(data => {
            document.querySelector('.fa-cart-shopping + span').textContent = data.count;
        });
    }
  
    // 增加商品數量
    function handleIncrease(pid, mid) {
        const input = document.getElementById(`qty-${pid}-${mid}`);
        const currentQty = parseInt(input.value);
        updateQuantity(pid, mid, currentQty + 1);
    }
  
    // 減少商品數量
    function handleDecrease(pid, mid) {
        const input = document.getElementById(`qty-${pid}-${mid}`);
        const currentQty = parseInt(input.value);
        if (currentQty > 1) {
        updateQuantity(pid, mid, currentQty - 1);
        }
    }
  
    // 更新商品數量與畫面
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
            // 更新數量輸入框
            document.getElementById(`qty-${pid}-${mid}`).value = newQty;
    
            // 更新商品小計（整數）
            const itemDiv = document.getElementById(`cart-item-${pid}-${mid}`);
            const price = parseFloat(itemDiv.dataset.price);
            const subtotal = price * newQty;
            document.getElementById(`subtotal-${pid}-${mid}`).textContent = `NT$${Math.round(subtotal)}`;
    
            // 更新店家總小計
            updateStoreSubtotal(mid);
    
            // 更新購物車總數（若你有設這功能）
            updateCartCount();
        }
        });
    }
  
    // 更新某間店家的小計總額
    function updateStoreSubtotal(mid) {
        let storeSubtotal = 0;
        const itemDivs = document.querySelectorAll(`[id^="cart-item-"][id$="-${mid}"]`);
    
        itemDivs.forEach(div => {
        const price = parseFloat(div.dataset.price);
        const pid = div.id.split('-')[2]; // 從 ID 拆出 pid
        const qtyInput = document.getElementById(`qty-${pid}-${mid}`);
        if (qtyInput) {
            const qty = parseInt(qtyInput.value);
            storeSubtotal += price * qty;
        }
        });
    
        const subtotalEl = document.getElementById(`store-subtotal-${mid}`);
        if (subtotalEl) {
        subtotalEl.textContent = `NT$${Math.round(storeSubtotal)}`;
        }
    }
  
  
    // Remove Item from Cart
    function removeItem(pid, mid) {
        fetch('remove_cart_item.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pid, mid })
        })
        .then(res => res.json())
        .then(data => {
        if (data.success) {
            updateCartCount();
            const itemRow = document.getElementById(`cart-item-${pid}-${mid}`);
            if (itemRow) {
            itemRow.closest('div[style*="display: flex"]').remove();
            }
            let subtotal = 0;
            const itemRows = document.querySelectorAll(`[id^="cart-item-"][id$="-${mid}"]`);
            itemRows.forEach(row => {
            const price = parseInt(row.dataset.price);
            const qtyInput = row.querySelector('input[id^="qty-"]');
            const qty = parseInt(qtyInput.value);
            subtotal += price * qty;
            });
            const subtotalElement = document.getElementById(`store-subtotal-${mid}`);
            if (subtotalElement) {
            subtotalElement.textContent = `NT$${subtotal}`;
            }
            if (itemRows.length === 0) {
            subtotalElement.closest('.mb-4').remove();
            }
            

        } else {
            alert('刪除失敗');
        }
        });
    }
    
    // Open Edit Modal for Cart Item
    function openEditModal(pid, mid, quantity, note) {
        document.getElementById('editPid').value = pid;
        document.getElementById('editMid').value = mid;
        document.getElementById('editQuantity').value = quantity;
        document.getElementById('editNote').value = note;
    
        const modalEl = document.getElementById('editCartModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
    
    // Save Edit for Cart Item
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
            location.reload();
        }
        });
    }
    function updateCartCount() {
        fetch('cart_count.php')
            .then(res => res.json())
            .then(data => {
                // 確保這裡更新的是商家數量
                document.querySelector('.fa-cart-shopping + span').textContent = data.count;  
            })
            .catch(error => {
                console.error("更新購物車數量失敗:", error);
            });
    }
    
    
    window.addEventListener('load', function () {
        updateCartCount();  // 頁面載入後即更新一次購物車數量
    });