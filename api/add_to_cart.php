<?php
// api/add_to_cart.php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $foodId = $_POST['food_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    if ($foodId > 0) {
        addToCart($foodId, $quantity);
        
        // Trả về tổng số lượng món trong giỏ (không phải số sản phẩm)
        echo json_encode([
            'success' => true, 
            'cart_count' => getCartCount(), // Tổng số lượng món
            'cart_total' => getCartTotal()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid food ID']);
    }
}
?>