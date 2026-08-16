<?php
// index.php
require_once 'includes/header.php';
global $conn;

$featuredFoods = $conn->query("SELECT * FROM foods LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <h1><i class="bi bi-house-heart-fill"></i> Cơm Nhà Linh</h1>
                <p class="lead">Cơm văn phòng ngon - Giá rẻ - Giao hàng nhanh trong 30 phút</p>
                <a href="menu.php" class="btn btn-warning btn-lg"><i class="bi bi-bag-check"></i> Xem Thực Đơn</a>
            </div>
        </div>
        <div class="restaurant-info text-center">
            <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($restaurant['address']); ?>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <i class="bi bi-telephone-fill"></i> <?php echo $restaurant['phone']; ?>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <i class="bi bi-clock-fill"></i> <?php echo $restaurant['opening_hours']; ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center fw-bold"><i class="bi bi-heart-fill text-danger"></i> Món ăn phổ biến</h2>
        <div class="row g-4">
            <?php foreach ($featuredFoods as $food): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card food-card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($food['category']); ?></span>
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($food['name']); ?></h5>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars($food['description']); ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="h5 mb-0 text-danger"><?php echo formatCurrency($food['price']); ?></span>
                            <button class="btn btn-sm btn-primary" onclick="addToCart(<?php echo $food['id']; ?>)">
                                <i class="bi bi-cart-plus"></i> Thêm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>