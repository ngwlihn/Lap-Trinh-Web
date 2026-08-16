<?php
// my_orders.php
require_once 'includes/header.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    setFlash('Vui lòng đăng nhập để xem đơn hàng', 'error');
    redirect('login.php');
    exit();
}

// Lấy danh sách đơn hàng của user
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero py-4">
    <div class="container">
        <h1><i class="bi bi-receipt"></i> Đơn Hàng Của Tôi</h1>
        <p>Theo dõi trạng thái đơn hàng của bạn</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (empty($orders)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Bạn chưa có đơn hàng nào</h4>
                <p>Hãy đặt món ngay để trải nghiệm dịch vụ của chúng tôi!</p>
                <a href="menu.php" class="btn btn-primary mt-2">Đặt Món Ngay</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($orders as $order): ?>
                    <?php
                    // Lấy chi tiết đơn hàng
                    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
                    $stmt->execute([$order['id']]);
                    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Tính số lượng món
                    $totalQuantity = 0;
                    foreach ($items as $item) {
                        $totalQuantity += $item['quantity'];
                    }
                    
                    // Màu sắc theo trạng thái
                    $statusColors = [
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'delivering' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger'
                    ];
                    $statusTexts = [
                        'pending' => '⏳ Chờ xác nhận',
                        'confirmed' => '✅ Đã xác nhận',
                        'delivering' => '🚚 Đang giao hàng',
                        'completed' => '🎉 Hoàn thành',
                        'cancelled' => '❌ Đã hủy'
                    ];
                    $statusColor = $statusColors[$order['status']] ?? 'secondary';
                    $statusText = $statusTexts[$order['status']] ?? $order['status'];
                    ?>
                    
                    <div class="col-12 mb-4">
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap" style="border-bottom: 2px solid #667eea;">
                                <div>
                                    <span class="badge bg-primary me-2 fs-6">
                                        <i class="bi bi-upc-scan"></i> <?php echo $order['order_code']; ?>
                                    </span>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3"></i> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </small>
                                </div>
                                <div>
                                    <span class="badge bg-<?php echo $statusColor; ?> px-3 py-2 fs-6">
                                        <?php echo $statusText; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="mb-3">
                                            <h6 class="text-primary mb-2"><i class="bi bi-cart"></i> Chi tiết đơn hàng:</h6>
                                            <?php foreach ($items as $idx => $item): ?>
                                                <div class="d-flex justify-content-between mb-2 pb-2 <?php echo $idx < count($items)-1 ? 'border-bottom' : ''; ?>">
                                                    <div>
                                                        <span class="fw-medium"><?php echo htmlspecialchars($item['food_name']); ?></span>
                                                        <span class="text-muted ms-2">x<?php echo $item['quantity']; ?></span>
                                                    </div>
                                                    <span class="text-danger fw-bold"><?php echo formatCurrency($item['price'] * $item['quantity']); ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-5">
                                        <div class="ps-md-3">
                                            <div class="mb-3">
                                                <h6 class="text-primary mb-2"><i class="bi bi-truck"></i> Thông tin giao hàng:</h6>
                                                <p class="mb-1"><i class="bi bi-person"></i> <?php echo htmlspecialchars($order['fullname']); ?></p>
                                                <p class="mb-1"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($order['phone']); ?></p>
                                                <p class="mb-0"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($order['address']); ?></p>
                                            </div>
                                            
                                            <div class="border-top pt-2">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span>Tạm tính:</span>
                                                    <span><?php echo formatCurrency($order['subtotal']); ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span>Phí giao hàng:</span>
                                                    <span><?php echo formatCurrency($order['shipping_fee']); ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between fw-bold fs-5 text-danger">
                                                    <span>Tổng cộng:</span>
                                                    <span><?php echo formatCurrency($order['total']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-white py-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <small class="text-muted">
                                            <i class="bi bi-credit-card"></i> 
                                            <?php echo $order['payment_method'] == 'cash' ? '💵 Thanh toán khi nhận hàng' : '🏦 Chuyển khoản ngân hàng'; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>


<?php require_once 'includes/footer.php'; ?>