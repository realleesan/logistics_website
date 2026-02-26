<?php
// Death Protection System - Activation Script
// File này để kích hoạt hệ thống từ website chính

// Kiểm tra xem có phải là request hợp lệ không
if (!isset($_GET['key']) || $_GET['key'] !== 'death_activation_2024') {
    die('Access denied.');
}

// Chuyển hướng đến hệ thống Death Protection
header('Location: index.php');
exit;
?> 