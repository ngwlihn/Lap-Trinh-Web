<?php
// api/update_cart.php
session_start();
require_once '../includes/functions.php';

// Xử lý cả GET và POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $foodId = $_POST['food_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;
} else {
    $foodId = $_GET['food_id'] ?? 0;
    $quantity = $_GET['quantity'] ?? 0;
}

if ($foodId > 0 && $quantity > 0) {
    updateCartItem($foodId, $quantity);
}

// Chuyển hướng về trang giỏ hàng
header('Location: ../cart.php');
exit();
?>