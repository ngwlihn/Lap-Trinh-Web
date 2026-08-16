<?php
// cart.php
require_once 'includes/header.php';

$cart = getCart();
$subtotal = getCartTotal();
$shippingFee = 30000;
$total = $subtotal + $shippingFee;
?>

<section class="hero py-4">
    <div class="container">
        <h1><i class="bi bi-cart3"></i> Giỏ Hàng Của Bạn</h1>
        <p>Kiểm tra, chỉnh sửa và thanh toán đơn hàng</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mb-4">
                <?php if (empty($cart)): ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-cart-x"></i> Giỏ hàng trống<br><br>
                    <a href="menu.php" class="btn btn-primary">Tiếp Tục Mua Sắm</a>
                </div>
                <?php else: ?>
                
                <!-- Hiển thị thông báo nếu chưa đăng nhập -->
                <?php if (!isLoggedIn()): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Bạn cần <a href="login.php" class="alert-link">đăng nhập</a> để thanh toán. 
                    Chưa có tài khoản? <a href="register.php" class="alert-link">Đăng ký ngay</a>
                </div>
                <?php endif; ?>
                
                <?php foreach ($cart as $id => $item): ?>
                <div class="cart-item">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                            <strong><?php echo formatCurrency($item['price']); ?></strong>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <a href="api/update_cart.php?food_id=<?php echo $id; ?>&quantity=<?php echo $item['quantity']-1; ?>" class="btn btn-outline-secondary">-</a>
                                <input type="text" class="form-control text-center" value="<?php echo $item['quantity']; ?>" readonly>
                                <a href="api/update_cart.php?food_id=<?php echo $id; ?>&quantity=<?php echo $item['quantity']+1; ?>" class="btn btn-outline-secondary">+</a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <strong><?php echo formatCurrency($item['price'] * $item['quantity']); ?></strong>
                        </div>
                        <div class="col-md-1">
                            <a href="api/remove_from_cart.php?food_id=<?php echo $id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa món này?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="d-grid gap-2 d-md-flex justify-content-between mt-4">
                    <a href="menu.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Tiếp Tục Mua Sắm</a>
                    <a href="api/clear_cart.php" class="btn btn-outline-danger" onclick="return confirm('Xóa toàn bộ giỏ hàng?')">
                        <i class="bi bi-trash"></i> Xóa Giỏ Hàng
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="cart-summary">
                    <h5><i class="bi bi-receipt"></i> Tóm Tắt Đơn Hàng</h5>
                    <div class="summary-row">
                        <span>Số lượng:</span>
                        <strong><?php echo getCartCount(); ?> món</strong>
                    </div>
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
                        <strong><?php echo formatCurrency($total); ?></strong>
                    </div>
                    
                    <?php if (!empty($cart)): ?>
                        <?php if (isLoggedIn()): ?>
                            <a href="checkout.php" class="btn btn-primary btn-lg w-100 mt-4">
                                <i class="bi bi-credit-card"></i> Tiến Hành Thanh Toán
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary btn-lg w-100 mt-4">
                                <i class="bi bi-box-arrow-in-right"></i> Đăng Nhập Để Thanh Toán
                            </a>
                            <div class="text-center mt-2">
                                <small>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></small>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>