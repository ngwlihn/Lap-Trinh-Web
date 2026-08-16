<?php

?>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5><i class="bi bi-shop"></i> Cơm Nhà Linh</h5>
                <p class="text-muted" style="color: rgba(255,255,255,0.7) !important;">
                    Cơm văn phòng ngon - Giá rẻ - Giao hàng nhanh trong 30 phút
                </p>
                <div class="mt-3">
                    <i class="bi bi-facebook me-2" style="font-size: 1.5rem; cursor: pointer;"></i>
                    <i class="bi bi-instagram me-2" style="font-size: 1.5rem; cursor: pointer;"></i>
                    <i class="bi bi-youtube me-2" style="font-size: 1.5rem; cursor: pointer;"></i>
                    <i class="bi bi-tiktok" style="font-size: 1.5rem; cursor: pointer;"></i>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <h5><i class="bi bi-headset"></i> Liên Hệ</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-telephone-fill"></i> Hotline: <strong>0987 654 321</strong></li>
                    <li class="mb-2"><i class="bi bi-envelope-fill"></i> Email: comnhalinh@gmail.com</li>
                    <li class="mb-2"><i class="bi bi-geo-alt-fill"></i> 123 Nguyễn Trãi, Thanh Xuân, Hà Nội</li>
                    <li class="mb-2"><i class="bi bi-clock-fill"></i> Giờ mở cửa: 08:00 - 21:00</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2026 Cơm Nhà Linh - Đặt cơm văn phòng online. All rights reserved.</p>
            <small>
                <a href="admin.php" style="color: rgba(255,255,255,0.5); text-decoration: none;">
                    <i class="bi bi-shield-lock"></i> Quản trị viên
                </a>
            </small>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
// ============================================
// CART FUNCTIONS - REAL TIME UPDATE
// ============================================

function addToCart(foodId, quantity = 1) {
    fetch('api/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'food_id=' + foodId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cart_count);
            showNotification('✓ Đã thêm ' + quantity + ' món vào giỏ hàng!', 'success');
        } else {
            showNotification(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Lỗi kết nối đến server', 'error');
    });
}

function updateCartBadge(count) {
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(badge => {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    });
}

function showNotification(message, type = 'success') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }
    
    const toastId = 'toast-' + Date.now() + '-' + Math.random();
    const bgColor = type === 'error' ? 'danger' : (type === 'info' ? 'info' : 'success');
    const icon = type === 'error' ? 'x-circle-fill' : (type === 'info' ? 'info-circle-fill' : 'check-circle-fill');
    
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${bgColor} border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${icon} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', toastHTML);
    
    setTimeout(() => {
        const toastElement = document.getElementById(toastId);
        if (toastElement) {
            toastElement.classList.remove('show');
            setTimeout(() => toastElement.remove(), 300);
        }
    }, 3000);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        minimumFractionDigits: 0
    }).format(amount);
}

// Make functions available globally
window.addToCart = addToCart;
window.updateCartBadge = updateCartBadge;
window.showNotification = showNotification;
window.formatCurrency = formatCurrency;
</script>
</body>
</html>