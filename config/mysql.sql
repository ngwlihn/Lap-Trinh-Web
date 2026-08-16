-- Tạo database
CREATE DATABASE IF NOT EXISTS linhfood_db;
USE linhfood_db;

-- Bảng users (chỉ lưu khách hàng, không có role)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(15) NOT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng foods (món ăn)
CREATE TABLE foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,0) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng orders (đơn hàng)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_code VARCHAR(20) UNIQUE NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    address TEXT NOT NULL,
    notes TEXT,
    payment_method VARCHAR(50) NOT NULL,
    subtotal DECIMAL(10,0) NOT NULL,
    shipping_fee DECIMAL(10,0) DEFAULT 15000,
    total DECIMAL(10,0) NOT NULL,
    status ENUM('pending', 'confirmed', 'delivering', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Bảng order_items (chi tiết đơn hàng)
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    food_id INT NOT NULL,
    food_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,0) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- === DỮ LIỆU MẪU ===

-- Thêm món ăn (10 món)
INSERT INTO foods (name, price, category, description) VALUES
('Cơm Gà Xối Mỡ', 45000, 'Cơm', 'Cơm gà thơm lừng với nước mắm chua ngọt truyền thống'),
('Cơm Tấm Bì Sườn', 50000, 'Cơm', 'Cơm tấm nức nà với bì sườn, trứng ốp la và nước mắm'),
('Trà Sữa Truyền Thống', 35000, 'Trà Sữa', 'Trà sữa đậm đà, khoai môn, trân châu'),
('Trà Sữa Matcha', 40000, 'Trà Sữa', 'Matcha chất lượng cao kết hợp sữa tươi'),
('Burger Thương Hiệu', 55000, 'Đồ ăn nhanh', 'Burger thịt bò, rau xà lách, phô mai'),
('Gà Rán Giòn', 60000, 'Đồ ăn nhanh', 'Gà rán giòn tan, nước chấm đặc biệt'),
('Phở Bò Nam Vang', 65000, 'Phở', 'Phở bò hầm 12 giờ, thơm, đậm đà'),
('Bún Chả Hà Nội', 48000, 'Bún', 'Bún chả xiên nướng, nước mắm chua cay'),
('Mỳ Ý Spaghetti', 70000, 'Mỳ', 'Spaghetti sốt cà chua, phô mai Parmesan'),
('Cà Phê Đen Đá', 20000, 'Cà Phê', 'Cà phê nguyên chất pha phin');

-- Không cần thêm tài khoản admin (đã có trong admin.php)