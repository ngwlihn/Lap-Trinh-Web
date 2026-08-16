<?php
// admin.php - Đặt trong thư mục gốc (cùng cấp với index.php)
session_start();

// Tài khoản mặc định (chỉ biết trong code)
$ADMIN_USER = 'admin';
$ADMIN_PASS = 'admin';

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['username'] === $ADMIN_USER && $_POST['password'] === $ADMIN_PASS) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_name'] = 'Administrator';
    } else {
        $error = 'Sai tài khoản hoặc mật khẩu!';
    }
}

// Xử lý đăng xuất
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit();
}

// Xử lý cập nhật trạng thái (CHỈ KHI CÓ DATABASE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    // Kiểm tra file database tồn tại
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['order_id']]);
        $success = 'Đã cập nhật trạng thái đơn hàng!';
    }
}

// Xử lý xóa đơn hàng (CHỈ KHI CÓ DATABASE)
if (isset($_GET['delete'])) {
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $success = 'Đã xóa đơn hàng!';
    }
}

// Hàm kiểm tra database có tồn tại không
$hasDatabase = file_exists('config/database.php');

// Nếu có database thì lấy dữ liệu thật
if ($hasDatabase) {
    require_once 'config/database.php';
    $total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    $confirmed_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'confirmed'")->fetchColumn();
    $delivering_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'delivering'")->fetchColumn();
    $completed_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'")->fetchColumn();
    $cancelled_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'")->fetchColumn();
    $total_revenue = $conn->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status = 'completed'")->fetchColumn();

    $statusFilter = $_GET['status'] ?? 'all';
    if ($statusFilter !== 'all') {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
        $stmt->execute([$statusFilter]);
        $orders = $stmt->fetchAll();
    } else {
        $orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
    }
} else {
    // Dữ liệu mẫu khi chưa có database
    $total_orders = 0;
    $pending_orders = 0;
    $confirmed_orders = 0;
    $delivering_orders = 0;
    $completed_orders = 0;
    $cancelled_orders = 0;
    $total_revenue = 0;
    $orders = [];
}

$statusText = [
    'pending' => ['text' => 'Chờ xác nhận', 'class' => 'warning', 'icon' => 'clock-history'],
    'confirmed' => ['text' => 'Đã xác nhận', 'class' => 'info', 'icon' => 'check-circle'],
    'delivering' => ['text' => 'Đang giao', 'class' => 'primary', 'icon' => 'truck'],
    'completed' => ['text' => 'Hoàn thành', 'class' => 'success', 'icon' => 'check-circle-fill'],
    'cancelled' => ['text' => 'Đã hủy', 'class' => 'danger', 'icon' => 'x-circle']
];

// Nếu chưa đăng nhập - Hiển thị form login
if (!isset($_SESSION['admin'])) {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - Cơm Nhà Linh</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                position: relative;
            }
            body::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.08)" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
                background-size: cover;
            }
            .login-card {
                background: white;
                border-radius: 30px;
                padding: 50px 40px;
                box-shadow: 0 25px 50px rgba(0,0,0,0.2);
                max-width: 450px;
                margin: auto;
                position: relative;
                z-index: 1;
                transition: all 0.3s ease;
            }
            .login-card:hover {
                transform: translateY(-10px);
            }
            .login-icon {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
            }
            .login-icon i {
                font-size: 40px;
                color: white;
            }
            .form-control {
                border: 2px solid #e0e0e0;
                border-radius: 12px;
                padding: 12px 16px;
                transition: all 0.3s;
            }
            .form-control:focus {
                border-color: #e67e22;
                box-shadow: 0 0 0 4px rgba(230,126,34,0.1);
            }
            .btn-login {
                background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
                border: none;
                padding: 12px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 18px;
                transition: all 0.3s;
                color: white;
            }
            .btn-login:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(230,126,34,0.3);
            }
            .alert {
                border-radius: 15px;
                border: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="login-card">
                <div class="login-icon">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div class="text-center mb-4">
                    <h2 class="fw-bold" style="color: #333;">Admin Panel</h2>
                    <p class="text-muted">Đăng nhập để quản lý đơn hàng</p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                    </div>
                    <button type="submit" name="login" class="btn-login w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Đăng nhập
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="text-decoration-none" style="color: #e67e22;">
                        <i class="bi bi-arrow-left"></i> Về trang chủ
                    </a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// ========== ĐÃ ĐĂNG NHẬP - GIAO DIỆN ADMIN ==========
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - Cơm Nhà Linh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
        }
        
        /* Navbar Admin */
        .admin-navbar {
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: white !important;
        }
        .navbar-brand i {
            margin-right: 0.5rem;
        }
        
        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        /* Filter Buttons */
        .filter-btn {
            border-radius: 50px;
            padding: 8px 20px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        .filter-btn:hover {
            transform: translateY(-2px);
        }
        
        /* Table Card */
        .table-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .table thead th {
            background: #f8f9fa;
            padding: 15px;
            font-weight: 600;
            border-bottom: 2px solid #e0e0e0;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Buttons */
        .btn-update {
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            border: none;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            color: white;
        }
        .btn-update:hover {
            background: linear-gradient(135deg, #d35400 0%, #e67e22 100%);
            transform: scale(1.05);
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .stat-card { margin-bottom: 1rem; }
            .filter-btn { margin-bottom: 0.5rem; }
        }
    </style>
</head>
<body>

<!-- Navbar Admin -->
<nav class="admin-navbar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="admin.php">
                <i class="bi bi-shield-lock-fill"></i> Cơm Nhà Linh - Admin
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white">
                    <i class="bi bi-person-circle"></i> <?php echo $_SESSION['admin_name']; ?>
                </span>
                <a href="?logout=1" class="btn btn-light btn-sm" onclick="return confirm('Đăng xuất?')">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </a>
                <a href="index.php" class="btn btn-outline-light btn-sm" target="_blank">
                    <i class="bi bi-globe"></i> Xem web
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container py-4">
    <!-- Thông báo -->
    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!$hasDatabase): ?>
        <div class="alert alert-warning fade-in">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Chưa có database!</strong> Vui lòng chạy file SQL trong thư mục database để tạo cơ sở dữ liệu.
        </div>
    <?php endif; ?>
    
    <!-- Thống kê -->
    <div class="row mb-4 fade-in">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Tổng đơn hàng</p>
                        <h2 class="mb-0 fw-bold"><?php echo $total_orders; ?></h2>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Chờ xác nhận</p>
                        <h2 class="mb-0 fw-bold text-warning"><?php echo $pending_orders; ?></h2>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Đang giao</p>
                        <h2 class="mb-0 fw-bold text-primary"><?php echo $delivering_orders; ?></h2>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-truck"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Doanh thu</p>
                        <h2 class="mb-0 fw-bold text-success"><?php echo number_format($total_revenue, 0, ',', '.'); ?>đ</h2>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bộ lọc -->
    <div class="mb-4 fade-in">
        <div class="btn-group flex-wrap">
            <a href="admin.php" class="btn filter-btn <?php echo ($statusFilter ?? 'all') == 'all' ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                📦 Tất cả (<?php echo $total_orders; ?>)
            </a>
            <a href="?status=pending" class="btn filter-btn <?php echo ($statusFilter ?? '') == 'pending' ? 'btn-warning' : 'btn-outline-warning'; ?>">
                ⏳ Chờ (<?php echo $pending_orders; ?>)
            </a>
            <a href="?status=confirmed" class="btn filter-btn <?php echo ($statusFilter ?? '') == 'confirmed' ? 'btn-info' : 'btn-outline-info'; ?>">
                ✅ Xác nhận (<?php echo $confirmed_orders; ?>)
            </a>
            <a href="?status=delivering" class="btn filter-btn <?php echo ($statusFilter ?? '') == 'delivering' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                🚚 Đang giao (<?php echo $delivering_orders; ?>)
            </a>
            <a href="?status=completed" class="btn filter-btn <?php echo ($statusFilter ?? '') == 'completed' ? 'btn-success' : 'btn-outline-success'; ?>">
                🎉 Hoàn thành (<?php echo $completed_orders; ?>)
            </a>
            <a href="?status=cancelled" class="btn filter-btn <?php echo ($statusFilter ?? '') == 'cancelled' ? 'btn-danger' : 'btn-outline-danger'; ?>">
                ❌ Đã hủy (<?php echo $cancelled_orders; ?>)
            </a>
        </div>
    </div>
    
    <!-- Danh sách đơn hàng -->
    <div class="table-card fade-in">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>SĐT</th>
                        <th>Địa chỉ</th>
                        <th>Tổng tiền</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="mt-2 text-muted">Chưa có đơn hàng nào</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?php echo $order['order_code']; ?></strong></td>
                                <td><?php echo htmlspecialchars($order['fullname']); ?></td>
                                <td><?php echo $order['phone']; ?></td>
                                <td><?php echo mb_substr(htmlspecialchars($order['address']), 0, 30); ?>...</td>
                                <td class="text-danger fw-bold"><?php echo number_format($order['total'], 0, ',', '.'); ?>đ</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <span class="status-badge bg-<?php echo $statusText[$order['status']]['class']; ?> bg-opacity-10 text-<?php echo $statusText[$order['status']]['class']; ?>">
                                        <i class="bi bi-<?php echo $statusText[$order['status']]['icon']; ?>"></i>
                                        <?php echo $statusText[$order['status']]['text']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" class="form-select form-select-sm" style="width: 110px;">
                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Chờ</option>
                                                <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Xác nhận</option>
                                                <option value="delivering" <?php echo $order['status'] == 'delivering' ? 'selected' : ''; ?>>Đang giao</option>
                                                <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Hủy</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn-update btn-sm">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <a href="?delete=<?php echo $order['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa đơn hàng #<?php echo $order['order_code']; ?>?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Link về trang chủ -->
    <div class="text-center mt-4">
        <a href="index.php" class="text-decoration-none" style="color: #e67e22;">
            <i class="bi bi-arrow-left"></i> Về trang chủ
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>