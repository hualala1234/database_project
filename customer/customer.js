document.addEventListener("DOMContentLoaded", function () {
    function toggleDropdown() {
        var dropdown = document.getElementById("myDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }
    window.onclick = function(event) {
        var dropdown = document.getElementById("myDropdown");
        if (!event.target.closest('.dropdown') && dropdown && dropdown.style.display === "block") {
            dropdown.style.display = "none";
        }
    }

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

    function addToCart() {
        const pid = document.getElementById('modalPid').value;
        const mid = document.getElementById('modalMid').value;
        const quantity = parseInt(document.getElementById('quantity').value);
        const note = document.getElementById('specialNote').value;

        fetch('add_to_cart.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ pid, mid, quantity, note })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('已加入購物車');
            updateCartCount();  // 呼叫更新購物車數量的函式
          } else {
            alert('加入失敗：' + data.message);
          }
        });
    }

    function updateCartCount() {
        fetch('cart_count.php')
            .then(res => res.json())
            .then(data => {
                document.querySelector('.fa-cart-shopping + span').textContent = data.count;
        });
    }

});