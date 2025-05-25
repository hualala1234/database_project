
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
  let globalCartTime = null;
  // ✅ 載入購物車資料
  function loadCart(mid) {
    
    fetch(`cart_data.php?mid=${mid}`)
      .then(res => res.json())
      .then(data => {
        const items = data.items;
        globalCartTime = data.cartTime;
        const tbody = document.querySelector("tbody");
        tbody.innerHTML = "";
        let subtotal = 0;
  
        items.forEach(item => {
          subtotal += item.total;
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <th scope="row" style="background-color: #D5E2D8!important;">
              <div class="d-flex align-items-center">
                <img src="../${item.pPicture}" class="img-fluid me-5 rounded-circle" style="width: 80px; height: 80px;" alt="">
              </div>
            </th>
            <td style="background-color: #D5E2D8!important;"><p class="my-2">${item.pName}</p></td>
            <td style="background-color: #D5E2D8!important;"><p class="my-2">$${item.price}</p></td>
            <td style="background-color: #D5E2D8!important;" class="text-center align-middle">
                <div style="background-color: #D5E2D8!important;"  class="d-flex justify-content-center w-100">
                    <div style="background-color: #D5E2D8!important;" class="input-group quantity my-2" style="width: 100px;">
                        <div  class="input-group-btn">
                            <button class="btn btn-sm btn-minus rounded-circle bg-white text-dark" data-pid="${item.pid}">
                            <i class="fa fa-minus"></i>
                            </button>
                        </div>
                            <input type="text" style="background-color: #D5E2D8; width:10px" class="form-control form-control-sm text-center border-0 quantity-input text-dark" value="${item.quantity}" data-pid="${item.pid}">                        <div class="input-group-btn">
                            <button class="btn btn-sm btn-plus rounded-circle bg-white text-dark" data-pid="${item.pid}">
                            <i class="fa fa-plus bg-white"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </td>
            <td style="background-color: #D5E2D8!important;"><p class="my-2 total-price" data-pid="${item.pid}">$${item.total}</p></td>
            <td style="background-color: #D5E2D8!important;">
              <textarea class="form-control form-control-sm my-1 special-note" 
                        placeholder="輸入備註..." data-pid="${item.pid}">${item.specialNote || ''}</textarea>
            </td>
            <td style="background-color: #D5E2D8!important;">
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
      const id = coupon.dataset.id;
      document.getElementById("selectedCoupon").value = code;
      document.getElementById("selectedCoupon").dataset.id = id; // 儲存 id 在 dataset 裡
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
  
      // 隱藏原價與原運費區塊
      const originalSubtotalSpan = document.querySelector(".original-subtotal");
      const originalDeliverySpan = document.querySelector(".original-delivery-fee");
  
      originalSubtotalSpan.classList.add("d-none");
      originalDeliverySpan.classList.add("d-none");
  
      // 移除 inline style 顏色
      const subtotalSpan = document.querySelector(".subtotal");
      const deliveryFeeSpan = document.querySelector(".delivery-fee");
  
      subtotalSpan.style.removeProperty("color");
      deliveryFeeSpan.style.removeProperty("color");
  
      updateSummary(currentSubtotal, true); // 回復為原始價格，並忽略套用優惠券
    }
  });
  
    

  // ✅ 套用優惠券邏輯
  function applyCoupon(code) {
    appliedCoupon = code;
    
    let deliveryFee = 30;
    let discountRate = 1;
    if (!code) {
        console.warn("⚠️ applyCoupon 被呼叫但沒有收到有效的 code！");
        return;
    }
    if (code === "CLAWWIN15") {
        discountRate = 0.85;
    } else if (code === "CLAWWIN30") {
        discountRate = 0.7;
    } else if (code === "CLAWWIN20") {
        discountRate = 0.8;
    } else if (code === "CLAWSHIP") {
        deliveryFee = 0;
    } else {
      const parsed = parseInt(code); // 嘗試將 code 轉成整數
      if (!isNaN(parsed)) {
          discountRate = 1 - (parsed / 100); // 例如 15 => 0.85
      }
    }
    
    
    console.log("Code input:", code, "Type:", typeof code);  // 檢查是字串還是數字
    const discountedSubtotal = Math.round(currentSubtotal * discountRate);
    const platformFee = Math.ceil(discountedSubtotal * 0.05);  // 使用打折後的小計計算平台費
    const total = discountedSubtotal + platformFee + deliveryFee;
    
    // ✅ 顯示原價與折扣價
    const subtotalSpan = document.querySelector(".subtotal");
    const originalSubtotalSpan = document.querySelector(".original-subtotal");
    if (discountRate < 1) {
        originalSubtotalSpan.textContent = `$${currentSubtotal}`;
        originalSubtotalSpan.classList.remove("d-none");  // 顯示原價並劃掉
        originalSubtotalSpan.style.textDecoration = "line-through"; // 加上劃線效果
        subtotalSpan.textContent = `$${discountedSubtotal}`;  // 顯示折扣後小計
        subtotalSpan.style.color = "#146E57"; // 設定折扣後小計顏色
    } else {
        originalSubtotalSpan.classList.add("d-none");  // 隱藏原價
        subtotalSpan.textContent = `$${currentSubtotal}`;  // 顯示原價
    }
    
    // ✅ 顯示運費與折扣後運費
    const deliveryFeeSpan = document.querySelector(".delivery-fee");
    const originalDeliverySpan = document.querySelector(".original-delivery-fee");
    if (code === "CLAWSHIP") {
        originalDeliverySpan.textContent = "$30";
        originalDeliverySpan.classList.remove("d-none");  // 顯示原運費並劃掉
        originalDeliverySpan.style.textDecoration = "line-through"; // 運費也需要劃掉
        deliveryFeeSpan.textContent = "$0";  // 顯示優惠後運費
        deliveryFeeSpan.style.color = "#146E57"; // 設定折扣後小計顏色
    } else {
        originalDeliverySpan.classList.add("d-none");  // 隱藏原運費
        deliveryFeeSpan.textContent = `$${deliveryFee}`;  // 顯示正常運費
    }
    
    // 顯示平台費與總金額
    document.querySelector(".platform-fee").textContent = `$${platformFee}`;
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
          totalPriceCell.textContent = `$${data.total}`;
        }
  
        if (!isNaN(data.subtotal)) {
          updateSummary(data.subtotal);
        }
      })
      .catch(err => console.error('❌ 更新數量錯誤:', err));
  }
  
  // ✅ 更新小計與總金額
  let platformFeeGlobal = 0;
  let appliedCoupon = null;
  // 把 platformFee 變成全域變數，或傳給 SubmitOrder 函式
  let platformFeeGlobal = 0;
  function updateSummary(subtotal) {

    currentSubtotal = subtotal; // 儲存未打折前的小計
    let platformFee = Math.ceil(subtotal * 0.05);

    platformFeeGlobal = platformFee;  // 儲存起來

    let deliveryFee = 30;
    let total = subtotal + platformFee + deliveryFee;
  
    // 如果有套用優惠券且不忽略優惠券，就重新套用
    if (appliedCoupon && !ignoreCoupon) {
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
    })
    .then(response => response.json())
    .then(data => {
      if (data.redirect) {
        window.location.href = data.redirect; // 手動跳轉
      } else {
        const mid = getQueryParam("mid");
        loadCart(mid); // 重新載入購物車內容
      }
    });
  }
  


  function SubmitOrder(mid) {
    document.getElementById("submitOrderBtn").addEventListener("click", () => {
      const selectedPayment = document.querySelector("input[name='paymentMethod']:checked");
      if (!selectedPayment) {
        alert("請選擇付款方式！");
        return;
      }
      const address = document.getElementById("current-address").textContent.trim();
      const paymentMethod = selectedPayment.value;
      const tNote = document.getElementById("specialNote").value || "";
      const couponCode = document.getElementById("selectedCoupon").value || null;
      console.log("cid：", cid);
      console.log("mid from URL:", mid); // 打印出 mid 的值來檢查
      console.log("備註是：", tNote);
      console.log("使用的付款方式是：", paymentMethod);
      console.log("使用的address是：", address);
  

  
      fetch(`cart_data.php?mid=${mid}`)
        .then(res => res.json())
        .then(data => {
          const items = data.items;
          const cartTime = data.cartTime;
          console.log("items是：", items);
          console.log("cartTime是：", cartTime);
          const totalPriceText = document.querySelector(".grand-total").textContent.replace("$", "");
          const totalPrice = parseInt(totalPriceText);
          const couponId = document.getElementById("selectedCoupon").dataset.id;
          console.log("totalPrice是：", totalPrice);
          console.log("couponId是：", couponId);

          const cartItems = items.map(item => ({
            pid: item.pid,
            price: item.price,
            quantity: item.quantity,
            specialNote: item.specialNote || ''
          }));

  
          fetch(`submit_order.php?mid=${mid}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              mid,
              totalPrice,
              paymentMethod,
              tNote,
              couponCode,
              address_text: address,
              cartItems,
              id: couponId,
              cartTime,
              subtotal: currentSubtotal,

              platformFee: platformFeeGlobal  // 新增傳送 platformFee
              

            })
          })
          .then(res => {
            if (!res.ok) {
              throw new Error('伺服器錯誤');
            }
            return res.json();  // 只有當回應成功時，才解析為 JSON
          })
          .then(result => {
            if (result.success) {
              alert("✅ 訂單送出成功！");
              window.location.href = `index.php?cid=${cid}`; // 送出訂單後導向首頁
            } else {
              alert("❌ 訂單失敗：" + result.error);
            }
          })
          .catch(async error => {
            const errorText = await error.text?.();
            console.error("❌ 錯誤內容：", errorText);
          });
        });
    });
  }
