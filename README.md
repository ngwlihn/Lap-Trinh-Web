# 🍔 Food Ordering Website

Website **đặt đồ ăn trực tuyến** được xây dựng bằng **PHP, MySQL, HTML, CSS và JavaScript**.
Hệ thống cho phép khách hàng xem thực đơn, thêm món ăn vào giỏ hàng, đặt hàng, theo dõi đơn hàng; đồng thời cung cấp trang quản trị để quản lý hoạt động của website.

---

## 📌 Giới thiệu

**Food Ordering Website** là một hệ thống bán và đặt đồ ăn trực tuyến đơn giản, được xây dựng nhằm mô phỏng quy trình hoạt động của một website nhà hàng / cửa hàng đồ ăn.

Người dùng có thể:

* Xem danh sách món ăn.
* Thêm món vào giỏ hàng.
* Thay đổi số lượng sản phẩm.
* Đăng ký và đăng nhập tài khoản.
* Tiến hành đặt hàng.
* Xem lịch sử và trạng thái đơn hàng.

Hệ thống cũng cung cấp khu vực **Admin** để hỗ trợ quản lý dữ liệu và đơn hàng.

---

## ✨ Chức năng chính

### 👤 Khách hàng

* Đăng ký tài khoản.
* Đăng nhập / đăng xuất.
* Xem trang chủ.
* Xem danh sách món ăn.
* Thêm món ăn vào giỏ hàng.
* Cập nhật số lượng món trong giỏ.
* Xóa món khỏi giỏ hàng.
* Xóa toàn bộ giỏ hàng.
* Xem tổng giá trị đơn hàng.
* Nhập thông tin giao hàng.
* Tiến hành checkout.
* Xem thông báo đặt hàng thành công.
* Xem lịch sử các đơn hàng đã đặt.

### 🛒 Giỏ hàng

Hệ thống hỗ trợ:

* Thêm sản phẩm vào giỏ.
* Tăng / giảm số lượng.
* Xóa từng sản phẩm.
* Xóa toàn bộ giỏ hàng.
* Tính tổng tiền tự động.
* Chuyển sang bước thanh toán.

Các thao tác với giỏ hàng được xử lý thông qua các API PHP trong thư mục:

```text
api/
├── add_to_cart.php
├── update_cart.php
├── remove_from_cart.php
└── clear_cart.php
```

### 📦 Đặt hàng

Khách hàng có thể:

1. Chọn món ăn.
2. Thêm món vào giỏ.
3. Kiểm tra lại giỏ hàng.
4. Nhập thông tin giao hàng.
5. Xác nhận đặt hàng.
6. Nhận thông báo đặt hàng thành công.
7. Theo dõi các đơn hàng trong mục **My Orders**.

### 🛠️ Quản trị viên

Trang quản trị:

```text
admin.php
```

được sử dụng để hỗ trợ quản lý hoạt động của hệ thống, dữ liệu liên quan đến món ăn và đơn hàng.

---

# 🖥️ Công nghệ sử dụng

| Công nghệ   | Mục đích                      |
| ----------- | ----------------------------- |
| PHP         | Xử lý logic phía server       |
| MySQL       | Lưu trữ dữ liệu               |
| HTML5       | Xây dựng cấu trúc giao diện   |
| CSS3        | Thiết kế giao diện            |
| JavaScript  | Xử lý tương tác phía client   |
| Apache      | Web Server                    |
| PHP Session | Quản lý đăng nhập và giỏ hàng |

---

# 📂 Cấu trúc thư mục

```text
food/
│
├── api/
│   ├── add_to_cart.php
│   ├── clear_cart.php
│   ├── remove_from_cart.php
│   └── update_cart.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   │
│   ├── js/
│   │   └── main.js
│   │
│   └── upload/
│
├── config/
│   ├── database.php
│   └── mysql.sql
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
│
├── admin.php
├── cart.php
├── checkout.php
├── index.php
├── login.php
├── logout.php
├── menu.php
├── my_orders.php
├── order_success.php
└── register.php
```

---

# ⚙️ Cài đặt

## 1. Yêu cầu hệ thống

Bạn cần cài một trong các môi trường hỗ trợ PHP + MySQL như:

* XAMPP
* WAMP
* MAMP
* Laragon

Khuyến nghị sử dụng **XAMPP** hoặc **Laragon** để chạy project trên Windows.

---

## 2. Clone project

```bash
git clone <repository-url>
```

Sau đó truy cập thư mục:

```bash
cd food
```

Hoặc tải source code về và giải nén trực tiếp.

---

## 3. Đưa project vào Web Server

### Với XAMPP

Copy thư mục:

```text
food
```

vào:

```text
C:\xampp\htdocs\
```

Kết quả:

```text
C:\xampp\htdocs\food
```

### Với Laragon

Copy project vào:

```text
C:\laragon\www\
```

---

# 🗄️ Cấu hình Database

## Bước 1: Khởi động MySQL

Nếu sử dụng XAMPP, mở **XAMPP Control Panel** và bật:

```text
Apache
MySQL
```

---

## Bước 2: Mở phpMyAdmin

Truy cập:

```text
http://localhost/phpmyadmin
```

---

## Bước 3: Import database

Trong source code đã có file:

```text
config/mysql.sql
```

Tại phpMyAdmin:

1. Tạo database mới nếu cần.
2. Chọn database.
3. Chọn tab **Import**.
4. Chọn file:

```text
config/mysql.sql
```

5. Nhấn **Import / Go**.

Database và các bảng cần thiết cho hệ thống sẽ được khởi tạo.

---

# 🔗 Cấu hình kết nối MySQL

Thông tin kết nối database nằm trong:

```text
config/database.php
```

Kiểm tra và thay đổi các thông tin phù hợp với máy của bạn, ví dụ:

```php
host     = localhost
username = root
password = 
database = <database_name>
```

Với XAMPP mặc định thường sử dụng:

```text
Host: localhost
Username: root
Password: để trống
```

Tên database cần tương ứng với database được tạo/import từ file `mysql.sql`.

---

# ▶️ Chạy ứng dụng

Sau khi bật **Apache** và **MySQL**, truy cập:

```text
http://localhost/food/
```

Trang chủ:

```text
http://localhost/food/index.php
```

Trang menu:

```text
http://localhost/food/menu.php
```

Trang đăng nhập:

```text
http://localhost/food/login.php
```

Trang đăng ký:

```text
http://localhost/food/register.php
```

Trang quản trị:

```text
http://localhost/food/admin.php
```

---

# 🔄 Luồng hoạt động của hệ thống

```text
Trang chủ
    ↓
Menu
    ↓
Chọn món ăn
    ↓
Thêm vào giỏ hàng
    ↓
Giỏ hàng
    ↓
Checkout
    ↓
Xác nhận đặt hàng
    ↓
Đặt hàng thành công
    ↓
My Orders
```

---

# 🛒 Luồng xử lý giỏ hàng

```text
menu.php
     │
     ▼
add_to_cart.php
     │
     ▼
cart.php
     │
     ├── update_cart.php
     │
     ├── remove_from_cart.php
     │
     └── clear_cart.php
     │
     ▼
checkout.php
     │
     ▼
order_success.php
```

---

# 👤 Authentication

Hệ thống hỗ trợ quản lý tài khoản người dùng thông qua:

```text
register.php
login.php
logout.php
```

Quy trình:

```text
Đăng ký
   ↓
Đăng nhập
   ↓
Tạo Session
   ↓
Sử dụng các chức năng của hệ thống
   ↓
Đăng xuất
```

---

# 📦 Quản lý đơn hàng

Sau khi khách hàng hoàn tất quá trình checkout, thông tin đơn hàng được lưu vào cơ sở dữ liệu.

Người dùng có thể xem các đơn hàng của mình tại:

```text
my_orders.php
```

Sau khi đặt hàng thành công, hệ thống chuyển tới:

```text
order_success.php
```

để hiển thị thông tin xác nhận đơn hàng.

---

# 🎨 Frontend

Phần giao diện được quản lý chủ yếu bởi:

```text
assets/css/style.css
```

Các xử lý JavaScript nằm tại:

```text
assets/js/main.js
```

Header và Footer được tách thành các component dùng chung:

```text
includes/header.php
includes/footer.php
```

Điều này giúp hạn chế việc lặp code giữa các trang.

---

# 🔧 Backend

Các hàm PHP dùng chung được đặt trong:

```text
includes/functions.php
```

Kết nối cơ sở dữ liệu:

```text
config/database.php
```

Các API thao tác giỏ hàng:

```text
api/
```

Cách tổ chức này giúp phân chia tương đối rõ giữa:

```text
Giao diện
   ↓
Xử lý nghiệp vụ
   ↓
Database
```

---

# 🔐 Lưu ý bảo mật

Nếu phát triển hệ thống để sử dụng trong môi trường thực tế, nên bổ sung:

* Prepared Statements để phòng chống SQL Injection.
* Validation dữ liệu phía server.
* Sanitize dữ liệu người dùng nhập vào.
* CSRF Token cho các form quan trọng.
* Kiểm soát quyền truy cập trang Admin.
* Hash mật khẩu bằng `password_hash()`.
* Kiểm tra file upload.
* Giới hạn loại và kích thước file upload.
* HTTPS khi triển khai production.
* Quản lý Session an toàn.
* Không lưu trực tiếp thông tin database trong repository public.

---

# 🚀 Hướng phát triển

Một số chức năng có thể bổ sung trong tương lai:

* Tìm kiếm món ăn.
* Phân loại món ăn theo danh mục.
* Bộ lọc theo giá.
* Đánh giá và bình luận món ăn.
* Mã giảm giá / Voucher.
* Thanh toán online.
* VNPay.
* MoMo.
* ZaloPay.
* Stripe.
* Theo dõi trạng thái giao hàng.
* Dashboard thống kê doanh thu.
* Quản lý tồn kho.
* Quản lý khách hàng.
* Quản lý danh mục món ăn.
* Phân quyền Admin / Staff / Customer.
* Gửi email xác nhận đơn hàng.
* Responsive nâng cao cho mobile.
* REST API.
* Deploy hệ thống lên hosting / cloud.

---

# 🌐 Deployment

Project PHP có thể được triển khai trên:

* Shared Hosting hỗ trợ PHP/MySQL
* cPanel Hosting
* VPS
* Railway
* Render
* Docker/VPS

Khi deploy production cần cập nhật lại cấu hình database trong:

```text
config/database.php
```

với thông tin database của server.

---

# 📋 Các trang chính

| Trang         | File                | Chức năng          |
| ------------- | ------------------- | ------------------ |
| Home          | `index.php`         | Trang chủ          |
| Menu          | `menu.php`          | Danh sách món ăn   |
| Cart          | `cart.php`          | Quản lý giỏ hàng   |
| Checkout      | `checkout.php`      | Tiến hành đặt hàng |
| Login         | `login.php`         | Đăng nhập          |
| Register      | `register.php`      | Đăng ký            |
| Logout        | `logout.php`        | Đăng xuất          |
| My Orders     | `my_orders.php`     | Lịch sử đơn hàng   |
| Order Success | `order_success.php` | Xác nhận đặt hàng  |
| Admin         | `admin.php`         | Quản trị hệ thống  |

---



⭐ Nếu project hữu ích, hãy để lại một **Star** cho repository!
