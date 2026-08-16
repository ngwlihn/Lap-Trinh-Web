/**
 * main.js - Frontend JavaScript cho LinhFood
 * Xử lý các tương tác AJAX với PHP backend
 */

// ============================================
// CART FUNCTIONS
// ============================================

/**
 * Thêm sản phẩm vào giỏ hàng
 * @param {number} foodId - ID món ăn
 * @param {number} quantity - Số lượng (mặc định = 1)
 */
function addToCart(foodId, quantity = 1) {
  fetch("api/add_to_cart.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "food_id=" + foodId + "&quantity=" + quantity,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification("Đã thêm vào giỏ hàng!", "success");
        updateCartBadge(data.cart_count);
      } else {
        showNotification(data.message || "Có lỗi xảy ra", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Lỗi kết nối đến server", "error");
    });
}

/**
 * Cập nhật số lượng sản phẩm trong giỏ
 * @param {number} foodId - ID món ăn
 * @param {number} quantity - Số lượng mới
 */
function updateCartQuantity(foodId, quantity) {
  if (quantity < 0) return;

  fetch("api/update_cart.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "food_id=" + foodId + "&quantity=" + quantity,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        location.reload();
      } else {
        showNotification(data.message || "Cập nhật thất bại", "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Lỗi kết nối đến server", "error");
    });
}

/**
 * Xóa sản phẩm khỏi giỏ hàng
 * @param {number} foodId - ID món ăn
 */
function removeFromCart(foodId) {
  if (confirm("Bạn có chắc chắn muốn xóa món này khỏi giỏ hàng?")) {
    fetch("api/remove_from_cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "food_id=" + foodId,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showNotification("Đã xóa khỏi giỏ hàng", "success");
          location.reload();
        } else {
          showNotification(data.message || "Xóa thất bại", "error");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showNotification("Lỗi kết nối đến server", "error");
      });
  }
}

/**
 * Xóa toàn bộ giỏ hàng
 */
function clearCart() {
  if (confirm("Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?")) {
    fetch("api/clear_cart.php", {
      method: "POST",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showNotification("Đã xóa toàn bộ giỏ hàng", "success");
          location.reload();
        } else {
          showNotification(data.message || "Xóa thất bại", "error");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showNotification("Lỗi kết nối đến server", "error");
      });
  }
}

/**
 * Cập nhật badge hiển thị số lượng giỏ hàng
 * @param {number} count - Số lượng sản phẩm
 */
function updateCartBadge(count) {
  const badges = document.querySelectorAll(".cart-badge");
  badges.forEach((badge) => {
    if (count > 0) {
      badge.textContent = count;
      badge.style.display = "inline-block";
    } else {
      badge.style.display = "none";
    }
  });
}

// ============================================
// FILTER FUNCTIONS
// ============================================

/**
 * Submit form lọc menu
 */
function applyFilters() {
  const form = document.getElementById("filter-form");
  if (form) {
    form.submit();
  }
}

/**
 * Reset tất cả bộ lọc
 */
function resetFilters() {
  window.location.href = "menu.php";
}

/**
 * Xử lý filter theo khoảng giá
 */
function filterByPrice() {
  const minPrice = document.getElementById("min-price")?.value;
  const maxPrice = document.getElementById("max-price")?.value;
  if (minPrice && maxPrice) {
    window.location.href = `menu.php?min_price=${minPrice}&max_price=${maxPrice}`;
  }
}

// ============================================
// FORM VALIDATION
// ============================================

/**
 * Validate form đăng ký
 * @returns {boolean}
 */
function validateRegisterForm() {
  const username = document.getElementById("username")?.value;
  const password = document.getElementById("password")?.value;
  const confirmPassword = document.getElementById("confirm_password")?.value;
  const fullname = document.getElementById("fullname")?.value;
  const phone = document.getElementById("phone")?.value;

  if (!username || username.length < 3) {
    showNotification("Tên đăng nhập phải có ít nhất 3 ký tự", "error");
    return false;
  }

  if (!password || password.length < 6) {
    showNotification("Mật khẩu phải có ít nhất 6 ký tự", "error");
    return false;
  }

  if (password !== confirmPassword) {
    showNotification("Mật khẩu xác nhận không khớp", "error");
    return false;
  }

  if (!fullname || fullname.length < 2) {
    showNotification("Vui lòng nhập họ tên đầy đủ", "error");
    return false;
  }

  const phoneRegex = /^0\d{9,10}$/;
  if (!phoneRegex.test(phone)) {
    showNotification("Số điện thoại không hợp lệ", "error");
    return false;
  }

  return true;
}

/**
 * Validate form thanh toán
 * @returns {boolean}
 */
function validateCheckoutForm() {
  const fullname = document.getElementById("fullname")?.value;
  const phone = document.getElementById("phone")?.value;
  const address = document.getElementById("address")?.value;
  const terms = document.getElementById("terms")?.checked;

  if (!fullname || fullname.length < 2) {
    showNotification("Vui lòng nhập họ tên", "error");
    return false;
  }

  const phoneRegex = /^0\d{9,10}$/;
  if (!phoneRegex.test(phone)) {
    showNotification(
      "Số điện thoại không hợp lệ (10-11 số, bắt đầu bằng 0)",
      "error",
    );
    return false;
  }

  if (!address || address.length < 10) {
    showNotification("Vui lòng nhập địa chỉ chi tiết", "error");
    return false;
  }

  if (!terms) {
    showNotification("Vui lòng đồng ý với điều khoản dịch vụ", "error");
    return false;
  }

  return true;
}

// ============================================
// UI HELPERS
// ============================================

/**
 * Hiển thị thông báo
 * @param {string} message - Nội dung thông báo
 * @param {string} type - Loại: 'success', 'error', 'info'
 */
function showNotification(message, type = "success") {
  // Kiểm tra nếu đã có container toast
  let container = document.querySelector(".toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container position-fixed bottom-0 end-0 p-3";
    document.body.appendChild(container);
  }

  const toastId = "toast-" + Date.now();
  const bgColor =
    type === "error" ? "danger" : type === "info" ? "info" : "success";

  const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${bgColor} border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

  container.insertAdjacentHTML("beforeend", toastHTML);

  const toastElement = document.getElementById(toastId);
  setTimeout(() => {
    if (toastElement) {
      toastElement.classList.remove("show");
      setTimeout(() => toastElement.remove(), 300);
    }
  }, 3000);
}

/**
 * Format số tiền thành VND
 * @param {number} amount - Số tiền
 * @returns {string}
 */
function formatCurrency(amount) {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(amount);
}

/**
 * Hiển thị loading spinner
 * @param {string} elementId - ID của element cần hiển thị loading
 */
function showLoading(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    element.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>
        `;
  }
}

/**
 * Xóa loading spinner
 * @param {string} elementId - ID của element cần xóa loading
 * @param {string} content - Nội dung thay thế
 */
function hideLoading(elementId, content) {
  const element = document.getElementById(elementId);
  if (element && content) {
    element.innerHTML = content;
  }
}

// ============================================
// PRODUCT FUNCTIONS
// ============================================

/**
 * Tìm kiếm sản phẩm
 * @param {string} keyword - Từ khóa tìm kiếm
 */
function searchProducts(keyword) {
  if (keyword.length > 0) {
    window.location.href = `menu.php?search=${encodeURIComponent(keyword)}`;
  }
}

/**
 * Lọc sản phẩm theo quán
 * @param {number} restaurantId - ID quán ăn
 */
function filterByRestaurant(restaurantId) {
  window.location.href = `menu.php?restaurant=${restaurantId}`;
}

/**
 * Lọc sản phẩm theo danh mục
 * @param {string} category - Tên danh mục
 */
function filterByCategory(category) {
  window.location.href = `menu.php?category=${encodeURIComponent(category)}`;
}

// ============================================
// ORDER FUNCTIONS
// ============================================

/**
 * Hủy đơn hàng
 * @param {number} orderId - ID đơn hàng
 */
function cancelOrder(orderId) {
  if (confirm("Bạn có chắc chắn muốn hủy đơn hàng này?")) {
    fetch(`api/cancel_order.php?id=${orderId}`, {
      method: "POST",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showNotification("Đã hủy đơn hàng thành công", "success");
          location.reload();
        } else {
          showNotification(data.message || "Hủy đơn hàng thất bại", "error");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showNotification("Lỗi kết nối đến server", "error");
      });
  }
}

// ============================================
// EVENT LISTENERS
// ============================================

/**
 * Khởi tạo các event listeners khi DOM đã sẵn sàng
 */
document.addEventListener("DOMContentLoaded", function () {
  // Auto-hide toast notifications sau 3 giây
  const toasts = document.querySelectorAll(".toast");
  toasts.forEach((toast) => {
    setTimeout(() => {
      toast.classList.remove("show");
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  });

  // Xử lý quantity buttons trên trang cart
  const decreaseBtns = document.querySelectorAll(".decrease-quantity");
  const increaseBtns = document.querySelectorAll(".increase-quantity");

  decreaseBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      const foodId = this.dataset.foodId;
      const currentQty = parseInt(this.dataset.currentQty);
      if (currentQty > 1) {
        updateCartQuantity(foodId, currentQty - 1);
      }
    });
  });

  increaseBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      const foodId = this.dataset.foodId;
      const currentQty = parseInt(this.dataset.currentQty);
      updateCartQuantity(foodId, currentQty + 1);
    });
  });

  // Xử lý search input với debounce
  const searchInput = document.getElementById("search-food");
  if (searchInput) {
    let timeout;
    searchInput.addEventListener("input", function () {
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        const keyword = this.value;
        if (keyword.length >= 2 || keyword.length === 0) {
          searchProducts(keyword);
        }
      }, 500);
    });
  }

  // Xử lý payment method change
  const paymentMethods = document.querySelectorAll(
    'input[name="payment_method"]',
  );
  if (paymentMethods.length > 0) {
    paymentMethods.forEach((method) => {
      method.addEventListener("change", function () {
        const bankInfo = document.getElementById("bank-info");
        if (bankInfo) {
          bankInfo.style.display =
            this.value === "bank_transfer" ? "block" : "none";
        }
      });
    });
  }
});

// ============================================
// EXPORT FUNCTIONS FOR GLOBAL USE
// ============================================

// Make functions available globally
window.addToCart = addToCart;
window.updateCartQuantity = updateCartQuantity;
window.removeFromCart = removeFromCart;
window.clearCart = clearCart;
window.applyFilters = applyFilters;
window.resetFilters = resetFilters;
window.filterByPrice = filterByPrice;
window.validateRegisterForm = validateRegisterForm;
window.validateCheckoutForm = validateCheckoutForm;
window.showNotification = showNotification;
window.formatCurrency = formatCurrency;
window.searchProducts = searchProducts;
window.filterByRestaurant = filterByRestaurant;
window.filterByCategory = filterByCategory;
window.cancelOrder = cancelOrder;
