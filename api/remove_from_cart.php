<?php
// api/remove_from_cart.php
session_start();
require_once '../includes/functions.php';

// Xử lý cả GET và POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $foodId = $_POST['food_id'] ?? 0;
} else {
    $foodId = $_GET['food_id'] ?? 0;
}

if ($foodId > 0) {
    removeFromCart($foodId);
}

// Chuyển hướng về trang giỏ hàng
header('Location: ../cart.php');
exit();
?>