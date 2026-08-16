<?php
// api/clear_cart.php
session_start();
require_once '../includes/functions.php';

// Xóa giỏ hàng
saveCart([]);

// Chuyển hướng về trang giỏ hàng
header('Location: ../cart.php');
exit();
?>