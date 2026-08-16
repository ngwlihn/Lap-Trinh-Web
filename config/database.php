<?php
// config/database.php
$host = 'localhost';
$dbname = 'linhfood_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Chỉ khởi tạo session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>