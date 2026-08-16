<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
$restaurant = [
    'address' => '123 Nguyễn Trãi, Thanh Xuân, Hà Nội',
    'phone' => '0987654321',
    'opening_hours' => '08:00 - 21:00'
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cơm Nhà Linh - Đặt Cơm Văn Phòng Online</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: #fff !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .navbar-brand:hover { transform: scale(1.05); }
        .nav-link {
            color: rgba(255,255,255,0.95) !important;
            font-weight: 600;
            margin: 0 5px;
            padding: 10px 15px !important;
            border-radius: 10px;
        }
        .nav-link:hover { background: rgba(255,255,255,0.2); }
        .hero {
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        .hero h1 { font-weight: 800; font-size: 2.5rem; margin-bottom: 1rem; }
        .food-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .food-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .btn-primary {
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            border: none;
            font-weight: 600;
            border-radius: 50px;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #d35400 0%, #e67e22 100%);
            transform: translateY(-2px);
        }
        .cart-summary {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }
        .cart-item {
            background: white;
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .checkout-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .filter-section {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        footer {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -12px;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }
        .restaurant-info {
            background: rgba(255,255,255,0.15);
            border-radius: 15px;
            padding: 10px 20px;
            margin-top: 15px;
        }
        @media (max-width: 768px) {
            .hero h1 { font-size: 1.8rem; }
            .navbar-brand { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-house-heart-fill"></i> Cơm Nhà Linh
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-house-door"></i> Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>" href="menu.php">
                        <i class="bi bi-grid-3x3-gap-fill"></i> Thực đơn
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>" href="cart.php">
                        <div class="cart-icon-wrapper position-relative d-inline-block">
                            <i class="bi bi-cart-fill"></i>
                            <span class="cart-badge" style="display: <?php echo getCartCount() > 0 ? 'flex' : 'none'; ?>">
                                <?php echo getCartCount(); ?>
                            </span>
                        </div>
                        Giỏ hàng
                    </a>
                </li>
                <?php if (isLoggedIn()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="my_orders.php"><i class="bi bi-receipt"></i> Đơn hàng</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">
                        <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">
                        <i class="bi bi-person-plus-fill"></i> Đăng ký
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php 
$flash = getFlash(); 
if ($flash): 
?>
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div class="toast align-items-center text-white bg-<?php echo $flash['type'] === 'error' ? 'danger' : 'success'; ?> border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-<?php echo $flash['type'] === 'error' ? 'x-circle-fill' : 'check-circle-fill'; ?> me-2"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>