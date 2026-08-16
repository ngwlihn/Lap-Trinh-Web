<?php
// checkout.php
require_once 'includes/header.php';

// Kiểm tra đăng nhập - Bắt buộc phải đăng nhập mới được thanh toán
if (!isLoggedIn()) {
    setFlash('Vui lòng đăng nhập để đặt hàng', 'error');
    redirect('login.php');
    exit();
}

// Lấy giỏ hàng
$cart = getCart();

// Kiểm tra giỏ hàng có trống không
if (empty($cart)) {
    setFlash('Giỏ hàng trống, vui lòng thêm món ăn', 'error');
    redirect('menu.php');
    exit();
}

// Tính tiền
$subtotal = getCartTotal();
$shippingFee = 15000;
$total = $subtotal + $shippingFee;

// Lấy thông tin user từ database
$stmt = $conn->prepare("SELECT fullname, phone, email, address FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Xử lý submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra lại giỏ hàng
    $cart = getCart();
    if (empty($cart)) {
        setFlash('Giỏ hàng trống, không thể đặt hàng', 'error');
        redirect('cart.php');
        exit();
    }
    
    // Lấy dữ liệu từ form
    $fullname = trim($_POST['fullname'] ?? $userData['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? $userData['phone'] ?? '');
    $email = trim($_POST['email'] ?? $userData['email'] ?? '');
    $address = trim($_POST['address'] ?? $userData['address'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'cash';
    $terms = isset($_POST['terms']);
    
    // Validate dữ liệu
    $errors = [];
    
    if (empty($fullname)) {
        $errors[] = 'Vui lòng nhập họ tên';
    }
    
    if (empty($phone)) {
        $errors[] = 'Vui lòng nhập số điện thoại';
    } elseif (!preg_match('/^0\d{9,10}$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ (10-11 số, bắt đầu bằng 0)';
    }
    
    if (empty($address)) {
        $errors[] = 'Vui lòng nhập địa chỉ giao hàng';
    } elseif (strlen($address) < 10) {
        $errors[] = 'Địa chỉ quá ngắn, vui lòng nhập chi tiết hơn';
    }
    
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }
    
    if (!$terms) {
        $errors[] = 'Vui lòng đồng ý với điều khoản dịch vụ';
    }
    
    // Nếu không có lỗi, tiến hành tạo đơn hàng
    if (empty($errors)) {
        $orderData = [
            'fullname' => $fullname,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'notes' => $notes,
            'payment_method' => $paymentMethod,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total' => $total
        ];
        
        $result = createOrder($orderData);
        
        if ($result['success']) {
            // Xóa giỏ hàng sau khi đặt thành công
            saveCart([]);
            
            // Chuyển đến trang thành công
            setFlash('Đặt hàng thành công! Mã đơn: ' . $result['order_code'], 'success');
            redirect('order_success.php?id=' . $result['order_id']);
            exit();
        } else {
            setFlash('Có lỗi xảy ra: ' . $result['error'], 'error');
        }
    } else {
        // Hiển thị lỗi
        $errorMessage = implode('<br>', $errors);
        setFlash($errorMessage, 'error');
    }
}
?>

<section class="hero py-4">
    <div class="container">
        <h1><i class="bi bi-credit-card"></i> Thanh Toán Đơn Hàng</h1>
        <p>Vui lòng kiểm tra thông tin và hoàn tất đơn hàng</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Form thông tin thanh toán -->
            <div class="col-lg-8">
                <div class="checkout-section">
                    <h4><i class="bi bi-person"></i> Thông Tin Giao Hàng</h4>
                    
                    <form method="POST" id="checkout-form">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" class="form-control" 
                                       value="<?php echo htmlspecialchars($userData['fullname'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số Điện Thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" 
                                       value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>">
                            <small class="text-muted">Chúng tôi sẽ gửi xác nhận đơn hàng qua email này</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Địa Chỉ Giao Hàng <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" 
                                   value="<?php echo htmlspecialchars($userData['address'] ?? ''); ?>" 
                                   placeholder="Số nhà, đường, phường/xã, quận/huyện, thành phố" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ghi Chú (Tùy Chọn)</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Ví dụ: Giao hàng vào giờ hành chính, cầu chua ít, không bỏ rau mùi..."></textarea>
                        </div>
                        
                        <h4 class="mt-4"><i class="bi bi-wallet2"></i> Phương Thức Thanh Toán</h4>
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" id="cash" name="payment_method" value="cash" checked>
                                <label for="cash" class="cursor-pointer">
                                    <i class="bi bi-cash-coin" style="font-size: 2rem; color: #26a65b"></i><br>
                                    <strong>Tiền Mặt</strong><br>
                                    <small class="text-muted">Thanh toán khi nhận hàng</small>
                                </label>
                            </div>
                            <div class="payment-method">
                                <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer">
                                <label for="bank_transfer" class="cursor-pointer">
                                    <i class="bi bi-bank" style="font-size: 2rem; color: #3498db"></i><br>
                                    <strong>Chuyển Khoản</strong><br>
                                    <small class="text-muted">Chuyển khoản ngân hàng</small>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Thông tin chuyển khoản (ẩn ban đầu) -->
                        <div id="bank-info" style="display: none;" class="mt-3 p-3 bg-light rounded">
                            <h6><i class="bi bi-info-circle"></i> Thông Tin Chuyển Khoản</h6>
                            <p class="mb-1"><strong>Ngân Hàng:</strong> Vietcombank</p>
                            <p class="mb-1"><strong>Số Tài Khoản:</strong> 1234567890</p>
                            <p class="mb-1"><strong>Chủ Tài Khoản:</strong> LINHFOOD COMPANY</p>
                            <p class="mb-0"><small class="text-muted">Vui lòng ghi rõ mã đơn hàng khi chuyển khoản</small></p>
                        </div>
                        
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                Tôi đồng ý với <a href="#" class="text-primary">điều khoản dịch vụ</a> và 
                                <a href="#" class="text-primary">chính sách bảo mật</a> của LinhFood <span class="text-danger">*</span>
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Xác Nhận Đặt Hàng
                            </button>
                            <a href="cart.php" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> Quay Lại Giỏ Hàng
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h5><i class="bi bi-receipt"></i> Tóm Tắt Đơn Hàng</h5>
                    
                    <!-- Danh sách sản phẩm -->
                    <div class="mb-3">
                        <?php foreach ($cart as $item): ?>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <div>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <br>
                                <small class="text-muted">x<?php echo $item['quantity']; ?></small>
                            </div>
                            <div class="text-end">
                                <strong class="text-danger"><?php echo formatCurrency($item['price'] * $item['quantity']); ?></strong>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <hr>
                    
                    <!-- Tổng tiền -->
                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <strong><?php echo formatCurrency($subtotal); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Phí giao hàng:</span>
                        <strong><?php echo formatCurrency($shippingFee); ?></strong>
                    </div>
                    <div class="summary-row total">
                        <span>Tổng cộng:</span>
                        <strong class="text-danger"><?php echo formatCurrency($total); ?></strong>
                    </div>
                    
                    <div class="mt-3 p-3 bg-light rounded">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-info-circle"></i> <strong>Thời gian giao hàng:</strong> 30-45 phút
                        </small>
                        <small class="text-muted d-block">
                            <i class="bi bi-shield-check"></i> <strong>Bảo mật:</strong> Thông tin của bạn được bảo mật tuyệt đối
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Hiển thị/ẩn thông tin chuyển khoản khi chọn phương thức thanh toán
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const bankInfo = document.getElementById('bank-info');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'bank_transfer') {
                bankInfo.style.display = 'block';
            } else {
                bankInfo.style.display = 'none';
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>