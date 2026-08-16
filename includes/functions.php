<?php
require_once __DIR__ . '/../config/database.php';

function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . '₫';
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    if (!headers_sent()) {
        header("Location: $url");
        exit();
    } else {
        echo "<script>window.location.href='$url';</script>";
        exit();
    }
}

function setFlash($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Cart functions
function getCart() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

function saveCart($cart) {
    $_SESSION['cart'] = $cart;
}

function addToCart($foodId, $quantity = 1) {
    global $conn;
    $cart = getCart();
    
    if (isset($cart[$foodId])) {
        $cart[$foodId]['quantity'] += $quantity;
    } else {
        $stmt = $conn->prepare("SELECT id, name, price, image FROM foods WHERE id = ?");
        $stmt->execute([$foodId]);
        $food = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($food) {
            $cart[$foodId] = [
                'id' => $food['id'],
                'name' => $food['name'],
                'price' => $food['price'],
                'image' => $food['image'],
                'quantity' => $quantity
            ];
        }
    }
    
    saveCart($cart);
    return $cart;
}

function removeFromCart($foodId) {
    $cart = getCart();
    unset($cart[$foodId]);
    saveCart($cart);
    return $cart;
}

function updateCartItem($foodId, $quantity) {
    $cart = getCart();
    if (isset($cart[$foodId])) {
        if ($quantity <= 0) {
            unset($cart[$foodId]);
        } else {
            $cart[$foodId]['quantity'] = $quantity;
        }
        saveCart($cart);
    }
    return $cart;
}

function getCartTotal() {
    $cart = getCart();
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function getCartCount() {
    $cart = getCart();
    $count = 0;
    foreach ($cart as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

// Get all foods
function getAllFoods($search = '', $category = null, $minPrice = 0, $maxPrice = 500000) {
    global $conn;
    $sql = "SELECT * FROM foods WHERE price BETWEEN :min_price AND :max_price";
    $params = [':min_price' => $minPrice, ':max_price' => $maxPrice];
    
    if ($search) {
        $sql .= " AND (name LIKE :search OR description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    if ($category) {
        $sql .= " AND category = :category";
        $params[':category'] = $category;
    }
    
    $sql .= " ORDER BY id DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategories() {
    global $conn;
    $stmt = $conn->query("SELECT DISTINCT category FROM foods ORDER BY category");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function createOrder($orderData) {
    global $conn;
    $conn->beginTransaction();
    
    try {
        $orderCode = 'LH' . date('Ymd') . rand(100, 999);
        
        $stmt = $conn->prepare("INSERT INTO orders (user_id, order_code, fullname, phone, email, address, notes, payment_method, subtotal, shipping_fee, total) 
                              VALUES (:user_id, :order_code, :fullname, :phone, :email, :address, :notes, :payment_method, :subtotal, :shipping_fee, :total)");
        
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'] ?? null,
            ':order_code' => $orderCode,
            ':fullname' => $orderData['fullname'],
            ':phone' => $orderData['phone'],
            ':email' => $orderData['email'],
            ':address' => $orderData['address'],
            ':notes' => $orderData['notes'],
            ':payment_method' => $orderData['payment_method'],
            ':subtotal' => $orderData['subtotal'],
            ':shipping_fee' => $orderData['shipping_fee'],
            ':total' => $orderData['total']
        ]);
        
        $orderId = $conn->lastInsertId();
        
        $cart = getCart();
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, food_id, food_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($cart as $item) {
            $stmt->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['quantity']]);
        }
        
        $conn->commit();
        saveCart([]);
        
        return ['success' => true, 'order_id' => $orderId, 'order_code' => $orderCode];
    } catch (Exception $e) {
        $conn->rollBack();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
?>