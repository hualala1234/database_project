// ✅ 工具：取得 URL 參數
function getQueryParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
  }
  
  // ✅ 初始載入
  document.addEventListener("DOMContentLoaded", () => {
    const mid = getQueryParam("mid");
    
    loadCart(mid);
    SubmitOrder(mid)
  });
  
  // ✅ 載入購物車資料
  function loadCart(mid) {
    
    fetch(`cart_data.php?mid=${mid}`)
      .then(res => res.json())
      .then(items => {
        const tbody = document.querySelector("tbody");
        tbody.innerHTML = "";
        let subtotal = 0;
  
        items.forEach(item => {
          subtotal += item.total;
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <th scope="row">
              <div class="d-flex align-items-center">
                <img src="../${item.pPicture}" class="img-fluid me-5 rounded-circle" style="width: 80px; height: 80px;" alt="">
              </div>
            </th>
            <td><p class="my-2">${item.pName}</p></td>
            <td><p class="my-2">$${item.price}</p></td>
            <td class="text-center align-middle">
                <div class="d-flex justify-content-center w-100">
                    <div class="input-group quantity my-2" style="width: 100px;">
                        <div class="input-group-btn">
                            <button class="btn btn-sm btn-minus rounded-circle bg-white border" data-pid="${item.pid}">
                            <i class="fa fa-minus"></i>
                            </button>
                        </div>
                        <input type="text" style="background-color: #D5E2D8;" class="form-control form-control-sm text-center border-0 quantity-input text-dark" value="${item.quantity}" data-pid="${item.pid}">
                        <div class="input-group-btn">
                            <button class="btn btn-sm btn-plus rounded-circle bg-white border" data-pid="${item.pid}">
                            <i class="fa fa-plus bg-white"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </td>
            <td><p class="my-2 total-price" data-pid="${item.pid}">$${item.total}</p></td>
            <td>
              <textarea class="form-control form-control-sm my-1 special-note" 
                        placeholder="輸入備註..." data-pid="${item.pid}">${item.specialNote || ''}</textarea>
            </td>
            <td>
              <button class="btn btn-md rounded-circle bg-white border my-2" data-remove="${item.pid}">
                <i class="fa fa-trash text-danger"></i>
              </button>
            </td>`;
          tbody.appendChild(tr);
        });
  
        bindCartEvents();
        updateSummary(subtotal);
      });

      // 綁定優惠券點擊事件
  document.querySelectorAll(".use-coupon").forEach(coupon => {
    
    coupon.addEventListener("click", () => {
      const code = coupon.dataset.code;
      document.getElementById("selectedCoupon").value = code;
      document.getElementById("selectedCouponText").textContent = "已套用：" + coupon.textContent.trim();
      applyCoupon(code);
      });
    });
  }
  // ✅ 綁定「不使用優惠券」按鈕
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("clear-coupon")) {
      appliedCoupon = null;
      document.getElementById("selectedCoupon").value = "";
      document.getElementById("selectedCouponText").textContent = "";
      updateSummary(currentSubtotal); // 回復為原始價格
    }
  });
  function SubmitOrder(mid) {
    document.getElementById("submitOrderBtn").addEventListener("click", () => {
      const selectedPayment = document.querySelector("input[name='paymentMethod']:checked");
      console.log(selectedPayment);  // 調試選中項目
      if (!selectedPayment) {
        alert("請選擇付款方式！");
        return;
      }
      
      const paymentMethod = selectedPayment.value;
      const cardName = document.getElementById("cardName").value || "";
      const tNote = document.getElementById("specialNote").value || "";
      const couponCode = document.getElementById("selectedCoupon").value || null;
      
      console.log("mid from URL:", mid); // 打印出 mid 的值來檢查
      
  
      console.log("使用的付款方式是：", paymentMethod);
  
      fetch("cart_data.php")
        .then(res => res.json())
        .then(cartItems => {
          const totalPriceText = document.querySelector(".grand-total").textContent.replace("$", "");
          const totalPrice = parseInt(totalPriceText);
  
          fetch("submit_order.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              mid,
              totalPrice,
              paymentMethod,
              cardName,
              tNote,
              couponCode,
              cartItems
            })
          })
          .then(res => res.json())
          .then(result => {
            if (result.success) {
              alert("✅ 訂單送出成功！");
              window.location.href = "success.html";
            } else {
              alert("❌ 訂單失敗：" + result.error);
            }
          });
        });
    });
  }


  let currentSubtotal = 0;
  let appliedCoupon = null;
    
  // ✅ 套用優惠券邏輯
  function applyCoupon(code) {
    appliedCoupon = code;
    
    let deliveryFee = 30;
    let discountRate = 1;
    
    if (code === "CLAWWIN15") {
      discountRate = 0.85;
    } else if (code === "CLAWWIN20") {
      discountRate = 0.8;
    } else if (code === "CLAWSHIP") {
      deliveryFee = 0;
    }
    
    const discountedSubtotal = Math.round(currentSubtotal * discountRate);
    const platformFee = Math.ceil(currentSubtotal * 0.05); // ✅ 始終以原始小計計算
    const total = discountedSubtotal + platformFee + deliveryFee;
    
    document.querySelector(".subtotal").textContent = `$${discountedSubtotal}`;
    document.querySelector(".platform-fee").textContent = `$${platformFee}`;
    document.querySelector(".delivery-fee").textContent = `$${deliveryFee}`;
    document.querySelector(".grand-total").textContent = `$${total}`;
  }
    

  // ✅ 綁定所有事件
  function bindCartEvents() {
    document.querySelectorAll(".btn-minus").forEach(btn => {
      btn.addEventListener("click", () => {
        const pid = btn.dataset.pid;
        updateQuantity(pid, -1);
      });
    });
  
    document.querySelectorAll(".btn-plus").forEach(btn => {
      btn.addEventListener("click", () => {
        const pid = btn.dataset.pid;
        updateQuantity(pid, 1);
      });
    });
  
    document.querySelectorAll(".special-note").forEach(textarea => {
      textarea.addEventListener("blur", () => updateSpecialNote(textarea));
    });
  
    document.querySelectorAll("[data-remove]").forEach(btn => {
      btn.addEventListener("click", () => {
        const pid = btn.dataset.remove;
        removeItem(pid);
      });
    });
  }
  
  // ✅ 更新數量
  function updateQuantity(pid, change) {
    const input = document.querySelector(`.quantity-input[data-pid="${pid}"]`);
    let quantity = parseInt(input.value) + change;
    if (quantity < 1) quantity = 1;
    input.value = quantity;
  
    fetch('update_quantity.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ pid, quantity })
    })
      .then(res => res.json())
      .then(data => {
        const row = input.closest("tr");
  
        if (data.deleted) {
          row.remove();
          updateSummary(data.subtotal || 0);
          return;
        }
  
        const totalPriceCell = row.querySelector(`.total-price[data-pid="${pid}"]`);
        if (totalPriceCell && !isNaN(data.total)) {
          totalPriceCell.textContent = `$${data.total.toFixed(2)}`;
        }
  
        if (!isNaN(data.subtotal)) {
          updateSummary(data.subtotal);
        }
      })
      .catch(err => console.error('❌ 更新數量錯誤:', err));
  }
  
  // ✅ 更新小計與總金額
  function updateSummary(subtotal) {
    currentSubtotal = subtotal; // 儲存未打折前的小計
    let platformFee = Math.ceil(subtotal * 0.05);
    let deliveryFee = 30;
    let total = subtotal + platformFee + deliveryFee;
  
    // 如果已經有套用優惠券，重新套用
    if (appliedCoupon) {
      applyCoupon(appliedCoupon);
      return;
    }
  
    document.querySelector(".subtotal").textContent = `$${subtotal}`;
    document.querySelector(".platform-fee").textContent = `$${platformFee}`;
    document.querySelector(".delivery-fee").textContent = `$${deliveryFee}`;
    document.querySelector(".grand-total").textContent = `$${total}`;
  }
  
  
  // ✅ 備註更新
  function updateSpecialNote(textarea) {
    const pid = textarea.dataset.pid;
    const specialNote = textarea.value;
  
    fetch('update_special_note.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ pid, specialNote })
    });
  }
  
  // ✅ 移除商品
  function removeItem(pid) {
    fetch('remove_item.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ pid })
    }).then(() => {
      const mid = getQueryParam("mid");
      loadCart(mid); // 重新載入購物車內容
    });
  }
  
  



  
  