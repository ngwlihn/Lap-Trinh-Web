<?php
// order_success.php
require_once 'includes/header.php';

// Lấy ID đơn hàng từ URL
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$orderId) {
    redirect('index.php');
}

// Lấy thông tin đơn hàng
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    redirect('index.php');
}

// Lấy chi tiết đơn hàng
$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero py-4">
    <div class="container">
        <h1><i class="bi bi-check-circle"></i> Đặt Hàng Thành Công!</h1>
        <p>Cảm ơn bạn đã tin tưởng và đặt hàng tại LinhFood</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="checkout-section text-center">
                    <!-- Icon thành công -->
                    <div style="font-size: 5rem; color: #26a65b;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    
                    <h3 class="mt-3">Đặt hàng thành công!</h3>
                    <p class="text-muted">Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đã được ghi nhận.</p>
                    
                    <!-- Mã đơn hàng -->
                    <div class="alert alert-info mt-3">
                        <strong>Mã đơn hàng:</strong> 
                        <span class="badge bg-primary fs-6"><?php echo $order['order_code']; ?></span>
                    </div>
                    
                    <!-- Thông tin đơn hàng -->
                    <div class="text-start mt-4">
                        <h5>Thông tin đơn hàng:</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 40%">Ngày đặt:</th>
                                <td><?php echo date('d/m/Y H:i:s', strtotime($order['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <th>Người nhận:</th>
                                <td><?php echo htmlspecialchars($order['fullname']); ?></td>
                            </tr>
                            <tr>
                                <th>Số điện thoại:</th>
                                <td><?php echo htmlspecialchars($order['phone']); ?></td>
                            </tr>
                            <tr>
                                <th>Địa chỉ giao hàng:</th>
                                <td><?php echo nl2br(htmlspecialchars($order['address'])); ?></td>
                            </tr>
                            <tr>
                                <th>Phương thức thanh toán:</th>
                                <td><?php echo $order['payment_method'] == 'cash' ? 'Tiền mặt khi nhận hàng' : 'Chuyển khoản ngân hàng'; ?></td>
                            </tr>
                            <?php if ($order['notes']): ?>
                            <tr>
                                <th>Ghi chú:</th>
                                <td><?php echo nl2br(htmlspecialchars($order['notes'])); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                        
                        <h5 class="mt-4">Chi tiết đơn hàng:</h5>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['food_name']); ?></td>
                                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                                    <td class="text-end"><?php echo formatCurrency($item['price']); ?></td>
                                    <td class="text-end"><?php echo formatCurrency($item['price'] * $item['quantity']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Tạm tính:</th>
                                    <td class="text-end"><?php echo formatCurrency($order['subtotal']); ?></td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Phí giao hàng:</th>
                                    <td class="text-end"><?php echo formatCurrency($order['shipping_fee']); ?></td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Tổng cộng:</th>
                                    <td class="text-end"><strong class="text-danger fs-5"><?php echo formatCurrency($order['total']); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Thông báo giao hàng -->
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-truck"></i> <strong>Dự kiến giao hàng:</strong> 30-45 phút
                    </div>
                    
                    <!-- Nút hành động -->
                    <div class="mt-4">
                        <a href="menu.php" class="btn btn-primary">
                            <i class="bi bi-cart-plus"></i> Tiếp Tục Mua Sắm
                        </a>
                        <a href="my_orders.php" class="btn btn-outline-secondary">
                            <i class="bi bi-receipt"></i> Xem Đơn Hàng Của Tôi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>